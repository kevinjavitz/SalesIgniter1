<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productDesigner_admin_data_manager_default extends Extension_productDesigner {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryBeforeExecute',
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave'
		), null, $this);
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		$Product->predesign_id = $items['v_predesign_id'];
		$Product->predesign_id_back = $items['v_predesign_id_back'];
		$Product->products_image_back = $items['v_products_image_back'];
		$Product->product_designer_color_tone = $items['v_product_designer_color_tone'];
		$Product->product_designer_default_set = $items['v_product_designer_default_set'];
		$Product->product_designer_display_color = substr($items['v_product_designer_display_color'], 1);
		$Product->product_designable = $items['v_product_designable'];
		$Product->product_designer_size_chart_id = $items['v_product_designer_size_chart_id'];
		
		$ProductDesignerImages =& $Product->ProductDesignerProductImages;
		$ProductDesignerImages->delete();
		if (isset($items['v_designer_image_set_1_front_image']) && !empty($items['v_designer_image_set_1_front_image'])){
			$end = false;
			$count = 1;
			$idx = 0;
			while($end === false){
				if (!isset($items['v_designer_image_set_' . $count . '_front_image'])){
					$end = true;
					continue;
				}
				
				if (empty($items['v_designer_image_set_' . $count . '_front_image'])){
					$count++;
					continue;
				}
				
				$ProductDesignerImages[$idx]->front_image = $items['v_designer_image_set_' . $count . '_front_image'];
				$ProductDesignerImages[$idx]->back_image = $items['v_designer_image_set_' . $count . '_back_image'];
				$ProductDesignerImages[$idx]->color_tone = $items['v_designer_image_set_' . $count . '_color_tone'];
				$ProductDesignerImages[$idx]->display_color = substr($items['v_designer_image_set_' . $count . '_display_color'], 1);
				$ProductDesignerImages[$idx]->default_set = $items['v_designer_image_set_' . $count . '_default'];

				$idx++;
				$count++;
			}
		}
	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$dataExport->setHeaders(array(
			'v_predesign_id',
			'v_predesign_id_back',
			'v_products_image_back',
			'v_product_designer_color_tone',
			'v_product_designer_default_set',
			'v_product_designer_display_color',
			'v_product_designable',
			'v_product_designer_size_chart_id'
		));
		
		$mostImages = 0;
		$Qcheck = Doctrine_Query::create()
		->select('count(images_id) as total')
		->from('ProductDesignerProductImages')
		->groupBy('products_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			foreach($Qcheck as $count){
				if ($count['total'] > $mostImages){
					$mostImages = $count['total'];
				}
			}
		}
		
		for($i=1; $i<$mostImages+1; $i++){
			$dataExport->setHeaders(array(
				'v_designer_image_set_' . $i . '_front_image',
				'v_designer_image_set_' . $i . '_back_image',
				'v_designer_image_set_' . $i . '_color_tone',
				'v_designer_image_set_' . $i . '_display_color',
				'v_designer_image_set_' . $i . '_default'
			));
		}
	}
	
	public function DataExportFullQueryBeforeExecute(&$query){
		$query->addSelect('
		p.predesign_id as v_predesign_id,
		p.predesign_id_back as v_predesign_id_back,
		p.products_image_back as v_products_image_back,
		p.product_designer_color_tone as v_product_designer_color_tone,
		p.product_designer_default_set as v_product_designer_default_set,
		p.product_designer_display_color as v_product_designer_display_color
		p.product_designable as v_product_designable
		p.product_designer_size_chart_id as v_product_designer_size_chart_id
		');
	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		if (!empty($productRow['v_product_designer_display_color'])){
			$productRow['v_product_designer_display_color'] = '#' . strtoupper($productRow['v_product_designer_display_color']);
		}
		$Qimages = Doctrine_Query::create()
		->from('ProductDesignerProductImages')
		->where('products_id = ?', $productRow['products_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qimages){
			foreach($Qimages as $i => $image){
				$realCount = $i+1;
				$productRow['v_designer_image_set_' . $realCount . '_front_image'] = $image['front_image'];
				$productRow['v_designer_image_set_' . $realCount . '_back_image'] = $image['back_image'];
				$productRow['v_designer_image_set_' . $realCount . '_color_tone'] = $image['color_tone'];
				$productRow['v_designer_image_set_' . $realCount . '_display_color'] = '#' . strtoupper($image['display_color']);
				$productRow['v_designer_image_set_' . $realCount . '_default'] = $image['default_set'];
			}
		}
	}
}
?>