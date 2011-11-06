<?php
/**
 * The task class file.
 *
 */

require_once('framework/control.class.php');
require_once('modules/User.class.php');
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

		if ($t != "g") {
			$results = get_tasks_from_db($userID,'active');
			$results_count = count($results)." local task(s) found."." <a href='index.php?module=abtask&method=display&t=g'>google task(s)</a>";
		} else {
			$results = get_tasks_from_google($userID,$tasksService);
			$results_count = count($results)." google task(s) found."." <a href='index.php?module=abtask&method=display'>local task(s)</a>";
		}
		$google_sync = "<a href='index.php?module=abtask&method=sync'>Google Sync >></a>";

		$this->smarty->assign('results',$results);
		$this->smarty->assign('results_count',$results_count);
		$this->smarty->assign('google_sync',$google_sync);
		$this->smarty->display('task.tpl');
	}

	public function getAuth(){
		echo $_SESSION['access_token'];
		echo "working";
	}

	public function sync() {
		$userID = user::getId ();
	
		$tasksService = user::getGoogleTaskService();

		$google_tasks = get_tasks_from_google($userID,$tasksService);
		$local_tasks = get_tasks_from_db($userID);

		$local_delete_tasks = array();
		$g_tasks = get_items_to_google($google_tasks,$local_tasks, $local_delete_tasks);

		$l_tasks = array_merge(get_items_to_local($google_tasks,$local_tasks),$local_delete_tasks);

		//insert/update/delete google 
		update_tasks_to_google($tasksService,$g_tasks);
		//insert/update/delete google 
		update_tasks_to_local($l_tasks);
		//redirect to task-local page
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'index.php?module=abtask&method=display';
		header("Location: http://$host$uri/$extra");
		//exit;
	}


}

?>