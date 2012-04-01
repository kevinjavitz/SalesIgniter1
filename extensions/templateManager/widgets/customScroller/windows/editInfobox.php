<?php
ob_start();
function getCategoryTree($parentId, $namePrefix = '', &$categoriesTree){
    global $lID, $allGetParams, $cInfo;
    $Qcategories = Doctrine_Query::create()
            ->select('c.*, cd.categories_name')
            ->from('Categories c')
            ->leftJoin('c.CategoriesDescription cd')
            ->where('cd.language_id = ?', (int)Session::get('languages_id'))
            ->andWhere('c.parent_id = ?', $parentId)
            ->orderBy('c.sort_order, cd.categories_name');

    EventManager::notify('CategoryListingQueryBeforeExecute', &$Qcategories);

    $Result = $Qcategories->execute();
    if ($Result->count() > 0){
        foreach($Result->toArray(true) as $Category){
            if ($Category['parent_id'] > 0){
                //$namePrefix .= '&nbsp;';
            }

            $categoriesTree[] = array(
                'categoryId'           => $Category['categories_id'],
                'categoryName'         => $namePrefix . $Category['CategoriesDescription'][Session::get('languages_id')]['categories_name'],
            );

            getCategoryTree($Category['categories_id'], '&nbsp;&nbsp;&nbsp;' . $namePrefix, &$categoriesTree);
        }
    }
}
$scrollerQueryTypes = array(
	'best_sellers' => 'Best Selling Products',
	'featured' => 'Featured Products',
	'new_products' => 'New Products',
	'top_rentals' => 'Top Rented Products',
	'specials' => 'Specials Products',
	'related' => 'Current Product Related Products',
	'category' => 'Current Category Products',
    'category_featured' => 'Featured Products From Selected Category'
);

$scrollerTypes = array(
	'stack' => 'Stacked',
	'tabs' => 'Tabs',
	'buttons' => 'Tabs As Buttons'
);
?>
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

	li .ui-icon-closethick, li .ui-icon-pencil {
		float : right; /*margin:.3em;*/
	}

	li select {
		margin-left  : .5em;
		margin-right : .5em;
	}

	table.scrollerConfig, .configTable table {
		width : auto;
	}

	table.scrollerConfig td, .configTable table td {
		padding : 0;
	}

	.configTable {
		display : none;
	}
</style>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/nestedSortable/jquery.ui.nestedSortable.js"></script>
<script>
	var scrollerQueries = <?php echo json_encode($scrollerQueryTypes);?>;
	var languageId = <?php echo Session::get('languages_id');?>;

	$(document).ready(function () {
		$('.scrollerSortable').nestedSortable({
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

        $('select[name="new_scroller_query"]').change(function (){
            if($(this).val() == 'category_featured') {
                $('select[name="new_selected_category"]').parent().parent().show();
            } else {
                $('select[name="new_selected_category"]').parent().parent().hide();
            }
        });
        $('select[name="new_scroller_query"]').trigger('change');

		$('.addScroller').click(function () {
			var inputKey = $('ol.scrollerSortable > li').size();

			var $configTable = $('.scrollerConfig').clone();
			var langHeading = '';
			$configTable.find('input, select').each(function () {
				var newName = $(this).attr('name');
				newName = newName.substr(4, newName.length);
				if ($(this).attr('name').substr(0, 20) == 'new_scroller_heading'){
					var langId = newName.substr(newName.lastIndexOf('_') + 1);
					newName = newName.substr(0, newName.lastIndexOf('_'));
					newName += '[' + inputKey + '][' + langId + ']';
					if (langId == languageId){
						langHeading = $(this).val();
					}
				}
				else {
					newName += '[' + inputKey + ']';
				}

				$(this).attr('name', newName);
			});
			$configTable.removeClass('scrollerConfig');

			$('.scrollerConfig').find('input, select').each(function () {
				$(this).val('');
			});

			var $li = $('<li id="scroller_config_' + inputKey + '" data-input_key="' + inputKey + '">' +
				'<div>' +
				'<span class="ui-icon ui-icon-closethick scrollerDelete" tooltip="Delete Scroller"></span>' +
				'<span class="langHeading">' + langHeading + '</span><br>' +
				'<span class="tableExpander">[ Show Settings ]</span><br>' +
				'<div class="configTable" style="font-size:.8em;">' +
				'</div>' +
				'</div>' +
				'</li>');
			$li.find('.configTable').append($configTable);

			$('.scrollerSortable').append($li);
            $li.find('select[name="scroller_query[' + inputKey + ']"]').change(function (){
                var thisName = $(this).attr('name');
                thisName = (thisName.substr(thisName.indexOf('['), thisName.lastIndexOf(']')));
                if($(this).val() == 'category_featured') {
                    $('select[name="selected_category' + thisName + '"]').parent().parent().show();
                } else {
                    $('select[name="selected_category' + thisName + '"]').parent().parent().hide();
                }
            });
            $li.find('select[name="scroller_query[' + inputKey + ']"]').trigger('change');
			$li.find('input[name="scroller_heading[' + inputKey + '][' + languageId + ']"]').keyup(function () {
				$(this).parent().parent().parent().parent().parent().parent().find('.langHeading').html($(this).val());
			});

			$li.find('.tableExpander').click(
				function () {
					if (!$(this).hasClass('showing')){
						$(this).parent().find('.configTable').show();
						$(this).addClass('showing').html('[ Hide Settings ]');
					}
					else {
						$(this).parent().find('.configTable').hide();
						$(this).removeClass('showing').html('[ Show Settings ]');
					}
				}).mouseover(
				function () {
					this.style.cursor = 'pointer';
				}).mouseout(function () {
				this.style.cursor = 'default';
			});
		});

        var inputKey = $('ol.scrollerSortable > li').size();
        for(var i=0; i < inputKey; i++){
            $('select[name="scroller_query\\[' + i + '\\]"]').change(function (){
                var thisName = $(this).attr('name');
                thisName = (thisName.substr(thisName.indexOf('['), thisName.lastIndexOf(']')));
                if($(this).val() == 'category_featured') {
                    $('select[name="selected_category' + thisName + '"]').parent().parent().show();
                } else {
                    $('select[name="selected_category' + thisName + '"]').parent().parent().hide();
                }
            });
            $('select[name="scroller_query\\[' + i + '\\]"]').trigger('change');
        }

		$('.tableExpander').click(
			function () {
				if (!$(this).hasClass('showing')){
					$(this).parent().find('.configTable').show();
					$(this).addClass('showing').html('[ Hide Settings ]');
				}
				else {
					$(this).parent().find('.configTable').hide();
					$(this).removeClass('showing').html('[ Show Settings ]');

				}
			}).mouseover(
			function () {
				this.style.cursor = 'pointer';
			}).mouseout(function () {
			this.style.cursor = 'default';
		});

		$('.headingInput').keyup(function () {
			$(this).parent().parent().parent().parent().parent().parent().find('.langHeading').html($(this).val());
		});

		$('.scrollerDelete').live('click', function () {
			$(this).parent().parent().remove();
		});

		$('.saveButton').click(function () {
			$('input[name=scrollerSortable]').val($('#scrollerBuilderTable').find('ol.sortable')
				.nestedSortable('serialize'));
		});
	});
</script>
<?php
$scrollerType = '';
if (isset($WidgetSettings->scrollers)){
	$scrollerType = $WidgetSettings->scrollers->type;
}

$scrollerQueryOptions = '';
foreach($scrollerQueryTypes as $k => $v){
	$scrollerQueryOptions .= '<option value="' . $k . '">' . $v . '</option>';
}

$scrollerTypeOptions = '';
foreach($scrollerTypes as $k => $v){
	$scrollerTypeOptions .= '<option value="' . $k . '"' . ($scrollerType == $k ? ' selected' : '') . '>' . $v . '</option>';
}

$speed = isset($WidgetSettings->scrollers->speed) ? $WidgetSettings->scrollers->speed : '500';
$duration = isset($WidgetSettings->scrollers->duration) ? $WidgetSettings->scrollers->duration : '3000';
$displayQty = isset($WidgetSettings->scrollers->displayQty) ? $WidgetSettings->scrollers->displayQty : 'auto';
$moveQty = isset($WidgetSettings->scrollers->moveQty) ? $WidgetSettings->scrollers->moveQty : 'auto';
$easing = '<select name="easing"><option value="swing">swing</option><option value="easeInQuad">easeInQuad</option><option value="easeOutQuad">easeOutQuad</option><option value="easeInOutQuad">easeInOutQuad</option><option value="easeInCubic">easeInCubic</option><option value="easeOutCubic">easeOutCubic</option><option value="easeInOutCubic">easeInOutCubic</option><option value="easeInQuart">easeInQuart</option><option value="easeOutQuart">easeOutQuart</option><option value="easeInOutQuart">easeInOutQuart</option><option value="easeInQuint">easeInQuint</option><option value="easeOutQuint">easeOutQuint</option><option value="easeInOutQuint">easeInOutQuint</option><option value="easeInSine">easeInSine</option><option value="easeOutSine">easeOutSine</option><option value="easeInOutSine">easeInOutSine</option><option value="easeInExpo">easeInExpo</option><option value="easeOutExpo">easeOutExpo</option><option value="easeInOutExpo">easeInOutExpo</option><option value="easeInCirc">easeInCirc</option><option value="easeOutCirc">easeOutCirc</option><option value="easeInOutCirc">easeInOutCirc</option><option value="easeInElastic">easeInElastic</option><option value="easeOutElastic">easeOutElastic</option><option value="easeInOutElastic">easeInOutElastic</option><option value="easeInBack">easeInBack</option><option value="easeOutBack">easeOutBack</option><option value="easeInOutBack">easeInOutBack</option><option value="easeInBounce">easeInBounce</option><option value="easeOutBounce">easeOutBounce</option><option value="easeInOutBounce">easeInOutBounce</option></select>';


$editTable = htmlBase::newElement('table')
	->setId('scrollerBuilderTable')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => '<b>Module Configuration</b>')
	)
));

$editTable->addBodyRow(array(
	'columns' => array(
		array('text' => '<b>Interface: </b><select name="scroller_type">' . $scrollerTypeOptions . '</select>')
	)
));
    $tableData = '<table>'.
	'<tr>' .
	'<td>Display Items: </td>' .
	'<td><input type="text" name="displayQty" value="' . $displayQty . '" size="4"></td>' .
	'</tr>' .
	'<tr>' .
	'<td>Move Items: </td>' .
	'<td><input type="text" name="moveQty" value="' . $moveQty . '" size="4"></td>' .
	'</tr>' .
	'<tr>' .
	'<td>Speed: </td>' .
	'<td><input type="text" name="speed" value="' . $speed . '" size="4"></td>' .
	'</tr>' .
	'<tr>' .
	'<td>Duration: </td>' .
	'<td><input type="text" name="duration" value="' . $duration . '" size="4"></td>' .
	'</tr>' .
	'<tr>' .
	'<td>Easing: </td>' .
	'<td>'.$easing.'</td>' .
	'</tr>' .
	'<tr>' .
	'<td>Auto Start: </td>' .
	'<td><input type="checkbox" name="scroller_autostart" value="1"' . ((isset($WidgetSettings->scrollers->autostart)?($WidgetSettings->scrollers->autostart=='autostart'?true:false):true) === true ? ' checked=checked' : '') . '></td>' .
	'</tr>' .
	'</table>';

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $tableData)
		)
	));

echo $editTable->draw();

$headingInputs = '';
foreach(sysLanguage::getLanguages() as $lInfo){
	$headingInputs .= $lInfo['showName']('&nbsp;') . ': <input type="text" name="new_scroller_heading_' . $lInfo['id'] . '"><br>';
}
$categoryTreeList = false;
getCategoryTree(0,'',&$categoryTreeList);
$categoryTreeNew = htmlBase::newElement('selectbox')
        ->setName('new_selected_category')
        ->setId('new_selected_category');
$categoryTreeNew->addOption('', '--select--');
foreach($categoryTreeList as $category){
    $categoryTreeNew->addOption($category['categoryId'], $category['categoryName']);
}


?>
<fieldset>
	<legend>New Scroller Configuration</legend>

	<table cellpadding="0" cellspacing="0" border="0" class="scrollerConfig">
		<tr>
			<td valign="top">Heading:</td>
			<td><?php echo $headingInputs;?></td>
		</tr>
		<tr>
			<td>Scroller Type:</td>
			<td><select name="new_scroller_query" class="scrollerQuery"><?php echo $scrollerQueryOptions;?></select>
			</td>
		</tr>


        <tr>
            <td>Show featured products from selected category:</td>
            <td><?php
                echo $categoryTreeNew->draw();
                ?>
            </td>
        </tr>
		<tr>
			<td>Scroller Rows:</td>
			<td><input type="text" name="new_scroller_rows" value="1" size="3"></td>
		</tr>
		<tr>
			<td>Limit Query Results:</td>
			<td><input type="text" name="new_scroller_query_limit" value="25" size="3"> 0 for no limit</td>
		</tr>
		<tr>
			<td>Show Product Name:</td>
			<td><input type="checkbox" name="new_scroller_show_product_name" value="1"></td>
		</tr>
		<tr>
			<td>Reflect Blocks:</td>
			<td><input type="checkbox" name="new_scroller_block_reflect" value="1"></td>
		</tr>
		<tr>
			<td>Block Width:</td>
			<td><input type="text" name="new_scroller_block_width" value="200" size="4"> In Pixels</td>
		</tr>
		<tr>
			<td>Block Height:</td>
			<td><input type="text" name="new_scroller_block_height" value="200" size="4"> In Pixels</td>
		</tr>
		<tr>
			<td>Previous Scroll Image:</td>
			<td>
				<input type="text" name="new_scroller_prev_image" class="BrowseServerField">
			</td>
		</tr>
		<tr>
			<td>Next Scroll Image:</td>
			<td>
				<input type="text" name="new_scroller_next_image" class="BrowseServerField">
			</td>
		</tr>
	</table>
</fieldset>
<span class="ui-icon ui-icon-plusthick addScroller"></span> Add Scroller
<ol class="ui-widget scrollerSortable sortable"><?php
	if (isset($WidgetSettings->scrollers)){
	foreach($WidgetSettings->scrollers->configs as $i => $cInfo){
		$scrollerQuery = $cInfo->query;

		$headingInputs = '';
		$langHeading = '';
		foreach(sysLanguage::getLanguages() as $lInfo){
			$headingInputs .= $lInfo['showName']('&nbsp;') . ': <input type="text" name="scroller_heading[' . $i . '][' . $lInfo['id'] . ']" class="headingInput" value="' . $cInfo->headings->$lInfo['id'] . '"><br>';

			if ($lInfo['id'] == Session::get('languages_id')){
				$langHeading = $cInfo->headings->$lInfo['id'];
			}
		}

		$scrollerQueryOptions = '';
		foreach($scrollerQueryTypes as $k => $v){
			$scrollerQueryOptions .= '<option value="' . $k . '"' . ($k == $cInfo->query ? ' selected' : '') . '>' . $v . '</option>';
		}

        $selectedCategory = isset($cInfo->selected_category) ? $cInfo->selected_category : '';

        $categoryTree = htmlBase::newElement('selectbox')
                ->setName('selected_category[' . $i . ']');
        $categoryTree->addOption('', '--select--');
        foreach($categoryTreeList as $category){
            $categoryTree->addOption($category['categoryId'], $category['categoryName']);
        }
        if(isset($selectedCategory)){
            $categoryTree->selectOptionByValue($selectedCategory);
        }

		$imgPrev = fixImagesPath($cInfo->prev_image);
		$imgNext = fixImagesPath($cInfo->next_image);

		echo '<li id="scroller_config_' . $i . '" data-input_key="' . $i . '">' .
			'<div>' .
			'<span class="ui-icon ui-icon-closethick scrollerDelete" tooltip="Delete Scroller"></span>' .
			'<span class="langHeading">' . $langHeading . '</span><br>' .
			'<span class="tableExpander">[ Show Settings ]</span><br>' .
			'<div class="configTable" style="font-size:.8em;">' .
			'<table cellpadding="0" cellspacing="0" border="0">' .
			'<tr>' .
			'<td valign="top">Heading: </td>' .
			'<td>' . $headingInputs . '</td>' .
			'</tr>' .
			'<tr>' .
			'<td>Scroller Type: </td>' .
			'<td><select name="scroller_query[' . $i . ']" class="scrollerQuery">' . $scrollerQueryOptions . '</select></td>' .
			'</tr>' .
            '<tr>' .
            '<td>Show featured products from selected category: </td>' .
            '<td>' .
             $categoryTree->draw() .
             '</td>' .
            '</tr>' .
			'<tr>' .
			'<td>Scroller Rows:</td>' .
			'<td><input type="text" name="scroller_rows[' . $i . ']" value="' . (!isset($cInfo->rows) ? 1 : $cInfo->rows) . '" size="3"></td>' .
			'</tr>' .
			'<tr>' .
			'<td>Limit Query Results: </td>' .
			'<td><input type="text" name="scroller_query_limit[' . $i . ']" size="3" value="' . $cInfo->query_limit . '"> 0 for no limit</td>' .
			'</tr>' .
			'<tr>' .
			'<td>Block Width: </td>' .
			'<td><input type="text" name="scroller_block_width[' . $i . ']" value="' . $cInfo->block_width . '" size="4"> In Pixels</td>' .
			'</tr>' .
			'<tr>' .
			'<td>Block Height: </td>' .
			'<td><input type="text" name="scroller_block_height[' . $i . ']" value="' . $cInfo->block_height . '" size="4"> In Pixels</td>' .
			'</tr>' .
			'<tr>' .
			'<td>Show Product Name: </td>' .
			'<td><input type="checkbox" name="scroller_show_product_name[' . $i . ']" value="1"' . ((isset($cInfo->show_product_name)?$cInfo->show_product_name:false) === true ? ' checked=checked' : '') . '></td>' .
			'</tr>' .
			'<tr>' .
			'<td>Reflect Blocks: </td>' .
			'<td><input type="checkbox" name="scroller_block_reflect[' . $i . ']" value="1"' . ($cInfo->reflect_blocks === true ? ' checked=checked' : '') . '></td>' .
			'</tr>' .
			'<tr>' .
			'<td>Previous Scroll Image: </td>' .
			'<td><input type="text" name="scroller_prev_image[' . $i . ']" class="BrowseServerField" value="' . $imgPrev . '"></td>' .
			'</tr>' .
			'<tr>' .
			'<td>Next Scroll Image: </td>' .
			'<td><input type="text" name="scroller_next_image[' . $i . ']" class="BrowseServerField" value="' . $imgNext . '"></td>' .
			'</tr>' .
			'</table>' .
			'</div>' .
			'</div>' .
			'</li>';
	}
}
	?></ol>

<?php
$fileContent = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $fileContent)
	)
));
?>