<?php
	$Qcoupons = Doctrine_Query::create()
	->from('Coupons c')
	->leftJoin('c.CouponsDescription cd')
	->where('c.coupon_type != ?', 'G')
	->andWhere('cd.language_id = ?', Session::get('languages_id'))
	->orderBy('cd.coupon_name');
	
	if (isset($_GET['status']) && $_GET['status'] != '*'){
		$Qcoupons->andWhere('c.coupon_active = ?', $_GET['status']);
	}

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qcoupons);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('New')->addClass('insertButton'),
		htmlBase::newElement('button')->setText('Email')->addClass('emailButton')->disable(),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Report')->addClass('reportButton')->disable()
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_COUPON_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_COUPON_AMOUNT')),
			array('text' => sysLanguage::get('TABLE_HEADING_COUPON_CODE')),
			array('text' => sysLanguage::get('TABLE_HEADING_COUPON_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$Coupons = &$tableGrid->getResults();
	if ($Coupons){
		foreach($Coupons as $cInfo){
			$couponActive = $cInfo['coupon_active'];
			$couponId = $cInfo['coupon_id'];
			$couponCode = $cInfo['coupon_code'];
			$couponAmount = $cInfo['coupon_amount'];
			$couponMinOrder = $cInfo['coupon_minimum_order'];
			$couponType = $cInfo['coupon_type'];
			$couponStartDate = $cInfo['coupon_start_date'];
			$couponExpireDate = $cInfo['coupon_expire_date'];
			$usesPerUser = $cInfo['uses_per_user'];
			$usesPerCoupon = $cInfo['uses_per_coupon'];
			//$restrictToProducts = $cInfo['restrict_to_products'];
			//$restrictToCategories = $cInfo['restrict_to_categories'];
			$restrictToPurchaseType = $cInfo['restrict_to_purchase_type'];
			$dateCreated = $cInfo['date_created'];
			$dateModified = $cInfo['date_modified'];
			
			$couponName = $cInfo['CouponsDescription'][0]['coupon_name'];
			
			if ($couponType == 'P'){
				if ($couponAmount == round($couponAmount)){
					$cAmount = number_format($couponAmount);
				}else{
					$cAmount = number_format($couponAmount, 2);
				}
				$cAmount .= '%';
			}elseif ($couponType == 'S'){
				$cAmount = sysLanguage::get('TEXT_FREE_SHIPPING');
			}else{
				$cAmount = $currencies->format($couponAmount);
			}

			$arrowIcon = htmlBase::newElement('icon')->setType('info');

			$statusIcon = htmlBase::newElement('icon');
			if ($couponActive == 'Y'){
				$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
				->setHref(itw_app_link('action=setflag&flag=N&cID=' . $couponId));
			}else{
				$statusIcon->setType('circleClose')->setTooltip('Click to enable')
				->setHref(itw_app_link('action=setflag&flag=Y&cID=' . $couponId));
			}

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-coupon_id' => $couponId
				),
				'columns' => array(
					array('text' => $couponName),
					array('align' => 'center', 'text' => $cAmount),
					array('align' => 'center', 'text' => $couponCode),
					array('align' => 'center', 'text' => $statusIcon->draw()),
					array('align' => 'center', 'text' => $arrowIcon->draw())
				)
			));

			/*
			$prodDetails = sysLanguage::get('NONE');
			if ($restrictToProducts != ''){
				$prod_details = '<A HREF="listproducts.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
			}
			*/
			
			/*
			$catDetails = sysLanguage::get('NONE');
			if ($restrictToCategories != ''){
				$cat_details = '<A HREF="listcategories.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
			}
			*/
			
			$purchaseTypeDetails = sysLanguage::get('NONE');
			if ($restrictToPurchaseType != ''){
				$purchaseTypeDetails = array();
				foreach(explode(',', $restrictToPurchaseType) as $typeName){
					$purchaseTypeDetails[] = $typeNames[$typeName];
				}
				$purchaseTypeDetails = implode(', ', $purchaseTypeDetails);
			}
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_COUPON_MIN_ORDER') . '</b></td>' .
							'<td> ' . $currencies->format($couponMinOrder) . '</td>' .
						'</tr>' . 
						'<tr>' . 
							'<td><b>' . sysLanguage::get('TEXT_COUPON_STARTDATE') . '</b></td>' .
							'<td>' . tep_date_short($couponStartDate) . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_COUPON_USES_COUPON') . '</b></td>' .
							'<td>' . $usesPerCoupon . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_COUPON_FINISHDATE') . '</b></td>' .
							'<td>'  . tep_date_short($couponExpireDate) . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_COUPON_USES_USER') . '</b></td>' .
							'<td>' . $usesPerUser . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_DATE_CREATED') . '</b></td>' .
							'<td>' . tep_date_short($dateCreated) . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_COUPON_PURCHASE_TYPE') . '</b></td>' .
							'<td>' . $purchaseTypeDetails . '</td>' .
						'</tr>' .
						'<tr>' .
							//'<td><b>' . sysLanguage::get('TEXT_COUPON_PRODUCTS') . '</b></td>' .
							//'<td>' . $prodDetails . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_DATE_MODIFIED') . '</b></td>' .
							'<td>' . tep_date_short($dateModified) . '</td>' .
						'</tr>' .
						//'<tr>' .
						//	'<td><b>' . sysLanguage::get('TEXT_COUPON_CATEGORIES') . '</b></td>' .
						//	'<td>' . $catDetails . '</td>' .
						//'</tr>' .
					'</table>')
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_DEFAULT');
?></div>
<br />
<div style="text-align:right;">
	<form name="status" action="<?php echo itw_app_link(null, 'coupons', 'default');?>" method="get"><?php
		$status_array[] = array('id' => 'Y', 'text' => sysLanguage::get('TEXT_COUPON_ACTIVE'));
		$status_array[] = array('id' => 'N', 'text' => sysLanguage::get('TEXT_COUPON_INACTIVE'));
		$status_array[] = array('id' => '*', 'text' => sysLanguage::get('TEXT_COUPON_ALL'));
		
		if (isset($_GET['status']) && !empty($_GET['status'])){
			$status = $_GET['status'];
		}else{
			$status = 'Y';
		}
		echo sysLanguage::get('HEADING_TITLE_STATUS') . ' ' . tep_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
	?></form>
</div>
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>