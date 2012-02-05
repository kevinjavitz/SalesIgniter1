<?php
	if(isset($Plan)){
		$Plan->is_affiliate = (int)(isset($_POST['is_affiliate']) ? $_POST['is_affiliate'] : '0');
		$Plan->save();
	}
?>