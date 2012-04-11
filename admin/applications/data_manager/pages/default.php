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
	    <form enctype="multipart/form-data" action="<?php echo itw_app_link('action=importProducts','data_manager','default');?>" method="post">
        <div align="left">
          <p><b><?php echo sysLanguage::get('HEADING_UPLOAD_FILE');?></b></p>
           <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="100000000">
           <p></p>
           <input name="usrfl" type="file" size="50">
           <input type="submit" name="buttoninsert" value="<?php echo sysLanguage::get('TEXT_BUTTON_INSERT_DB');?>">
           <br>

         </div>
         </form>

        <p><b><?php echo sysLanguage::get('HEADING_DOWNLOAD_FILE');?></b></p>
        <!-- Download file links -  Add your custom fields here -->
		<form name="dl" action="<?php echo itw_app_link('action=exportProducts','data_manager','default');?>" method="post">
			Start number: <input name="start_num" type="text" size="20"><br />
			Number of items: <input name="num_items" type="text" size="20"><br />
	        <input type="submit" value="Download complete tab-delimited .txt file to edit"><br>
		</form>
		
		<p><b><?php echo sysLanguage::get('HEADING_DOWNLOAD_GOOGLE_FILE');?></b></p>
		<form name="dl" action="<?php echo itw_app_link('action=exportProductsGoogle','data_manager','default');?>" method="post">
			<input type="submit" value="Download Google products XML"><br>
		</form>
        </td>
       </tr>
      </table>
     </div>
     <?php if ($showLogInfo === true) echo getLogDivs();?>
    </div>