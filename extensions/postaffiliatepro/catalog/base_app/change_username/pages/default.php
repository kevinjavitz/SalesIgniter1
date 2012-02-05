<?php
	$Qusername = Doctrine_Query::create()
	->from('UsernamesToIds')
	->where('customers_email_address = ?', $userAccount->getEmailAddress())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
ob_start();
?>
<form name="new_event" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=update');?>" method="post" enctype="multipart/form-data">
	<table cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_USERNAME'); ?></td>
			<td class="main"><?php echo tep_draw_input_field('username', $Qusername[0]['username']); ?></td>
		</tr>
	</table>
	<div style="text-align:right"><?php
        $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(null, 'account', 'default', 'SSL'));

		echo $saveButton->draw() . $cancelButton->draw();
		?></div>
</form>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();

$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null,'account','default'))
	->setType('submit')
	->draw();


$pageContent->set('pageTitle', 'Change Username');
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
?>
