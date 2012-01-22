<?php
	$link = itw_app_link(tep_get_all_get_params(array('action')));
	if (isset($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id'])) {
		$categories_id = (int)$_POST['categories_id'];
		$new_parent_id = (int)$_POST['move_to_category_id'];

		$path = explode('_', tep_get_generated_category_path_ids($new_parent_id));

		if (in_array($categories_id, $path)) {
			$messageStack->addSession('pageStack', sysLanguage::get('ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT'), 'error');
		} else {
			Doctrine_Query::create()
			->update('PhotoGalleryCategories')
			->set('parent_id', '?', $new_parent_id)
			->where('categories_id = ?', $categories_id)
			->execute();;

			$messageStack->addSession('pageStack', 'Category has been moved', 'success');

			$link = itw_app_link(tep_get_all_get_params(array('action', 'cPath', 'cID')) . ($new_parent_id > 0 ? 'cPath=' . $new_parent_id . '&' : '') . 'cID=' . $categories_id);
		}
	}
	EventManager::attachActionResponse($link, 'redirect');
?>