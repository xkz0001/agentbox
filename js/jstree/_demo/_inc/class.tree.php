<?php
class _tree_struct {
	// Structure table and fields
	protected $table	= "";
	protected $fields	= array(
			"id"		=> false,
			"parent_id"	=> false,
			"position"	=> false,
			"left"		=> false,
			"right"		=> false,
			"level"		=> false
		);

	// Constructor
	function __construct($table = "file_system", $fields = array()) {
		$this->table = $table;
		if(!count($fields)) {
			foreach($this->fields as $k => &$v) { $v = $k; }
		}
		else {
			foreach($fields as $key => $field) {
				switch($key) {
					case "id":
					case "parent_id":
					case "position":
					case "left":
					case "right":
					case "level":
						$this->fields[$key] = $field;
						break;
				}
			}
		}
		// Database
		$this->db = new _database;
	}

	function _get_node($id) {
		$this->db->query("SELECT `".implode("` , `", $this->fields)."` FROM `".$this->table."` WHERE `".$this->fields["id"]."` = ".(int) $id);
		$this->db->nextr();
		return $this->db->nf() === 0 ? false : $this->db->get_row("assoc");
	}
	function _get_children($id, $recursive = false) {
		$children = array();
		if($recursive) {
			$node = $this->_get_node($id);
			$this->db->query("SELECT `".implode("` , `", $this->fields)."` FROM `".$this->table."` WHERE `".$this->fields["left"]."` >= ".(int) $node[$this->fields["left"]]." AND `".$this->fields["right"]."` <= ".(int) $node[$this->fields["right"]]." ORDER BY `".$this->fields["left"]."` ASC");
		}
		else {
			$this->db->query("SELECT `".implode("` , `", $this->fields)."` FROM `".$this->table."` WHERE `".$this->fields["parent_id"]."` = ".(int) $id." ORDER BY `".$this->fields["position"]."` ASC");
		}
		while($this->db->nextr()) $children[$this->db->f($this->fields["id"])] = $this->db->get_row("assoc");
		return $children;
	}
	function _get_path($id) {
		$node = $this->_get_node($id);
		$path = array();
		if(!$node === false) return false;
		$this->db->query("SELECT `".implode("` , `", $this->fields)."` FROM `".$this->table."` WHERE `".$this->fields["left"]."` <= ".(int) $node[$this->fields["left"]]." AND `".$this->fields["right"]."` >= ".(int) $node[$this->fields["right"]]);
		while($this->db->nextr()) $path[$this->db->f($this->fields["id"])] = $this->db->get_row("assoc");
		return $path;
	}

	function _create($parent, $position) {
		return $this->_move(0, $parent, $position);
	}
	function _remove($id) {
		if((int)$id === 1) { return false; }
		$data = $this->_get_node($id);
		$lft = (int)$data[$this->fields["left"]];
		$rgt = (int)$data[$this->fields["right"]];
		$dif = $rgt - $lft + 1;

		// deleting node and its children
		$this->db->query("" . 
			"DELETE FROM `".$this->table."` " . 
			"WHERE `".$this->fields["left"]."` >= ".$lft." AND `".$this->fields["right"]."` <= ".$rgt
		);
		// shift left indexes of nodes right of the node
		$this->db->query("".
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["left"]."` = `".$this->fields["left"]."` - ".$dif." " . 
			"WHERE `".$this->fields["left"]."` > ".$rgt
		);
		// shift right indexes of nodes right of the node and the node's parents
		$this->db->query("" . 
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["right"]."` = `".$this->fields["right"]."` - ".$dif." " . 
			"WHERE `".$this->fields["right"]."` > ".$lft
		);

		$pid = (int)$data[$this->fields["parent_id"]];
		$pos = (int)$data[$this->fields["position"]];

		// Update position of siblings below the deleted node
		$this->db->query("" . 
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["position"]."` = `".$this->fields["position"]."` - 1 " . 
			"WHERE `".$this->fields["parent_id"]."` = ".$pid." AND `".$this->fields["position"]."` > ".$pos
		);
		return true;
	}
	function _move($id, $ref_id, $position = 0, $is_copy = false) {
		if((int)$ref_id === 0 || (int)$id === 1) { return false; }
		$sql		= array();						// Queries executed at the end
		$node		= $this->_get_node($id);		// Node data
		$nchildren	= $this->_get_children($id);	// Node children
		$ref_node	= $this->_get_node($ref_id);	// Ref node data
		$rchildren	= $this->_get_children($ref_id);// Ref node children

		$ndif = 2;
		$node_ids = array(-1);
		if($node !== false) {
			$node_ids = array_keys($this->_get_children($id, true));
			// TODO: should be !$is_copy && , but if copied to self - screws some right indexes
			if(in_array($ref_id, $node_ids)) return false;
			$ndif = $node[$this->fields["right"]] - $node[$this->fields["left"]] + 1;
		}
		if($position >= count($rchildren)) {
			$position = count($rchildren);
		}

		// Not creating or copying - old parent is cleaned
		if($node !== false && $is_copy == false) {
			$sql[] = "" . 
				"UPDATE `".$this->table."` " . 
					"SET `".$this->fields["position"]."` = `".$this->fields["position"]."` - 1 " . 
				"WHERE " . 
					"`".$this->fields["parent_id"]."` = ".$node[$this->fields["parent_id"]]." AND " . 
					"`".$this->fields["position"]."` > ".$node[$this->fields["position"]];
			$sql[] = "" . 
				"UPDATE `".$this->table."` " . 
					"SET `".$this->fields["left"]."` = `".$this->fields["left"]."` - ".$ndif." " . 
				"WHERE `".$this->fields["left"]."` > ".$node[$this->fields["right"]];
			$sql[] = "" . 
				"UPDATE `".$this->table."` " . 
					"SET `".$this->fields["right"]."` = `".$this->fields["right"]."` - ".$ndif." " . 
				"WHERE " . 
					"`".$this->fields["right"]."` > ".$node[$this->fields["left"]]." AND " . 
					"`".$this->fields["id"]."` NOT IN (".implode(",", $node_ids).") ";
		}
		// Preparing new parent
		$sql[] = "" . 
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["position"]."` = `".$this->fields["position"]."` + 1 " . 
			"WHERE " . 
				"`".$this->fields["parent_id"]."` = ".$ref_id." AND " . 
				"`".$this->fields["position"]."` >= ".$position." " . 
				( $is_copy ? "" : " AND `".$this->fields["id"]."` NOT IN (".implode(",", $node_ids).") ");

		$ref_ind = $ref_id === 0 ? (int)$rchildren[count($rchildren) - 1][$this->fields["right"]] + 1 : (int)$ref_node[$this->fields["right"]];
		$ref_ind = max($ref_ind, 1);

		$self = ($node !== false && !$is_copy && (int)$node[$this->fields["parent_id"]] == $ref_id && $position > $node[$this->fields["position"]]) ? 1 : 0;
		foreach($rchildren as $k => $v) {
			if($v[$this->fields["position"]] - $self == $position) {
				$ref_ind = (int)$v[$this->fields["left"]];
				break;
			}
		}
		if($node !== false && !$is_copy && $node[$this->fields["left"]] < $ref_ind) {
			$ref_ind -= $ndif;
		}

		$sql[] = "" . 
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["left"]."` = `".$this->fields["left"]."` + ".$ndif." " . 
			"WHERE " . 
				"`".$this->fields["left"]."` >= ".$ref_ind." " . 
				( $is_copy ? "" : " AND `".$this->fields["id"]."` NOT IN (".implode(",", $node_ids).") ");
		$sql[] = "" . 
			"UPDATE `".$this->table."` " . 
				"SET `".$this->fields["right"]."` = `".$this->fields["right"]."` + ".$ndif." " . 
			"WHERE " . 
				"`".$this->fields["right"]."` >= ".$ref_ind." " . 
				( $is_copy ? "" : " AND `".$this->fields["id"]."` NOT IN (".implode(",", $node_ids).") ");

		$ldif = $ref_id == 0 ? 0 : $ref_node[$this->fields["level"]] + 1;
		$idif = $ref_ind;
		if($node !== false) {
			$ldif = $node[$this->fields["level"]] - ($ref_node[$this->fields["level"]] + 1);
			$idif = $node[$this->fields["left"]] - $ref_ind;
			if($is_copy) {
				$sql[] = "" . 
					"INSERT INTO `".$this->table."` (" . 
						"`".$this->fields["parent_id"]."`, " . 
						"`".$this->fields["position"]."`, " . 
						"`".$this->fields["left"]."`, " . 
						"`".$this->fields["right"]."`, " . 
						"`".$this->fields["level"]."`" . 
					") " . 
						"SELECT " . 
							"".$ref_id.", " . 
							"`".$this->fields["position"]."`, " . 
							"`".$this->fields["left"]."` - (".($idif + ($node[$this->fields["left"]] >= $ref_ind ? $ndif : 0))."), " . 
							"`".$this->fields["right"]."` - (".($idif + ($node[$this->fields["left"]] >= $ref_ind ? $ndif : 0))."), " . 
							"`".$this->fields["level"]."` - (".$ldif.") " . 
						"FROM `".$this->table."` " . 
						"WHERE " . 
							"`".$this->fields["id"]."` IN (".implode(",", $node_ids).") " . 
						"ORDER BY `".$this->fields["level"]."` ASC";
			}
			else {
				$sql[] = "" . 
					"UPDATE `".$this->table."` SET " . 
						"`".$this->fields["parent_id"]."` = ".$ref_id.", " . 
						"`".$this->fields["position"]."` = ".$position." " . 
					"WHERE " . 
						"`".$this->fields["id"]."` = ".$id;
				$sql[] = "" . 
					"UPDATE `".$this->table."` SET " . 
						"`".$this->fields["left"]."` = `".$this->fields["left"]."` - (".$idif."), " . 
						"`".$this->fields["right"]."` = `".$this->fields["right"]."` - (".$idif."), " . 
						"`".$this->fields["level"]."` = `".$this->fields["level"]."` - (".$ldif.") " . 
					"WHERE " . 
						"`".$this->fields["id"]."` IN (".implode(",", $node_ids).") ";
			}
		}
		else {
			$sql[] = "" . 
				"INSERT INTO `".$this->table."` (" . 
					"`".$this->fields["parent_id"]."`, " . 
					"`".$this->fields["position"]."`, " . 
					"`".$this->fields["left"]."`, " . 
					"`".$this->fields["right"]."`, " . 
					"`".$this->fields["level"]."` " . 
					") " . 
				"VALUES (" . 
					$ref_id.", " . 
					$position.", " . 
					$idif.", " . 
					($idif + 1).", " . 
					$ldif. 
				")";
		}
		foreach($sql as $q) { $this->db->query($q); }
		$ind = $this->db->insert_id();
		if($is_copy) $this->_fix_copy($ind, $position);
		return $node === false || $is_copy ? $ind : true;
	}
	function _fix_copy($id, $position) {
		$node = $this->_get_node($id);
		$children = $this->_get_children($id, true);

		$map = array();
		for($i = $node[$this->fields["left"]] + 1; $i < $node[$this->fields["right"]]; $i++) {
			$map[$i] = $id;
		}
		foreach($children as $cid => $child) {
			if((int)$cid == (int)$id) {
				$this->db->query("UPDATE `".$this->table."` SET `".$this->fields["position"]."` = ".$position." WHERE `".$this->fields["id"]."` = ".$cid);
				continue;
			}
			$this->db->query("UPDATE `".$this->table."` SET `".$this->fields["parent_id"]."` = ".$map[(int)$child[$this->fields["left"]]]." WHERE `".$this->fields["id"]."` = ".$cid);
			for($i = $child[$this->fields["left"]] + 1; $i < $child[$this->fields["right"]]; $i++) {
				$map[$i] = $cid;
			}
		}
	}
}

class json_tree extends _tree_struct { 
	function __construct($table = "tree", $fields = array(), $add_fields = array("title" => "title", "type" => "type", "owner" => "owner" )) {
		parent::__construct($table, $fields);
		$this->fields = array_merge($this->fields, $add_fields);
		$this->add_fields = $add_fields;
	}

	function create_node($data) {
		$id = parent::_create((int)$data[$this->fields["id"]], (int)$data[$this->fields["position"]]);
		if($id) {
			$data["id"] = $id;
			$this->set_data($data);
			return  "{ \"status\" : 1, \"id\" : ".(int)$id." }";
		}
		return "{ \"status\" : 0 }";
	}
	function set_data($data) {
		if(count($this->add_fields) == 0) { return "{ \"status\" : 1 }"; }
		$s = "UPDATE `".$this->table."` SET `".$this->fields["id"]."` = `".$this->fields["id"]."` "; 
		foreach($this->add_fields as $k => $v) {
			if(isset($data[$k]))	$s .= ", `".$this->fields[$v]."` = \"".$this->db->escape($data[$k])."\" ";
			else					$s .= ", `".$this->fields[$v]."` = `".$this->fields[$v]."` ";
		}
		$s .= "WHERE `".$this->fields["id"]."` = ".(int)$data["id"];
		$this->db->query($s);
		return "{ \"status\" : 1 }";
	}
	function rename_node($data) { return $this->set_data($data); }

	function move_node($data) { 
		$id = parent::_move((int)$data["id"], (int)$data["ref"], (int)$data["position"], (int)$data["copy"]);
		if(!$id) return "{ \"status\" : 0 }";
		if((int)$data["copy"] && count($this->add_fields)) {
			$ids	= array_keys($this->_get_children($id, true));
			$data	= $this->_get_children((int)$data["id"], true);

			$i = 0;
			foreach($data as $dk => $dv) {
				$s = "UPDATE `".$this->table."` SET `".$this->fields["id"]."` = `".$this->fields["id"]."` "; 
				foreach($this->add_fields as $k => $v) {
					if(isset($dv[$k]))	$s .= ", `".$this->fields[$v]."` = \"".$this->db->escape($dv[$k])."\" ";
					else				$s .= ", `".$this->fields[$v]."` = `".$this->fields[$v]."` ";
				}
				$s .= "WHERE `".$this->fields["id"]."` = ".$ids[$i];
				$this->db->query($s);
				$i++;
			}
		}
		return "{ \"status\" : 1, \"id\" : ".$id." }";
	}
	function remove_node($data) {
		$id = parent::_remove((int)$data["id"]);
		return "{ \"status\" : 1 }";
	}
	function get_children($data) {
		$tmp = $this->_get_children((int)$data["id"]);
		if((int)$data["id"] === 1 && count($tmp) === 0) {
			$this->_create_default();
			$tmp = $this->_get_children((int)$data["id"]);
		}
		$result = array();
		if((int)$data["id"] === 0) return json_encode($result);
		foreach($tmp as $k => $v) {
			$result[] = array(
				"attr" => array("id" => "node_".$k, "rel" => $v[$this->fields["type"]]),
				"data" => $v[$this->fields["title"]],
				"state" => ((int)$v[$this->fields["right"]] - (int)$v[$this->fields["left"]] > 1) ? "closed" : ""
			);
		}
		return json_encode($result);
	}
	function search($data) {
		$this->db->query("SELECT `".$this->fields["left"]."`, `".$this->fields["right"]."` FROM `".$this->table."` WHERE `".$this->fields["title"]."` LIKE '%".$this->db->escape($data["search_str"])."%'");
		if($this->db->nf() === 0) return "[]";
		$q = "SELECT DISTINCT `".$this->fields["id"]."` FROM `".$this->table."` WHERE 0 ";
		while($this->db->nextr()) {
			$q .= " OR (`".$this->fields["left"]."` < ".(int)$this->db->f(0)." AND `".$this->fields["right"]."` > ".(int)$this->db->f(1).") ";
		}
		$result = array();
		$this->db->query($q);
		while($this->db->nextr()) { $result[] = "#node_".$this->db->f(0); }
		return json_encode($result);
	}

	function _create_default() {
		$this->_drop();
		$this->create_node(array(
			"id" => 1,
			"position" => 0,
			"title" => "C:",
			"type" => "drive"
		));
		$this->create_node(array(
			"id" => 1,
			"position" => 1,
			"title" => "D:",
			"type" => "drive"
		));
		$this->create_node(array(
			"id" => 2,
			"position" => 0,
			"title" => "_demo",
			"type" => "folder"
		));
		$this->create_node(array(
			"id" => 2,
			"position" => 1,
			"title" => "_docs",
			"type" => "folder"
		));
		$this->create_node(array(
			"id" => 4,
			"position" => 0,
			"title" => "index.html",
			"type" => "default"
		));
		$this->create_node(array(
			"id" => 5,
			"position" => 1,
			"title" => "doc.html",
			"type" => "default"
		));
	}
}

?>