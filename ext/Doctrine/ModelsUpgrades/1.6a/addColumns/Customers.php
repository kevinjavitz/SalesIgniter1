<?php
	$Customers = Doctrine_Core::getTable('Customers')->getTableName();

	$addColumns = array();
	if ($DoctrineImport->tableColumnExists($Customers, 'language_id') === false){
		$addColumns['language_id'] = array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		);
	}
	
	$changeColumns = array();
	if ($DoctrineImport->tableColumnExists($Customers, 'customers_dob') === true){
		$changeColumns['customers_dob'] = array(
			'length' => null,
			'definition' => array(
				'type'    => 'date',
				'default' => '0000-00-00'
			)
		);
	}

	if (!empty($addColumns) || !empty($changeColumns)){
		$commandArr = array();
		if (!empty($addColumns)){
			$commandArr['add'] = $addColumns;
		}
		
		if (!empty($changeColumns)){
			$commandArr['change'] = $changeColumns;
		}
		
		$DoctrineExport->alterTable($Customers, $commandArr);
	}
