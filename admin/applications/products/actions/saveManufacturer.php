<?php
	if (!empty($_POST)){
		$Manufacturers = Doctrine_Core::getTable('Manufacturers');
		if (isset($_GET['mID'])){
			$Manufacturer = $Manufacturers->find($_GET['mID']);
		}

		if (!isset($Manufacturer) || !$Manufacturer){
			$Manufacturer = $Manufacturers->create();
		}

		$Manufacturer->manufacturers_name = $_POST['manufacturers_name'];

		$manufacturers_image = new upload('manufacturers_image', DIR_FS_CATALOG_IMAGES);
		if ($manufacturers_image){
			$Manufacturer->manufacturers_image = $manufacturers_image->filename;
		}

		$ManInfo =& $Manufacturer->ManufacturersInfo;
		$ManInfo->delete();
		$languages = tep_get_languages();
		for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
			//HTC BOC
			$htcTitles = $_POST['manufacturers_htc_title_tag'];
			$htcDescs = $_POST['manufacturers_htc_desc_tag'];
			$htcKeywords = $_POST['manufacturers_htc_keywords_tag'];
			//HTC EOC
			$langId = $languages[$i]['id'];

			$ManInfo[$langId]->manufacturers_url = $_POST['manufacturers_url'][$langId];
			$ManInfo[$langId]->manufacturers_htc_description = $_POST['manufacturers_htc_description'][$langId];
			$ManInfo[$langId]->manufacturers_htc_title_tag = $_POST['manufacturers_name'];
			$ManInfo[$langId]->manufacturers_htc_desc_tag = $_POST['manufacturers_name'];
			$ManInfo[$langId]->manufacturers_htc_keywords_tag = $_POST['manufacturers_name'];
			$ManInfo[$langId]->languages_id = $langId;

			if (!empty($htcTitles[$langId])){
				$ManInfo[$langId]->manufacturers_htc_title_tag = $htcTitles[$langId];
			}

			if (!empty($htcDescs[$langId])){
				$ManInfo[$langId]->manufacturers_htc_desc_tag = $htcDescs[$langId];
			}

			if (!empty($htcKeywords[$langId])){
				$ManInfo[$langId]->manufacturers_htc_keywords_tag = $htcKeywords[$langId];
			}
		}
		$Manufacturer->save();
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'mID')) . 'mID=' . $Manufacturer->manufacturers_id), 'redirect');
?>