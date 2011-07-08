<?php
/*
	Product Purchase Type: Download

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/catalog/classes/product/purchase_types/download.php');

class OrderCreatorProductPurchaseTypeDownload extends PurchaseType_download {

	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
		$Qdownloads = Doctrine_Query::create()
		->from('ProductsUploads')
		->where('products_id = ?', (int)$ProductObj->getProductsId())
		->andWhere('file_name != ?', '')
		->andWhere('type = ?', 'download')
		->execute();
			
		if ($Qdownloads->count() > 0){
			foreach($Qdownloads->toArray() as $dInfo){
				$Download = new OrdersProductsDownload();
				$Download->orders_products_filename = $dInfo['file_name'];
				$Download->download_maxdays = sysConfig::get('DOWNLOAD_MAX_DAYS');
				$Download->download_maxcount = sysConfig::get('DOWNLOAD_MAX_COUNT');
				$Download->download_count = '0';
				if(isset($_POST['estimateOrder'])){
					$Download->is_estimate = 1;
				}else{
					$Download->is_estimate = 0;
				}
				$CollectionObj->OrdersProductsDownload->add($Download);
			}
		}
	}
}
?>