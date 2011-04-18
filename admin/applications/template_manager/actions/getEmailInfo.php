<?php
	$email_subject = array();
	$email_text = array();
	$standardVars = array();
	$conditionVars = array();
	
	$Qtemplate = Doctrine_Query::create()
	->from('EmailTemplates e')
	->leftJoin('e.EmailTemplatesDescription ed')
	->leftJoin('e.EmailTemplatesVariables ev')
	->where('e.email_templates_id = ?', (int) $_GET['tID'])
	->execute();
	if ($Qtemplate->count() > 0){
		$templateData = $Qtemplate->toArray();

		$languages = sysLanguage::getLanguages();
		foreach($languages as $lInfo){
			$languageId = $lInfo['id'];
			$languageName = $lInfo['name'];
			
			$emailContent = '';
			$emailSubject = '';
			if (isset($templateData[0]['EmailTemplatesDescription'][$languageId])){
				$emailContent = $templateData[0]['EmailTemplatesDescription'][$languageId]['email_templates_content'];
				$emailSubject = $templateData[0]['EmailTemplatesDescription'][$languageId]['email_templates_subject'];
			}		
			$email_text[$languageName][] = $emailContent;
			$email_subject[$languageName][] = $emailSubject;
		}

		foreach($templateData[0]['EmailTemplatesVariables'] as $vInfo){
			if ($vInfo['is_conditional'] == '1'){
				if (!empty($vInfo['condition_check'])){
					$key = $vInfo['condition_check'];
				}else{
					$key = $vInfo['event_variable'];
				}
				$condition = '&lt;!-- if ($' . $key . ')<br />';
				$condition .= '&nbsp;&nbsp;&nbsp;$' . $vInfo['event_variable'] . '<br />';
				$condition .= '--&gt;';

				$conditionVars[] = $condition;
			}else{
				$standardVars[] = '$' . $vInfo['event_variable'];
			}
		}
	}
	
	EventManager::attachActionResponse(array(
		'templateId'      => (int) $_GET['tID'],
		'emailTemplate'   => $Qtemplate[0]['email_templates_name'],
		'emailEvent'      => $Qtemplate[0]['email_templates_event'],
		'emailFile'       => $Qtemplate[0]['email_templates_attach'],
		'emailSubject'    => (!empty($email_subject) ? $email_subject : 'false'),
		'emailText'       => (!empty($email_text) ? $email_text : 'false'),
		'standardVars'    => $standardVars,
		'conditionalVars' => $conditionVars
	), 'json');
?>