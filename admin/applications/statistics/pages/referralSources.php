<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_REFERRALS');
?></div>
<br />
<?php
	$Qreferrals = Doctrine_Query::create()
	->select('count(ci.customers_info_source_id) as no_referrals, ci.customers_info_date_account_created, s.sources_name, s.sources_id')
	->from('CustomersInfo ci')
	->leftJoin('ci.Sources s')
	->where('ci.customers_info_source_id != ?', 9999)
	->groupBy('s.sources_id')
	->orderBy('no_referrals DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)

	->setQuery($Qreferrals);
	
	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_REFERRALS')),
			array('text' => sysLanguage::get('TABLE_HEADING_REFERRED'))
		)
	));
	
	$Referrals = &$tableGrid->getResults();
	if ($Referrals){
		foreach($Referrals as $rInfo){
			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => (!empty($rInfo['Sources']['sources_name']) ? $rInfo['Sources']['sources_name'] : sysLanguage::get('TEXT_OTHER'))),
					array('text' => $rInfo['no_referrals'], 'align' => 'right')
				)
			));
		}
	}
	
	$QreferralsOther = Doctrine_Query::create()
	->select('count(ci.customers_info_source_id) as no_referrals, so.customers_id, ci.customers_info_date_account_created, so.sources_other_name')
	->from('CustomersInfo ci')
	->leftJoin('ci.SourcesOther so')
	->where('ci.customers_info_source_id = ?', 9999)
	->groupBy('so.sources_other_name')
	->orderBy('no_referrals DESC');

	$tableGrid2 = htmlBase::newElement('newGrid')
	->usePagination(false)

	->setQuery($QreferralsOther);
	
	$tableGrid2->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_OTHER_REFERRALS')),
			array('text' => sysLanguage::get('TABLE_HEADING_REFERRED'))
		)
	));
	
	$ReferralsOther = &$tableGrid->getResults();
	if ($ReferralsOther){
		foreach($ReferralsOther as $roInfo){
			if (!empty($roInfo['SourcesOther']['sources_name'])){
				$ReferralsOtherInfo = Doctrine_Query::create()
				->select('ci.customers_info_id, ci.customers_info_date_account_created')
				->from('CustomersInfo ci')
				->leftJoin('ci.SourcesOther so')
				->where('so.sources_other_name = ?', $roInfo['SourcesOther']['sources_name'])
				->orderBy('ci.customers_info_id DESC')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			
				$rowcount = 0;
				$col1Html = '<b>' . $roInfo['SourcesOther']['sources_name'] . '</b>' . 
				           '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			           
				foreach($ReferralsOtherInfo as $roiInfo){
					if ($rowcount > 10){
						$col1Html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						$rowcount = 0;
					}
					$rowcount++;
				
					$col1Html .= substr($roiInfo['customers_info_date_account_created'], 0, 9) . '&nbsp;&nbsp;';
				}
				$tableGrid2->addBodyRow(array(
					'columns' => array(
						array('text' => $col1Html),
						array('text' => $roInfo['no_referrals'], 'align' => 'right')
					)
				));
			}
		}
	}
?>
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
   <br />
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid2->draw();?>
   </div>
  </div>
 </div>