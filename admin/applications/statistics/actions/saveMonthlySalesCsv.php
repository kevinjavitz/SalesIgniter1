<?php
	if (isset($_POST['saveas'])){
		$savename= $_POST['saveas'] . ".csv";
	}else{
		$savename='unknown.csv';
	}
	
	$csv_string = '';
	if (isset($_POST['csv'])) $csv_string = $_POST['csv'];
	
	if (strlen($csv_string) > 0){
		header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
		header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: Application/octet-stream");
		header("Content-Disposition: attachment; filename=$savename");
		$html = $csv_string;
	}else{
		$html = 'CSV string empty';
	}
	
	EventManager::attachActionResponse($html, 'html');
?>