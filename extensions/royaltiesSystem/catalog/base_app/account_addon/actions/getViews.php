<?php
	$html = 'false';
	if ($_GET['cID'] == $userAccount->getCustomerId()){
		$Qviews = Doctrine_Query::create()
		->from('RoyaltiesSystemViews')
		->where('customers_id = ?', (int) $_GET['cID'])
		->andWhere('streaming_id = ?', (int) $_GET['sID'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qviews){
			$html = '';
			$end = sizeof($Qviews);
			foreach($Qviews as $i => $vInfo){
				$html .= '<tr class="InfoRow view_info_' . (int) $vInfo['streaming_id'] . '">' . 
					'<td class="first">&nbsp;</td>' . 
					'<td>' . tep_date_short($vInfo['date_added']) . '</td>' . 
					'<td>&nbsp;</td>' . 
					'<td>&nbsp;</td>' . 
					'<td style="text-align:right">' . $currencies->format($vInfo['royalty']) . '</td>' . 
					'<td class="last">&nbsp;</td>' . 
				'</tr>';
			}
			$html .= '<tr class="InfoRow last view_info_' . (int) $vInfo['streaming_id'] . '">' . 
				'<td class="first">&nbsp;</td>' . 
				'<td colspan="4"></td>' . 
				'<td class="last">&nbsp;</td>' . 
			'</tr>';
		}
	}
	
	EventManager::attachActionResponse($html, 'html');
?>