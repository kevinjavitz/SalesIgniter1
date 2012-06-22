<?php
	if(isset($Plan)){
		$Plan->ppr_rentals = (int)(isset($_POST['ppr_rentals']) ? $_POST['ppr_rentals'] : '0');
		$Plan->save();
	}
?>