<?php
/**
 * Html Grid Widget Class
 * @package Html
 */
class htmlWidget_grid implements htmlWidgetPlugin {
	protected $gridElement,
	$gridHeaderElement,
	$gridBodyElement,
	$gridFooterElement;

	public function __construct(){
		$this->gridElement = new htmlElement('table');
		
		$this->gridElement->attr('cellpadding', '2')
		->attr('cellspacing', '0')
		->css('width', '100%')
		->addClass('ui-grid');

		$this->gridHeaderElement = new htmlElement('thead');
		$this->gridBodyElement = new htmlElement('tbody');
		//$this->gridFooterElement = new htmlElement('tfoot');
		
		$this->stripeRows = false;
		$this->bodyRowNum = 0;
		$this->usePages = false;
		$this->useSortBy = false;
		$this->sortByKey = 'sortBy';
		$this->sortByDirKey = 'sortDir';
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->gridElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->gridElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->gridElement->attr('name', $val);
		return $this;
	}

	public function draw(){
		$this->gridElement->append($this->gridHeaderElement)->append($this->gridBodyElement);
		
		if ($this->useSortBy === true){
			$this->gridElement
			->attr('data-sort_key', $this->sortByKey)
			->attr('data-sort_dir_key', $this->sortByDirKey);
		}
		
		$output = $this->gridElement->draw();
		
		if ($this->usePages === true && isset($this->pagerBar)){
			$pageElement = htmlBase::newElement('div')
			->addClass('ui-grid-pager')
			->css(array(
				'text-align' => 'right'
			))->html($this->pagerBar);
			$output .= $pageElement->draw();
		}
		return $output;
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */

	private function parseRow($settings){
		$row = new htmlElement('tr');
		$row->addClass('ui-grid-row');

		if (isset($settings['addCls'])){
			$classes = explode(' ', $settings['addCls']);
			foreach($classes as $className){
				$row->addClass($className);
			}
		}

		if (isset($settings['rowAttr'])){
			foreach($settings['rowAttr'] as $k => $v){
				$row->attr($k, $v);
			}
		}

		if (isset($settings['click'])){
			$row->click($settings['click']);
		}
		return $row;
	}

	private function parseColumn($tag, $settings, $first = false, $last = false){
		global $currencies;
		$colHtml = (isset($settings['text']) && strlen($settings['text']) > 0 ? $settings['text'] : '&nbsp;');
		if (isset($settings['format'])){
			switch($settings['format']){
				case 'int': $colHtml = (int)$colHtml; break;
				case 'float': $colHtml = (float)$colHtml; break;
				case 'string': $colHtml = (string)$colHtml; break;
				case 'currency': $colHtml = $currencies->format($colHtml); break;
			}
		}
		$col = new htmlElement($tag);
		$col->addClass('ui-widget-content')
		->addClass('ui-grid-cell')
		->html($colHtml);

		if ($first === true){
			$col->addClass('ui-grid-cell-first');
		}

		if (isset($settings['align'])){
			$cls = 'leftAlign';
			if ($settings['align'] == 'right'){
				$cls = 'rightAlign';
			}elseif ($settings['align'] == 'center'){
				$cls = 'centerAlign';
			}
			$col->addClass($cls);
		}

		if (isset($settings['valign'])){
			$col->attr('valign', $settings['valign']);
		}

		if (isset($settings['colspan'])){
			$col->attr('colspan', $settings['colspan']);
		}

		if ($last === true){
			$col->addClass('ui-grid-cell-last');
		}

		if (isset($settings['css'])){
			foreach($settings['css'] as $k => $v){
				$col->css($k, $v);
			}
		}
		
		if (isset($settings['addCls'])){
			$col->addClass($settings['addCls']);
		}

		if (isset($settings['attr'])){
			foreach($settings['attr'] as $k => $v){
				$col->attr($k, $v);
			}
		}

		if (isset($settings['click'])){
			$col->click($settings['click']);
		}
		
		if (isset($settings['allowSort']) && $settings['allowSort'] === true){
			if (isset($settings['sortKey']) && !empty($settings['sortKey'])){
				$sortInfo = $this->getSortInfo();
				$sortDir = ($sortInfo['sortDir'] == 'DESC' ? 'desc' : 'asc');
				$col->attr('data-sort_by', $settings['sortKey'])
				->attr('data-current_sort_direction', $sortDir)
				->addClass('ui-grid-sortable-header');
			}
		}
		return $col;
	}

	public function addHeaderRow($settings){
		if (!isset($settings['columns'])){
			die('Missing Columns For Grid Header');
		}

		$tr = $this->parseRow($settings)->addClass('ui-grid-heading-row');

		$col = 0;
		$lastCol = sizeof($settings['columns']) - 1;
		foreach($settings['columns'] as $cInfo){
			$first = false;
			$last = false;
			if ($col == 0){
				$first = true;
			}elseif ($col == $lastCol){
				$last = true;
			}
			$th = $this->parseColumn('th', $cInfo, $first, $last)->addClass('ui-state-default');

			$tr->append($th);
			$col++;
		}
		$this->gridHeaderElement->append($tr);
		//		return $this;
	}

	public function addBodyRow($settings){
		if (!isset($settings['columns'])){
			die('Missing Columns For Grid Header');
		}

		$tr = $this->parseRow($settings);

		$col = 0;
		$lastCol = sizeof($settings['columns']) - 1;
		foreach($settings['columns'] as $cInfo){
			$first = false;
			$last = false;
			if ($col == 0){
				$first = true;
			}elseif ($col == $lastCol){
				$last = true;
			}
			$td = $this->parseColumn('td', $cInfo, $first, $last);

			$tr->append($td);
			$col++;
		}
		
		if ($this->stripeRows === true){
			$tr->addClass(($this->bodyRowNum%2 == 0 ? $this->evenRowCls : $this->oddRowCls));
		}
		
		$this->gridBodyElement->append($tr);
		$this->bodyRowNum++;
		//		return $this;
	}
	
	public function usePagination($val){
		$this->usePages = $val;
		return $this;
	}
	
	public function setPageLimit($val){
		$this->pageLimit = $val;
		return $this;
	}
	
	public function setCurrentPage($val){
		$this->currentPage = $val;
		return $this;
	}
	
	public function setQuery(&$query){
		$this->dataQuery = &$query;
		return $this;
	}
	
	public function useSorting($val){
		$this->useSortBy = $val;
		return $this;
	}
	
	public function setSortKey($val){
		$this->sortByKey = $val;
		return $this;
	}
	
	public function setSortDirKey($val){
		$this->sortByDirKey = $val;
		return $this;
	}
	
	public function stripeRows($evenCls, $oddCls){
		$this->stripeRows = true;
		$this->evenRowCls = $evenCls;
		$this->oddRowCls = $oddCls;
		return $this;
	}
	
	private function getSortInfo(){
		$sortKey = false;
		$sortKeyDir = false;
		if ($this->useSortBy === true){
			$sortKey = (isset($_POST[$this->sortByKey]) ? $_POST[$this->sortByKey] : (isset($_GET[$this->sortByKey]) ? $_GET[$this->sortByKey] : false));
			if ($sortKey !== false){
				$sortKeyDir = strtolower((isset($_POST[$this->sortByDirKey]) ? $_POST[$this->sortByDirKey] : (isset($_GET[$this->sortByDirKey]) ? $_GET[$this->sortByDirKey] : 'ASC')));
				if ($sortKeyDir == 'asc' || $sortKeyDir == 'a' || $sortKeyDir == '+'){
					$sortKeyDir = 'ASC';
				}elseif ($sortKeyDir == 'desc' || $sortKeyDir == 'd' || $sortKeyDir == '-'){
					$sortKeyDir = 'DESC';
				}else{
					die('Unsupported sort direction: ' . $sortKeyDir);
				}
			}
		}
		
		return array(
			'sortKey' => $sortKey,
			'sortDir' => $sortKeyDir
		);
	}
	
	public function &getResults(){
		if ($this->useSortBy === true){
			$sortInfo = $this->getSortInfo();
			if ($sortInfo['sortKey'] !== false){
				$this->dataQuery->orderBy($sortInfo['sortKey'] . ' ' . $sortInfo['sortDir']);
			}
		}
		
		if ($this->usePages === false){
			$result = $this->dataQuery->execute()->toArray(true);
			return $result;
		}
		
		$listingPager = new Doctrine_Pager($this->dataQuery, $this->currentPage, $this->pageLimit);
		$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

		$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 15
		));
		
		$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
		$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default">{%page}</a>');
		$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all">{%page}</span>');

		$pager = $pagerLayout->getPager();
		
		$Qproducts = $pager->execute()->toArray(true);
		
		$this->pagerBar = $pagerLayout->display(array(), true);
		$this->pager = $pager;
		return $Qproducts;
	}
}

if(class_exists('PagerLayoutWithArrows') != true){
	class PagerLayoutWithArrows extends Doctrine_Pager_Layout {
		private $myType = 'records';

		public function setMyType($val){
			$this->myType = $val;
		}

		public function getMyType(){
			return $this->myType;
		}

		public function display($options = array(), $return = false){
			$pager = $this->getPager();
			$str = '';

			// First page
			$this->addMaskReplacement('page', '&laquo;', true);
			$options['page_number'] = $pager->getFirstPage();
			$str .= $this->processPage($options);

			// Previous page
			$this->addMaskReplacement('page', '&lsaquo;', true);
			$options['page_number'] = $pager->getPreviousPage();
			$str .= $this->processPage($options);

			// Pages listing
			$this->removeMaskReplacement('page');
			$str .= parent::display($options, true);

			// Next page
			$this->addMaskReplacement('page', '&rsaquo;', true);
			$options['page_number'] = $pager->getNextPage();
			$str .= $this->processPage($options);

			// Last page
			$this->addMaskReplacement('page', '&raquo;', true);
			$options['page_number'] = $pager->getLastPage();
			$str .= $this->processPage($options);

			$str .= '&nbsp;&nbsp;<b>' . $pager->getFirstIndice() . ' - ' . $pager->getLastIndice() . ' (of ' . $pager->getNumResults() . ' ' . $this->myType .')</b>';
			// Possible wish to return value instead of print it on screen
			if ($return) {
				return $str;
			}
			echo $str;
		}
	}
}
?>