<?php
    require(sysConfig::getDirFsAdmin() . 'includes/classes/table_block.php');
    require(sysConfig::getDirFsAdmin() . 'includes/classes/box.php');
    require(sysConfig::getDirFsAdmin() . 'includes/classes/split_page_results.php');

    require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
    $currencies = new currencies();
    $purchaseTypeNames = $typeNames;
    $purchaseTypeNames['global'] = 'All Purchase Types';
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

    $appContent = $App->getAppContentFile();

    $App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
    $App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
    $App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
    $App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
    $App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
?>