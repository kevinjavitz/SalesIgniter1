<?php
if (!class_exists('productListing')){
	require(sysConfig::getDirFsCatalog() . 'includes/classes/product_listing.php');
}

class productListing_row extends productListing {

	public function __construct(){
		parent::__construct();
	}

	private function getDoctrinePager($useQuery, $useLimit = null){
		$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
		$limitsArray = explode(',',sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT_ARRAY'));
		$limitResults = sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT');
		if((isset($_GET['limit']) && (int)$_GET['limit'] > 0 && (int)$_GET['limit'] <= 25) || ((int)$_GET['limit'] >= 25 && in_array((int)$_GET['limit'],$limitsArray)) ){
			$limitResults = (int)$_GET['limit'];
		}
		//$limitResults = (isset($_GET['limit']) ? (int)$_GET['limit'] : 10);

		if (is_null($useLimit) === false){
			$limitResults = $useLimit;
		}

		$listingPager = new Doctrine_Pager($useQuery, $currentPage, $limitResults);
		$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');
		$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 5
		));

		$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
		$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default productListingRowPagerLink">{%page}</a>');
		$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all productListingRowPagerLinkActive">{%page}</span>');

		return $pagerLayout;
	}

	public function buildPagerBar(){
		if ($this->usePaging === false){
			$Result = $this->query->execute();
			return array(
				'Products' => $Result
			);
		}

		if (sysConfig::get('PRODUCT_LISTING_HIDE_NO_INVENTORY') == 'True'){
			$PagerLayout = $this->getDoctrinePager($this->query, 999999);
		}else{
			$PagerLayout = $this->getDoctrinePager($this->query);
		}

		$Pager = $PagerLayout->getPager();

		if ($this->allowSorting === true){
			$getVars = tep_get_all_get_params(array('action', $this->sortKey));
			parse_str($getVars, $getArr);
			$hiddenFields = '';
			create_hidden_fields($getArr,&$hiddenFields);

			$sortForm = htmlBase::newElement('form')
			->attr('name', 'sorter')
			->attr('method', 'get')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action', $this->sortKey))))
			->html($hiddenFields);

			$sortBox = htmlBase::newElement('selectbox')
			->setName($this->sortKey)
			->attr('onchange', 'this.form.submit()');

			if (isset($_GET[$this->sortKey])){
				$sortBox->selectOptionByValue($_GET[$this->sortKey]);
			}

			$sortBox->addOption('none', sysLanguage::get('PRODUCT_LISTING_SELECT_SORT_OPTION'));
			$removeSorter = true;
			foreach($this->columnInfo as $cInfo){
				if ($cInfo['enabled'] === true && $cInfo['allow_sort'] === true){
					$sortBox->addOption($cInfo['sort_key'] . '_a', $cInfo['heading'] . ' ' . sysLanguage::get('PRODUCT_LISTING_ASC'));
					$sortBox->addOption($cInfo['sort_key'] . '_d', $cInfo['heading'] . ' ' . sysLanguage::get('PRODUCT_LISTING_DESC'));
					$removeSorter = false;
				}
			}

			if ($removeSorter === false){
				$this->templateData['sortForm'] =& $sortForm;
				$this->templateData['sortBox'] =& $sortBox;
			}
		}
		$Result = $Pager->execute();

		$this->templateData['pagerLinks'] = ($Pager->haveToPaginate() ? $PagerLayout->display(array(), true) : '1 '.sysLanguage::get('PRODUCT_LISTING_OF').' 1');

		return array(
			'Products'    => $Result,
			'pagerLayout' => $PagerLayout
		);
	}

	function draw(){
		$rows = 1;

		$this->templateData = array(
			'headerSettings' => array('addCls' => 'ui-widget-header'),
			'listingHeaders' => array(),
			'rowSettings'    => array(),
			'listingColumns' => array()
		);

		$columns = $this->getColumnList();
		foreach ($this->columnList as $key) {
			$col = $this->columnInfo[$key];
			if ($col['enabled'] === true && $col['allow_sort'] === false){
				$this->templateData['listingHeaders'][] = array(
					'key'      => $key,
					'sorts'    => $col['allow_sort'],
					'sort_key' => ($col['allow_sort'] === true ? $col['sort_key'] : ''),
					'align'    => $col['heading_align'],
					'text'     => (strlen($col['heading']) > 0 ? $col['heading'] : '&nbsp;'),
					'disabled' => true
				);
				$this->templateData['listingColumns'][$key] = array();
			}
		}

		if (isset($this->loadedData)){
			$Products = $this->loadedData;
		}else{
			$this->listedProducts = array();
			$this->listedProductsIDS = array();
			if (sysConfig::get('PRODUCT_LISTING_HIDE_NO_INVENTORY') == 'True'){
				$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
				$limitsArray = explode(',',sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT_ARRAY'));
				$limitResults = sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT');
				if((isset($_GET['limit']) && (int)$_GET['limit'] > 0 && (int)$_GET['limit'] <= 25) || ((int)$_GET['limit'] >= 25 && in_array((int)$_GET['limit'],$limitsArray)) ){
					$limitResults = (int)$_GET['limit'];
				}

			//while(true){
				$query = clone $this->query;
				//$query->limit($limitResults);
				//$query->offset($limitResults*($currentPage-1));

				EventManager::notify('ProductListingQueryAfterExecute', &$query);

				$Products = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				//if(count($Products) == 0){
				//	break;
				//}

				//$currentPage++;
				//if(count($this->listedProducts) > $limitResults){
				//	break;
				//}
			//}
			}else{
				$pagerInfo = $this->buildPagerBar();
				$Products = $pagerInfo['Products']->toArray(true);
				EventManager::notify('ProductListingQueryAfterExecute', &$Products);
			}
		}
		if(isset($Products)){
			foreach($Products as $pInfo){
				$product = new product($pInfo['products_id']);
				if ($product->isValid() === false) continue;

				if (sysConfig::get('PRODUCT_LISTING_HIDE_NO_INVENTORY') == 'True'){
					$hasSomeInv = false;
					$PurchaseTypes = $product->productInfo['typeArr'];
					foreach($PurchaseTypes as $typeName){
						$purchaseTypeCls = $product->getPurchaseType($typeName);
						if ($purchaseTypeCls && $purchaseTypeCls->hasInventory() === true){
							$hasSomeInv = true;
						}
					}

					if ($hasSomeInv === false){
						continue;
					}
				}
				$this->listedProductsIDS[] = $pInfo['products_id'];
				$this->listedProducts[] = $product;
			}
		}

		if (sysConfig::get('PRODUCT_LISTING_HIDE_NO_INVENTORY') == 'True'){
			if (sizeof($this->listedProductsIDS) > 0){
				$PagerLayout = $this->getDoctrinePager('SELECT count(products_id) FROM Products WHERE products_id IN(' . implode(',', $this->listedProductsIDS) . ')');
				$Pager = $PagerLayout->getPager();
				$Pager->execute();
				$this->templateData['pagerLinks'] = ($Pager->haveToPaginate() ? $PagerLayout->display(array(), true) : '1 of 1');
			}
		}

		if (sizeof($this->listedProducts) > 0){
			    $limitp = -1;
				foreach($this->listedProducts as $product){
					if (sysConfig::get('PRODUCT_LISTING_HIDE_NO_INVENTORY') == 'True'){
						$limitp++;
						if($limitp >= $currentPage*$limitResults){
							break;
						}
						if($limitp < ($currentPage-1)*$limitResults){
							continue;
						}

					}
					if (($rows/2) == floor($rows/2)) {
						$class = 'even';
					} else {
						$class = 'odd';
					}
					$this->templateData['rowSettings'][$rows] = array(
						'addCls' => 'productListingRow-' . $class
					);

					foreach($this->templateData['listingHeaders'] as $idx => $header){
						$key = $header['key'];
						$align = $header['align'];
						$purchaseTypeCol = '';
						$text = $this->columnInfo[$key]['showModule']->show($product, &$purchaseTypeCol);

						if ($header['disabled'] === true){
							if ($text !== false){
								$this->templateData['listingHeaders'][$idx]['disabled'] = false;
							}
						}

						$this->templateData['listingColumns'][$key][$rows] = array(
							'align'  => $align,
							'addCls' => 'main',
							'text'   => $text,
							'purchaseType' => $purchaseTypeCol
						);
					}
					$rows++;
				}
			//}
		}else{
			if ($this->showNoProducts === false) return false;

			$div = htmlBase::newElement('div')
			->addClass('ui-widget ui-widget-content ui-corner-all noproducts')
			->html(sysLanguage::get('PRODUCT_LISTING_NO_PRODUCTS'))
			->css(array(
				'text-align' => 'center',
				'padding' => '2em'
			));

			$this->templateData['noProducts'] = $div->draw();
		}
		return $this->parseTemplate();
	}

	function parseTemplate(){
		$listing = new Template('product_listing_row.tpl', 'modules');
		if (isset($this->templateData['noProducts'])){
			$listing->setVars(array(
				'listingData' => $this->templateData['noProducts']
			));
			return $listing->parse();
		}

		$boxContents = array();
		$col = 0;
		foreach($this->templateData['listingHeaders'] as $k => $header){
			if ($header['disabled'] === false){
				$key = $header['key'];

				$boxContents[0][$col] = array(
					'align'  => $header['align'],
					'valign' => $header['valign'],
					'addCls' => (isset($this->templateData['headerSettings']['addCls']) ? $this->templateData['headerSettings']['addCls'] : false),
					'text'   => $header['text']
				);

				foreach($this->templateData['listingColumns'][$key] as $row => $rInfo){
					$boxContents[$row]['addCls'] = $this->templateData['rowSettings'][$row]['addCls'];
					$boxContents[$row][$col] = array(
						'addCls' => $rInfo['addCls'],
						'align'  => $rInfo['align'],
						'text'   => ($rInfo['text'] === false ? '&nbsp;' : $rInfo['text']),
						'purchaseType' =>$rInfo['purchaseType']
					);
				}
				$col++;
			}else{
				if (isset($this->templateData['sortBox'])){
					if ($header['sorts'] === true){
						$this->templateData['sortBox']
						->removeOption($header['sort_key'] . '_a')
						->removeOption($header['sort_key'] . '_d');
					}
				}
			}
		}

		$TemplateVars = array(
			'listingData' => $boxContents
		);

		if (isset($this->templateData['sortForm'])){
			$sortForm = $this->templateData['sortForm'];
			$sortForm->append($this->templateData['sortBox']);

			$TemplateVars['sorter'] = $sortForm->draw();
		}

		if (isset($this->templateData['pagerLinks'])){
			$TemplateVars['pager'] = $this->templateData['pagerLinks'];
		}

		$listing->setVars($TemplateVars);

		return $listing->parse();
	}
}
?>