<?php
	$CategoriesDescription = Doctrine_Core::getTable('CategoriesDescription');
	
	$changeColumns = array(
		'categories_id' => array(
			'length' => 4,
			'definition' => array(
				'type'          => 'integer',
				'length'        => 4,
				'unsigned'      => 0,
				'primary'       => false,
				'autoincrement' => false
			)
		),
		'language_id' => array(
			'length' => 4,
			'definition' => array(
				'type'          => 'integer',
				'length'        => 4,
				'unsigned'      => 0,
				'primary'       => false,
				'autoincrement' => false
			)
		)
	);
	
	if (!empty($changeColumns)){
		$DoctrineExport->alterTable($CategoriesDescription->getTableName(), array(
			'change' => $changeColumns
		));
	}
