<?php
/**
 * The calendar class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/User.class.php');
require_once("helper/google_api.php");
require_once("helper/db.php");

class calendar extends control {
	public function __construct() {
		parent::__construct ();
	}
	
	public function display() {
		$t = @$_GET["t"];
		$userID = user::getId ();
		
		if ($t != "g") {
			$results = get_events_from_db($userID,'active');
			$results_count = count($results)." local event(s) found."." <a href='index.php?module=calendar&method=display&t=g'>google event(s)</a>";
		} else {
			$results = get_events_from_google($userID);
			$results_count = count($results)." google event(s) found."." <a href='index.php?module=calendar&method=display'>local event(s)</a>";
		}
		$google_sync = "<a href='index.php?module=calendar&method=sync'>Google Sync >></a>";

		$this->smarty->assign('results',$results);
		//$this->smarty->assign('t',$t);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		
		$this->smarty->display('calendar.tpl');
	}
	
	public function sync() {
		$userID = user::getId ();
		
		$google_events = get_events_from_google($userID);
		$local_events = get_events_from_db($userID);

		$local_delete_events = array();
		$g_events = get_items_to_google($google_events,$local_events, $local_delete_events);

		$l_events = array_merge(get_items_to_local($google_events,$local_events),$local_delete_events);

		//insert/update/delete google 
		update_events_to_google($g_events);
		//insert/update/delete google 
		update_events_to_local($l_events);

		
		//redirect to event-local page
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=calendar&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;

	}

}

?>