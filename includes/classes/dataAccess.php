<?php
class dataAccess {
	/*
	* @todo: These will be moved to a mysql.php class later - END
	*/
	private static $accessMethod = 'mysql';
	private static $dataConnector = null;

	public function __construct(){
		if (self::$accessMethod == 'mysql'){
			if (self::$dataConnector == null){
				self::establishConnection();
			}
		}else{
			die('No access methods are accepted except MySQL for now.');
		}
	}

	public static function establishConnection(){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/dataAccess/' . self::$accessMethod . '.php');
		$className = 'dataAccess_' . self::$accessMethod;
		self::$dataConnector = new $className;
		self::$dataConnector->connect();
	}

	public static function setQuery($queryString){
		return new dataAccessResult($queryString);
	}
	
	public static function batchInsert($tableName){
		return new dataAccessBatch('insert', $tableName);
	}

	public static function batchUpdate($tableName){
		return new dataAccessBatch('update', $tableName);
	}

	public static function batchDelete($tableName){
		return new dataAccessBatch('delete', $tableName);
	}

	public static function arrayQuery($table, $dataArray, $action = 'insert', $params = '', $returnInsertId = false){
		reset($dataArray);
		if ($action == 'insert') {
			$cols = array();$vals = array();
			foreach($dataArray as $col => $colVal){
				$cols[] = $col;
				switch((string)$colVal){
					case 'now()':
						$vals[] = 'now()';
						break;
					case 'null':
						$vals[] = 'null';
						break;
					default:
						$vals[] = '"' . self::cleanInput($colVal) . '"';
						break;
				}
			}
			$Qquery = self::setQuery('insert into ' . $table . ' (' . implode(', ', $cols) . ') values (' . implode(', ', $vals) . ')');
		} elseif ($action == 'update') {
			$colValSets = array();
			foreach($dataArray as $col => $colVal){
				switch((string)$colVal){
					case 'now()':
						$colValSets[] = $col . ' = now()';
						break;
					case 'null':
						$colValSets[] = $col . ' = null';
						break;
					default:
						$colValSets[] = $col . ' = "' . self::cleanInput($colVal) . '"';
						break;
				}
			}
			$Qquery = self::setQuery('update ' . $table . ' set ' . implode(', ', $colValSets) . ' where ' . $params);
		}
		$Qquery->runQuery();
		if ($returnInsertId === true){
			return $Qquery->insertId();
		}
		return true;
	}

	public static function runQuery($queryString){
		return self::$dataConnector->query($queryString);
	}

	public static function fetchArray($queryResource){
		return self::$dataConnector->fetchArray($queryResource);
	}

	public static function insertId(){
		return self::$dataConnector->insertId();
	}

	public static function numberOfRows($queryResource){
		return self::$dataConnector->numberOfRows($queryResource);
	}

	public static function cleanInput($string){
		return self::$dataConnector->cleanInput($string);
	}

	public static function cleanOutput($string){
		return self::$dataConnector->cleanOutput($string);
	}

	public static function dataSeek($query, $row){
		return self::$dataConnector->dataSeek($query, $row);
	}
}

class dataAccessResult {
	public function __construct($queryString){
		$this->query = $queryString;
	}

	public function setTable($placeHolder, $value){
		$this->query = str_replace($placeHolder, $value, $this->query);
		return $this;
	}

	public function setValue($placeHolder, $value){
		$this->query = str_replace($placeHolder, '"' . dataAccess::cleanInput($value) . '"', $this->query);
		return $this;
	}

	public function setRaw($placeHolder, $value){
		/*
		* @todo: how do i prevent people from creating serious security holes?
		*/
		$this->query = str_replace($placeHolder, $value, $this->query);
		return $this;
	}

	public function reportError($result){
		global $messageStack;
		if (sysConfig::get('ERROR_REPORTING_METHOD') == 'display'){
			$errMsg = '<table cellpadding="3" cellspacing="0" border="0">' .
				'<tr>' .
					'<td class="main" style="white-space:nowrap;">' . $result['errMsg'] . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td class="main" style="white-space:nowrap;" valign="top"><b><u>Query Used</u></b></td>' .
				'</tr>' .
				'<tr>' .
					'<td class="main">' . $this->query . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td class="main" style="white-space:nowrap;" valign="top"><b><u>Server Message</u></b></td>' .
				'</tr>' .
				'<tr>' .
					'<td class="main">' . $result['serverErrMsg'] . '</td>' .
				'</tr>' .
			'</table>';
			if (is_object($messageStack)){
				$messageStack->addSession('footerStack', $errMsg, 'error');
			}
		}
	}

	public function dataSeek($row){
		$result = dataAccess::dataSeek($this->Qresource['queryResource'], $row);
		if (isset($result['errMsg']) && !empty($result['errMsg'])){
			$this->reportError($result);
		}
		return $result['result'];
	}

	public function runQuery(){
		global $messageStack;
		$this->Qresource = dataAccess::runQuery($this->query);

		if (isset($this->Qresource['errMsg']) && !empty($this->Qresource['errMsg'])){
			$this->reportError($this->Qresource);
		}
		return $this;
	}

	public function fetchArray(){
		global $messageStack;
		$this->currentResult = dataAccess::fetchArray($this->Qresource['queryResource']);
		if (isset($this->currentResult['errMsg']) && !empty($this->currentResult['errMsg'])){
			if (sysConfig::get('ERROR_REPORTING_METHOD') == 'display'){
				$this->reportError($this->Qresource);
			}
		}
		return $this->currentResult['fetchResource'];
	}

	public function next(){
		if (!isset($this->Qresource['queryResource'])){
			$this->runQuery();
		}
		return $this->fetchArray();
	}

	public function insertId(){
		return dataAccess::insertId($this->Qresource['queryResource']);
	}

	public function numberOfRows(){
		global $messageStack;
		$result = dataAccess::numberOfRows($this->Qresource['queryResource']);
		if (isset($result['errMsg']) && !empty($result['errMsg'])){
			$this->reportError($result);
		}
		return $result['numberOfRows'];
	}

	public function getVal($column){
		if (!isset($this->currentResult['fetchResource'])){
			$this->fetchArray();
		}
		if (isset($this->currentResult['fetchResource'][$column])){
			return dataAccess::cleanOutput($this->currentResult['fetchResource'][$column]);
		}
		return '';
	}

	public function getIntVal($column){
		return (int)$this->getVal($column);
	}

	public function getFloatVal($column){
		return (float)$this->getVal($column);
	}

	public function nextId(){
		return $this->insertId();
	}

	public function toArray(){
		if (!isset($this->currentResult['fetchResource'])){
			$this->fetchArray();
		}
		return $this->currentResult['fetchResource'];
	}

	public function setPagination(&$startPage, $perPage = 10){
		if (empty($startPage)) $startPage = 1;

		$posTo = strlen($this->query);
		$posFrom = stripos($this->query, ' from', 0);

		$posGroupBy = stripos($this->query, ' group by', $posFrom);
		if ($posGroupBy < $posTo && $posGroupBy !== false) $posTo = $posGroupBy;

		$posHaving = stripos($this->query, ' having', $posFrom);
		if ($posHaving < $posTo && $posHaving !== false) $posTo = $posHaving;

		$posOrderBy = stripos($this->query, ' order by', $posFrom);
		if ($posOrderBy < $posTo && $posOrderBy !== false) $posTo = $posOrderBy;

		$Qcount = dataAccess::setQuery('select count(*) as total ' . substr($this->query, $posFrom, ($posTo - $posFrom)));
		$Qcount->runQuery();

		$numRows = $Qcount->getVal('total');

		$numberOfPages = ceil($numRows / $perPage);
		if ($startPage > $numberOfPages){
			$startPage = $numberOfPages;
		}

		$offset = (int)($perPage * ($startPage - 1));
		if ($offset < 0){
			$offset = 0;
		}

		$this->query .= ' limit ' . $offset . ', ' . $perPage;

		$this->paginationInfo = array(
			'perPage'       => $perPage,
			'currentPage'   => $startPage,
			'numberOfRows'  => $numRows,
			'numberOfPages' => $numberOfPages
		);
		return $this;
	}

	public function showPageLinks($params = '', $varName = 'page'){
		if (!empty($params) && (substr($params, -1) != '&')) $params .= '&';

		// calculate number of pages needing links
		$numPages = $this->paginationInfo['numberOfPages'];
		$pages_array = array();
		for ($i=1; $i<=$numPages; $i++) {
			$pages_array[] = array('id' => $i, 'text' => $i);
		}

		$fileName = basename($_SERVER['PHP_SELF']);
		$currentPage = $this->paginationInfo['currentPage'];
		if ($numPages > 1){
			$display_links = tep_draw_form('pages', $fileName, '', 'get');
			if ($currentPage > 1){
				$display_links .= '<a href="' . tep_href_link($fileName, $params . $varName . '=' . ($currentPage - 1), 'NONSSL') . '" class="splitPageLink">' . sysLanguage::get('PREVNEXT_BUTTON_PREV') . '</a>&nbsp;&nbsp;';
			} else {
				$display_links .= sysLanguage::get('PREVNEXT_BUTTON_PREV') . '&nbsp;&nbsp;';
			}

			$display_links .= sprintf(sysLanguage::get('TEXT_RESULT_PAGE'), tep_draw_pull_down_menu($varName, $pages_array, $currentPage, 'onChange="this.form.submit();"'), $numPages);

			if ($currentPage < $numPages && $numPages != 1){
				$display_links .= '&nbsp;&nbsp;<a href="' . tep_href_link($fileName, $params . $varName . '=' . ($currentPage + 1), 'NONSSL') . '" class="splitPageLink">' . sysLanguage::get('PREVNEXT_BUTTON_NEXT') . '</a>';
			} else {
				$display_links .= '&nbsp;&nbsp;' . sysLanguage::get('PREVNEXT_BUTTON_NEXT');
			}

			if ($params != ''){
				if (substr($params, -1) == '&') $params = substr($params, 0, -1);
				$pairs = explode('&', $params);
				foreach($pairs as $pair){
					$pInfo = explode('=', $pair);
					$display_links .= tep_draw_hidden_field(rawurldecode($pInfo[0]), rawurldecode($pInfo[1]));
				}
			}

			if (defined('SID')) $display_links .= tep_draw_hidden_field(Session::getSessionName(), Session::getSessionId());
			$display_links .= '</form>';
		} else {
			$display_links = sprintf('Result Page(s): %s of %s', (int)$numPages, (int)$numPages);
		}
		return $display_links;
	}

	public function showPageCount(){
		$rowsPerPage = $this->paginationInfo['perPage'];
		$currentPage = $this->paginationInfo['currentPage'];
		$numberOfRows = $this->paginationInfo['numberOfRows'];

		$toNum = ($rowsPerPage * $currentPage);
		if ($toNum > $numberOfRows) $toNum = $numberOfRows;

		$fromNum = ($rowsPerPage * ($currentPage - 1));
		if ($toNum == 0){
			$fromNum = 0;
		} else {
			$fromNum++;
		}
		return sprintf('Displaying <b>%s</b> to <b>%s</b> (of <b>%s</b>) results', $fromNum, $toNum, $numberOfRows);
	}
}

abstract class DataAccessTransactions {
	public function beginTransaction(){
	}
	
	public function commitTransaction(){
	}
	
	public function rollbackTransaction(){
	}
}

class dataAccessBatch extends DataAccessTransactions {
	private $batchType;
	private $tableName;
	
	public function __construct($batchType, $tableName){
		$this->batchType = $batchType;
		$this->tableName = $tableName;
	}
	
	public function addRecord($arr){
		$this->records[] = $arr;
	}
	
	public function process(){
		$this->beginTransaction();
		switch ($this->batchType){
			case 'insert':
			case 'update':
				foreach($this->records as $idx => $rInfo){
					dataAccess::arrayQuery($this->tableName, $rInfo, $this->batchType);
				}
				break;
			case 'delete':
				$whereArr = array();
				foreach($this->records as $idx => $rInfo){
					foreach($rInfo as $k => $v){
						$whereArr[] = $k . '="' . $v . '"';
					}
				}
				dataAccess::runQuery('delete from ' . $this->tableName . ' where ' , implode(' and ', $whereArr));
				break;
		}
		$this->commitTransaction();
	}
}

interface dataAccessConnector {
	public function connect();
	public function disconnect();
	public function query($queryString);
	public function fetchArray($queryResource);
	public function fetchObject($queryResource);
	public function numberOfRows($queryResource);
	public function insertId();
	public function getError($errorNumber);
	public function freeResult($queryResource);
	public function cleanInput($string);
	public function cleanOutput($string);
}
?>