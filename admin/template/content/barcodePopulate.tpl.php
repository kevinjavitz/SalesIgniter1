<?php
    echo 'Inventory Populate 2.0 ';
    ?><p class="smallText"><?php
if (isset($_FILES['usrfl'])){
	if (!class_exists('upload')){
		require(sysConfig::getDirFsAdmin().'includes/classes/upload.php');
	}

	$upload = new upload('usrfl');
	$upload->set_extensions(array('txt', 'xls', 'csv'));
	$upload->set_destination($BP->tempDir);

	if ($upload->parse() && $upload->save()) {
		$uploaded = true;
	}
}
	$split = $_GET['split'];
	
  if ($uploaded === true){
      if ($split == 0) {
          $BP->importFile($upload->filename);
          $showLogInfo = true;
      }elseif ($split == 1) {
          //*******************************
          //*******************************
          // UPLOAD AND SPLIT FILE
          //*******************************
          //*******************************
          // move the file to where we can work with it
          $file = tep_get_uploaded_file('usrfl');
          
          if (is_uploaded_file($file['tmp_name'])) {
              tep_copy_uploaded_file($file, $BP->tempDir);
          }
          
          $BP->splitFile($usrfl_name);
          $showLogInfo = true;
      }
  }
?>
    </p>

    <script>
     function showHideDivs(selector){
         if ($(selector + ':visible').size() > 0){
             $(selector).hide();
         }else{
             $(selector).show();
         }
     };
     
     $(document).ready(function (){
         $('#epTabs').tabs();
     });
    </script>
    <div id="epTabs">
     <ul>
      <li><a href="#main">Main</a></li>
      <?php if ($showLogInfo === true) echo getLogTabs();?>
     </ul>
     <div id="main">
      <table width="75%" border="0">
       <tr>
        <td width="75%"><form enctype="multipart/form-data" action="barcodePopulate.php?split=0" method="post">
			<div align="left">
			  <p><b><?php echo sysLanguage::get('HEADING_UPLOAD_FILE');?></b></p>
			   <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="100000000">
			   <p></p>
			   <input name="usrfl" type="file" size="50">
			   <input type="submit" name="buttoninsert" value="<?php echo sysLanguage::get('TEXT_BUTTON_INSERT_DB');?>">
			   <br>
			 </div>
        </form>
        <form enctype="multipart/form-data" action="barcodePopulate.php?split=1" method="post">
			 <div align="left">
			  <p><b><?php echo sysLanguage::get('HEADING_SPLIT_FILE');?></b></p>
			   <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000000">
			   <p></p>
			   <input name="usrfl" type="file" size="50">
			   <input type="submit" name="buttonsplit" value="<?php echo sysLanguage::get('TEXT_BUTTON_SPLIT_FILE');?>">
			   <br>
			 </div>
        </form>
          <p><b><?php echo sysLanguage::get('HEADING_FROOGLE');?></b></p>
	         <form name="dl" action="barcodePopulate.php?download=stream&dltype=full" method="post">
				 Start number: <input name="start_num" type="text" size="20"><br />
				 Number of items: <input name="num_items" type="text" size="20"><br />
				 <input type="submit" value="Download complete tab-delimited .txt file to edit"><br>
			</form>
        </td>
       </tr>
      </table>
     </div>
     <?php if ($showLogInfo === true) echo getLogDivs();?>
    </div>