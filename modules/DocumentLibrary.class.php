<?php
/**
 * The file list class file.
 *
 */

require_once 'framework/control.class.php';
require_once 'modules/User.class.php';

class documentlibrary extends control {
	private $pId;
	
	public function __construct() {
		parent::__construct ();
	}
	public function df() {
		//		getFListById('0');
		$this->smarty->assign ( 'uId', user::getId () );
		$this->smarty->assign ( 'gId', user::getPriv() );
		$this->smarty->assign ( 'pId','' );

		$this->smarty->display ( 'document.tpl' );
	} 

	public function get_child_doc($pid){
		$rsArr = array();
		$rs_doc = mysql_query ( "SELECT * FROM file_system WHERE parent_id = {$pid}" );
		while ( $row = mysql_fetch_array ($rs_doc)) {
			$temp = array ();
			$temp ['id'] = $row ['id'];
			$temp ['title'] = $row ['title'];
			$temp ['type'] = $row ['type'];
			$temp ['owner'] = $row ['owner'];
			$temp ['tags'] = $row ['tags'];
			$temp ['is_folder'] = $row ['is_folder'];
			$temp ['pId'] = $row['parent_id'];
			$temp ['pname'] = $row['pname'];
			$rsArr[] = $temp;
		}
		return $rsArr;
	}

	public function get_doc_details($id){
		$rsArr = array();
		$rs_doc = mysql_query ( "SELECT * FROM file_system WHERE id = {$id}" );
		while ( $row = mysql_fetch_array ($rs_doc)) {
			$rsArr ['id'] = $row ['id'];
			$rsArr ['title'] = $row ['title'];
			$rsArr ['type'] = $row ['type'];
			$rsArr ['owner'] = $row ['owner'];
			$rsArr ['tags'] = $row ['tags'];
			$rsArr ['is_folder'] = $row ['is_folder'];
			$rsArr ['parent_id'] = $row['parent_id'];
			$rsArr ['permission'] = $row['permission'];
			$rsArr ['pname'] = $row['pname'];
		}
		return $rsArr;
	}

	public function result_response() {
		$id = $_POST['id'];
		$arr = $this->get_child_doc($id);
		echo(json_encode ($arr));
	}

	public function get_child_doc_html(){
		$arr = $this->get_child_doc($_GET['pid']);
		if (count($arr)==0){
			echo("<li class='line-last'></li>");
		}
		foreach($arr as $doc){
			if ($doc['is_folder']=="1"){
				echo("<li id='".$doc['id']."'><span class=text>".$doc['title']."</span><ul class='ajax'><li id='f".$doc['id']."'>{url:index.php?module=documentlibrary&method=get_child_doc_html&pid=".$doc['id']."}</li></ul></li>");		
			} else {
				echo("<li id='".$doc['id']."'><span class='text'>".$doc['title']."</span></li>");	
			}
		}
		
	}

	public function uploadfolder(){
		$sql = "INSERT INTO file_system (title,owner, parent_id, is_folder, position, permission) VALUES ('".$_POST['title']."','" . user::getId () . "', '" . $_POST ['folder_pid'] . "'," . $_POST['is_folder'] . ",1, " . $_POST ['permission'] . ")";
		$rs = mysql_query ( $sql );
		if (! $rs)
			echo mysql_error ();
		$this->smarty->display ( 'document.tpl' );
	}

	public function uploadfile(){
		if ($_FILES ["upFile"] ["error"] > 0) {
			echo "Return Code: " . $_FILES ["upFile"] ["error"] . "<br />";
		} else {
			$type = $this->get_filetype($_FILES["upFile"]["name"]);
			$filename= time() . $type;
			if (file_exists ("upload/".$filename)) {
				echo $_FILES["upFile"]["name"] . " already exists. ";
			} else if(move_uploaded_file($_FILES["upFile"]["tmp_name"], "upload/".$filename)){
				$sql = "INSERT INTO file_system (title, pname, type, tags, owner, permission, parent_id, is_folder) VALUES ('".$_FILES["upFile"]["name"]."','" . $filename . "', '". $type ."', 
				'" . $_POST ['fileTags'] . "', '" . user::getId () . "', '" . $_POST ['permission'] . "', '" . $_POST ['file_pid'] . "',0)";
				$rs = mysql_query ( $sql );
				if (! $rs)
					echo mysql_error ();
				$this->smarty->display ( 'document.tpl' );
			}
		}
		
	}

	public function move(){
		$sql = "UPDATE file_system SET parent_id = '" . $_POST['did'] . "', position='".$_POST['pos']."' WHERE id = '" . $_POST['sid'] . "'";
		$rs = mysql_query ( $sql );
		if (! $rs)
			echo mysql_error ();

		$arr = $this->get_permission($_POST['sid']);
	}

	public function get_permission($fid=null){
		$rsArr = array("create_folder"=>0,"create_file"=>0,"edit"=>0,"delete"=>0);
		$id = isset($fid) ? $fid : $_POST['id'];
		$current_user = user::getId();
		$current_user_priv = user::getPriv();
		$row = $this->get_doc_details($id);
		if (count($row)>0){
			if ($current_user==$row['owner'] || $current_user_priv=="1"){
				$rsArr["create_folder"] = 1;
				$rsArr["create_file"] = 1;
				$rsArr["edit"] = 1;
				$rsArr["delete"] = 1;
			} else {
				if ($row['permission']=="0" || $row['permission']=="1"){
					$rsArr["create_folder"] = 1;
					$rsArr["create_file"] = 1;
					$rsArr["edit"] = 1;
					$rsArr["delete"] = 1;
				} else {
					$rsArr["create_folder"] = 0;
					$rsArr["create_file"] = 0;
					$rsArr["edit"] = 0;
					$rsArr["delete"] = 0;
				}
				if ($row["is_folder"]=="0"){
					$rsArr["create_folder"] = 0;
					$rsArr["create_file"] = 0;
				}	
			}
		}
		$arr_permission = array_merge($row,$rsArr);
		echo(json_encode($arr_permission));
	}

	function get_filetype($path) {
		$pos = strrpos ( $path, '.' );
		if ($pos !== false) {
			return substr ( $path, $pos );
		} else {
			return '';
		}
	}
	
}

