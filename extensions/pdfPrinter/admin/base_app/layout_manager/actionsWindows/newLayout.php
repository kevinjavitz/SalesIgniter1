<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stephen
 * Date: 3/26/11
 * Time: 5:14 PM
 * To change this template use File | Settings | File Templates.
 */

$layoutName = '';
$layoutType = 'pdf';
$selApps = array();
if (isset($_GET['lID'])){
	$QLayout = Doctrine_Query::create()
	->select('layout_id, layout_name, layout_type')
	->from('PDFTemplateManagerLayouts')
	->where('layout_id = ?', (int) $_GET['lID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$layoutId = $QLayout[0]['layout_id'];
	$layoutName = $QLayout[0]['layout_name'];
	$layoutType = $QLayout[0]['layout_type'];

	$QselApps = Doctrine_Query::create()
	->from('PDFTemplatePages')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QselApps as $sInfo){
		$layouts = explode(',', $sInfo['layout_id']);
		if (in_array($layoutId, $layouts)){
			if (!empty($sInfo['extension'])){
				$selApps['ext'][$sInfo['extension']][$sInfo['application']][$sInfo['page']] = true;
			}
			else {
				$selApps[$sInfo['application']][$sInfo['page']] = true;
			}
		}
	}
}
$SettingsTable = htmlBase::newElement('table')
->setCellPadding(3)
->setCellSpacing(0);

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Layout Name:'),
			array('text' => htmlBase::newElement('input')
				->setName('layoutName')
				->attr('id', 'layoutName')
				->val($layoutName)
				->draw())
		)
	));

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Display Type:'),
			array('text' => htmlBase::newElement('selectbox')
				->setName('layoutType')
				->addOption('pdf', 'PDF')
				->selectOptionByValue($layoutType)
				->draw())
		)
	));

$Applications = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'applications/');
$AppArray = array();
foreach($Applications as $AppDir){
	if ($AppDir->isDot() || $AppDir->isFile()){
		continue;
	}
	$appName = $AppDir->getBasename();

	$AppArray[$appName] = array();

	if (is_dir($AppDir->getPathname() . '/pages/')){
		$Pages = new DirectoryIterator($AppDir->getPathname() . '/pages/');
		foreach($Pages as $Page){
			if ($Page->isDot() || $Page->isDir()){
				continue;
			}
			$pageName = $Page->getBasename();

			$AppArray[$appName][$pageName] = (isset($selApps[$appName][$pageName]) ? $selApps[$appName][$pageName] : false);
		}
	}
	ksort($AppArray[$appName]);
}

$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
foreach($Extensions as $Extension){
	if ($Extension->isDot() || $Extension->isFile()){
		continue;
	}

	if (is_dir($Extension->getPathName() . '/catalog/base_app/')){
		$extName = $Extension->getBasename();

		$AppArray['ext'][$extName] = array();

		$ExtApplications = new DirectoryIterator($Extension->getPathname() . '/catalog/base_app/');
		foreach($ExtApplications as $ExtApplication){
			if ($ExtApplication->isDot() || $ExtApplication->isFile()){
				continue;
			}
			$appName = $ExtApplication->getBasename();

			$AppArray['ext'][$extName][$appName] = array();

			if (is_dir($ExtApplication->getPathname() . '/pages/')){
				$ExtPages = new DirectoryIterator($ExtApplication->getPathname() . '/pages/');
				foreach($ExtPages as $ExtPage){
					if ($ExtPage->isDot() || $ExtPage->isDir()){
						continue;
					}
					$pageName = $ExtPage->getBasename();

					$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
				}
			}
			ksort($AppArray['ext'][$extName][$appName]);
		}
		ksort($AppArray['ext']);
	}
}

$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
foreach($Extensions as $Extension){
	if ($Extension->isDot() || $Extension->isFile()){
		continue;
	}

	if (is_dir($Extension->getPathName() . '/catalog/ext_app/')){
		$ExtCheck = new DirectoryIterator($Extension->getPathname() . '/catalog/ext_app/');
		foreach($ExtCheck as $eInfo){
			if ($eInfo->isDot() || $eInfo->isFile()){
				continue;
			}

			if (is_dir($eInfo->getPathName() . '/pages')){
				$appName = $eInfo->getBasename();

				$Pages = new DirectoryIterator($eInfo->getPathname() . '/pages/');
				foreach($Pages as $Page){
					if ($Page->isDot() || $Page->isDir()){
						continue;
					}
					$pageName = $Page->getBasename();

					if (!isset($AppArray[$appName][$pageName])){
						$AppArray[$appName][$pageName] = (isset($selApps[$appName][$pageName]) ? $selApps[$appName][$pageName] : false);
					}
				}
			}
			elseif (isset($AppArray['ext'][$eInfo->getBasename()])) {
				$Apps = new DirectoryIterator($eInfo->getPathName());
				$extName = $eInfo->getBasename();

				foreach($Apps as $App){
					if ($App->isDot() || $App->isFile()){
						continue;
					}
					$appName = $App->getBasename();

					if (is_dir($App->getPathname() . '/pages')){
						$Pages = new DirectoryIterator($App->getPathname() . '/pages/');
						foreach($Pages as $Page){
							if ($Page->isDot() || $Page->isDir()){
								continue;
							}
							$pageName = $Page->getBasename();

							if (!isset($AppArray['ext'][$extName][$App->getBasename()])){
								$AppArray['ext'][$extName][$App->getBasename()] = array();
							}

							$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
						}
					}
				}
			}
		}
	}
}

ksort($AppArray);

$BoxesContainer = htmlBase::newElement('div');
$col = 0;
foreach($AppArray as $appName => $aInfo){
	if ($appName == 'ext'){
		continue;
	}

	if (!empty($aInfo)){
		$Box = htmlBase::newElement('div')
		->addClass('ui-widget-content ui-corner-all mainBox')
		->css(array(
			'float' => 'left',
			'margin' => '.5em',
			'min-width' => '260px',
			'min-height' => '250px',
			'padding' => '.5em'
		));

		$checkboxes = '<div class="ui-widget-header"><input type="checkbox" class="appBox checkAllPages"> ' . $appName . '</div>';
		foreach($aInfo as $pageName => $pageChecked){
			$checkboxes .= '<div style="margin: 0 0 0 1em;"><input class="pageBox" type="checkbox" name="applications[' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName . '</div>';
		}

		$Box->html($checkboxes);
		$BoxesContainer->append($Box);
	}
}

foreach($AppArray['ext'] as $ExtName => $eInfo){
	if (!empty($eInfo)){
		$Box = htmlBase::newElement('div')
		->addClass('ui-widget-content ui-corner-all mainBox')
		->css(array(
			'float' => 'left',
			'margin' => '.5em',
			'min-width' => '260px',
			'min-height' => '250px',
			'padding' => '.5em'
		));

		$checkboxes = '<div class="ui-widget-header"><input type="checkbox" class="extensionBox checkAllApps"> ' . $ExtName . '</div>';
		foreach($eInfo as $appName => $aInfo){
			$checkboxes .= '<div><div class="ui-state-hover" style="margin: .5em .5em 0 .5em"><input type="checkbox" class="appBox checkAllPages"> ' . $appName . '</div>';
			foreach($aInfo as $pageName => $pageChecked){
				$checkboxes .= '<div style="margin: 0 0 0 1em;"><input type="checkbox" class="pageBox" name="applications[ext][' . $ExtName . '][' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName . '</div>';
			}
			$checkboxes .= '</div>';
		}

		$Box->html($checkboxes);
		$BoxesContainer->append($Box);
	}
}
$BoxesContainer->append(htmlBase::newElement('div')->addClass('ui-helper-clearfix'));

if (!isset($_GET['lID'])){
	$layoutTemplatesContainer = htmlBase::newElement('div');
	$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/pdfPrinter/layoutTemplates/');
	foreach($Dir as $d){
		if ($d->isFile() === true || $d->isDot() === true){
			continue;
		}

		$Box = htmlBase::newElement('div')
		->css(array(
			'float' => 'left',
			'margin' => '.5em'
		))
		->html('<center>' .
		'<input type="radio" name="layout_template" value="' . $d->getBasename() . '"' . ($d->getBasename() == 'empty' ? ' checked=checked' : '') . '>' .
		'&nbsp;' . ucfirst($d->getBasename()) . '<br>' .
		'<img src="' . sysConfig::getDirWsCatalog() . 'extensions/pdfPrinter/layoutTemplates/' . $d->getBasename() . '/' . $d->getBasename() . '.png" width="200" height="200">' .
		'</center>');

		$layoutTemplatesContainer->append($Box);
	}
	$layoutTemplatesContainer->append(htmlBase::newElement('div')->addClass('ui-helper-clearfix'));

	$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Layout Type:'),
			array('text' => $layoutTemplatesContainer->draw())
		)
	));
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW') . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);
$infoBox->addContentRow($SettingsTable->draw());

ob_start();
?>
<script type="text/javascript">
	function newWindowOnLoad() {
		var height = 0;
		var width = 0;
		$('.mainBox').each(function () {
			if ($(this).outerWidth() > width){
				width = $(this).outerWidth();
			}

			if ($(this).outerHeight() > height){
				height = $(this).outerHeight();
			}
		});

		$('.mainBox').width(width).height(height);
	}
</script>
<?php
$javascript = ob_get_contents();
ob_end_clean();

EventManager::attachActionResponse($javascript . $infoBox->draw(), 'html');
?>