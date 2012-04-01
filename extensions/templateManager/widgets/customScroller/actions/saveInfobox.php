<?php
	$scrollers = array(
		'type' => $_POST['scroller_type'],
		'duration' => $_POST['duration'],
		'speed' => $_POST['speed'],
		'displayQty' => $_POST['displayQty'],
		'moveQty' => $_POST['moveQty'],
		'easing' => $_POST['easing'],
		'autostart' => (isset($_POST['scroller_autostart'])?'autostart':'')
	);
	
	if (isset($_POST['scroller_query']) && !empty($_POST['scroller_query'])){
		foreach($_POST['scroller_query'] as $id => $val){
			$scroller = array(
				'headings' => $_POST['scroller_heading'][$id],
				'rows' => $_POST['scroller_rows'][$id],
				'query' => $val,
				'selected_category' => ($val == 'category_featured' ? (isset($_POST['selected_category'][$id]) ? $_POST['selected_category'][$id] : '0') : ''),
				'query_limit' => $_POST['scroller_query_limit'][$id],
				'show_product_name' => (isset($_POST['scroller_show_product_name'][$id]) ? true : false),
				'reflect_blocks' => (isset($_POST['scroller_block_reflect'][$id]) ? true : false),
				'block_width' => $_POST['scroller_block_width'][$id],
				'block_height' => $_POST['scroller_block_height'][$id],
				'prev_image' => $_POST['scroller_prev_image'][$id],
				'next_image' => $_POST['scroller_next_image'][$id]
			);
		
			$scrollers['configs'][] = $scroller;
		}
	}

$WidgetProperties['scrollers'] = $scrollers;
?>