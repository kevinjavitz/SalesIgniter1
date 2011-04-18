<?php
	if (isset($_POST['product_designer_correlation_type'])){
		$Category->product_designer_correlation_type = $_POST['product_designer_correlation_type'];
		if ($_POST['product_designer_correlation_type'] == 'activity'){
			$Category->product_designer_correlation_id = $_POST['product_designer_activity_correlation'];
		}else{
			$Category->product_designer_correlation_id = $_POST['product_designer_category_correlation'];
		}
		$Category->save();
	}
?>