<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_royaltiesSystem extends ExtensionBase {

	public function __construct(){
		parent::__construct('royaltiesSystem');
	}
	
	public function init(){
		global $appExtension;

		if ($appExtension->isAdmin()){
			EventManager::attachEvents(array('BoxMarketingAddLink',
				'RentalQueueProductSent'
			), null, $this);
		}

		EventManager::attachEvents(array(
			'OrdersProductsDownloadUpdateDownloadsAfterSave',
			'OrdersProductsStreamUpdateViewsAfterSave',
			'CustomersMembershipStreamUpdateViewsAfterSave',
			'NewProductDownloadExistsBeforeSave',
			'NewProductDownloadNewBeforeSave',
			'NewProductStreamExistsBeforeSave',
			'NewProductStreamNewBeforeSave',

		), null, $this);
	}

	public function BoxMarketingAddLink(&$contents){
		$contents['children'][] = array(
			'link' => itw_app_link('appExt=royaltiesSystem', 'show_reports', 'default', 'SSL'),
			'text' => 'Downloads/Streaming Report'
		);

		$contents['children'][] = array(
			'link' => itw_app_link('appExt=royaltiesSystem', 'show_reports', 'totals', 'SSL'),
			'text' => 'Downloads/Streaming Report Totals'
		);
	}

	public function OrdersProductsDownloadUpdateDownloadsAfterSave(&$OrdersDownload){
		global $userAccount;
		
		//check to see if royalty last date is smaller than datenow with config is it is than now royalty is added 
		$secs = (float)sysConfig::get('EXTENSION_ROYALTIES_SYSTEM_DOWNLOAD_DAYS_COUNT') * 24 * 3600;
		$royaltiesSystemRoyaltiesEarnedCheck = Doctrine_Query::create()
		->from('RoyaltiesSystemRoyaltiesEarned')
		->where('customers_id = ?', (int)$userAccount->getcustomerId())
		->andWhere('download_id = ?', (int)$OrdersDownload->ProductsDownloads->download_id)
		->andWhere('products_id = ?', (int)$OrdersDownload->ProductsDownloads->products_id)
		->andWhere('orders_id = ?', (int)$OrdersDownload->orders_id)
		->orderBy('date_added DESC')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		if(is_null($royaltiesSystemRoyaltiesEarnedCheck[0]['customers_id']))
		{
			$productsDownloadsTable = Doctrine_Core::getTable('ProductsDownloads');
			$contentProvidersId = $productsDownloadsTable->findOneByDownloadId($OrdersDownload->download_id,Doctrine_Core::HYDRATE_RECORD);

			$royaltiesSystemRoyaltiesEarned = new RoyaltiesSystemRoyaltiesEarned();
			$royaltiesSystemRoyaltiesEarned->customers_id = (int)$userAccount->getcustomerId();
			$royaltiesSystemRoyaltiesEarned->orders_id = (int)$OrdersDownload->orders_id;
			$royaltiesSystemRoyaltiesEarned->content_provider_id = (int)$contentProvidersId->content_provider_id;
			$royaltiesSystemRoyaltiesEarned->products_id = (int)$OrdersDownload->ProductsDownloads->products_id;
			$royaltiesSystemRoyaltiesEarned->download_id = (int)$OrdersDownload->ProductsDownloads->download_id;
			$royaltiesSystemRoyaltiesEarned->date_added = date("Y-m-d h:i:s");
			$royaltiesSystemRoyaltiesEarned->purchase_type = 'download';
			$royaltiesSystemRoyaltiesEarned->royalty = $OrdersDownload->ProductsDownloads->royalty_fee;
			$royaltiesSystemRoyaltiesEarned->save();
		}

	}

	public function OrdersProductsStreamUpdateViewsAfterSave(&$OrdersStream){
		global $userAccount;

		$royaltiesSystemRoyaltiesEarnedCheck = Doctrine_Query::create()
		->from('RoyaltiesSystemRoyaltiesEarned')
		->where('customers_id = ?', (int)$userAccount->getcustomerId())
		->andWhere('streaming_id = ?', (int)$OrdersStream->ProductsStreams->stream_id)
		->andWhere('products_id = ?', (int)$OrdersStream->ProductsStreams->products_id)
		->andWhere('orders_id = ?', (int)$OrdersStream->orders_id)
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		
		//every download a royalty is counted or onetime royalty(check if products, streaming and customer exists if not then add
		if (is_null($royaltiesSystemRoyaltiesEarnedCheck[0]['customers_id'])){
			$ProductsStreamsTable = Doctrine_Core::getTable('ProductsStreams');
			$contentProvidersId = $ProductsStreamsTable->findOneByStreamId($OrdersStream->ProductsStreams->stream_id,Doctrine_Core::HYDRATE_RECORD);

			$royaltiesSystemRoyaltiesEarned = new RoyaltiesSystemRoyaltiesEarned();
			$royaltiesSystemRoyaltiesEarned->customers_id = (int)$userAccount->getcustomerId();
			$royaltiesSystemRoyaltiesEarned->products_id = (int)$OrdersStream->ProductsStreams->products_id;
			$royaltiesSystemRoyaltiesEarned->streaming_id = (int)$OrdersStream->ProductsStreams->stream_id;
			$royaltiesSystemRoyaltiesEarned->orders_id = (int)$OrdersStream->orders_id;
			$royaltiesSystemRoyaltiesEarned->content_provider_id = (int)$contentProvidersId->content_provider_id;
			$royaltiesSystemRoyaltiesEarned->date_added = date("Y-m-d h:i:s");
			$royaltiesSystemRoyaltiesEarned->purchase_type = 'stream';
			$royaltiesSystemRoyaltiesEarned->royalty = $OrdersStream->ProductsStreams->royalty_fee;
			$royaltiesSystemRoyaltiesEarned->save();
		}
	}
	
	public function CustomersMembershipStreamUpdateViewsAfterSave(&$View){

		$royaltiesSystemRoyaltiesEarnedCheck = Doctrine_Query::create()
		->from('RoyaltiesSystemRoyaltiesEarned')
		->where('customers_id = ?', (int)$View->customers_id)
		->andWhere('streaming_id = ?', (int)$View->ProductsStreams->stream_id)
		->andWhere('products_id = ?', (int)$View->ProductsStreams->products_id)
		->andWhere('orders_id = ?', (int)$View->orders_id)
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		
		//every download a royalty is counted or onetime royalty(check if products, streaming and customer exists if not then add
		if (is_null($royaltiesSystemRoyaltiesEarnedCheck[0]['customers_id'])){
			$ProductsStreamsTable = Doctrine_Core::getTable('ProductsStreams');
			$contentProvidersId = $ProductsStreamsTable->findOneByStreamId($View->ProductsStreams->stream_id,Doctrine_Core::HYDRATE_RECORD);

			$royaltiesSystemRoyaltiesEarned = new RoyaltiesSystemRoyaltiesEarned();
			$royaltiesSystemRoyaltiesEarned->customers_id = (int)$View->customers_id;
			$royaltiesSystemRoyaltiesEarned->products_id = (int)$View->ProductsStreams->products_id;
			$royaltiesSystemRoyaltiesEarned->streaming_id = (int)$View->ProductsStreams->stream_id;
			$royaltiesSystemRoyaltiesEarned->orders_id = (int)$View->orders_id;
			$royaltiesSystemRoyaltiesEarned->content_provider_id = (int)$contentProvidersId->content_provider_id;
			$royaltiesSystemRoyaltiesEarned->date_added = date("Y-m-d h:i:s");
			$royaltiesSystemRoyaltiesEarned->purchase_type = 'stream';
			$royaltiesSystemRoyaltiesEarned->royalty = $View->ProductsStreams->royalty_fee;
			$royaltiesSystemRoyaltiesEarned->save();
		}
	}
	
	public function NewProductStreamExistsBeforeSave(&$Stream, $idx){
		$Stream->content_provider_id = $_POST['stream_content_provider_id'][$idx];
		$Stream->royalty_fee = $_POST['stream_royalty_fee'][$idx];
	}
	
	public function NewProductStreamNewBeforeSave(&$Stream, $idx){
		$Stream->content_provider_id = $_POST['stream_content_provider_id_new'][$idx];
		$Stream->royalty_fee = $_POST['stream_royalty_fee_new'][$idx];
	}
	
	public function NewProductDownloadExistsBeforeSave(&$Download, $idx){
		$Download->content_provider_id = $_POST['download_content_provider_id'][$idx];
		$Download->royalty_fee = $_POST['download_royalty_fee'][$idx];
	}
	
	public function NewProductDownloadNewBeforeSave(&$Download, $idx){
		$Download->content_provider_id = $_POST['download_content_provider_id_new'][$idx];
		$Download->royalty_fee = $_POST['download_royalty_fee_new'][$idx];
	}

	public function RentalQueueProductSent(&$RentedProduct){
		$ProductsRoyaltiesTable = Doctrine_Core::getTable('RoyaltiesSystemProductsRoyalties');
		$ProductsRoyalties = $ProductsRoyaltiesTable->findOneByProductsId($RentedProduct->products_id);

		if ($ProductsRoyalties->content_provider_id) {
			if (strpos($ProductsRoyalties->royalty_fee, '%') === false) {
				$royaltyFee = $ProductsRoyalties->royalty_fee;
			} else {
				$royaltyFee = $ProductsRoyalties->products_price_rental * ($ProductsRoyalties->royalty_fee / 100);
			}
			$royaltiesSystemRoyaltiesEarned = new RoyaltiesSystemRoyaltiesEarned();
			//$royaltiesSystemRoyaltiesEarned = $royaltiesSystemRoyaltiesEarned->create();
			$royaltiesSystemRoyaltiesEarned->products_id = $RentedProduct->products_id;
			$royaltiesSystemRoyaltiesEarned->customers_id = $RentedProduct->customers_id;
			$royaltiesSystemRoyaltiesEarned->content_provider_id = $ProductsRoyalties->content_provider_id;
			$royaltiesSystemRoyaltiesEarned->rented_products_id = $RentedProduct->rented_products_id;
			$royaltiesSystemRoyaltiesEarned->products_barcode = $RentedProduct->products_barcode;
			$royaltiesSystemRoyaltiesEarned->shipment_date = $RentedProduct->shipment_date;
			$royaltiesSystemRoyaltiesEarned->arrival_date = $RentedProduct->arrival_date;
			$royaltiesSystemRoyaltiesEarned->date_added = date("Y-m-d h:i:s");
			$royaltiesSystemRoyaltiesEarned->royalty = $royaltyFee;
			$royaltiesSystemRoyaltiesEarned->purchase_type = 'rental';
			$royaltiesSystemRoyaltiesEarned->save();
		}
	}
}
?>