<?php
    if($_POST['start'] != 'undefined'){
	$start = $_POST['start'];
}else{
	$start = 0;
}
$gId = $_POST['gid'];
$numItems = -1;
$spaceBetween = 20;
$groupWidth = 1;
$groupThumbsWidth = 1;
$groupThumbsHeight = 1;
$groupName = '';
$Qgroup = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select * from banner_manager_groups where banner_group_id = "' . $gId . '"');
if (sizeof($Qgroup) > 0){
	foreach($Qgroup as $cInfo){
		$groupName = $cInfo['banner_group_name'];
		$groupWidth = $cInfo['banner_group_width'];
		$groupHeight = $cInfo['banner_group_height'];
		$groupThumbsWidth = $cInfo['banner_group_thumbs_width'];
		$groupThumbsHeight = $cInfo['banner_group_thumbs_height'];
		$numItems = floor($groupWidth / ($groupThumbsWidth + $spaceBetween));
	}
}

mysql_free_result($Qgroup);



$Qproducts = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select * from products LEFT JOIN banner_manager_products_to_groups ON products.products_id=banner_manager_products_to_groups.products_id WHERE banner_manager_products_to_groups.banner_group_id = "' . $gId . '"')or die(mysql_error());
if (sizeof($Qproducts) > 0){
	if (sizeof($Qproducts) <= $start){
		$start = 0;
	}
}

$Qbanner = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select products.products_id, products.products_image from products LEFT JOIN banner_manager_products_to_groups ON products.products_id=banner_manager_products_to_groups.products_id WHERE banner_manager_products_to_groups.banner_group_id = "' . $gId . '" ORDER BY RAND() LIMIT '.$start. ', '.$numItems)or die(mysql_error());

$start += $numItems;
$html = '';
if (sizeof($Qbanner) > 0){
	while($bInfo = $Qbanner[0]){
		$Img = htmlBase::newElement('image')
			->setSource(sysConfig::getDirWsCatalog() . 'images/' . $bInfo['products_image'])
			->setWidth($groupThumbsWidth)
			->setHeight($groupThumbsHeight)
			->thumbnailImage(true)
			->addClass('imgrot' . $groupName);

		$link = itw_app_link('products_id=' . $bInfo['products_id'], 'product', 'info');
		$html .= '<li class="protator'.$groupName.'">' .
			'<a href="' . $link . '">' . $Img->draw() . '</a>' .
			'</li>';
	}
}

EventManager::attachActionResponse(array(
		'success' => true,
		'start'  => $start,
		'html'     => "<ul>".$html."</ul><br style='clear:both;'/>"
	), 'json');

?>