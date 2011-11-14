<?php

function sync_contacts_to_local($contacts){
	foreach ($contacts as $contact){
		//$updated = add_time_offset($contact['updated'],1);
		
		
		$name = $contact['name'];
		$emailAddress = $contact['emailAddress'];
		$orgName = $contact['orgName'];
		$orgTitle = $contact['orgTitle'];
		$website   = $contact['website'];
		$phoneNumber = $contact['phoneNumber'];
		$googleID = $contact['googleID'];
		$updated = date("Y-m-d H:i:s",strtotime($contact['updated']));

		if ($contact['action']=='delete'){
			$id = $contact['id'];
			mysql_query("UPDATE contacts 
						SET is_deleted = 1, 
							updated = '$updated'
						WHERE id = $id;");
		} elseif ($contact['action']=='update'){
			$id = $contact['id'];
			mysql_query("UPDATE contacts 
						SET name = '$name', 
							emailAddress = '$emailAddress', 
							orgName = '$orgName', 
							orgTitle = '$orgTitle', 
							website = '$website', 
							phoneNumber = '$phoneNumber', 
							googleID = '$googleID', 
							updated = '$updated' 
						WHERE id = $id");
		} elseif ($contact['action']=='insert'){
			$userID = $contact['userID'];
			mysql_query("INSERT INTO contacts 
						(userID, name, emailAddress, orgName, orgTitle, website, phoneNumber, googleID, updated) 
						VALUES 
						($userID,'$name','$emailAddress','$orgName','$orgTitle','$website','$phoneNumber','$googleID', '$updated');");
		}
		echo mysql_error();
	}
}

//grap contacts from database
// params: 
//	1 - userid
//  2 - type: 1- all, 2 - no_google_contacts 
function get_contacts_from_db($userID, $type=null) {
  $contacts = array();
	$sql = "SELECT id, userID, name, emailAddress, orgName, orgTitle, website, phoneNumber, googleID, is_deleted, updated
					FROM contacts 
					WHERE userID='$userID' ";
	if ($type == "active") {
		$sql .= "AND is_deleted=0";
	} else if ($type == "updated") {
		$updated = user::getUpdatedTimestamp("contacts");
		$sql .= " AND updated>'$updated'";
	}
	$result = mysql_query($sql);
  if($result != false) {
		while($res=mysql_fetch_array($result)) {
			$re['id'] = $res['id'];
			$re['userID'] = $res['userID'];
			$re['name'] = $res['name'];
			$re['emailAddress'] = $res['emailAddress'];
			$re['orgName'] = $res['orgName'];
			$re['orgTitle'] = $res['orgTitle'];
			$re['website'] = $res['website'];
			$re['phoneNumber'] = $res['phoneNumber'];
			$re['googleID'] = $res['googleID'];
			$re['is_deleted'] = $res['is_deleted'];
			$re['updated'] = $res['updated'];
			array_push($contacts,$re);
		}
	}
	return $contacts;
}

//Only update google id to indentify the contact has been synced with google
//TODO update other contact info with google
function update_google_id_time_to_local($arr,$module) {
	$id   = $arr['id'];
	$googleID = $arr['googleID'];
	$updated = date("Y-m-d H:i:s",strtotime($arr['updated']));
	mysql_query("UPDATE {$module} SET googleID='$googleID', updated='$updated' where id='$id'");
	return true;
}

function find_item_by_google_id($googleID, $module){
	$result = mysql_query("SELECT id FROM {$module} WHERE googleID='$googleID'");
	while($res = mysql_fetch_array($result)) {
		return $res["id"];
	}
	return false;
}

function update_google_sync_time($time,$module){
	if ($module=="contacts"){
		$time_str = "'$time','1970-01-01 00:00:01','1970-01-01 00:00:01'";
	} else if ($module=="tasks"){
		$time_str = "'1970-01-01 00:00:01','$time','1970-01-01 00:00:01'";
	} else if ($module=="events"){
		$time_str = "'1970-01-01 00:00:01','1970-01-01 00:00:01','$time'";
	}
	mysql_query("INSERT google_sync_time (userID, contacts, tasks, events) values (1,{$time_str}) ON DUPLICATE KEY UPDATE {$module}='$time';");
}

//check if contact exists has googleID
function contact_google_id_not_exist($googleID) {
	$result = mysql_query("SELECT id FROM contacts WHERE googleID='$googleID'");
	while($res = mysql_fetch_array($result)) {
		return false;
	}
	return true;
}

/*Calendar db function
*/

//grap events from database
// params: 
//	1 - userid
//  2 - type: 1- all, 2 - no_google_calendar 
function get_events_from_db($userID, $type=null) {
	$events = array();
	$sql = "SELECT id, userID, title, description, location, guest, startTime, endTime, owner, googleID, is_deleted, updated
					FROM events 
					WHERE userID='$userID' ";
	if ($type == "active") {
		$sql .= "AND is_deleted=0";
	} else if ($type == "updated") {
		$updated = user::getUpdatedTimestamp("events");
		$sql .= " AND updated>'$updated'";
	}
	$result = mysql_query($sql);
	if($result != false) {
		while($res=mysql_fetch_array($result)) {
			$re['id'] = $res['id'];
			$re['userID'] = $res['userID'];
			$re['title'] = $res['title'];
			$re['location'] = $res['location'];
			$re['guest'] = $res['guest'];
			$re['description'] = $res['description'];
			$re['startTime'] = $res['startTime'];
			$re['endTime'] = $res['endTime'];
			$re['owner'] = $res['owner'];
			$re['googleID'] = $res['googleID'];
			$re['is_deleted'] = $res['is_deleted'];
			$re['updated'] = $res['updated'];
			$events[] = $re;
		}
	}
	return $events;
}

function sync_events_to_local($events){
	foreach ($events as $event){
		
		$title = $event['title'];
		$location = $event['location'];
		$guest = $event['guest'];
		$description = $event['description'];
		$startTime = $event['startTime'];
		$endTime   = $event['endTime'];
		$owner = isset($event['owner']) ? $event['owner']:"" ;
		$googleID = $event['googleID'];

		$updated = date("Y-m-d H:i:s",strtotime($event['updated']));

		if ($event['action']=='delete'){
			$id = $event['id'];
			mysql_query("UPDATE events 
						SET is_deleted = 1, 
							updated = '$updated'
						WHERE id = $id;");
		} elseif ($event['action']=='update'){
			$id = $event['id'];
			

			mysql_query("UPDATE events 
						SET title = '$title', 
							location = '$location', 
							guest = '$guest', 
							description = '$description', 
							startTime = '$startTime', 
							endTime = '$endTime', 
							owner = '$owner', 
							googleID = '$googleID', 
							updated = '$updated' 
						WHERE id = $id");
			
		} elseif ($event['action']=='insert'){
			
			$userID = $event['userID'];

			mysql_query("INSERT INTO events 
						(userID, title, location, guest, description, startTime, endTime, owner, googleID, updated) 
						VALUES 
						($userID,'$title','$location','$guest','$description','$startTime','$endTime','$owner','$googleID', '$updated');");
		}
	}
}

//Only update google id to indentify the event has been synced with google
//TODO update other event info with google
function update_event_google_id($arr) {
	$id   = $arr['id'];
	$googleID = $arr['googleID'];
	mysql_query("UPDATE events SET googleID='$googleID' where id='$id'");
	return true;
}

//The function is not using any more. Using array_diff_googleid now.
function event_google_id_not_exist($googleID) {
	$result = mysql_query("SELECT id FROM events WHERE googleID='$googleID'");
	while($res=mysql_fetch_array($result)) {
		return false;
	}
	return true;
}


/*
* Task database function
*
*
*/

//grap tasks from database
// params: 
//	1 - userid
//  2 - type: 1- all, 2 - no_google_tasks 
function get_tasks_from_db($userID, $type=null) {
  $tasks = array();
	$sql = "SELECT id, userID, title, description, date, googleID, status, is_deleted, updated 
					FROM tasks 
					WHERE userID='$userID' ";
	if ($type == "active") {
		$sql .= "AND is_deleted=0";
	} else if ($type == "updated") {
		$updated = user::getUpdatedTimestamp("tasks");
		$sql .= " AND updated>'$updated'";
	}
	$result = mysql_query($sql);
  if($result != false) {
		while($res=mysql_fetch_array($result)) {
			$re['id'] = $res['id'];
			$re['userID'] = $res['userID'];
			$re['title'] = $res['title'];
			$re['description'] = $res['description'];
			$re['date'] = $res['date'];
			$re['status'] = $res['status'];
			$re['is_deleted'] = $res['is_deleted'];
			$re['updated'] = $res['updated'];
			$re['googleID'] = $res['googleID'];
			array_push($tasks,$re);
		}
	}
	return $tasks;
}

function update_tasks_to_local($arr) {
	for($i=0;$i<count($arr);$i++) {
		if ($arr[$i]['action']=='insert'){
			$userID = $arr[$i]['userID'];
			$title = $arr[$i]['title'];
			$description = $arr[$i]['description'];
			$date = is_date($arr[$i]['date']) ? $arr[$i]['date'] : 'null';
			$googleID = $arr[$i]['googleID'];
			$status = $arr[$i]['status'];
			$updated = $arr[$i]['updated'];
			mysql_query("INSERT INTO tasks 
									(userID, title, description, date, googleID, status, updated) 
									VALUES ($userID,'$title','$description','$date','$googleID', '$status','$updated');");
			
		} else if ($arr[$i]['action']=='update'){
			$id = $arr[$i]['id'];
			$userID = $arr[$i]['userID'];
			$title = $arr[$i]['title'];
			$description = $arr[$i]['description'];
			$date = is_date($arr[$i]['date']) ? $arr[$i]['date'] : 'null';
			$googleID = $arr[$i]['googleID'];
			$status = $arr[$i]['status'];
			$updated = add_time_offset($arr[$i]['updated'],1);

			mysql_query("UPDATE tasks
						SET title = '$title',
							description = '$description',
							date = '$date',
							status = '$status',
							updated = '$updated'
						WHERE id = '$id'");
						
		} else if ($arr[$i]['action']=='delete') {
			$id = $arr[$i]['id'];
			mysql_query("UPDATE tasks
						SET is_deleted = 1 
						WHERE id = '$id'");
						
		}
	}
	return true;
}

function sync_tasks_to_local($tasks) {
	foreach ($tasks as $task) {
		$title = $task['title'];

			$description = $task['description'];
			$date = is_date($task['date']) ? $task['date'] : 'null';
			$googleID = $task['googleID'];
			$status = $task['status'];
			$updated = date("Y-m-d H:i:s",strtotime($task['updated']));
		
		if ($task['action']=='insert'){
			$userID = $task['userID'];
			
			mysql_query("INSERT INTO tasks 
									(userID, title, description, date, googleID, status, updated) 
									VALUES ($userID,'$title','$description','$date','$googleID', '$status','$updated');");
			
		} else if ($task['action']=='update'){
			$id = $task['id'];

			mysql_query("UPDATE tasks
						SET title = '$title',
							description = '$description',
							date = '$date',
							status = '$status',
							updated = '$updated'
						WHERE id = '$id'");
						
		} else if ($task['action']=='delete') {
			$id = $task['id'];
			mysql_query("UPDATE tasks
						SET is_deleted = 1, 
							updated = '$updated'
						WHERE id = '$id'");
						
		}
	}
	return true;
}



//Only update google id to indentify the task has been synced with google
//TODO update other task info with google
function update_task_google_id($arr) {
	$id   = $arr['id'];
	$googleID = $arr['googleID'];
	mysql_query("UPDATE tasks SET googleID='$googleID' where id='$id'");
	return true;
}


function is_date( $str ) 
{ 
  $stamp = strtotime( $str ); 
  
  if (!is_numeric($stamp)) 
  { 
     return FALSE; 
  } 
  $month = date( 'm', $stamp ); 
  $day   = date( 'd', $stamp ); 
  $year  = date( 'Y', $stamp ); 
  
  if (checkdate($month, $day, $year)) 
  { 
     return TRUE; 
  } 
  
  return FALSE; 
} 

?>