<?php
	
	function addGridRow($bannerClass, &$tableGrid, &$infoBoxes){
		global $allGetParams, $editButton, $deleteButton;

        $bannerId = (int)$bannerClass['banners_id'];

        //get number of comments
		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref(itw_app_link($allGetParams . 'bID=' . $bannerId));
			
		$statusIcon = htmlBase::newElement('icon');
		if ($bannerClass['banners_status'] == '1' ){
			$statusIcon->setType('circleCheck')->setTooltip('Click to pause')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=0&bID=' . $bannerId));
		}else if ($bannerClass['banners_status'] == '0' ){
			$statusIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=1&bID=' . $bannerId));
		}else if ($bannerClass['banners_status'] == '2' ){
			$statusIcon->setType('circleCheck')->setTooltip('Banner is running. No action will be taken');
		}else if ($bannerClass['banners_status'] == '3' ){
			$statusIcon->setType('circleCheck')->setTooltip('Banner is expired. No action will be taken');
		}
		
		$rowAttr = array('infobox_id' => $bannerId);

		$tableGrid->addBodyRow(array(
			'rowAttr' => $rowAttr,
			'columns' => array(
				array('text' => $bannerClass['banners_name']),
				array('text' => '<a href="'.itw_app_link($allGetParams . 'bID='.$bannerId, null, 'new_banner').'">'.'[View/Edit/Add]'.'</a>', 'align' => 'center'),
				array('text' => $statusIcon->draw(), 'align' => 'center')
			)
		));
			
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');

		$infoBox->setHeader('<b>' . $bannerClass['banners_name'] . '</b>');
		$editButton->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action','bID')).'bID=' . $bannerId, null, 'new_banner'));
		$deleteButton->attr('banners_id', $bannerId);

		$infoBox->addButton($editButton)->addButton($deleteButton);
		$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_short($bannerClass['banners_date_added']));

		$infoBoxes[$bannerId] = $infoBox->draw();
		unset($infoBox);

	}
	
	$rows = 0;
	$banner_count = 0;

	$Qbanners = Doctrine_Query::create()
	->select('b.*')
	->from('BannerManagerBanners b')
	->orderBy('b.banners_name');

if (isset($_GET['search'])) {
		$search = tep_db_prepare_input($_GET['search']);
		$Qbanners->andWhere('b.banners_name LIKE ?', '%' . $search . '%');
	}

	
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qbanners);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_BANNER')),
            array('text' => "Action"),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
		)
	));
	
	$banners = &$tableGrid->getResults();
	$infoBoxes = array();
	if ($banners){
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete')->addClass('deleteBannerButton');
		
		$allGetParams = tep_get_all_get_params(array('action', 'bID', 'flag'));
		foreach($banners as $banner){
			addGridRow($banner, $tableGrid, $infoBoxes);
		}
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php
   $searchForm = htmlBase::newElement('form')
   ->attr('name', 'search')
   ->attr('action', itw_app_link(null, null, null, 'SSL'))
   ->attr('method', 'get');
   
   $searchField = htmlBase::newElement('input')->setName('search')
   ->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))->setLabelPosition('before');
   if (isset($_GET['search'])){
   	$searchField->setValue($_GET['search']);
   }
   
   $searchForm->append($searchField);
   echo $searchForm->draw();
   ?></td>
  </tr>
 </table>
 
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
    if (!isset($_GET['search'])){
    	$newProdButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_BANNER'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'bID')), null, 'new_banner'));

    	echo $newProdButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php

 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $bID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $bID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>