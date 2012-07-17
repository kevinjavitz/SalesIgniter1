<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<?php
switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = sysLanguage::get('TEXT_ALL_CUSTOMERS');
        break;
      case '**D':
        $mail_sent_to = sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS');
        break;
      default:
        $mail_sent_to = $_POST['customers_email_address'];
        break;
    }
?>
<form name="mail" action="<?php echo itw_app_link('action=send', 'mail', 'preview');?>" method="post">
<table border="0" width="100%" cellpadding="0" cellspacing="2">
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="smallText"><b><?php echo sysLanguage::get('TEXT_CUSTOMER'); ?></b><br><?php echo $mail_sent_to; ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="smallText"><b><?php echo sysLanguage::get('TEXT_FROM'); ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="smallText"><b><?php echo sysLanguage::get('TEXT_SUBJECT'); ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="smallText"><b><?php echo sysLanguage::get('TEXT_MESSAGE'); ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td><?php
			/* Re-Post all POST'ed variables */
			reset($_POST);
			while (list($key, $value) = each($_POST)) {
				if (!is_array($_POST[$key])) {
					echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
				}
			}
		?><table border="0" width="100%" cellpadding="0" cellspacing="2">
			<tr>
				<td align="right"><?php
					echo htmlBase::newElement('button')
					->usePreset('cancel')
					->setHref(itw_app_link(null, 'mail', 'default'))
					->draw() . 
					' ' . 
					htmlBase::newElement('button')
					->setText(sysLanguage::get('IMAGE_SEND_EMAIL'))
					->setType('submit')
					->draw();
				?></td>
			</tr>
		</table></td>
	</tr>
</table>
</form>