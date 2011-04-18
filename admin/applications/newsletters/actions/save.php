<?php
	$newsletter_module = $_POST['module'];
	$title = $_POST['title'];
	$content = $_POST['content'];

	$newsletter_error = false;
	if (empty($_POST['title'])) {
		$messageStack->add(sysLanguage::get('ERROR_NEWSLETTER_TITLE'), 'error');
		$newsletter_error = true;
	}

	if (empty($_POST['module'])) {
		$messageStack->add(sysLanguage::get('ERROR_NEWSLETTER_MODULE'), 'error');
		$newsletter_error = true;
	}

	if ($newsletter_error == false){
		$Newsletters = Doctrine_Core::getTable('Newsletters');
		if (isset($_GET['nID'])){
			$Newsletter = $Newsletters->find((int) $_GET['nID']);
		}else{
			$Newsletter = $Newsletters->create();
			$Newsletter->status = '0';
			$Newsletter->locked = '0';
		}
		
		$Newsletter->title = $_POST['title'];
		$Newsletter->content = $_POST['content'];
		$Newsletter->module = $_POST['module'];
		
		$Newsletter->save();

		EventManager::attachActionResponse(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $Newsletter->newsletters_id, 'newsletters', 'default'), 'redirect');
	}
?>