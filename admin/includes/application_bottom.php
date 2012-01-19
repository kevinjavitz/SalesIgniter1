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

if (sysConfig::get('STORE_PAGE_PARSE_TIME') == 'true') {
	if (!is_object($logger)) $logger = new logger;
	echo $logger->timer_stop(sysConfig::get('DISPLAY_PAGE_PARSE_TIME'));
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

	$totalTime = 0;
	$totalMemory = 0;
	$eventsList = '<table cellpadding="2" cellspacing="0" border="0">';
	$eventsList .= '<tr>' . 
		'<td style="font-size:inherit;font-family:inherit;"><b><u>Event Name</u></b></td>' . 
		'<td style="font-size:inherit;font-family:inherit;padding:0 1em;"><b><u>Exec Time</u></b></td>' . 
		'<td style="font-size:inherit;font-family:inherit;padding:0 1em;"><b><u>Memory Usage</u></b></td>' . 
	'</tr>';
	
	foreach(EventManager::getProfileEvents() as $event){
		if ($event->getElapsedSecs() > 0){
			$totalTime += $event->getElapsedSecs();
			$totalMemory += $event->getMemoryUsage();
			$eventsList .= '' . 
				'<tr>' . 
					'<td style="font-size:inherit;font-family:inherit;">' . $event->getEventName() . '</td>' .
					'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center">' . number_format($event->getElapsedSecs(), 4) . ' Sec</td>' .
					'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center">' . number_format($event->getMemoryUsage()/1024/1024, 2) . ' MB<td>' . 
				'</tr>' . 
			'';
		}
	}
	$eventsList .= '' . 
		'<tr>' . 
			'<td style="font-size:inherit;font-family:inherit;"></td>' .
			'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center"><b>' . number_format($totalTime, 4) . ' Sec</b></td>' .
			'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center"><b>' . number_format($totalMemory/1024/1024, 2) . ' MB</b><td>' . 
		'</tr>' . 
	'';
	$eventsList .= '</table>';

	$totalTime = 0;
	$totalMemory = 0;
	$profilesList = '<table cellpadding="2" cellspacing="0" border="0">';
	$profilesList .= '<tr>' . 
		'<td style="font-size:inherit;font-family:inherit;"><b><u>Profile Name</u></b></td>' . 
		'<td style="font-size:inherit;font-family:inherit;padding:0 1em;"><b><u>Exec Time</u></b></td>' . 
		'<td style="font-size:inherit;font-family:inherit;padding:0 1em;"><b><u>Memory Usage</u></b></td>' . 
	'</tr>';
	
	foreach(SES_Profiler::getAll() as $profile){
		if ($profile->getElapsedSecs() > 0){
			$totalTime += $profile->getElapsedSecs();
			$totalMemory += $profile->getMemoryUsage();
			$profilesList .= '' . 
				'<tr>' . 
					'<td style="font-size:inherit;font-family:inherit;">' . $profile->getName() . '</td>' .
					'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center">' . number_format($profile->getElapsedSecs(), 4) . ' Sec</td>' .
					'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center">' . number_format($profile->getMemoryUsage()/1024/1024, 2) . ' MB<td>' . 
				'</tr>' . 
			'';
		}
	}
	$profilesList .= '' . 
		'<tr>' . 
			'<td style="font-size:inherit;font-family:inherit;"></td>' .
			'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center"><b>' . number_format($totalTime, 4) . ' Sec</b></td>' .
			'<td style="font-size:inherit;font-family:inherit;padding:0 1em;" align="center"><b>' . number_format($totalMemory/1024/1024, 2) . ' MB</b><td>' . 
		'</tr>' . 
	'';
	$profilesList .= '</table>';
?>	
<br /><br />
<div style="border:1px solid black;background:#fcfcfc;"><table cellpadding="3" cellspacing="0" border="0">
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