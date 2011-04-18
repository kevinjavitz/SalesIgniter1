<?php
	global $events_array, $events_vars;
	
	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
	$EmailTemplatesDescription = Doctrine_Core::getTable('EmailTemplatesDescription');
	$EmailTemplatesVariables = Doctrine_Core::getTable('EmailTemplatesVariables');
	
	$Templates = $EmailTemplates->findAllByLanguagesId(Session::get('language_id'));
	if ($Templates){
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
			$eventId = $tInfo->email_templates_event;
			$templateName = $tInfo->email_templates_name;
			$templateSubject = $tInfo->email_templates_subject;
			$templateContent = file_get_contents(sysConfig::getDirFsCatalog() . 'templates/email/english/' . $templateName);
			
			$tInfo->email_templates_name = $knownEvents[$eventId]['text'];
			$tInfo->email_templates_event = $knownEvents[$eventId]['event'];
			
			$newDescription = $EmailTemplatesDescription->create();
			$newDescription->email_templates_subject = $templateSubject;
			$newDescription->email_templates_content = $templateContent;
			$newDescription->language_id = Session::get('language_id');
			
			$tInfo->EmailTemplatesDescription->add($newDescription);
			
			foreach($knownEvents[$eventId]['vars'] as $k => $vInfo){
				if ($k == 'conditional'){
					foreach($vInfo as $cond => $vars){
						foreach($vars as $var){
							$Variable = $EmailTemplatesVariables->create();
							$Variable->event_variable = $var;
							$Variable->is_conditional = 1;
							$Variable->condition_check = $cond;
							
							$tInfo->EmailTemplatesVariables->add($Variable);
						}
					}
				}else{
					$Variable = $EmailTemplatesVariables->create();
					$Variable->event_variable = $vInfo;
					$Variable->is_conditional = 0;
					$Variable->condition_check = null;
					
					$tInfo->EmailTemplatesVariables->add($Variable);
				}
			}
		}
		$Templates->save();
	}
