<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

abstract class InfoBoxAbstract {
	private $boxId = null;
	private $boxPath = null;
	private $boxHeadingText = null;
	private $boxHeadingLink = null;
	private $boxContent = null;
	private $boxForm = null;
	private $boxButtons = null;
	private $installed = false;
	private $boxTemplateDefaultDir = null;
	private $boxTemplateDefault = 'box.tpl';
	private $boxWidgetProperties = '';
	private $boxCurrentTemplateDir = null;
	private $boxTemplateFile = null;
	private $boxTemplateDir = null;
	private $extName = null;
	private $templateVars = array();
	
	public function init($boxCode, $boxPath = null){
		global $App;
		$this->boxCode = $boxCode;
		$this->boxTemplateDefaultDir = sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates/';
		$this->boxCurrentTemplateDir = sysConfig::getDirFsCatalog() . 'templates/' . (Session::exists('tplDir') ? Session::get('tplDir') : 'fallback') . '/boxes/';

		if (is_null($boxPath) === false){
			$widgetPath = $boxPath . '/';
		}else{
			$widgetPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgets/' . $this->boxCode . '/';
		}
		$langPath = $widgetPath . '/language_defines/global.xml';
		$DoctPath = $widgetPath . '/Doctrine/base/';
		$overwritePath = 'includes/languages/' . Session::get('language') . '/' . str_replace('language_defines/', '', $langPath);

		$this->boxPath = $widgetPath;

		sysLanguage::loadDefinitions($langPath);
		sysLanguage::loadDefinitions($overwritePath);
		//load language
		if(Session::exists('tplDir') && file_exists(sysConfig::getDirFsCatalog() .'templates/'.Session::get('tplDir') . '/language_defines/global.xml')){
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() .'templates/'.Session::get('tplDir') . '/language_defines/global.xml');
		}
		if (is_dir($DoctPath)){
			Doctrine_Core::loadModels($DoctPath, Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
		}
	}

	public function getPath(){
		return $this->boxPath;
	}
	
	public function getExtName(){
		return $this->extName;
	}
	
	public function isInstalled(){
		return $this->installed;
	}
	
	public function getBoxCode(){
		return $this->boxCode;
	}
	
	public function setBoxTemplateFile($val){
		$this->boxTemplateFile = $val;
	}
	
	public function setBoxTemplateDir($val){
		$this->boxTemplateDir = $val;
	}
	
	public function setBoxHeading($val){
		$this->boxHeadingText = $val;
	}
	
	public function setBoxHeadingLink($val){
		$this->boxHeadingLink = $val;
	}

	public function setWidgetProperties($val){
		$this->boxWidgetProperties = $val;
	}

	public function getWidgetProperties(){
		return $this->boxWidgetProperties;
	}
	
	public function setBoxContent($val){
		$this->boxContent = $val;
	}

	public function setBoxForm($val){
		$this->boxForm = $val;
	}

	public function setBoxButtons($val){
		$this->boxButtons = $val;
	}


	public function setBoxId($val){
		$this->boxId = $val;
	}
	
	public function getBoxTemplateFile(){
		return $this->boxTemplateFile;
	}
	
	public function getBoxTemplateDir(){
		return $this->boxTemplateDir;
	}
	
	public function getBoxHeading(){
		return $this->boxHeadingText;
	}
	
	public function getBoxHeadingLink(){
		return $this->boxHeadingLink;
	}
	
	public function getBoxContent(){
		return $this->boxContent;
	}
	
	public function show(){
		return $this->draw();
	}

	public function setTemplateVar($var, $val){
		$this->templateVars[$var] = $val;
	}
	
	public function draw(){
		$WidgetSettings = $this->getWidgetProperties();

		$templateFile = $this->boxTemplateDefault;
		if (isset($WidgetSettings->template_file) && is_null($WidgetSettings->template_file) === false){
			$templateFile = $WidgetSettings->template_file;
		}
		
		$boxTemplate = new Template($templateFile, $this->boxTemplateDefaultDir);

		$this->templateVars['boxHeading'] = $this->boxHeadingText;
		$this->templateVars['boxContent'] = $this->boxContent;
		if (!is_null($this->boxId)){
			$this->templateVars['box_id'] = $this->boxId;
		}

		if (!is_null($this->boxForm)){
			$this->templateVars['boxForm'] = $this->boxForm;
		}

		if (!is_null($this->boxButtons)){
			$this->templateVars['boxButtons'] = $this->boxButtons;
		}

		if (is_null($this->boxHeadingLink) === false){
			$link = htmlBase::newElement('a')
			->setHref($this->boxHeadingLink)
			->attr('alt', 'more')
			->attr('title', 'more')
			->addClass('ui-icon ui-icon-circle-triangle-e');

			$this->templateVars['boxLink'] = $link->draw();
		}
		
		$boxTemplate->setVars($this->templateVars);
		
		return $boxTemplate->parse();
	}
}
?>