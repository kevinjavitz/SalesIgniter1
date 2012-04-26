<?php
if (!class_exists('sysConfig')){
	require_once('includes/application_top.php');
}
// require_once(DIR_WS_CLASSES . 'order.php');
// require_once(DIR_WS_CLASSES . 'shopping_cart.php');

class emailEvent {
	private $eventName = null;
	private $languageId = null;
	private $templateData = array();
	private $templateSubjectUnparsed = null;
	private $templateUnparsed = null;
	private $templateFileUnparsed = null;
	private $templateSubjectParsed = null;
	private $templateParsed = null;
	private $templateFileParsed = null;

	public function __construct($eventName = null, $languageId = null){
		$this->languageId = Session::get('languages_id');
		
		if (is_null($languageId) === false){
			$this->languageId = $languageId;
		}
		
		if (is_null($eventName) === false){
			$this->setEvent($eventName, true);
		}
	}

	public function setEvent($eventName){
		$this->eventName = $eventName;
		$this->templateSubjectParsed = null;
		$this->templateFileParsed = null;
		$this->templateParsed = null;
		
		$Qtemplate = Doctrine_Query::create()
		->from('EmailTemplates e')
		->leftJoin('e.EmailTemplatesDescription ed')
		->leftJoin('e.EmailTemplatesVariables ev')
		->where('e.email_templates_event = ?', $this->eventName)
		->andWhere('ed.language_id = ?', $this->languageId)
		->execute();
		if ($Qtemplate->count() > 0){
			$this->templateData = $Qtemplate->toArray(true);
			$this->eventVars = $this->templateData[0]['EmailTemplatesVariables'];
			$this->allowedVars = array(
				'today_short'       => tep_date_short(date('Y-m-d')),
				'today_long'        => tep_date_long(date('Y-m-d')),
				'store_name'        => sysConfig::get('STORE_NAME'),
				'store_owner'       => sysConfig::get('STORE_OWNER'),
				'store_owner_email' => sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'),
				'store_url'         => sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_CATALOG')
			);
			
			if (!empty($this->eventVars)){
				foreach($this->eventVars as $vInfo){
					if (array_key_exists($vInfo['event_variable'], $this->allowedVars) === false){
						$this->allowedVars[$vInfo['event_variable']] = '';
					}
				}
			}

			EventManager::notify('EmailEventSetAllowedVars', &$this->allowedVars);
		}
	}

	public function parseTemplateSubject(){
		$subject = '';
		if (stristr($this->templateSubjectUnparsed, '{$')){
			$subject = $this->replaceVar($this->templateSubjectUnparsed);
		}else{
			$subject = $this->templateSubjectUnparsed;
		}
		return $subject;
	}

	public function parseTemplateFile(){
		$file = '';
		$this->templateFileUnparsed = str_replace('<--APPEND-->','',$this->templateFileUnparsed);
		if (stristr($this->templateFileUnparsed, '{$')){
			$file = $this->replaceVar($this->templateFileUnparsed);
		}else{
			$file = $this->templateFileUnparsed;
		}
		return $file;
	}

	public function parseTemplateText($allowHTML = true){
		$ifStarted = false;
		$curIfText = '';
		$templateText = '';
		foreach($this->templateUnparsed as $line){
			if (substr($line, 0, 4) == '<!--'){
				$checkVarText = trim(str_replace(array('<!-- if', '(', ')', '$'), '', $line));
				$checkVar = (isset($this->allowedVars[$checkVarText]) ? $this->allowedVars[$checkVarText] : false);
				if ($checkVar){
					$ifSatisfied = true;
				}else{
					$ifSatisfied = false;
				}
				$ifStarted = true;
			}elseif ($ifStarted === true){
				if (substr($line, 0, 3) == '-->'){
					$ifStarted = false;
					if ($ifSatisfied === true){
						$templateText .= $curIfText;
					}
					$curIfText = '';
				}else{
					if ($allowHTML == 0){
						$line = strip_tags($line);
					}
					if (stristr($line, '{$')){
						$curIfText .= $this->replaceVar($line);
					}else{
						$curIfText .= $line;
					}
				}
			}else{
				if ($allowHTML === false){
					$line = strip_tags($line);
				}
				if (stristr($line, '{$')){
					$templateText .= $this->replaceVar($line);
				}else{
					$templateText .= $line;
				}
			}
		}
		$templateText = str_replace('<--APPEND-->','',$templateText);
		return $templateText;
	}

	public function replaceVar($string){
		foreach($this->allowedVars as $varName => $val){
			$string = str_replace('{$' . $varName . '}', $val, $string);
		}
		if (stristr($string, '{$')){
			$newString = '';
			$erasing = false;
			for($i=0, $n=strlen($string); $i<$n; $i++){
				if ($string[$i] == '{'){
					$erasing = true;
				}elseif ($erasing === true){
					if ($string[$i] == '}'){
						$erasing = false;
					}
				}elseif ($erasing === false){
					$newString .= $string[$i];
				}
			}
			$string = $newString;
		}
		return $string;
	}

	public function sendEmail($sendTo = false){
		if (!empty($this->allowedVars)){
			if (isset($this->templateData[0]['EmailTemplatesDescription'][$this->languageId])){
				$emailInfo = $this->templateData[0]['EmailTemplatesDescription'][$this->languageId];

				if (is_null($this->templateSubjectParsed) === true){
					$this->templateSubjectUnparsed = $emailInfo['email_templates_subject'];
					$this->templateSubjectParsed = $this->parseTemplateSubject();
				}

				if (is_null($this->templateFileParsed) === true){
					$this->templateFileUnparsed = $this->templateData[0]['email_templates_attach'];
					EventManager::notify('EmailEventPreParseTemplateFile_' . $this->eventName, &$this->templateFileUnparsed);
					$this->templateFileParsed = $this->parseTemplateFile();
				}

				if (is_null($this->templateParsed) === true){
					$this->templateUnparsed = explode("\n", $emailInfo['email_templates_content']);
					EventManager::notify('EmailEventPreParseTemplateText_' . $this->eventName, &$this->templateUnparsed);
					$this->templateParsed = $this->parseTemplateText();
				}
			}
		}

		if ($sendTo === false){
			$sendTo = $this->sendTo;
		}
		$sendFrom = sysConfig::get('STORE_OWNER');
		$sendFromEmail = sysConfig::get('STORE_OWNER_EMAIL_ADDRESS');
		if (isset($this->allowedVars['store_owner'])){
			$sendFrom = $this->allowedVars['store_owner'];
		}
		if (isset($this->allowedVars['store_owner_email'])){
			$sendFromEmail = $this->allowedVars['store_owner_email'];
		}
		if (isset($sendTo['from_email'])){
			$sendFromEmail = $sendTo['from_email'];
		}
		if (isset($sendTo['from_name'])){
			$sendFrom = $sendTo['from_name'];
		}

		if(isset($sendTo['attach'])){
			$this->templateFileParsed = $sendTo['attach'];
		}

		//echo 'tep_mail(' . $sendTo['name'] . ', ' . $sendTo['email'] . ', ' . $this->templateSubjectParsed . ', ' . $this->templateParsed . ', ' . STORE_OWNER . ', ' . STORE_OWNER_EMAIL_ADDRESS . ')';
		if($this->templateData[0]['is_disabled'] == '0'){
			tep_mail($sendTo['name'], $sendTo['email'], $this->templateSubjectParsed, $this->templateParsed, $sendFrom, $sendFromEmail, $this->templateFileParsed);
		}
	}

	public function setVar($varName, $varValue){
		if (!empty($varName)){
			$this->allowedVars[$varName] = $varValue;
		}
	}

	public function setVars($vars){
		if (!is_array($vars)) return;

		foreach($vars as $varName => $val){
			$this->setVar($varName, $val);
		}
	}

	public function getVar($varName){
		if (tep_not_null($varName)){
			return $this->allowedVars[$varName];
		}
		return false;
	}
}
?>