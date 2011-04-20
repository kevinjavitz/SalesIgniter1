<?php
// BTSv1.5d
$bts_debug = FALSE;
// if(!(defined('DIR_WS_TEMPLATES_BASE'))) 
define ('DIR_WS_TEMPLATES_BASE','templates/');

if ((TEMPLATE_SWITCHING_ALLOWED == 'true') && (isset($_GET['tplDir'])) && is_dir(DIR_WS_TEMPLATES_BASE . basename($_GET['tplDir'])) ) {
    Session::set('tplDir', basename($_GET['tplDir']));
  } else {
	if ((Session::exists('tplDir') === true)&&(TEMPLATE_SWITCHING_ALLOWED == 'true') && is_dir(DIR_WS_TEMPLATES_BASE . basename(Session::get('tplDir')))){
	  Session::set('tplDir', basename(Session::get('tplDir')));	
    }else{ 	  
      Session::set('tplDir', DIR_WS_TEMPLATES_DEFAULT);
    }
  }
  
EventManager::notify('SetTemplateName');

 $tplDir = basename(Session::get('tplDir')); 
if ((preg_match('/^[[:alnum:]|_|-]+$/', $tplDir)) && (is_dir (DIR_WS_TEMPLATES_BASE . $tplDir))){
  // 'Input Validated' only allow alfanumeric characters and underscores in template name
  define('DIR_WS_TEMPLATES', DIR_WS_TEMPLATES_BASE . $tplDir . '/' ); 
} else {
  echo strip_tags($tplDir) . '<br>';
  exit('Illegal template directory!');
}

?>
