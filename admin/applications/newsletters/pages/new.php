<?php
	$Newsletters = Doctrine_Core::getTable('Newsletters');
	if (isset($_GET['nID'])){
		$Newsletter = $Newsletters->find((int) $_GET['nID']);
	}else{
		$Newsletter = $Newsletters->getRecord();
		if (!empty($_POST)){
			$Newsletter->synchronizeWithArray($_POST);
		}
	}

	$file_extension = '.php';
	$directory_array = array();
	if (is_dir(sysConfig::getDirFsAdmin() . 'includes/modules/newsletters/')){
		$dirObj = new DirectoryIterator(sysConfig::getDirFsAdmin() . 'includes/modules/newsletters/');
		foreach($dirObj as $file){
			if ($file->isDot() || $file->isDir()) continue;
			
			$directory_array[] = $file->getBasename('.php');
		}
		sort($directory_array);
	}
	
	for($i=0, $n=sizeof($directory_array); $i<$n; $i++){
		$modules_array[] = array(
			'id' => $directory_array[$i],
			'text' => $directory_array[$i]
		);
	}
?>
<form name="newsletter" action="<?php echo itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] . '&' : '') . 'action=save', 'newsletters', 'new');?>" method="post">
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_NEWSLETTER_MODULE'); ?></td>
			<td class="main"><?php echo tep_draw_pull_down_menu('module', $modules_array, $Newsletter->module); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_NEWSLETTER_TITLE'); ?></td>
			<td class="main"><?php echo tep_draw_input_field('title', $Newsletter->title, '', true); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>
		<tr>
			<td class="main" valign="top"><?php echo sysLanguage::get('TEXT_NEWSLETTER_CONTENT'); ?></td>
			<td class="main"><textarea name="content" style="width:100%" rows="20"><?php echo $Newsletter->content; ?></textarea></td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<tr>
			<td class="main" align="right"><?php
				echo htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . '&nbsp;&nbsp;' . 
				htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] : ''), 'newsletters', 'default'))->draw();
			?></td>
		</tr>
	</table>
</form>