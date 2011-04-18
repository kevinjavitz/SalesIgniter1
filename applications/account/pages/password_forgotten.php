<?php
	ob_start();
?>
<table border="0" width="100%" height="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
	<tr>
		<td class="main" colspan="2"><?php echo sysLanguage::get('TEXT_MAIN'); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo '<b>' . sysLanguage::get('ENTRY_EMAIL_ADDRESS') . '</b> ' . tep_draw_input_field('email_address'); ?></td>
	</tr>
</table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'login', 'SSL'))
	->draw() . 
	htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'password_forgotten',
		'action' => itw_app_link('action=resetPassword', 'account', 'password_forgotten', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
