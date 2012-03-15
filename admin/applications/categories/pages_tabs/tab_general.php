<?php

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


?>
<table cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_IMAGE'); ?></td>
   <td class="main"><?php
    $categories_image = htmlBase::newElement('uploadManagerInput')
	   ->setName('categories_image')
	   ->setFileType('image')
	   ->autoUpload(true)
	   ->showPreview(true)
	   ->showMaxUploadSize(true)
	   ->allowMultipleUploads(false);

	   $categories_image->setPreviewFile($Category->categories_image);
	   echo $categories_image->draw();
   ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <?php if (!isset($cPath_array) || sizeof($cPath_array) <= 0){ ?>
  <tr>
   <td class="main" valign="top"><?php echo 'Show In Menu:'; ?></td>
   <td class="main"><?php
    $menuSet = htmlBase::newElement('radio')
    ->addGroup(array(
		'name'      => 'categories_menu',
		'checked'   => (isset($_GET['cID']) ? $Category->categories_menu : 'both'),
		'data'      => array(
			array('label' => 'Top Menu', 'value' => 'top', 'labelPosition' => 'after'),
			array('label' => 'Infobox Menu', 'value' => 'infobox', 'labelPosition' => 'after'),
			array('label' => 'Both', 'value' => 'both', 'labelPosition' => 'after')
		),
		'separator' => '<br />'
	));
    echo $menuSet->draw();
   ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <?php } ?>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SORT_ORDER'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('sort_order', (isset($_GET['cID']) ? $Category->sort_order : ''), 'size="2"');?></td>
  </tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_PARENT_CATEGORY'); ?></td>
		<td class="main"><?php echo $selectBox->draw();?></td>
	</tr>
 </table>