<?php
    if (isset($_POST['ppr_show_in_menu'])){
		$Category->ppr_show_in_menu = '1';	
	}else{
		$Category->ppr_show_in_menu = '0';
	}
	$Category->save();
?>