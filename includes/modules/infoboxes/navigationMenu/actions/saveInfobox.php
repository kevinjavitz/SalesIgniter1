<?php
function parseItemLink($itemId) {
	if ($_POST['menu_item_link'][$itemId] == 'app'){
		$itemLink = array(
			'type' => 'app',
			'application' => $_POST['menu_item_link_app'][$itemId],
			'page' => $_POST['menu_item_link_app_page'][$itemId],
			'target' => $_POST['menu_item_link_app_target'][$itemId]
		);
	}
	elseif ($_POST['menu_item_link'][$itemId] == 'category'){
		$catPath = $_POST['menu_item_link_category'][$itemId];
		if (isset($_POST['menu_item_link_category_path'][$itemId])){
			foreach($_POST['menu_item_link_category_path'][$itemId] as $id){
				if ($id == 'none') break;
				$catPath .= '_' . $id;
			}
		}
		$itemLink = array(
			'type' => 'category',
			'application' => 'index',
			'page' => 'default',
			'target' => $_POST['menu_item_link_category_target'][$itemId],
			'get_vars' => 'cPath=' . $catPath
		);
	}
	elseif ($_POST['menu_item_link'][$itemId] == 'custom') {
		$itemLink = array(
			'type' => 'custom',
			'url' => $_POST['menu_item_link_custom'][$itemId],
			'target' => $_POST['menu_item_link_custom_target'][$itemId]
		);
	}
	else {
		$itemLink = false;
	}
	return $itemLink;
}

function parseChildren($itemId, $itemArr, &$childArr) {
	$itemKeys = array_keys($itemArr, $itemId);
	foreach($itemKeys as $itemId){
		if ($itemArr[$itemId] == 'root'){
			continue;
		}

		$childArr[$itemId] = array(
			'icon' => $_POST['menu_item_icon'][$itemId],
			'icon_src' => (isset($_POST['menu_item_icon_src'][$itemId]) ? $_POST['menu_item_icon_src'][$itemId] : ''),
			'link' => parseItemLink($itemId),
			'condition' => $_POST['menu_item_condition'][$itemId],
			'children' => array()
		);
		foreach(sysLanguage::getLanguages() as $lInfo){
			$childArr[$itemId][$lInfo['id']]['text'] = $_POST['menu_item_text'][$lInfo['id']][$itemId];
		}

		if (in_array($itemId, $itemArr)){
			parseChildren($itemId, $itemArr, $childArr[$itemId]['children']);
		}
	}
}

if (!isset($_POST['linked_to'])){
	$menuConfig = array();
	if (!empty($_POST['navMenuSortable'])){
		parse_str($_POST['navMenuSortable'], $items);
		$i = 0;
		foreach($items['menu_item'] as $itemId => $parent){
			if ($parent == 'root'){
				$menuConfig[$i] = array(
					'icon' => $_POST['menu_item_icon'][$itemId],
					'icon_src' => (isset($_POST['menu_item_icon_src'][$itemId]) ? $_POST['menu_item_icon_src'][$itemId] : ''),
					'link' => parseItemLink($itemId),
					'condition' => $_POST['menu_item_condition'][$itemId],
					'children' => array()
				);

				foreach(sysLanguage::getLanguages() as $lInfo){
					$menuConfig[$i][$lInfo['id']]['text'] = $_POST['menu_item_text'][$lInfo['id']][$itemId];
				}

				if (in_array($itemId, $items['menu_item'])){
					parseChildren($itemId, $items['menu_item'], $menuConfig[$i]['children']);
				}
				$i++;
			}
		}
	}
	$WidgetProperties['menuSettings'] = $menuConfig;
}else{
	$WidgetProperties['linked_to'] = $_POST['linked_to'];
}

$WidgetProperties['menuId'] = $_POST['menu_id'];
$WidgetProperties['forceFit'] = (isset($_POST['force_fit']) ? 'true' : 'false');
