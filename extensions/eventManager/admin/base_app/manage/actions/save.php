<?php
/*
	PPR Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/


	$event_start_date = $_POST['events_start_date'];
	$event_end_date = $_POST['events_end_date'];

	$Events = Doctrine_Core::getTable('EventManagerEvents');
	if (isset($_GET['eID'])){
		$Event = $Events->find((int) $_GET['eID']);
	}else{
		$Event = $Events->getRecord();
	}
	

	$Event->events_start_date = $event_start_date;
	$Event->events_end_date = $event_end_date;


$languages = tep_get_languages();
$EventsDescription =& $Event->EventManagerEventsDescription;
for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
	$lID = $languages[$i]['id'];

	$EventsDescription[$lID]->language_id = $lID;
	$EventsDescription[$lID]->events_title = $_POST['events_title'][$lID];
	$EventsDescription[$lID]->events_description_text = $_POST['events_description'][$lID];

}
$Event->save();
        EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $Event->events_id, null, 'default'), 'redirect');
?>