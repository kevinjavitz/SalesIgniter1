<?php
ob_start();
?>
<div id="loginDialog" data-role="page" style="background: url(/et_video/templates/moviestore/images/body_bg.png)">
	<div data-role="header">
		<h1>Login</h1>
	</div>
	<div data-role="content">
		<div><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS');?></div>
		<div><?php
			$emailAddress = htmlBase::newElement('input')
			//->setType('email')
				->setName('email_address')
				->attr('required', '');
			echo $emailAddress->draw();
			?></div>
		<div><?php echo sysLanguage::get('ENTRY_PASSWORD');?></div>
		<div><?php
			$password = htmlBase::newElement('input')
				->setType('password')
				->setName('password')
				->attr('required', '');
			echo $password->draw();
			?></div>
	</div>
	<div data-role="footer">
		<div class="ui-bar" style="text-align:center;"><?php
			$loginButton = htmlBase::newElement('button')
				->setId('loginDialogSubmit')
				->setText(sysLanguage::get('IMAGE_BUTTON_LOGIN'))
				->setIcon('circleTriangleEast');

			echo $loginButton->draw();
			?></div>
	</div>
</div>
<?php
$Contents = ob_get_contents();
ob_end_clean();

$pageContent->set('pageTitle', 'Login');
$pageContent->set('pageContent', $Contents);

?>