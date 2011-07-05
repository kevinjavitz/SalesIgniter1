<?php
class Extension_streamProducts extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
		/* Membership Class Events --BEGIN-- */
			'LoadUserMembershipInfo',
			'GetUserMembershipPlanInfo',
			'SetUserMembershipPlanBeforeSave',
			'CreateUserMembershipAccountBeforeSave',
		/* Membership Class Events --BEGIN-- */
			'ProductQueryBeforeExecute',
			'OrderQueryBeforeExecute',
			'ApplicationTopActionCheckPost',
			'ApplicationTopAction_buy_stream_product',
			'ApplicationTopAction_stream_product',
			'TemplateHeaderNavAddButton',
			'AccountDefaultAddLinksBlock'
		), null, $this);
		
		require(dirname(__FILE__) . '/providerModules/Abstract.php');
	}
	
	public function bindMethods(&$class){
		if ($class instanceof RentalStoreUser){
			$class->plugins['membership']->bindMethod('setStreamingStartDate', function (&$Membership, $value){
				$Membership->membershipInfo['membership_start_streaming'] = $value;
			});
			
			$class->plugins['membership']->bindMethod('setStreamingEndDate', function (&$Membership, $value){
				$Membership->membershipInfo['membership_end_streaming'] = $value;
			});
			
			$class->plugins['membership']->bindMethod('isAllowedStreaming', function (&$Membership, $value){
				return ($Membership->planInfo['streaming_allowed'] == '1');
			});
			
			$class->plugins['membership']->bindMethod('getStreamingEndDate', function (&$Membership, $toTime = false){
				if ($toTime === true){
					$date = date_parse($Membership->membershipInfo['membership_end_streaming']);
					$return = mktime(0,0,0, $date['month'], $date['day'], $date['year']);
				}else{
					$return = $Membership->membershipInfo['membership_end_streaming'];
				}
				return $return;
			});
			
			$class->plugins['membership']->bindMethod('getStreamingStartDate', function (&$Membership, $toTime = false){
				if ($toTime === true){
					$date = date_parse($Membership->membershipInfo['membership_start_streaming']);
					$return = mktime(0,0,0, $date['month'], $date['day'], $date['year']);
				}else{
					$return = $Membership->membershipInfo['membership_start_streaming'];
				}
				return $return;
			});
			
			$class->plugins['membership']->bindMethod('getStreamingViewPeriod', function (&$Membership){
				return $Membership->planInfo['streaming_views_period'];
			});
			
			$class->plugins['membership']->bindMethod('getStreamingViewTime', function (&$Membership){
				return $Membership->planInfo['streaming_views_time'];
			});
			
			$class->plugins['membership']->bindMethod('getStreamingViewTimePeriod', function (&$Membership){
				return $Membership->planInfo['streaming_views_time_period'];
			});
		}
	}
	
	/*
	 * Pulled from membership class --BEGIN--
	 */
	public function updateStreamingAccessDates($start, $end){
		global $userAccount;
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('membership_start_streaming', '?', $start)
		->set('membership_end_streaming', '?', $end)
		->where('customers_id = ?', $userAccount->getCustomerId())
		->execute();
	}

	public function setStreamingStartDate($value){
		$this->membershipInfo['membership_start_streaming'] = $value;
	}

	public function OrderQueryBeforeExecute(&$orderQuery){
		$orderQuery->leftJoin('op.OrdersProductsStream op_s');
	}

	public function setStreamingEndDate($value){
		$this->membershipInfo['membership_end_streaming'] = $value;
	}

	public function isAllowedStreaming(){
		return ($this->planInfo['streaming_allowed'] == '1');
	}

	public function getStreamingEndDate($toTime = false){
		if ($toTime === true){
			$date = date_parse($this->membershipInfo['membership_end_streaming']);
			$return = mktime(0,0,0, $date['month'], $date['day'], $date['year']);
		}else{
			$return = $this->membershipInfo['membership_end_streaming'];
		}
		return $return;
	}

	public function getStreamingStartDate($toTime = false){
		if ($toTime === true){
			$date = date_parse($this->membershipInfo['membership_start_streaming']);
			$return = mktime(0,0,0, $date['month'], $date['day'], $date['year']);
		}else{
			$return = $this->membershipInfo['membership_start_streaming'];
		}
		return $return;
	}

	public function getStreamingViewPeriod(){
		return $this->planInfo['streaming_views_period'];
	}

	public function getStreamingViewTime(){
		return $this->planInfo['streaming_views_time'];
	}

	public function getStreamingViewTimePeriod(){
		return $this->planInfo['streaming_views_time_period'];
	}
	/*
	 * Pulled from membership class --END--
	 */
	
	/*
	 * Pulled from product class --BEGIN--
	 */
	public function hasPreview($pInfo){
		$return = false;
		if (isset($pInfo['ProductsStreams'])){
			foreach($pInfo['ProductsStreams'] as $sInfo){
				if ($sInfo['is_preview'] == 1){
					$return = true;
					break;
				}
			}
		}
		return $return;
	}
	
	public function getPreview($pInfo){
		$return = false;
		if (is_array($pInfo) && isset($pInfo['ProductsStreams'])){
			foreach($pInfo['ProductsStreams'] as $sInfo){
				if ($sInfo['is_preview'] == 1){
					$return = $sInfo;
					break;
				}
			}
		}elseif (is_numeric($pInfo)){
			$Qcheck = Doctrine_Query::create()
			->from('ProductsStreams s')
			->leftJoin('s.ProductsStreamProviders p')
			->where('s.products_id = ?', (int) $pInfo)
			->andWhere('s.is_preview = ?', 1)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				$return = $Qcheck[0];
			}
		}
		return $return;
	}
	
	public function getStream($productId, $streamId){
		$return = false;

		$Qcheck = Doctrine_Query::create()
		->from('ProductsStreams s')
		->leftJoin('s.ProductsStreamProviders p')
		->where('s.products_id = ?', (int) $productId)
		->andWhere('s.stream_id = ?', (int) $streamId)
		->andWhere('s.is_preview = ?', 0)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			$return = $Qcheck[0];
		}
		
		return $return;
	}
	
	public function getProviderModule($moduleName, $providerSettings = array()){
		$Module = null;
		$file = sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleName . '/module.php';
		if (file_exists($file)){
			require($file);
			$className = 'StreamProvider' . ucfirst($moduleName);
		
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
		->select('count(o.orders_id) as total')
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsStream ops')
		->leftJoin('o.OrdersStatus os')
		->leftJoin('os.OrdersStatusDescription osd')
		->where('o.customers_id = ?', $userAccount->getCustomerId())
		->andWhere('osd.language_id = ?', Session::get('languages_id'));

		EventManager::notify('OrdersListingBeforeExecute', &$Qcheck);

		$Result = $Qcheck->execute(array(), Doctrine::HYDRATE_ARRAY);
		$html = '';
		if ($Result[0]['total'] > 0){
			$streamsLink = htmlBase::newElement('a')->html(sysLanguage::get('BOX_MY_STREAMS_VIEW_LINK'))
			->setHref(itw_app_link('appExt=streamProducts', 'streams', 'default', 'SSL'))
			->draw();
				
			$linkList = htmlBase::newElement('list')
			->css(array(
				'list-style' => 'none',
				'margin' => '1em',
				'padding' => 0
			))
			->addItem('', $streamsLink);
				
			$headingDiv = htmlBase::newElement('div')
			->addClass('main')
			->css(array(
				'font-weight' => 'bold',
				'margin-top' => '1em'
			))
			->html(sysLanguage::get('BOX_HEADING_MY_STREAMS'));
				
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
			->select('count(ops.orders_products_id) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsStream ops')
			->leftJoin('o.OrdersStatus os')
			->leftJoin('os.OrdersStatusDescription osd')
			->where('o.customers_id = ?', $userAccount->getCustomerId())
			->andWhere('osd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck[0]['total'] > 0){
 				$headerNavButtons[] = array(
					'link' => itw_app_link('appExt=streamProducts', 'streams', 'default'),
					'text' => /*sysLanguage::get('HEADER_NAV_LINK_STREAMS')*/'<span style="color:#ff0000">My Streams</span>'
				);
			}
		}
	}
	
	public function ApplicationTopActionCheckPost(&$action){
		if (isset($_POST['buy_stream_product'])) $action = 'buy_stream_product';
		if (isset($_POST['stream_product']))     $action = 'stream_product';
	}
	
	public function ApplicationTopAction_buy_stream_product(){
		global $ShoppingCart;
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));
		$ShoppingCart->addProduct($productsId, 'stream', 1);
		tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
	}
	
	public function ApplicationTopAction_stream_product(){
		global $userAccount, $messageStack;
		$productsId = (isset($_POST['products_id']) ? $_POST['products_id'] : (isset($_GET['products_id']) ? $_GET['products_id'] : null));
		if ($userAccount->isLoggedIn() === true){
			$Qaccount = Doctrine_Query::create()
			->from('CustomersMembership c')
			->leftJoin('c.Membership m')
			->where('c.customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qaccount > 0){
				if ($Qaccount[0]['Membership']['streaming_allowed'] == '1'){
					$Qcheck = Doctrine_Query::create()
					->select('count(*) as total')
					->from('CustomersStreamingViews')
					->where('customers_id = ?', $userAccount->getCustomerId())
					->andWhere('date_added >= ?', $Qaccount[0]['membership_start_streaming'])
					->andWhere('date_added <= ?', $Qaccount[0]['membership_end_streaming'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qcheck[0]['total'] < $Qaccount[0]['Membership']['streaming_no_of_views']){
						tep_redirect(itw_app_link('appExt=streamProducts&pID=' . $productsId, 'streams', 'listing'));
					}else{
						$messageStack->addSession('pageStack', sysLanguage::get('TEXT_EXCEEDED_VIEWS'), 'warning');
					}
				}else{
					$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_ALLOWED_STREAMING'), itw_app_link('checkoutType=rental','checkout','default','SSL')), 'warning');
				}
			}else{
				$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login')), 'warning');
			}
		}else{
			$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login')), 'warning');
		}
		tep_redirect(itw_app_link('products_id=' . $productsId, 'product', 'info'));
	}

	public function LoadUserMembershipInfo($MembershipClass, &$membershipInfo, &$Qmembership){
		$startStreaming = $MembershipClass->dateToTime($Qmembership[0]['membership_start_streaming']);
		$endStreaming = $MembershipClass->dateToTime($Qmembership[0]['membership_end_streaming']);
		
		$membershipInfo['membership_start_streaming'] = $startStreaming;
		$membershipInfo['membership_end_streaming'] = $endStreaming;
	}
	
	public function GetUserMembershipPlanInfo(&$planInfo, $Qmembership){
		$planInfo['streaming_allowed'] = $Qmembership[0]['streaming_allowed'];
		$planInfo['streaming_no_of_views'] = $Qmembership[0]['streaming_no_of_views'];
		$planInfo['streaming_views_period'] = $Qmembership[0]['streaming_views_period'];
		$planInfo['streaming_views_time'] = $Qmembership[0]['streaming_views_time'];
		$planInfo['streaming_views_time_period'] = $Qmembership[0]['streaming_views_time_period'];
		$planInfo['streaming_access_hours'] = $Qmembership[0]['streaming_access_hours'];
	}
	
	public function SetUserMembershipPlanBeforeSave($MembershipClass, $planInfo, &$CustomersMembership){
		$now = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$newStart = $now;
		$newEnd = $now;
		if ($planInfo['streaming_allowed'] == '1'){
			if ($planInfo['streaming_allowed']){
				$streamViewPeriod = $planInfo['streaming_views_period'];
				if ($streamViewPeriod == 'T'){
					$streamViewTime = $planInfo['streaming_views_time'];
					$streamViewTimePeriod = $planInfo['streaming_views_time_period'];
					if ($streamViewTimePeriod == 'D'){
						$period = 'day';
					}elseif ($streamViewTimePeriod == 'W'){
						$period = 'week';
					}elseif ($streamViewTimePeriod == 'M'){
						$period = 'month';
					}
					$newEnd = strtotime('+' . $streamViewTime . ' ' . $period, $now);
				}else{
					$membershipMonths = $MembershipClass->getMembershipMonths();
					$membershipDays = $MembershipClass->getMembershipMonths();

					$newEnd = strtotime('+' . $membershipMonths . ' month ' . $membershipDays . ' day', $now);
				}
			}
		}
		$CustomersMembership->membership_start_streaming = $newStart;
		$CustomersMembership->membership_end_streaming = $newEnd;
	}
	
	public function CreateUserMembershipAccountBeforeSave($MembershipClass, &$CustomersMembership){
		$now = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$newStart = $now;
		$newEnd = $now;
		if ($MembershipClass->planInfo['streaming_allowed']){
			$MembershipClass->membershipInfo['membership_start_streaming'] = $now;
			$streamViewPeriod = $MembershipClass->getStreamingViewPeriod();
			if ($streamViewPeriod == 'T'){
				$streamViewTime = $MembershipClass->getStreamingViewTime();
				$streamViewTimePeriod = $MembershipClass->getStreamingViewTimePeriod();
				if ($streamViewTimePeriod == 'D'){
					$period = 'day';
				}elseif ($streamViewTimePeriod == 'W'){
					$period = 'week';
				}elseif ($streamViewTimePeriod == 'M'){
					$period = 'month';
				}
				$newEnd = strtotime('+' . $streamViewTime . ' ' . $period, $now);
			}else{
				$membershipMonths = $MembershipClass->getMembershipMonths();
				$membershipDays = $MembershipClass->getMembershipMonths();

				$newEnd = strtotime('+' . $membershipMonths . ' month ' . $membershipDays . ' day', $now);
			}
		}
		
		$CustomersMembership->membership_start_streaming = date('Y-m-d', $newStart);
		$CustomersMembership->membership_end_streaming = date('Y-m-d', $newEnd);
	}
	
	public function UpdateUserMembershipAccountBeforeSave($MembershipClass, &$CustomersMembership){
		$CustomersMembership->membership_start_streaming = (!empty($MembershipClass->membershipInfo['membership_start_streaming']) ? date('Y-m-d', $MembershipClass->membershipInfo['membership_start_streaming']) : '');
		$CustomersMembership->membership_end_streaming = (!empty($MembershipClass->membershipInfo['membership_end_streaming']) ? date('Y-m-d', $MembershipClass->membershipInfo['membership_end_streaming']) : '');
	}
	
	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('streams.*, providers.*')->leftJoin('p.ProductsStreams streams')
		->leftJoin('streams.ProductsStreamProviders providers');
	}
}
?>