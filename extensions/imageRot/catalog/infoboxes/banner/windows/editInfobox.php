<?php
$selectedGroup = isset($WidgetSettings->selected_banner_group) ? $WidgetSettings->selected_banner_group : '';

$Qgroups = Doctrine_Query::create()
	->select('g.*')
	->from('BannerManagerGroups g')
	->orderBy('g.banner_group_name')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$bannerName = htmlBase::newElement('selectbox')
	->setName('selected_banner_group')
	->selectOptionByValue($selectedGroup);

foreach($Qgroups as $iBanner){
	$bannerName->addOption($iBanner['banner_group_id'], $iBanner['banner_group_name']);
}

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Banner Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Banner Group:'),
		array('text' => $bannerName->draw())
	)
));
