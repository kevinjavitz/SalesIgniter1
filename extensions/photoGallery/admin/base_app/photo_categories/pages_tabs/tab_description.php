<?php
	echo '<ul>';
	foreach(sysLanguage::getLanguages() as $lInfo){
		echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lInfo['id'] . '"><span>' . '&nbsp;' . $lInfo['showName']() . '</span></a></li>';
	}
	echo '</ul>';

function makeCategoriesArray($parentId = 0){
	$catArr = array();
	$Qcategories = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_title as categories_name')
		->from('PhotoGalleryCategories c')
		->leftJoin('c.PhotoGalleryCategoriesDescription cd')
		->where('parent_id = ?', $parentId)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qcategories as $category){
		$catArr[$category['categories_id']] = array(
			'name' => $category['categories_name']
		);

		$Children = makeCategoriesArray($category['categories_id']);
		if (!empty($Children)){
			$catArr[$category['categories_id']]['children'] = $Children;
		}
	}

	return $catArr;
}
$CatArr = makeCategoriesArray(0);



$selectBox = htmlBase::newElement('selectbox')
	->setName('parent_id')
	->selectOptionByValue($Categories->parent_id);

$selectBox->addOption('-1','--Please Select--');
$selectBox->addOption('0','--Root--');

function buildCategoryBoxes($catArr, $sKey, $selectBox){

	$f = '';
	for($i=0;$i<$sKey;$i++){
		$f .= '-';
	}

	foreach($catArr as $id => $cInfo){
		$selectBox->addOption($id, $f . $cInfo['name']);
		if (isset($cInfo['children']) && sizeof($cInfo['children']) > 0){
			buildCategoryBoxes($cInfo['children'], $sKey + 1, $selectBox);
		}
	}
}
buildCategoryBoxes($CatArr, 0, $selectBox);

	$CategoryImage = htmlBase::newElement('uploadManagerInput')
	->setName('categories_image')
	->setFileType('image')
	->autoUpload(true)
	->showPreview(true)
	->showMaxUploadSize(true);


	$zoomIcon = htmlBase::newElement('icon')->setType('zoomIn');
	$deleteIcon = htmlBase::newElement('icon')->setType('closeThick')->addClass('deleteImage');
	$imgSrc =  'images/';
	$thumbSrc = 'imagick_thumb.php?width=80&height=80&imgSrc=' . 'images/';

   $CategoryImage->setPreviewFile($Category->categories_image);
	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];
		$name = ''; $description = '';$seo_url = ''; $htc_title = ''; $htc_desc = ''; $htc_keywords = ''; $htc_descrip = '';
		if (isset($_GET['cID'])){
			$name = $Category->PhotoGalleryCategoriesDescription[$lID]->categories_title;
			$description = $Category->PhotoGalleryCategoriesDescription[$lID]->categories_description_text;
			//$seo_url = $Category->PhotoGalleryCategoriesDescription[$lID]->categories_seo_url;
		}
?>
<div id="langTab_<?php echo $lID;?>">
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_CATEGORIES_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('categories_name[' . $lID . ']', $name); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_DESCRIPTION'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('categories_description[' . $lID . ']', 'hard', 30, 5, $description, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
</table>

</div>
	<?php
       }
?>
   <table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_IMAGE'); ?></td>
		<td class="main"><?php
  echo $CategoryImage->draw();
			?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_SORT_ORDER'); ?></td>
		<td class="main"><?php echo tep_draw_input_field('sort_order', (isset($_GET['cID']) ? $Category->sort_order : ''), 'size="2"');?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_PARENT_CATEGORY'); ?></td>
		<td class="main"><?php echo $selectBox->draw();?></td>
	</tr>
	   <tr>
		   <td class="main">
			   <?php echo sysLanguage::get('TEXT_ADDITIONAL_IMAGES'); ?>
		   </td>
		   <td class="main">
			   <table cellpadding="3" cellspacing="0" border="0">
				   <thead>
				   <tr>
					   <th><b>Additional Images</b></th>
				   </tr>
				   </thead>
				   <tbody>
				   <tr>
					   <td colspan="2"><?php
							$additionalImage = htmlBase::newElement('uploadManagerInput')
						   ->setName('additional_images')
						   ->setFileType('image')
						   ->autoUpload(true)
						   ->showPreview(true)
					       ->hasCaption(true)
					       ->hasDescription(true)
						   ->showMaxUploadSize(true)
						   ->allowMultipleUploads(true);

						   $QImageToCategories = Doctrine_Query::create()
							->from('ImagesToCategories')
							->where('categories_id = ?', $Category->categories_id)
						   ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						   foreach($QImageToCategories as $imgInfo){
							   $additionalImage->setPreviewFile($imgInfo['file_name'], $imgInfo['caption'], $imgInfo['big_caption']);
						   }
						   echo $additionalImage->draw();
						   ?></td>
				   </tr>
				   </tbody>
			   </table>

		   </td>
	   </tr>
   </table>


