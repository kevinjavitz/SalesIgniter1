<?php
	if (isset($_POST['categories_description2'])){
		foreach($_POST['categories_description2'] as $langId => $description){
			$Category->CategoriesDescription[$langId]->categories_description2 = $description;
		}
		$Category->save();
	}
?>