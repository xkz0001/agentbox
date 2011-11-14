<?php
/**
 * The task class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/user.class.php');
require_once("helper/google_api.php");
require_once("helper/db.php");
require_once 'helper/google_task/src/contrib/apiTasksService.php';
require_once 'helper/google_task/src/apiClient.php';

class abtask extends control {
	public function __construct() {
		parent::__construct ();
	}

	public function display() {
		$t = @$_GET["t"];
		$userID = user::getId ();
		$tasksService = user::getGoogleTaskService();
		$link = " <a href='index.php?module=abtask&method=display&t=lu'>Local Task(s) Updated</a>".
				" <a href='index.php?module=abtask&method=display&t=lf'>Local Task(s) Full</a>".
				" <a href='index.php?module=abtask&method=display&t=gu'>Google Task(s) Updated</a>".
				" <a href='index.php?module=abtask&method=display&t=gf'>Google Task(s) Full</a>";
		switch ($t) {
			case "lu":
				$results = get_tasks_from_db($userID,'updated');
				$results_count = count($results)." local task(s) found.";
				break;
			case "gu":
				$results = get_tasks_from_google($userID,$tasksService,'updated');
				$results_count = count($results)." google task(s) found.";
				break;
			case "gf":
				$results = get_tasks_from_google($userID,$tasksService,'full');
				$results_count = count($results)." google task(s) found.";
				break;
			default:
				$results = get_tasks_from_db($userID,'active');
				$results_count = count($results)." local task(s) found.";
				break;
		}
		
		$google_sync = "<a href='index.php?module=abtask&method=sync&scope=updated'>Google Sync From Updated >></a><br><a href='index.php?module=abtask&method=sync&scope=full'>Google Sync Full >> >></a>";

		$this->smarty->assign('results',$results);
		$this->smarty->assign('link',$link);
		//$this->smarty->assign('t',$t);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		
		$this->smarty->display('task.tpl');
	}
	
	public function getAuth(){
		echo $_SESSION['access_token'];
		echo "working";
	}

	public function sync() {
		$sync_start = date("Y-m-d\TH:i:s\Z", time());
		
		$userID = user::getId ();
		$tasksService = user::getGoogleTaskService();
		$type = $_GET["scope"];
		if ($type=="full") {
			$google_tasks = get_tasks_from_google($userID,$tasksService, "full");
			$local_tasks = get_tasks_from_db($userID, "full");
			$local_delete_tasks = array();
			$g_tasks = get_items_to_google($google_tasks,$local_tasks, $local_delete_tasks);
			$l_tasks = array_merge(get_items_to_local($google_tasks,$local_tasks),$local_delete_tasks);
		} else {
			$google_tasks = get_tasks_from_google($userID,$tasksService, "updated");
			$local_tasks = get_tasks_from_db($userID, "updated");
			$g_tasks = get_updated_items_to_google($google_tasks,$local_tasks);
			$l_tasks = get_updated_items_to_local($google_tasks,$local_tasks, "tasks");
		}


		var_dump($google_tasks);
		var_dump($local_tasks);
		var_dump($g_tasks);
		var_dump($l_tasks);
		sync_tasks_to_google($tasksService, $g_tasks);
		sync_tasks_to_local($l_tasks);
		update_google_sync_time($sync_start,"tasks");
		//redirect to event-local page

		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=abtask&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;


	}


}

?>