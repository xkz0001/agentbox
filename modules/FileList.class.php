<?php
/**
 * The file list class file.
 *
 */

require_once 'framework/control.class.php';
require_once 'modules/user.class.php';

class filelist extends control {
	private $pId;
	
	public function __construct() {
		parent::__construct ();
	}
	
	public function df() {
		//		getFListById('0');
		$this->smarty->assign ( 'uId', user::getId () );
		$this->smarty->assign ( 'gId', user::getPriv() );
		$this->smarty->assign ( 'pId','' );

		$this->smarty->display ( 'fileList.tpl' );
	} 
	
	public function getList() {
		$sql = "SELECT password FROM file_system WHERE id = '" . $_GET ['pId'] . "'";
		$rs = mysql_query ( $sql );
		$row = mysql_fetch_row ( $rs );
		if ($row ['password'] == "" || $row ['password'] == $_GET ['pwd']) {
			$this->getFListById ( $_GET ['pId'] );
		} else {
			echo "The password is wrong!";
		}
	}
	
	public function ckpass() {
		$sql = "SELECT password FROM file_system WHERE id = '" . $_GET ['fId'] . "'";
		$rs = mysql_query ( $sql );
		$row = mysql_fetch_array ( $rs );
		if ($row ['password'] == "" || $row ['password'] == $_GET ['pwd']) {
			$this->getFListById ( $_GET ['fId'] );
		} else {
			echo "The password is incorrect.";
		}
	}

	public function getEnterList() {
		$this->pId = $_GET['pId'];
		$this->getFListById ($_GET['pId']);
	}
	
	public function enterFolder() {
		$this->getFListById ( $_GET ['pId'] );
	}
	
	public function enterUpFolder() {
		$sql = "SELECT parent_id FROM file_system WHERE id = '" . $_GET ['pId'] . "'";
		$rs = mysql_query ( $sql );
		$row = mysql_fetch_row ( $rs );
		$this->getFListById ( $row [0] );
	}
	
	public function getFListById($pId) {
		$this->pId = $pId;
		$sql = "SELECT * FROM file_system WHERE parent_id = '$pId'";
		$this->rebackList ( $sql );
	}
	
	public function searchF() {
		$sql = "SELECT * FROM file_system WHERE name like '%" . $_GET ['key'] . "%' OR tags = '" . $_GET ['key'] . "'";
		$this->rebackList ( $sql );
	}
	
	private function rebackList($sql) {
		$rs = mysql_query ( $sql );
		$out[] = 1;
		$rsArr = array();
		while ( $row = mysql_fetch_array ( $rs ) ) {
			$temp = array ();
			$temp ['fId'] = $row ['id'];
			$temp ['name'] = $row ['name'];
			$temp ['pwd'] = $row ['password']=='' ? "":"pass";
			$temp ['type'] = $row ['type'];
			$temp ['owner'] = $row ['owner'];
			$temp ['tags'] = $row ['tags'];
			$temp ['isFolder'] = $row ['isFolder'];
			$temp ['pId'] = $row['parent_id'];
			$temp ['pname'] = $row['pname'];
			$temp ['path'] = $this->getPath($row ['parent_id']);

			$rsArr[] = $temp;
		}
		$out[] = $rsArr;
		$out[] = $this->pId;
		$rsOut[] = $out;
		$rsOut[] = $this->getPath($this->pId);
		echo json_encode ( $rsOut );
	}

	private function getPath($pId){
		$tempPath[2] = $pId;
		while($tempPath[2] != 0){
			$tempPath = $this->getPathById($tempPath[2]);
			$rsPath[] = array($tempPath[0], $tempPath[1]);
		}
		$rsPath[] = array(0, 'Root');
		$rsPath = array_reverse($rsPath);
		return array(2, $rsPath);
	}
	
	private function getPathById($pId){
		$sql = "SELECT id,name,parent_id FROM file_system WHERE id = '$pId'";
		$rs = mysql_query ( $sql );
		$row = mysql_fetch_row($rs);
		return array($row[0], $row[1], $row[2]);
	}
	
	public function createFolder() {
		$sql = "INSERT INTO file_system (name, type, tags, owner, password, parent_id, isFolder) VALUES ('" . $_GET ['folderName'] . "', '1', 
		'" . $_GET ['tags'] . "', '" . user::getId () . "', '" . $_GET ['pwd'] . "', '" . $_GET ['pId'] . "',1)";
		$rs = mysql_query ( $sql );
		if (! $rs)
			echo mysql_error ();
		
		$this->getFListById ( $_GET ['pId'] );
	}
	
	public function editF() {
		$sql = "UPDATE file_system SET name = '" . $_GET ['folderName'] . "', tags = '" . $_GET ['tags'] . "', password = '" . $_GET ['pwd'] . "'
		WHERE id = '" . $_GET ['fId'] . "'";
		$rs = mysql_query ( $sql );
		if (! $rs)
			echo mysql_error ();
		$this->getFListById ( $this->pId );
	}
	
	public function moveF() {
		if($_GET ['aId'] == -1){
			$uId = $this->getParentId($_GET ['fId']);
			if($uId != -1){
				$uId = $this->getParentId($uId);
				if($uId != -1){
					$sql = "UPDATE file_system SET parent_id = '" . $uId . "' WHERE id = '" . $_GET ['fId'] . "'";
					$rs = mysql_query ( $sql );
					if (! $rs)
						echo mysql_error ();
				}
				
			}
		}else{
			$sql = "UPDATE file_system SET parent_id = '" . $_GET ['aId'] . "' WHERE id = '" . $_GET ['fId'] . "'";
			$rs = mysql_query ( $sql );
			if (! $rs)
				echo mysql_error ();
		}
		$this->getFListById ( $_GET ['pId'] );
	}
	
		private function getParentId($id){
		$sql = "SELECT parent_id FROM file_system WHERE id = '$id'";
		$rs = mysql_query ( $sql );
		$row = mysql_fetch_row($rs);
		return $row[0];
	}

	public function delFolders() {
		//		$delSql = "DELETE file_system WHERE id IN (SELECT id FROM file_system WHERE parent_id = '".$_POST['fId']."')";
		//		mysql_query($delSql);
		$this->pId = $_GET["fpId"];
		$this->delFolder ( $_GET ['fId'] );
		$sql = "DELETE FROM file_system WHERE id = '" . $_GET ['fId'] . "'";
		$rs = mysql_query ( $sql );
		if (! $rs)
			echo mysql_error ();
		
		if (@$_GET ['isfolder'] != 0) {
			$this->removeFile ( $_GET ['fId'] );
		}
		$this->getFListById ( $this->pId );
	}
	
	public function delFolder($fId) {
		$sql = "SELECT * FROM file_system WHERE parent_id = '$fId'";
		$rs = mysql_query ( $sql );
		while ( $row = mysql_fetch_array ( $rs ) ) {
			$this->delFolder ( $row ['id'] );
			if ($row ['isFolder'] == 0) { //delete file
				$this->removeFile ( $row ['id'] );
			}
			$delSql = "DELETE FROM file_system WHERE id = '" . $row ['id'] . "'";
			mysql_query ( $delSql );
		}
	}
	
	function upload() {
		if ($_FILES ["upFile"] ["error"] > 0) {
			echo "Return Code: " . $_FILES ["upFile"] ["error"] . "<br />";
		} else {
			$filename= time() . $this->get_filetype($_FILES["upFile"]["name"]);
			if (file_exists ("upload/".$filename)) {
				echo $_FILES["upFile"]["name"] . " already exists. ";
			} else if(move_uploaded_file($_FILES["upFile"]["tmp_name"], "upload/".$filename)){
				$sql = "INSERT INTO file_system (name,pname, type, tags, owner, password, parent_id, isFolder) VALUES ('".$_FILES["upFile"]["name"]."','" . $filename . "', '2', 
				'" . $_POST ['fileTags'] . "', '" . user::getId () . "', '" . @$_POST ['filePwd'] . "', '" . $_POST ['fpId'] . "',0)";
				$rs = mysql_query ( $sql );
				if (! $rs)
					echo mysql_error ();
				$this->smarty->assign ( 'pId',$_POST ['fpId']  );
				$this->smarty->assign ( 'uId', user::getId () );
				$this->smarty->assign ( 'gId', user::getPriv() );
				$this->smarty->display ( 'fileList.tpl' );
				
			}
		}
	}
	function removeFile($filepath) {
		if (! empty ( $filepath ) && file_exists ( './'.$filepath )) {
			unlink ( './'.$filepath );
			return true;
		} else {
			return false;
		}
	}
	function showError($files) {
		if (isset ( $files ['error'] )) {
			
			$msg = "";
			if ($files ['error'] == 1 || $files ['error'] == 2) {
				$msg = "The file is too large.";
			}
		}
	
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

?>