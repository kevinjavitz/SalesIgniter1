<?php
 include('includes/application_top.php');
 include('includes/functions/barcode.php');
 
 $barcodeID = (int)$_GET['code'];
 $Qbarcode = tep_db_query('select barcode from ' . TABLE_PRODUCTS_INVENTORY_BARCODES . ' where barcode_id = "' . $barcodeID . '"');
 $barcode = tep_db_fetch_array($Qbarcode);
 header('Content-type: image/png');
 //header('Content-Transfer-Encoding: binary');
 makeBarcode($barcode['barcode'], true);
 itwExit();
?>