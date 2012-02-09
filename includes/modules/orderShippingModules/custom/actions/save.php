<?php
	$Module1 = OrderShippingModules::getModule($_GET['module'], true);
	$ModuleMethods = $Module1->getMethods();
	
	$Methods = Doctrine_Core::getTable('ModulesShippingCustomMethods');
	$saveArray = array();
	if (isset($_POST['method'])){
		foreach($_POST['method'] as $methodId => $mInfo){
			$Method = $Methods->find($methodId);
			if (!$Method){
				$Method = $Methods->create();
			}
			
			$Method->method_text = $mInfo['text'];
			$Method->method_status = $mInfo['status'];
			$Method->method_cost = $mInfo['cost'];
			$Method->method_default = (isset($_POST['method_default']) && $_POST['method_default'] == $methodId ? '1' : '0');
			$Method->sort_order = $mInfo['sort_order'];
			
			$Method->save();
			
			$saveArray[] = $Method->method_id;
		}
	}
	
	if (!empty($saveArray)){
		Doctrine_Query::create()
		->delete('ModulesShippingCustomMethods')
		->whereNotIn('method_id', $saveArray)
		->execute();
	}
?>