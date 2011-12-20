<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

class sysLanguage
{

	/**
	 * @var array
	 */
	private static $defines = array();

	/**
	 * @var array
	 */
	private static $overwritten = array();

	/**
	 * @var array
	 */
	private static $javascriptDefines = array();

	/**
	 * @var array
	 */
	private static $javascriptOverwritten = array();

	/**
	 * @var array
	 */
	private static $catalog_languages = array();

	/**
	 * @var string
	 */
	private static $browser_languages = '';

	/**
	 * @var string
	 */
	private static $language = '';

	/**
	 * @var array
	 */
	private static $languages = array();

	/**
	 * @var array
	 */
	private static $settings = array();

	/**
	 * @static
	 * @param string $lang
	 */
	public static function init($lang = '') {
		self::getLanguages();
		if (Session::exists('language') === false || isset($_GET['language']) || !empty($lang)){
			if (isset($_GET['language']) && !empty($_GET['language'])){
				self::setLanguage($_GET['language']);
			}
			elseif (!empty($lang)) {
				self::setLanguage($lang);
			}
			else {
				self::getBrowserLanguage();
			}

			Session::set('language', self::$language['directory']);
			Session::set('languages_code', self::$language['code']);
			Session::set('languages_id', self::$language['id']);
		}
		else {
			self::setLanguage(Session::get('languages_code'));
		}

		self::loadLanguage();
			
			if (Session::exists('currency') === false || isset($_GET['currency']) || (sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY') == 'true' &&sysConfig::get('LANGUAGE_CURRENCY') != Session::get('currency'))){
				if (isset($_GET['currency'])) {
					if (!$currency = tep_currency_exists($_GET['currency'])) $currency = (sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY') == 'true') ? sysConfig::get('LANGUAGE_CURRENCY') : sysConfig::get('DEFAULT_CURRENCY');
				} else {
					$currency = (sysConfig::get('USE_DEFAULT_LANGUAGE_CURRENCY') == 'true') ? sysConfig::get('LANGUAGE_CURRENCY') : sysConfig::get('DEFAULT_CURRENCY');
				}
				$QcurrencyValue = mysql_query('select value from currencies where code = "' . $currency . '"');
				$currencyValue = mysql_fetch_assoc($QcurrencyValue);

				Session::set('currency', $currency);
				Session::set('currency_value', $currencyValue['value']);
			}
	}

	/**
	 * @static
	 * @param bool $reload
	 * @return array
	 */
	public static function getLanguages($reload = false) {
		if (empty(self::$catalog_languages) || $reload === true){
			$Languages = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select * from languages where status = 1 order by sort_order');
			if (sizeof($Languages) > 0){
				foreach($Languages as $lInfo){
					self::$catalog_languages[$lInfo['code']] = array(
						'id'		=> $lInfo['languages_id'],
						'code'	  => $lInfo['code'],
						'name'	  => $lInfo['name'],
						'name_real' => $lInfo['name_real'],
						'image'	 => $lInfo['image'],
						'directory' => $lInfo['directory'],
						'forced_default' => $lInfo['forced_default'],
						'showName'  => function ($sep = '<br>') use ($lInfo) {
							return $lInfo['name_real'] . $sep . '( ' . $lInfo['name'] . ' )';
						}
					);
				}
			}
		}
		return self::$catalog_languages;
	}

	/**
	 * @static
	 * @param int $id
	 * @return array
	 */
	public static function getLanguage($id = 0) {
		$languages = self::getLanguages(true);
		$language = array();
		foreach($languages as $code => $lInfo){
			if ($lInfo['id'] == $id){
				$language = $lInfo;
				break;
			}
		}

		return $language;
	}

	/**
	 * @static
	 * @param int $id
	 * @return int
	 */
	public static function getId($id = 0) {
		$langId = 0;
		if ($id > 0){
			$lang = self::getLanguage($id);
			if (is_null($lang) === false){
				$langId = $lang['id'];
			}
		}
		else {
			$langId = self::$language['id'];
		}
		return $langId;
	}

	/**
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getName($id = 0) {
		$name = '';
		if ($id > 0){
			$lang = self::getLanguage($id);
			if (empty($lang) === false){
				$name = $lang['name'];
			}
		}
		else {
			$name = self::$language['name'];
		}
		return $name;
	}

	/**
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getImage($id = 0) {
		$image = '';
		if ($id > 0){
			$lang = self::getLanguage($id);
			if (is_null($lang) === false){
				$image = $lang['image'];
			}
		}
		else {
			$image = self::$language['image'];
		}
		return $image;
	}

	/**
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getCode($id = 0) {
		$code = '';
		if ($id > 0){
			$lang = self::getLanguage($id);
			if (is_null($lang) === false){
				$code = $lang['code'];
			}
		}
		else {
			$code = self::$language['code'];
		}
		return $code;
	}

	/**
	 * @static
	 * @param string|int $lang
	 * @return string
	 */
	public static function getDirectory($lang = '') {
		$dir = '';
		if ($lang != ''){
			$ResultSet = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select directory from languages where code = "' . $lang . '" or languages_id = "' . $lang . '"');
			if (sizeof($ResultSet) > 0){
				$dir = $ResultSet['directory'];
			}
		}
		else {
			$dir = self::$language['directory'];
		}

		return $dir;
	}

	/**
	 * @static
	 *
	 */
	public static function loadLanguage() {
		global $App;
		$languageDir = sysConfig::getDirFsCatalog() . 'includes/languages/' . strtolower(self::getName()) . '/';
		self::loadSettings($languageDir . 'settings.xml');
		self::loadDefinitions($languageDir . $App->getEnv() . '/global.xml');
	}

	/**
	 * @static
	 * @param string $filePath
	 * @param string $forcedEnv
	 */
	public static function loadSettings($filePath, $forcedEnv = null) {
		if (file_exists($filePath)){
			$langData = simplexml_load_file(
				$filePath,
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);

			self::$settings['date_format'] = (string)$langData->date_format;
			self::$settings['date_format_short'] = (string)$langData->date_format_short;
			self::$settings['date_format_long'] = (string)$langData->date_format_long;
			self::$settings['date_time_format'] = (string)$langData->date_time_format;
			self::$settings['default_currency'] = (string)$langData->default_currency;
			self::$settings['html_params'] = (string)$langData->html_params;
			self::$settings['html_charset'] = (string)$langData->html_charset;
		}
	}

	/**
	 * @static
	 * @param string $filePath
	 * @param string $forcedEnv
	 */
	public static function loadDefinitions($filePath, $forcedEnv = null) {
		global $App, $messageStack;
		//$langFile = strtolower(self::getName()) . '.xml';

		if (substr($filePath, -3) != 'xml'){
			$langFile = 'global.xml';
			$prependPath = '';
			if (substr($filePath, 0, 1) != '/'){
				if (substr($filePath, 0, strlen(sysConfig::getDirFsCatalog())) != sysConfig::getDirFsCatalog()){
					$prependPath = sysConfig::getDirFsCatalog();
				}
			}

			$filePath = $prependPath . $filePath . $langFile;
		}

		if (file_exists($filePath)){
			$langData = simplexml_load_file(
				$filePath,
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);

			foreach($langData->define as $langDefine){
				if (isset($langDefine['javascript']) && (string)$langDefine['javascript'] == 'true'){
					self::setJavascript((string)$langDefine['key'], (string)$langDefine[0]);
				}

				if (isset($langDefine['php'])){
					if ((string)$langDefine['php'] == 'true'){
						self::set((string)$langDefine['key'], (string)$langDefine[0]);
					}
				}
				else {
					self::set((string)$langDefine['key'], (string)$langDefine[0]);
				}
			}
		}
		else {
			//trigger_error('Language file does not exist (' . $filePath . ')', E_USER_ERROR);
			/*$messageStack->addSession('footerStack', array(
								'Server Message' => 'Language file does not exist',
								'File Requested' => $filePath
							), 'error');*/
		}
	}

	/**
	 * @static
	 * @param string $type
	 * @return string|null
	 */
	public static function getDateFormat($type = '') {
		if ($type == ''){
			return self::$settings['date_format'];
		}
		elseif ($type == 'short') {
			return self::$settings['date_format_short'];
		}
		elseif ($type == 'long') {
			return self::$settings['date_format_long'];
		}
		return null;
	}

	/**
	 * @static
	 * @return string
	 */
	public static function getDateTimeFormat() {
		return self::$settings['date_time_format'];
	}

	/**
	 * @static
	 * @return string
	 */
	public static function getCurrency() {
		return self::$settings['default_currency'];
	}

	/**
	 * @static
	 * @return string
	 */
	public static function getHtmlParams() {
		return self::$settings['html_params'];
	}

	/**
	 * @static
	 * @return string
	 */
	public static function getCharset() {
		return self::$settings['html_charset'];
	}

	/**
	 * @static
	 * @param string $key
	 * @return bool
	 */
	public static function exists($key) {
		return isset(self::$defines[$key]);
	}

	/**
	 * @static
	 * @param string $key
	 * @param string $val
	 */
	public static function set($key, $val) {
		global $messageStack;
		if (isset(self::$defines[$key])){
			self::$overwritten[$key][] = array(
				'original' => self::$defines[$key],
				'new'	  => $val
			);
		}

		self::$defines[$key] = $val;
	}

	/**
	 * @static
	 * @param string $key
	 * @param string $val
	 */
	public static function setJavascript($key, $val) {
		global $messageStack;
		if (isset(self::$javascriptDefines[$key])){
			self::$javascriptOverwritten[$key][] = array(
				'original' => self::$javascriptDefines[$key],
				'new'	  => $val
			);
		}

		self::$javascriptDefines[$key] = $val;
	}

	/**
	 * @static
	 * @return bool
	 */
	public static function hasJavascriptDefines() {
		return !empty(self::$javascriptDefines);
	}

	/**
	 * @static
	 * @return array
	 */
	public static function getJavascriptDefines() {
		return self::$javascriptDefines;
	}

	/**
	 * @static
	 * @param string $key
	 * @return string
	 */
	public static function get($key) {
		global $messageStack;
		if (self::exists($key)){
			$text = self::$defines[$key];
		}
		elseif (defined($key)) {
			$text = constant($key);
		}
		else {
			trigger_error('Language key not defined (' . $key . ')', E_USER_NOTICE);
			/*$messageStack->addSession('footerStack', array(
								'Server Message' => 'Language key not defined',
								'Key Requested' => $key
							), 'error');*/
			$text = $key;
		}
		return str_replace('\\n', "\n", $text);
	}

	/**
	 * @static
	 * @return string
	 */
	public static function getBrowserLanguage() {
		$Forced = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select code from languages where forced_default = 1');
		if (sizeof($Forced) > 0){
			self::$language = self::$catalog_languages[$Forced[0]['code']];
		}else{
			self::$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$languagesSorted = array();
			foreach(self::$browser_languages as $lInfo){
				if (stristr($lInfo, ';')){
					$data = explode(';', $lInfo);
					$sort = explode('=', $data[1]);
					$order = $sort[1];
					$langCode = $data[0];
				}
				else {
					$order = 1;
					$langCode = $lInfo;
				}

				$languagesSorted[$order] = $langCode;
			}
			krsort($languagesSorted);

			$langSet = false;
			foreach($languagesSorted as $code){
				if (isset(self::$catalog_languages[strtolower($code)])){
					self::$language = self::$catalog_languages[strtolower($code)];
					$langSet = true;
					break;
				}
			}

			if ($langSet === false){
				self::$language = self::$catalog_languages[sysConfig::get('DEFAULT_LANGUAGE')];
			}
		}
		return self::$language;
	}

	/**
	 * @static
	 * @param string $lang
	 */
	public static function setLanguage($lang) {
		if (!empty($lang) && isset(self::$catalog_languages[$lang])){
			self::$language = self::$catalog_languages[$lang];
		}
		else {
			self::$language = self::$catalog_languages[sysConfig::get('DEFAULT_LANGUAGE')];
		}
	}

	/**
	 * @static
	 * @param string $filePath
	 * @param string $toLangCode
	 * @param string $langName
	 * @param bool $returnXml
	 * @return mixed
	 */
	public static function translateFile($filePath, $toLangCode, $langName, $returnXml = false) {
		$langData = simplexml_load_file(
			$filePath,
			'SimpleXMLExtended'
		);

		/*
		 * If this is a settings file, just update the information.
		 * no need for a translation request
		 */
		if (basename($filePath) == 'settings.xml'){
			$htmlParams = (string)$langData->html_params;
			$htmlParams = str_replace('"en"', '"' . $toLangCode . '"', $htmlParams);
			$langData->html_params->setCData($htmlParams);
			$langData->name->setCData($langName);
			$langData->code->setCData($toLangCode);
		}
		else {
			/*
			 * Builds an array to help throttle the translations per request and an array of the keys per request
			 * It was timing out at 25 so 10 seems to be a safe max per request
			 */
			$requests = array();
			$keys = array();
			$reqNum = 0;
			$i = 0;
			foreach($langData->define as $define){
				$requests[$reqNum][] = 'q=' . urlencode((string)$define[0]);
				$keys[$reqNum][] = (string)$define['key'];
				$i++;
				if ($i > 10){
					$reqNum++;
					$i = 0;
				}
			}

			/*
			 * Send the requests to google for translation and build an array of the returns indexed by the
			 * define key set in the $keys array above, to make it easier to add them back to the xml file
			 */
			$translated = array();
			$errorReport = '';
			foreach($requests as $reqNum => $reqQueries){
				self::sendRequestToGoogle($reqQueries, 'en', $toLangCode, $keys[$reqNum], $translated);
			}

			foreach($langData->define as $define){
				$define->setCData($translated[(string)$define['key']]);
			}
		}

		if ($returnXml === true){
			return $langData->asPrettyXML();
		}else{
			$fileObj = fopen($filePath, 'w+');
			if ($fileObj){
				ftruncate($fileObj, -1);
				fwrite($fileObj, $langData->asPrettyXML());
				fclose($fileObj);
			}
		}

		if (empty($errorReport) === false){
			die($errorReport);
		}
	}

	/**
	 * @static
	 * @param string $text
	 * @param string|int $fromLangCode
	 * @param string|int $toLangCode
	 * @return array
	 */
	public static function translateText($text, $fromLangCode, $toLangCode = 1) {
		if (is_numeric($toLangCode)){
			$toLangCode = self::getCode($toLangCode);
		}
		if (is_numeric($fromLangCode)){
			$fromLangCode = self::getCode($fromLangCode);
		}

		/*
		 * Builds an array to help throttle the translations per request and an array of the keys per request
		 * Google translate has a maximum request uri of 5000
		 */
		$requests = array();
		$keys = array();
		$untranslated = array();
		$reqNum = 0;
		$requestLength = 300;
		if (is_array($text)){
			foreach($text as $key => $str){
				if (!empty($str)){
					$textLength = strlen($str) + 2;
					if ($textLength > 5000){
						$untranslated[$key] = $str;
					}
					else {
						if (($requestLength + $textLength) > 5000){
							$reqNum++;
							$requestLength = 300;
						}
						else {
							$requestLength += $textLength;
						}

						$requests[$reqNum][] = 'q=' . urlencode($str);
						$keys[$reqNum][] = (string)$key;
					}
				}
				else {
					$untranslated[$key] = '';
				}
			}
		}
		else {
			$requests[0][] = 'q=' . urlencode($text);
			$keys[$reqNum][] = 0;
		}

		/*
		 * Send the requests to google for translation and build an array of the returns indexed by the
		 * define key set in the $keys array above, to make it easier to add them back to the xml file
		 */
		$translated = array();
		$errorReport = '';
		foreach($requests as $reqNum => $reqQueries){
			self::sendRequestToGoogle($reqQueries, $fromLangCode, $toLangCode, $keys[$reqNum], $translated);
		}

		foreach($untranslated as $key => $val){
			$translated[$key] = $val;
		}

		return $translated;
	}

	/**
	 * @static
	 * @param array $reqQueries
	 * @param string $fromLangCode
	 * @param string $toLangCode
	 * @param array $keys
	 * @param array $translated
	 */
	private static function sendRequestToGoogle(array $reqQueries, $fromLangCode, $toLangCode, array $keys, array &$translated) {
		$Request = new CurlRequest();
		$Request->setSendMethod('get');
		$Request->setUrl('https://www.googleapis.com/language/translate/v2');
		$Request->setData('key=' . sysConfig::get('GOOGLE_TRANSLATE_API_SERVER_KEY') . '&' . implode('&', $reqQueries) . '&source=' . urlencode($fromLangCode) . '&target=' . urlencode($toLangCode));
		$Response = $Request->execute();

		$json = json_decode($Response->getResponse());
		if (!$json){
			echo 'CURL ERROR: ' . $Response->getError() . '<br>URL: ' . $Request->getUrl() . '<br>DATA: ' . $Request->getDataFormatted() . '<br>BODY: ' . $Response->getResponse();
			itwExit();
		}

		foreach($json->data->translations as $k => $translations){
			$translated[$keys[$k]] = $translations->translatedText;
		}
	}

	/**
	 * @static
	 *
	 */
	public static function cleanAbandonedLanguages() {
		$existsId = array();
		$existsDir = array();
		foreach(self::getLanguages() as $lInfo){
			$existsId[] = $lInfo['id'];
			$existsDir[] = $lInfo['directory'];
		}

		$loadedModels = Doctrine_Core::getLoadedModelFiles();
		foreach($loadedModels as $modelName => $modelPath){
			$Model = Doctrine_Core::getTable($modelName);
			$RecordInst = $Model->getRecordInstance();
			if (method_exists($RecordInst, 'cleanLanguageProcess')){
				$RecordInst->cleanLanguageProcess($existsId);
			}
		}

		$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/languages');
		foreach($dirObj as $dir){
			if (!in_array($dir->getBasename(), $existsDir)){
				Doctrine_Lib::removeDirectories($dir->getPathName());
			}
		}
	}

	/**
	 * @static
	 * @return array
	 */
	public static function getGoogleLanguages() {
		global $messageStack;

		$Request = new CurlRequest();
		$Request->setSendMethod('get');
		$Request->setUrl('https://www.googleapis.com/language/translate/v2/languages');
		$Request->setData('target=' . sysLanguage::getCode() . '&key=' . sysConfig::get('GOOGLE_TRANSLATE_API_SERVER_KEY'));
		$Response = $Request->execute();

		$json = json_decode($Response->getResponse());
		if (!$json){
			echo 'CURL ERROR: ' . $Response->getError() . '<br>URL: ' . $Request->getUrl() . '<br>DATA: ' . $Request->getDataFormatted() . '<br>BODY: ' . $Response->getResponse();
			itwExit();
		}

		$langArray = array();
		if (isset($json->error)){
			$ErrorTable = htmlBase::newElement('table');

			$ErrorTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Url Used: '),
					array('text' => $Request->getUrl())
				)
			));
			foreach($json->error->errors as $eInfo){
				$ErrorTable->addBodyRow(array(
					'columns' => array(
						array('text' => 'Domain: '),
						array('text' => $eInfo->domain)
					)
				));

				$ErrorTable->addBodyRow(array(
					'columns' => array(
						array('text' => 'Reason: '),
						array('text' => $eInfo->reason)
					)
				));

				$ErrorTable->addBodyRow(array(
					'columns' => array(
						array('text' => 'Message: '),
						array('text' => $eInfo->message)
					)
				));

				$ErrorTable->addBodyRow(array(
					'columns' => array(
						array('text' => 'More Info: '),
						array('text' => '<a href="' . $eInfo->extendedHelp . '">' . $eInfo->extendedHelp . '</a>')
					)
				));
			}

			$messageStack->add('pageStack', $ErrorTable->draw(), 'error');
		}
		else {
			foreach($json->data->languages as $lang){
				$langArray[$lang->language] = $lang->name;
			}
		}
		return $langArray;
	}
}

/*
 * SimpleXML extension to add in functions required for the cart
 */
class SimpleXMLExtended extends SimpleXMLElement
{

	/*
	 * SimpleXML does not support CDATA tags, this function fixes that
	 */
	public function setCData($cdata_text) {
		$node = dom_import_simplexml($this);
		$no = $node->ownerDocument;
		$node->replaceChild($no->createCDATASection($cdata_text), $node->firstChild);
	}

	/*
	 * SimpleXML builds files as one line, lets keep it pretty and human readable with this function
	 */
	public function asPrettyXML() {
		$string = $this->asXML();

		/*
		 * put each element on it's own line
		 */
		$string = preg_replace("/>\s*</", ">\n<", $string);

		/*
		 * Fix CDATA Element and closing tag to be one the same line as the opening tag
		 */
		$string = preg_replace("/(>\n*<!\[)/", "><![", $string);
		$string = preg_replace("/(\]>\n<\/)/", "]></", $string);
		$string = preg_replace("/(>\n<)(?!(def)|(\/def)|(\!))/", "><", $string);

		/*
		 * each element to own array
		 */
		$xmlArray = explode("\n", $string);

		/*
		 * holds indentation
		 */
		$currIndent = 0;

		/*
		 * set xml element first by shifting of initial element
		 */
		$string = array_shift($xmlArray) . "\n";

		foreach($xmlArray as $element){
			/*
			 * find open only tags... add name to stack, and print to string
			 * increment currIndent
			 */
			if (preg_match('/^<([\w])+[^>\/]*>$/U', $element)){
				$string .= str_repeat('	', $currIndent) . $element . "\n";
				$currIndent += 1;
			}
			elseif (preg_match('/^<\/.+>$/', $element)) {
				/*
				 * find standalone closures, decrement currindent, print to string
				 */
				$currIndent -= 1;
				$string .= str_repeat('	', $currIndent) . $element . "\n";
			}
			else {
				/*
				 * find open/closed tags on the same line print to string
				 */
				$string .= str_repeat('	', $currIndent) . $element . "\n";
			}
		}

		/*
		 * Trim off added new lines from the end of the string
		 */
		while(substr($string, -1) == "\n"){
			$string = substr($string, 0, -1);
		}

		return $string;
	}
}

?>