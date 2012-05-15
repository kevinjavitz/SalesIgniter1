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
