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
			$result .= '<li>'.'<a href="#" onclick="return setSelected(this,'. $_POST['cID'] . ',' . $image['images_id'] .')">'.'<img src="'.DIR_WS_CATALOG .'product_thumb.php?w=200&img='.'extensions/productDesigner/images/clipart/'.$image['image'].'" file="' . $image['image'] . '"/></a></li>';
		}
	}

	$result = $result.'</ul>';
}

EventManager::attachActionResponse(array(
	'success' => 'yes',
	'data' => $result
), 'json');
?>