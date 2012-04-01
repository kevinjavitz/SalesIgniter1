<?php
class Extension_downloadProducts extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('downloadProducts');
	}
	
	public function init(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductQueryBeforeExecute',
			'ApplicationTopActionCheckPost',
			'ApplicationTopAction_buy_download_product',
			'TemplateHeaderNavAddButton',
			'OrderQueryBeforeExecute',
			'PullDownloadAfterUpdate',
			'AccountDefaultAddLinksBlock'
		), null, $this);
		
		require(dirname(__FILE__) . '/providerModules/Abstract.php');
	}
	
	/*
	 * Pulled from product class --BEGIN--
	 */
	public function getDownload($productId, $downloadId){
		$return = false;

		$Qcheck = Doctrine_Query::create()
		->from('ProductsDownloads d')
		->leftJoin('d.ProductsDownloadProviders p')
		->where('d.products_id = ?', (int) $productId)
		->andWhere('d.download_id = ?', (int) $downloadId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$return = $Qcheck[0];
		}
		
		return $return;
	}

	public function getOrderDownload($orderProductId){
		$return = false;

		$Qcheck = Doctrine_Query::create()
			->select('d.*')
			->from('ProductsDownloads d')
			->leftJoin('OrdersProductsDownloads opd')
			->where('opd.download_id = d.download_id')
			->andWhere('opd.orders_products_id = ?', (int) $orderProductId)
			->fetchArray();
		if ($Qcheck){
			$return = $Qcheck[0];
		}

		return $return;
	}

	public function OrderQueryBeforeExecute(&$orderQuery){
		$orderQuery->leftJoin('op.OrdersProductsDownload op_d');
	}

	public function getProviderInfo($providerId){
		$Qprovider = Doctrine_Query::create()
		->from('ProductsDownloadProviders')
		->where('provider_id = ?', (int) $providerId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		return $Qprovider[0];
	}
	
	public function getProviderModule($moduleName, $providerSettings = array()){
		$Module = null;
		$file = sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $moduleName . '/module.php';
		if (file_exists($file)){
			require($file);
			$className = 'DownloadProvider' . ucfirst($moduleName);
		
			$config = false;
			if (!empty($providerSettings)){
				$config = unserialize($providerSettings);
			}
			
			$Module = new $className($config);
		}
		return $Module;
	}
	/*
	 * Pulled from product class --END--
	 */
	
	public function AccountDefaultAddLinksBlock(&$pageContents){
		global $userAccount;
		$Qcheck = Doctrine_Query::create()
		->select('count(opd.orders_products_id) as total')
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsDownload opd')
		->leftJoin('o.OrdersStatus os')
		->leftJoin('os.OrdersStatusDescription osd')
		->where('o.customers_id = ?', $userAccount->getCustomerId())
		->andWhere('osd.language_id = ?', Session::get('languages_id'));

		$Result = $Qcheck->execute(array(), Doctrine::HYDRATE_ARRAY);
		$html = '';
		if ($Result[0]['total'] > 0){
			$downloadsLink = htmlBase::newElement('a')->html(sysLanguage::get('BOX_MY_DOWNLOADS_VIEW_LINK'))
			->setHref(itw_app_link('appExt=downloadProducts', 'downloads', 'default', 'SSL'))
			->draw();
				
			$linkList = htmlBase::newElement('list')
			->css(array(
				'list-style' => 'none',
				'margin' => '1em',
				'padding' => 0
			))
			->addItem('', $listIcon . $downloadsLink);
				
			$headingDiv = htmlBase::newElement('div')
			->addClass('main')
			->css(array(
				'font-weight' => 'bold',
				'margin-top' => '1em'
			))
			->html(sysLanguage::get('BOX_HEADING_MY_DOWNLOADS'));
				
			$contentDiv = htmlBase::newElement('div')
			->addClass('ui-widget ui-widget-content ui-corner-all')
			->append($linkList);
				
			$html = $headingDiv->draw() . $contentDiv->draw();
		}
		$pageContents .= $html;
	}
	
	public function TemplateHeaderNavAddButton(&$headerNavButtons){
		global $userAccount;
		
		if ($userAccount->isLoggedIn() === true){
			$Qcheck = Doctrine_Query::create()
			->select('count(opd.orders_products_id) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsDownload opd')
			->leftJoin('o.OrdersStatus os')
			->leftJoin('os.OrdersStatusDescription osd')
			->where('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('osd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck[0]['total'] > 0){
 				$headerNavButtons[] = array(
					'link' => itw_app_link('appExt=downloadProducts', 'downloads', 'default'),
					'text' => /*sysLanguage::get('HEADER_NAV_LINK_DOWNLOADS')*/'<span style="color:#ff0000">My Downloads</span>'
				);
			}
		}
	}
	
	public function ApplicationTopActionCheckPost(&$action){
		if (isset($_POST['buy_download_product'])) $action = 'buy_download_product';
	}
	
	public function ApplicationTopAction_buy_download_product(){
		global $ShoppingCart;
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));
		$ShoppingCart->addProduct($productsId, 'download', 1);
		tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
	}

	public function PullDownloadAfterUpdate(&$Download, $ordersId, $ordersProductsId){
		$Download = $this->getOrderDownload($ordersProductsId);
		$Qcheck = tep_db_query('select download_count, download_maxcount from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . $ordersId . '" and orders_products_id = "' . $ordersProductsId . '" and orders_products_filename = "' . $Download['file_name'] . '"');
		$check = tep_db_fetch_array($Qcheck);
		if ($check['download_count'] >= $check['download_maxcount']){
			die('Exceeded number of downloads');
			exit;
		}
		tep_db_query('update ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' set download_count = download_count + 1 where orders_id = "' . $ordersId . '" and orders_products_id = "' . $ordersProductsId . '" and orders_products_filename = "' . $Download['file_name'] . '"');
	}
	
	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('downloads.*, dproviders.*')
		->leftJoin('p.ProductsDownloads downloads')
		->leftJoin('downloads.ProductsDownloadProviders dproviders');
	}
}
?>