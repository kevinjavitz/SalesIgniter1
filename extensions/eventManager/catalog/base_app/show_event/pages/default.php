<?php
$events = Doctrine_Query::create()
	->from('EventManagerEvents eve')
	->leftJoin('eve.EventManagerEventsDescription eved')
	->where('eved.language_id = ?', Session::get('languages_id'))
	->andWhere('eve.events_id = ?', $_GET['ev_id'])
	->orderBy('events_start_date')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$evInfo = $events[0];
	$myDate = date('M d',strtotime($evInfo['events_start_date'])).' - '.date('M d Y', strtotime($evInfo['events_end_date']));

	$contentHtml = 'Name: '. $evInfo['EventManagerEventsDescription'][0]['events_title']."<br/><br/>Date of Event: ".$myDate."<br/><br/>Description: ".$evInfo['EventManagerEventsDescription'][0]['events_description_text'];

	$pageTitle = stripslashes($evInfo['EventManagerEventsDescription'][0]['events_title']);
	$pageContents = stripslashes($contentHtml);
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
