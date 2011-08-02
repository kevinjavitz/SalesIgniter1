<?php
if (!class_exists('productListing')){
	require(sysConfig::getDirFsCatalog() . 'includes/classes/product_listing.php');
}

class productListing_date extends productListing {

	public function __construct(){
		parent::__construct();
		$this->allowSorting = false;
	}

	public function buildPagerBar(){
		if ($this->usePaging === false){
			$Result = $this->query->execute();
			return array(
				'Products' => $Result
			);
		}

		$currentPage = (isset($_GET['page']) ? (int)$_GET['page'] : 1);
		$limitResults = (isset($_GET['limit']) ? (int)$_GET['limit'] : 10);

		$listingPager = new Doctrine_Pager($this->query, $currentPage, $limitResults);
		$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');
		$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 5
		));

		$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
		$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default productListingRowPagerLink">{%page}</a>');
		$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all productListingRowPagerLinkActive">{%page}</span>');
		$pager = $pagerLayout->getPager();
		$Result = $pager->execute();

		$this->templateData['pagerLinks'] = ($pager->haveToPaginate() ? $pagerLayout->display(array(), true) : '1 '.sysLanguage::get('PRODUCT_LISTING_OF').' 1');

		return array(
			'Products'    => $Result,
			'pager'       => $listingPager,
			'pagerLayout' => $pagerLayout
		);
	}

	/*private function compareDates($date1, $date2){

		return tep_date_short($date1) > tep_date_short($date2)
	}*/


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
			$pagerInfo = $this->buildPagerBar();
			$Products = $pagerInfo['Products']->toArray(true);
		}

		if (isset($pagerInfo['pagerLayout'])){
			$PagerLayout = $pagerInfo['pagerLayout'];
			$Pager = $PagerLayout->getPager();
		}

		if ((isset($Pager) && $Pager->getNumResults() > 0) || (!isset($Pager) && sizeof($Products) > 0)){
			$list_box_contents = array();
			if (sizeof($Products) > 0){
				$date_start = date("Y-m-d");
				$max = -1;
				foreach($this->templateData['listingHeaders'] as $idx => $header){
					$key = $header['key'];
					if($key > $max){
						$max = $key;
					}
				}
				foreach($Products as $pInfo){

					$this->listedProductsIDS[] = $pInfo['products_id'];
					$product = new product($pInfo['products_id']);

					if ($product->isValid() === false) continue;

					$class = "dater";
					$date_added = date("Y-m-d", strtotime($product->getDateAdded()));

					if($date_added != $date_start){
						$this->templateData['rowSettings'][$rows] = array(
							'addCls' => 'productListingDate'
						);
						if($max > 0){
							$this->templateData['listingColumns'][0][$rows] = array(
								'align'  => 'center',
								'valign' => 'middle',
								'addCls' => 'ui-widget-header',
								'text'   => 'Entered on date: &nbsp;&nbsp;'.$date_added
							);
						}
						for($i=1;$i<=$max;$i++){
							$this->templateData['listingColumns'][$i][$rows] = array(
								'align'  => 'center',
								'valign' => 'middle',
								'addCls' => 'ui-widget-header',
								'text'   => ''
							);
						}

						$date_start = date('Y-m-d',strtotime($product->getDateAdded()));
						$rows++;
					}

					$this->templateData['rowSettings'][$rows] = array(
						'addCls' => 'productListingRow-' . $class.'-'.$pInfo['products_id']
					);
					foreach($this->templateData['listingHeaders'] as $idx => $header){
						$key = $header['key'];
						$align = $header['align'];
						$text = $this->columnInfo[$key]['showModule']->show($product);
						if ($header['disabled'] === true){
							if ($text !== false){
								$this->templateData['listingHeaders'][$idx]['disabled'] = false;
							}
						}

						$this->templateData['listingColumns'][$key][$rows] = array(
							'align'  => $align,
							'addCls' => 'maind',
							'text'   => $text
						);
					}

					$rows++;
				}
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

				foreach($this->templateData['listingColumns'][$key] as $row => $rInfo){
					$boxContents[$row]['addCls'] = $this->templateData['rowSettings'][$row]['addCls'];
					$boxContents[$row][$col] = array(
						'addCls' => $rInfo['addCls'],
						'align'  => $rInfo['align'],
						'text'   => ($rInfo['text'] === false ? '&nbsp;' : $rInfo['text'])
					);
				}
				$col++;
			}
		}

		$TemplateVars = array(
			'listingData' => $boxContents
		);

		if (isset($this->templateData['pagerLinks'])){
			$TemplateVars['pager'] = $this->templateData['pagerLinks'];
		}

		$listing->setVars($TemplateVars);

		return $listing->parse();
	}
}
?>