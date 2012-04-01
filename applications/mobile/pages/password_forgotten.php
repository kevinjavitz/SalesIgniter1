<?php
ob_start();
?>
<div id="passwordForgottenDialog" data-role="page" style="background: url(/et_video/templates/moviestore/images/body_bg.png)">
	<div data-role="header">
		<h1><?php echo sysLanguage::get('HEADING_TITLE_PASSWORD_FORGOTTEN');?></h1>
	</div>
	<div data-role="content">
		<div><?php echo sysLanguage::get('TEXT_MAIN_PASSWORD_FORGOTTEN');?></div>
		<br>

		<div><b><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS');?></b></div>
		<div><?php
			echo htmlBase::newElement('input')
				->setName('email_address')
				->draw();
			?></div>
	</div>
	<div data-role="footer">
		<div class="ui-bar" style="text-align:center;"><?php
			$loginButton = htmlBase::newElement('button')
				->setId('passwordForgottenDialogSubmit')
				->usePreset('continue');

			echo $loginButton->draw();
			?></div>
	</div>
</div>
<?php
$pageContents = ob_get_contents();
ob_end_clean();

$pageContent->set('pageForm', array(
	'name'   => 'password_forgotten',
	'action' => itw_app_link('action=resetPassword', 'account', 'password_forgotten', 'SSL'),
	'method' => 'post'
));

$pageContent->set('pageContent', $pageContents);
