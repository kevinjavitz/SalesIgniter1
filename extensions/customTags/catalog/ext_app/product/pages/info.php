<?php
/*
	Custom Tags Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class customTags_catalog_product_info extends Extension_customTags {
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		EventManager::attachEvents(array(
			'ProductInfoAfterInfo'
		), null, $this);
	}

	public function ProductInfoAfterInfo(&$product){
		global $userAccount;
		$QProductTagsList = Doctrine_Query::create()
		->from('CustomTags c')
		->leftJoin('c.TagsToProducts tp')
		->where('tp.products_id = ?', $product->getID())
		->andWhere('c.tag_status = ?','1')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($QProductTagsList) > 0){
			$tagList = sysLanguage::get('TEXT_TAGS_LIST');
		}else{
			$tagList = sysLanguage::get('TEXT_TAGS_LIST_ADD');
		}
		$cid = 0;
		if($userAccount->isLoggedIn()){
			$cId = $userAccount->getCustomerId();
		}
		foreach($QProductTagsList as $pTags){
			$tag = $pTags['tag_name'];
			$QProductTags = Doctrine_Query::create()
				->from('CustomTags ct')
				->leftJoin('ct.TagsToProducts tp')
				->where('ct.tag_name = ?', $tag)
				->andWhere('tp.products_id = ?', $product->getID())
				->andWhere('tp.customers_id = ?', $cId)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$tagExt = '';
			if(count($QProductTags) > 0){
				$tagExt = '<span class="ui-icon ui-icon-close deleteBut" style="display:inline-block" prodid="'.$product->getID().'" tagid="'.$pTags['tag_id'].'"></span><span class="ui-icon ui-icon-pencil editBut" style="display:inline-block" prodid="'.$product->getID().'" tags_name="'.$pTags['tag_name'].'" tagid="'.$pTags['tag_id'].'"></span>';
			}
			$tagList .= '<a href="'.itw_app_link('appExt=customTags&tag_name='.urlencode($pTags['tag_name']),'search_tag','default').'">'.$tag.'</a>'.$tagExt.'&nbsp;&nbsp;&nbsp;';
		}

		$productTagsForm = htmlBase::newElement('form')
		->attr('name','product_tags')
		->attr('method','post')
		->attr('action', itw_app_link('action=saveTags&products_id='.$product->getID(), 'product', 'info'));

		$htmlProductTags = htmlBase::newElement('input')
		->setName('tags_names')
		->setLabel(sysLanguage::get('TEXT_PRODUCT_ADD_TAGS'))
		->setLabelPosition('before');

		$htmlButton = htmlBase::newElement('button')
		->setName('submitTags')
		->setText(sysLanguage::get('TEXT_SAVE_TAGS'))
		->setType('submit');

		$productTagsForm->append($htmlProductTags)->append($htmlButton);
		$tagForm = '';
		if($userAccount->isLoggedIn()){
			$tagForm = $productTagsForm->draw();
		}
		return $tagList.'<br/>'.$tagForm;

	}

}
?>