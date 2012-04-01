<?php
$CategoryId = (isset($WidgetSettings->category_id) ? $WidgetSettings->category_id : '');

$CategorySelect = htmlBase::newElement('selectbox')
	->setName('category_id')
	->selectOptionByValue($CategoryId);

$Qcategories = Doctrine_Query::create()
	->from('Categories c')
	->leftJoin('c.CategoriesDescription cd')
	->where('cd.language_id = ?', Session::get('languages_id'))
	->andWhere('c.parent_id = ?', 0)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
$CategorySelect->addOption('', sysLanguage::get('TEXT_PLEASE_SELECT'));
foreach($Qcategories as $cInfo){
	$CategorySelect->addOption(
		$cInfo['categories_id'],
		$cInfo['CategoriesDescription'][0]['categories_name']
	);
}

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<br>' . sysLanguage::get('TEXT_CATEGORYPRODUCTLISTING_INFO') . '<br>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_CATEGORYPRODUCTLISTING_SELECT_CATEGORY')),
		array('text' => $CategorySelect->draw())
	)
));
?>