<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class gallery_catalog_product_info extends Extension_gallery {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductInfoTabHeader',
			'ProductInfoTabBody'
		), null, $this);
	}
	public function ProductInfoTabHeader(&$product){

			$QGallery = Doctrine_Query::create()
			->from('ProductGallery')
			->where('products_id = ?', $product->getID())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($QGallery) == 0){
				$return = '';
			}else{
				$return = '<li><a href="#tabGallery"><span>' . sysLanguage::get('TAB_GALLERY') . '</span></a></li>';
			}
			return $return;
		}

		public function ProductInfoTabBody(&$product){
			$QGallery = Doctrine_Query::create()
			->from('ProductGallery')
			->where('products_id = ?', $product->getID())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($QGallery) == 0){
				$return = '';
			}else{

				$div = htmlBase::newElement('div')
				->css(array(
					'display' => 'block',
					'margin-left' => 'auto',
					'margin-right' => 'auto'
				));
				$imgSrc =  sysConfig::get('DIR_WS_IMAGES');
				$productsImage = '';
				$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=';

				foreach($QGallery as $iGallery){

				$addImage = $thumbUrl  . $iGallery['file_name'];
				$productImageSrc = $addImage . '&width='.sysConfig::get('EXTENSION_GALLERY_THUMBS_WIDTH').'&height='.sysConfig::get('EXTENSION_GALLERY_THUMBS_HEIGHT');

				$productsImage .= '<div style="display:inline-block;width:'.sysConfig::get('EXTENSION_GALLERY_THUMBS_WIDTH').'px;height:'.sysConfig::get('EXTENSION_GALLERY_THUMBS_HEIGHT').'px;padding:10;"><a class="galleryAddon" rel="galleryAddon" href="' . $addImage . '" title="'.$iGallery['comments'].'"><img class="galleryImage" src="' . $productImageSrc . '"><span>'.$iGallery['comments'].'</span></a></div>';

				}
				$div->html($productsImage);
				$content = $div->draw();
				$return = '<div id="tabGallery">' . $content . '</div>';
			}
			return $return;
		}
}
?>