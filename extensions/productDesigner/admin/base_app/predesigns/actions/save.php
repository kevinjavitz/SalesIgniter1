<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Predesigns = Doctrine_Core::getTable('ProductDesignerPredesigns');
	if (isset($_GET['dID'])){
		$Predesign = $Predesigns->findOneByPredesignId($_GET['dID']);
	}else{
		$Predesign = $Predesigns->create();
	}
	$Predesign->predesign_name = $_POST['design_name'];
	$Predesign->predesign_cost = $_POST['design_cost'];
	$Predesign->predesign_location = $_POST['design_location'];
	$Predesign->predesign_settings = serialize($_POST['item']);
	$Predesign->predesign_activities = (isset($_POST['activities']) ? implode(',', $_POST['activities']) : '');
	$Predesign->predesign_classes = (isset($_POST['classes']) ? implode(',', $_POST['classes']) : '');
	
	$PredesignsToCategories =& $Predesign->ProductDesignerPredesignsToPredesignCategories;
	$PredesignsToCategories->delete();
	if (isset($_POST['categories'])){
		foreach($_POST['categories'] as $cID){
			$PredesignsToCategories[]->categories_id = $cID;
		}
	}
	
	$Predesign->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');
?>