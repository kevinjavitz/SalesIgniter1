<?php

  $savePath = "images/barcode/"; // Save Path for Barcode
  //include_once("includes/functions/barcode.php" );
  require('includes/classes/data_populate/export.php');
  $dataExport = new dataExport();
  require('includes/classes/barcodePopulate.php');
  $BP = new barcodePopulate();

  $showLogInfo = false;
  if (isset($_GET['download'])){
      $BP->export();
  }


if (isset($_FILES['usrfl'])){
	$upload = new upload('usrfl');
	$upload->set_extensions(array('txt', 'xls', 'csv'));
	$upload->set_destination($BP->tempDir);

	if ($upload->parse() && $upload->save()) {
		$uploaded = true;
	}
}
	if (isset($_GET['split'])){
		$split = $_GET['split'];
	}else{
		$split = -1;
	}

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
