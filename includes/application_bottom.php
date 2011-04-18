<?php
/*
$Id: application_bottom.php,v 1.14 2003/02/10 22:30:41 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

// close session (store variables)
Session::stop();

if (STORE_PAGE_PARSE_TIME == 'true') {
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' - ' . getenv('REQUEST_URI') . ' (' . $parse_time . 's)' . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);

	if (DISPLAY_PAGE_PARSE_TIME == 'true') {
		echo '<span class="smallText">Parse Time: ' . $parse_time . 's</span>';
	}
}

if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded == true) && ($ini_zlib_output_compression < 1) ) {
	if ( (PHP_VERSION < '4.0.4') && (PHP_VERSION >= '4') ) {
		tep_gzip_output(GZIP_LEVEL);
	}
}

if (isset($_GET['showStats'])){
	$execStart = explode(' ', PAGE_PARSE_START_TIME);
	$execEnd = explode(' ', microtime());
	$executionTime = number_format(($execEnd[1] + $execEnd[0] - ($execStart[1] + $execStart[0])), 3);
	$time = 0;
	$prepareTime = 0;
	foreach ($profiler as $event) {
		$eventName = trim($event->getName());
		if ($eventName == 'query' || $eventName == 'execute' || $eventName == 'fetch' || $eventName == 'fetch all'){
			$time += $event->getElapsedSecs();
		}elseif ($eventName == 'prepare'){
			$prepareTime += $event->getElapsedSecs();
		}
		
		/*$params = $event->getParams();
		
		echo $messageStack->parseTemplate('footerstack', '<table cellpadding="3" cellspacing="0" border="0">
		 <tr>
		  <td class="main" colspan="2"><b><u>Doctrine Connection Profiler</u></b></td>
		 </tr>
		 <tr>
		  <td class="main">Event Name:</td>
		  <td class="main">' . $eventName . '</td>
		 </tr>
		 <tr>
		  <td class="main">Execution Time:</td>
		  <td class="main">' . number_format(sprintf("%f", $event->getElapsedSecs()), 4) . ' seconds</td>
		 </tr>
		 <tr>
		  <td class="main">Query Used:</td>
		  <td class="main">' . $event->getQuery() . '</td>
		 </tr>
		 ' . (!empty($params) ? '
		 <tr>
		  <td class="main">Params Used:</td>
		  <td class="main">' . implode(', ', $params) . '</td>
		 </tr>' : '') . '
		</table>', 'warning') . '<br />';*/
	}

	$eventsList = '<ul style="list-style:none;margin:0;padding:0;">';
	foreach(EventManager::getProfileEvents() as $event){
		if ($event->getElapsedSecs() > 0){
			$eventsList .= '<li>' .
				$event->getEventName() . ' ( ' . number_format($event->getElapsedSecs(), 4) . ' sec )' .
			'</li>';
		}
	}
	$eventsList .= '</ul>';

	$profilesList = '<ul style="list-style:none;margin:0;padding:0;">';
	foreach(SES_Profiler::getAll() as $profile){
		if ($profile->getElapsedSecs() > 0){
			$profilesList .= '<li>' .
				$profile->getName() . ' ( ' . number_format($profile->getElapsedSecs(), 4) . ' sec )' .
			'</li>';
		}
	}
	$profilesList .= '</ul>';
?>	
<br /><br />
<div style="border:1px solid black;background:#fcfcfc;position:absolute;top:.5em;left:.5em;z-index:9999;"><table cellpadding="3" cellspacing="0" border="0" align="center">
 <tr>
  <td class="main"><b>Database Query Prep Time:</b></td>
  <td class="main"><?php echo number_format($prepareTime, 4);?> sec</td>
 </tr>
 <tr>
  <td class="main"><b>Database Query Time:</b></td>
  <td class="main"><?php echo number_format($time, 4);?> sec</td>
 </tr>
 <tr>
  <td class="main"><b>Page Execution Time:</b></td>
  <td class="main"><?php echo number_format($executionTime, 4);?> sec</td>
 </tr>
 <tr>
  <td class="main"><b>Start Memory Usage:</b></td>
  <td class="main"><?php echo number_format(((START_MEMORY_USAGE / 1024)/1024), 4);?> MB</td>
 </tr>
 <tr>
  <td class="main"><b>End Memory Usage:</b></td>
  <td class="main"><?php echo number_format(((memory_get_usage() / 1024)/1024), 4);?> MB</td>
 </tr>
 <tr>
  <td class="main"><b>Peak Memory Usage:</b></td>
  <td class="main"><?php echo number_format(((memory_get_peak_usage() / 1024)/1024), 4);?> MB</td>
 </tr>
 <tr>
  <td class="main" valign="top"><b>Event Manager:</b></td>
  <td class="main"><?php echo $eventsList;?></td>
 </tr>
 <tr>
  <td class="main" valign="top"><b>System Profiles:</b></td>
  <td class="main"><?php echo $profilesList;?></td>
 </tr>
</table></div>
<?php
}
?>