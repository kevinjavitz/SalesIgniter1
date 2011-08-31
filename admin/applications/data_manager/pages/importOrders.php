<div class="pageHeading"><?php
	echo "Data Manager Version 1.0";
	?></div>
<br />
    <div id="epTabs">
	    <ul>
		    <li><a href="#main">Main</a></li>
		    <?php if ($showLogInfo === true) echo getLogTabs();?>
	    </ul>
	    <div id="main">
		    <table width="75%" border="0">
			    <tr>
				    <td width="75%">
<form enctype="multipart/form-data" action="<?php echo itw_app_link('action=importOrders','data_manager','importOrders');?>" method="post">
	<div align="left">
		<p><b><?php echo sysLanguage::get('HEADING_UPLOAD_FILE');?></b></p>
		<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="100000000">
		<p></p>
		<input name="usrfl" type="file" size="50">
		<input type="submit" name="buttoninsert" value="<?php echo sysLanguage::get('TEXT_BUTTON_INSERT_DB');?>">
		<br>

	</div>
</form>
				    </td>
			    </tr>
		    </table>
	    </div>
	    <?php if ($showLogInfo === true) echo getLogDivs();?>
    </div>