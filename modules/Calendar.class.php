<?php
/**
 * The calendar class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/user.class.php');
require_once("helper/google_api.php");
require_once("helper/db.php");

class calendar extends control {
	public function __construct() {
		parent::__construct ();
	}
	
	public function display() {
		$t = @$_GET["t"];
		$userID = user::getId ();

		$link = " <a href='index.php?module=calendar&method=display&t=lu'>Local calendar(s) Updated</a>".
				" <a href='index.php?module=calendar&method=display&t=lf'>Local calendar(s) Full</a>".
				" <a href='index.php?module=calendar&method=display&t=gu'>Google calendar(s) Updated</a>".
				" <a href='index.php?module=calendar&method=display&t=gf'>Google calendar(s) Full</a>";
		switch ($t) {
			case "lu":
				$results = get_events_from_db($userID,'updated');
				$results_count = count($results)." local event(s) found.";
				break;
			case "gu":
				$results = get_events_from_google($userID,'updated');
				$results_count = count($results)." google event(s) found.";
				break;
			case "gf":
				$results = get_events_from_google($userID,'full');
				$results_count = count($results)." google event(s) found.";
				break;
			default:
				$results = get_events_from_db($userID,'active');
				$results_count = count($results)." local event(s) found.";
				break;
		}
		
		$google_sync = "<a href='index.php?module=calendar&method=sync&scope=updated'>Google Sync From Updated >></a><br><a href='index.php?module=calendar&method=sync&scope=full'>Google Sync Full >> >></a>";

		$this->smarty->assign('results',$results);
		$this->smarty->assign('link',$link);
		//$this->smarty->assign('t',$t);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		
		$this->smarty->display('calendar.tpl');

	}
	
	public function sync() {

		$sync_start = date("Y-m-d\TH:i:s\Z", time());
		
		$userID = user::getId ();
		$type = $_GET["scope"];
		if ($type=="full") {
			$google_events = get_events_from_google($userID, "full");
			$local_events = get_events_from_db($userID, "full");
			$local_delete_events = array();
			$g_events = get_items_to_google($google_events,$local_events, $local_delete_events);
			$l_events = array_merge(get_items_to_local($google_events,$local_events),$local_delete_events);
		} else {
			$google_events = get_events_from_google($userID, "updated");
			$local_events = get_events_from_db($userID, "updated");
			$g_events = get_updated_items_to_google($google_events,$local_events);
			$l_events = get_updated_items_to_local($google_events,$local_events, "events");
		}


		var_dump($google_events);
		var_dump($local_events);
		var_dump($g_events);
		var_dump($l_events);
		sync_events_to_google($g_events);
		sync_events_to_local($l_events);
		update_google_sync_time($sync_start,"events");
		//redirect to event-local page

		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=calendar&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;

	}

}

?>