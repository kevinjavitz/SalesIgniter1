<?php
	$Qnewsletter = Doctrine_Query::create()
	->select('title, content, module')
	->from('Newsletters')
	->where('newsletters_id = ?', (int) $_GET['nID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/newsletters/' . $nInfo->module . '.xml');

    include(sysConfig::getDirFsAdmin() . 'includes/modules/newsletters/' . $Qnewsletter[0]['module'] . '.php');
    $module_name = $Qnewsletter[0]['module'];
    $module = new $module_name($Qnewsletter[0]['title'], $Qnewsletter[0]['content']);
?>
<div><?php echo $module->confirm(); ?></div>