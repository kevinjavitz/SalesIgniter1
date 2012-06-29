<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxEventFeatured extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('eventFeatured');
	}

	public function show(){
        global $appExtension;

        $htmlText = '';

        $Qevents = Doctrine_Query::create()
        ->from('EventManagerEventsDescription ed')
        ->leftJoin('ed.EventManagerEvents em')
        ->where('ed.events_featured=?', '1')
        ->andWhere('em.events_start_date >=?', date("Y-m-d h:i:s"))
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $date = date_parse($Qevents[0]['EventManagerEvents']['events_start_date']);
        $month = date('F', mktime(0,0,0,$date['month'],1));
        $htmlText .= '<div style="font-weight:bold;font-size:13px;margin-left:25px;display:inline-block;vertical-align:top">'.$month.','.$date['day'].'</div>';
        $htmlText .= '<div style="margin-top:10px;vertical-align:bottom;color:#ffffff;display:inline-block;"><a href="' . itw_app_link('ev_id='.$dates['events_id'],'eventManager','show_event/default').'">'.$Qevents[0]['events_title'].'</a></div>';
        if($Qevents[1]){
            $date = date_parse($Qevents[1]['EventManagerEvents']['events_start_date']);
            $month = date('F', mktime(0,0,0,$date['month'],1));
            $htmlText .= '<div style="vertical-align:top;display:inline-block;font-weight:bold;font-size:13px;margin-left:20px;">'.$month.','.$date['day'].'</div>';
            $htmlText .= '<div style="margin-top:10px;vertical-align:bottom;color:#ffffff;display:inline-block;"><a href="' . itw_app_link('ev_id='.$Qevents[1]['EventManagerEvents']['events_id'],'eventManager','show_event/default').'">'.$Qevents[1]['events_title'].'</a></div>';
        }

        $this->setBoxContent($htmlText);
        return $this->draw();
	}
}
?>