<?php
	if (isset($_POST['boxID'])){
		$WidgetProperties['boxID'] = $_POST['boxID'];
	}

	if (isset($_POST['hasHeader'])){
		$WidgetProperties['hasHeader'] = true;
	}else{
		$WidgetProperties['hasHeader'] = false;
	}

	if (isset($_POST['hasButton'])){
		$WidgetProperties['hasButton'] = true;
	}else{
		$WidgetProperties['hasButton'] = false;
	}

	if (isset($_POST['hasGeographic'])){
		$WidgetProperties['hasGeographic'] = true;
	}else{
		$WidgetProperties['hasGeographic'] = false;
	}

	if (isset($_POST['hasLP'])){
		$WidgetProperties['hasLP'] = true;
	}else{
		$WidgetProperties['hasLP'] = false;
	}
	if (isset($_POST['hasTimesHeader'])){
		$WidgetProperties['hasTimesHeader'] = true;
	}else{
		$WidgetProperties['hasTimesHeader'] = false;
	}
	if (isset($_POST['showSubmit'])){
		$WidgetProperties['showSubmit'] = true;
	}else{
		$WidgetProperties['showSubmit'] = false;
	}

	if (isset($_POST['showShipping'])){
		$WidgetProperties['showShipping'] = true;
	}else{
		$WidgetProperties['showShipping'] = false;
	}

	if (isset($_POST['showQty'])){
		$WidgetProperties['showQty'] = true;
	}else{
		$WidgetProperties['showQty'] = false;
	}

	if (isset($_POST['showTimes'])){
		$WidgetProperties['showTimes'] = true;
	}else{
		$WidgetProperties['showTimes'] = false;
	}

	if (isset($_POST['showCategories'])){
		$WidgetProperties['showCategories'] = true;
	}else{
		$WidgetProperties['showCategories'] = false;
	}

	if (isset($_POST['showPickup'])){
		$WidgetProperties['showPickup'] = true;
	}else{
		$WidgetProperties['showPickup'] = false;
	}

	if (isset($_POST['showDropoff'])){
		$WidgetProperties['showDropoff'] = true;
	}else{
		$WidgetProperties['showDropoff'] = false;
	}

?>