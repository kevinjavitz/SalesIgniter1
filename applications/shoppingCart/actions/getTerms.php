<?php
    if(isset($_POST['get'])){
		$infoPages = $appExtension->getExtension('infoPages');
		$pageInfo = $infoPages->getInfoPage('conditions');

		$terms = $pageInfo['PagesDescription'][Session::get('languages_id')]['pages_html_text'];


		$json = array(
				'success' => true,
				'html' => $terms
		);
		EventManager::attachActionResponse($json, 'json');
	}else{
		Session::set('agreed_terms', $_POST['agree']);
		$json = array(
				'success' => true
		);
		EventManager::attachActionResponse($json, 'json');
	}


?>