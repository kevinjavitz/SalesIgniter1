<?php
if (!class_exists('productListing')){
	require(sysConfig::getDirFsCatalog() . 'includes/classes/product_listing.php');
}

class productListing_col extends productListing {

	public function __construct(){
		parent::__construct();

		$this->setTemplate('product_listing_col.tpl', 'modules');
	}

	public function buildPagerBar(){
		if ($this->usePaging === false){
			$Result = $this->query->execute();
			return array(
				'Products' => $Result
			);
		}

		$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
		$limitsArray = explode(',',sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT_ARRAY'));
		$limitResults = sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT');
		if((isset($_GET['limit']) && (int)$_GET['limit'] > 0 && (int)$_GET['limit'] <= 25) || ((int)$_GET['limit'] >= 25 && in_array((int)$_GET['limit'],$limitsArray)) ){
			$limitResults = (int)$_GET['limit'];
		}
		//$limitResults = (isset($_GET['limit']) ? (int)$_GET['limit'] : sysConfig::get('PRODUCT_LISTING_PRODUCTS_LIMIT'));

		$listingPager = new Doctrine_Pager($this->query, $currentPage, $limitResults);
		$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');
		$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 5
		));

		$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
		$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default productListingColPagerLink">{%page}</a>');
		$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all productListingColPagerLinkActive">{%page}</span>');
		$pager = $pagerLayout->getPager();

		if ($this->allowSorting === true){
			$getVars = tep_get_all_get_params(array('action', 'sort'));
			parse_str($getVars, $getArr);
			$hiddenFields = '';
			foreach($getArr as $k => $v){
				$hiddenFields .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
			}

			$sortForm = htmlBase::newElement('form')
			->attr('name', 'sorter')
			->attr('method', 'get')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'sort'))))
			->html($hiddenFields);

			$sortBox = htmlBase::newElement('selectbox')
			->setName($this->sortKey)
			->attr('onchange', 'this.form.submit()');

			if (isset($_GET[$this->sortKey])){
				$sortBox->selectOptionByValue($_GET[$this->sortKey]);
			}

			$sortBox->addOption('none', sysLanguage::get('PRODUCT_LISTING_SELECT_SORT_OPTION'));
			foreach($this->columnInfo as $cInfo){
				if ($cInfo['enabled'] === true && $cInfo['allow_sort'] === true){
					$sortBox->addOption($cInfo['sort_key'] . '_a', $cInfo['heading'] . ' ' . sysLanguage::get('PRODUCT_LISTING_ASC'));
					$sortBox->addOption($cInfo['sort_key'] . '_d', $cInfo['heading'] . ' ' . sysLanguage::get('PRODUCT_LISTING_DESC'));
				}
			}

			$this->templateData['sortForm'] =& $sortForm;
			$this->templateData['sortBox'] =& $sortBox;
		}

		$Result = $pager->execute();

		$this->templateData['pagerLinks'] = ($pager->haveToPaginate() ? $pagerLayout->display(array(), true) : '1 '.sysLanguage::get('PRODUCT_LISTING_OF'). ' 1 ' . sysLanguage::get('PRODUCT_LISTING_RECORDS'));

		return array(
			'Products'    => $Result,
			'pager'       => $listingPager,
			'pagerLayout' => $pagerLayout
		);
	}

	function draw(){
		$pagerInfo = $this->buildPagerBar();

		$Products = $pagerInfo['Products'];
		if (isset($pagerInfo['pager'])){
			$listingPager = $pagerInfo['pager'];
			$listingPagerLayout = $pagerInfo['pagerLayout'];
		}

		$productListing = new Template($this->templateFile, $this->templateDir);
		if ((isset($listingPager) && $listingPager->getNumResults() > 0) || (!isset($listingPager) && $Products)){
			$listingData = array();
			foreach($Products->toArray(true) as $pInfo){
				$product = new product($pInfo['products_id']);
				if ($product->isValid()){
					$listingData[] = $product;
				}
			}
			unset($product);

			$templateVars = array(
				'listingData' => $listingData
			);

			if (isset($pagerInfo['pager'])){
				$templateVars['pager'] = $this->templateData['pagerLinks'];
			}

			if (isset($this->templateData['sortForm'])){
				$sortForm = $this->templateData['sortForm'];
				$sortForm->append($this->templateData['sortBox']);

				$templateVars['sorter'] = $sortForm->draw();
			}
		}else{
			if ($this->showNoProducts === false) return false;

			$div = htmlBase::newElement('div')
			->addClass('ui-widget ui-widget-content ui-corner-all')
			->html(sysLanguage::get('PRODUCT_LISTING_NO_PRODUCTS'))
			->css(array(
				'text-align' => 'center',
				'padding' => '2em'
			));

			$templateVars = array(
				'listingData' => $div->draw()
			);
		}
		$productListing->setVars($templateVars);

		return $productListing->parse();
	}
}
?>