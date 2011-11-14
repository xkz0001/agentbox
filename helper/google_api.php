<?php

/************************full sync function***********************/
/********************************************************************/


//get items which need to insert/update/delete to google

function get_items_to_google($google_items, $local_items, & $delete_local_items){
	$items = array();
	$delete_local_items = array();
	foreach($local_items as $local_item){
		//if local item is deleted and there is a google item with matched google id.
		//the item need to be deleted.
		if ($local_item['is_deleted']) {
			if(recursive_array_search($google_items,$local_item['googleID'],'googleID') !== false) {
				$local_item['action'] = 'delete';
				$items[] = $local_item;
			}
		} else {
			//if local item googleid is empty 
			if (strlen($local_item['googleID'])==0){
				$local_item['action'] = 'insert';
				$items[] = $local_item;
			} else {
				$key = recursive_array_search($google_items,$local_item['googleID'],'googleID'); 
				if( $key=== false){
					$local_item['action'] = 'delete';
					$delete_local_items[] = $local_item;
				} else {
					//if local item is find in google items and it is newer than google one, it need to be updated
					if (latest_updated($local_item['updated'], $google_items[$key]['updated'])=="local") {
						//echo "local:",$local_item['updated'],"google:",$google_items[$key]['updated'],"<br>";
						$local_item['action'] = 'update';
						$items[]=$local_item;
					}
				}
			}
		}
	}
	return $items;
}

//get item which need to update/insert to local
function get_items_to_local($google_items,$local_items) {
	$items = array();
	foreach($google_items as $google_item){		
		//if local item is not find in google items, it need to be inserted
		$key = recursive_array_search($local_items,$google_item['googleID'],'googleID'); 
		if( $key=== false){
			$google_item['action'] = 'insert';
			$items[] = $google_item;
		} else {
			//if local item is find in google items and it is newer than google one, it need to be updated
			if (latest_updated($local_items[$key]['updated'], $google_item['updated'])=="google") {
				$google_item['action'] = 'update';
				$google_item['id'] = $local_items[$key]['id'];
				$items[]=$google_item;
			}
		}
	}
	return $items;
}

/************************updated sync function***********************/
/********************************************************************/


function get_updated_items_to_google($google_items, $local_items){
	$items = array();
	$delete_local_items = array();
	foreach($local_items as $local_item){
		//if local item is deleted and there is a google id, delete
		if ($local_item['is_deleted'] && strlen($local_item['googleID'])!=0 ) {
			$local_item['action'] = 'delete';
			$items[] = $local_item;			
		} else {
			//if local item googleid is empty, insert
			if (strlen($local_item['googleID'])==0){
				$local_item['action'] = 'insert';
				$items[] = $local_item;
			} else {
				$key = recursive_array_search($google_items,$local_item['googleID'],'googleID');
				//if no match google id, update
				if( $key=== false){
					$local_item['action'] = 'update';
					$items[]=$local_item;
				} else {
					//if local item is find in google items and it is newer than google one, it need to be updated
					if (latest_updated($local_item['updated'], $google_items[$key]['updated'])=="local") {
						//echo "local:",$local_item['updated'],"google:",$google_items[$key]['updated'],"<br>";
						$local_item['action'] = 'update';
						$items[]=$local_item;
					}
				}				
			}
		}
	}
	return $items;
}


//get item which need to update/insert to local
function get_updated_items_to_local($google_items,$local_items, $module) {
	$items = array();

	foreach($google_items as $google_item){		
		//look for local item with google item id
		$matched_local = find_item_by_google_id($google_item['googleID'],$module);
		if ($matched_local) {
			//delete item from local if google item status is deleted
			if ($google_item['is_deleted']) {
				$google_item['action'] = 'delete';
				$google_item['id'] = $matched_local;
				$items[] = $google_item;	
			} else {
				//if both updated, compare timestamp, if not, the google item will update local
				$key = recursive_array_search($local_items,$google_item['googleID'],'googleID'); 
				if( $key=== false){
					$google_item['action'] = 'update';
					$google_item['id'] = $matched_local;
					$items[] = $google_item;
				} else {
					//if local item is find in google items and it is newer than google one, it need to be updated
					if (latest_updated($local_items[$key]['updated'], $google_item['updated'])=="google") {
						$google_item['action'] = 'update';
						$google_item['id'] = $local_items[$key]['id'];
						$items[]=$google_item;
					}
				}
			}
		} else {
			if ($google_item['is_deleted'] == false ) {
				$google_item['action'] = 'insert';
				$items[] = $google_item;
			}
		}
	}
	return $items;
}




/*
    for full google service name list, 
    check http://code.google.com/apis/gdata/faq.html#clientlogin

*/

//get contacts from google type: full/updated
function get_contacts_from_google($uId, $type) {
	try {
		$gdata = user::getZendServiceClient('cp');
		$gdata->setMajorProtocolVersion(3);
		  
		// Zend query and get feed of all results
		$query = new Zend_Gdata_Query(
			'http://www.google.com/m8/feeds/contacts/default/full');
		$query->maxResults = 0;
		
		//google only keep tombstone for 30 days. so you still need use "full" type to sync deleted contacts.
		if ($type=="updated"){
			$query->updatedMin = add_time_offset(user::getUpdatedTimestamp('contacts'),2);
			$query->setParam('showdeleted', 'true');
		}

		$query->setParam('orderby', 'lastmodified');
		$query->setParam('sortorder', 'descending');
		$query->setParam('sortorder', 'descending');
		 

		
		$feed = $gdata->getFeed($query);
		$results = array();
		foreach($feed as $entry){
			
			$re['userID']=$uId;//TODO get from session
			$re['googleID'] = $entry->id->text;
			$xml = simplexml_load_string($entry->getXML());
			if(isset($xml->deleted)) { $deleted = true; } else { $deleted = false; } 
			$re['is_deleted']=$deleted;
			$re['name'] = (string) $entry->title;
			$re['orgName'] = (string) $xml->organization->orgName; 
			$re['orgTitle'] = (string) $xml->organization->orgTitle; 
			$emailAddress = Array();
			foreach ($xml->email as $e) {
				$emailAddress[] = (string) $e['address'];
			}
			$re['emailAddress'] = join(',', $emailAddress);
			$phoneNumber = Array();
			foreach ($xml->phoneNumber as $p) {
				$phoneNumber[] = (string) $p;
			}
			$re['phoneNumber'] = join(',', $phoneNumber);
			$website = Array();
			foreach ($xml->website as $w) {
				$website[] = (string) $w['href'];
			}
			$re['website'] = join(',', $website);
			$re['updated'] = $entry->updated->text;
			$results[] = $re;
		}
		return $results;
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage()); 
	} 
}

function create_contact_to_google($contact,$gdata){
	try {
		// create new entry
		$doc  = new DOMDocument();
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gd', 'http://schemas.google.com/g/2005');
		$doc->appendChild($entry);
		
		// add name element
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);
		$fullName = $doc->createElement('gd:fullName', $contact['name']);
		$name->appendChild($fullName);
		
		// add emailAddress elements
		$arr = explode(',', $contact['emailAddress']);
		foreach ($arr as $a) {
			$email = $doc->createElement('gd:email'); 
			$email->setAttribute('address', $a);
			$email->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
			$entry->appendChild($email);          
		}
		
		// add orgName element
		$org = $doc->createElement('gd:organization');
		$org->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
		$entry->appendChild($org);
		$orgName = $doc->createElement('gd:orgName', $contact['orgName']);
		$org->appendChild($orgName);
		// add orgTitle element
		$orgTitle = $doc->createElement('gd:orgTitle', $contact['orgTitle']);
		$org->appendChild($orgTitle);
		
		// add website elements
		$arr = explode(',', $contact['website']);
		foreach ($arr as $w) {
			$website = $doc->createElement('gd:website'); 
			$website->setAttribute('href', $w);
			$website->setAttribute('rel' ,'http://schemas.google.com/g/2005#profile');
			$entry->appendChild($website);          
		}
		
		// add phoneNumber elements
		$arr = explode(',', $contact['phoneNumber']);
		foreach ($arr as $p) {
			if(!empty($p)) {
				$phoneNumber = $doc->createElement('gd:phoneNumber', $p ); 
				$phoneNumber->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
				$entry->appendChild($phoneNumber);
			}
		}
		// insert entry
		$entryResult = $gdata->insertEntry($doc->saveXML(), 'http://www.google.com/m8/feeds/contacts/default/full');
		$contact['googleID'] = $entryResult->id;
		$contact['updated'] = $entryResult->updated->text;
		update_google_id_time_to_local($contact,'contacts');
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function update_contact_to_google($contact, $gdata){
	try {
		
		$query = new Zend_Gdata_Query($contact['googleID']);

		$entry = $gdata->getEntry($query);
		$xml = simplexml_load_string($entry->getXML());

		$xml->name->fullName = $contact['name'];
		
		$arr = explode(',', $contact['emailAddress']);
		foreach ($arr as $a) {
			if (isset($xml->email['work'])){
				$xml->email->address = $a;
			}   
		}
		
		$xml->organization->orgName=$contact['orgName'];
		$xml->organization->orgTitle=$contact['orgTitle'];
		
		// add website elements
		$arr = explode(',', $contact['website']);
		foreach ($arr as $w) {
			$xml->website->href=$w; 
		}
		
		// add phoneNumber elements
		$arr = explode(',', $contact['phoneNumber']);
		foreach ($arr as $p) {
			if(!empty($p)) {
				$xml->phoneNumber=$p;
			}
		}
		$extra_header = array();
		$extra_header['If-Match']='*';
		$entryResult = $gdata->updateEntry($xml->saveXML(),$entry->getEditLink()->href,null,$extra_header);
		$contact['updated'] = $entryResult->updated->text;
		update_google_id_time_to_local($contact,'contacts');
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function delete_contact_to_google($contact,$gdata){
	try {
		/*
		//there is bug in version 3, have to switch to version 1
		
		*/
		$gdata->setMajorProtocolVersion(1);
		$query = new Zend_Gdata_Query(str_replace('base','full',$contact['googleID']));
		$entry = $gdata->getEntry($query);
		if ($entry) {
			$gdata->delete($entry);
		}
		//$gdata->delete(str_replace('base','full',$contact['googleID']));
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function sync_contacts_to_google($contacts) {
	try {
		$gdata = user::getZendServiceClient('cp');	
		foreach($contacts as $contact){
			if ($contact['action']=='delete') {
				delete_contact_to_google($contact, $gdata);
			} else {
				if ($contact['action']=='insert') {
					create_contact_to_google($contact, $gdata);
				} else if($contact['action']=='update') {
					update_contact_to_google($contact, $gdata);
				}
			}
		}
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

/*
//*******************Calendar****************************
*/
function get_events_from_google($uId, $type) {
	try {
		//for full google service name list, 
		//check http://code.google.com/apis/gdata/faq.html#clientlogin
		//cl: calendar
		$gdata = user::getZendServiceClient('cl');

		$query = $gdata->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setSortOrder('ascending');

		
		//google only keep tombstone for 30 days. so you still need use "full" type to sync deleted contacts.
		if ($type=="updated"){
			$query->updatedMin = add_time_offset(user::getUpdatedTimestamp('events'),2);
			$query->setParam('showdeleted', 'true');
		}

		$eventFeed = $gdata->getCalendarEventFeed($query);
		$results = array();
		foreach ($eventFeed as $event) {

			$googleID = $event->id->text;
			if(strpos($event->eventstatus->value,"canceled")===false) { $deleted = false; } else { $deleted = true; } 
			$re['is_deleted']=$deleted;
			$re['userID']=$uId;
			$re['googleID'] = $googleID;
			$re['title'] = $event->title->text;
			$re['description'] = $event->content->text;
			$re['updated'] = $event->updated->text;
			
			if ($event->when){
				foreach ($event->when as $when) {
					$re['startTime'] = $when->startTime;
					$re['endTime'] = $when->endTime;
					break;
				}
			} elseif ($event->recurrence ){
				$arr = explode("\n", $event->recurrence->text);
				foreach ($arr as $when){
					if (strpos($when,"DTSTART")!==false){
						$time_arr=explode(':',$when);
						$re['startTime'] = $time_arr[1];
					}
					if (strpos($when,"DTEND")!==false){
						$time_arr=explode(':',$when);
						$re['endTime'] = $time_arr[1];
					}
				}
				foreach ($event->recurrence as $when) {
					
					$re['startTime'] = $when->startTime;
					$re['endTime'] = $when->endTime;
					break;
				}
			}

			$guest = array();
			foreach($event->who as $who){
				$guest[] = $who->getEmail();
			}
			$re['guest'] =  join(',', $guest);
			//echo $event->where->valueString;
			foreach ($event->where as $where) {
				$re['location'] = $where->valueString;
				break;
			}
			$results[] = $re;		
		}
		return $results;
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage()); 
	} 
}


function sync_events_to_google($events) {
	try {
		$gdata = user::getZendServiceClient('cl');	
		foreach($events as $event){
			if ($event['action']=='delete') {
				delete_event_to_google($event, $gdata);
			} else {
				if ($event['action']=='insert') {
					create_event_to_google($event, $gdata);
				} else if($event['action']=='update') {
					update_event_to_google($event, $gdata);
				}
			}
		}
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function create_event_to_google($event,$gdata) {
	try {
		// create new entry
		$newEntry = $gdata->newEventEntry();
		// add title element
		$newEntry->title = $gdata->newTitle(trim($event['title']));
		// add location element
		$newEntry->where = array($gdata->newWhere($event['location']));
		// add guest element
		$guests = explode(',', $event['guest']);
		if (count($guests)>0){
			$who = array();
			foreach ($guests as $guest){
				$who[]=$gdata->newWho($guest);
			}
			$newEntry->who = $who;
		}
		// add description element
		$newEntry->content = $gdata->newContent($event['description']);
		$newEntry->content->type = 'text';
		// add starttime and endtime element
		$when = $gdata->newWhen();
		$start = explode(' ', $event['startTime']);
		$end = explode(' ', $event['endTime']);
		$when->startTime = "{$start[0]}T{$start[1]}.000+11:00";
		$when->endTime = "{$end[0]}T{$end[1]}.000+11:00";
		$newEntry->when = array($when);
		$result = $gdata->insertEvent($newEntry);
		$event['googleID'] = $result->id;
		$event['update'] = $result->updated->text;
		update_google_id_time_to_local($event,'events');	
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function update_event_to_google($event,$gdata) {
	try {
		$query = $gdata->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setEvent(get_google_id($event['googleID']));
		
		if ($eventExisting = $gdata->getCalendarEventEntry($query)){
			
			$eventExisting->title = $gdata->newTitle($event['title']);

			$eventExisting->where = array($gdata->newWhere($event['location']));
			
			$guests = explode(',', $event['guest']);
			
			$who = array();
			foreach ($guests as $guest){
				if (validEmail($guest)){
					$who[]=$gdata->newWho($guest);
				}
			}
			if (count($who)>0){
				$eventExisting->who = $who;
			}
		

			$eventExisting->content = $gdata->newContent($event['description']);
			$eventExisting->content->type = 'text';
			$when = $gdata->newWhen();
			$start = explode(' ', $event['startTime']);
			$end = explode(' ', $event['endTime']);
			$when->startTime = "{$start[0]}T{$start[1]}.000+11:00";
			$when->endTime = "{$end[0]}T{$end[1]}.000+11:00";
			$eventExisting->when = array($when);	
			$eventExisting->save();
			$result = $gdata->getCalendarEventEntry($query);
			$event['updated'] = $result->updated->text;
			update_google_id_time_to_local($event,'events');
		} else {
			echo("no matching event find");
		}
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}

function delete_event_to_google($event,$gdata) {
	try {		
		$query = $gdata->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setEvent(get_google_id($event['googleID']));
		
		if ($eventExisting = $gdata->getCalendarEventEntry($query)){
			$eventExisting->delete();
		} else {
			echo("no matching event find");
		}
		
	} catch (Exception $e) {
		echo('ERROR:' . $e->getMessage());
	}
}


//To refine the local data which are not in google
function array_diff_googleid($array1,$array2) {
	$arr_diff = array();
	foreach ($array1 as $arr) {
		if(recursive_array_search($array2,$arr['googleID'],'googleID') === false) {
			$arr_diff[] = $arr;
		}
	}
	return $arr_diff;
}



/*
*****************************************Task************************************
*/


function get_tasks_from_google($uId,$ts,$type){
	$lists = $ts->tasklists->listTasklists();
	$tresults = array();
	foreach ($lists['items'] as $list) {
		$query = array();
		if ($type=="updated"){
			$query = array("showDeleted"=>true, "updatedMin"=>add_time_offset(user::getUpdatedTimestamp('tasks'),2));
		}
		$tasks = $ts->tasks->listTasks($list['id'], $query);
		if (isset($tasks['items'])){
			foreach ($tasks['items'] as $task) {
				
				if(isset($task['deleted'])) { $deleted = true; } else { $deleted = false; } 
				$re['is_deleted']=$deleted;
				$re['googleID'] = $task['id'];
				$re['userID']=$uId;
				$re['title'] = $task['title'];
				$re['description'] = isset($task['notes']) ? $task['notes']:"";
				$re['date'] = isset($task['due']) ? $task['due']:"";
				$re['updated'] = $task['updated'];
				$re['status'] = $task['status'];
				$tresults[] = $re;		
			}
		}
	}
    return $tresults;
}



function delete_task_to_google($task,$ts_list, $ts){
	$result = $ts->tasks->delete($ts_list['id'], $task['googleID']);
}

function create_task_to_google($task,$ts_list, $ts){
	$new_task = new Task();
	$new_task->setTitle($task['title']);
	$new_task->setNotes($task['description']);
	if ($task['date']){
		$new_task->setDue("{$task['date']}T00:00:00.000");
	}
	$new_task->setStatus(($task['status']=="completed")? "completed":"needsAction");

	$result=$ts->tasks->insert($ts_list['id'], $new_task);
	$task['googleID'] = $result['id'];
	$task['updated'] = $result['updated'];
	update_google_id_time_to_local($task,"tasks");
}

function update_task_to_google($task,$ts_list, $ts){
	$existing_task = $ts->tasks->get($ts_list['id'], $task['googleID']);				
	$new_task = new Task($existing_task);
	$new_task->setTitle($task['title']);
	$new_task->setNotes($task['description']);
	if ($task['date']){
		$new_task->setDue("{$task['date']}T00:00:00.000+00:00");
	}
	$new_task->setStatus(($task['status']=="completed")? "completed":"needsAction");
	$new_result=$ts->tasks->update($ts_list['id'], $task['id'], $new_task);
	$task['updated'] = $new_result['updated'];
	update_google_id_time_to_local($task,"tasks");

}

function sync_tasks_to_google($ts,$tasks){
	$lists = $ts->tasklists->listTasklists();
	foreach ($lists['items'] as $list) {
		foreach($tasks as $task){
			if ($task['action']=='delete') {
				delete_task_to_google($task,$list,$ts);
			} else if ($task['action']=='insert') {
				create_task_to_google($task,$list,$ts);
			} else {
				update_task_to_google($task,$list,$ts);
			}
		}
	}
}




/*
for find matching google id
*/
function recursive_array_search($haystack, $needle, $index = null) {
	$aIt = new RecursiveArrayIterator($haystack);
	$it = new RecursiveIteratorIterator($aIt);
	while($it->valid())	{
		if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
			return $aIt->key();
		}
		$it->next();
	}
	return false;
}

//get googleid from edit url
function get_google_id($url) {
	$pos = strrpos ( $url, '/' );
	if ($pos !== false) {
		return substr ( $url, $pos+1 );
	} else {
		return '';
	}
}


//type 1 : convert google time to local
//type 2 : convert local time to google
//TODO: apply timezone here
function add_time_offset($timestr,$type=1){
	if ($type==1){
		$d = strtotime($timestr)+11*60*60;
	} else {
		$d = strtotime($timestr)-11*60*60;
	}
	return date("Y-m-d\TH:i:s.000\Z",$d);
	
}

//compare local update time and google update time to decide which side need to updated.
function latest_updated($local_time, $google_time){
	$datetime_local = new DateTime($local_time);
	$datetime_google = new DateTime($google_time);
	$datetime_google->add(new DateInterval('PT11H'));
	//echo $datetime_local->format('Y-m-d H:i:s'),"*******",$datetime_google->format('Y-m-d H:i:s'),"<br>"; 
	if (strtotime($datetime_local->format("Y-m-d H:i:s"))<strtotime($datetime_google->format("Y-m-d H:i:s"))){
		return "google";
	}else if (strtotime($datetime_local->format("Y-m-d H:i:s"))>strtotime($datetime_google->format("Y-m-d H:i:s"))){
		return "local";
	}else {
		return "";
	}
}

//valid guest email. google only accept valid email address
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         $isValid = false;
      }
   }
   return $isValid;
}