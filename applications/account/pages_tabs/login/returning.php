<p><?php
  echo sysLanguage::get('TEXT_RETURNING_CUSTOMER');
	?></p>
<table border="0" cellspacing="0" cellpadding="0" width="95%">
	<tr>
		<td>
			<table cellpadding="3" cellspacing="0" border="0">
				<tr>
					<td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS');?></td>
					<td><?php
						echo htmlBase::newElement('input')
							//->setType('email')
							->setName('email_address')
							->attr('required', '')
							->css('width', '200px')
							->draw();
						?></td>
				</tr>
				<tr>
					<td class="main"><?php echo sysLanguage::get('ENTRY_PASSWORD');?></td>
					<td><?php
						echo htmlBase::newElement('input')
							->setType('password')
							->setName('password')
							->attr('required', '')
							->css('width', '200px')
							->draw();
						?></td>
				</tr>
			</table>
		</td>
		<td align="right">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" class="smalltext"><?php
						$loginButton = htmlBase::newElement('button')
						->setText(sysLanguage::get('IMAGE_BUTTON_LOGIN'))
						->setType('submit')
						->setIcon('circleTriangleEast');

						echo $loginButton->draw();
						?>
						<br><br><a href="<?php echo itw_app_link(null, 'account', 'password_forgotten', 'SSL');?>"><?php echo sysLanguage::get('TEXT_PASSWORD_FORGOTTEN');?></a><br><br>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>