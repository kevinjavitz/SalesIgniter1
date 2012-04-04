<?php
	class Zipper extends ZipArchive {
		public $basePath = '';

		public function setBasePath($path){
			$this->basePath = $path;
		}

		public function addDirs(){
			$Dir = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($this->basePath),
				RecursiveIteratorIterator::SELF_FIRST
			);
			while($Dir->valid()){
				$Item = $Dir->current();
				if ($Item->getBasename() == '.' || $Item->getBasename() == '..'){
					$Dir->next();
					continue;
				}

				if ($Item->isFile()){
					if ($Item->getBasename() == 'export.zip') continue;
					
					$this->addFile(
						$Item->getPathname(),
						str_replace($this->basePath, '', $Item->getPathname())
					);
				}else{
					$this->addEmptyDir(str_replace($this->basePath, '', $Item->getPathname()));
				}
				$Dir->next();
			}
		}
	}
	
	function cleanString($string){
		$string = str_replace("'", "\'", $string);
		$string = str_replace("\n", '\' . "\n" . \'', $string);
		$string = str_replace('<?php', "<' . '?php", $string);
		$string = str_replace('?>', "?' . '>", $string);
		
		return $string;
	}

function sprintfStyles($pattern, $Styles){
	$return = '';
	foreach($Styles as $Style){
		$return .= sprintf($pattern, $Style->definition_key, cleanString($Style->definition_value)) . "\n";
	}
	return $return;
}

function sprintfConfig($pattern, $Configuration){
	$return = '';
	foreach($Configuration as $Config){
		$return .= sprintf($pattern, $Config->configuration_key, cleanString($Config->configuration_value)) . "\n";
	}
	return $return;
}

function exportElement($exportVar, $addItemVar, $exportTable, $Element, $elementId){
	global $TemplateDir, $WidgetUpdates, $linksArr, $paths;
	$return = "\n" . $exportVar . '[' . $elementId . '] = ' . $addItemVar . '->' . $exportTable . '->getTable()->create();' . "\n";

	$return .= "\n" . $exportVar . '[' . $elementId . ']->save();';
	$return .= $addItemVar . '->' . $exportTable . '->add(' . $exportVar . '[' . $elementId . ']);' . "\n";
	if ($exportTable == 'Widgets'){
		$return .= $exportVar . '[' . $elementId . ']->identifier = \'' . $Element->identifier . '\';' . "\n";
	}
	if($exportTable == 'Containers'){

			$LinkedContainers = Doctrine_Core::getTable('TemplateManagerContainerLinks')
			->find($Element->link_id);
			if($LinkedContainers){
				$return .= '$ContainerLink = new TemplateManagerContainerLinks();';

				$return .= '$ContainerLink->link_name = "'.$LinkedContainers->link_name.'";';

				$return .= '$ContainerLink->save();';
				$return .= '$linksArr[$ContainerLink->link_id] = array("var1"=>'.$exportVar . ',"var2"=>' .$LinkedContainers->container_id.');';

				$return .= $exportVar . '[' . $elementId . ']->link_id = $ContainerLink->link_id;' . "\n";
			}
	}
	$return .= $exportVar . '[' . $elementId . ']->sort_order = \'' . $Element->sort_order . '\';' . "\n";
	$return .= sprintfStyles($exportVar . '[' . $elementId . ']->Styles[\'%s\']->definition_value = \'%s\';', $Element->Styles);
	$return .= sprintfConfig($exportVar . '[' . $elementId . ']->Configuration[\'%s\']->configuration_value = \'%s\';', $Element->Configuration);

	if ($Element->hasRelation('Children') && $Element->Children->count() > 0){
		foreach($Element->Children as $Child){
			$return .= exportElement(
				'$Child',
				$exportVar . '[' . $elementId . ']',
				'Children',
				$Child,
				$Child->container_id
			);
		}
	}elseif ($Element->hasRelation('Columns') && $Element->Columns->count() > 0){
		foreach($Element->Columns as $Column){
			$return .= exportElement(
				'$Column',
				$exportVar . '[' . $elementId . ']',
				'Columns',
				$Column,
				$Column->column_id
			);
		}
	}elseif ($Element->hasRelation('Widgets') && $Element->Widgets->count() > 0){
		foreach($Element->Widgets as $Widget){
			/*$boxCode = '\'' . $Widget->TemplatesInfoboxes->box_code . '\'';
			$boxPath = '\'' . $Widget->TemplatesInfoboxes->box_path . '\'';
			$boxExt = '\'' . (is_null($Widget->TemplatesInfoboxes->ext_name) === false ? $Widget->TemplatesInfoboxes->ext_name : 'null') . '\'';

			$return .= "\n" . 'if (!isset($Box[' . $boxCode . '])){' . "\n";
			$return .= ' $Box[' . $boxCode . '] = $TemplatesInfoboxes->findOneByBoxCode(' . $boxCode . ');' . "\n";
			$return .= '    if (!is_object($Box[' . $boxCode . ']) || $Box[' . $boxCode . ']->count() <= 0){' . "\n" .
				'       installInfobox(' . $boxPath . ', ' . $boxCode . ', ' . $boxExt . ');' . "\n" .
				'       $Box[' . $boxCode . '] = $TemplatesInfoboxes->findOneByBoxCode(' . $boxCode . ');' . "\n" .
				'   }' . "\n";
			$return .= '}' . "\n";*/

			$return .= exportElement(
				'$Widget',
				$exportVar . '[' . $elementId . ']',
				'Widgets',
				$Widget,
				$Widget->widget_id
			);


			if (file_exists($paths[$Widget->identifier] . '/actions/exportTemplate.php')){
				$WidgetUpdates .= '$WidgetProperties = json_decode($Widget[' . $Widget->widget_id . ']->Configuration[\'widget_settings\']->configuration_value);' . "\n";
				ob_start();
				require($paths[$Widget->identifier] . '/actions/exportTemplate.php');
				$WidgetUpdates .= ob_get_contents();
				ob_end_clean();
				$WidgetUpdates .= '$Widget[' . $Widget->widget_id . ']->Configuration[\'widget_settings\']->configuration_value = json_encode($WidgetProperties);' . "\n";
				$WidgetUpdates .= '$Widget[' . $Widget->widget_id . ']->save();' . "\n";
			}
		}
	}
	return $return;
}

	ob_start();
	$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find($_GET['tID']);
	$TemplateDir = $Template->Configuration['DIRECTORY']->configuration_value;
	$WidgetUpdates = '';
	$templateExt = $appExtension->getExtension('templateManager');
	$templateExt->loadWidgets($TemplateDir);
	$paths = $templateExt->getWidgetPaths();

	echo '<?php' . "\n";
	echo '$Template = Doctrine_Core::getTable(\'TemplateManagerTemplates\')->create();' . "\n";
	echo sprintfStyles('$Template->Styles[\'%s\']->definition_value = \'%s\';', $Template->Styles);
	echo sprintfConfig('$Template->Configuration[\'%s\']->configuration_value = \'%s\';', $Template->Configuration);
	echo '$linksArr = array();';
	$layoutIds = array();
	foreach($Template->Layouts as $Layout){
		if($Layout->backupof_layout_id > 0) continue;
		$layoutKey = $Layout->layout_id;
		$layoutIds[] = $layoutKey;
		echo "\n" . '$Layout[' . $layoutKey . '] = $Template->Layouts->getTable()->create();' . "\n";
		echo '$Template->Layouts->add($Layout[' . $layoutKey . ']);' . "\n";
		echo '$Layout[' . $layoutKey . ']->layout_name = \'' . $Layout->layout_name . '\';' . "\n";
		echo sprintfStyles('$Layout[' . $layoutKey . ']->Styles[\'%s\']->definition_value = \'%s\';', $Layout->Styles);
		echo sprintfConfig('$Layout[' . $layoutKey . ']->Configuration[\'%s\']->configuration_value = \'%s\';', $Layout->Configuration);

		foreach($Layout->Containers as $Container){
			if ($Container->parent_id > 0) continue;

			echo exportElement(
				'$Container',
				'$Layout[' . $layoutKey . ']',
				'Containers',
				$Container,
				$Container->container_id
			);
		}
	}

	echo 'foreach($linksArr as $linkA => $contVal){';
	echo '$linkCont = Doctrine_Core::getTable("TemplateManagerContainerLinks")->find($linkA);';
	echo 'if($linkCont){';
    	echo '$linkCont->container_id = $Container[$contVal[\'var2\']]->container_id;';
 	echo '$linkCont->save();';
	echo '}}';
	echo '$Template->save();' . "\n";
	echo $WidgetUpdates;

	foreach($layoutIds as $layoutKey){
		$Qpages = Doctrine_Query::create()
			->from('TemplatePages')
			->where('FIND_IN_SET(' . $layoutKey . ', layout_id) > 0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qpages as $pInfo){
			echo 'addLayoutToPage(\'' . $pInfo['application'] . '\', \'' . $pInfo['page'] . '\', ' . (!empty($pInfo['extension']) ? '\'' . $pInfo['extension'] . '\'' : 'null') . ', $Layout[' . $layoutKey . ']->layout_id);' . "\n";
		}
	}

	$installFileContent = ob_get_contents();
	ob_end_clean();

	if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip')){
		unlink(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip');
	}
	
	$ZipArchive = new Zipper();
	$ZipArchive->open(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip', ZipArchive::OVERWRITE);
	$ZipArchive->setBasePath(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/');
	$ZipArchive->addDirs();
	$ZipArchive->addFromString('installData.php', $installFileContent);
	$ZipArchive->close();
	if (Session::get('login_master') == 'master'){//no other admin beside master can upload to sesStuff
		//export sesStuff
		$urlFtp = 'http://www.itwebexperts.com/sesUpgrades/installer/1/actions/getFtp.php';
		$ch = curl_init($urlFtp);
		$postVars = array('success=1');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $postVars));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);

		curl_close($ch);

			if ($result){
				$result = json_decode($result);
				if ($result->success){
					//then send the headers to foce download the zip file
					$ftpConn = ftp_connect($result->ftp_server);
					if ($ftpConn === false){
						die('Error ftp_connect');
					}
					else {
						$ftpCmd = ftp_login($ftpConn, $result->ftp_username , $result->ftp_password);
						if (!$ftpCmd){
							die('Error ftp_login');
						}
					}

					$ftpCmd = ftp_chdir($ftpConn, $result->ftp_path);
					if (!$ftpCmd){
						die('Error ftp_chdir');
					}
					ftp_mkdir($ftpConn, $TemplateDir);
					ftp_delete($ftpConn, ''.$TemplateDir.'/export.zip', sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip');
					ftp_put($ftpConn, ''.$TemplateDir.'/export.zip', sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip',FTP_BINARY);

					ftp_close($ftpConn);
				}else{

				}
			}
	}
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=export.zip");
header("Pragma: no-cache");
header("Expires: 0");
readfile(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/export.zip');
exit;


	