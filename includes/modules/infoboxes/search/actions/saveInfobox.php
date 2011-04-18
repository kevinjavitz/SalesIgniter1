<?php
Doctrine_Query::create()
	->delete('TemplatesInfoboxSearchGuided')
	->where('template_name = ?', $templateName)
	->execute();

$TemplatesInfoboxSearchGuided = Doctrine_Core::getTable('TemplatesInfoboxSearchGuided');
foreach($_POST['option'] as $type => $oInfo){
	foreach($oInfo as $oID){
		$sortOrder = $_POST['option_sort'][$type][$oID];
		$optionHeading = $_POST['option_heading'][$type][$oID];

		$SearchOption = $TemplatesInfoboxSearchGuided->findOneByOptionIdAndTemplateNameAndOptionType($oID, $templateName, $type);
		if (!$SearchOption){
			$SearchOption = new TemplatesInfoboxSearchGuided();
			$SearchOption->option_type = $type;
			$SearchOption->option_id = (int)$oID;
			$SearchOption->template_name = $templateName;
		}
		$SearchOption->option_sort = $sortOrder;
		$SearchOption->TemplatesInfoboxSearchGuidedDescription[Session::get('languages_id')]->search_title = $optionHeading;
		$SearchOption->save();
	}
}
