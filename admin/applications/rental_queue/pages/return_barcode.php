<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_RETURN_BARCODE');
?></div>
<form name="return_barcode" action="<?php echo itw_app_link('action=returnBarcodes', 'rental_queue', 'return_barcode');?>" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr class="dataTableHeadingRow">
				<td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE'); ?></td>
				<td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_COMMENTS'); ?></td>
				<td valign="top" class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_BROKEN'); ?></td>
			</tr>
<?php
	for($i=0;$i<10;$i++){
?>
			<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
				<td class="smallText" align="center"><?php echo tep_draw_input_field('barcode[]'); ?></td>
				<td class="smallText" align="center"><?php echo tep_draw_textarea_field('comment[]',true,30,2); ?></td>
				<td class="smallText" align="center"><?php echo tep_draw_checkbox_field('broken[]','1') ?></td>
			</tr>
<?php
	}
?>
		</table></td>
	</tr>
	<tr>
		<td align="center" colspan="4" height="35" valign="middle"><?php
			echo htmlBase::newElement('button')
			->setType('submit')
			->setText(sysLanguage::get('TEXT_BUTTON_RETURN'))
			->draw();
		?></td>
	</tr>
</table>
</form>