<?php
$dbTable = htmlBase::newElement('newGrid');
$dbTable->addButtons(array(
	htmlBase::newElement('button')->addClass('fixEverythingButton')->setText('Fix Everything')
));

$dbTable->addHeaderRow(array(
	'columns' => array(
		array('text' => 'MySQL Variable'),
		array('text' => 'MySQL Variable Info'),
		array('text' => 'MySQL Variable Value'),
		array('text' => 'Change Charset/Collation')
	)
));

$Charsets = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('SHOW CHARACTER SET');

$CharsetSelect = htmlBase::newElement('selectbox')
	->attr('data-action_url', itw_app_link('action=fixProblem&resolution=changeVariable', 'database_manager', 'default'))
	->setName('charset')
	->setLabel('Charset: ')
	->setLabelPosition('before');
foreach($Charsets as $cInfo){
	$CharsetSelect->addOption($cInfo['Charset'], $cInfo['Description'], false, array(
		'data-default_collation' => $cInfo['Default collation']
	));
}

$Collations = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('SHOW COLLATION');

$CollationSelect = htmlBase::newElement('selectbox')
	->attr('data-action_url', itw_app_link('action=fixProblem&resolution=changeVariable', 'database_manager', 'default'))
	->setName('collation')
	->setLabel('Collation: ')
	->setLabelPosition('before');
foreach($Collations as $cInfo){
	$CollationSelect->addOption($cInfo['Collation'], $cInfo['Collation'], false, array(
		'data-charset' => $cInfo['Charset']
	));
}

$dbTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'system_character_set'),
		array('text' => 'SalesIgniter Character Set'),
		array('text' => sysConfig::get('SYSTEM_CHARACTER_SET')),
		array('align' => 'center', 'text' => $CharsetSelect
			->attr('data-variable', 'system_character_set')
			->selectOptionByValue(sysConfig::get('SYSTEM_CHARACTER_SET'))
			->draw())
	)
));

$dbTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'system_character_set_collation'),
		array('text' => 'SalesIgniter Character Set Collation'),
		array('text' => sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION')),
		array('align' => 'center', 'text' => $CollationSelect
			->attr('data-variable', 'system_character_set_collation')
			->selectOptionByValue(sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION'))
			->draw())
	)
));

$Variables = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('SHOW VARIABLES');
foreach($Variables as $dbVariable){
	//echo '<pre>';print_r($dbVariable);
	$text = null;
	$selectMenu = null;

	$CharsetSelect->selectOptionByValue($dbVariable['Value']);
	$CollationSelect->selectOptionByValue($dbVariable['Value']);
	$CharsetSelect->attr('data-variable', $dbVariable['Variable_name']);
	$CollationSelect->attr('data-variable', $dbVariable['Variable_name']);

	${$dbVariable['Variable_name']} = $dbVariable['Value'];

	switch ($dbVariable['Variable_name']){
		case 'character_set_client':
			$text = 'MySQL Client Character Set';
			break;
		case 'character_set_connection':
			$text = 'MySQL Connection Character Set';
			if ($dbVariable['Value'] != sysConfig::get('SYSTEM_CHARACTER_SET')){
				$selectMenu = $CharsetSelect->draw() . ' - Recommended: ' . sysConfig::get('SYSTEM_CHARACTER_SET');
			}
			break;
		case 'character_set_database':
			$text = 'MySQL Database Character Set';
			if ($dbVariable['Value'] != sysConfig::get('SYSTEM_CHARACTER_SET')){
				$selectMenu = $CharsetSelect->draw() . ' - Recommended: ' . sysConfig::get('SYSTEM_CHARACTER_SET');
			}
			break;
		case 'character_set_results':
			$text = 'MySQL Results Character Set';
			break;
		case 'character_set_server':
			$text = 'MySQL Server Character Set';
			break;
		case 'character_set_system':
			$text = 'System Character Set';
			break;
		case 'collation_connection':
			$text = 'MySQL Connection Collation';
			if ($dbVariable['Value'] != sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION')){
				$selectMenu = $CollationSelect->draw() . ' - Recommended: ' . sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION');
			}
			break;
		case 'collation_database':
			$text = 'MySQL Database Collation';
			if ($dbVariable['Value'] != sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION')){
				$selectMenu = $CollationSelect->draw() . ' - Recommended: ' . sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION');
			}
			break;
		case 'collation_server':
			$text = 'MySQL Server Collation';
			break;
	}

	if (is_null($text) === false){
		$dbTable->addBodyRow(array(
			'columns' => array(
				array('text' => $dbVariable['Variable_name']),
				array('text' => $text),
				array('text' => $dbVariable['Value']),
				array('align' => 'center', 'text' => $selectMenu)
			)
		));
	}
}

$tableGrid = htmlBase::newElement('newGrid');

/*$tableGrid->addButtons(array(
	htmlBase::newElement('button')->addClass('fixEverythingButton')->setText('Fix Everything')
));*/

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => 'Doctrine Model'),
		array('text' => 'Table Name'),
		array('text' => 'Error Info'),
		array('text' => 'Charset Compatible'),
		array('text' => 'Status'),
		array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
	)
));

$Models = Doctrine_Core::getLoadedModels();
sort($Models);

$DatabaseTables = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('SHOW TABLES FROM ' . sysConfig::get('DB_DATABASE'));

$tablesInDb = array();
foreach($DatabaseTables as $tInfo){
	foreach($tInfo as $tableName){
		$tablesInDb[$tableName] = $tableName;
	}
}

foreach($Models as $mInfo){
	$ModelCheck = checkModel($mInfo, sysConfig::get('SYSTEM_CHARACTER_SET'), sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION'));
	if (isset($tablesInDb[$ModelCheck['table_name']])){
		unset($tablesInDb[$ModelCheck['table_name']]);
	}
	$tableGrid->addBodyRow(array(
		'columns' => array(
			array('text' => $mInfo),
			array('text' => $ModelCheck['table_name']),
			array('text' => $ModelCheck['info']),
			array('align' => 'center', 'text' => '<span class="charsetStatusIcon ui-icon ui-icon-circle-' . ($ModelCheck['isCharset'] === false ? 'close' : 'check') . '"></span>'),
			array('align' => 'center', 'text' => '<span class="statusIcon ui-icon ui-icon-circle-' . ($ModelCheck['isOk'] === false ? 'close' : 'check') . '"></span>'),
			array('align' => 'center', 'text' => ($ModelCheck['isOk'] === false ? htmlBase::newElement('button')->addClass('allResButton')->setText('Fix All Problems')->draw() : ''))
		)
	));
}

foreach($tablesInDb as $tableName){
	$tableGrid->addBodyRow(array(
		'columns' => array(
			array('text' => 'No Model'),
			array('text' => $tableName),
			array('text' => 'Table exists but has no doctrine model to access it<br>It\'s possible the model is loaded only on demand<br>Please make sure before deleting the table'),
			array('align' => 'center', 'text' => 'N/A'),
			array('align' => 'center', 'text' => 'N/A'),
			array('align' => 'center', 'text' => htmlBase::newElement('button')->addClass('delTableButton')->setText('Delete Table')->draw())
		)
	));
}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div>
	<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
		<div style="margin:5px;"><?php
			echo $dbTable->draw();
			echo '<br>';
			echo $tableGrid->draw();
		?></div>
	</div>
</div>
