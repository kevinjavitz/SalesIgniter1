<?php

$imageWidth = isset($WidgetSettings->image_width) ? $WidgetSettings->image_width : '175';
$imageHeight = isset($WidgetSettings->image_height) ? $WidgetSettings->image_height : '175';

$new_selected_category = isset($WidgetSettings->new_selected_category) ? $WidgetSettings->new_selected_category : '-1';
//can add category from where to pull articles

function getCategoryTree($parentId, $namePrefix = '', &$categoriesTree){
	global $lID, $allGetParams, $cInfo;
	$Qcategories = Doctrine_Query::create()
		->select('c.*, cd.blog_categories_title')
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('cd.language_id = ?', (int)Session::get('languages_id'))
		->andWhere('c.parent_id = ?', $parentId)
		->orderBy('c.sort_order, cd.blog_categories_title');

	$Result = $Qcategories->execute();
	if ($Result->count() > 0){
		foreach($Result->toArray(true) as $Category){
			if ($Category['parent_id'] > 0){
				//$namePrefix .= '&nbsp;';
			}

			$categoriesTree[] = array(
				'categoryId'           => $Category['blog_categories_id'],
				'categoryName'         => $namePrefix . $Category['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'],
			);

			getCategoryTree($Category['blog_categories_id'], '&nbsp;&nbsp;&nbsp;' . $namePrefix, &$categoriesTree);
		}
	}
}

$categoryTreeList = false;
getCategoryTree(0,'',&$categoryTreeList);
$categoryTreeNew = htmlBase::newElement('selectbox')
	->setName('new_selected_category')
	->setId('new_selected_category');
$categoryTreeNew->addOption('-1', '--select--');
foreach($categoryTreeList as $category){
	$categoryTreeNew->addOption($category['categoryId'], $category['categoryName']);
}
$categoryTreeNew->selectOptionByValue($new_selected_category);

$checkedImage = '';
$checkedReadMore = '';
$checkedDesc = '';
$descLength = '200';
$imageHasLink = '';
$checkedTitle = '';
if (isset($WidgetSettings->showImage) && $WidgetSettings->showImage == 'showImage'){
	$checkedImage = 'checked="checked"';
}

if (isset($WidgetSettings->showReadMore) && $WidgetSettings->showReadMore == 'showReadMore'){
	$checkedReadMore = 'checked="checked"';
}

if (isset($WidgetSettings->showDesc) && $WidgetSettings->showDesc == 'showDesc'){
	$checkedDesc = 'checked="checked"';
}

if (isset($WidgetSettings->imageHasLink) && $WidgetSettings->imageHasLink == 'imageHasLink'){
	$imageHasLink = 'checked="checked"';
}

if (isset($WidgetSettings->showTitle) && $WidgetSettings->showTitle == 'showTitle'){
	$checkedTitle = 'checked="checked"';
}

if (isset($WidgetSettings->descLength) && !empty($WidgetSettings->descLength)){
	$descLength = $WidgetSettings->descLength;
}
ob_start();
?>
<fieldset>
	<legend>Blog Subcategories Configuration</legend>

	<table cellpadding="0" cellspacing="0" border="0" class="scrollerConfig">
		<tr>
			<td>Show From Category:</td>
			<td><?php echo $categoryTreeNew->draw();?></td>
		</tr>
		<tr>
			<td>Show Image:</td>
			<td><input type="checkbox" name="showImage" value="showImage" <?php echo $checkedImage;?>></td>
		</tr>
		<tr>
			<td>Show Title:</td>
			<td><input type="checkbox" name="showTitle" value="showTitle" <?php echo $checkedTitle;?>></td>
		</tr>
		<tr>
			<td>Show Read More:</td>
			<td><input type="checkbox" name="showReadMore" value="showReadMore" <?php echo $checkedReadMore;?>></td>
		</tr>
		<tr>
			<td>Image Has Link:</td>
			<td><input type="checkbox" name="imageHasLink" value="imageHasLink" <?php echo $imageHasLink;?>></td>
		</tr>
		<tr>
			<td>Show Description:</td>
			<td><input type="checkbox" name="showDesc" value="showDesc" <?php echo $checkedDesc;?>></td>
		</tr>
		<tr>
			<td>Description Length:</td>
			<td><input type="text" name="descLength" value="<?php echo $descLength;?>"></td>
		</tr>
		<tr>
			<td>Image Width:</td>
			<td><input type="text" name="imageWidth" value="<?php echo $imageWidth;?>"></td>
		</tr>
		<tr>
			<td>Image Height:</td>
			<td><input type="text" name="imageHeight" value="<?php echo $imageHeight;?>"></td>
		</tr>
	</table>
</fieldset>
<?php
$Fieldset = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Blog Subcategories</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $Fieldset)
	)
));
