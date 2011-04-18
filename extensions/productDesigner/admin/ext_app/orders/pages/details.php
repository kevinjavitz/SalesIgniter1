<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productDesigner_admin_orders_details extends Extension_productDesigner {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('OrderDetailsTabPaneBeforeDraw', null, $this);
	}
	
	public function OrderDetailsTabPaneBeforeDraw(&$order, &$tabsObj){
		$tabContent = '';
		foreach($order->products as $product){
			if (!empty($product['predesign'])){
				if (isset($product['predesign']['front'])){
					$tabContent .= '<h2>"' . $product['name'] . '" - Front Design Settings</h2><br />';
					$predesignId = $product['predesign']['front'];
					$urlVars = array();
					foreach($product['predesign'] as $k => $v){
						$valDisplay = $v;
						if ($k == 'images_id' && $v != 'product'){
							$Qimage = Doctrine_Query::create()
							->select('front_image, back_image')
							->from('ProductDesignerProductImages')
							->where('images_id = ?', $v)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							$valDisplay = '<img src="' . tep_catalog_href_link('imagick_thumb.php', 'path=rel&width=150&height=150&imgSrc=/images/' . $Qimage[0]['front_image']) . '"/>';
						}
						$tabContent .= '<b>' . $k . ':</b> ' . $valDisplay . '<br />';
						if ($k != 'front' && $k != 'back'){
							$urlVars[] = $k . '=' . $v;
						}
					}
				
					$urlVars[] = 'orders_store_id=' . $order->info['store_id'];
				
					if (sizeof($urlVars > 0)){
						$urlVars = '&' . implode('&', $urlVars);
					}else{
						$urlVars = '';
					}
					
					$dl_72 = itw_catalog_app_link('dpi=72&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$dl_150 = itw_catalog_app_link('dpi=150&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$dl_300 = itw_catalog_app_link('dpi=300&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$preview = itw_catalog_app_link('w=300&h=300&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					
					$tabContent .= '<br />' . 
					'<b><u>Front Preview</u></b>&nbsp;' . 
					'Download Full: ' . 
						'<a target="_blank" href="' . $dl_72 . '">72 dpi</a>&nbsp;|&nbsp;' . 
						'<a target="_blank" href="' . $dl_150 . '">150 dpi</a>&nbsp;|&nbsp;' . 
						'<a target="_blank" href="' . $dl_300 . '">300 dpi</a>' . 
					'<br /><br />' . 
					'<img src="' . $preview . '" />';
				}
				
				if (isset($product['predesign']['back']) && $product['predesign']['back'] != 'none'){
					$predesignId = $product['predesign']['back'];
					$urlVars = array();
					foreach($product['predesign'] as $k => $v){
						if ($k != 'front' && $k != 'back'){
							$urlVars[] = $k . '=' . $v;
						}
					}
				
					$urlVars[] = 'orders_store_id=' . $order->info['store_id'];
				
					if (sizeof($urlVars > 0)){
						$urlVars = '&' . implode('&', $urlVars);
					}else{
						$urlVars = '';
					}
					
					$dl_72 = itw_catalog_app_link('dpi=72&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$dl_150 = itw_catalog_app_link('dpi=150&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$dl_300 = itw_catalog_app_link('dpi=300&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					$preview = itw_catalog_app_link('w=300&h=300&appExt=productDesigner&predesign_id=' . $predesignId . $urlVars, 'predesign_thumb', 'process');
					
					$tabContent .= '<br />' . 
					'<b><u>Back Preview</u></b>&nbsp;' . 
					'Download Full: ' . 
						'<a target="_blank" href="' . $dl_72 . '">72 dpi</a>&nbsp;|&nbsp;' . 
						'<a target="_blank" href="' . $dl_150 . '">150 dpi</a>&nbsp;|&nbsp;' . 
						'<a target="_blank" href="' . $dl_300 . '">300 dpi</a>' . 
					'<br /><br />' . 
					'<img src="' .$preview . '" />';
				}
			}
			
			if (!empty($product['custom_design'])){
				$tabContent .= '<h2>"' . $product['name'] . '" - Design Settings</h2><br />';
				$tabContent .= '<b><u>Front Preview</u></b>&nbsp;Download Full: <a target="_blank" href="' . itw_catalog_app_link('dpi=72&appExt=productDesigner&orders_products_id=' . $product['opID'], 'custom_thumb', 'process') . '">72 dpi</a>&nbsp;|&nbsp;<a target="_blank" href="' . itw_catalog_app_link('dpi=150&appExt=productDesigner&orders_products_id=' . $product['opID'], 'custom_thumb', 'process') . '">150 dpi</a>&nbsp;|&nbsp;<a target="_blank" href="' . itw_catalog_app_link('dpi=300&appExt=productDesigner&orders_products_id=' . $product['opID'], 'custom_thumb', 'process') . '">300 dpi</a><br /><br /><img src="' . itw_catalog_app_link('dpi=50&appExt=productDesigner&orders_products_id=' . $product['opID'], 'custom_thumb', 'process') . '" /><br />';

				foreach($product['custom_design'] as $type => $tInfo){
					$tabContent .= '<h2> - Design Type: ' . $type . '</h2><br />';
					foreach($tInfo as $idx => $item){
						$tabContent .= '#' . $idx . ': <ul>';
						foreach($item as $k => $v){
							$tabContent .= '<li><b>' . $k . ':</b> ' . $v . '</li>';
						}
						$tabContent .= '</ul>';
					}
				}
				
			}
		}
		
		if (!empty($tabContent)){
			$tabsObj->addTabHeader('tab_design_settings', array('text' => 'Design Settings'))
			->addTabPage('tab_design_settings', array('text' => $tabContent));
		}
	}
}
?>