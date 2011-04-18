<?php
if (isset($_POST['cID'])) {
	$cat = (int)$_POST['cID'];
	$Qimages = Doctrine_Query::create()
	->select('cd.*')
	->from('ProductDesignerClipartImages cd')
	->leftJoin('cd.ProductDesignerClipartImagesToCategories c')
	->where('c.categories_id = ?', $cat )
	->execute();
	if ($Qimages) {
		$result = '<ul class="cimages">';
		foreach ($Qimages as $image) {
			$src = 'imagick_thumb.php?width=200&height=200&imgSrc=' . sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/clipart/';
			$result .= '<li>'.'<a href="#" onclick="return setSelected(this,'. $_POST['cID'] . ',' . $image['images_id'] .')">'.'<img src="' . $src . $image['image'] . '" file="' . $image['image'] . '"/></a></li>';
		}
	}

	$result = $result.'</ul>';
}

EventManager::attachActionResponse(array(
	'success' => 'yes',
	'data' => $result
), 'json');
?>