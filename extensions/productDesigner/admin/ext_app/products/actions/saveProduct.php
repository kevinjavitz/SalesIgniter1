<?php
	$EditableAreas = Doctrine_Core::getTable('ProductDesignerEditableAreas');
	foreach($_POST['editable_area'] as $loc => $aInfo){
		if ($aInfo['x2'] <= 0 || $aInfo['y2'] <= 0) continue;
		$Area = $EditableAreas->findOneByProductsIdAndAreaLocation($Product->products_id, $loc);
		if (!$Area){
			$Area = $EditableAreas->create();
		}
	
		$Area->products_id = $Product->products_id;
		$Area->area_x1 = $aInfo['x1'];
		$Area->area_x2 = $aInfo['x2'];
		$Area->area_y1 = $aInfo['y1'];
		$Area->area_y2 = $aInfo['y2'];
		$Area->area_width = $aInfo['width'];
		$Area->area_height = $aInfo['height'];
		$Area->area_height_inches = $aInfo['height_inches'];
		$Area->area_width_inches = $aInfo['width_inches'];
		$Area->area_location = $loc;
		$Area->save();
		unset($Area);
	}
	
	$ProductDesignerImages = Doctrine_Core::getTable('ProductDesignerProductImages');
	$ProductImages = $ProductDesignerImages->findByProductsId($Product->products_id);
	$ProductImages->delete();
	if (isset($_POST['designer_image_light_front'])){
		$imgIdx = 0;
		foreach($_POST['designer_image_light_front'] as $idx => $fileName){
			$ProductImages[$imgIdx]->front_image = $fileName;
			$ProductImages[$imgIdx]->back_image = $_POST['designer_image_light_back'][$idx];
			$ProductImages[$imgIdx]->products_id = $Product->products_id;
			$ProductImages[$imgIdx]->color_tone = 'light';
			$ProductImages[$imgIdx]->display_color = $_POST['designer_image_light_color'][$idx];
				
			if (is_array($_POST['designer_image_default'])){
				if (isset($_POST['designer_image_default']['light_' . $idx])){
					$ProductImages[$imgIdx]->default_set = implode(',', $_POST['designer_image_default']['light_' . $idx]);
				}else{
					$ProductImages[$imgIdx]->default_set = '';
				}
			}else{
				$ProductImages[$imgIdx]->default_set = ($_POST['designer_image_default'] == 'light_' . $idx ? '1' : '0');
			}
			$imgIdx++;
		}
	}

	if (isset($_POST['designer_image_dark_front'])){
		$imgIdx = 0;
		foreach($_POST['designer_image_dark_front'] as $idx => $fileName){
			$ProductImages[$imgIdx]->front_image = $fileName;
			$ProductImages[$imgIdx]->back_image = $_POST['designer_image_dark_back'][$idx];
			$ProductImages[$imgIdx]->products_id = $Product->products_id;
			$ProductImages[$imgIdx]->color_tone = 'dark';
			$ProductImages[$imgIdx]->display_color = $_POST['designer_image_dark_color'][$idx];
				
			if (is_array($_POST['designer_image_default'])){
				if (isset($_POST['designer_image_default']['dark_' . $idx])){
					$ProductImages[$imgIdx]->default_set = implode(',', $_POST['designer_image_default']['dark_' . $idx]);
				}else{
					$ProductImages[$imgIdx]->default_set = '';
				}
			}else{
				$ProductImages[$imgIdx]->default_set = ($_POST['designer_image_default'] == 'dark_' . $idx ? '1' : '0');
			}
			$imgIdx++;
		}
	}
	$ProductImages->save();
	
	if (isset($_POST['product_designer_size_chart_id'])){
		$Product->product_designer_size_chart_id = $_POST['product_designer_size_chart_id'];
	}
	
	if (isset($_POST['predesign_id'])){
		$Product->predesign_id = $_POST['predesign_id'];
	}
	
	if (isset($_POST['predesign_id_back'])){
		$Product->predesign_id_back = $_POST['predesign_id_back'];
	}
	
	$Product->product_designer_predesign_classes = (isset($_POST['predesign_class']) ? implode(',', $_POST['predesign_class']) : '');
	$Product->product_designable = (isset($_POST['product_designable']) ? '1' : '0');
	$Product->product_designer_color_tone = (isset($_POST['product_designer_color_tone']) ? $_POST['product_designer_color_tone'] : 'light');
	if (isset($_POST['designer_image_default']) && is_array($_POST['designer_image_default'])){
		if (isset($_POST['designer_image_default']['product'])){
			$Product->product_designer_default_set = implode(',', $_POST['designer_image_default']['product']);
		}else{
			$Product->product_designer_default_set = '';
		}
	}else{
		$Product->product_designer_default_set = (isset($_POST['designer_image_default']) && $_POST['designer_image_default'] == 'product' ? '1' : '0');
	}
	$Product->product_designer_display_color = $_POST['product_designer_display_color'];
	$Product->products_image_back = (isset($_POST['products_image_back']) ? $_POST['products_image_back'] : '');
	$Product->save();
?>