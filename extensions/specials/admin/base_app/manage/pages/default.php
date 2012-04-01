<?php
/*
	Products Specials Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qspecials = Doctrine_Query::create()
	->select('p.products_id, p.products_image, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status')
	->from('Specials s')
	->leftJoin('s.Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->orderBy('pd.products_name');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qspecials);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_PRICE')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$products = &$tableGrid->getResults();
	foreach($products as $pInfo){
		$specialId = $pInfo['specials_id'];

		if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ($_GET['sID'] == $pInfo['specials_id']))) && !isset($sInfo)){
			$sInfo = new objectInfo($pInfo);
		}

		$arrowIcon = htmlBase::newElement('icon')
		->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $specialId));

		$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $specialId);
		if (isset($sInfo) && $specialId == $sInfo->specials_id){
			$addCls = 'ui-state-default';
			$onClickLink .= '&action=edit';
			$arrowIcon->setType('circleTriangleEast');
		} else {
			$addCls = '';
			$arrowIcon->setType('info');
		}

		$statusIcon = htmlBase::newElement('icon');
		if ($pInfo['status'] == '1') {
			$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'action=setflag&flag=0&sID=' . $specialId));
		} else {
			$statusIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'action=setflag&flag=1&sID=' . $specialId));
		}

		$tableGrid->addBodyRow(array(
			'addCls'  => $addCls,
			'click'   => 'document.location=\'' . $onClickLink . '\'',
			'columns' => array(
				array('text' => $pInfo['Products']['ProductsDescription'][Session::get('languages_id')]['products_name']),
				array('text' => '<s>' . $currencies->format($pInfo['Products']['products_price']) . '</s> <span class="specialPrice">' . $currencies->format($pInfo['specials_new_products_price']) . '</span>', 'align' => 'center'),
				array('text' => $statusIcon->draw(), 'align' => 'center'),
				array('text' => $arrowIcon->draw(), 'align' => 'right')
			)
		));
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	$editButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_EDIT'));
	$deleteButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DELETE'));
	if (!empty($action)){
		$cancelButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CANCEL'));
	}

	switch ($action) {
		case 'delete':
			$Products = $sInfo->Products;
			$ProductsDescription = $Products['ProductsDescription'][Session::get('languages_id')];

			$infoBox->setHeader('<b>' . $ProductsDescription['products_name'] . '</b>');
			$intro = sysLanguage::get('TEXT_INFO_DELETE_INTRO');
			$infoBox->setForm(array(
				'name'   => 'specials',
				'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteConfirm')
			));

			$deleteButton->setType('submit');
			$cancelButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $sInfo->specials_id));

			$infoBox->addButton($deleteButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DELETE_INTRO'));
			$infoBox->addContentRow('<b>' . $ProductsDescription['products_name'] . '</b>');
			break;
		default:
			if (isset($sInfo)){
				$Products = $sInfo->Products;
				$ProductsDescription = $Products['ProductsDescription'][Session::get('languages_id')];

				$infoBox->setHeader('<b>' . $ProductsDescription['products_name'] . '</b>');

				$editButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $sInfo->specials_id, null, 'new'));
				$deleteButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'action=delete&sID=' . $sInfo->specials_id));

				$infoBox->addButton($editButton)->addButton($deleteButton);

				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DATE_ADDED') . ' ' . tep_date_short($sInfo->specials_date_added));
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . ' ' . tep_date_short($sInfo->specials_last_modified));
				$infoBox->addContentRow(tep_info_image($Products['products_image'], $ProductsDescription['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ORIGINAL_PRICE') . ' ' . $currencies->format($Products['products_price']));
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_NEW_PRICE') . ' ' . $currencies->format($sInfo->specials_new_products_price));
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_PERCENTAGE') . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $Products['products_price']) * 100)) . '%');
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_EXPIRES_DATE') . ' <b>' . tep_date_short($sInfo->expires_date) . '</b>');
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_STATUS_CHANGE') . ' ' . tep_date_short($sInfo->date_status_change));
			}
			break;
	}

?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
   	$newProductButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_NEW_PRODUCT'))
   	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'sID')), null, 'new'));

   	echo $newProductButton->draw();
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>