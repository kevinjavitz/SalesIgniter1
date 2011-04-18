<?php
 $canView = false;
 if ($userAccount->isLoggedIn() === true){
     $canView = true;
 }
 
 $name = substr($_GET['file'], 0, strpos($_GET['file'], '.'));
 if ($name == 'preview'){
     $canView = true;
 }
 
 if ($name != 'preview' && !stristr($name, 'loaded_stream_')){
     $canView = false;
 }
 
 $pID = (isset($_GET['pID']) ? $_GET['pID'] : (isset($_GET['pid']) ? $_GET['pid'] : false));
 if ($pID === false || !is_numeric($pID)){
     $canView = false;
 }
 
 if ($canView === true){
     if ($name == 'preview'){
         $Qfile = tep_db_query('select movie_preview as file_name from ' . TABLE_PRODUCTS . ' where products_id = "' . (int)$pID . '"');
     }else{
         $uploadID = str_replace('loaded_stream_', '', $name);
         $Qfile = tep_db_query('select * from products_uploads where products_id = "' . (int)$pID . '" and upload_id = "' . $uploadID . '"');
     }
     $file = tep_db_fetch_array($Qfile);
     if ($userAccount->isLoggedIn() === true){
         if (isset($uploadID) && !isset($_GET['oID'])){
             tep_db_query('insert into customers_streaming_views (customers_id, products_id, streaming_id, date_added) values ("' . (int)$userAccount->getcustomerId() . '", "' . (int)$pID . '", "' . (int)$uploadID . '", now())');
         }elseif (isset($_GET['oID'])){
             if ($file['type'] == 'stream'){
                 tep_db_query('update ' . TABLE_ORDERS_PRODUCTS_STREAM . ' set stream_count = stream_count + 1 where orders_id = "' . (int)$_GET['oID'] . '" and orders_products_id = "' . (int)$_GET['opID'] . '" and orders_products_filename = "' . $file['file_name'] . '"');

                 EventManager::notify('PullStreamAfterUpdate', &$uploadID, &$pID, &$file);
             }elseif ($file['type'] == 'download'){
                 $Qcheck = tep_db_query('select download_count, download_maxcount from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . (int)$_GET['oID'] . '" and orders_products_id = "' . (int)$_GET['opID'] . '" and orders_products_filename = "' . $file['file_name'] . '"');
                 $check = tep_db_fetch_array($Qcheck);
                 if ($check['download_count'] >= $check['download_maxcount']){
                     die('Exceeded number of downloads');
                     exit;
                 }
                 tep_db_query('update ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' set download_count = download_count + 1 where orders_id = "' . (int)$_GET['oID'] . '" and orders_products_id = "' . (int)$_GET['opID'] . '" and orders_products_filename = "' . $file['file_name'] . '"');

                 EventManager::notify('PullDownloadAfterUpdate', &$uploadID, &$pID, &$file);
             }
         }
     }
     
     if ($file['type'] == 'download'){
         if (stristr($file['file_name'], '.gif')){
             header('Content-type: image/gif');
         }elseif (stristr($file['file_name'], '.png')){
             header('Content-type: image/png');
         }elseif (stristr($file['file_name'], '.jpg')){
             header('Content-type: image/jpg');
         }elseif (stristr($file['file_name'], '.mpg')){
             header('Content-type: video/mpg');
         }elseif (stristr($file['file_name'], '.flv')){
             header("Content-Type: video/flv");
         }
         header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
     }
     readfile('streamer/movies/' . $file['file_name']);
     exit;
 }else{
     echo 'File Not Found';
 }
?>