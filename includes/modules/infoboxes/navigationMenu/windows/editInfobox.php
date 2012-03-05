<?php
ob_start();
if (!isset($WidgetSettings->linked_to)){
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
				}elseif ($Extension->getBasename() == 'categoriesPages'){
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

	function makeCategoriesArray($parentId = 0){
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

	$menuIcons = array(
		'none' => '-- No Icon --',
		'jquery' => 'jQuery Icon',
		'custom' => 'My Own Icon'
	);

	$menuLinkTypes = array(
		'none' => 'No Link',
		'app' => 'Application',
		'category' => 'Category',
		'custom' => 'Custom'
	);

	$menuLinkTargets = array(
		'same' => '-- Link Target --',
		'same' => 'Same Window',
		'new' => 'New Window',
		'dialog' => 'jQuery Dialog'
	);

	$menuItemConditions = array(
		'' => '-- No Condition --',
		'customer_logged_in' => 'Customer is logged in',
		'customer_not_logged_in' => 'Customer is not logged in',
		'shopping_cart_empty' => 'Shopping cart is empty',
		'shopping_cart_not_empty' => 'Shopping cart is not empty'
	);

	$jqueryIcons = array('ui-icon-carat-1-n', 'ui-icon-carat-1-ne', 'ui-icon-carat-1-e', 'ui-icon-carat-1-se', 'ui-icon-carat-1-s', 'ui-icon-carat-1-sw', 'ui-icon-carat-1-w', 'ui-icon-carat-1-nw', 'ui-icon-carat-2-n-s', 'ui-icon-carat-2-e-w', 'ui-icon-triangle-1-n', 'ui-icon-triangle-1-ne', 'ui-icon-triangle-1-e', 'ui-icon-triangle-1-se', 'ui-icon-triangle-1-s', 'ui-icon-triangle-1-sw', 'ui-icon-triangle-1-w', 'ui-icon-triangle-1-nw', 'ui-icon-triangle-2-n-s', 'ui-icon-triangle-2-e-w', 'ui-icon-arrow-1-n', 'ui-icon-arrow-1-ne', 'ui-icon-arrow-1-e', 'ui-icon-arrow-1-se', 'ui-icon-arrow-1-s', 'ui-icon-arrow-1-sw', 'ui-icon-arrow-1-w', 'ui-icon-arrow-1-nw', 'ui-icon-arrow-2-n-s', 'ui-icon-arrow-2-ne-sw', 'ui-icon-arrow-2-e-w', 'ui-icon-arrow-2-se-nw', 'ui-icon-arrowstop-1-n', 'ui-icon-arrowstop-1-e', 'ui-icon-arrowstop-1-s', 'ui-icon-arrowstop-1-w', 'ui-icon-arrowthick-1-n', 'ui-icon-arrowthick-1-ne', 'ui-icon-arrowthick-1-e', 'ui-icon-arrowthick-1-se', 'ui-icon-arrowthick-1-s', 'ui-icon-arrowthick-1-sw', 'ui-icon-arrowthick-1-w', 'ui-icon-arrowthick-1-nw', 'ui-icon-arrowthick-2-n-s', 'ui-icon-arrowthick-2-ne-sw', 'ui-icon-arrowthick-2-e-w', 'ui-icon-arrowthick-2-se-nw', 'ui-icon-arrowthickstop-1-n', 'ui-icon-arrowthickstop-1-e', 'ui-icon-arrowthickstop-1-s', 'ui-icon-arrowthickstop-1-w', 'ui-icon-arrowreturnthick-1-w', 'ui-icon-arrowreturnthick-1-n', 'ui-icon-arrowreturnthick-1-e', 'ui-icon-arrowreturnthick-1-s', 'ui-icon-arrowreturn-1-w', 'ui-icon-arrowreturn-1-n', 'ui-icon-arrowreturn-1-e', 'ui-icon-arrowreturn-1-s', 'ui-icon-arrowrefresh-1-w', 'ui-icon-arrowrefresh-1-n', 'ui-icon-arrowrefresh-1-e', 'ui-icon-arrowrefresh-1-s', 'ui-icon-arrow-4', 'ui-icon-arrow-4-diag', 'ui-icon-extlink', 'ui-icon-newwin', 'ui-icon-refresh', 'ui-icon-shuffle', 'ui-icon-transfer-e-w', 'ui-icon-transferthick-e-w', 'ui-icon-folder-collapsed', 'ui-icon-folder-open', 'ui-icon-document', 'ui-icon-document-b', 'ui-icon-note', 'ui-icon-mail-closed', 'ui-icon-mail-open', 'ui-icon-suitcase', 'ui-icon-comment', 'ui-icon-person', 'ui-icon-print', 'ui-icon-trash', 'ui-icon-locked', 'ui-icon-unlocked', 'ui-icon-bookmark', 'ui-icon-tag', 'ui-icon-home', 'ui-icon-flag', 'ui-icon-calendar', 'ui-icon-cart', 'ui-icon-pencil', 'ui-icon-clock', 'ui-icon-disk', 'ui-icon-calculator', 'ui-icon-zoomin', 'ui-icon-zoomout', 'ui-icon-search', 'ui-icon-wrench', 'ui-icon-gear', 'ui-icon-heart', 'ui-icon-star', 'ui-icon-link', 'ui-icon-cancel', 'ui-icon-plus', 'ui-icon-plusthick', 'ui-icon-minus', 'ui-icon-minusthick', 'ui-icon-close', 'ui-icon-closethick', 'ui-icon-key', 'ui-icon-lightbulb', 'ui-icon-scissors', 'ui-icon-clipboard', 'ui-icon-copy', 'ui-icon-contact', 'ui-icon-image', 'ui-icon-video', 'ui-icon-script', 'ui-icon-alert', 'ui-icon-info', 'ui-icon-notice', 'ui-icon-help', 'ui-icon-check', 'ui-icon-bullet', 'ui-icon-radio-off', 'ui-icon-radio-on', 'ui-icon-pin-w', 'ui-icon-pin-s', 'ui-icon-play', 'ui-icon-pause', 'ui-icon-seek-next', 'ui-icon-seek-prev', 'ui-icon-seek-end', 'ui-icon-seek-start', 'ui-icon-seek-first', 'ui-icon-stop', 'ui-icon-eject', 'ui-icon-volume-off', 'ui-icon-volume-on', 'ui-icon-power', 'ui-icon-signal-diag', 'ui-icon-signal', 'ui-icon-battery-0', 'ui-icon-battery-1', 'ui-icon-battery-2', 'ui-icon-battery-3', 'ui-icon-circle-plus', 'ui-icon-circle-minus', 'ui-icon-circle-close', 'ui-icon-circle-triangle-e', 'ui-icon-circle-triangle-s', 'ui-icon-circle-triangle-w', 'ui-icon-circle-triangle-n', 'ui-icon-circle-arrow-e', 'ui-icon-circle-arrow-s', 'ui-icon-circle-arrow-w', 'ui-icon-circle-arrow-n', 'ui-icon-circle-zoomin', 'ui-icon-circle-zoomout', 'ui-icon-circle-check', 'ui-icon-circlesmall-plus', 'ui-icon-circlesmall-minus', 'ui-icon-circlesmall-close', 'ui-icon-squaresmall-plus', 'ui-icon-squaresmall-minus', 'ui-icon-squaresmall-close', 'ui-icon-grip-dotted-vertical', 'ui-icon-grip-dotted-horizontal', 'ui-icon-grip-solid-vertical', 'ui-icon-grip-solid-horizontal', 'ui-icon-gripsmall-diagonal-se', 'ui-icon-grip-diagonal-se');
	?>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/nestedSortable/jquery.ui.nestedSortable.js"></script>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/ui/jquery.ui.selectmenu.js"></script>
<link rel="stylesheet" href="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/themes/smoothness/ui.selectmenu.css" type="text/css" media="screen,projection" />
<script type="text/javascript">
	var appJson = <?php echo json_encode($AppArray);?>;
	var catJson = <?php echo json_encode($CatArr);?>;
	var menuIcons = <?php echo json_encode($menuIcons);?>;
	var menuLinkTypes = <?php echo json_encode($menuLinkTypes);?>;
	var menuLinkTargets = <?php echo json_encode($menuLinkTargets);?>;
	var menuItemConditions = <?php echo json_encode($menuItemConditions);?>;
	var jqueryIcons = <?php echo json_encode($jqueryIcons);?>;

	$(document).ready(function () {
		$('#navMenuTable').find('ol.sortable').nestedSortable({
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div'
		});

		$('#navMenuTable').find('.addMainBlock').click(function () {
			var inputKey = 0;
			while($('#navMenuTable').find('ol.sortable > li[data-input_key=' + inputKey + ']').size() > 0){
				inputKey++;
			}

			var menuIconOptions = '';
			$.each(menuIcons, function (k, v) {
				menuIconOptions += '<option value="' + k + '">' + v + '</option>';
			});

			var menuLinkTypesOptions = '';
			$.each(menuLinkTypes, function (k, v) {
				menuLinkTypesOptions += '<option value="' + k + '">' + v + '</option>';
			});

			var menuItemConditionsOptions = '';
			$.each(menuItemConditions, function (k, v) {
				menuItemConditionsOptions += '<option value="' + k + '">' + v + '</option>';
			});

			$('#navMenuTable').find('ol.sortable')
				.append('<li id="menu_item_' + inputKey + '" data-input_key="' + inputKey + '">' +
				'<div><table cellpadding="2" cellspacing="0" border="0">' +
				'<tr>' +
				'<td valign="top"><span>Show if (<select name="menu_item_condition[' + inputKey + ']">' + menuItemConditionsOptions + '</select>)</span></td>' +
				'<td valign="top"><select name="menu_item_icon[' + inputKey + ']" class="menuItemIcon">' + menuIconOptions + '</select></td>' +
				'<td valign="top"><table cellpadding="2" cellspacing="0" border="0">' +
				<?php
					foreach(sysLanguage::getLanguages() as $lInfo){
					echo '\'<tr>\' + ' . "\n" .
						'\'<td>' . $lInfo['showName']('&nbsp;') . '</td>\' + ' . "\n" .
						'\'<td><input type="text" name="menu_item_text[' . $lInfo['id'] . '][\' + inputKey + \']" value="Menu Text"></td>\' + ' . "\n" .
						'\'</tr>\' + ' . "\n";
				}
				?>
				'</table></td>' +
				'<td valign="top"><select name="menu_item_link[' + inputKey + ']" class="menuLinkType">' + menuLinkTypesOptions + '</select></td>' +
				'<td valign="top"><span class="ui-icon ui-icon-closethick menuItemDelete" tooltip="Delete Item and Children"></span></td>' +
				'</tr>' +
				'</table></div>' +
				'</li>');
		});

		$('.menuItemIcon').live('change', function () {
			var inputKey = $(this).parentsUntil('ol').last().attr('data-input_key');

			if ($(this).val() == 'jquery'){
				var options = '';
				$.each(jqueryIcons, function (k, v) {
					options = options + '<option value="' + k + '">' + v + '</option>';
				});
				var field = '<select name="menu_item_icon_src[' + inputKey + ']" class="menuItemIconSrc">' + options + '</select>';
			}
			else {
				if ($(this).val() == 'custom'){
					var field = '<input type="text" name="menu_item_icon_src[' + inputKey + ']" class="menuItemIconSrc BrowseServerField">';
				}
			}

			$(this).parent().find('.menuItemIconSrc').remove();
			$(field).insertAfter(this);

			if ($(this).val() == 'jquery'){
				$(this).parent().find('.menuItemIconSrc').selectmenu({
					style: 'dropdown',
					width: 60,
					menuWidth: 60,
					maxHeight: 300,
					format: function (text) {
						return '<span class="ui-icon ' + text + '" style="position:relative;top:.5em;"></span>';
					}
				});
			}
		});

		$('.menuLinkType').live('change', function () {
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
				var field = '<select name="menu_item_link_app[' + inputKey + ']" class="menuLinkApp linkFields" style="display:block"></select>';
				var targetName = 'menu_item_link_app_target';
			}else if ($(this).val() == 'category'){
				var options = '<option value="none">-- Category --</option>';
				$.each(catJson, function (categoryId, cInfo) {
					options = options + '<option value="' + categoryId + '" data-hasChild="' + (cInfo.children ? 'true' : 'false') + '">' + cInfo.name + '</option>';
				});
				var field = '<select name="menu_item_link_category[' + inputKey + ']" class="menuLinkCategory linkFields" style="display:block"></select>';
				var targetName = 'menu_item_link_category_target';
			}
			else {
				if ($(this).val() == 'custom'){
					var field = '<input type="text" name="menu_item_link_custom[' + inputKey + ']" class="linkFields" style="display:block">';
					var targetName = 'menu_item_link_custom_target';
				}
			}

			if ($(this).val() != 'none'){
				$(field).append(options).appendTo($(this).parent());

				var menuLinkTargetOptions = '';
				$.each(menuLinkTargets, function (k, v) {
					menuLinkTargetOptions += '<option value="' + k + '">' + v + '</option>';
				});

				$('<select name="' + targetName + '[' + inputKey + ']" class="menuLinkAppTarget linkFields" style="display:block">' + menuLinkTargetOptions + '</select>')
					.insertAfter(this);
			}
		});

		$('.menuLinkApp').live('change', function () {
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
			$(this).parent().find('.menuLinkAppPage').remove();
			$('<select name="menu_item_link_app_page[' + inputKey + ']" class="menuLinkAppPage linkFields" style="display:block"></select>')
				.append(options).appendTo($(this).parent());
		});

		$('.menuLinkCategory').live('change', function (){
			var inputKey = $(this).parentsUntil('ol').last().attr('data-input_key');

			if ($(this).find('option:selected').attr('data-hasChild') == 'true'){
				var baseArr = catJson;
				$(this).parent().find('.menuLinkCategory').each(function (){
					if (baseArr['children'] && baseArr['children'][$(this).val()]){
						baseArr = baseArr['children'][$(this).val()];
					}else{
						baseArr = baseArr[$(this).val()];
					}
				});

				if (baseArr.children){
					var options = '<option value="none">-- Category --</option>';
					$.each(baseArr.children, function (categoryId, cInfo){
						options = options + '<option value="' + categoryId + '" data-hasChild="' + (cInfo.children ? 'true' : 'false') + '">' + cInfo.name + '</option>';
					});
					var field = '<select name="menu_item_link_category_path[' + inputKey + '][]" class="menuLinkCategory linkFields" style="display:block"></select>';
					$(field).append(options).appendTo($(this).parent());
				}
			}
		});

		$('.menuItemDelete').live('click', function () {
			$(this).parentsUntil('ol').last().remove();
		});

		$('select.menuItemIconSrc').selectmenu({
			style: 'dropdown',
			width: 60,
			menuWidth: 60,
			maxHeight: 300,
			format: function (text) {
				return '<span class="ui-icon ' + text + '" style="position:relative;top:.5em;"></span>';
			}
		});

		$('.saveButton').click(function () {
			$('input[name=navMenuSortable]').val($('#navMenuTable').find('ol.sortable').nestedSortable('serialize'));
		});
	});
</script>
<style>
	.placeholder {
		background-color : #cfcfcf;
	}

	.ui-nestedSortable-error {
		background : #fbe3e4;
		color      : #8a1f11;
	}

	ol {
		margin       : 0;
		padding      : 0;
		padding-left : 30px;
	}

	ol.sortable, ol.sortable ol {
		margin          : 0 0 0 25px;
		padding         : 0;
		list-style-type : none;
	}

	ol.sortable {
		margin : 2em 0;
	}

	.sortable li {
		margin  : 7px 0 0 0;
		padding : 0;
	}

	.sortable li div {
		border  : 1px solid black;
		padding : 3px;
		margin  : 0;
		cursor  : move;
	}

	li .ui-icon-closethick {
		float  : right;
		margin : .5em;
	}

	li select {
		margin-left  : .5em;
		margin-right : .5em;
	}
</style>
				<?php

}
$editTable = htmlBase::newElement('table')
	->setId('navMenuTable')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => '<b>Navigation Menu</b>')
	)
));

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Id for css:<input type="text" name="menu_id" value="' . (isset($WidgetSettings->menuId) ? $WidgetSettings->menuId : '') . '">')
	)
));

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => '<input type="checkbox" name="force_fit" value="true"' . (isset($WidgetSettings->forceFit) && $WidgetSettings->forceFit == 'true' ? ' checked=checked' : '') . '> Expand To Fit Container')
	)
));

if (!isset($WidgetSettings->linked_to)){
	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<span class="ui-icon ui-icon-plusthick addMainBlock"></span><span class="ui-icon ui-icon-closethick"></span>')
		)
	));

	function buildCategoryBoxes($catArr, $sKey, $sArr, $i){
		$field = '';
		$selectBox = htmlBase::newElement('selectbox')
			->setName('menu_item_link_category_path[' . $i . '][]')
			->addClass('menuLinkCategory linkFields')
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

	function parseMenuItem($item, &$i) {
		global $AppArray, $CatArr, $menuIcons, $menuLinkTypes, $jqueryIcons, $menuItemConditions, $menuLinkTargets, $template;

		$iconMenu = htmlBase::newElement('selectbox')
			->setName('menu_item_icon[' . $i . ']')
			->addClass('menuItemIcon')
			->selectOptionByValue((isset($item->icon) ? $item->icon : ''));
		foreach($menuIcons as $k => $v){
			$iconMenu->addOption($k, $v);
		}

		$iconInput = '';
		if ($item->icon == 'jquery'){
			$iconSrcMenu = htmlBase::newElement('selectbox')
				->setName('menu_item_icon_src[' . $i . ']')
				->addClass('menuItemIconSrc')
				->selectOptionByValue($item->icon_src);
			foreach($jqueryIcons as $v){
				$iconSrcMenu->addOption($v, $v);
			}
			$iconInput = $iconSrcMenu->draw();
		}
		elseif ($item->icon == 'custom') {
			$iconInput = htmlBase::newElement('input')
				->setName('menu_item_icon_src[' . $i . ']')
				->addClass('menuItemIconSrc')
				->addClass('BrowseServerField')
				->val($item->icon_src)
				->draw();
		}
		$textInputs = '<table cellpadding="2" cellspacing="0" border="0">';
		foreach(sysLanguage::getLanguages() as $lInfo){
			$textInput = htmlBase::newElement('input')
				->setName('menu_item_text[' . $lInfo['id'] . '][' . $i . ']')
				->val($item->{$lInfo['id']}->text);
			$textInputs .= '<tr>' .
				'<td>' . $lInfo['showName']('&nbsp;') . '</td>' .
				'<td>' . $textInput->draw() . '</td>' .
				'</tr>';
		}
		$textInputs .= '</table>';

		$linkTargetMenu = htmlBase::newElement('selectbox')
			->setName('tempName[' . $i . ']')
			->addClass('menuLinkAppTarget linkFields');
		foreach($menuLinkTargets as $k => $v){
			$linkTargetMenu->addOption($k, $v);
		}

		$linkTypeMenu = htmlBase::newElement('selectbox')
			->setName('menu_item_link[' . $i . ']')
			->addClass('menuLinkType')
			->selectOptionByValue($item->link->type);
		foreach($menuLinkTypes as $k => $v){
			$linkTypeMenu->addOption($k, $v);
		}

		$linkConditionsMenu = htmlBase::newElement('selectbox')
			->setName('menu_item_condition[' . $i . ']')
			->addClass('menuItemCondition')
			->selectOptionByValue($item->condition);
		foreach($menuItemConditions as $k => $v){
			$linkConditionsMenu->addOption($k, $v);
		}

		$itemTemplate = '<li id="menu_item_' . $i . '" data-input_key="' . $i . '">' .
			'<div><table cellpadding="2" cellspacing="0" border="0" width="100%">' .
			'<tr>' .
			'<td valign="top"><span>Show if (' . $linkConditionsMenu->draw() . ')</span></td>' .
			'<td valign="top">' . $iconMenu->draw() . $iconInput . '</td>' .
			'<td valign="top">' . $textInputs . '</td>';

		if ($item->link !== false){
			$linkTargetMenu->selectOptionByValue($item->link->target);
			$field = '';

			if ($item->link->type == 'app'){
				$appMenu = htmlBase::newElement('selectbox')
					->setName('menu_item_link_app[' . $i . ']')
					->addClass('menuLinkApp linkFields')
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
					->setName('menu_item_link_app_page[' . $i . ']')
					->addClass('menuLinkAppPage linkFields')
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

				$linkTargetMenu->setName('menu_item_link_app_target[' . $i . ']');
			}
			elseif ($item->link->type == 'category'){
				$catPath = explode('_', substr($item->link->get_vars, strpos($item->link->get_vars, '=') + 1));

				$rootMenu = htmlBase::newElement('selectbox')
					->setName('menu_item_link_category[' . $i . ']')
					->addClass('menuLinkCategory linkFields')
					->css('display', 'block')
					->attr('data-hasChild', (sizeof($CatArr[$catPath[0]]['children']) > 0 ? 'true' : 'false'))
					->selectOptionByValue($catPath[0]);
				$rootMenu->addOption('none', '-- Category --');

				foreach($CatArr as $id => $cInfo){
					$rootMenu->addOption($id, $cInfo['name']);
				}

				$field = $rootMenu->draw();
				if (isset($CatArr[$catPath[0]]['children']) && sizeof($CatArr[$catPath[0]]['children']) > 0){
					$field .= buildCategoryBoxes($CatArr[$catPath[0]]['children'], 1, $catPath, $i);
				}

				$linkTargetMenu->setName('menu_item_link_category_target[' . $i . ']');
			}
			elseif ($item->link->type == 'custom') {
				$field = htmlBase::newElement('input')
					->setName('menu_item_link_custom[' . $i . ']')
					->addClass('linkFields')
					->css('display', 'block')
					->val($item->link->url)
					->draw();

				$linkTargetMenu->setName('menu_item_link_custom_target[' . $i . ']');
			}

			$itemTemplate .= '<td valign="top">' . $linkTypeMenu->draw();
			if ($item->link->type != 'none'){
				$itemTemplate .= $linkTargetMenu->draw() . $field;
			}
			$itemTemplate .= '</td>';
		}else{
			$options = '';
			foreach($menuLinkTypes as $k => $v){
				$options .= '<option value="' . $k . '">' . $v . '</option>';
			}
			$itemTemplate .= '<td valign="top"><select name="menu_item_link[' . $i . ']" class="menuLinkType">' . $options . '</select></td>';
		}

		$itemTemplate .= '<td valign="top"><span class="ui-icon ui-icon-closethick menuItemDelete" tooltip="Delete Item and Children"></span></td>';
		$itemTemplate .= '</tr></table></div>';

		$i++;
		if (!empty($item->children)){
			foreach($item->children as $childItem){
				$itemTemplate .= '<ol>' . parseMenuItem($childItem, &$i) . '</ol>';
			}
		}

		$itemTemplate .= '</li>';

		return $itemTemplate;
	}

	$menuItems = '';
	if (isset($WidgetSettings->menuSettings)){
		$i = 0;
		foreach($WidgetSettings->menuSettings as $mInfo){
			$menuItems .= parseMenuItem($mInfo, &$i);

			if (empty($mInfo->children)){
				$i++;
			}
		}
	}

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<ol class="ui-widget sortable">' . $menuItems . '</ol>')
		)
	));
}
echo $editTable->draw();
echo '<input type="hidden" name="navMenuSortable" value="">';
if (isset($WidgetSettings->linked_to)){
	echo '<input type="hidden" name="linked_to" value="' . $WidgetSettings->linked_to . '">';
}
$fileContent = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $fileContent)
	)
));
