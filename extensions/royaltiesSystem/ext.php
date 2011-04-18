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
			EventManager::attachEvent('BoxMarketingAddLink', null, $this);
		}

		EventManager::attachEvents(array(
			'OrdersProductsDownloadUpdateDownloadsAfterSave',
			'OrdersProductsStreamUpdateViewsAfterSave',
			'CustomersMembershipStreamUpdateViewsAfterSave',
			'NewProductDownloadExistsBeforeSave',
			'NewProductDownloadNewBeforeSave',
			'NewProductStreamExistsBeforeSave',
			'NewProductStreamNewBeforeSave'
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
		$Qroyal = Doctrine_Query::create()
		->from('RoyaltiesSystemViews')
		->where('customers_id = ?', (int)$userAccount->getcustomerId())
		->andWhere('download_id = ?', (int)$OrdersDownload->ProductsDownloads->download_id)
		->andWhere('products_id = ?', (int)$OrdersDownload->ProductsDownloads->products_id)
		->orderBy('date_added DESC')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		
		$dateFuture =  mktime(date("H"), date("i"), date("s")-$secs, date("m"), date("d"), date("y"));
		//get date last added royalty for the same product
		$dateNow =  strtotime($Qroyal[0]['date_added']);
		if ($dateFuture > $dateNow){
			$QRoyaltiesSystemViews = new RoyaltiesSystemViews();
			$QRoyaltiesSystemViews->customers_id = (int)$userAccount->getcustomerId();
			$QRoyaltiesSystemViews->products_id = (int)$OrdersDownload->ProductsDownloads->products_id;
			$QRoyaltiesSystemViews->download_id = (int)$OrdersDownload->ProductsDownloads->download_id;
			$QRoyaltiesSystemViews->date_added = date("Y-m-d h:i:s");
			$QRoyaltiesSystemViews->royalty = $OrdersDownload->ProductsDownloads->royalty_fee;
			$QRoyaltiesSystemViews->save();
		}
	}

	public function OrdersProductsStreamUpdateViewsAfterSave(&$OrdersStream){
		global $userAccount;
		$times = 1;

		$Qroyal = Doctrine_Query::create()
		->from('RoyaltiesSystemViews')
		->where('customers_id = ?', (int)$userAccount->getcustomerId())
		->andWhere('streaming_id = ?', (int)$OrdersStream->ProductsStreams->stream_id)
		->andWhere('products_id = ?', (int)$OrdersStream->ProductsStreams->products_id)
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		//every download a royalty is counted or onetime royalty(check if products, streaming and customer exists if not then add
		if (count($Qroyal) < $times){
			$QRoyaltiesSystemViews = new RoyaltiesSystemViews();
			$QRoyaltiesSystemViews->customers_id = (int)$userAccount->getcustomerId();
			$QRoyaltiesSystemViews->products_id = (int)$OrdersStream->ProductsStreams->products_id;
			$QRoyaltiesSystemViews->streaming_id = (int)$OrdersStream->ProductsStreams->stream_id;
			$QRoyaltiesSystemViews->date_added = date("Y-m-d h:i:s");
			$QRoyaltiesSystemViews->royalty = $OrdersStream->ProductsStreams->royalty_fee;
			$QRoyaltiesSystemViews->save();
		}
	}
	
	public function CustomersMembershipStreamUpdateViewsAfterSave(&$View){
		$times = 1;

		$Qroyal = Doctrine_Query::create()
		->from('RoyaltiesSystemViews')
		->where('customers_id = ?', (int)$View->customers_id)
		->andWhere('streaming_id = ?', (int)$View->ProductsStreams->stream_id)
		->andWhere('products_id = ?', (int)$View->ProductsStreams->products_id)
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		//every download a royalty is counted or onetime royalty(check if products, streaming and customer exists if not then add
		if (count($Qroyal) < $times){
			$QRoyaltiesSystemViews = new RoyaltiesSystemViews();
			$QRoyaltiesSystemViews->customers_id = (int)$View->customers_id;
			$QRoyaltiesSystemViews->products_id = (int)$View->ProductsStreams->products_id;
			$QRoyaltiesSystemViews->streaming_id = (int)$View->ProductsStreams->stream_id;
			$QRoyaltiesSystemViews->date_added = date("Y-m-d h:i:s");
			$QRoyaltiesSystemViews->royalty = $View->ProductsStreams->royalty_fee;
			$QRoyaltiesSystemViews->save();
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
}
?>