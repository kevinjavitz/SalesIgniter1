<?php
	$Tables = $DoctrineImport->listTables();
	foreach($Tables as $tableName){
		$DoctrineExport->alterTable($tableName, array(
			'name' => $tableName,
			'engine' => 'MyISAM'
		));
	}
	