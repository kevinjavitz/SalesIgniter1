<?php
/*
	Multi Stores Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$multiStore = $appExtension->getExtension('multiStore');
	if ($page_error === false && $multiStore !== false){
		$stores = $multiStore->getStoresArray();
		$languages = tep_get_languages();
		foreach($stores as $sInfo){
			$sID = $sInfo['stores_id'];
			$Pages->StoresPages[$sID]->stores_id = $sID;
			$Pages->StoresPages[$sID]->show_method = $_POST['store_show_method'][$sID];

			if ($_POST['store_show_method'][$sID] == 'use_custom'){
				for($i=0, $n=sizeof($languages); $i<$n; $i++){
					$lID = $languages[$i]['id'];

					$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->pages_title = $_POST['store_pages_title'][$sID][$lID];
					if($appExtension->isEnabled('metaTags')){
						$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->pages_head_title_tag = $_POST['pages_head_title_tag'][$sID][$lID];
						$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->pages_head_desc_tag = $_POST['pages_head_desc_tag'][$sID][$lID];
						$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->pages_head_keywords_tag = $_POST['pages_head_keywords_tag'][$sID][$lID];
					}
					$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->pages_html_text = $_POST['store_pages_html_text'][$sID][$lID];
					$Pages->StoresPages[$sID]->StoresPagesDescription[$lID]->language_id = $lID;
				}
			}else{
				$Pages->StoresPages[$sID]->StoresPagesDescription->delete();
			}
		}
		$Pages->save();		
	}
?>