<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stephen
 * Date: 3/26/11
 * Time: 5:14 PM
 * To change this template use File | Settings | File Templates.
 */

$layoutName = '';
$layoutType = 'desktop';
$selApps = array();
$pageTypes = array();
if (isset($_GET['lID'])){
	$QLayout = Doctrine_Query::create()
	->select('layout_id, layout_name, layout_type')
	->from('TemplateManagerLayouts')
	->where('layout_id = ?', (int) $_GET['lID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$layoutId = $QLayout[0]['layout_id'];
	$layoutName = $QLayout[0]['layout_name'];
	$layoutType = $QLayout[0]['layout_type'];

	$QselApps = Doctrine_Query::create()
	->from('TemplatePages')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QselApps as $sInfo){
		$layouts = explode(',', $sInfo['layout_id']);
		$pageType = explode(',', $sInfo['page_type']);
		$assocurls = explode(',', $sInfo['associative_url']);
		if (in_array($layoutId, $layouts) && !empty($pageType) && !empty($assocurls)){
			if (!empty($sInfo['extension'])){
				$selApps['ext'][$sInfo['extension']][$sInfo['application']][$sInfo['page']] = true;
				$pageTypes['ext'][$sInfo['extension']][$sInfo['application']][$sInfo['page']] = isset($pageType[array_search($layoutId,$layouts)])?$pageType[array_search($layoutId,$layouts)]:'';
				$assocurl['ext'][$sInfo['extension']][$sInfo['application']][$sInfo['page']] = isset($assocurls[array_search($layoutId,$layouts)])?$assocurls[array_search($layoutId,$layouts)]:'';

			}
			else {
				$selApps[$sInfo['application']][$sInfo['page']] = true;
				$pageTypes[$sInfo['application']][$sInfo['page']] = isset($pageType[array_search($layoutId,$layouts)])?$pageType[array_search($layoutId,$layouts)]:'';
				$assocurl[$sInfo['application']][$sInfo['page']] = isset($assocurls[array_search($layoutId,$layouts)])?$assocurls[array_search($layoutId,$layouts)]:'';
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
				->addOption('desktop', 'Desktop')
				->addOption('smartphone', 'Smart Phone')
				->addOption('tablet', 'Tablet')
				->selectOptionByValue($layoutType)
				->draw())
		)
	));

$layoutBackupsHtml = htmlBase::newElement('selectbox')
	->setName('layoutBackup')
	->selectOptionByValue('0');
$layoutBackupsHtml->addOption('0','Please Select');

$QLayoutsBackup = Doctrine_Query::create()
	->from('TemplateManagerLayouts')
	->andWhere('backupof_layout_id = ?', $_GET['lID'])
	->orderBy('backup_date desc')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

foreach($QLayoutsBackup as $backupL){
	$layoutBackupsHtml->addOption($backupL['layout_id'], $backupL['backup_date']);
}

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Load Layout Backup:'),
			array('text' => $layoutBackupsHtml->draw())
		)
	));

$iTemplate = Doctrine_Query::create()
	->from('TemplateManagerTemplates')
	->where('template_id = ?', $_GET['tID'])
	->fetchOne();

if($iTemplate->Configuration['NAME']->configuration_value == 'codeGeneration'){
	$associativeUrl = htmlBase::newElement('input')
		->setLabel('Show in page:');

}

function makeCategoriesArray($parentId = 0){
	$catArr = array();
	$Qcategories = Doctrine_Query::create()
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('parent_id = ?', $parentId)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qcategories as $category){
		$catArr[$category['categories_id']] = array(
			'name' => $category['CategoriesDescription'][0]['categories_seo_url']
		);

		$Children = makeCategoriesArray($category['categories_id']);
		if (!empty($Children)){
			$catArr[$category['categories_id']]['children'] = $Children;
		}
	}

	return $catArr;
}
$CatArr = makeCategoriesArray(0);

function buildCategorisPages($CatArr, &$AppArray, $appName){
	global $selApps;
	foreach($CatArr as $cat){
		$pageName = $cat['name'];
		$AppArray[$appName][$pageName] = (isset($selApps[$appName][$pageName]) ? $selApps[$appName][$pageName] : false);
		if (isset($cat['children']) && sizeof($cat['children']) > 0){
			buildCategorisPages($cat['children'], $AppArray, $appName);
		}
	}
}


function makeBlogCategoriesArray($parentId = 0){
	$catArr = array();
	$Qcategories = Doctrine_Query::create()
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('parent_id = ?', $parentId)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qcategories as $category){
		$catArr[$category['blog_categories_id']] = array(
			'name' => $category['BlogCategoriesDescription'][0]['blog_categories_seo_url']
		);

		$Children = makeBlogCategoriesArray($category['blog_categories_id']);
		if (!empty($Children)){
			$catArr[$category['blog_categories_id']]['children'] = $Children;
		}
	}

	return $catArr;
}
$BlogCatArr = makeBlogCategoriesArray(0);

function buildBlogCategorisPages($BlogCatArr, &$AppArray, $appName, $extName){
	global $selApps;
	foreach($BlogCatArr as $cat){
		$pageName = $cat['name'];
		$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
		if (isset($cat['children']) && sizeof($cat['children']) > 0){
			buildBlogCategorisPages($cat['children'], $AppArray, $appName, $extName);
		}
	}
}

function buildBlogCategorisPagesPosts($BlogCatArr, &$AppArray, $appName, $extName){
	global $selApps;
	foreach($BlogCatArr as $cat){
		$pageName = 'posts-'.$cat['name'];
		$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
		if (isset($cat['children']) && sizeof($cat['children']) > 0){
			buildBlogCategorisPagesPosts($cat['children'], $AppArray, $appName, $extName);
		}
	}
}




function buildProductPages(&$AppArray, $appName){
	global $selApps;
	$QProducts = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'));

	EventManager::notify('AdminProductListingTemplateQueryBeforeExecute', &$QProducts);

	$QProducts = $QProducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QProducts as $prod){
		$pageName = $prod['products_id'];
		$AppArray[$appName][$pageName] = (isset($selApps[$appName][$pageName]) ? $selApps[$appName][$pageName] : false);
	}
}

$Applications = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'applications/');
$AppArray = array();
foreach($Applications as $AppDir){
	if ($AppDir->isDot() || $AppDir->isFile()){
		continue;
	}
	$appName = $AppDir->getBasename();

	$AppArray[$appName] = array();
	if($appName == 'index'){
		buildCategorisPages($CatArr, $AppArray, $appName);
	}

	if($appName == 'product' && isset($associativeUrl)){
		buildProductPages($AppArray, $appName);
	}
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
			if ($Extension->getBasename() == 'infoPages'){
				$Qpages = Doctrine_Query::create()
					->select('page_key')
					->from('Pages')
					->where('page_type = ?', 'page')
					->orderBy('page_key asc')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qpages){
					foreach($Qpages as $pInfo){
						$pageName = $pInfo['page_key'];

						$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
					}
				}
			}elseif ($Extension->getBasename() == 'categoriesPages' && $appExtension->isInstalled('categoriesPages') && $appExtension->isEnabled('categoriesPages')){
				$Qpages = Doctrine_Query::create()
					->select('page_key')
					->from('CategoriesPages')
					->orderBy('page_key asc')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qpages){
					foreach($Qpages as $pInfo){
						$pageName = $pInfo['page_key'];

						$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
					}
				}
			}elseif (is_dir($ExtApplication->getPathname() . '/pages/')){
				$ExtPages = new DirectoryIterator($ExtApplication->getPathname() . '/pages/');
				foreach($ExtPages as $ExtPage){
					if ($ExtPage->isDot() || $ExtPage->isDir()){
						continue;
					}
					$pageName = $ExtPage->getBasename();

					$AppArray['ext'][$extName][$appName][$pageName] = (isset($selApps['ext'][$extName][$appName][$pageName]) ? $selApps['ext'][$extName][$appName][$pageName] : false);
				}
			}

			if ($Extension->getBasename() == 'blog' && $appExtension->isInstalled('blog') && $appExtension->isEnabled('blog') && $appName == 'show_category'){

				buildBlogCategorisPages($BlogCatArr, $AppArray, $appName, $extName);

			}

			if ($Extension->getBasename() == 'blog' && $appExtension->isInstalled('blog') && $appExtension->isEnabled('blog') && $appName == 'show_post'){

				buildBlogCategorisPagesPosts($BlogCatArr, $AppArray, $appName, $extName);

			}

			//add here post-categoryname...then check in template

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

$rentalMemberCheckbox = htmlBase::newElement('checkbox')->setLabel('R')->setValue('R');
$nonRentalMemberCheckbox = htmlBase::newElement('checkbox')->setLabel('N')->setValue('N');



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
			$pageName1 = $pageName;
			if($appName == 'product' && is_numeric($pageName) && isset($associativeUrl)){
				$QProducts = Doctrine_Query::create()
					->from('Products p')
					->leftJoin('p.ProductsDescription pd')
					->where('pd.language_id = ?', Session::get('languages_id'))
					->andWhere('p.products_id = ?', $pageName)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$pageName1 = $QProducts[0]['ProductsDescription'][0]['products_name'];
			}
			$rentalMemberCheckbox
					->setName('pagetype[' . $appName . '][' . $pageName . ']')
					->setChecked((isset($pageTypes[$appName][$pageName]) && $pageTypes[$appName][$pageName] == 'R') ? true : false);

			$nonRentalMemberCheckbox
					->setName('pagetype[' . $appName . '][' . $pageName . ']')
					->setChecked((isset($pageTypes[$appName][$pageName]) && $pageTypes[$appName][$pageName] == 'N') ? true : false);

			$checkboxes .= '<div style="margin: 0 0 0 1em;"><input class="pageBox" type="checkbox" name="applications[' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName1;
			$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$nonRentalMemberCheckbox->draw();
			$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$rentalMemberCheckbox->draw();
			if(isset($associativeUrl)){
				$associativeUrl->setName('assocurl['. $appName . '][' . $pageName . ']')
				->setValue(isset($assocurl[$appName][$pageName])?$assocurl[$appName][$pageName]:'');
				$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$associativeUrl->draw();
			}
			$checkboxes .= '</div>';

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
				$rentalMemberCheckbox
						->setName('pagetype[ext][' . $ExtName . '][' . $appName . '][' . $pageName . ']')
						->setChecked((isset($pageTypes['ext'][$ExtName][$appName][$pageName]) && $pageTypes['ext'][$ExtName][$appName][$pageName] == 'R') ? true : false);

				$nonRentalMemberCheckbox
						->setName('pagetype[ext][' . $ExtName . '][' . $appName . '][' . $pageName . ']')
						->setChecked((isset($pageTypes['ext'][$ExtName][$appName][$pageName]) && $pageTypes['ext'][$ExtName][$appName][$pageName] == 'N') ? true : false);

				$checkboxes .= '<div style="margin: 0 0 0 1em;"><input type="checkbox" class="pageBox" name="applications[ext][' . $ExtName . '][' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName;
				$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$nonRentalMemberCheckbox->draw();
				$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$rentalMemberCheckbox->draw();

				if(isset($associativeUrl)){
					$associativeUrl->setName('assocurl[ext][' . $ExtName . '][' . $appName . '][' . $pageName . ']')
						->setValue(isset($assocurl['ext'][$ExtName][$appName][$pageName])?$assocurl['ext'][$ExtName][$appName][$pageName]:'');
					$checkboxes .= '&nbsp;&nbsp;&nbsp;'.$associativeUrl->draw();
				}
				$checkboxes .= '</div>';
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
	$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/templateManager/layoutTemplates/');
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
		'<img src="' . sysConfig::getDirWsCatalog() . 'extensions/templateManager/layoutTemplates/' . $d->getBasename() . '/' . $d->getBasename() . '.png" width="200" height="200">' .
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

$SettingsTable->addBodyRow(array(
	'columns' => array(
		array( 'text' => 'Layout Pages:'),
		array('css'=>array('color'=>'red'),'text' => '<strong>N : Non Rental Members <br> R : Rental Members</strong>')
	)
));

$SettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<input type="checkbox" id="checkAll"/> <span id="checkAllText">Check All Pages</span>' . $BoxesContainer->draw())
	)
));

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

		$('#checkAll').click(function(){
			var self = this;
			$(this).parent().find('.appBox').each(function (){
				this.checked = self.checked;
			});
			$(this).parent().find('.pageBox').each(function (){
				this.checked = self.checked;
			});
			$(this).parent().find('.extensionBox').each(function (){
				this.checked = self.checked;
			});

			if (self.checked){
				$('#checkAllText').html('Uncheck All Elements');
			}else{
				$('#checkAllText').html('Check All Elements');
			}
		});

		$('.checkAllPages').click(function (){
			var self = this;
			$(self).parent().parent().find('.pageBox').each(function (){
				this.checked = self.checked;
			});
		});

		$('.checkAllApps').click(function (){
			var self = this;
			$(self).parent().parent().find('.appBox').each(function (){
				this.checked = self.checked;
			});
			$(self).parent().parent().find('.pageBox').each(function (){
				this.checked = self.checked;
			});
		});

	}
</script>
<?php
$javascript = ob_get_contents();
ob_end_clean();

EventManager::attachActionResponse($javascript . $infoBox->draw(), 'html');
?>