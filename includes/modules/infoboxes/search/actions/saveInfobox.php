<?php
$SearchOption = array();
foreach($_POST['option'] as $type => $oInfo){
	foreach($oInfo as $oID){
		$SearchOption[$type][$oID]['option_type'] = $type;
		$SearchOption[$type][$oID]['option_id'] = (int)$oID;
		$SearchOption[$type][$oID]['search_title'][Session::get('languages_id')] = $_POST['option_heading'][$type][$oID];
		if($type == 'price' || $type == 'priceppr'){
			$SearchOption[$type][$oID]['price_start'] = $_POST['option_sort'][$type]['start'][$oID];
			$SearchOption[$type][$oID]['price_stop'] = $_POST['option_sort'][$type]['stop'][$oID];
		} else {
			$SearchOption[$type][$oID]['option_sort'] = $_POST['option_sort'][$type][$oID];
		}
	}
}
$WidgetProperties['searchOptions'] = $SearchOption;
?>