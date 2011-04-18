<?php
	$appContent = $App->getAppContentFile();

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
 	$Qcheck = Doctrine_Query::create()
 	->select('count(*) as total')
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsDownload opd')
	->leftJoin('op.OrdersProductsStream ops')
	->leftJoin('o.OrdersStatus os')
	->leftJoin('os.OrdersStatusDescription osd')
	->where('o.customers_id = ?', $userAccount->getCustomerId())
	->andWhere('o.orders_id = ?', (int) $_GET['oID'])
	->andWhere('op.orders_products_id = ?', (int) $_GET['opID'])
	->andWhere('osd.language_id = ?', Session::get('languages_id'))
	->andWhere('(
		opd.orders_products_id > 0 AND 
		opd.download_count < opd.download_maxcount AND 
		(
			opd.download_maxdays = 0 
				OR 
			DATE_ADD(o.date_purchased, INTERVAL download_maxdays DAY) < now() AND 
			FIND_IN_SET(o.orders_status, "' . sysConfig::get('DOWNLOAD_ORDERS_STATUS') . '")
		) AND TRUE
	) OR (
		ops.orders_products_id > 0 AND 
		(
			ops.stream_maxdays = 0 
				OR 
			DATE_ADD(o.date_purchased, INTERVAL stream_maxdays DAY) < now() AND 
			FIND_IN_SET(o.orders_status, "' . sysConfig::get('STREAM_ORDERS_STATUS') . '")
		) AND TRUE
	) AND TRUE')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
 	
     if ($Qcheck[0]['total'] > 0){
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
             tep_redirect(itw_app_link('oID=' . (int)$_GET['oID'] . '&opID=' . (int)$_GET['opID'] . '&pID=' . $pID . '&file=' . $movieName, 'viewStream', 'pull'));
         }
     }else{
         $multipleFiles = true;
         $canView = true;
     }
 }

	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE'), itw_app_link(null, 'viewStream', 'default'));
?>