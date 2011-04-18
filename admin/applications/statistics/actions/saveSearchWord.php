<?php
	$SearchwordSwap = Doctrine_Core::getTable('SearchwordSwap');
	if (isset($_GET['word_id'])){
		$Record = $SearchwordSwap->find($_GET['word_id']);
	}else{
		$Record = $SearchwordSwap->create();
	}
	
	$Record->sws_word = $_POST['original_word'];
	$Record->sws_replacement = $_POST['replacement_word'];
	$Record->save();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>