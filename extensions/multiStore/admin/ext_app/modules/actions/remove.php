<?php
Doctrine_Query::create()
	->delete('StoresModulesConfiguration')
	->where('module_type = ?', $_GET['moduleType'])
	->andWhere('module_code = ?', $_GET['module'])
	->execute();
