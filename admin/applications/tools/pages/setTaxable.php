<?php
$QtaxClasses = Doctrine_Query::create()
	->select('tax_class_id, tax_class_title, tax_class_description, last_modified, date_added')
	->from('TaxClass')
	->orderBy('tax_class_title')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$selectForm = htmlBase::newElement('form')
->attr('method','post')
->attr('action', itw_app_link('action=setTax','tools', 'setTaxable'));

$selectClasses = htmlBase::newElement('selectbox')
->setName('selectTaxClass')
->addOption('-1','Please Select')
->addOption('0','No Tax')
->selectOptionByValue('-1');

foreach($QtaxClasses as $taxClass){
	$selectClasses->addOption($taxClass['tax_class_id'], $taxClass['tax_class_title']);
}
$pushButton= htmlBase::newElement('button')
->setText('Convert All Products to the selected class')
->setType('submit')
->usePreset('submit');

$selectForm->append($selectClasses)->append($pushButton);
 echo $selectForm->draw();
?>