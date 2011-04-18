<?php
class InfoBoxInfoPages extends InfoBoxAbstract {
	
	public function __construct(){
		global $App;
		$this->init('infoPages', 'infoPages');

		$this->enabled = (sysConfig::get('EXTENSION_INFO_PAGES_INFOBOX') == 'True');
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_INFOPAGES'));
	}
	
	public function getPageList(){
		global $appExtension;
		$infoPages = $appExtension->getExtension('infoPages');
				
		$pages = $infoPages->getInfoPage(null, null, true, true);
		$multiStore = $appExtension->getExtension('multiStore');
		if ($multiStore !== false && $multiStore->isEnabled() === true){
			$checkMultiStore = true;
		}else{
			$checkMultiStore = false;
		}
		
		$page_list = '<ul>';
		foreach($pages as $pInfo){
			$id = $pInfo['pages_id'];
			$key = $pInfo['page_key'];
			$description = $pInfo['PagesDescription'][Session::get('languages_id')];
			$linkTarget = $description['link_target'];
			$intOrExt = $description['intorext'];
			$extLink = $description['externallink'];
			$title = $description['pages_title'];
			
			if ($pInfo['page_type'] != 'field' && $checkMultiStore === true && Session::exists('current_store_id')){
				if ($pInfo['StoresPages'][Session::get('current_store_id')]['show_method'] == 'use_custom'){
					$title = $pInfo['StoresPages'][Session::get('current_store_id')]['StoresPagesDescription'][Session::get('languages_id')]['pages_title'];
				}
			}
			
			if ($pInfo['page_type'] == 'field'){
				$title = $infoPages->getFieldPageTitle($pInfo);
			}
			
			$linkEl = htmlBase::newElement('a')
			->html(htmlspecialchars($title));
			
			if ($linkTarget == '1'){
				$linkEl->attr('target', '_blank');
			}
			
			if ($intOrExt == 1){
				$linkEl->setHref($extLink);
			}elseif ($title != 'Contact Us'){
				if (!empty($key)){
					$linkEl->setHref(itw_app_link('appExt=infoPages', 'show_page', $key));
				}else{
					$linkEl->setHref(itw_app_link('appExt=infoPages&pages_id=' . $id, 'show_page', 'default'));
				}
			}else{
				$linkEl->setHref(itw_app_link(null, 'contact_us', 'default'));
			}
		
			$page_list .= '<li>'. $linkEl->draw() . '</li>';
		}
		return $page_list.'</ul>';
	}
	
	public function show(){
		if ($this->enabled === false) return;
		
		$this->setBoxContent($this->getPageList());
		
		return $this->draw();
	}
}
?>