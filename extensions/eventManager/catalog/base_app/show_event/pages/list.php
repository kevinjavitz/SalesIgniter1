<?php
	$events = Doctrine_Query::create()
	->from('EventManagerEvents eve')
	->leftJoin('eve.EventManagerEventsDescription eved')
	->where('eved.language_id = ?', Session::get('languages_id'))
	->orderBy('events_start_date')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$contentHtml = '';
	foreach($events as $evInfo){
		$contentHtml .= "<div class='list_ev' style='margin-bottom:20px;'><b>Event Name:</b> ".$evInfo['EventManagerEventsDescription'][0]['events_title']."<br/>";
		$contentHtml .= '<b>Date:</b> '. strftime(sysLanguage::getDateFormat('short'), strtotime($evInfo['events_start_date'])).' - '.strftime(sysLanguage::getDateFormat('short'), strtotime($evInfo['events_end_date']))."<br/>";
		$contentHtml .= "<a class='moreinfo' href='".itw_app_link('appExt=eventManager&ev_id='.$evInfo['events_id'],'show_event','default')."'><b>More info</b></a>"."</div>";
	}


	$pageTitle = 'List of Events';
	$pageContents = stripslashes($contentHtml);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>