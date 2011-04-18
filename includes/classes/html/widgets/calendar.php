<?php
/**
 * Html Calendar Widget Class
 * @package Html
 */
class htmlWidget_calendar implements htmlWidgetPlugin {
	protected $tableElement;
	
	public function __construct(){
		$this->tableElement = htmlBase::newElement('table')
		->addClass('htmlcal')
		->setCellPadding(0)
		->setCellSpacing(0);
		
		$this->cal = '"CAL_GREGORIAN';
		$this->format = '%Y%m%d';
		$this->today = '';
		$this->day = 1;
		$this->month = '';
		$this->year = '';
		$this->pmonth = '';
		$this->pyear = '';
		$this->nmonth = '';
		$this->nyear = '';
		$this->wday_names = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
		
		$this->dateNow();
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->tableElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Classes From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->tableElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->tableElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$this->buildCal();
		return $this->tableElement->draw();
	}
	/* Required Classes From Interface: htmlElementPlugin --END-- */
	
	public function attr($name, $val = false){
		if ($val !== false){
			$this->tableElement->attr($name, $val);
		}else{
			if ($this->tableElement->hasAttr($name)){
				return $this->tableElement->attr($name);
			}else{
				return '';
			}
		}
		return $this;
	}
	
	function dateNow($month = '', $year = ''){
		if (empty($month)){
			$this->month = strftime("%m",time());
		}else{
			$this->month = $month;
		}
		
		if (empty($year)){
			$this->year = strftime("%Y",time());	
		}else{
			$this->year = $year;
		}
		
		$this->today = strftime("%d",time());		
		$this->pmonth = $this->month - 1;
		$this->pyear = $this->year - 1;
		$this->nmonth = $this->month + 1;
		$this->nyear = $this->year + 1;
		return $this;
	}

	function daysInMonth($month, $year){
		if (empty($year)){
			$year = $this->dateNow("%Y");
		}

		if (empty($month)){
			$month = $this->dateNow("%m");
		}
		
		if ($month == 2){
			if ($this->isLeapYear($year)){
				return 29;
			}else{
				return 28;
			}
		}else if ($month==4 || $month==6 || $month==9 || $month==11){
			return 30;
		}else{
			return 31;
		}
	}

	function isLeapYear($year){
		return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0); 
	}

	function dayOfWeek($month, $year){ 
		if ($month > 2){
			$month -= 2;
		}else{
			$month += 10;
			$year--;
		}
		 
		$day = (
			floor((13 * $month - 1) / 5) + 
			$this->day + ($year % 100) + 
			floor(($year % 100) / 4) + 
			floor(($year / 100) / 4) - 2 * 
			floor($year / 100) + 77
		); 
		
		$weekday_number = (($day - 7 * floor($day / 7))); 
		 
		return $weekday_number; 
	}

	function getWeekDay(){
		$week_day = $this->dayOfWeek($this->month, $this->year);
		return $week_Day;
	}
	
	public function buildCal(){
		$monthDrop = htmlBase::newElement('selectbox')->setName('cal_month');
		for($i=1; $i<13; $i++){
			$monthDrop->addOption($i, $i);
		}
		
		$yearDrop = htmlBase::newElement('selectbox')->setName('cal_year');
		for($i=(date('Y') - 20); $i<date('Y') + 20; $i++){
			$yearDrop->addOption($i, $i);
		}
		
		$this->tableElement->addHeaderRow(array(
			'columns' => array(
				array('addCls' => 'htmlcal-curmonthyear', 'colspan' => 7, 'text' => '<div style="position:relative;"><span class="ui-icon ui-icon-circle-triangle-w"></span><span class="ui-icon ui-icon-circle-triangle-e"></span>Month & Year: <b>' . $monthDrop->selectOptionByValue($this->month)->draw() . ' / ' . $yearDrop->selectOptionByValue($this->year)->draw() . '</b></div>')
			)
		));
		
		$dayNameCols = array();
		for($i=0; $i<7; $i++){
			$dayNameCols[] = array(
				'addCls' => 'htmlcal-day-header',
				'text' => $this->wday_names[$i]
			);
		}
		
		$this->tableElement->addHeaderRow(array(
			'addCls' => 'htmlcal-days-header',
			'columns' => $dayNameCols
		));
		
		$wday = $this->dayOfWeek($this->month, $this->year);
		$no_days = $this->daysInMonth($this->month, $this->year);
		$count = 1;
		$curRowCols = array();
		if ($wday > 0){
			for($i=0; $i<$wday; $i++){
				$curRowCols[] = array(
					'addCls' => 'htmlcal-day htmlcal-day-unselectable',
					'text' => '&nbsp;'
				);
				$count++;
			}
		}
		
		for($i=1; $i<=$no_days; $i++){
			$addCls = 'htmlcal-day';
			if ($i == $this->today){
				$addCls .= ' htmlcal-today';
			}
				
			if ($count > 6){
				$curRowCols[] = array(
					'addCls' => $addCls,
					'text' => $i,
					'attr' => array(
						'day' => $i
					)
				);
					
				$this->tableElement->addBodyRow(array(
					'columns' => $curRowCols
				));
				
				$curRowCols = array();
				$count = 0;
			}else{
				$curRowCols[] = array(
					'addCls' => $addCls,
					'text' => $i,
					'attr' => array(
						'day' => $i
					)
				);
			}
			$count++;
		}
		
		if (!empty($curRowCols)){
			if (sizeof($curRowCols) < 7){
				$n = sizeof($curRowCols);
				for($i=$n; $i<=6; $i++){
					$curRowCols[] = array(
						'addCls' => 'htmlcal-day htmlcal-day-unselectable',
						'text' => '&nbsp;'
					);
				}
			}
			$this->tableElement->addBodyRow(array(
				'columns' => $curRowCols
			));
			$curRowCols = array();
		}
		return $this;
	}
}
?>