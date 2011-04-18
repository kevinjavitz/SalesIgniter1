<?php
	$selectedTemplate = (isset($_GET['template']) ? $_GET['template'] : 'fallback');
	
	$trashBin = htmlBase::newElement('div')
	->addClass('trashBin')
	->html(sysLanguage::get('TEXT_TRASH_BIN') . '<div class="ui-icon ui-icon-trash" style="float:left;"></div>');
	
	$leftSortableList = htmlBase::newElement('sortable_list');
	$rightSortableList = htmlBase::newElement('sortable_list');
?>
<style>
.notInstalled .ui-widget-header {
	background: #fea4a4;
}
.searchOptions {
	list-style:none;
	margin:0;
	padding:0;
}
.searchOptions li {
	margin: .3em;
}
</style>
<div class="pageHeading"><?php
	$templates = new fileSystemBrowser(sysConfig::getDirFsCatalog()  . 'templates/');
	$directories = $templates->getDirectories(array('email', 'help', 'help-text'));
	$templatesArray = array();
	foreach($directories as $dirInfo){
		$templatesArray[] = ucfirst($dirInfo['basename']);
	}

	sort($templatesArray);

	$switcher = htmlBase::newElement('selectbox')
	->setName('template')
	->setId('templateSwitcher')
	->selectOptionByValue(sysConfig::get('DIR_WS_TEMPLATES_DEFAULT'));
	foreach($templatesArray as $dir){
		if ($dir != 'Fallback'){
			$lowered = strtolower($dir);
			$switcher->addOption($lowered, $dir, (isset($_GET['template']) && $_GET['template'] == $lowered));
		}
	}

	echo '<div class="smallText" style="float:right">' . sysLanguage::get('HEADING_TITLE_TEMPLATE') . $switcher->draw() . '</div>';
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all leftColumn" id="leftColumn" style="padding: 0.5em; width: 170px; height: 400px; position: absolute; left: 0.5em;"><?php
	echo $trashBin->draw() . '<hr />' . $leftSortableList->draw()
?></div>
<div class="ui-widget ui-widget-content ui-corner-all rightColumn" id="rightColumn" style="padding: 0.5em; width: 170px; height: 400px; position: absolute; right: 0.5em;"><?php
	echo $trashBin->draw() . '<hr />' . $rightSortableList->draw()
?></div>
<div class="editWindow centerColumn" style="display:none; position: relative; margin-left: 195px; margin-right: 195px;"></div>
<div class="boxListing centerColumn" style="position: relative; margin-left: 195px; margin-right: 195px;">
<div class="ui-widget ui-widget-header ui-corner-top" style="padding: 0.3em;"><?php
	echo sprintf(sysLanguage::get('TEXT_INFO_DEFAULT_LAYOUT_FILE'), sysConfig::getDirFsCatalog()  . 'templates/fallback/boxes/box.tpl');
?></div>
<div class="ui-widget ui-widget-content ui-corner-bottom dropToInstall" style="padding: 0.3em;border-top: none;"><?php
	$Qinfoboxes = Doctrine_Query::create()
	->from('TemplatesInfoboxes')
	->orderBy('box_code')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qinfoboxes){
		foreach($Qinfoboxes as $box){
			$className = 'InfoBox' . ucfirst($box['box_code']);
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . $box['box_path'] . 'infobox.php');
			}
			$infoBox = new $className;
			echo '<div class="ui-widget ui-widget-content ui-corner-all draggableField installed" id="' . $infoBox->getBoxCode() . '" style="margin:.2em;float:left;width:175px;padding:.3em;">' . 
				'<div class="ui-widget-header ui-corner-all" style="padding:.2em;padding-left:.5em;">' . 
					$infoBox->getBoxCode() . 
				'</div>' . 
			'</div>';
		}
	}
	echo '<div class="ui-helper-clearfix"></div>';
?></div>
<br />
<div class="ui-widget ui-widget-header ui-corner-top" style="padding: 0.3em;"><?php
	echo 'Boxes Not Installed ( Drag to box above to install )';
?></div>
<div class="ui-widget ui-widget-content ui-corner-bottom dropToUninstall" style="padding: 0.3em;border-top: none;"><?php
	$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/modules/infoboxes/');
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile() === true) continue;
		
		$className = 'InfoBox' . ucfirst($fileObj->getBaseName());
		if (!class_exists($className)){
			require($fileObj->getPathName() . '/infobox.php');
		}
		$classObj = new $className;
		if ($classObj->isInstalled() === false){
			echo '<div class="ui-widget ui-widget-content ui-corner-all draggableField notInstalled" id="' . $classObj->getBoxCode() . '" style="margin:.2em;float:left;width:175px;padding:.3em;">' . 
				'<div class="ui-widget-header ui-corner-all" style="padding:.2em;padding-left:.5em;">' . 
					$classObj->getBoxCode() . 
				'</div>' . 
			'</div>';
		}
	}
	
	$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile() === true) continue;
		
		if (is_dir($fileObj->getPathName() . '/catalog/infoboxes/')){
			$dir2 = new DirectoryIterator($fileObj->getPathName() . '/catalog/infoboxes/');
			foreach($dir2 as $boxDirObj){
				if ($boxDirObj->isDot() || $boxDirObj->isFile() === true) continue;
				
				$className = 'InfoBox' . ucfirst($boxDirObj->getBaseName());
				if (!class_exists($className)){
					require($boxDirObj->getPathName() . '/infobox.php');
				}
				$classObj = new $className;
				if ($classObj->isInstalled() === false){
					echo '<div class="ui-widget ui-widget-content ui-corner-all draggableField notInstalled" id="' . $classObj->getBoxCode() . '" extName="' . $fileObj->getBaseName() . '" style="margin:.2em;float:left;width:175px;padding:.3em;">' . 
						'<div class="ui-widget-header ui-corner-all" style="padding:.2em;padding-left:.5em;">' .
							$classObj->getBoxCode() . 
						'</div>' . 
					'</div>';
				}
			}
		}
	}
	echo '<div class="ui-helper-clearfix"></div>';
?></div>
</div>