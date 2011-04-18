<?php
	global $events_array, $events_vars;
	if (!is_array($events_array)){
		require(sysConfig::getDirFsCatalog() . 'includes/application_top_events.php');
	}
	
	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
	$EmailTemplatesDescription = Doctrine_Core::getTable('EmailTemplatesDescription');
	$EmailTemplatesVariables = Doctrine_Core::getTable('EmailTemplatesVariables');

    $emailTemplatesTable = $EmailTemplates->getTableName();
	$Templates = $DoctrineConnection->fetchAll('select * from ' . $emailTemplatesTable . ' where languages_id = ?', array(
		Session::get('languages_id')
	));
	if (sizeof($Templates) > 0){
		$knownEvents = array();
		foreach($events_array as $eInfo){
			$knownEvents[$eInfo['id']]['text'] = $eInfo['text'];
		}
		
		$File = file(sysConfig::getDirFsCatalog() . 'includes/application_top_events.php');
		foreach($File as $line){
			if (substr($line, 0, 7) == 'define('){
				$DefineArr = explode(',', $line);
				$EventName = str_replace(array('define(', '\''), '', $DefineArr[0]);
				$EventId = constant($EventName);
				
				$knownEvents[$EventId]['vars'] = $events_vars[$EventId];
				$knownEvents[$EventId]['event'] = substr(strtolower($EventName), 0, -6);
			}
		}
		
		foreach($Templates as $tInfo){
			$templateId = $tInfo['email_templates_id'];
			$eventId = $tInfo['email_templates_event'];
			$templateName = $tInfo['email_templates_name'];
			$templateSubject = $tInfo['email_templates_subject'];
			$languageId = $tInfo['languages_id'];
			$templateContent = file_get_contents(sysConfig::getDirFsCatalog() . 'templates/email/english/' . $templateName);

			Doctrine_Query::create()
			->update('EmailTemplates')
			->set('email_templates_name', '?', $knownEvents[$eventId]['text'])
			->set('email_templates_event', '?', $knownEvents[$eventId]['event'])
			->where('email_templates_id = ?', $templateId)
			->execute();

			$newDescription = $EmailTemplatesDescription->create();
			$newDescription->email_templates_id = $templateId;
			$newDescription->email_templates_subject = $templateSubject;
			$newDescription->email_templates_content = $templateContent;
			$newDescription->language_id = $languageId;
			$newDescription->save();
			
			foreach($knownEvents[$eventId]['vars'] as $k => $vInfo){
				if ($k == 'condition_vars'){
					foreach($vInfo as $cond => $vars){
						foreach($vars as $var){
							$Variable = $EmailTemplatesVariables->create();
							$Variable->email_templates_id = $templateId;
							$Variable->event_variable = $var;
							$Variable->is_conditional = 1;
							$Variable->condition_check = $cond;
							$Variable->save();
						}
					}
				}else{
					$Variable = $EmailTemplatesVariables->create();
					$Variable->email_templates_id = $templateId;
					$Variable->event_variable = $vInfo;
					$Variable->is_conditional = 0;
					$Variable->condition_check = null;
					$Variable->save();
				}
			}
		}
	}
