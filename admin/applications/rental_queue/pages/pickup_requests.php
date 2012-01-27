<?php

$Qcheck = Doctrine_Query::create()
	->select('MAX(pickup_requests_id) as nextId')
	->from('PickupRequests')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$TableHidden = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('ui-widget ui-widget-content pickupRequestTable')
	->css(array(
		'width' => '100%'
	))
	->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
	->attr('language_id', Session::get('languages_id'));

$TableHidden->addHeaderRow(array(
		'addCls' => 'ui-state-hover pickupRequestTableHeader',
		'columns' => array(
			array('text' => '<div style="float:left;width:150px;">' . sysLanguage::get('TABLE_HEADING_PICKUP_DATE') . '</div>' .
				'<div style="float:left;width:150px;">' . sysLanguage::get('TABLE_HEADING_PICKUP_TYPE') . '</div>' .
				'<div style="float:left;width:40px;">' . htmlBase::newElement('icon')->setType('insert')
				->addClass('insertIconPickup')->draw() .
				'</div><br style="clear:both"/>'
			)
		)
	));

$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconPickup')->draw();
$QpickupDates = Doctrine_Query::create()
	->from('PickupRequests')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$pickupList = htmlBase::newElement('list')
	->addClass('pickupList');

$PickupRequestsTypes = Doctrine_Query::create()
	->from('PickupRequestsTypes')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$htype = htmlBase::newElement('selectbox')->attr('id', 'types_select');

foreach($PickupRequestsTypes as $iType){
	$htype->addOption($iType['pickup_requests_types_id'], $iType['type_name']);
}

foreach($QpickupDates as $iPickup){
	$pickupid = $iPickup['pickup_requests_id'];

	$pickupDate = htmlBase::newElement('input')
	->addClass('ui-widget-content date_pickup')
	->setName('pickup[' . $pickupid . '][start_date]')
	->attr('size', '15')
	->val(strftime('%Y-%m-%d', strtotime($iPickup['start_date'])));

	$type = htmlBase::newElement('selectbox')
		->addClass('ui-widget-content')
		->setName('pickup[' . $pickupid . '][type_name]')
		->selectOptionByValue($iPickup['pickup_requests_types_id']);

	foreach($PickupRequestsTypes as $iType){
		$type->addOption($iType['pickup_requests_types_id'], $iType['type_name']);
	}


	$divLi1 = '<div style="float:left;width:150px;">' . $pickupDate->draw() . '</div>';
	$divLi2 = '<div style="float:left;width:150px;">' . $type->draw() . '</div>';
	$divLi5 = '<div style="float:left;width:40px;">' . $deleteIcon . '</div>';

	$liObj = new htmlElement('li');
	$liObj->css(array(
			'font-size' => '.8em',
			'list-style' => 'none',
			'line-height' => '1.1em',
			'border-bottom' => '1px solid #cccccc',
			'cursor' => 'crosshair'
		))
		->html($divLi1 . $divLi2. $divLi5 . '<br style="clear:both;"/>');
	$pickupList->addItemObj($liObj);
}
$TableHidden->addBodyRow(array(
		'columns' => array(
			array('align' => 'center', 'text' => $pickupList->draw(), 'addCls' => 'pickupRequest')
		)
	));
?>

<form name="new_event" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=savePickup');?>" method="post" enctype="multipart/form-data">
	<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PICKUP_REQUESTS');
		?></div>
	<br />

	<div id="tab_container">
<?php
   echo $TableHidden->draw().$htype->draw();
	?>
	</div>
	<br />
	<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

		echo $saveButton->draw() . $cancelButton->draw();
		?></div>
</form>
