<?php

$imageWidth = isset($WidgetSettings->image_width) ? $WidgetSettings->image_width : '175';
$imageHeight = isset($WidgetSettings->image_height) ? $WidgetSettings->image_height : '175';
$speed = isset($WidgetSettings->speed) ? $WidgetSettings->speed : '500';
$duration = isset($WidgetSettings->duration) ? $WidgetSettings->duration : '3000';
$displayQty = isset($WidgetSettings->displayQty) ? $WidgetSettings->displayQty : '3';
$moveQty = isset($WidgetSettings->moveQty) ? $WidgetSettings->moveQty : '3';
$widgetId = isset($WidgetSettings->widgetId) ? $WidgetSettings->widgetId : '';

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
$checkedVideo = '';
$checkedVideoImage = '';
$checkedDate = '';
$checkedReadMore = '';
$checkedDesc = '';
$descLength = '200';
$imageHasLink = '';
$startAuto = '';
$checkedTitle = '';
if (isset($WidgetSettings->showImage) && $WidgetSettings->showImage == 'showImage'){
	$checkedImage = 'checked="checked"';
}
if (isset($WidgetSettings->showTitle) && $WidgetSettings->showTitle == 'showTitle'){
	$checkedTitle = 'checked="checked"';
}

if (isset($WidgetSettings->showVideo) && $WidgetSettings->showVideo == 'showVideo'){
	$checkedVideo = 'checked="checked"';
}
if (isset($WidgetSettings->showVideoImage) && $WidgetSettings->showVideoImage == 'showVideoImage'){
	$checkedVideoImage = 'checked="checked"';
}

if (isset($WidgetSettings->showImage) && $WidgetSettings->showImage == 'showImage'){
	$checkedImage = 'checked="checked"';
}


if (isset($WidgetSettings->showReadMore) && $WidgetSettings->showReadMore == 'showReadMore'){
	$checkedReadMore = 'checked="checked"';
}
if (isset($WidgetSettings->showDate) && $WidgetSettings->showDate == 'showDate'){
	$checkedDate = 'checked="checked"';
}
if (isset($WidgetSettings->showDesc) && $WidgetSettings->showDesc == 'showDesc'){
	$checkedDesc = 'checked="checked"';
}
if (isset($WidgetSettings->imageHasLink) && $WidgetSettings->imageHasLink == 'imageHasLink'){
	$imageHasLink = 'checked="checked"';
}
if (isset($WidgetSettings->startAuto) && $WidgetSettings->startAuto == 'startAuto'){
	$startAuto = 'checked="checked"';
}

if (isset($WidgetSettings->descLength) && !empty($WidgetSettings->descLength)){
	$descLength = $WidgetSettings->descLength;
}

$easing = '<select name="easing"><option value="swing">swing</option><option value="easeInQuad">easeInQuad</option><option value="easeOutQuad">easeOutQuad</option><option value="easeInOutQuad">easeInOutQuad</option><option value="easeInCubic">easeInCubic</option><option value="easeOutCubic">easeOutCubic</option><option value="easeInOutCubic">easeInOutCubic</option><option value="easeInQuart">easeInQuart</option><option value="easeOutQuart">easeOutQuart</option><option value="easeInOutQuart">easeInOutQuart</option><option value="easeInQuint">easeInQuint</option><option value="easeOutQuint">easeOutQuint</option><option value="easeInOutQuint">easeInOutQuint</option><option value="easeInSine">easeInSine</option><option value="easeOutSine">easeOutSine</option><option value="easeInOutSine">easeInOutSine</option><option value="easeInExpo">easeInExpo</option><option value="easeOutExpo">easeOutExpo</option><option value="easeInOutExpo">easeInOutExpo</option><option value="easeInCirc">easeInCirc</option><option value="easeOutCirc">easeOutCirc</option><option value="easeInOutCirc">easeInOutCirc</option><option value="easeInElastic">easeInElastic</option><option value="easeOutElastic">easeOutElastic</option><option value="easeInOutElastic">easeInOutElastic</option><option value="easeInBack">easeInBack</option><option value="easeOutBack">easeOutBack</option><option value="easeInOutBack">easeInOutBack</option><option value="easeInBounce">easeInBounce</option><option value="easeOutBounce">easeOutBounce</option><option value="easeInOutBounce">easeInOutBounce</option></select>';

ob_start();
?>
<fieldset>
	<legend>Blog Latest Articles Configuration</legend>

	<table cellpadding="0" cellspacing="0" border="0" class="scrollerConfig">
		<tr>
			<td>Widget ID(required):</td>
			<td><input type="text" name="widgetId" value="<?php echo $widgetId;?>"></td>
		</tr>
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
			<td>Show Video:</td>
			<td><input type="checkbox" name="showVideo" value="showVideo" <?php echo $checkedVideo;?>></td>
		</tr>
		<tr>
			<td>Show Video Image:</td>
			<td><input type="checkbox" name="showVideoImage" value="showVideoImage" <?php echo $checkedVideoImage;?>></td>
		</tr>
		<tr>
			<td>Show Date:</td>
			<td><input type="checkbox" name="showDate" value="showDate" <?php echo $checkedDate;?>></td>
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
			<td>Start auto:</td>
			<td><input type="checkbox" name="startAuto" value="startAuto" <?php echo $startAuto;?>></td>
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
$Fieldset = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Blog Slide Articles</b>')
	)
));


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $Fieldset)
	)
));
