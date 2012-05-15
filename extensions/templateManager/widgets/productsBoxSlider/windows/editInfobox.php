<?php
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

$productsBoxQueryTypes = array(
		'best_sellers' => 'Best Selling Products',
		'featured' => 'Featured Products',
		'new_products' => 'New Products',
		'top_rentals' => 'Top Rented Products',
		'specials' => 'Specials Products',
		'category_featured' => 'Featured products from specific Category',
		'related' => 'Current Product Related Products',
		'category' => 'Current Category Products'
	);

	$productsBoxId = '';
	if (isset($WidgetSettings->id)){
		$productsBoxId = $WidgetSettings->id;
	}
	
	$productsBoxQuery = 'best_sellers';
	$productsBoxQueryLimit = '25';
	$productsBoxReflect = false;
	$productsBoxBlockWidth = 200;
	$productsBoxBlockHeight = 200;
	if (isset($WidgetSettings->config)){
		$productsBoxQuery = $WidgetSettings->config->query;
		$productsBoxQueryLimit = $WidgetSettings->config->query_limit;
		$productsBoxReflect = $WidgetSettings->config->reflect_blocks;
		$productsBoxBlockWidth = $WidgetSettings->config->block_width;
		$productsBoxBlockHeight = $WidgetSettings->config->block_height;
		if(isset($WidgetSettings->config->selected_category)){
			$categorySelected = $WidgetSettings->config->selected_category;
		}
	}

$speed = isset($WidgetSettings->speed) ? $WidgetSettings->speed : '500';
$duration = isset($WidgetSettings->duration) ? $WidgetSettings->duration : '3000';
$displayQty = isset($WidgetSettings->displayQty) ? $WidgetSettings->displayQty : '3';
$moveQty = isset($WidgetSettings->moveQty) ? $WidgetSettings->moveQty : '3';
$easing = '<select name="easing"><option value="swing">swing</option><option value="easeInQuad">easeInQuad</option><option value="easeOutQuad">easeOutQuad</option><option value="easeInOutQuad">easeInOutQuad</option><option value="easeInCubic">easeInCubic</option><option value="easeOutCubic">easeOutCubic</option><option value="easeInOutCubic">easeInOutCubic</option><option value="easeInQuart">easeInQuart</option><option value="easeOutQuart">easeOutQuart</option><option value="easeInOutQuart">easeInOutQuart</option><option value="easeInQuint">easeInQuint</option><option value="easeOutQuint">easeOutQuint</option><option value="easeInOutQuint">easeInOutQuint</option><option value="easeInSine">easeInSine</option><option value="easeOutSine">easeOutSine</option><option value="easeInOutSine">easeInOutSine</option><option value="easeInExpo">easeInExpo</option><option value="easeOutExpo">easeOutExpo</option><option value="easeInOutExpo">easeInOutExpo</option><option value="easeInCirc">easeInCirc</option><option value="easeOutCirc">easeOutCirc</option><option value="easeInOutCirc">easeInOutCirc</option><option value="easeInElastic">easeInElastic</option><option value="easeOutElastic">easeOutElastic</option><option value="easeInOutElastic">easeInOutElastic</option><option value="easeInBack">easeInBack</option><option value="easeOutBack">easeOutBack</option><option value="easeInOutBack">easeInOutBack</option><option value="easeInBounce">easeInBounce</option><option value="easeOutBounce">easeOutBounce</option><option value="easeInOutBounce">easeInOutBounce</option></select>';
	$productsBoxQueryOptions = '';
	foreach($productsBoxQueryTypes as $k => $v){
		$productsBoxQueryOptions .= '<option value="' . $k . '"' . ($productsBoxQuery == $k ? ' selected' : '') . '>' . $v . '</option>';
	}
	
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
			array('text' => '<b>ID For css: </b><input type="text" name="products_box_id" value="' . $productsBoxId . '">')
		)
	));

	ob_start();

	echo $editTable->draw();
$categoryTreeList = false;
getCategoryTree(0,'',&$categoryTreeList);
$categoryTreeNew = htmlBase::newElement('selectbox')
	->setName('new_selected_category')
	->setId('new_selected_category');
$categoryTreeNew->addOption('-1', '--select--');
foreach($categoryTreeList as $category){
	$categoryTreeNew->addOption($category['categoryId'], $category['categoryName']);
}
if(isset($categorySelected)){
	$categoryTreeNew->selectOptionByValue($categorySelected);
}
?>
<fieldset>
	<legend>Box Configuration</legend>
	
	<table cellpadding="0" cellspacing="0" border="0" class="scrollerConfig">
		<tr>
			<td>Data Type: </td>
			<td><select name="products_box_query"><?php echo $productsBoxQueryOptions;?></select></td>
		</tr>
		<tr>
			<td>Limit Query Results: </td>
			<td><input type="text" name="products_box_query_limit" value="<?php echo $productsBoxQueryLimit;?>" size="3"> 0 for no limit</td>
		</tr>
		<tr>
			<td>Reflect Blocks: </td>
			<td><input type="checkbox" name="products_box_block_reflect" value="1"<?php echo ($productsBoxReflect === true ? ' checked=checked' : '');?>></td>
		</tr>
		<tr>
			<td>Block Width: </td>
			<td><input type="text" name="products_box_block_width" value="<?php echo $productsBoxBlockWidth;?>" size="4"> In Pixels</td>
		</tr>
		<tr>
			<td>Block Height: </td>
			<td><input type="text" name="products_box_block_height" value="<?php echo $productsBoxBlockHeight;?>" size="4"> In Pixels</td>
		</tr>
		<tr>
			<td>Show featured products from selected category:</td>
			<td><?php
                echo $categoryTreeNew->draw();
				?>
			</td>
		</tr>
		<tr>
			<td>Display Items:</td>
			<td><input type="text" name="displayQty" value="<?php echo $displayQty;?>"></td>
		</tr>
		<tr>
			<td>Move Items:</td>
			<td><input type="text" name="moveQty" value="<?php echo $moveQty;?>"></td>
		</tr>
		<tr>
			<td>Speed:</td>
			<td><input type="text" name="speed" value="<?php echo $speed;?>"></td>
		</tr>
		<tr>
			<td>Duration:</td>
			<td><input type="text" name="duration" value="<?php echo $duration;?>"></td>
		</tr>
		<tr>
			<td>Easing:</td>
			<td><?php echo $easing;?></td>
		</tr>
	</table>
</fieldset>
<?php
$editContent = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $editContent)
	)
));
