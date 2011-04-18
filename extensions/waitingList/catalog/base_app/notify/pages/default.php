<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="ui-widget ui-widget-content ui-corner-all">
	<form name="notify" action="<?php echo itw_app_link('appExt=waitingList&pID=' . (int) $_GET['pID'] . '&purchaseType=' . $_GET['purchaseType'] . '&action=save');?>" method="post">
		<div style="margin:.5em;">Enter your email address below to recieve a notification when this product is in stock.</div>
		<table cellpadding="3" cellspacing="0" border="0" style="margin:.5em;">
			<tr>
				<td>Email Address: </td>
				<td><input type="text" name="email_address" value="<?php echo ($userAccount->isLoggedIn() ? $userAccount->getEmailAddress() : '');?>"></td>
			</tr>
		</table>
		<hr />
		<div style="margin:.5em;text-align:right;"><?php
			echo htmlBase::newElement('button')
			->css(array('float' => 'left'))
			->usePreset('back')
			->setHref(itw_app_link('products_id=' . (int) $_GET['pID'], 'product', 'info'))
			->draw();
			
			echo htmlBase::newElement('button')
			->setType('submit')
			->usePreset('continue')
			->draw();
		?></div>
	</form>
</div>