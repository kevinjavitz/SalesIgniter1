<?php
require(dirname(__FILE__) . '/Abstract.php');

class OrderTotalModules extends SystemModulesLoader {
	public static $dir = 'orderTotalModules';
	public static $classPrefix = 'OrderTotal';
	
	public static function process() {
		$orderTotalArray = array();
		$enabledModules = array();
		$enabledModulesName = array();
		$enabledModulesId = array();
		if (self::hasModules() === true) {
			foreach(self::getModules() as $moduleName => $moduleClass){
				if ($moduleClass->isEnabled() === true){
					$enabledModulesId[] = (int)$moduleClass->getDisplayOrder();
					$enabledModules[] = $moduleClass;
					$enabledModulesName[] = $moduleName;
				}
			}
			array_multisort($enabledModulesId, $enabledModules, $enabledModulesName);
			$pos = 0;
			foreach ($enabledModules as $moduleClass){
				$moduleClass->process();
				$moduleOutput = $moduleClass->getOutput();
				for ($i = 0, $n = sizeof($moduleOutput); $i < $n; $i++) {
					if (tep_not_null($moduleOutput[$i]['title']) && tep_not_null($moduleOutput[$i]['text'])) {
						$orderTotalArray[] = array(
							'module_type' => $enabledModulesName[$pos],
							'code' => $moduleClass->getCode(),
							'module' => null,
							'method' => null,
							'title' => $moduleOutput[$i]['title'],
							'text' => $moduleOutput[$i]['text'],
							'value' => $moduleOutput[$i]['value'],
							'sort_order' => $moduleClass->getDisplayOrder()
						);
					}
				}
				$pos++;
			}
		}
		return $orderTotalArray;
	}

	public static function output($type = 'html') {
		if ($type == 'json'){
			$outputString = array();
			if (self::hasModules() === true){
				foreach(self::getModules() as $moduleName => $moduleClass){
					if ($moduleClass->isEnabled() === true){
						$moduleOutput = $moduleClass->getOutput();
						for($i=0, $n=sizeof($moduleOutput); $i<$n; $i++){
							$outputString[] = array(
								$moduleOutput[$i]['title'] . (isset($moduleOutput[$i]['help']) ? ' (<a href=\"' . $moduleOutput[$i]['help'] . '\" onclick=\"popupWindow(\'' . $moduleOutput[$i]['help'] . '\',\'300\',\'300\');return false;\">?</a>)' : ''),
								$moduleOutput[$i]['text']
							);
						}
					}
				}
			}
		}else{
			$outputString = '';

			$enabledModules = array();
			$enabledModulesId = array();
			if (self::hasModules() === true) {
				foreach(self::getModules() as $moduleName => $moduleClass){
					if ($moduleClass->isEnabled() === true){
						$enabledModulesId[] = $moduleClass->getDisplayOrder();
						$enabledModules[] = $moduleClass;
					}
				}
				array_multisort($enabledModulesId, $enabledModules);
				$pos = 0;
				foreach ($enabledModules as $moduleClass){
					$moduleOutput = $moduleClass->getOutput();
					for ($i = 0, $n = sizeof($moduleOutput); $i < $n; $i++) {
						$outputString .= '<tr>
	                           <td align="right" class="main">' . $moduleOutput[$i]['title'] . '</td>
	                           <td align="right" class="main">' . $moduleOutput[$i]['text'] . '</td>
	                          </tr>';
					}
				}
			}
		}
		return $outputString;
	}
}
?>