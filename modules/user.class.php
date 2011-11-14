<?php
/**
 * The user class file.
 *
 */
require_once 'helper/google_task/src/apiClient.php';

class user extends control {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function df(){
		$this->smarty -> display('login.tpl');
	}

	public static function getPriv(){
		if(!isset($_SESSION['priv'])){
			return 0;
		}else
			return $_SESSION['priv'];
	}
	
	public static function getId(){
		if(isset($_SESSION['userId'])){
			return $_SESSION['userId'];
		}
	}

	public function getGoogleAuth(){
		
	}

	public static function getGoogleTaskService(){
		$client = new apiClient();
		//register below param in google api
		$client->setClientId('1022619735647.apps.googleusercontent.com');
		$client->setClientSecret('tGbO-oXQRvcL0qQgqPvwfGW7');
		$client->setRedirectUri('http://localhost/agentbox/index.php?module=abtask&method=display');
		$client->setApplicationName("http://localhost");
		$tasksService = new apiTasksService($client);
		if (isset($_SESSION['access_token'])) {
		  $client->setAccessToken($_SESSION['access_token']);
		} else {
		  $client->setAccessToken($client->authenticate());
		  $_SESSION['access_token'] = $client->getAccessToken();
		  exit();
		}
		return $tasksService;
	}

	public static function getZendServiceClient($service_name){
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Http_Client');
		Zend_Loader::loadClass('Zend_Gdata_Query');
		Zend_Loader::loadClass('Zend_Gdata_Feed');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		$client = Zend_Gdata_ClientLogin::getHttpClient(
        $_SESSION['googleUsername'], $_SESSION['googlePassword'], $service_name);
		if ($service_name == "cp"){
			$gdata = new Zend_Gdata($client);
			$gdata->setMajorProtocolVersion(3);
		} else {
			$gdata = new Zend_Gdata_Calendar($client);
		}
		
		return $gdata;
	}

	public static function getUpdatedTimestamp($module) {
		$sql = "SELECT {$module} FROM google_sync_time WHERE userID={$_SESSION['userId']}";
		$rs = mysql_query($sql);
		if(!$rs)
			echo mysql_error();
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_array($rs)){
				return $row[$module];
			}
		} else {
			return (date("Y-m-d\TH:i:s\Z",  mktime(0, 0, 0, 1, 1, 1970)));
		}

	}
	
	public function auth(){
		$sql = "SELECT * FROM user WHERE username = '".$_POST['username']."' AND password = '".$_POST['pwd']."'";
		$rs = mysql_query($sql);
		if(!$rs)
			echo mysql_error();
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_array($rs)){
				$_SESSION['userId'] = $row['id'];
				$_SESSION['priv'] = $row['group'];
				$_SESSION['googleUsername'] = $row['google_username'];
				$_SESSION['googlePassword'] = $row['google_password'];
			}
			header('Location:index.php?module=filelist');
		}else{
			$this->smarty -> assign('errorInfo', 'Your user ID or password is incorrect.');
			$this->smarty -> display('login.tpl');
		}
	}
	
	public function logout(){
		unset($_SESSION['userId']);
		unset($_SESSION['priv']);
		unset($_SESSION['access_token']);
		header('Location:index.php');
	}
}