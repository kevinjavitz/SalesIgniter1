<?php
class productListing_productsMembershipStream
{

	public function show(&$productClass, &$purchaseTypesCol){
		if ($productClass->canRent('member_stream')){
			$viewStreamButton = htmlBase::newElement('button')
				->setText(sysLanguage::get('TEXT_BUTTON_VIEW_STREAM'))
				->setHref(itw_app_link('action=stream_product&products_id=' . $productClass->getID()), true);

			return $viewStreamButton->draw();
		}
		return false;
	}
}

?>