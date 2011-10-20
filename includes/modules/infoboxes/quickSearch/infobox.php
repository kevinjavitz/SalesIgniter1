<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
class infoBoxQuickSearch extends InfoBoxAbstract {
	public function __construct(){
		global $App;
		$this->init('quickSearch');
		//$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SEARCH'));
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show(){
		$buttonGo = htmlBase::newElement('button')
				->addClass('quickSearchGoButton')
				->setType('submit')
				->setText(' Go ')
				->draw();
		$searchField = htmlBase::newElement('input')
				->setName('keywords')
				->addClass('quickSearchInput')
				->setType('text')
				->setSize('20')
				->draw();

		$boxContent = htmlBase::newElement('form')
					->attr('name', 'quick_find')
					->attr('action', itw_app_link(null, 'products', 'search_result'))
					->attr('method','get')
					->draw();

		$boxContent .= tep_hide_session_id() ;
		$boxContent .=  htmlBase::newElement('span')
				              ->addClass('quickSearchLabel')
				              ->text(sysLanguage::get('INFOBOX_QUICK_SEARCH_TEXT')  .
                                     '<br><a href="' . itw_app_link(null, 'products', 'search') . '"><b>' . sysLanguage::get('INFOBOX_SEARCH_ADVANCED_SEARCH') . '</b></a>' .
                                     '</form><br />')
				              ->draw();
		$boxContent .=  $searchField ;
		$boxContent .=  $buttonGo ;

		$this->setBoxContent($boxContent);
		return $this->draw();
	}
}

?>