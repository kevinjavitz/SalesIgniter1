<?php
function parseElementSettings($el, &$Container) {
	if ($el->attr('data-styles')){
		$Styles = json_decode(urldecode($el->attr('data-styles')));
		$InputVals = json_decode(urldecode($el->attr('data-inputs')));

		foreach($Styles as $k => $v){
			if ($k == 'boxShadow') {
				continue;
			}
			if (substr($k, 0, 10) == 'background') {
				continue;
			}

			if (is_array($v) || is_object($v)){
				$Container->Styles[$k]->definition_value = json_encode($v);
			}
			else {
				$Container->Styles[$k]->definition_value = $v;
			}
		}

		if (!empty($InputVals)){
			foreach($InputVals as $k => $v){
				if ($k == 'boxShadow') {
					continue;
				}
				if (substr($k, 0, 6) == 'margin') {
					continue;
				}
				if (substr($k, 0, 7) == 'padding') {
					continue;
				}

				if (is_array($v) || is_object($v)){
					$Container->Configuration[$k]->configuration_value = json_encode($v);
				}
				else {
					$Container->Configuration[$k]->configuration_value = $v;
				}
			}

			if (isset($InputVals->margin)){
				$Container->Configuration['margin']->configuration_value = json_encode(array(
						'top' => $InputVals->margin->top,
						'top_unit' => $InputVals->margin->top_unit,
						'right' => $InputVals->margin->right,
						'right_unit' => $InputVals->margin->right_unit,
						'bottom' => $InputVals->margin->bottom,
						'bottom_unit' => $InputVals->margin->bottom_unit,
						'left' => $InputVals->margin->left,
						'left_unit' => $InputVals->margin->left_unit
					));
				$Container->Styles['margin']->definition_value = $Container->Configuration['margin']->configuration_value;
			}

			if (isset($InputVals->padding)){
				$Container->Configuration['padding']->configuration_value = json_encode(array(
						'top' => $InputVals->padding->top,
						'top_unit' => $InputVals->padding->top_unit,
						'right' => $InputVals->padding->right,
						'right_unit' => $InputVals->padding->right_unit,
						'bottom' => $InputVals->padding->bottom,
						'bottom_unit' => $InputVals->padding->bottom_unit,
						'left' => $InputVals->padding->left,
						'left_unit' => $InputVals->padding->left_unit
					));
				$Container->Styles['padding']->definition_value = $Container->Configuration['padding']->configuration_value;
			}

			if (isset($InputVals->backgroundType) && isset($InputVals->backgroundType->global)){
				$backgroundType = $InputVals->backgroundType->global;
				if (isset($InputVals->background->global->$backgroundType)){
					if ($backgroundType == 'solid'){
						$bInfo = $InputVals->background->global->$backgroundType->config;

						$Container->Styles['background_solid']->definition_value = json_encode($bInfo);
					}
					else
					{
						if ($backgroundType == 'gradient'){
							$gInfo = $InputVals->background->global->$backgroundType;

							$gradientObj = array(
								'type' => $gInfo->config->gradient_type,
								'h_pos_start' => $gInfo->config->start_horizontal_pos,
								'v_pos_start' => $gInfo->config->start_vertical_pos,
								'h_pos_end' => $gInfo->config->end_horizontal_pos,
								'v_pos_end' => $gInfo->config->end_vertical_pos,
								'h_pos_start_unit' => '%',
								'v_pos_start_unit' => '%',
								'h_pos_end_unit' => '%',
								'v_pos_end_unit' => '%'
							);

							$gradientObj['colorStops'][] = array(
								'color' => array(
									'r' => $gInfo->config->start_color_r,
									'g' => $gInfo->config->start_color_g,
									'b' => $gInfo->config->start_color_b,
									'a' => ($gInfo->config->start_color_a / 100)
								),
								'position' => '0'
							);
							foreach($gInfo->colorStops as $stopInfo){
								$gradientObj['colorStops'][] = array(
									'color' => array(
										'r' => $stopInfo->color_stop_color_r,
										'g' => $stopInfo->color_stop_color_g,
										'b' => $stopInfo->color_stop_color_b,
										'a' => ($stopInfo->color_stop_color_a / 100)
									),
									'position' => ($stopInfo->color_stop_pos / 100)
								);
							}
							$gradientObj['colorStops'][] = array(
								'color' => array(
									'r' => $gInfo->config->end_color_r,
									'g' => $gInfo->config->end_color_g,
									'b' => $gInfo->config->end_color_b,
									'a' => ($gInfo->config->end_color_a / 100)
								),
								'position' => '1'
							);

							if (isset($gInfo->imagesBefore)){
								foreach($gInfo->imagesBefore as $bimageInfo){
									$gradientObj['images'][] = array(
										'css_placement' => 'before',
										'image' => $bimageInfo->image_source,
										'repeat' => $bimageInfo->image_repeat,
										'pos_x' => $bimageInfo->image_pos_x,
										'pos_y' => $bimageInfo->image_pos_y,
										'pos_x_unit' => '%',
										'pos_y_unit' => '%'
									);
								}
							}

							if (isset($gInfo->imagesAfter)){
								foreach($gInfo->imagesAfter as $aimageInfo){
									$gradientObj['images'][] = array(
										'css_placement' => 'after',
										'image' => $aimageInfo->image_source,
										'repeat' => $aimageInfo->image_repeat,
										'pos_x' => $aimageInfo->image_pos_x,
										'pos_y' => $aimageInfo->image_pos_y,
										'pos_x_unit' => '%',
										'pos_y_unit' => '%'
									);
								}
							}

							$Container->Styles['background_complex_gradient']->definition_value = json_encode($gradientObj);
						}
					}
				}
			}

			if (isset($InputVals->boxShadow) && sizeof($InputVals->boxShadow) > 0){
				$shadowsArr = array();
				foreach($InputVals->boxShadow as $sInfo){
					$shadowsArr[] = array(
						'offset_x' => $sInfo->offset_x,
						'offset_y' => $sInfo->offset_y,
						'offset_x_unit' => 'px',
						'offset_y_unit' => 'px',
						'blur' => $sInfo->blur,
						'spread' => $sInfo->spread,
						'color' => $sInfo->color,
						'inset' => ($sInfo->inset === true ? 'true' : 'false')
					);
				}

				if (sizeof($shadowsArr) > 0){
					$Container->Styles['box_shadow']->definition_value = json_encode($shadowsArr);
				}
			}

			if (isset($InputVals->borderRadius) && sizeof($InputVals->borderRadius) > 0){
				$Container->Styles['border_radius']->definition_value = json_encode(array(
						'border_top_left_radius' => $InputVals->borderRadius->border_top_left_radius,
						'border_top_left_radius_unit' => $InputVals->borderRadius->border_top_left_radius_unit,
						'border_top_right_radius' => $InputVals->borderRadius->border_top_right_radius,
						'border_top_right_radius_unit' => $InputVals->borderRadius->border_top_right_radius_unit,
						'border_bottom_right_radius' => $InputVals->borderRadius->border_bottom_right_radius,
						'border_bottom_right_radius_unit' => $InputVals->borderRadius->border_bottom_right_radius_unit,
						'border_bottom_left_radius' => $InputVals->borderRadius->border_bottom_left_radius,
						'border_bottom_left_radius_unit' => $InputVals->borderRadius->border_bottom_left_radius_unit
					));
			}
		}
	}
}

function parseLayout($el) {
	global $Layout;

	if ($Layout->Styles){
		$Layout->Styles->clear();
	}
	if ($Layout->Configuration){
		$Layout->Configuration->clear();
	}

	parseElementSettings($el, $Layout);
}

function parseElement(&$el, &$parent) {
	global $Layout, $newElementHolder;

	if (!is_object($parent)){
		if ($el->attr('data-container_id')){
			$Container = $Layout->Containers->getTable()->find($el->attr('data-container_id'));
		}
		else {
			$Container = $Layout->Containers->getTable()->create();
			$Layout->Containers->add($Container);
			$Container->parent_id = 0;
		}
	}
	elseif ($el->hasClass('container')) {
		if ($el->attr('data-container_id')){
			$containerId = $el->attr('data-container_id');
			$Container = $parent->Children->getTable()->find($containerId);
			if ($Container->parent_id == 0){
				$parent->Children->add($Container);
			}
		}
		else {
			$Container = $parent->Children->getTable()->create();
			$parent->Children->add($Container);
		}
	}
	else {
		if ($el->attr('data-column_id')){
			$Container = $parent->Columns->getTable()->find($el->attr('data-column_id'));
		}
		else {
			$Container = $parent->Columns->getTable()->create();
			$parent->Columns->add($Container);
		}
	}
	if ($Container->Styles){
		$Container->Styles->clear();
	}
	if ($Container->Configuration){
		$Container->Configuration->clear();
	}
	$Container->sort_order = (int)$el->attr('data-sort_order');

	if ($el->attr('tmid')){
		$newElementHolder[$el->attr('tmid')] = $Container;
	}

	parseElementSettings($el, $Container);

	foreach($el->children() as $child){
		$childObj = pq($child);
		if ($childObj->is('ul')){
			foreach($childObj->children() as $wInfo){
				$wInfo = pq($wInfo);
				if ($wInfo->attr('data-widget_id')){
					$Widget = $Container->Widgets->getTable()->find($wInfo->attr('data-widget_id'));
					if ($Widget->column_id != $Container->column_id){
						if (!is_numeric($Container->column_id)){
							$Widget->column_id = '';
						}
						else {
							$Widget->column_id = $Container->column_id;
						}
					}
				}
				else {
					$Widget = $Container->Widgets->getTable()->create();
					$Container->Widgets->add($Widget);
				}
				if ($Widget->Styles){
					$Widget->Styles->clear();
				}
				if ($Widget->Configuration){
					$Widget->Configuration->clear();
				}
				$Widget->identifier = $wInfo->attr('data-widget_code');
				$Widget->sort_order = $wInfo->attr('data-sort_order');
				$Widget->Configuration['widget_settings']->configuration_value = $wInfo->attr('data-widget_settings');

				if ($wInfo->attr('tmid')){
					$newElementHolder[$wInfo->attr('tmid')] = $Widget;
				}
			}
		}
		else {
			$newParent = ($el->hasClass('column') ? null : (isset($Container) ? $Container : null));
			parseElement($childObj, $newParent);
		}
	}
}

function deleteRemovedElements(&$Element, $existingContainers, $existingColumns, $existingWidgets) {
	if (isset($Element->container_id) && !isset($Element->column_id)){
		if (!in_array($Element->container_id, $existingContainers)){
			$Element->delete();
		}
		elseif ($Element->Children->count() > 0) {
			foreach($Element->Children as $childObj){
				deleteRemovedElements($childObj, $existingContainers, $existingColumns, $existingWidgets);
			}
		}
		elseif ($Element->Columns->count() > 0) {
			foreach($Element->Columns as $colObj){
				deleteRemovedElements($colObj, $existingContainers, $existingColumns, $existingWidgets);
			}
		}
	}
	elseif (isset($Element->container_id) && isset($Element->column_id)) {
		if (!in_array($Element->column_id, $existingColumns)){
			$Element->delete();
		}
		elseif ($Element->Widgets->count() > 0) {
			foreach($Element->Widgets as $widgetObj){
				deleteRemovedElements($widgetObj, $existingContainers, $existingColumns, $existingWidgets);
			}
		}
	}
	elseif (isset($Element->column_id) && isset($Element->widget_id)) {
		if (!in_array($Element->widget_id, $existingWidgets)){
			$Element->delete();
		}
	}
}

$newElementInfo = array();
$newElementHolder = array();
if ($_GET['layout_id'] != 'null'){
	$TemplateLayoutSource = $_POST['templateData'];
	$TemplateLayout = phpQuery::newDocumentHTML($TemplateLayoutSource);
	$existsContainer = array();
	foreach($TemplateLayout->find('.container') as $child){
		$el = pq($child);
		if (!$el->attr('data-container_id')) {
			continue;
		}

		$existsContainer[] = $el->attr('data-container_id');
	}

	$existsColumn = array();
	foreach($TemplateLayout->find('.column') as $child){
		$el = pq($child);
		if (!$el->attr('data-column_id')) {
			continue;
		}

		$existsColumn[] = $el->attr('data-column_id');
	}

	$existsWidget = array();
	foreach($TemplateLayout->find('.widget') as $child){
		$el = pq($child);
		if (!$el->attr('data-widget_id')) {
			continue;
		}

		$existsWidget[] = $el->attr('data-widget_id');
	}

	$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find($_GET['layout_id']);
	foreach($Layout->Containers as $key => $Container){
		deleteRemovedElements($Layout->Containers[$key], $existsContainer, $existsColumn, $existsWidget);
	}
	$Layout->Save();

	parseLayout($TemplateLayout->find('#construct'));

	if (sizeof($TemplateLayout->find('#construct')->children()) > 0){
		foreach($TemplateLayout->find('#construct')->children() as $child){
			$childObj = pq($child);
			$parent = null;
			parseElement($childObj, $parent);
		}
		$Layout->save();
	}

	if (!empty($newElementHolder)){
		foreach($newElementHolder as $tmId => $El){
			if (isset($El->container_id) && !isset($El->column_id)){
				$newElementInfo['containers'][$tmId] = $El->container_id;
			}elseif (isset($El->container_id) && isset($El->column_id)){
				$newElementInfo['columns'][$tmId] = $El->column_id;
			}elseif (isset($El->column_id) && isset($El->widget_id)){
				$newElementInfo['widgets'][$tmId] = $El->widget_id;
			}
		}
	}
}

EventManager::attachActionResponse(array(
		'success' => true,
		'newElementInfo' => $newElementInfo
	), 'json');
