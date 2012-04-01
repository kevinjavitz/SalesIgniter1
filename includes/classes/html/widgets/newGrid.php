<?php
/**
 * Html Grid Widget Class
 * @package Html
 */
class htmlWidget_newGrid implements htmlWidgetPlugin
{

	/**
	 * @var \htmlElement
	 */
	protected $mainElement;

	/**
	 * @var \htmlElement
	 */
	protected $gridElement;

	/**
	 * @var \htmlElement
	 */
	protected $gridHeaderElement;

	/**
	 * @var \htmlElement
	 */
	protected $gridBodyElement;

	/**
	 * @var \htmlElement
	 */
	protected $gridFooterElement;

	/**
	 * @var array
	 */
	protected $buttons;

	/**
	 * @var int
	 */
	protected $bodyRowNum = 0;

	/**
	 * @var bool
	 */
	protected $stripeRows = false;

	/**
	 * @var bool
	 */
	protected $usePages = false;

	/**
	 * @var string
	 */
	protected $pageLimitKey = 'limit';

	/**
	 * @var string
	 */
	protected $pageKey = 'page';

	/**
	 * @var int
	 */
	protected $currentPage = 0;

	/**
	 * @var bool
	 */
	protected $useSearch = false;

	/**
	 * @var bool
	 */
	protected $useSortBy = false;

	/**
	 * @var string
	 */
	protected $sortByKey = 'sortBy';

	/**
	 * @var string
	 */
	protected $sortByDirKey = 'sortDir';

	/**
	 * @var string
	 */
	protected $insertBeforeHeaderBar = '';

	/**
	 * @var string
	 */
	protected $insertAfterHeaderBar = '';

	/**
	 * @var Doctrine_Query
	 */
	protected $dataQuery;

	public function __construct() {
		$this->mainElement = new htmlElement('div');
		$this->mainElement->addClass('gridContainer');
		$this->gridElement = new htmlElement('table');

		$this->gridElement->attr('cellpadding', '2')
			->attr('cellspacing', '0')
			->css('width', '100%')
			->addClass('grid');

		$this->gridHeaderElement = new htmlElement('thead');
		$this->gridHeaderElement->addClass('gridHeader');

		$this->gridBodyElement = new htmlElement('tbody');
		$this->gridBodyElement->addClass('gridBody');
		//$this->gridFooterElement = new htmlElement('tfoot');
	}

	/**
	 * @param $function
	 * @param $args
	 * @return htmlWidget_newGrid|mixed
	 */
	public function __call($function, $args) {
		$return = call_user_func_array(array($this->mainElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}

	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain() {
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setId($val) {
		$this->mainElement->attr('id', $val);
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setName($val) {
		$this->mainElement->attr('name', $val);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function draw() {
		if ($this->useSearch === true){

		}

		$this->gridElement->append($this->gridHeaderElement)->append($this->gridBodyElement);

		if (!empty($this->insertBeforeHeaderBar)){
			$extraContent = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->css(array(
				'padding'       => '.5em',
				'margin-bottom' => '.5em',
				'text-align'    => 'left',
				'font-weight'   => 'normal'
			))
				->html($this->insertBeforeHeaderBar);

			$this->mainElement->append($extraContent);
		}

		if (!empty($this->buttons)){
			$buttonBar = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-header ui-corner-all gridButtonBar')
				->css(array(
				'margin-bottom' => '.5em',
				'text-align'    => 'right'
			));

			foreach($this->buttons as $button){
				$buttonBar->append($button);
			}

			$this->mainElement->append($buttonBar);
		}

		if (!empty($this->insertAfterHeaderBar)){
			$extraContent = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-content ui-corner-all')
				->css(array(
				'padding'       => '.5em',
				'margin-bottom' => '.5em',
				'text-align'    => 'left',
				'font-weight'   => 'normal'
			))
				->html($this->insertAfterHeaderBar);
			$this->mainElement->append($extraContent);
		}

		if ($this->useSortBy === true){
			$this->gridElement
				->attr('data-sort_key', $this->sortByKey)
				->attr('data-sort_dir_key', $this->sortByDirKey);
		}

		$gridContainer = htmlBase::newElement('div')
			->append($this->gridElement);

		$this->mainElement->append($gridContainer);

		if ($this->usePages === true && isset($this->pagerBar)){
			$pageElement = htmlBase::newElement('div')
				->addClass('ui-widget ui-widget-header ui-corner-all gridPagerBar')
				->css(array(
				'margin-top' => '.5em',
				'text-align' => 'right'
			))->html($this->pagerBar);

			$this->mainElement->append($pageElement);
		}
		return $this->mainElement->draw();
	}

	/* Required Functions From Interface: htmlElementPlugin --END-- */

	/**
	 * @param array $settings
	 * @return htmlElement
	 */
	private function parseRow(array $settings) {
		$row = new htmlElement('tr');
		//$row->addClass('ui-grid-row');

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

	/**
	 * @param $tag
	 * @param $settings
	 * @return htmlElement
	 */
	private function parseColumn($tag, $settings) {
		global $currencies;
		if (is_object($settings['text'])){
			if ($settings['text'] instanceof SesDateTime){
				$colHtml = $settings['text']->format(sysLanguage::getDateFormat('short'));
			}else{
				$colHtml = $settings['text']->draw();
			}
		}
		elseif (!isset($settings['text']) || strlen($settings['text']) <= 0){
			$colHtml = '&nbsp;';
		}
		else{
			$colHtml = $settings['text'];
		}

		if (isset($settings['format'])){
			switch($settings['format']){
				case 'int':
					$colHtml = (int)$colHtml;
					break;
				case 'float':
					$colHtml = (float)$colHtml;
					break;
				case 'string':
					$colHtml = (string)$colHtml;
					break;
				case 'currency':
					$colHtml = $currencies->format($colHtml);
					break;
			}
		}
		$col = new htmlElement($tag);
		$col->html($colHtml);

		if (isset($settings['align'])){
			$cls = 'leftAlign';
			if ($settings['align'] == 'right'){
				$cls = 'rightAlign';
			}
			elseif ($settings['align'] == 'center') {
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

		if (isset($settings['useSort']) && $settings['useSort'] === true){
			if (isset($settings['sortKey']) && !empty($settings['sortKey'])){
				$sortInfo = $this->getSortInfo($settings['sortKey']);
				$sortDir = ($sortInfo['sortDir'] === false ? 'none' : ($sortInfo['sortDir'] == 'DESC' ? 'desc' : 'asc'));
				$col->attr('data-sort_by', $settings['sortKey'])
					->attr('data-current_sort_direction', $sortDir)
					->addClass('ui-grid-sortable-header');
			}
		}
		return $col;
	}

	/**
	 * @param IGridSearchObj $searchObj
	 * @return htmlElement
	 */
	private function parseSearchColumn(IGridSearchObj $searchObj){
		global $currencies;
		$col = new htmlElement('th');
		$col->html($searchObj->getFieldHtml());
		$col->addClass('centerAlign');
		$col->attr('valign', 'middle');

		return $col;
	}

	/**
	 * @param $buttons
	 * @return htmlWidget_newGrid
	 */
	public function addButtons($buttons) {
		$this->buttons = $buttons;
		return $this;
	}

	/**
	 * @param $settings
	 */
	public function addHeaderRow($settings) {
		if (!isset($settings['columns'])){
			die('Missing Columns For Grid Header');
		}

		$tr = $this->parseRow($settings)->addClass('gridHeaderRow');
		if ($this->useSearch === true){
			$addSearchRow = false;
			foreach($settings['columns'] as $cInfo){
				if (isset($cInfo['useSearch']) && $cInfo['useSearch'] === true){
					$addSearchRow = true;
					break;
				}
			}

			if ($addSearchRow === true){
				$searchTr = $this->parseRow(array())->addClass('gridHeaderRow gridSearchHeaderRow');
			}
		}

		$col = 0;
		$lastCol = sizeof($settings['columns']) - 1;
		foreach($settings['columns'] as $cInfo){
			$first = false;
			$last = false;
			if ($col == 0){
				$first = true;
			}
			elseif ($col == $lastCol) {
				$last = true;
			}

			$th = $this->parseColumn('th', $cInfo);

			if ($last === true){
				$th->addClass('gridHeaderRowColumnLast');
			}
			else {
				$th->addClass('gridHeaderRowColumn');
			}

			$tr->append($th);

			if ($this->useSearch === true && isset($searchTr)){
				if (isset($cInfo['useSearch']) && $cInfo['useSearch'] === true){
					$searchTh = $this->parseSearchColumn($cInfo['searchObj']);
					$cInfo['searchObj']->addToQuery($this->dataQuery);
				}
				else {
					if (isset($cInfo['useSort'])){
						$cInfo['useSort'] = false;
					}
					$searchTh = $this->parseColumn('th', $cInfo);
					if ($last === true){
						$goButton = htmlBase::newElement('button')
							->addClass('applyFilterButton')
							->setText(sysLanguage::get('TEXT_BUTTON_APPLY_FILTER'))
							->setTooltip(sysLanguage::get('TEXT_TOOLTIP_APPLY_FILTER_BUTTON'));
						$resetButton = htmlBase::newElement('button')
							->addClass('resetFilterButton')
							->setText(sysLanguage::get('TEXT_BUTTON_RESET_FILTER'))
							->setTooltip(sysLanguage::get('TEXT_TOOLTIP_CLEAR_FILTER_BUTTON'));
						$searchTh->html($goButton->draw() . '&nbsp;' . $resetButton->draw());
					}else{
						$searchTh->html('&nbsp;');
					}
				}

				if ($last === true){
					$searchTh->addClass('gridHeaderRowColumnLast');
				}
				else {
					$searchTh->addClass('gridHeaderRowColumn');
				}

				$searchTr->append($searchTh);
			}
			$col++;
		}
		$this->gridHeaderElement->append($tr);
		if ($this->useSearch === true && isset($searchTr)){
			$this->gridHeaderElement->append($searchTr);
		}
		//		return $this;
	}

	/**
	 * @param $settings
	 */
	public function addBodyRow($settings) {
		if (!isset($settings['columns'])){
			die('Missing Columns For Grid Header');
		}

		$tr = $this->parseRow($settings);

		if ($tr->hasClass('gridInfoRow') === false){
			$tr->addClass('gridBodyRow');
		}

		$col = 0;
		$lastCol = sizeof($settings['columns']) - 1;
		foreach($settings['columns'] as $cInfo){
			$first = false;
			$last = false;
			if ($col == 0){
				$first = true;
			}
			elseif ($col == $lastCol) {
				$last = true;
			}
			$td = $this->parseColumn('td', $cInfo, $first, $last);

			if ($last === true){
				$td->addClass('gridBodyRowColumnLast');
			}
			else {
				$td->addClass('gridBodyRowColumn');
			}

			$tr->append($td);
			$col++;
		}

		if ($this->stripeRows === true){
			$tr->addClass(($this->bodyRowNum % 2 == 0 ? $this->evenRowCls : $this->oddRowCls));
		}

		$this->gridBodyElement->append($tr);
		$this->bodyRowNum++;
		//		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function usePagination($val) {
		$this->usePages = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setPageLimit($val) {
		$this->pageLimit = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setPageKey($val) {
		$this->pageKey = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setPageLimitKey($val) {
		$this->pageLimitKey = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setCurrentPage($val) {
		$this->currentPage = $val;
		return $this;
	}

	/**
	 * @param $query
	 * @return htmlWidget_newGrid
	 */
	public function setQuery(&$query) {
		$this->dataQuery = &$query;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function useSorting($val) {
		$this->useSortBy = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setSortKey($val) {
		$this->sortByKey = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function setSortDirKey($val) {
		$this->sortByDirKey = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function useSearching($val){
		$this->useSearch = $val;
		return $this;
	}

	/**
	 * @param $evenCls
	 * @param $oddCls
	 * @return htmlWidget_newGrid
	 */
	public function stripeRows($evenCls, $oddCls) {
		$this->stripeRows = true;
		$this->evenRowCls = $evenCls;
		$this->oddRowCls = $oddCls;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function addBeforeButtonBar($val) {
		$this->insertBeforeHeaderBar = $val;
		return $this;
	}

	/**
	 * @param $val
	 * @return htmlWidget_newGrid
	 */
	public function addAfterButtonBar($val) {
		$this->insertAfterHeaderBar = $val;
		return $this;
	}

	/**
	 * @param $ColSortKeyVal
	 * @return array
	 */
	private function getSortInfo($ColSortKeyVal) {
		$sortKey = false;
		$sortKeyDir = false;
		if ($this->useSortBy === true){
			$sortKey = (isset($_POST[$this->sortByKey]) ? $_POST[$this->sortByKey] : (isset($_GET[$this->sortByKey]) ? $_GET[$this->sortByKey] : false));
			if ($sortKey !== false && ($ColSortKeyVal === false || $sortKey == $ColSortKeyVal)){
				$sortKeyDir = strtolower((isset($_POST[$this->sortByDirKey]) ? $_POST[$this->sortByDirKey] : (isset($_GET[$this->sortByDirKey]) ? $_GET[$this->sortByDirKey] : 'ASC')));
				if ($sortKeyDir == 'asc' || $sortKeyDir == 'a' || $sortKeyDir == '+'){
					$sortKeyDir = 'ASC';
				}
				elseif ($sortKeyDir == 'desc' || $sortKeyDir == 'd' || $sortKeyDir == '-') {
					$sortKeyDir = 'DESC';
				}
				else {
					die('Unsupported sort direction: ' . $sortKeyDir);
				}
			}
		}

		return array(
			'sortKey' => $sortKey,
			'sortDir' => $sortKeyDir
		);
	}

	/**
	 * @param bool $returnArray
	 * @return mixed
	 */
	public function &getResults($returnArray = true) {
		if ($this->useSortBy === true){
			$sortInfo = $this->getSortInfo(false);
			if ($sortInfo['sortKey'] !== false){
				$this->dataQuery->orderBy($sortInfo['sortKey'] . ' ' . $sortInfo['sortDir']);
			}
		}

		if ($this->usePages === false){
			$result = $this->dataQuery->execute();
			if ($returnArray === true){
				return $result->toArray(true);
			}
			return $result;
		}

		$pageLimit = (isset($_POST[$this->pageLimitKey]) ? $_POST[$this->pageLimitKey] : (isset($_GET[$this->pageLimitKey]) ? $_GET[$this->pageLimitKey] : 25));
		$currentPage = (isset($_POST[$this->pageKey]) ? $_POST[$this->pageKey] : (isset($_GET[$this->pageKey]) ? $_GET[$this->pageKey] : false));

		$listingPager = new Doctrine_Pager($this->dataQuery, $currentPage, $pageLimit);
		$pagerLink = itw_app_link(tep_get_all_get_params(array($this->pageKey, 'action')) . $this->pageKey . '={%page_number}');

		$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 15
		));

		$pagerLayout = new NewGridPagerLayout($listingPager, $pagerRange, $pagerLink);
		$pagerLayout->setTemplate('<a href="{%url}" class="ui-widget ui-corner-all ui-state-default navButton">{%page}</a>');
		$pagerLayout->setSelectedTemplate('<span class="ui-widget ui-corner-all navButton">{%page}</span>');

		$pager = $pagerLayout->getPager();

		$Qproducts = $pager->execute();

		$this->pagerBar = $pagerLayout->display(array(
			'pageKey'      => $this->pageKey,
			'pageNum'      => $currentPage,
			'pageLimitKey' => $this->pageLimitKey,
			'pageLimit'    => $pageLimit
		), true);

		if ($returnArray === true){
			return $Qproducts->toArray(true);
		}
		return $Qproducts;
	}
}

class NewGridPagerLayout extends Doctrine_Pager_Layout
{

	public function display($options = array(), $return = false) {
		$pager = $this->getPager();
		$str = '';

		// First page
		$this->addMaskReplacement('page', '<span class="ui-icon ui-icon-seek-first" tooltip="First Page"></span>', true);
		$options['page_number'] = $pager->getFirstPage();
		$str .= $this->processPage($options);

		// Previous page
		$this->addMaskReplacement('page', '<span class="ui-icon ui-icon-seek-prev" tooltip="Previous Page"></span>', true);
		$options['page_number'] = $pager->getPreviousPage();
		$str .= $this->processPage($options);

		// Pages listing
		$this->removeMaskReplacement('page');
		$str .= '<select onchange="window.location=this.options[this.options.selectedIndex].value">';
		for($i = 1; $i <= $pager->getLastPage(); $i++){
			$str .= '<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageKey'], 'action')) . $options['pageKey'] . '=' . $i) . '"' . ($options['pageNum'] == $i ? ' selected="selected"': '') . '>Page ' . $i . '</option>';
		}
		$str .= '</select>';
		//$str .= parent::display($options, true);

		// Next page
		$this->addMaskReplacement('page', '<span class="ui-icon ui-icon-seek-next" tooltip="Next Page"></span>', true);
		$options['page_number'] = $pager->getNextPage();
		$str .= $this->processPage($options);

		// Last page
		$this->addMaskReplacement('page', '<span class="ui-icon ui-icon-seek-end" tooltip="Last Page"></span>', true);
		$options['page_number'] = $pager->getLastPage();
		$str .= $this->processPage($options);

		$str .= '<span class="gridPagerText">&nbsp;&nbsp;<b>' . $pager->getFirstIndice() . ' - ' . $pager->getLastIndice() . ' (of ' . $pager->getNumResults() . ' records)</b></span>';

		$str .= '<span class="gridPagerText">&nbsp;&nbsp;-&nbsp;&nbsp;Page Size: </span><select onchange="window.location=this.options[this.options.selectedIndex].value">' .
			'<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageLimitKey'], 'action')) . 'limit=10') . '"' . ($options['pageLimit'] == '10' ? ' selected="selected"' : '') . '>10</option>' .
			'<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageLimitKey'], 'action')) . 'limit=25') . '"' . ($options['pageLimit'] == '25' ? ' selected="selected"' : '') . '>25</option>' .
			'<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageLimitKey'], 'action')) . 'limit=50') . '"' . ($options['pageLimit'] == '50' ? ' selected="selected"' : '') . '>50</option>' .
			'<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageLimitKey'], 'action')) . 'limit=100') . '"' . ($options['pageLimit'] == '100' ? ' selected="selected"' : '') . '>100</option>' .
			'<option value="' . itw_app_link(tep_get_all_get_params(array($options['pageLimitKey'], 'action')) . 'limit=250') . '"' . ($options['pageLimit'] == '250' ? ' selected="selected"' : '') . '>250</option>' .
			'</select>';
		// Possible wish to return value instead of print it on screen

		if ($return){
			return '<span class="gridPager">' . $str . '</span>';
		}
		echo '<span class="gridPager">' . $str . '</span>';
	}
}

class GridSearchObj
{

	/**
	 * @static
	 * @return GridSearchObjEqual
	 */
	public static function Equal() {
		return new GridSearchObjEqual();
	}

	/**
	 * @static
	 * @return GridSearchObjLike
	 */
	public static function Like() {
		return new GridSearchObjLike();
	}

	/**
	 * @static
	 * @return GridSearchObjBetween
	 */
	public static function Between() {
		return new GridSearchObjBetween();
	}
}

interface IGridSearchObj {

	public function setFieldName($val);

	public function useFieldObj($fieldObj);

	public function setDatabaseColumn($val);

	public function getFieldHtml();

	public function addToQuery(Doctrine_Query &$Query);
}

class GridSearchObjEqual implements IGridSearchObj
{

	/**
	 * @var htmlElement
	 */
	protected $fieldObj;

	/**
	 * @var string
	 */
	protected $fieldName = '';

	/**
	 * @var string
	 */
	protected $dbCol = '';

	/**
	 * @param $val
	 * @return GridSearchObjEqual
	 */
	public function setFieldName($val) {
		$this->fieldName = $val;
		return $this;
	}

	/**
	 * @param $fieldObj
	 * @return GridSearchObjEqual
	 */
	public function useFieldObj($fieldObj){
		$this->setFieldName($fieldObj->attr('name'));
		$this->fieldObj = $fieldObj;
		return $this;
	}

	/**
	 * @param $val
	 * @return GridSearchObjEqual
	 */
	public function setDatabaseColumn($val) {
		$this->dbCol = $val;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFieldHtml() {
		$clearFilter = '';
		$val = '';
		if (isset($_GET[$this->fieldName]) && !empty($_GET[$this->fieldName])){
			$val = $_GET[$this->fieldName];
			$clearFilter = htmlBase::newElement('span')
				->addClass('ui-icon ui-icon-cancel clearFilterIcon')
				->css('vertical-align', 'middle')
				->attr('tooltip', sysLanguage::get('TEXT_TOOLTIP_CLEAR_FILTER_ICON'))
				->draw();
		}

		if (is_object($this->fieldObj)){
			$fieldHtml = $this->fieldObj
				->setName($this->fieldName)
				->val($val)
				->draw();
		}else{
			$fieldHtml = htmlBase::newElement('input')
				->setName($this->fieldName)
				->val($val)
				->draw();
		}
		return $clearFilter . $fieldHtml;
	}

	/**
	 * @param Doctrine_Query $Query
	 */
	public function addToQuery(Doctrine_Query &$Query) {
		if (isset($_GET[$this->fieldName]) && !empty($_GET[$this->fieldName])){
			if (is_array($this->dbCol)){
				foreach($this->dbCol as $dbCol){
					$Query->orWhere($dbCol . ' = ?', $_GET[$this->fieldName]);
				}
			}else{
				$Query->andWhere($this->dbCol . ' = ?', $_GET[$this->fieldName]);
			}
		}
	}
}

class GridSearchObjLike implements IGridSearchObj
{

	/**
	 * @var htmlElement
	 */
	protected $fieldObj;

	/**
	 * @var string
	 */
	protected $fieldName = '';

	/**
	 * @var string
	 */
	protected $dbCol = '';

	/**
	 * @param $val
	 * @return GridSearchObjLike
	 */
	public function setFieldName($val) {
		$this->fieldName = $val;
		return $this;
	}

	/**
	 * @param $fieldObj
	 * @return GridSearchObjLike
	 */
	public function useFieldObj($fieldObj){
		$this->setFieldName($fieldObj->attr('name'));
		$this->fieldObj = $fieldObj;
		return $this;
	}

	/**
	 * @param $val
	 * @return GridSearchObjLike
	 */
	public function setDatabaseColumn($val) {
		$this->dbCol = $val;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFieldHtml() {
		$clearFilter = '';
		$val = '';
		if (isset($_GET[$this->fieldName]) && !empty($_GET[$this->fieldName])){
			$val = $_GET[$this->fieldName];
			$clearFilter = htmlBase::newElement('span')
				->addClass('ui-icon ui-icon-cancel clearFilterIcon')
				->css('vertical-align', 'middle')
				->attr('tooltip', sysLanguage::get('TEXT_TOOLTIP_CLEAR_FILTER_ICON'))
				->draw();
		}

		if (is_object($this->fieldObj)){
			$fieldHtml = $this->fieldObj
				->setName($this->fieldName)
				->val($val)
				->draw();
		}else{
			$fieldHtml = htmlBase::newElement('input')
				->setName($this->fieldName)
				->val($val)
				->draw();
		}

		return $clearFilter . $fieldHtml;
	}

	/**
	 * @param Doctrine_Query $Query
	 */
	public function addToQuery(Doctrine_Query &$Query) {
		if (isset($_GET[$this->fieldName]) && !empty($_GET[$this->fieldName])){
			if (is_array($this->dbCol)){
				foreach($this->dbCol as $dbCol){
					$Query->orWhere($dbCol . ' LIKE ?', '%' . $_GET[$this->fieldName] . '%');
				}
			}else{
				$Query->andWhere($this->dbCol . ' LIKE ?', '%' . $_GET[$this->fieldName] . '%');
			}
		}
	}
}

class GridSearchObjBetween implements IGridSearchObj
{

	/**
	 * @var htmlElement
	 */
	protected $fieldObj;

	/**
	 * @var string
	 */
	protected $fromFieldName = '';

	/**
	 * @var string
	 */
	protected $toFieldName = '';

	/**
	 * @var string
	 */
	protected $dbCol = '';

	/**
	 * @param $val
	 * @return GridSearchObjBetween
	 */
	public function setFieldName($val) {
		$this->fromFieldName = $val . '_from';
		$this->toFieldName = $val . '_to';
		return $this;
	}

	/**
	 * @param $fieldObj
	 * @return GridSearchObjBetween
	 */
	public function useFieldObj($fieldObj){
		$this->setFieldName($fieldObj->attr('name'));
		$this->fieldObj = $fieldObj;
		return $this;
	}

	/**
	 * @param $val
	 * @return GridSearchObjBetween
	 */
	public function setDatabaseColumn($val) {
		$this->dbCol = $val;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFieldHtml() {
		$clearFilter = '';
		$fromVal = '';
		$toVal = '';
		if (
			(isset($_GET[$this->fromFieldName]) && !empty($_GET[$this->fromFieldName])) ||
			(isset($_GET[$this->toFieldName]) && !empty($_GET[$this->toFieldName]))
		){
			$fromVal = $_GET[$this->fromFieldName];
			$toVal = $_GET[$this->toFieldName];
			$clearFilter = htmlBase::newElement('span')
				->addClass('ui-icon ui-icon-cancel clearFilterIcon')
				->css('vertical-align', 'middle')
				->attr('tooltip', sysLanguage::get('TEXT_TOOLTIP_CLEAR_FILTER_ICON'))
				->draw();
		}

		if (is_object($this->fieldObj)){
			$this->fieldObj
				->setName($this->fromFieldName)
				->val($fromVal);

			$fieldHtml = $this->fieldObj->draw();

			$this->fieldObj
				->setName($this->toFieldName)
				->val($toVal);

			$fieldHtml .= '&nbsp;-&nbsp;' .$this->fieldObj->draw();
		}else{
			$fieldHtml = htmlBase::newElement('input')
				->setName($this->fromFieldName)
				->val($fromVal)
				->draw() .
				'&nbsp;-&nbsp;' .
				htmlBase::newElement('input')
					->setName($this->toFieldName)
					->val($toVal)
					->draw();
		}
		return $clearFilter . $fieldHtml;
	}

	/**
	 * @param Doctrine_Query $Query
	 */
	public function addToQuery(Doctrine_Query &$Query) {
		if (
			(isset($_GET[$this->fromFieldName]) && !empty($_GET[$this->fromFieldName])) ||
			(isset($_GET[$this->toFieldName]) && !empty($_GET[$this->toFieldName]))
		){
			if (is_array($this->dbCol)){
				foreach($this->dbCol as $dbCol){
					$Query->orWhere($dbCol . ' BETWEEN ? AND ?', array($_GET[$this->fromFieldName], $_GET[$this->toFieldName]));
				}
			}else{
				$Query->andWhere($this->dbCol . ' BETWEEN ? AND ?', array($_GET[$this->fromFieldName], $_GET[$this->toFieldName]));
			}
		}
	}
}

?>