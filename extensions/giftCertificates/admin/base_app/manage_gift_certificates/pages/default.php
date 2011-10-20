<?php
	$QGiftCertificates = Doctrine_Query::create()
        ->from('GiftCertificates gc')
        ->leftJoin('gc.GiftCertificatesDescription gcd')
        ->leftJoin('gc.GiftCertificatesToPurchaseTypes gcpt')
        ->where('gcd.language_id = ?', Session::get('languages_id'))
        ->orderBy('gcd.gift_certificates_name');

    if (isset($_GET['status']) && $_GET['status'] != '*'){
        $QGiftCertificates->andWhere('gc.gift_certificates_status = ?', $_GET['status']);
    }

    $tableGrid = htmlBase::newElement('newGrid')
            ->usePagination(true)
            ->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
            ->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
            ->setQuery($QGiftCertificates);

    $tableGrid->addButtons(array(
                                htmlBase::newElement('button')->setText('New')->addClass('insertButton'),
                                htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
                                htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
                                htmlBase::newElement('button')->setText('Email')->addClass('emailButton')
                           ));

    $tableGrid->addHeaderRow(array(
                                  'columns' => array(
                                      array('text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_NAME')),
                                      array('text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_PRICE')),
                                      array('text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_GLOBAL_VALUE')),
                                      array('text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_STATUS'))
                                  )
                             ));

    $GiftCertificates = &$tableGrid->getResults();
    if ($GiftCertificates){
        foreach($GiftCertificates as $gcInfo){
            $giftCertificatesActive = $gcInfo['gift_certificates_status'];
            $giftCertificatesId = $gcInfo['gift_certificates_id'];
            $valuesTable = htmlBase::newElement('table')
                    ->setCellPadding(2)
                    ->setCellSpacing(0)
                    ->css(array('width' => '100%'));
            foreach($gcInfo['GiftCertificatesToPurchaseTypes'] as $giftCertificatesPurchaseType) {
                $valuesTable->addBodyRow(array(
                    'columns' => array(
                        array('text' => ucwords($giftCertificatesPurchaseType['purchase_type'])),
                        array('text' => $currencies->format($giftCertificatesPurchaseType['gift_certificates_value']))
                    )
                ));
            }
            $giftCertificatesPrice = $gcInfo['gift_certificates_price'];
            $giftCertificatesName = $gcInfo['GiftCertificatesDescription'][0]['gift_certificates_name'];

            $arrowIcon = htmlBase::newElement('icon')->setType('info');

            $statusIcon = htmlBase::newElement('icon');
            if ($giftCertificatesActive == 'Y'){
                $statusIcon->setType('circleCheck')->setTooltip('Click to disable')
                        ->setHref(itw_app_link('appExt=giftCertificates&action=setflag&flag=N&gcID=' . $giftCertificatesId));
            }else{
                $statusIcon->setType('circleClose')->setTooltip('Click to enable')
                        ->setHref(itw_app_link('appExt=giftCertificates&action=setflag&flag=Y&gcID=' . $giftCertificatesId));
            }

            $tableGrid->addBodyRow(array(
                'rowAttr' => array(
                    'data-gift_certificates_id' => $giftCertificatesId
                ),
                'columns' => array(
                    array('text' => $giftCertificatesName),
                    array('align' => 'center', 'text' => $giftCertificatesPrice),
                    array('align' => 'center', 'text' => $valuesTable->draw()),
                    array('align' => 'center', 'text' => $statusIcon->draw()),
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
		$status_array[] = array('id' => 'Y', 'text' => sysLanguage::get('TEXT_GIFT_CERTIFICATES_ACTIVE'));
        $status_array[] = array('id' => 'N', 'text' => sysLanguage::get('TEXT_GIFT_CERTIFICATES_INACTIVE'));
        $status_array[] = array('id' => '*', 'text' => sysLanguage::get('TEXT_GIFT_CERTIFICATES_ALL'));

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