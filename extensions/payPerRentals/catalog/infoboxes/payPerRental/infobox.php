<?php
class InfoBoxPayPerRental extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('payPerRental', 'payPerRentals');

		$this->enabled = ((sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') != 'Using calendar after browsing products and clicking Reserve') ? true:false);
		if (isset($_GET['app']) && $_GET['app'] == 'checkout'){
			$this->enabled = false;
		}
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_PAYPERRENTAL'));
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}
	
	
	
	public function show(){
		if ($this->enabled === false) return;
		
		$this->setBoxContent("");
		
		return $this->draw();
	}
	
	public function buildStylesheet(){
		$css = '' . "\n" . 
		'#categoriesPPRBoxMenu.ui-infobox { ' . 
			'padding:0;' . 
			'background: transparent;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-infobox-header { ' . 
			'margin:0;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-infobox-content { ' . 
			'padding:0;' . 
			'margin:0;' . 
			'border-top:none;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-widget-content { ' . 
			'padding:0;' . 
			'background: #eeeeee;' . 
			'font-size:.9em;' . 
			'font-family:Arial;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-widget-content .ui-widget-content { ' . 
			'font-size:1em;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-header { ' . 
			'color:#ffffff;' . 
			'font-weight:bold;' . 
			'margin:0;' . 
			'padding: .5em;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-header.ui-state-hover { ' . 
			'background-color: #d70e0e;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-header.ui-state-active { ' . 
			'border-color: transparent;' . 
			'background-color: #ae0303;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-header .ui-icon { ' . 
			'right: .5em;' . 
			'background-image: url(/ext/jQuery/themes/icons/ui-icons_ffffff_256x240.png);' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-header.ui-corner-all { ' . 
			'border-top: none;' . 
			'border-left: none;' . 
			'border-right: none;' . 
			'border-color: #ffffff;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content { ' . 
			'padding: 0;' . 
			'margin: 0;' . 
			'border:none;' . 
			'background: transparent;' . 
			'overflow:visible;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content ul { ' . 
			'list-style: none;' . 
			'padding: 0;' . 
			'margin: 0;' . 
			'margin: .1em;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content li { ' . 
			'font-size: 1em;' . 
			'padding: .1em 0;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content li ul { ' . 
			'width: 150px;' . 
			'padding: .2em;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content li a { ' . 
			'text-decoration: none;' . 
			'display:block;' . 
			'padding: .1em;' . 
			'margin-left: auto;' . 
			'margin-right: auto;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content li .ui-icon { ' . 
			'margin-right: .3em;' . 
		' }' . "\n" . 
		'#categoriesPPRBoxMenu .ui-accordion-content li a:hover, ' . 
		'#categoriesPPRBoxMenu .ui-accordion .ui-accordion-content li a.selected { ' .
			'background: #e6e6e6;' . 
		' }' . "\n" . 
		'#headPPRBoxMenu { ' . 
			'height:30px;' . 
			'padding-left:10px;' . 
			'line-height:25px;' . 
			'color:#ffffff;' . 
		' }' . "\n" . 
		'' . "\n";
		
		return $css;
	}
}
?>