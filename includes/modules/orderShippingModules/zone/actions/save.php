<?php
	$Module1 = OrderShippingModules::getModule($_GET['module'], true);
	$ModuleMethods = $Module1->getMethods();
	
	$Methods = Doctrine_Core::getTable('ModulesShippingZoneMethods');
	$saveArray = array();
	if (isset($_POST['method'])){
		foreach($_POST['method'] as $methodId => $mInfo){
			$Method = $Methods->find($methodId);
			if (!$Method){
				$Method = $Methods->create();
			}
			
			$Method->method_countries = $mInfo['countries'];
			$Method->method_cost = $mInfo['cost'];
			$Method->method_handling_cost = $mInfo['handling'];
			
			$Method->save();
			
			$saveArray[] = $Method->method_id;
		}
	}
	
	if (!empty($saveArray)){
		Doctrine_Query::create()
		->delete('ModulesShippingZoneMethods')
		->whereNotIn('method_id', $saveArray)
		->execute();
	}
?>