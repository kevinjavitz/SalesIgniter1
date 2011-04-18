<?php
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');

	$tax_class_array = array(array('id' => '0', 'text' => sysLanguage::get('TEXT_NONE')));
	$QtaxClass = Doctrine_Query::create()
	->select('tax_class_id, tax_class_title')
	->from('TaxClass')
	->orderBy('tax_class_title')
	->execute()->toArray();
	foreach($QtaxClass as $taxClass){
		$tax_class_array[] = array(
			'id'   => $taxClass['tax_class_id'],
			'text' => $taxClass['tax_class_title']
		);
	}
?>