<?php
$SearchOption = array();
foreach($_POST['option'] as $type => $oInfo){
	foreach($oInfo as $oID){
		$SearchOption[$type][$oID]['option_type'] = $type;
		$SearchOption[$type][$oID]['option_id'] = (int)$oID;
		$SearchOption[$type][$oID]['option_sort'] = $_POST['option_sort'][$type][$oID];
		$SearchOption[$type][$oID]['search_title'][Session::get('languages_id')] = $_POST['option_heading'][$type][$oID];;
	}
}
$WidgetProperties['searchOptions'] = $SearchOption;
?>