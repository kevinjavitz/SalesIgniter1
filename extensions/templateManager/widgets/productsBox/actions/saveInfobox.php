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

$WidgetProperties['id'] = $_POST['products_box_id'];
$WidgetProperties['config'] = $config;
