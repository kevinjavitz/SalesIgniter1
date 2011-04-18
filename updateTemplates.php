<?php
die();
require('includes/application_top.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/html/dom/phpQuery.php');

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
	$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
	$rgbArray = array();
	if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
		$colorVal = hexdec($hexStr);
		$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
		$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
		$rgbArray['blue'] = 0xFF & $colorVal;
	} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
		$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
		$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
		$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
	} else {
		return false; //Invalid hex color code
	}
	return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}
function parseElement(&$el, &$parent, $sortOrder = 0){
	global $Layout, $oldLayoutId;

	$StylesInfo = json_decode($el->attr('data-stylesinfo'));
	$Id = $el->attr('id');
	$Styles = $StylesInfo->styles;
	$Inputs = $StylesInfo->inputVals;
	$Inputs->id = $Id;
	if (isset($StylesInfo->gradients)){
		$Inputs->gradients = $StylesInfo->gradients;
	}

	$el->attr('data-styles', json_encode($Styles));
	$el->attr('data-inputs', json_encode($Inputs));

	if (!is_object($parent)){
		$Container = $Layout->Containers->getTable()->create();
		$Layout->Containers->add($Container);
	}elseif ($el->hasClass('container')){
		$Container = $parent->Children->getTable()->create();
		$parent->Children->add($Container);
	}else{
		$Container = $parent->Columns->getTable()->create();
		$parent->Columns->add($Container);
	}
	if ($Container->Styles){
		//$Container->Styles->clear();
	}
	if ($Container->Configuration){
		//$Container->Configuration->clear();
	}
	$Container->sort_order = (int) $sortOrder;
	$sortOrder++;

	// process css for id and classes
	if ($el->attr('data-styles')){
		$Styles = json_decode(urldecode($el->attr('data-styles')));
		$InputVals = json_decode(urldecode($el->attr('data-inputs')));

		foreach($Styles as $k => $v){
			if ($k == 'boxShadow') continue;
			if ($k == 'end-color-r' || $k == 'end-color-g' || $k == 'end-color-b' ||
				$k == 'end-color-a' || $k == 'start-vertical-pos' || $k == 'start-color-r' || $k == 'start-color-g' ||
				$k == 'start-color-b' || $k == 'start-color-a' || $k == 'color' || $k == 'font-family' || $k == 'font-size' ||
				$k == 'color-stop-color-r' || $k == 'color-stop-color-g' || $k == 'color-stop-color-b' || $k == 'color-stop-color-a' ||
				$k == 'color-stop-pos' || $k == 'image-background-color-r' || $k == 'image-background-color-g' ||
				$k == 'image-background-color-b' || $k == 'image-repeat' || $k == 'end-vertical-pos' || $k == '_empty_') continue;
			if (substr($k, 0, 10) == 'background') continue;
			if (substr($k, 0, 6) == 'margin') continue;
			if (substr($k, 0, 7) == 'padding') continue;
			if (substr($k, 0, 6) == 'border') continue;

			if (is_array($v) || is_object($v)){
				$Container->Styles[$k]->definition_value = json_encode($v);
			}else{
				$Container->Styles[$k]->definition_value = $v;
			}
		}

		if (!empty($InputVals)){
			$ignored = array(
				'classes',
				'custom_css',
				'enable_advanced',
				'float',
				'position',
				'top',
				'top_unit',
				'right',
				'right_unit',
				'bottom',
				'bottom_unit',
				'left',
				'left_unit',
				'overflow_x',
				'overflow_y',
				'z_index',
				'boxShadow',
				'background_type',
				'equal_heights',
				'gradients',
				'global',
				'width_type',
				'width_auto',
				'color',
				'font_family',
				'font_size',
				'font_size_unit'
			);
			foreach($InputVals as $k => $v){
				if (in_array($k, $ignored)) continue;
				if (substr($k, 0, 6) == 'border') continue;
				if (substr($k, 0, 6) == 'margin') continue;
				if (substr($k, 0, 7) == 'padding') continue;
				if (substr($k, 0, 10) == 'background') continue;

				if (is_array($v) || is_object($v)){
					$Container->Configuration[$k]->configuration_value = json_encode($v);
				}else{
					$Container->Configuration[$k]->configuration_value = $v;
				}
			}

				$Container->Configuration['font']->configuration_value = json_encode(array(
					'color' => (isset($InputVals->color) ? $InputVals->color : '#000000'),
					'family' => (isset($InputVals->font_family) ? $InputVals->font_family : 'Arial'),
					'size' => (isset($InputVals->font_size) ? (int)$InputVals->font_size : 1),
					'size_unit' => (isset($InputVals->font_size_unit) ? $InputVals->font_size_unit : 'em')
				));
				$Container->Styles['font']->definition_value = $Container->Configuration['font']->configuration_value;

				$Container->Configuration['border']->configuration_value = json_encode(array(
					'top' => array(
						'width' => (isset($InputVals->border_top_width) ? (int)$InputVals->border_top_width : 0),
						'width_unit' => (isset($InputVals->border_top_width_unit) ? $InputVals->border_top_width_unit : 'px'),
						'color' => (isset($InputVals->border_top_color) ? $InputVals->border_top_color : '#000000'),
						'style' => (isset($InputVals->border_top_style) ? $InputVals->border_top_style : 'solid')
					),
					'right' => array(
						'width' => (isset($InputVals->border_right_width) ? (int)$InputVals->border_right_width : 0),
						'width_unit' => (isset($InputVals->border_right_width_unit) ? $InputVals->border_right_width_unit : 'px'),
						'color' => (isset($InputVals->border_right_color) ? $InputVals->border_right_color : '#000000'),
						'style' => (isset($InputVals->border_right_style) ? $InputVals->border_right_style : 'solid')
					),
					'bottom' => array(
						'width' => (isset($InputVals->border_bottom_width) ? (int)$InputVals->border_bottom_width : 0),
						'width_unit' => (isset($InputVals->border_bottom_width_unit) ? $InputVals->border_bottom_width_unit : 'px'),
						'color' => (isset($InputVals->border_bottom_color) ? $InputVals->border_bottom_color : '#000000'),
						'style' => (isset($InputVals->border_bottom_style) ? $InputVals->border_bottom_style : 'solid')
					),
					'left' => array(
						'width' => (isset($InputVals->border_left_width) ? (int)$InputVals->border_left_width : 0),
						'width_unit' => (isset($InputVals->border_left_width_unit) ? $InputVals->border_left_width_unit : 'px'),
						'color' => (isset($InputVals->border_left_color) ? $InputVals->border_left_color : '#000000'),
						'style' => (isset($InputVals->border_left_style) ? $InputVals->border_left_style : 'solid')
					),
				));
				$Container->Styles['border']->definition_value = $Container->Configuration['border']->configuration_value;

				$Container->Configuration['margin']->configuration_value = json_encode(array(
					'top' => (isset($InputVals->margin_top) ? (int)$InputVals->margin_top : 0),
					'top_unit' => (isset($InputVals->margin_top_unit) ? $InputVals->margin_top_unit : 'px'),
					'right' => (isset($InputVals->margin_right) ? (int)$InputVals->margin_right : 0),
					'right_unit' => (isset($InputVals->margin_right_unit) ? $InputVals->margin_right_unit : 'px'),
					'bottom' => (isset($InputVals->margin_bottom) ? (int)$InputVals->margin_bottom : 0),
					'bottom_unit' => (isset($InputVals->margin_bottom_unit) ? $InputVals->margin_bottom_unit : 'px'),
					'left' => (isset($InputVals->margin_left) ? (int)$InputVals->margin_left : 0),
					'left_unit' => (isset($InputVals->margin_left_unit) ? $InputVals->margin_left_unit : 'px')
				));
				$Container->Styles['margin']->definition_value = $Container->Configuration['margin']->configuration_value;

			if (isset($InputVals->padding_top)){
				$Container->Configuration['padding']->configuration_value = json_encode(array(
					'top' => (isset($InputVals->padding_top) ? (int)$InputVals->padding_top : 0),
					'top_unit' => (isset($InputVals->padding_top_unit) ? $InputVals->padding_top_unit : 'px'),
					'right' => (isset($InputVals->padding_right) ? (int)$InputVals->padding_right : 0),
					'right_unit' => (isset($InputVals->padding_right_unit) ? $InputVals->padding_right_unit : 'px'),
					'bottom' => (isset($InputVals->padding_bottom) ? (int)$InputVals->padding_bottom : 0),
					'bottom_unit' => (isset($InputVals->padding_bottom_unit) ? $InputVals->padding_bottom_unit : 'px'),
					'left' => (isset($InputVals->padding_left) ? (int)$InputVals->padding_left : 0),
					'left_unit' => (isset($InputVals->padding_left_unit) ? $InputVals->padding_left_unit : 'px')
				));
				$Container->Styles['padding']->definition_value = $Container->Configuration['padding']->configuration_value;
			}

			if (isset($InputVals->border_top_left_radius)){
				$Container->Configuration['border_radius']->configuration_value = json_encode(array(
					'border_top_left_radius' => (int)$InputVals->border_top_left_radius,
					'border_top_left_radius_unit' => $InputVals->border_top_left_radius_unit,
					'border_top_right_radius' => (int)$InputVals->border_top_right_radius,
					'border_top_right_radius_unit' => $InputVals->border_top_right_radius_unit,
					'border_bottom_left_radius' => (int)$InputVals->border_bottom_left_radius,
					'border_bottom_left_radius_unit' => $InputVals->border_bottom_left_radius_unit,
					'border_bottom_right_radius' => (int)$InputVals->border_bottom_right_radius,
					'border_bottom_right_radius_unit' => $InputVals->border_bottom_right_radius_unit
				));
				$Container->Styles['border_radius']->definition_value = $Container->Configuration['border_radius']->configuration_value;
			}

			if (isset($InputVals->background_type)){
				$Container->Configuration['backgroundType']->configuration_value = json_encode(array(
					'global' => $InputVals->background_type
				));

				if ($InputVals->background_type == 'solid'){
					if (isset($InputVals->background_r)){
						$Container->Configuration['background']->configuration_value = json_encode(array(
							'global' => array(
								'solid' => array(
									'config' => array(
										'background_r' => $InputVals->background_r,
										'background_g' => $InputVals->background_g,
										'background_b' => $InputVals->background_b,
										'background_a' => $InputVals->background_a
									)
								)
							)
						));
						$Container->Styles['background']->definition_value = json_encode(array(
							'background_r' => $InputVals->background_r,
							'background_g' => $InputVals->background_g,
							'background_b' => $InputVals->background_b,
							'background_a' => $InputVals->background_a
						));
					}
				}
			}

			if (isset($InputVals->gradients)){
				$gInfo = $InputVals->gradients;

				$stylesStops = array();
				$configStops = array();
				foreach($gInfo as $sKey => $stopInfo){
					$rgb = hex2RGB($stopInfo->color);
					if ($sKey == 0){
						$start_color_r = $rgb['red'];
						$start_color_g = $rgb['green'];
						$start_color_b = $rgb['blue'];
						$start_color_a = 100;
					}elseif (!isset($gInfo->{$sKey + 1})){
						$end_color_r = $rgb['red'];
						$end_color_g = $rgb['green'];
						$end_color_b = $rgb['blue'];
						$end_color_a = 100;
					}else{
						$configStops[] = array(
							'color_stop_color_r' => $rgb['red'],
							'color_stop_color_g' => $rgb['green'],
							'color_stop_color_b' => $rgb['blue'],
							'color_stop_color_a' => 100,
							'color_stop_pos' => $stopInfo->percent
						);
					}

					$stylesStops[] = array(
						'color' => array(
							'r' => $rgb['red'],
							'g' => $rgb['green'],
							'b' => $rgb['blue'],
							'a' => 100
						),
						'position' => ($stopInfo->percent/100)
					);
				}

				$Container->Configuration['background']->configuration_value = json_encode(array(
					'global' => array(
						'gradient' => array(
							'config' => array(
								'gradient_type' => 'linear',
								'start_horizontal_pos' => 0,
								'start_vertical_pos' => 0,
								'end_horizontal_pos' => 0,
								'end_vertical_pos' => 100,
								'start_color_r' => $start_color_r,
								'start_color_g' => $start_color_g,
								'start_color_b' => $start_color_b,
								'start_color_a' => $start_color_a,
								'end_color_r' => $end_color_r,
								'end_color_g' => $end_color_g,
								'end_color_b' => $end_color_b,
								'end_color_a' => $end_color_a
							),
							'colorStops' => $configStops,
							'imagesBefore' => array(),
							'imagesAfter' => array()
						)
					)
				));

				$Container->Styles['background_complex_gradient']->definition_value = json_encode(array(
					'type' => 'linear',
					'h_pos_start' => 0,
					'v_pos_start' => 0,
					'h_pos_end' => 0,
					'v_pos_end' => 100,
					'h_pos_start_unit' => '%',
					'v_pos_start_unit' => '%',
					'h_pos_end_unit' => '%',
					'v_pos_end_unit' => '%',
					'colorStops' => $stylesStops
				));
			}
		}
	}

	foreach($el->children() as $child){
		$childObj = pq($child);
		if ($childObj->is('ul')){
			$WidgetSortOrder = 0;
			foreach($childObj->children() as $wInfo){
				$wInfo = pq($wInfo);
				$WidgetId = explode('_', $wInfo->attr('id'));
				$WidgetId = $WidgetId[1];

				$Widget = $Container->Widgets->getTable()->create();
				$Container->Widgets->add($Widget);
				if ($Widget->Styles){
					$Widget->Styles->clear();
				}
				if ($Widget->Configuration){
					$Widget->Configuration->clear();
				}
				$Widget->identifier = $WidgetId;
				$Widget->sort_order = $WidgetSortOrder++;

				$WidgetConfig = Doctrine_Query::create()
					->select('templates_infoboxes_id, template_file, widget_properties')
					->from('TemplatesInfoboxesToTemplates')
					->where('box_id = ?', $wInfo->attr('id'))
					->andWhere('layout_id = ?', $oldLayoutId)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if (sizeof($WidgetConfig) > 0){
					$Settings = unserialize($WidgetConfig[0]['widget_properties']);
					$Settings['template_file'] = $WidgetConfig[0]['template_file'];

					$Qtitle = Doctrine_Query::create()
						->select('language_id, box_heading')
						->from('TemplatesInfoboxesDescription')
						->where('templates_infoboxes_id = ?', $WidgetConfig[0]['templates_infoboxes_id'])
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qtitle){
						foreach($Qtitle as $tInfo){
							$Settings['widget_title'][$tInfo['language_id']] = $tInfo['box_heading'];
						}
					}

					if (isset($Settings['menuSettings'])){
						foreach($Settings['menuSettings'] as $mKey => $mInfo){
							$langText = array();
							foreach(sysLanguage::getLanguages() as $lInfo){
								if (isset($Settings['menuSettings'][$mKey][$lInfo['id']])){
									$langText[$lInfo['id']] = $Settings['menuSettings'][$mKey][$lInfo['id']]['text'];
									unset($Settings['menuSettings'][$mKey][$lInfo['id']]);
								}elseif (isset($Settings['menuSettings'][$mKey]['text'])){
									$langText[$lInfo['id']] = $Settings['menuSettings'][$mKey]['text'];
								}
							}
							if (isset($Settings['menuSettings'][$mKey]['text'])){
								unset($Settings['menuSettings'][$mKey]['text']);
							}
							$Settings['menuSettings'][$mKey]['text'] = $langText;
						}
					}

					if (isset($Settings['template_file'])){
						$Settings['template_file'] = ($Settings['template_file'] != 'null' && !empty($Settings['template_file']) && is_null($Settings['template_file']) === false ? $Settings['template_file'] : 'noFormatingBox.tpl');
					}

					if (isset($Settings['image_src'])){
						$Settings['image_source'] = $Settings['image_src'];
						unset($Settings['image_src']);
					}
					$Widget->Configuration['widget_settings']->configuration_value = json_encode($Settings);
				}
			}
		}else{
			$newParent = ($el->hasClass('column') ? null : (isset($Container) ? $Container : null));
			parseElement($childObj, $newParent, $sortOrder);
			$sortOrder++;
		}
	}
}

$Template = Doctrine_Core::getTable('TemplateManagerTemplates');
$OldLayouts = Doctrine_Core::getTable('TemplateLayouts');
foreach($OldLayouts->findAll() as $OldLayout){
	$oldLayoutId = $OldLayout->layout_id;
	$TemplateLayout = phpQuery::newDocumentHTML($OldLayout->layout_content_source);

	if (isset($madeTemplates[$OldLayout['template_name']])){
		$NewTemplate = $madeTemplates[$OldLayout['template_name']];
	}else{
		$NewTemplate = $Template->create();
		$NewTemplate->Configuration['NAME']->configuration_value = ucwords($OldLayout['template_name']);
		$NewTemplate->Configuration['DIRECTORY']->configuration_value = $OldLayout['template_name'];
	}

	$Layout = $NewTemplate->Layouts->getTable()->create();
	$Layout->layout_name = $OldLayout['layout_name'];
	$NewTemplate->Layouts->add($Layout);

	$sortOrder = 0;
	foreach($TemplateLayout->children() as $child){
		$childObj = pq($child);
		$parent = null;
		parseElement($childObj, $parent, $sortOrder);
		$sortOrder++;
	}

	$NewTemplate->save();
	//echo '<pre>';print_r($NewTemplate->toArray(true));
	$madeTemplates[$OldLayout['template_name']] = $NewTemplate;
}