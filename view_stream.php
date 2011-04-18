<?php
 include('includes/application_top.php');
 
  $content = 'view_stream';

 $canView = false;
 if (Session::exists('viewAllowed') && Session::get('viewAllowed') === true){
     $canView = true;
 }
 
 if (Session::exists('viewAllowed') && Session::get('viewAllowed') == (int)$_GET['pID']){
     $canView = true;
 }elseif ($canView === false){
     Session::remove('viewAllowed');
 }
 
 if (isset($_GET['oID'])){
     $Qcheck = tep_db_query('select
       count(*) as total
   from 
       ' . TABLE_ORDERS . ' o, 
       ' . TABLE_ORDERS_PRODUCTS . ' op
       left join ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' opd on opd.orders_products_id = op.orders_products_id
       left join ' . TABLE_ORDERS_PRODUCTS_STREAM . ' ops on ops.orders_products_id = op.orders_products_id, 
       ' . TABLE_ORDERS_STATUS . ' os 
   where
       os.orders_status_id = o.orders_status and 
       op.orders_id = o.orders_id and 
       (
        opd.orders_id = o.orders_id and 
        opd.download_count < opd.download_maxcount and 
        (opd.download_maxdays = 0 or DATE_ADD(o.date_purchased, INTERVAL opd.download_maxdays DAY) < now()) and
        FIND_IN_SET(o.orders_status, "' . DOWNLOAD_ORDERS_STATUS . '")
       ) or (
        ops.orders_id = o.orders_id and 
        (ops.stream_maxdays = 0 or DATE_ADD(o.date_purchased, INTERVAL ops.stream_maxdays DAY) < now()) and 
        FIND_IN_SET(o.orders_status, "' . STREAM_ORDERS_STATUS . '")
       ) and 
       o.customers_id = "' . $userAccount->getCustomerId() . '" and 
       os.orders_status_id = o.orders_status and 
       o.orders_id = "' . (int)$_GET['oID'] . '" and 
       op.orders_products_id = "' . (int)$_GET['opID'] . '" and 
       os.language_id = "' . Session::get('languages_id') . '"'
     );
     $check = tep_db_fetch_array($Qcheck);
     if ($check['total'] > 0){
         $canView = true;
     }else{
         $canView = false;
     }
 }
 
 if ($canView === true){
     if (isset($_GET['oID'])){
         $QproductID = tep_db_query('select products_id from ' . TABLE_ORDERS_PRODUCTS . ' where orders_products_id = "' . (int)$_GET['opID'] . '"');
         $productID = tep_db_fetch_array($QproductID);
         $pID = $productID['products_id'];
     }else{
         $pID = (int)$_GET['pID'];
     }
     
     if (isset($_GET['fID'])){
         $Qfile = tep_db_query('select * from products_uploads where products_id = "' . $pID . '" and upload_id = "' . (int)$_GET['fID'] . '"');
     }else{
         $Qfile = tep_db_query('select * from products_uploads where products_id = "' . $pID . '" and type="stream"');
     }
     if (tep_db_num_rows($Qfile) == 1){
         $multipleFiles = false;
         $file = tep_db_fetch_array($Qfile);
         
         $ext = substr($file['file_name'], strpos($file['file_name'], '.'));
         $movieName = 'loaded_stream_' . $file['upload_id'] . $ext;
         $getVars = array(
             $pID
         );
         if (isset($_GET['oID'])){
             $getVars[] = (int)$_GET['oID'];
         }
         if (isset($_GET['opID'])){
             $getVars[] = (int)$_GET['opID'];
         }
         $getVars[] = $movieName;
         
         if ($file['type'] == 'download'){
             tep_redirect(tep_href_link('pullStream.php', 'oID=' . (int)$_GET['oID'] . '&opID=' . (int)$_GET['opID'] . '&pID=' . $pID . '&file=' . $movieName));
         }
     }else{
         $multipleFiles = true;
         $canView = true;
     }
 }
 
  include (bts_select('main'));
  
 include('includes/application_bottom.php');
?>