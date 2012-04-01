<?php
function parseItemLink($itemId) {
	if ($_POST['image_link'][$itemId] == 'app'){
		$itemLink = array(
			'type' => 'app',
			'application' => $_POST['image_link_app'][$itemId],
			'page' => $_POST['image_link_app_page'][$itemId],
			'target' => $_POST['image_link_app_target'][$itemId]
		);
	}
	elseif ($_POST['image_link'][$itemId] == 'category'){
		$catPath = $_POST['image_link_category'][$itemId];
		if (isset($_POST['image_link_category_path'][$itemId])){
			foreach($_POST['image_link_category_path'][$itemId] as $id){
				if ($id == 'none') break;
				$catPath .= '_' . $id;
			}
		}
		$itemLink = array(
			'type' => 'category',
			'application' => 'index',
			'page' => 'default',
			'target' => $_POST['image_link_category_target'][$itemId],
			'get_vars' => 'cPath=' . $catPath
		);
	}
	elseif ($_POST['image_link'][$itemId] == 'custom') {
		$itemLink = array(
			'type' => 'custom',
			'url' => $_POST['image_link_custom'][$itemId],
			'target' => $_POST['image_link_custom_target'][$itemId]
		);
	}
	else {
		$itemLink = false;
	}
	return $itemLink;
}

if (isset($_POST['imagesSortable'])){
	$imagesSortable = array();
	parse_str($_POST['imagesSortable'], &$imagesSortable);

	$images = array();
	foreach($imagesSortable['image'] as $displayOrder => $imageNumber){
		$images[] = array(
			'image' => $_POST['image_source'][$imageNumber],
			'link' => parseItemLink($imageNumber)
		);
	}
	$WidgetProperties['images'] = $images;
}
?>