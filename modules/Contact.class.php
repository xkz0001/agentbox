<?php
/**
 * The contact class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/User.class.php');
require_once("helper/google_api.php");
require_once("helper/db.php");

class contact extends control {
	public function __construct() {
		parent::__construct ();
	}
	
	public function display() {
		$t = @$_GET["t"];
		$userID = user::getId ();
		
		if ($t != "g") {
			$results = get_contacts_from_db($userID,'active');
			$results_count = count($results)." local contact(s) found."." <a href='index.php?module=contact&method=display&t=g'>google contact(s)</a>";
		} else {
			$results = get_contacts_from_google($userID);
			$results_count = count($results)." google contact(s) found."." <a href='index.php?module=contact&method=display'>local contact(s)</a>";
		}
		$google_sync = "<a href='index.php?module=contact&method=sync&scope=updated'>Google Sync From Last Updated >></a><br><a href='index.php?module=contact&method=sync&scope=full'>Full Google Sync >> >></a>";

		$this->smarty->assign('results',$results);
		//$this->smarty->assign('t',$t);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		
		$this->smarty->display('contact.tpl');
	}
	
	public function sync() {
		echo "xx";
		//$sync_start = date("Ymd\THis\Z", time());
		
		$userID = user::getId ();
		$type = $_GET["scope"];
		echo "google_contacts";
		if ($type=="full") {
			$google_contacts = get_contacts_from_google($userID, "full");
			$local_contacts = get_contacts_from_db($userID, "full");

			$local_delete_contacts = array();
			$g_contacts = get_items_to_google($google_contacts,$local_contacts, $local_delete_contacts);

			$l_contacts = array_merge(get_items_to_local($google_contacts,$local_contacts),$local_delete_contacts);
		} else {
			echo "google_contacts";
			$google_contacts = get_contacts_from_google($userID, "updated");
			$local_contacts = get_contacts_from_db($userID, "updated");
			$g_contacts = get_updated_items_to_google($google_contacts,$local_contacts);

			$l_contacts = get_updated_items_to_local($google_contacts,$local_contacts, "contacts");
		}
		echo "google_contacts";
		var_dump($google_contacts);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		var_dump($local_contacts);
		echo "<br>";
		echo "<br>";
		echo "<br>";

		var_dump($g_contacts);
		echo "<br>";
		echo "<br>";
		echo "<br>";
		var_dump($l_contacts);
		echo "<br>";
		echo "<br>";	
		echo "<br>";
		//update_google_sync_time($sync_start);
		die();
		

		
		//insert/update/delete google 
		update_contacts_to_google($g_contacts);
		//insert/update/delete google 
		update_contacts_to_local($l_contacts);
		//redirect to contact-local page
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=contact&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;

	}


}

?>