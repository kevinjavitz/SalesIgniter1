<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_infoPages extends ExtensionBase {

	public function __construct(){
		global $App;
		parent::__construct('infoPages');

		if ($App->getEnv() == 'catalog' && isset($_GET['appExt']) && $_GET['appExt'] == 'infoPages'){
			if (isset($_GET['pages_id'])){
				$this->pageId = (int) $_GET['pages_id'];
			}else{
				$this->pageKey = $_GET['appPage'];
			}

			$App->setAppPage('default');
			$_GET['actualPage'] = $this->pageKey;
		}
	}

	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;

		if ($appExtension->isAdmin()){
			EventManager::attachEvent('BoxCmsAddLink', null, $this);
		} else {
			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				}else{
					$this->checkMultiStore = false;
				}
			}
			if($this->checkMultiStore){
				if (isset($_GET['appExt']) && $_GET['appExt'] == 'infoPages'){
					EventManager::attachEvents(array(
						'PageLayoutHeaderTitle',
						'PageLayoutHeaderMetaDescription',
						'PageLayoutHeaderMetaKeyword'
					), null, $this);
				}
			}
		}
	}

	public function PageLayoutHeaderTitle(&$title){
		$Query = Doctrine_Query::create()
			->select('p.pages_id,pd.pages_head_title_tag,sp.*,spd.pages_head_title_tag') //If select is not included, the following error is thrown: "The root class of the query (alias p) must have at least one field selected."
			->from('Pages p')
			->leftJoin('p.PagesDescription pd')
			->leftJoin('p.StoresPages sp')
			->leftJoin('sp.StoresPagesDescription spd')
			->where('pd.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('sp.stores_id = ?', (int)Session::get('current_store_id'))
			->andWhere('p.page_key = ?', $_GET['appPage']);

		$Result = $Query->fetchArray();
		if(count($Result)){
			if(count($Result[0]['StoresPages'][0]['StoresPagesDescription']) <= 0){
				$title = $Result[0]['PagesDescription'][0]['pages_head_title_tag'];

			} else {
				$title = $Result[0]['StoresPages'][0]['StoresPagesDescription'][0]['pages_head_title_tag'];
			}
		}
	}

	public function PageLayoutHeaderMetaDescription(&$desc){
		$Query = Doctrine_Query::create()
			->select('p.pages_id,pd.pages_head_desc_tag,sp.*,spd.pages_head_desc_tag') //If select is not included, the following error is thrown: "The root class of the query (alias p) must have at least one field selected."
			->from('Pages p')
			->leftJoin('p.PagesDescription pd')
			->leftJoin('p.StoresPages sp')
			->leftJoin('sp.StoresPagesDescription spd')
			->where('pd.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('sp.stores_id = ?', (int)Session::get('current_store_id'))
			->andWhere('p.page_key = ?', $_GET['appPage']);

		$Result = $Query->fetchArray();
		if(count($Result)){
			if(count($Result[0]['StoresPages'][0]['StoresPagesDescription']) <= 0){
				$desc = $Result[0]['PagesDescription'][0]['pages_head_desc_tag'];

			} else {

				$desc = $Result[0]['StoresPages'][0]['StoresPagesDescription'][0]['pages_head_desc_tag'];
			}
		}
	}

	public function PageLayoutHeaderMetaKeyword(&$keys){
		$Query = Doctrine_Query::create()
			->select('p.pages_id,pd.pages_head_keywords_tag,sp.*,spd.pages_head_keywords_tag') //If select is not included, the following error is thrown: "The root class of the query (alias p) must have at least one field selected."
			->from('Pages p')
			->leftJoin('p.PagesDescription pd')
			->leftJoin('p.StoresPages sp')
			->leftJoin('sp.StoresPagesDescription spd')
			->where('pd.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('sp.stores_id = ?', (int)Session::get('current_store_id'))
			->andWhere('p.page_key = ?', $_GET['appPage']);

		$Result = $Query->fetchArray();
		if(count($Result)){
			if(count($Result[0]['StoresPages'][0]['StoresPagesDescription']) <= 0){
				$keys = $Result[0]['PagesDescription'][0]['pages_head_keywords_tag'];

			} else {
				$keys = $Result[0]['StoresPages'][0]['StoresPagesDescription'][0]['pages_head_keywords_tag'];
			}
		}
	}

	public function BoxCmsAddLink(&$contents){
		$contents['children'][] = array(
			'link' => itw_app_link('appExt=infoPages','manage','default','SSL'),
			'text' => 'Manage Pages'
		);
	}

	public function getInfoPage($pageId = null, $languageId = null, $shownInInfobox = false, $mustBeEnabled = true){
		global $appExtension;
		if (!isset($this->checkMultiStore)){
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled() === true){
				$this->checkMultiStore = true;
			}else{
				$this->checkMultiStore = false;
			}
		}

		$Query = Doctrine_Query::create()
		->select('*') //If select is not included, the following error is thrown: "The root class of the query (alias p) must have at least one field selected."
		->from('Pages p')
		->leftJoin('p.PagesDescription pd')
		->leftJoin('p.PagesFields pf');

		if (is_null($languageId) === false){
			$Query->where('pd.language_id = ?', (int)$languageId);
		}else{
			$Query->where('pd.language_id = ?', (int)Session::get('languages_id'));
		}

		if ($this->checkMultiStore === true && is_null($pageId) === false){
			$Query->addSelect('sp.stores_pages_id, sp.show_method, spd.pages_title, spd.pages_html_text, spd.language_id')
			->leftJoin('p.StoresPages sp')
			->leftJoin('sp.StoresPagesDescription spd');
		}

		if (is_null($pageId) === false){
			if (is_numeric($pageId)){
				$Query->andWhere('p.pages_id = ?', $pageId);
			}else{
				$Query->andWhere('p.page_key = ?', $pageId);
			}
		}

		if ($shownInInfobox === true){
			$Query->andWhere('p.infobox_status = ?', 1)
			->addSelect('pd.intorext, pd.externallink, pd.link_target');
		}

		if ($mustBeEnabled === true){
			$Query->andWhere('p.status = ?', 1);
		}
		$Query->orderBy('p.sort_order');
		EventManager::notify('InfoPagesQueryBeforeExecute', &$Query);

		if (is_null($pageId) === false){
			$Result = $Query->fetchOne();
		}else{
			$Result = $Query->execute();
		}
		return $Result;
	}

	public function getFieldPageTitle(&$Page){
		if ($Page['PagesFields']['listing_type'] == 'attribute'){
			$Qtitle = Doctrine_Query::create()
			->select('products_options_name as Title')
			->from('ProductsOptionsDescription')
			->where('language_id = ?', Session::get('languages_id'))
			->andWhere('products_options_id = ?', $Page['PagesFields']['listing_attribute_id']);
		}else{
			$Qtitle = Doctrine_Query::create()
			->select('field_name as Title')
			->from('ProductsCustomFieldsDescription')
			->where('language_id = ?', Session::get('languages_id'))
			->andWhere('field_id = ?', $Page['PagesFields']['listing_field_id']);
		}
		$Result = $Qtitle->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Result[0]['Title'];
	}

	private function getLetterValues($letter, $listingType, $id, &$ResultHtml){
		if ($listingType == 'attribute'){
			$Qtext = Doctrine_Query::create()
			->select('DISTINCT ovd.products_options_values_name as Text, ovd.products_options_values_id as Id')
			->from('ProductsAttributes a')
			->leftJoin('a.ProductsOptionsValues ov')
			->leftJoin('ov.ProductsOptionsValuesDescription ovd')
			->where('ovd.language_id = ?', Session::get('languages_id'))
			->andWhere('a.options_id = ?', $id)
			->andWhere('ovd.products_options_values_name LIKE ?', $letter . '%');
		}else{
			$Qtext = Doctrine_Query::create()
			->select('DISTINCT value as Text, field_id as Id')
			->from('ProductsCustomFieldsToProducts')
			->where('field_id = ?', $id)
			->andWhere('(value LIKE "' . $letter . '%" or value LIKE "%;' . $letter . '%")');
		}
		$Result = $Qtext->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$ResultHtml .= '<h1>' . strtoupper($letter) . '</h1>';
		if ($Result){
			$ResultHtml .= '<ul>';
			foreach($Result as $tInfo){
				if ($listingType == 'field' && strstr($tInfo['Text'], ';')){
					$items = explode(';', $tInfo['Text']);
					foreach($items as $itemInfo){
						if (substr($itemInfo, 0, 1) == $letter){
							$ResultHtml .= '<li><a href="' . itw_app_link('field_id=' . $tInfo['Id'] . '&field_val=' . $itemInfo, 'products', 'search_result') . '">' . ucwords($itemInfo) . '</a></li>';
						}
					}
				}else{
					$ResultHtml .= '<li><a href="' . itw_app_link(($listingType == 'attribute' ? 'option_value_id=' . $tInfo['Id'] : 'field_id=' . $tInfo['Id'] . '&field_val=' . $tInfo['Text']), 'products', 'search_result') . '">' . ucwords($tInfo['Text']) . '</a></li>';
				}
			}
			$ResultHtml .= '</ul>';
		}
	}

	public function getFieldPageContent(&$Page){
		$listingType = $Page['PagesFields']['listing_type'];
		if ($listingType == 'attribute'){
			$id = $Page['PagesFields']['listing_attribute_id'];
		}else{
			$id = $Page['PagesFields']['listing_field_id'];
		}

		$ResultHtml = '<table width="100%"><tr><td valign="top">';
		foreach(range('a','m') as $letter){
			$this->getLetterValues($letter, $listingType, $id, &$ResultHtml);
		}
		$ResultHtml .= '</td>';

		$ResultHtml .= '<td valign="top">';
		foreach(range('n','z') as $letter){
			$this->getLetterValues($letter, $listingType, $id, &$ResultHtml);
		}
		$ResultHtml .= '</td></tr></table>';
		return $ResultHtml;
	}

	public function displayContentBlock($id){
		$block = $this->getInfoPage($id, Session::get('languages_id'), false, true);
		if ($block && isset($block['PagesDescription'][Session::get('languages_id')])){
			$content = $block['PagesDescription'][Session::get('languages_id')]['pages_html_text'];
			if ($this->checkMultiStore === true){
				if ($block['StoresPages'][Session::get('current_store_id')]['show_method'] == 'use_custom'){
					$content = $block['StoresPages'][Session::get('current_store_id')]['StoresPagesDescription'][Session::get('languages_id')]['pages_html_text'];
				}
			}
			return stripslashes($content);
		}
	}
}
?>
