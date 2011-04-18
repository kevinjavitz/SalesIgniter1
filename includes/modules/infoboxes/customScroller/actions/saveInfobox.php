<?php
	$scrollers = array(
		'type' => $_POST['scroller_type']
	);
	
	if (isset($_POST['scroller_query']) && !empty($_POST['scroller_query'])){
		foreach($_POST['scroller_query'] as $id => $val){
			$scroller = array(
				'headings' => $_POST['scroller_heading'][$id],
				'query' => $val,
				'query_limit' => $_POST['scroller_query_limit'][$id],
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