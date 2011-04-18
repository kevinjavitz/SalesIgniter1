<?php
	$RequestObj = new CurlRequest('https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getVersions.php');
	$ResponseObj = $RequestObj->execute();
	if ($ResponseObj->hasError()){
		echo 'cURL Error: ' . $ResponseObj->getError() . '<br>';
	}else{
		$ResponseText = $ResponseObj->getResponse();
	}
?>
<div class="pageHeading"><?php
	echo 'Upgrade System';
?></div>
<br>
<table cellpadding="3" cellspacing="0" border="0" class="formTable">
	<tr>
		<td>Current Version: </td>
		<td><?php echo sysConfig::get('SYSTEM_VERSION');?></td>
	</tr>
	<tr>
		<td>Upgrade To: </td>
		<td><select name="version"><?php echo $ResponseText;?></select></td>
	</tr>
	<tr>
		<td><input type="checkbox" class="noValidation"></td>
		<td>Do not verify files ( Do not use this if you want to choose files to ignore )</td>
	</tr>
	<tr>
		<td colspan="2"><?php
			if (PHP_VERSION >= 5.3){
				echo htmlBase::newElement('button')->addClass('continueButton')->usePreset('continue')->draw();
			}else{
				echo 'PHP Version Is Too Low, 5.3+ Required For Upgrade. You Have ' . PHP_VERSION;
			}
		 ?></td>
	</tr>
</table>
<div class="upgradeInfo" style="display:none">
	<div id="globalProgressBar"></div>
	<br>
	<div id="globalProgressMessage">This process will take a few minutes and the table below will be populated with file diffs when complete.</div>
	<br>
	<div id="processProgressBar"></div>
	<br>
	<div id="processProgressMessage"></div>
	<br>
	<?php echo htmlBase::newElement('button')->addClass('processFiles')->usePreset('continue')->setText('Process Checked')->draw();?>
	<table class="ui-widget ui-widget-content fileDiffs" cellpadding="2" cellspacing="0" width="100%">
		<thead>
			<tr class="ui-widget-header">
				<th colspan="4" class="rootDir"></th>
			</tr>
			<tr class="ui-widget-header ui-state-hover">
				<th>Action</th>
				<th align="left">File</th>
				<th align="left">Compare Message</th>
				<th></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>