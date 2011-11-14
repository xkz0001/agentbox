<?php
/**
 * The contact class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/user.class.php');
require_once("helper/google_api.php");
require_once("helper/db.php");

class contact extends control {
	public function __construct() {
		parent::__construct ();
	}
	
	public function display() {
		$t = @$_GET["t"];
		$userID = user::getId ();
		$link = " <a href='index.php?module=contact&method=display&t=lu'>Local Contact(s) Updated</a>".
				" <a href='index.php?module=contact&method=display&t=lf'>Local Contact(s) Full</a>".
				" <a href='index.php?module=contact&method=display&t=gu'>Google Contact(s) Updated</a>".
				" <a href='index.php?module=contact&method=display&t=gf'>Google Contact(s) Full</a>";
		switch ($t) {
			case "lu":
				$results = get_contacts_from_db($userID,'updated');
				$results_count = count($results)." local contact(s) found.";
				break;
			case "gu":
				$results = get_contacts_from_google($userID,'updated');
				$results_count = count($results)." google contact(s) found.";
				break;
			case "gf":
				$results = get_contacts_from_google($userID,'full');
				$results_count = count($results)." google contact(s) found.";
				break;
			default:
				$results = get_contacts_from_db($userID,'active');
				$results_count = count($results)." local contact(s) found.";
				break;
		}
		
		
		$google_sync = "<a href='index.php?module=contact&method=sync&scope=updated'>Google Sync From Updated >></a><br><a href='index.php?module=contact&method=sync&scope=full'>Google Sync Full >> >></a>";

		$this->smarty->assign('results',$results);
		$this->smarty->assign('link',$link);
		//$this->smarty->assign('t',$t);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		
		$this->smarty->display('contact.tpl');
	}
	
	public function sync() {
		$sync_start = date("Y-m-d\TH:i:s\Z", time());
		
		$userID = user::getId ();
		$type = $_GET["scope"];
		if ($type=="full") {
			$google_contacts = get_contacts_from_google($userID, "full");
			$local_contacts = get_contacts_from_db($userID, "full");
			$local_delete_contacts = array();
			$g_contacts = get_items_to_google($google_contacts,$local_contacts, $local_delete_contacts);
			$l_contacts = array_merge(get_items_to_local($google_contacts,$local_contacts),$local_delete_contacts);
		} else {
			$google_contacts = get_contacts_from_google($userID, "updated");
			$local_contacts = get_contacts_from_db($userID, "updated");
			$g_contacts = get_updated_items_to_google($google_contacts,$local_contacts);
			$l_contacts = get_updated_items_to_local($google_contacts,$local_contacts, "contacts");
		}
		
		
		sync_contacts_to_google($g_contacts);
		sync_contacts_to_local($l_contacts);
		update_google_sync_time($sync_start,"contacts");
		//redirect to contact-local page
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=contact&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;

	}


}

?>