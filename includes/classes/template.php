<?php
class Template {
	public function __construct($fileName, $baseDir = null){
		$this->templateVars = array();
		$this->isPopup = false;
		if (!is_null($baseDir)){
			if (substr($baseDir, -1) != '/'){
				$baseDir .= '/';
			}
		}else{
			$baseDir = '/';
		}

		if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/' . $baseDir . $fileName)){
			$this->baseDir = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/' . $baseDir;
		}elseif (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/' . $fileName)){
			$this->baseDir = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/';
		}elseif (file_exists(sysConfig::getDirFsCatalog() . 'templates/fallback/' . $baseDir . $fileName)){
			$this->baseDir = sysConfig::getDirFsCatalog() . 'templates/fallback/' . $baseDir;
		}elseif (file_exists($baseDir . $fileName)){
			$this->baseDir = $baseDir;
		}else{
			$this->baseDir = sysConfig::getDirFsCatalog() . 'templates/fallback/';
		}
		
		$this->setTemplateFile($fileName);
	}
	
	public function setPopupMode($val){
		$this->isPopup = $val;
	}
	
	public function setTemplateFile($fileName, $baseDir = null){
		if (!is_null($baseDir)){
			$this->baseDir = $baseDir;
		}
		$this->layoutFile = $fileName;
		return $this;
	}
	
	public function set($varName, $value){
		$this->templateVars[$varName] = $value;
		return $this;
	}
	
	public function setReference($varName, $value){
		$this->templateVars[$varName] = &$value;
		return $this;
	}
	
	public function setVars(array $vars){
		foreach($vars as $k => $v){
			$this->set($k, $v);
		}
		return $this;
	}

	public function &getVar($varName){
		return $this->templateVars[$varName];
	}

	public function getVars(){
		return $this->templateVars;
	}

	public function appendVar($varName, $value){
		if (!isset($this->templateVars[$varName])){
			$this->templateVars[$varName] = '';
		}
		
		if (is_array($this->templateVars[$varName])){
			$this->templateVars[$varName][] = $value;
		}else{
			$this->templateVars[$varName] .= $value;
		}
		return $this;
	}
	
	public function remove($varName){
		if (isset($this->templateVars[$varName])){
			unset($this->templateVars[$varName]);
		}
		return $this;
	}
	
	public function parse($useFile = null){
		global $App, $appExtension, $messageStack, $ExceptionManager, $request_type;
		$userAccount = &Session::getReference('userAccount');
		
		$file = $this->layoutFile;
		if (is_null($useFile) === false) $file = $useFile;
		
		foreach($this->templateVars as $var => $val){
			if (is_object($val) && get_class($val) == 'Template'){
				$this->templateVars[$var] = $val->parse();
			}
		}
		extract($this->templateVars, EXTR_REFS);

		ob_start();
		if ($this->isPopup === true){
			echo $this->templateVars['pageContent'];
		}else{
			require($this->baseDir . $file);
		}
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
}
