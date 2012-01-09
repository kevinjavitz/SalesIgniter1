<?php
function checkModel($modelName, $charset, $collation){
	global $manager;
	$dbConn = $manager->getCurrentConnection();
	$tableObj = Doctrine_Core::getTable($modelName);
	$tableName = $tableObj->getTableName();
	$isOk = true;
	$isCharset = false;
	$info = array();
	$resLink = '';
	if ($dbConn->import->tableExists($tableName)){
		$tableObjRecord = $tableObj->getRecordInstance();
		
		$DBtableColumns = $dbConn->import->listTableColumns($tableName);
		
		$DBtableStatus = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('SHOW TABLE STATUS LIKE "' . $tableName . '"');
		
		$tableColumns = array();
		foreach($DBtableColumns as $k => $v){
			$tableColumns[strtolower($k)] = $v;
		}
		
		if ($DBtableStatus[0]['Collation'] != $collation){
			$isCharset = false;
			$isOk = false;
			$info[] = array(
					'text'    => 'Table collation doesnt match system collation ( <b>System:</b> ' . $collation . ' - <b>Table:</b> ' . $DBtableStatus[0]['Collation'] . ' )',
					'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=changeTableCollation&table=' . $tableName . '&to=' . $charset . '&collate=' . $collation, 'database_manager', 'default'),
					'resInfo' => 'Change Database Table Collation'
				);
		}else{
			$isCharset = true;
		}
		
		$modelColumns = $tableObj->getColumns();
		$tableIsCharset = $isCharset;
		foreach($modelColumns as $colName => $colSettings){
			if ($colName == 'id') continue;
			if (array_key_exists($colName, $tableColumns) === false){
				$isOk = false;
				$info[] = array(
						'text'    => '<b>' . $colName . '</b> is missing in database table',
						'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=addColumn&model=' . $modelName . '&column=' . $colName, 'database_manager', 'default'),
						'resInfo' => 'Add Column To Database Table'
					);
			}else{
				if ($tableIsCharset === true && !empty($tableColumns[$colName]['collation']) && $tableColumns[$colName]['collation'] != $collation){
					$isCharset = false;
					$isOk = false;
					$info[] = array(
							'text'    => '<b>' . $colName . '</b> collation doesnt match system collation ( <b>System:</b> ' . $collation . ' - <b>Column:</b> ' . $tableColumns[$colName]['collation'] . ' )',
							'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=changeColumnCollation&table=' . $tableName . '&column=' . $colName . '&to=' . $charset . '&collate=' . $collation, 'database_manager', 'default'),
							'resInfo' => 'Change Collation For Database Column'
						);
				}
				
				/*if ($colSettings['type'] != $tableColumns[$colName]['type']){
					$isOk = false;
					$info[] = array(
							'text'    => '<b>' . $colName . '</b> type has changed in database ( ' . $tableColumns[$colName]['type'] . ' ) or model ( ' . $colSettings['type'] . ' )',
							'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=changeColumnType&table=' . $tableName . '&column=' . $colName . '&to=' . $colSettings['type'], 'database_manager', 'default'),
							'resInfo' => 'Change Type Setting For Database Column'
						);
				}*/
				
				//if ($colName == 'layout_content'){
				//	echo '<pre>';print_r($colSettings);print_r($tableColumns[$colName]);
				//}
				if (
					$colSettings['type'] != $tableColumns[$colName]['type'] || 
					$colSettings['length'] != $tableColumns[$colName]['length']
				){
					if (
						($colSettings['type'] != 'timestamp') && 
						($colSettings['type'] != 'datetime') && 
						($colSettings['type'] != 'date') && 
						(($colSettings['type'] == 'clob' && empty($colSettings['length']) && is_null($tableColumns[$colName]['length'])) === false) &&
						(($colSettings['type'] == 'string' && $colSettings['length'] == '999' && is_null($tableColumns[$colName]['length'])) === false) &&
						(($colSettings['type'] == 'text' && empty($colSettings['length']) && is_null($tableColumns[$colName]['length'])) === false)
					){
						$isOk = false;
						$info[] = array(
								'text'    => '<b>' . $colName . '\'s</b> settings are out of sync',
								'resLink' => itw_app_link('rType=ajax&showErrors=true&action=fixProblem&resolution=syncColumnSettings&model=' . $modelName . '&column=' . $colName, 'database_manager', 'default'),
								'resInfo' => 'Syncronize Settings For Database Column'
							);
						//echo '<pre>';print_r($tableColumns);echo '</pre>';
					}
				}
			}
		}
		
		foreach($tableColumns as $colName => $colSettings){
			if (array_key_exists($colName, $modelColumns) === false){
				$isOk = false;
				$info[] = array(
						'text'    => 'Abandoned Table Column: <b>' . $colName . '</b>',
						'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=removeColumn&table=' . $tableName . '&column=' . $colName, 'database_manager', 'default'),
						'resInfo' => 'Remove Column From Database Table'
					);
			}
		}
	}else{
		$isOk = false;
		$info[] = array(
				'text'    => 'Table Missing In Database',
				'resLink' => itw_app_link('rType=ajax&action=fixProblem&resolution=addTable&model=' . $modelName, 'database_manager', 'default'),
				'resInfo' => 'Add Table To Database'
			);
	}
	
	$infoText = '';
	if (sizeof($info) > 0){
		$infoTable = htmlBase::newElement('table')
			->setCellPadding(0)
			->setCellSpacing(0)
			->css(array(
				'width' => '100%'
			));
		foreach($info as $infoData){
			$infoTable->addBodyRow(array(
					'columns' => array(
						array('text' => $infoData['text']),
						array('align' => 'right', 'text' => htmlBase::newElement('button')->addClass('resButton')->setText('Fix Problem')->attr('tooltip', $infoData['resInfo'])->setHref($infoData['resLink'])->draw())
					)
				));
		}
		$infoText = $infoTable->draw();
	}
	return array(
		'isOk' => $isOk,
		'isCharset' => $isCharset,
		'resolution' => $resLink,
		'table_name' => $tableName,
		'info' => $infoText
	);
}

Doctrine_Core::loadAllModels();
$appContent = $App->getAppContentFile();
