<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td><table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_SERVER_HOST'); ?></b></td>
				<td class="smallText"><?php echo $system['host'] . ' (' . $system['ip'] . ')'; ?></td>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_DATABASE_HOST'); ?></b></td>
				<td class="smallText"><?php echo $system['db_server'] . ' (' . $system['db_ip'] . ')'; ?></td>
			</tr>
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_SERVER_OS'); ?></b></td>
				<td class="smallText"><?php echo $system['system'] . ' ' . $system['kernel']; ?></td>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_DATABASE'); ?></b></td>
				<td class="smallText"><?php echo $system['db_version']; ?></td>
			</tr>
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_SERVER_DATE'); ?></b></td>
				<td class="smallText"><?php echo $system['date']; ?></td>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_DATABASE_DATE'); ?></b></td>
				<td class="smallText"><?php echo $system['db_date']; ?></td>
			</tr>
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_SERVER_UP_TIME'); ?></b></td>
				<td colspan="3" class="smallText"><?php echo $system['uptime']; ?></td>
			</tr>
			<tr>
				<td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
			</tr>
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_HTTP_SERVER'); ?></b></td>
				<td colspan="3" class="smallText"><?php echo $system['http_server']; ?></td>
			</tr>
			<tr>
				<td class="smallText"><b><?php echo sysLanguage::get('TITLE_PHP_VERSION'); ?></b></td>
				<td colspan="3" class="smallText"><?php echo $system['php'] . ' (' . sysLanguage::get('TITLE_ZEND_VERSION') . ' ' . $system['zend'] . ')'; ?></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td><h1 class="p"><?php echo PROJECT_VERSION;?></h1></td>
	</tr>
</table>