<?php
/*
$Id: application_bottom.php,v 1.8 2002/03/15 02:40:38 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License
*/

// close session (store variables)
Session::stop();

if (STORE_PAGE_PARSE_TIME == 'true') {
	if (!is_object($logger)) $logger = new logger;
	echo $logger->timer_stop(DISPLAY_PAGE_PARSE_TIME);
}

if (isset($_GET['showStats'])){
	$execStart = explode(' ', PAGE_PARSE_START_TIME);
	$execEnd = explode(' ', microtime());
	$executionTime = number_format(($execEnd[1] + $execEnd[0] - ($execStart[1] + $execStart[0])), 3);
	$time = 0;
	foreach ($profiler as $event) {
		$eventName = trim($event->getName());
		$time += $event->getElapsedSecs();
		if ($eventName != 'query' && $eventName != 'execute' && $eventName != 'fetch') continue;
		
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
?>	
<br /><br />
<div style="border:1px solid black;background:#fcfcfc;position:absolute;top:.5em;left:.5em;"><table cellpadding="3" cellspacing="0" border="0" align="center">
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
</table></div>
<?php
}
?>