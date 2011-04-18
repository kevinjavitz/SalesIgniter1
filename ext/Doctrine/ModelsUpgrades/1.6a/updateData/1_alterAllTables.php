<?php
	$Results = $DoctrineConnection->fetchAll('SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = "FOREIGN KEY" AND TABLE_SCHEMA = "' . DB_DATABASE . '"');
	foreach($Results as $rInfo){
		$DoctrineConnection->execute('ALTER TABLE ' . $rInfo['TABLE_NAME'] . ' DROP FOREIGN KEY ' . $rInfo['CONSTRAINT_NAME']);
	}

	$Tables = $DoctrineImport->listTables();
	foreach($Tables as $tableName){
		$DoctrineConnection->execute('ALTER TABLE ' . $tableName . ' ENGINE = ?', array(
			'MyISAM'
		));
	}
