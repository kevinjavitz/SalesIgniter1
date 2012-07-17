<?php
class streamProducts_catalog_product_info extends Extension_streamProducts
{

	public function __construct(){
		parent::__construct('streamProducts');
	}

	public function load(){
		if ($this->isEnabled() === false) {
			return;
		}

		EventManager::attachEvents(array(
			'ProductInfoTabHeader',
			'ProductInfoTabBody'
		), null, $this);
	}

	public function ProductInfoTabHeader(&$product){
		if ($this->hasPreview($product->productInfo) === false){
			$return = '';
		} else{
			$return = '<li><a href="#tabPreview"><span>' . sysLanguage::get('TAB_STREAMING_PREVIEW') . '</span></a></li>';
		}
		return $return;
	}

	public function ProductInfoTabBody(&$product){
		if ($this->hasPreview($product->productInfo) === false){
			$return = '';
		} else{
			$PreviewStream = $this->getPreview($product->productInfo);
			$streamId = $PreviewStream['stream_id'];
			$Provider = $this->getProviderModule(
				$PreviewStream['ProductsStreamProviders']['provider_module'],
				$PreviewStream['ProductsStreamProviders']['provider_module_settings']
			);

			if ($Provider->userHasPermission($streamId) === false){
				$content = sysLanguage::get('TEXT_INFO_STREAM_PERMISSION_DENIED');
			} else{
				$div = htmlBase::newElement('div')
					->setId('streamPlayer')
					->attr('data-pID', $product->getID())
					->attr('data-sID', $streamId)
					->css(array(
					'display'      => 'block',
					'width'        => '600px',
					'height'       => '450px',
					'margin-left'  => 'auto',
					'margin-right' => 'auto'
				));

				$content = $div->draw();
			}
			$return = '<div id="tabPreview"><input type="hidden" id="pID" name="pID" value="'.$product->getID().'"><input type="hidden" id="sID" name="sID" value="'.$streamId.'">'. $content .'</div>';
		}
		return $return;
	}
}

?>