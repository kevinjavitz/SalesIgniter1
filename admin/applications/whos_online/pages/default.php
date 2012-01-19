<?php
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<div>
	<div style="float: right;">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="smallText"><?php
					echo $icons['activeCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_CART');
				?></td>
				<td class="smallText"><?php
					echo $icons['inactiveCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_CART');
				?></td>
			</tr>
			<tr>
				<td class="smallText"><?php
					echo $icons['activeNoCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_NOCART');
				?></td>
				<td class="smallText"><?php
					echo $icons['inactiveNoCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_NOCART');
				?></td>
			</tr>
			<tr>
				<td class="smallText"><?php
					echo $icons['botActive'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_BOT');
				?></td>
				<td class="smallText"><?php
					echo $icons['botInactive'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_BOT');
				?></td>
			</tr>
		</table>
	</div>
	<br><span class="smallText" style="color:#909090"><?php echo sysLanguage::get('TEXT_SET_REFRESH_RATE');?>:&nbsp;</span>
	<span style="font-size: 10px; color:#0000CC"><?php
		echo '<a class="menuBoxContentLink refreshLink" data-seconds="null" href="javascript:void(0);"><b> ' . sysLanguage::get('TEXT_NONE') . ' </b></a>';
		foreach ($refresh_time as $key => $value) {
			echo ' &#183; <a class="menuBoxContentLink refreshLink" data-seconds="' . $value . '" href="javascript:void(0);"><b>' . $refresh_display[$key] . '</b></a>';
		}
	?></span>
</div>
<div style="margin-left:auto;margin-right:auto;"><font size="2" face="Arial" color="blue"><?php
	echo sysLanguage::get('TEXT_INFO_LAST_REFRESH') . '&nbsp;<span class="refreshTime"></span><br />';
	echo sysLanguage::get('TEXT_INFO_NEXT_REFRESH') . '&nbsp;<span class="nextRefreshTime">N/A</span>';
?></font></div>
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;" class="gridTableHolder"></div>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<div style="margin:.5em;"><table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td class="smallText" align="left" colspan="2"><?php echo sprintf(sysLanguage::get('TEXT_NUMBER_OF_CUSTOMERS'), '<span class="sessionCount">0</span>');?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="duplicateCount">0</span></td>
		<td class="smallText" align="left"><?php echo sysLanguage::get('TEXT_DUPLICATE_IP'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="botCount">0</span></td>
		<td class="smallText" width="570"><?php echo sysLanguage::get('TEXT_BOTS'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="adminCount">0</span></td>
		<td class="smallText"><?php echo sysLanguage::get('TEXT_ME'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="customerCount">0</span></td>
		<td class="smallText"><?php echo sysLanguage::get('TEXT_REAL_CUSTOMERS'); ?></td>
	</tr>
</table></div>
<div style="margin:.5em;"><?php
	echo '<b>' . sysLanguage::get('TEXT_MY_IP_ADDRESS') . ':</b>&nbsp;' . tep_get_ip_address() . '<br><small>' . sysConfig::get('TEXT_NOT_AVAILABLE') . '</small>';
?></div>
