<?php
	$Categories = Doctrine_Core::getTable('PhotoGalleryCategories');
	if (isset($_GET['cID'])){
		$Category = $Categories->findOneByCategoriesId((int)$_GET['cID']);
	}else{
		$Category = $Categories->create();
		if (isset($_GET['parent_id'])){
			$Category->parent_id = $_GET['parent_id'];
		}
	}

	if (isset($_POST['parent_id']) && $_POST['parent_id'] > -1){
		$Category->parent_id = $_POST['parent_id'];
	}

	$Category->sort_order = (int)$_POST['sort_order'];

	if ($categories_image = new upload('categories_image', sysConfig::getDirFsCatalog() . 'images')) {
		$Category->categories_image = $_POST['categories_image'];
	}

	$languages = tep_get_languages();
	$CategoriesDescription =& $Category->PhotoGalleryCategoriesDescription;
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$lID = $languages[$i]['id'];

		$CategoriesDescription[$lID]->language_id = $lID;
		$CategoriesDescription[$lID]->categories_title = $_POST['categories_name'][$lID];
		$CategoriesDescription[$lID]->categories_description_text = $_POST['categories_description'][$lID];

		//$CategoriesDescription[$lID]->categories_seo_url = $_POST['categories_seo_url'][$lID];
	}
	$AdditionalImages =& $Category->ImagesToCategories;
	$AdditionalImages->delete();
	if (isset($_POST['additional_images']) && !empty($_POST['additional_images'])){
		$saved = array();
		$imgArr = explode(';', $_POST['additional_images']);
		foreach($imgArr as $fileName){
			if (!in_array($fileName, $saved)){
				$idx = $AdditionalImages->count();
				$AdditionalImages[$idx+1]->file_name = $fileName;
				$lFileName = str_lreplace('.','_',$fileName);
				if(isset($_POST['caption_'.$lFileName]) && !empty($_POST['caption_'.$lFileName])){
					$AdditionalImages[$idx+1]->caption = $_POST['caption_'.$lFileName];
				}
				if(isset($_POST['desc_'.$lFileName]) && !empty($_POST['desc_'.$lFileName])){
					$AdditionalImages[$idx+1]->big_caption = $_POST['desc_'.$lFileName];
				}
				$saved[] = $fileName;
			}
		}
	}


	/*
	 * anything additional to handle into $ArticlesDescription ?
	 */
	EventManager::notify('PhotoGalleryCategoriesDescriptionsBeforeSave', &$CategoriesDescription);

	$Category->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $Category->categories_id, null, 'default'), 'redirect');
?>
