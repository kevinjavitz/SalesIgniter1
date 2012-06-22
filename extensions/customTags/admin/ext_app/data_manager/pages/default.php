<?php
/*
	Products Custom Fields Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class customTags_admin_data_manager_default extends Extension_customTags {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'DataExportFullQueryFileLayoutHeader',
			'DataExportBeforeFileLineCommit',
			'DataImportBeforeSave'
		), null, $this);
	}
	
	public function DataImportBeforeSave(&$items, &$Product){
		if (!isset($items['v_custom_tags'])) return;
		
		if (!empty($items['v_custom_tags'])){
			$cID = 0;
			$pID = $Product->products_id;
				$tagArr = ParseTagString($items['v_custom_tags']);
				foreach($tagArr as $iTag){
					$iTag = trim($iTag);
					$CustomTag = Doctrine_Core::getTable('CustomTags')->findOneByTagName($iTag);
					if(!$CustomTag){
						$CustomTag = new CustomTags();
						$CustomTag->tag_name = $iTag;
						$CustomTag->tag_status = 1;
						$CustomTag->save();
					}
					$tagId = $CustomTag->tag_id;
					$ProductTags = Doctrine_Core::getTable('TagsToProducts')->findOneByTagIdAndProductsIdAndCustomersId($tagId, $pID, $cID);
					if(!$ProductTags){
						$ProductTags = new TagsToProducts();
						$ProductTags->tag_id = $tagId;
						$ProductTags->products_id = $pID;
						$ProductTags->customers_id = $cID;
						$ProductTags->save();
					}

				}

		}
	}
	
	public function DataExportFullQueryFileLayoutHeader(&$dataExport){
		$dataExport->setHeaders(array(
			'v_custom_tags'
		));

	}
	
	public function DataExportBeforeFileLineCommit(&$productRow){
		$tagList = '';
		$QProductTagsList = Doctrine_Query::create()
				->from('CustomTags c')
				->leftJoin('c.TagsToProducts tp')
				->where('tp.products_id = ?', $productRow['products_id'])
				->andWhere('c.tag_status = ?','1')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($QProductTagsList) > 0){

			foreach($QProductTagsList as $pTags){
				$tagList .= "'".$pTags['tag_name']."' ";
			}
		}
		$productRow['v_custom_tags'] = $tagList;
	}
}
?>