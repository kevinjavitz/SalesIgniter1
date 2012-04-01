<?php
class InfoBoxEventList extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('eventList', __DIR__);
	}
	
	public function getEventsList(){
		global $appExtension;

		$events = Doctrine_Query::create()
			->from('EventManagerEvents eve')
			->leftJoin('eve.EventManagerEventsDescription eved')
			->where('eved.language_id = ?', Session::get('languages_id'))
			->orderBy('events_start_date')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$page_list = '<ul class="eventListInfobox">';
		foreach($events as $evInfo){
			$title = '<span class="title">'.$evInfo['EventManagerEventsDescription'][0]['events_title'].'</span>';
			if($evInfo['events_start_date'] == $evInfo['events_end_date']){
				$date = strftime(sysLanguage::getDateFormat('long'), strtotime($evInfo['events_start_date']));
			}else{
				if(date('n', strtotime($evInfo['events_start_date'])) == date('n', strtotime($evInfo['events_end_date']))){
					$date = date('d', strtotime($evInfo['events_start_date'])). ' - '.date('d', strtotime($evInfo['events_end_date'])).' '.date('F', strtotime($evInfo['events_start_date'])).', '.date('Y', strtotime($evInfo['events_start_date']));
				}else{
					$date = strftime(sysLanguage::getDateFormat('long'), strtotime($evInfo['events_start_date'])). ' - '.strftime(sysLanguage::getDateFormat('long'), strtotime($evInfo['events_end_date']));
				}

			}
			$linkEl = htmlBase::newElement('a')
			->html('<span class="date">'.$date.'</span><br/>'.$title);

			//$linkEl->attr('target', '_blank');

			$linkEl->setHref(itw_app_link('appExt=eventManager&ev_id='.$evInfo['events_id'],'show_event','default'));

		
			$page_list .= '<li>'. $linkEl->draw() . '</li>';
		}
		$linkEl = htmlBase::newElement('a')
		->html('View All Events');
		//$linkEl->attr('target', '_blank');
		$linkEl->setHref(itw_app_link('appExt=eventManager','show_event','list'));

		$page_list .= '<li class="viewall">'. $linkEl->draw() . '</li>';
		return $page_list.'</ul>';
	}
	
	public function show(){
		
		$this->setBoxContent($this->getEventsList());
		
		return $this->draw();
	}
}
?>