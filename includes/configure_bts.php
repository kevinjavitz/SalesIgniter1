<?php
// BTSv1.5d
unset($javascript,$content,$content_template,$boxLink,$box_id,$box_base_name,$css_page_width);
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

  define('DIR_WS_CONTENT', DIR_WS_TEMPLATES . 'content/');
  define('DIR_WS_JAVASCRIPT', DIR_WS_INCLUDES . 'javascript/');
  define('DIR_WS_BOX_TEMPLATES', DIR_WS_TEMPLATES . 'boxes/');
  define('DIR_WS_MODULE_TEMPLATES', DIR_WS_TEMPLATES . 'modules/');
// define the templatenames used in the project
  define('TEMPLATENAME_BOX', 'box.tpl.php');
  define('TEMPLATENAME_MODULE', 'module.tpl.php');
  define('TEMPLATENAME_MAIN_PAGE', 'main_page.tpl.php');
  define('TEMPLATENAME_POPUP', 'popup.tpl.php');
  define('TEMPLATENAME_STATIC', 'static.tpl.php');

  /* BTSv1.4 */
  define('DIR_WS_TEMPLATES_FALLBACK', 'templates/fallback/'); 
  define('DIR_WS_BOX_TEMPLATES_FALLBACK', DIR_WS_TEMPLATES_FALLBACK . 'boxes/');
  define('DIR_WS_MODULE_TEMPLATES_FALLBACK', DIR_WS_TEMPLATES_FALLBACK . 'modules/');
  define('DIR_WS_CONTENT_FALLBACK', DIR_WS_TEMPLATES_FALLBACK . 'content/');  

 
function bts_select($template_type, $filename = '') {
  // $content_template ??
  global $content_template, $content, $box_base_name;
  
  switch ($template_type) {

    case 'main':
    // default or main_page
      if(is_file(DIR_WS_TEMPLATES . TEMPLATENAME_MAIN_PAGE)) {
	      $path = (DIR_WS_TEMPLATES . TEMPLATENAME_MAIN_PAGE);
      } else {
	      $path = (DIR_WS_TEMPLATES_FALLBACK . TEMPLATENAME_MAIN_PAGE);	  
      }
    break;

    case 'content':
    	// pages or content (middle area)
    	// extra security: added basename()
    	global $appContent;
    	if (isset($appContent) && file_exists(sysConfig::getDirFsCatalog() . 'applications/' . $appContent)){
    		$path = sysConfig::getDirFsCatalog() . 'applications/' . $appContent;
    	}elseif (isset($appContent) && file_exists($appContent)){
    		$path = $appContent;
    	}else{
    		if (isset($content_template)) {
    			if (is_file(DIR_WS_CONTENT . basename($content_template))){
    				$path = (DIR_WS_CONTENT . basename($content_template));
    			}elseif (is_file(DIR_WS_CONTENT_FALLBACK . basename($content_template))){
    				$path = (DIR_WS_CONTENT_FALLBACK . basename($content_template));
    			}
    		}else{
    			if (is_file(DIR_WS_CONTENT . basename($content . '.tpl.php'))){
    				$path = (DIR_WS_CONTENT . basename($content . '.tpl.php'));
    			}else{
    				$path = (DIR_WS_CONTENT_FALLBACK . $content . '.tpl.php');
    			}
    		}
    	}
    break;

    case 'boxes':
    // small sideboxes   
      if(is_file(DIR_WS_BOX_TEMPLATES . $box_base_name . '.tpl.php')) {
        // if exists, load unique box template for this box from templates/boxes/
        $path = (DIR_WS_BOX_TEMPLATES . $box_base_name . '.tpl.php');
      } elseif(is_file(DIR_WS_BOX_TEMPLATES_FALLBACK . $box_base_name . '.tpl.php')) {
        // if exists, load unique box template for this box from templates/boxes/
        $path = (DIR_WS_BOX_TEMPLATES_FALLBACK . $box_base_name . '.tpl.php');
      } elseif(is_file(DIR_WS_BOX_TEMPLATES . TEMPLATENAME_BOX)) {
        // if exists, load unique box template for this box from templates/boxes/
	      $path = (DIR_WS_BOX_TEMPLATES . TEMPLATENAME_BOX);
      } else {
        $path = (DIR_WS_BOX_TEMPLATES_FALLBACK . TEMPLATENAME_BOX);	            
      }   
    break;

    case 'popup':
    // popup main page (images/advanced search)
      if(is_file(DIR_WS_TEMPLATES . TEMPLATENAME_POPUP)) {
	      $path = (DIR_WS_TEMPLATES . TEMPLATENAME_POPUP);
      } else {
	      $path = (DIR_WS_TEMPLATES_FALLBACK . TEMPLATENAME_POPUP);	  
      }
    break;
  
    case 'content_popup':
    // popup pages or content (images/advanced search)
      if(is_file(DIR_WS_CONTENT . basename($content) . '.tpl.php')) {
	      $path = (DIR_WS_CONTENT . basename($content) . '.tpl.php');
      } else {
	      $path = (DIR_WS_CONTENT_FALLBACK . basename($content) . '.tpl.php');	  
      }
    break;

    case 'javascript':
      $path = '';
    break;
    
    case 'stylesheet':
      // $path = DIR_WS_TEMPLATE_FILES . $filename;
      if(is_file(DIR_WS_TEMPLATES . $filename)) {
        $path = DIR_WS_TEMPLATES . $filename;
      } else {
        $path = DIR_WS_TEMPLATES_FALLBACK . $filename;
      }
    break;
        
    case 'column':
      // enables different columns per template function, falls back to (to fallback/ and then to) stock osC columns (inludes/) if no column templates are found
      if(is_file(DIR_WS_TEMPLATES . $filename)) {
        $path = DIR_WS_TEMPLATES . $filename;
      } elseif (is_file(DIR_WS_TEMPLATES_FALLBACK . $filename)) {
        $path = DIR_WS_TEMPLATES_FALLBACK . $filename;
      } else {
        $path = DIR_WS_INCLUDES . $filename;        
      }
    break; 
    
     case 'images':
     // added for loading images directly from your templates directory (w.o. the tep_image() function)
       if (is_file(DIR_WS_TEMPLATES . 'images/' . $filename)) {
	       $path = DIR_WS_TEMPLATES .'images/' . $filename;
       } else {
	       $path = DIR_WS_TEMPLATES_FALLBACK . 'images/' . $filename;
       }    
    break;
    
     case 'buttons':
     // added for loading images directly from your templates directory (w.o. the tep_image() function)
       if (is_file(DIR_WS_TEMPLATES . 'images/buttons/' . Session::get('language') . '/' . $filename)) {
	       $path = DIR_WS_TEMPLATES .'images/buttons/' . Session::get('language') . '/' . $filename;
       } else {
	       $path = DIR_WS_LANGUAGES . Session::get('language') . '/images/buttons/' . $filename;
       }    
     break;
    
     case 'modules':
      if(is_file(DIR_WS_MODULE_TEMPLATES . $filename . '.tpl.php')) {
        // if exists, load unique box template for this box from templates/boxes/
        $path = (DIR_WS_MODULE_TEMPLATES . $filename . '.tpl.php');
      } elseif(is_file(DIR_WS_MODULE_TEMPLATES_FALLBACK . $filename . '.tpl.php')) {
        // if exists, load unique box template for this box from templates/boxes/
        $path = (DIR_WS_MODULE_TEMPLATES_FALLBACK . $filename . '.tpl.php');
      } elseif(is_file(DIR_WS_MODULE_TEMPLATES . TEMPLATENAME_MODULE)) {
        // if exists, load unique box template for this box from templates/boxes/
	      $path = (DIR_WS_MODULE_TEMPLATES . TEMPLATENAME_MODULE);
      } else {
        $path = (DIR_WS_MODULE_TEMPLATES_FALLBACK . TEMPLATENAME_MODULE);	            
      }   
     break;

    case 'common':
    if (is_file(DIR_WS_TEMPLATES_BASE . $filename)) {
	    $path = (DIR_WS_TEMPLATES_BASE . $filename);
    } else {
      return (FALSE);
    }
    break;   
                     
    default:
    exit ('fatal error bts_select()! (no template selected)');
    //return 'template_error!';
    break;
        
    }
    return ($path);
  }
?>
