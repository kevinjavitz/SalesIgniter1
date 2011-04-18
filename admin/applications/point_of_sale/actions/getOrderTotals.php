<?php
	$pointOfSale->processOrderTotals();

	$list = htmlBase::newElement('sortable_list')
	->attr('id', 'orderTotalsList')
	->css('margin-top', '.3em');
	
	$html = '';
	$pointOfSale = &Session::getReference('pointOfSale');
	if (is_array($pointOfSale->order_totals)) {
		foreach($pointOfSale->order_totals as $index => $output){
			$icons = '<span style="vertical-align:middle;display:inline-block;" class="ui-icon ui-icon-arrow-4"></span>';
			if ($output['code'] == 'CustomTotal'){
				$icons .= '<span style="vertical-align:middle;display:inline-block;" class="ui-icon ui-icon-pencil"></span>';
				$icons .= '<span style="vertical-align:middle;display:inline-block;" class="ui-icon ui-icon-closethick"></span>';
			}
			
			$itemObj = htmlBase::newElement('li')
			->attr('id', $output['code'])
			->addClass('orderTotal')
			->html('<div style="width:100px;text-align:right;display:inline-block;vertical-align:middle;">' . $output['title'] . '</div><div style="width:75px;text-align:right;display:inline-block;vertical-align:middle;">' . $output['text'] . '</div>' . $icons);
			$list->addItemObj($itemObj);
		}
	}
	EventManager::attachActionResponse($list->draw(), 'html');
?>