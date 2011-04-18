<script>
$(document).ready(function (){
	var newHeight = $('img', $('.popupWindow')).height() +45;
	var newWidth = $('img', $('.popupWindow')).width() +45;

	if (newHeight > 100){
		$('.popupWindow').dialog('option', 'height', newHeight);
	}

	if (newWidth > 100){
		$('.popupWindow').dialog('option', 'width', newWidth);
	}
});
</script>
<?php
	require(DIR_WS_CLASSES . 'template.php');
	$thisTemplate = Session::get('tplDir');
  	$thisApp = $App->getAppName();
    $thisAppPage = $App->getAppPage() .'.php';
	$thisDir = sysConfig::getDirFsCatalog() . 'templates/'.$thisTemplate;
	$thisFile = basename($_SERVER['PHP_SELF']);
	$thisExtension = (isset($_GET['appExt'])?$_GET['appExt']:'');
    if (isset($_GET['cPath']) && $thisApp == 'index'){
		$thisAppPage = 'index.php';
	}
	$Template = new Template('layout.tpl', $thisDir);
	$pageContent = new Template('pageContent.tpl', $thisDir);
	
	$Template->setVars(array(
		'pageStackOutput' => ($messageStack->size('pageStack') > 0 ? $messageStack->output('pageStack') : '')
	))
	->setReference('pageContent', &$pageContent);
	/*$Qpages = Doctrine_Query::create()
               ->from('TemplatePages')
               ->where('extension=?', $thisExtension)
               ->andWhere('application=?', $thisApp)
               ->andWhere('page=?',$thisAppPage)
               ->fetchOne();
    $layoutArr = explode(',', $Qpages->layout_id);

    $QtemplateLayouts = Doctrine_Query::create()
                        ->from('TemplateLayouts')
                        ->whereIn('layout_id', $layoutArr)
                        ->andWhere('template_name=?',$thisTemplate)
                        ->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
	$layout_id = $QtemplateLayouts[0]['layout_id'];
 	$theBox = 'pageContent';
    EventManager::notify('PageCreateWidgets', &$Template, $layout_id, &$pageContent, &$theBox);
	EventManager::notify('PageContentLoad', &$pageContent);*/
    $theBox = 'pageContent';
     if (file_exists(dirname(__FILE__) . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php')){
		ob_start();
		require(dirname(__FILE__) . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php');
		$contents = ob_get_contents();
		ob_end_clean();
		$Template->set($theBox, $contents);
    }elseif (file_exists(sysConfig::getDirFsCatalog() . '/applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php')){
		ob_start();
		require(sysConfig::getDirFsCatalog() . '/applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php');
		$contents = ob_get_contents();
		ob_end_clean();
		$Template->set($theBox, $contents);
    }elseif (isset($appContent) && file_exists($appContent)){
		ob_start();
    	require($appContent);
		$contents = ob_get_contents();
		ob_end_clean();
		$Template->set($theBox, $contents);
    }elseif (file_exists(bts_select('content'))){
		ob_start();
		require(bts_select('content'));
		$contents = ob_get_contents();
		ob_end_clean();
		$Template->set($theBox, $contents);
	}

	echo $Template->parse();
?>