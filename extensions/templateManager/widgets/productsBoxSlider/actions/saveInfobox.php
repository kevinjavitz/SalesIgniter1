<?php
$config = array();
if (isset($_POST['products_box_query']) && !empty($_POST['products_box_query'])){
	$config = array(
		'query' => $_POST['products_box_query'],
		'query_limit' => $_POST['products_box_query_limit'],
		'reflect_blocks' => (isset($_POST['products_box_block_reflect']) ? true : false),
		'block_width' => $_POST['products_box_block_width'],
		'block_height' => $_POST['products_box_block_height'],
	);
	if(isset($_POST['new_selected_category']) && $_POST['new_selected_category'] != '-1' && $_POST['products_box_query'] == 'category_featured'){
		$config['selected_category'] = $_POST['new_selected_category'];
	}
}

if (isset($_POST['displayQty'])){
	$WidgetProperties['displayQty'] = $_POST['displayQty'];
}
else {
	$WidgetProperties['displayQty'] = '3';
}

if (isset($_POST['moveQty'])){
	$WidgetProperties['moveQty'] = $_POST['moveQty'];
}
else {
	$WidgetProperties['moveQty'] = '3';
}

if (isset($_POST['speed'])){
	$WidgetProperties['speed'] = $_POST['speed'];
}
else {
	$WidgetProperties['speed'] = '5000';
}

if (isset($_POST['duration'])){
	$WidgetProperties['duration'] = $_POST['duration'];
}
else {
	$WidgetProperties['duration'] = '3000';
}

if (isset($_POST['easing'])){
	$WidgetProperties['easing'] = $_POST['easing'];
}
else {
	$WidgetProperties['easing'] = 'swing';
}

$WidgetProperties['id'] = $_POST['products_box_id'];
$WidgetProperties['config'] = $config;
