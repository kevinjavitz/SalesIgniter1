<?php
	$Qnewsletter = Doctrine_Query::create()
	->select('newsletters_id, title, content, module')
	->from('Newsletters')
	->where('newsletters_id = ?', (int) $_GET['nID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/admin/modules/newsletters/' . $nInfo->module . '.xml');

    include(sysConfig::getDirFsAdmin() . 'includes/modules/newsletters/' . $Qnewsletter[0]['module'] . '.php');
    $module_name = $Qnewsletter[0]['module'];
    $module = new $module_name($Qnewsletter[0]['title'], $Qnewsletter[0]['content']);
?>
<div>
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td class="main" valign="middle"><?php
				echo tep_image(DIR_WS_IMAGES . 'ani_send_email.gif', IMAGE_ANI_SEND_EMAIL);
			?></td>
			<td class="main" valign="middle"><b><?php
				echo sysLanguage::get('TEXT_PLEASE_WAIT');
			?></b></td>
		</tr>
	</table>
</div>
<?php
  tep_set_time_limit(0);
  flush();
  $module->send($Qnewsletter[0]['newsletters_id']);
?>
<br>
<div>
	<font color="#ff0000"><b><?php echo sysLanguage::get('TEXT_FINISHED_SENDING_EMAILS'); ?></b></font>
	<br><br>
	<?php
		echo htmlBase::newElement('button')
		->usePreset('back')
		->setHref(itw_app_link('page=' . $_GET['page'] . '&nID=' . $_GET['nID'], 'newsletters', 'default'))
		->draw();
	?>
</div>