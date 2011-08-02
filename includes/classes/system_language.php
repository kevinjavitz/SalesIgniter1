<?php
	class sysLanguage {
		private static $defines = array();
		private static $overwritten = array();
		private static $javascriptDefines = array();
		private static $javascriptOverwritten = array();
		private static $catalog_languages = array();
		private static $browser_languages = '';
		private static $language = '';
		private static $languages = array();
		private static $settings = array();
		
		public static function init($lang = ''){
			self::getLanguages();
			if (Session::exists('language') === false || isset($_GET['language']) || !empty($lang)){
				if (isset($_GET['language']) && !empty($_GET['language'])){
					self::setLanguage($_GET['language']);
				}elseif (!empty($lang)){
					self::setLanguage($lang);
				}else{
					self::getBrowserLanguage();
				}
				
				Session::set('language', self::$language['directory']);
				Session::set('languages_code', self::$language['code']);
				Session::set('languages_id', self::$language['id']);
			}else{
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
		
		public static function getLanguages($reload = false){
			if (empty(self::$catalog_languages) || $reload === true){
				$Qlanguages = mysql_query('select * from languages where status = 1 order by sort_order');
				if (mysql_num_rows($Qlanguages)){
					while($lInfo = mysql_fetch_assoc($Qlanguages)){
						self::$catalog_languages[$lInfo['code']] = array(
							'id'        => $lInfo['languages_id'],
							'code'      => $lInfo['code'],
							'name'      => $lInfo['name'],
							'name_real' => $lInfo['name_real'],
							'image'     => $lInfo['image'],
							'directory' => $lInfo['directory'],
							'showName'  => function ($sep = '<br>') use ($lInfo) {
								return $lInfo['name_real'] . $sep . '( ' . $lInfo['name'] . ' )';
							}
						);
					}
				}
			}
			return self::$catalog_languages;
		}
		
		public static function getLanguage($id){
			$languages = self::getLanguages(true);
			$language = null;
			foreach($languages as $code => $lInfo){
				if ($lInfo['id'] == $id){
					$language = $lInfo;
					break;
				}
			}
			
			return $language;
		}
		
		public static function getId($id = null){
			$langId = null;
			if (is_null($id) === false){
				$lang = self::getLanguage($id);
				if (is_null($lang) === false){
					$langId = $lang['id'];
				}
			}else{
				$langId = self::$language['id'];
			}
			return $langId;
		}
		
		public static function getName($id = null){
			$name = null;
			if (is_null($id) === false){
				$lang = self::getLanguage($id);
				if (is_null($lang) === false){
					$name = $lang['name'];
				}
			}else{
				$name = self::$language['name'];
			}
			return $name;
		}
		
		public static function getImage($id = null){
			$image = null;
			if (is_null($id) === false){
				$lang = self::getLanguage($id);
				if (is_null($lang) === false){
					$image = $lang['image'];
				}
			}else{
				$image = self::$language['image'];
			}
			return $image;
		}
		
		public static function getCode($id = null){
			$code = null;
			if (is_null($id) === false){
				$lang = self::getLanguage($id);
				if (is_null($lang) === false){
					$code = $lang['code'];
				}
			}else{
				$code = self::$language['code'];
			}
			return $code;
		}
		
		public static function getDirectory($lang = null){
			$dir = null;
			if (is_null($lang) === false){
				$Qdir = mysql_query('select directory from languages where code = "' . $lang . '" or languages_id = "' . $lang . '"');
				if (mysql_num_rows($Qdir)){
					$result = mysql_fetch_assoc($Qdir);
					$dir = $result['directory'];
				}
			}else{
				$dir = self::$language['directory'];
			}
			
			return $dir;
		}
		
		public static function loadLanguage(){
			global $App;
			$languageDir = sysConfig::getDirFsCatalog() . 'includes/languages/' . strtolower(self::getName()) . '/';
			self::loadSettings($languageDir . 'settings.xml');
			self::loadDefinitions($languageDir . $App->getEnv() . '/global.xml');
		}
		
		public static function loadSettings($filePath, $forcedEnv = null){
			if (file_exists($filePath)){
				$langData = simplexml_load_file(
					$filePath,
					'SimpleXMLElement',
					LIBXML_NOCDATA
				);

				self::$settings['date_format'] = (string) $langData->date_format;
				self::$settings['date_format_short'] = (string) $langData->date_format_short;
				self::$settings['date_format_long'] = (string) $langData->date_format_long;
				self::$settings['date_time_format'] = (string) $langData->date_time_format;
				self::$settings['default_currency'] = (string) $langData->default_currency;
				self::$settings['html_params'] = (string) $langData->html_params;
				self::$settings['html_charset'] = (string) $langData->html_charset;
			}
		}
		
		public static function loadDefinitions($filePath, $forcedEnv = null){
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
					if (isset($langDefine['javascript']) && (string) $langDefine['javascript'] == 'true'){
						self::setJavascript((string) $langDefine['key'], (string) $langDefine[0]);
					}
					
					if (isset($langDefine['php'])){
						if ((string) $langDefine['php'] == 'true'){
							self::set((string) $langDefine['key'], (string) $langDefine[0]);
						}
					}else{
						self::set((string) $langDefine['key'], (string) $langDefine[0]);
					}
				}
			}else{
				//trigger_error('Language file does not exist (' . $filePath . ')', E_USER_ERROR);
				/*$messageStack->addSession('footerStack', array(
					'Server Message' => 'Language file does not exist',
					'File Requested' => $filePath
				), 'error');*/
			}
		}
		
		public static function getDateFormat($type = null){
			if (is_null($type) === true){
				return self::$settings['date_format'];
			}elseif ($type == 'short'){
				return self::$settings['date_format_short'];
			}elseif ($type == 'long'){
				return self::$settings['date_format_long'];
			}
			return null;
		}
		
		public static function getDateTimeFormat(){
			return self::$settings['date_time_format'];
		}
		
		public static function getCurrency(){
			return self::$settings['default_currency'];
		}
		
		public static function getHtmlParams(){
			return self::$settings['html_params'];
		}
		
		public static function getCharset(){
			return self::$settings['html_charset'];
		}
		
		public static function exists($key){
			return isset(self::$defines[$key]);
		}
		
		/*
		 * Set a language key in the internal array
		 */
		public static function set($key, $val){
			global $messageStack;
			if (isset(self::$defines[$key])){
				self::$overwritten[$key][] = array(
					'original' => self::$defines[$key],
					'new'      => $val
				);
				//trigger_error('Language key already defined (' . $key . ')', E_USER_NOTICE);
				/*$messageStack->addSession('footerStack', array(
					'Server Message' => 'Language key already defined',
					'Key Sent' => $key
				), 'error');*/
			}else{
				/* TODO: Remove when all calls are updated*/
				define($key, str_replace('\n', "\n", $val));
			}
			
			self::$defines[$key] = $val;
		}
		
		public static function setJavascript($key, $val){
			global $messageStack;
			if (isset(self::$javascriptDefines[$key])){
				self::$javascriptOverwritten[$key][] = array(
					'original' => self::$javascriptDefines[$key],
					'new'      => $val
				);
				//trigger_error('Language key already defined (' . $key . ')', E_USER_NOTICE);
				/*$messageStack->addSession('footerStack', array(
					'Server Message' => 'Language key already defined',
					'Key Sent' => $key
				), 'error');*/
			}
			
			self::$javascriptDefines[$key] = $val;
		}
		
		public static function hasJavascriptDefines(){
			return !empty(self::$javascriptDefines);
		}
		
		public static function getJavascriptDefines(){
			return self::$javascriptDefines;
		}
		
		/*
		 * Get a language key that was defined when the language definitions were loaded
		 */
		public static function get($key){
			global $messageStack;
			if (self::exists($key)){
				$text = self::$defines[$key];
			}elseif (defined($key)){
				$text = constant($key);
			}else{
				trigger_error('Language key not defined (' . $key . ')', E_USER_NOTICE);
				/*$messageStack->addSession('footerStack', array(
					'Server Message' => 'Language key not defined',
					'Key Requested' => $key
				), 'error');*/
				$text = $key;
			}
			return str_replace('\\n', "\n", $text);
		}

		/*
		 * Set the browser language based on the priority set within the browser
		 */
		public static function getBrowserLanguage(){
			self::$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$languagesSorted = array();
			foreach(self::$browser_languages as $lInfo){
				if (stristr($lInfo, ';')){
					$data = explode(';', $lInfo);
					$sort = explode('=', $data[1]);
					$order = $sort[1];
					$langCode = $data[0];
				}else{
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
			return self::$language;
		}
		
		/*
		 * Set the specified language, if it doesn't exist then use the admin set default
		 */
		public static function setLanguage($lang){
			if (!empty($lang) && isset(self::$catalog_languages[$lang])){
				self::$language = self::$catalog_languages[$lang];
			}else{
				self::$language = self::$catalog_languages[sysConfig::get('DEFAULT_LANGUAGE')];
			}
		}
		
		/*
		 * Translates a file from english to the specified language
		 */
		public static function translateFile($filePath, $toLangCode, $langName){
			$langData = simplexml_load_file(
				$filePath,
				'SimpleXMLExtended'
			);
			
			/*
			 * If this is a settings file, just update the information.
			 * no need for a translation request
			 */
			if (basename($filePath) == 'settings.xml'){
				$htmlParams = (string) $langData->html_params;
				$htmlParams = str_replace('"en"', '"' . $toLangCode . '"', $htmlParams);
				$langData->html_params->setCData($htmlParams);
				$langData->name->setCData($langName);
				$langData->code->setCData($toLangCode);
			}else{
				/*
				 * Builds an array to help throttle the translations per request and an array of the keys per request
				 * It was timing out at 25 so 10 seems to be a safe max per request
				 */
				$requests = array();
				$keys = array();
				$reqNum = 0;
				$i = 0;
				foreach($langData->define as $define){
					$requests[$reqNum][] = 'q=' . urlencode((string) $define[0]);
					$keys[$reqNum][] = (string) $define['key'];
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
					$define->setCData($translated[(string) $define['key']]);
				}
			}
					
			$fileObj = fopen($filePath, 'w+');
			if ($fileObj){
				ftruncate($fileObj, -1);
				fwrite($fileObj, $langData->asPrettyXML());
				fclose($fileObj);
			}
				
			if (empty($errorReport) === false){
				die($errorReport);
			}
		}
		
		public static function translateText($text, $toLangId, $fromLangId = 1){
			$toLangCode = self::getCode($toLangId);
			$fromLangCode = self::getCode($fromLangId);
			
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
						}else{
							if (($requestLength + $textLength) > 5000){
								$reqNum++;
								$requestLength = 300;
							}else{
								$requestLength += $textLength;
							}
						
							$requests[$reqNum][] = 'q=' . urlencode($str);
							$keys[$reqNum][] = (string) $key;
						}
					}else{
						$untranslated[$key] = '';
					}
				}
			}else{
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
		
		private static function sendRequestToGoogle($reqQueries, $fromLangCode, $toLangCode, $keys, &$translated){
			$url = 'http://ajax.googleapis.com/ajax/services/language/translate';
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'v=1.0&userip=' . $_SERVER['REMOTE_ADDR'] . '&' . implode('&', $reqQueries) . '&langpair=' . urlencode($fromLangCode) . '|' . urlencode($toLangCode));
			curl_setopt($ch, CURLOPT_REFERER, sysConfig::get('HTTP_SERVER'));
			$body = curl_exec($ch);
			curl_close($ch);

			$json = json_decode($body);
			if (!$json){
				echo 'CURL ERROR: ' . curl_error($ch) . '<br>URL: ' . $url . '<br>BODY: ' . $body;
				itwExit();
			}
				
			foreach($json->responseData as $k => $response){
				/*
				 * Check for multiple keys in the response, sometimes it's only one key and the $response
				 * will be the translated text
				 */
				if (!isset($response->responseData)){
					$translated[$keys[0]] = $response;
				}else{
					$translated[$keys[$k]] = $response->responseData->translatedText;
				}
			}
		}
		
		/*
		 * Cleans database and files out for any language entries that don't have a language in the languages table
		 */
		public static function cleanAbandonedLanguages(){
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
		
		/*
		 * Returns all languages supported by google translate, this list was grabbed from their interface 
		 * and may not always be up to date, so check it every few months to see if more are added
		 */
		public static function getGoogleLanguages(){
			return array(
				'af' => 'Afrikaans',
				'sq' => 'Albanian',
				'ar' => 'Arabic',
				'hy' => 'Armenian ALPHA',
				'az' => 'Azerbaijani ALPHA',
				'eu' => 'Basque ALPHA',
				'be' => 'Belarusian',
				'bg' => 'Bulgarian',
				'ca' => 'Catalan',
				'zh-CN' => 'Chinese',
				'hr' => 'Croatian',
				'cs' => 'Czech',
				'da' => 'Danish',
				'nl' => 'Dutch',
				'en' => 'English',
				'et' => 'Estonian',
				'tl' => 'Filipino',
				'fi' => 'Finnish',
				'fr' => 'French',
				'gl' => 'Galician',
				'ka' => 'Georgian ALPHA',
				'de' => 'German',
				'el' => 'Greek',
				'ht' => 'Haitian Creole ALPHA',
				'iw' => 'Hebrew',
				'hi' => 'Hindi',
				'hu' => 'Hungarian',
				'is' => 'Icelandic',
				'id' => 'Indonesian',
				'ga' => 'Irish',
				'it' => 'Italian',
				'ja' => 'Japanese',
				'ko' => 'Korean',
				'lv' => 'Latvian',
				'lt' => 'Lithuanian',
				'mk' => 'Macedonian',
				'ms' => 'Malay',
				'mt' => 'Maltese',
				'no' => 'Norwegian',
				'fa' => 'Persian',
				'pl' => 'Polish',
				'pt' => 'Portuguese',
				'ro' => 'Romanian',
				'ru' => 'Russian',
				'sr' => 'Serbian',
				'sk' => 'Slovak',
				'sl' => 'Slovenian',
				'es' => 'Spanish',
				'sw' => 'Swahili',
				'sv' => 'Swedish',
				'th' => 'Thai',
				'tr' => 'Turkish',
				'uk' => 'Ukrainian',
				'ur' => 'Urdu ALPHA',
				'vi' => 'Vietnamese',
				'cy' => 'Welsh',
				'yi' => 'Yiddish'
			);
		}
	}
	
	/*
	 * SimpleXML extension to add in functions required for the cart
	 */
	class SimpleXMLExtended extends SimpleXMLElement {
		/*
		 * SimpleXML does not support CDATA tags, this function fixes that
		 */
		public function setCData($cdata_text){
			$node = dom_import_simplexml($this);
			$no = $node->ownerDocument;
			$node->replaceChild($no->createCDATASection($cdata_text), $node->firstChild);
		}
		
		/*
		 * SimpleXML builds files as one line, lets keep it pretty and human readable with this function
		 */
		public function asPrettyXML(){
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
					$string .=  str_repeat('	', $currIndent) . $element . "\n";
					$currIndent += 1;
				}elseif (preg_match('/^<\/.+>$/', $element)){
				/*
				 * find standalone closures, decrement currindent, print to string
				 */ 
					$currIndent -= 1;
					$string .=  str_repeat('	', $currIndent) . $element . "\n";
				}else{
				/*
				 * find open/closed tags on the same line print to string
				 */
					$string .=  str_repeat('	', $currIndent) . $element . "\n";
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