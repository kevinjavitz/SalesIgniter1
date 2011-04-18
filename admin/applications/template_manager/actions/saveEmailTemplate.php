<?php
	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
	if (isset($_POST['template_id'])){
		$Template = $EmailTemplates->find((int) $_POST['template_id']);
	}else{
		$Template = $EmailTemplates->create();
	}
	
	$Template->email_templates_name = $_POST['email_template'];
	$Template->email_templates_event = $_POST['email_event'];
	$attach_file = new upload('email_file', sysConfig::getDirFsCatalog() . 'images/upload');
	if ($attach_file && !empty($attach_file->filename)){
		$Template->email_templates_attach = 'images/upload/' . $attach_file->filename;
	}else{
		$Template->email_templates_attach = '';
	}

	
	if (isset($_POST['variable'])){
		$Variables = $Template->EmailTemplatesVariables;
		
		if (isset($_POST['variable']['standard'])){
			foreach($_POST['variable']['standard'] as $k => $v){
				$Variable = new EmailTemplatesVariables();
				$Variable->event_variable = $v;
				$Variable->is_conditional = '0';
				//$Variable->save();

				$Variables[] = $Variable;
			}
		}
		
		if (isset($_POST['variable']['condition'])){
			foreach($_POST['variable']['condition']['var'] as $k => $v){
				$Variable = new EmailTemplatesVariables();
				$Variable->event_variable = $v;
				$Variable->is_conditional = '1';
				
				$checkVar = $_POST['variable']['condition']['check'][$k];
				if (!empty($checkVar) && $checkVar != $v){
					$Variable->condition_check = $checkVar;
				}
				
				//$Variable->save();
				$Variables[] = $Variable;
			}
		}
	}
	
	$Descriptions = $Template->EmailTemplatesDescription;
	$languages = sysLanguage::getLanguages();
	foreach($languages as $lInfo){
		if (!empty($_POST['email_subject'][$lInfo['id']])){
			$Descriptions[$lInfo['id']]->email_templates_subject = $_POST['email_subject'][$lInfo['id']];
			$Descriptions[$lInfo['id']]->email_templates_content = $_POST['email_text'][$lInfo['id']];
			$Descriptions[$lInfo['id']]->language_id = $lInfo['id'];
			//$Description->save();
		}
	}
	$Template->save();
	
	EventManager::attachActionResponse(itw_app_link(), 'redirect');
?>