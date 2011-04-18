<?php
/*
	Multi Stores Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false){
		$Qdelete = Doctrine_Query::create()
		->delete('ProductDesignerPredesignKeysToStores')
		->where('stores_id = ?', $Store->stores_id)
		->execute();
		
		if (isset($_POST['text_id'])){
			foreach($_POST['text_id'] as $id => $val){
				$entry = new ProductDesignerPredesignKeysToStores();
				$entry->stores_id = $Store->stores_id;
				$entry->key_id = $id;
				$entry->content = $val;
				$entry->use_color_replace = (isset($_POST['text_color_replace'][$id]) ? '1' : '0');
				$entry->save();
			}
		}
		
		if (isset($_POST['clipart'])){
			foreach($_POST['clipart'] as $id => $cInfo){
				$entry = new ProductDesignerPredesignKeysToStores();
				$entry->stores_id = $Store->stores_id;
				$entry->key_id = $id;
				
				foreach($cInfo as $tone => $fileName){
					if ($tone == 'default'){
						$contentKey = 'content';
						$colorReplaceKey = 'use_color_replace';
					}elseif ($tone == 'light'){
						$contentKey = 'content_light';
						$colorReplaceKey = 'use_color_replace_light';
					}elseif ($tone == 'dark'){
						$contentKey = 'content_dark';
						$colorReplaceKey = 'use_color_replace_dark';
					}

					$entry->$contentKey = $fileName;
					if (isset($_POST['clipart_color_replace'][$id][$tone])){
						$entry->$colorReplaceKey = '1';
					}else{
						$entry->$colorReplaceKey = '0';
					}
				}
				$entry->save();
			}
		}
		
		$Store->designer_dark_primary_color = $_POST['dark_primary_color'];
		$Store->designer_dark_secondary_color = $_POST['dark_secondary_color'];
		$Store->designer_light_primary_color = $_POST['light_primary_color'];
		$Store->designer_light_secondary_color = $_POST['light_secondary_color'];
		$Store->save();
	}
?>