<?php
	echo add_time_offset("2011-11-06T13:30:28.071Z",1),"<br>";
	echo date("Y-m-d H:i:s",strtotime("2011-11-06T13:30:28.071Z"));


function add_time_offset($timestr,$type=1){
	if ($type==1){
		$d = strtotime($timestr)+11*60*60;
	} else {
		$d = strtotime($timestr)-11*60*60;
	}
	return date("Y-m-d H:i:s",$d);
	
}
?>