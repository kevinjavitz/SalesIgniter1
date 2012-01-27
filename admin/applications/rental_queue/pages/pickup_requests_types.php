<?php

$Qcheck = Doctrine_Query::create()
	->select('MAX(pickup_requests_types_id) as nextId')
	->from('PickupRequestsTypes')
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
			array('text' => '<div style="float:left;width:150px;">' . sysLanguage::get('TABLE_HEADING_PICKUP_TYPE_NAME') . '</div>' .

				'<div style="float:left;width:40px;">' . htmlBase::newElement('icon')->setType('insert')
				->addClass('insertIconPickup')->draw() .
				'</div><br style="clear:both"/>'
			)
		)
	));

$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconPickup')->draw();
$QpickupDates = Doctrine_Query::create()
	->from('PickupRequestsTypes')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$pickupList = htmlBase::newElement('list')
	->addClass('pickupList');

foreach($QpickupDates as $iPickup){
	$pickupid = $iPickup['pickup_requests_types_id'];
	$pickupTypeName = htmlBase::newElement('input')
		->addClass('ui-widget-content pickup_type')
		->setName('pickup[' . $pickupid . '][type_name]')
		->attr('size', '15')
		->val($iPickup['type_name']);



	$divLi1 = '<div style="float:left;width:150px;">' . $pickupTypeName->draw() . '</div>';
	$divLi5 = '<div style="float:left;width:40px;">' . $deleteIcon . '</div>';

	$liObj = new htmlElement('li');
	$liObj->css(array(
			'font-size' => '.8em',
			'list-style' => 'none',
			'line-height' => '1.1em',
			'border-bottom' => '1px solid #cccccc',
			'cursor' => 'crosshair'
		))
		->html($divLi1 . $divLi5 . '<br style="clear:both;"/>');
	$pickupList->addItemObj($liObj);
}
$TableHidden->addBodyRow(array(
		'columns' => array(
			array('align' => 'center', 'text' => $pickupList->draw(), 'addCls' => 'pickupRequestTypes')
		)
	));
?>

	<form name="new_event" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=savePickupType');?>" method="post" enctype="multipart/form-data">
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PICKUP_TYPES');
	?></div>
<br />

<div id="tab_container">
<?php
   echo $TableHidden->draw();
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
