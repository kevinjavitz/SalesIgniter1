<?php
include('includes/application_top.php');
$canView = false;
$name = substr($_GET['file'], 0, strpos($_GET['file'], '.'));
if($name == 'preview' && !isset($_GET['opID'])){
	$canView = true;
}
/*
if($name != 'preview' && !stristr($name, 'loaded_stream_')){
	$canView = false;
}
*/
$pID = (isset($_GET['pID']) ? $_GET['pID'] : (isset($_GET['pid']) ? $_GET['pid'] : false));
if($pID === false || !is_numeric($pID)){
	$canView = false;
}
if($userAccount->isLoggedIn() === true && isset($_GET['opID']) && isset($_GET['oID'])){
	$canView = true;
}
if($canView === true){
	if(isset($_GET['oID']) && $userAccount->isLoggedIn() === true){
		$QOrderPurchaseType = tep_db_query('select purchase_type from ' . TABLE_ORDERS_PRODUCTS . ' where orders_products_id = "' . (int) $_GET['opID'] . '"');

		if(tep_db_num_rows($QOrderPurchaseType)){
			$orderPurchaseType = tep_db_fetch_array($QOrderPurchaseType);
			switch($orderPurchaseType['purchase_type']){
				case 'stream':
					$Stream = false;
					EventManager::notify('PullStreamAfterUpdate', &$Stream, (int) $_GET['oID'], (int) $_GET['opID']);
					$file = array('file_name' => $Stream['file_name'],
						 'type' => 'stream');
					break;
				case 'download':
					$Download = false;
					$fileName = $Stream['file_name'];
					EventManager::notify('PullDownloadAfterUpdate', &$Download, (int) $_GET['oID'], (int) $_GET['opID']);
					$file =
						array
						('file_name' => $Stream['file_name'],
						 'type' => 'download');
					break;
			}
		}
	} elseif($name == 'preview'){
		$Qfile = tep_db_query('select movie_preview as file_name, "stream" as type from ' . TABLE_PRODUCTS . ' where products_id = "' . (int) $pID . '"');
		if(tep_db_num_rows($Qfile)){
			$file = tep_db_fetch_array($Qfile);
		}
	}
	if($file['type'] == 'download'){
		if(stristr($file['file_name'], '.gif')){
			header('Content-type: image/gif');
		} elseif(stristr($file['file_name'], '.png')){
			header('Content-type: image/png');
		} elseif(stristr($file['file_name'], '.jpg')){
			header('Content-type: image/jpg');
		} elseif(stristr($file['file_name'], '.mpg')){
			header('Content-type: video/mpg');
		} elseif(stristr($file['file_name'], '.flv')){
			header("Content-Type: video/flv");
		}
		header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
	}
	//readfile('streamer/movies/' . $file['file_name']);

	readfile('streamer/' . $file['file_name']);
	exit;
} else{
	echo 'File Not Found';
}
include('includes/application_bottom.php');
?>