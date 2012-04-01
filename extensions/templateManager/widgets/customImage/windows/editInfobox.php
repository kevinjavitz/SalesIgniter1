<?php
ob_start();
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
			$pageName = $Page->getBasename('.php');

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

	$ExtCls = $appExtension->getExtension($Extension->getBasename());
	if ($ExtCls && $ExtCls->isEnabled() && is_dir($Extension->getPathName() . '/catalog/base_app/')){
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
			}
			elseif ($Extension->getBasename() == 'categoriesPages') {
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
			}
			elseif (is_dir($ExtApplication->getPathname() . '/pages/')) {
				$ExtPages = new DirectoryIterator($ExtApplication->getPathname() . '/pages/');
				foreach($ExtPages as $ExtPage){
					if ($ExtPage->isDot() || $ExtPage->isDir()){
						continue;
					}
					$pageName = $ExtPage->getBasename('.php');

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
					$pageName = $Page->getBasename('.php');

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
							$pageName = $Page->getBasename('.php');

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

function makeCategoriesArray($parentId = 0) {
	$catArr = array();
	$Qcategories = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name as categories_name')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('parent_id = ?', $parentId)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qcategories as $category){
		$catArr[$category['categories_id']] = array(
			'name' => addslashes($category['categories_name'])
		);

		$Children = makeCategoriesArray($category['categories_id']);
		if (!empty($Children)){
			$catArr[$category['categories_id']]['children'] = $Children;
		}
	}

	return $catArr;
}

$CatArr = makeCategoriesArray(0);

$LinkTypes = array(
	'none'     => 'No Link',
	'app'      => 'Application',
	'category' => 'Category',
	'custom'   => 'Custom'
);

$LinkTargets = array(
	'same'   => '-- Link Target --',
	'same'   => 'Same Window',
	'new'    => 'New Window',
	'dialog' => 'jQuery Dialog'
);
?>
<style>
	#imagesSortable { list-style-type: none; margin: 0; padding: 0; }
	#imagesSortable li { display:inline-block;vertical-align: top;margin: 3px 3px 3px 0; padding: 1px; width: 350px; font-size: 4em; text-align: left; }
	#imagesSortable li div { cursor: move;margin: 0;padding: 3px;border: 1px solid black;background:#ffffff; }
	#imagesSortable li .ui-icon-closethick {
		float  : right;
		margin : .5em;
	}

	#imagesSortable li select {
		margin-left  : .5em;
		margin-right : .5em;
	}
</style>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/ui/jquery.ui.selectmenu.js"></script>
<link rel="stylesheet" href="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/themes/smoothness/ui.selectmenu.css" type="text/css" media="screen,projection" />
<script type="text/javascript">
var appJson = <?php echo json_encode($AppArray);?>;
var catJson = <?php echo json_encode($CatArr);?>;
var LinkTypes = <?php echo json_encode($LinkTypes);?>;
var LinkTargets = <?php echo json_encode($LinkTargets);?>;

$(document).ready(function () {
	$('#imagesSortable').sortable({
		tolerance: 'pointer',
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true
	});

	$('#imagesTable').find('.addMainBlock').click(function () {
		var inputKey = 0;
		while($('#imagesSortable > li[data-input_key=' + inputKey + ']').size() > 0){
			inputKey++;
		}

		var LinkTypesOptions = '';
		$.each(LinkTypes, function (k, v) {
			LinkTypesOptions += '<option value="' + k + '">' + v + '</option>';
		});

		$('#imagesSortable')
			.append('<li id="image_' + inputKey + '" data-input_key="' + inputKey + '">' +
			'<div><table cellpadding="2" cellspacing="0" border="0" width="100%">' +
			'<tr>' +
			'<td valign="top"><table cellpadding="2" cellspacing="0" border="0">' +
		<?php foreach(sysLanguage::getLanguages() as $lInfo){ ?>
			'<tr>' +
				'<td><?php echo $lInfo['showName']('&nbsp;');?></td>' +
				'<td><input type="text" class="BrowseServerField" name="image_source[' + inputKey + '][<?php echo $lInfo['id'];?>]" value=""></td>' +
				'</tr>' +
		<?php } ?>
			'</table></td>' +
			'<td valign="top">' +
			'<span class="ui-icon ui-icon-closethick imageDelete" tooltip="Delete Image"></span>' +
			'</td>' +
			'</tr>' +
			'<tr>' +
			'<td valign="top" colspan="2"><select name="image_link[' + inputKey + ']" class="imageLinkType">' + LinkTypesOptions + '</select></td>' +
			'</tr>' +
			'</table></div>' +
			'</li>');
		$('#imagesSortable').sortable('refresh');
	});

	$('.imageLinkType').live('change', function () {
		var inputKey = $(this).parentsUntil('ol').last().attr('data-input_key');

		$(this).parent().find('.linkFields').remove();
		if ($(this).val() == 'app'){
			var options = '<option value="none">-- Application --</option>';
			$.each(appJson, function (appName, pages) {
				if (appName == 'ext'){
					return;
				}

				options = options + '<option value="' + appName + '">' + appName + '</option>';
			});
			$.each(appJson.ext, function (extName, Apps) {
				$.each(Apps, function (appName, pages) {
					var val = extName + '/' + appName;
					var text = extName + ' > ' + appName;
					options = options + '<option value="' + val + '">' + text + '</option>';
				});
			});
			var field = '<select name="image_link_app[' + inputKey + ']" class="imageLinkApp linkFields" style="display:block"></select>';
			var targetName = 'image_link_app_target';
		} else if ($(this).val() == 'category'){
			var options = '<option value="none">-- Category --</option>';
			$.each(catJson, function (categoryId, cInfo) {
				options = options + '<option value="' + categoryId + '" data-hasChild="' + (cInfo.children ? 'true' : 'false') + '">' + cInfo.name + '</option>';
			});
			var field = '<select name="image_link_category[' + inputKey + ']" class="imageLinkCategory linkFields" style="display:block"></select>';
			var targetName = 'image_link_category_target';
		}
		else {
			if ($(this).val() == 'custom'){
				var field = '<input type="text" name="image_link_custom[' + inputKey + ']" class="linkFields" style="display:block">';
				var targetName = 'image_link_custom_target';
			}
		}

		if ($(this).val() != 'none'){
			$(field).append(options).appendTo($(this).parent());

			var LinkTargetOptions = '';
			$.each(LinkTargets, function (k, v) {
				LinkTargetOptions += '<option value="' + k + '">' + v + '</option>';
			});

			$('<select name="' + targetName + '[' + inputKey + ']" class="imageLinkAppTarget linkFields" style="display:block">' + LinkTargetOptions + '</select>')
				.insertAfter(this);
		}
	});

	$('.imageLinkApp').live('change', function () {
		var inputKey = $(this).parentsUntil('ol').last().attr('data-input_key');

		var options = '<option value="none">-- Page --</option>';
		if ($(this).val().indexOf('/') > -1){
			var extInfo = $(this).val().split('/');

			var extension = extInfo[0];
			var application = extInfo[1];
			$.each(appJson.ext[extension][application], function (pageName, tORf) {
				options = options + '<option value="' + pageName + '">' + pageName + '</option>';
			});
		}
		else {
			$.each(appJson[$(this).val()], function (pageName, tORf) {
				options = options + '<option value="' + pageName + '">' + pageName + '</option>';
			});
		}
		$(this).parent().find('.imageLinkAppPage').remove();
		$('<select name="image_link_app_page[' + inputKey + ']" class="imageLinkAppPage linkFields" style="display:block"></select>')
			.append(options).appendTo($(this).parent());
	});

	$('.imageLinkCategory').live('change', function () {
		var inputKey = $(this).parentsUntil('ol').last().attr('data-input_key');

		if ($(this).find('option:selected').attr('data-hasChild') == 'true'){
			var baseArr = catJson;
			$(this).parent().find('.imageLinkCategory').each(function () {
				if (baseArr['children'] && baseArr['children'][$(this).val()]){
					baseArr = baseArr['children'][$(this).val()];
				}
				else {
					baseArr = baseArr[$(this).val()];
				}
			});

			if (baseArr.children){
				var options = '<option value="none">-- Category --</option>';
				$.each(baseArr.children, function (categoryId, cInfo) {
					options = options + '<option value="' + categoryId + '" data-hasChild="' + (cInfo.children ? 'true' : 'false') + '">' + cInfo.name + '</option>';
				});
				var field = '<select name="image_link_category_path[' + inputKey + '][]" class="imageLinkCategory linkFields" style="display:block"></select>';
				$(field).append(options).appendTo($(this).parent());
			}
		}
	});

	$('.imageDelete').live('click', function () {
		$(this).parentsUntil('ol').last().remove();
	});

	$('.saveButton').click(function () {
		$('input[name=imagesSortable]').val($('#imagesSortable').sortable('serialize'));
	});
});
</script>
<?php
$editTable = htmlBase::newElement('table')
	->setId('imagesTable')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => '<b>Images</b>')
	)
));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<span class="ui-icon ui-icon-plusthick addMainBlock"></span><span class="ui-icon ui-icon-closethick"></span>')
		)
	));

	function buildCategoryBoxes($catArr, $sKey, $sArr, $i) {
		$field = '';
		$selectBox = htmlBase::newElement('selectbox')
			->setName('image_link_category_path[' . $i . '][]')
			->addClass('imageLinkCategory linkFields')
			->css('display', 'block')
			->attr('data-hasChild', (isset($catArr['children']) && sizeof($catArr['children']) > 0 ? 'true' : 'false'))
			->selectOptionByValue((isset($sArr[$sKey]) ? $sArr[$sKey] : 'none'));
		$selectBox->addOption('none', '-- Category --');
		foreach($catArr as $id => $cInfo){
			$selectBox->addOption($id, $cInfo['name']);
		}
		$field .= $selectBox->draw();

		foreach($catArr as $id => $cInfo){
			if (isset($cInfo['children']) && sizeof($cInfo['children']) > 0){
				$sKey++;
				$field .= buildCategoryBoxes($cInfo['children'], $sKey, $sArr, $i);
			}
		}
		return $field;
	}

	function parseImage($item, &$i) {
		global $AppArray, $CatArr, $LinkTypes, $LinkTargets, $template;

		$textInputs = '<table cellpadding="2" cellspacing="0" border="0">';
		foreach(sysLanguage::getLanguages() as $lInfo){
			$textInput = htmlBase::newElement('input')
				->addClass('BrowseServerField')
				->setName('image_source[' . $i . '][' . $lInfo['id'] . ']')
				->val((isset($item->image->{$lInfo['id']}) ? $item->image->{$lInfo['id']} : ''));
			$textInputs .= '<tr>' .
				'<td>' . $lInfo['showName']('&nbsp;') . '</td>' .
				'<td>' . $textInput->draw() . '</td>' .
				'</tr>';
		}
		$textInputs .= '</table>';

		$linkTargetMenu = htmlBase::newElement('selectbox')
			->setName('tempName[' . $i . ']')
			->addClass('imageLinkAppTarget linkFields');
		foreach($LinkTargets as $k => $v){
			$linkTargetMenu->addOption($k, $v);
		}

		$linkTypeMenu = htmlBase::newElement('selectbox')
			->setName('image_link[' . $i . ']')
			->addClass('imageLinkType');
		if ($item->link !== false){
			$linkTypeMenu->selectOptionByValue($item->link->type);
		}
		foreach($LinkTypes as $k => $v){
			$linkTypeMenu->addOption($k, $v);
		}

		$itemTemplate = '<li id="image_' . $i . '" data-input_key="' . $i . '">' .
			'<div><table cellpadding="2" cellspacing="0" border="0" width="100%">' .
			'<tr>' .
			'<td valign="top">' . $textInputs . '</td>' .
			'<td valign="top"><span class="ui-icon ui-icon-closethick imageDelete" tooltip="Delete Image"></span></td>' .
			'</tr>' .
			'<tr>' .
			'<td valign="top" colspan="2"><table cellpadding="2" cellspacing="0" border="0" width="100%">';

		if ($item->link !== false){
			$linkTargetMenu->selectOptionByValue($item->link->target);
			$field = '';

			if ($item->link->type == 'app'){
				$appMenu = htmlBase::newElement('selectbox')
					->setName('image_link_app[' . $i . ']')
					->addClass('imageLinkApp linkFields')
					->css('display', 'block')
					->selectOptionByValue($item->link->application);
				$appMenu->addOption('none', '-- Application --');

				foreach($AppArray as $appName => $pages){
					if ($appName == 'ext'){
						continue;
					}

					$appMenu->addOption($appName, $appName);
				}

				foreach($AppArray['ext'] as $extName => $apps){
					foreach($apps as $appName => $pages){
						$appMenu->addOption($extName . '/' . $appName, $extName . ' > ' . $appName);
					}
				}

				$pagesMenu = htmlBase::newElement('selectbox')
					->setName('image_link_app_page[' . $i . ']')
					->addClass('imageLinkAppPage linkFields')
					->css('display', 'block')
					->selectOptionByValue($item->link->page);
				$pagesMenu->addOption('none', '-- Page --');

				if (stristr($item->link->application, '/')){
					$extInfo = explode('/', $item->link->application);

					$extName = $extInfo[0];
					$appName = $extInfo[1];
					foreach($AppArray['ext'][$extName][$appName] as $pageName => $tORf){
						$pageName = str_replace('.php', '', $pageName);
						$pagesMenu->addOption($pageName, $pageName);
					}
				}
				else {
					foreach($AppArray[$item->link->application] as $pageName => $tORf){
						$pageName = str_replace('.php', '', $pageName);
						$pagesMenu->addOption($pageName, $pageName);
					}
				}

				$field = $appMenu->draw() . $pagesMenu->draw();

				$linkTargetMenu->setName('image_link_app_target[' . $i . ']');
			}
			elseif ($item->link->type == 'category') {
				$catPath = explode('_', substr($item->link->get_vars, strpos($item->link->get_vars, '=') + 1));

				$rootMenu = htmlBase::newElement('selectbox')
					->setName('image_link_category[' . $i . ']')
					->addClass('imageLinkCategory linkFields')
					->css('display', 'block')
					->attr('data-hasChild', (isset($CatArr[$catPath[0]]['children']) && sizeof($CatArr[$catPath[0]]['children']) > 0 ? 'true' : 'false'))
					->selectOptionByValue($catPath[0]);
				$rootMenu->addOption('none', '-- Category --');

				foreach($CatArr as $id => $cInfo){
					$rootMenu->addOption($id, $cInfo['name']);
				}

				$field = $rootMenu->draw();
				if (isset($CatArr[$catPath[0]]['children']) && sizeof($CatArr[$catPath[0]]['children']) > 0){
					$field .= buildCategoryBoxes($CatArr[$catPath[0]]['children'], 1, $catPath, $i);
				}

				$linkTargetMenu->setName('image_link_category_target[' . $i . ']');
			}
			elseif ($item->link->type == 'custom') {
				$field = htmlBase::newElement('input')
					->setName('image_link_custom[' . $i . ']')
					->addClass('linkFields')
					->css('display', 'block')
					->val($item->link->url)
					->draw();

				$linkTargetMenu->setName('image_link_custom_target[' . $i . ']');
			}

			if ($item->link->type != 'none'){
				$itemTemplate .= '<tr><td><b><u>Link Target</u></b></td></tr><tr><td>' . $linkTargetMenu->draw() . '</td></tr>';
			}
			$itemTemplate .= '<tr><td><b><u>Link Settings</u></b></td></tr><tr><td>' . $linkTypeMenu->draw();
			if ($item->link->type != 'none'){
				$itemTemplate .= $field . '<br>';
			}
			$itemTemplate .= '</td></tr>';
		}
		else {
			$options = '';
			foreach($LinkTypes as $k => $v){
				$options .= '<option value="' . $k . '">' . $v . '</option>';
			}
			$itemTemplate .= '<tr><td><b><u>Link Settings</u></b></td></tr><tr><td><select name="image_link[' . $i . ']" class="imageLinkType">' . $options . '</select></td></tr>';
		}

		$itemTemplate .= '</table></td></tr></table></div>';

		$i++;

		$itemTemplate .= '</li>';

		return $itemTemplate;
	}

$Images = '';
	if (isset($WidgetSettings->images)){
		$i = 0;
		foreach($WidgetSettings->images as $iInfo){
			$Images .= parseImage($iInfo, &$i);
		}
	}

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<ol id="imagesSortable" class="ui-widget sortable">' . $Images . '</ol>')
		)
	));

echo $editTable->draw();
echo '<input type="hidden" name="imagesSortable" value="">';
$fileContent = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array(
			'colspan' => 2,
			'text'    => $fileContent
		)
	)
));
