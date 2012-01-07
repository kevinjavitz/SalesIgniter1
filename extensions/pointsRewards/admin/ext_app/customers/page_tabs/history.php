<?php
	$QpointsRewardsEarned = Doctrine_Query::create()
	->from('pointsRewardsPointsEarned')
	->where('customers_id = ?', $cID)
	->orderBy('pointsEarned_id DESC')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$QpointsRewardsDeducted = Doctrine_Query::create()
		->from('pointsRewardsPointsDeducted')
		->where('customers_id = ?', $cID)
		->orderBy('pointsDeducted_id DESC')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$htmlTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->addClass('ui-widget')
	->css(array(
		'width' => '100%'
	));
	$saveForm = htmlBase::newElement('form')
			->attr('name', 'addRemovePoints')
			->attr('id', 'frmAddRemovePoints')
			->attr('method', 'post');
	$points = htmlBase::newElement('input')
			->setName('points')
			->setType('text');

	$actionType = htmlBase::newElement('selectbox')
			->setName('actionAddRemove')
			->setId('actionAddRemove');
	$actionType->addOption('', ' Select Action ');
	$actionType->addOption('add', 'ADD');
	$actionType->addOption('deduct', 'DEDUCT');

	$purchaseType = htmlBase::newElement('selectbox')
			->setName('purchaseType');
	//$purchaseType->addOption('', ' Select purchase type ');
	$purchaseType->addOption('new', 'New');
	$purchaseType->addOption('used', 'Used');
	//$purchaseType->addOption('rental' , 'Member Rental');
	if (defined('EXTENSION_PAY_PER_RENTALS_ENABLED') && EXTENSION_PAY_PER_RENTALS_ENABLED == 'True'){
		$purchaseType->addOption('reservation', 'Pay per rental');
	}
	if (defined('EXTENSION_STREAMPRODUCTS_ENABLED') && EXTENSION_STREAMPRODUCTS_ENABLED == 'True'){
		$purchaseType->addOption('stream', 'Streaming');
	}
	if (defined('EXTENSION_DOWNLOADPRODUCTS_ENABLED') && EXTENSION_DOWNLOADPRODUCTS_ENABLED == 'True'){
		$purchaseType->addOption('download', 'Download');
	}
	$TableHidden = htmlBase::newElement('table')
			->setCellPadding(3)
			->setCellSpacing(0)
			->addClass('ui-widget ui-widget-content')
			->css(array(
					   'width' => '100%'
				  ))
			->attr('customers_id', $cID)
			->attr('id', 'managePointsTable');

	$TableHidden->addBodyRow(array(
								  'columns' => array(
									  array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TEXT_POINTS_TO_ADD')),
									  array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TEXT_PURCHASE_TYPE'))
								  )
							 ));
	$TableHidden->addBodyRow(array(
								  'columns' => array(
									  array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0),'text' => $points->draw()),
									  array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0),'text' => $purchaseType->draw())
								  )
							 ));
	$addPointsButton = htmlBase::newElement('button')->setText('Add Points')->addClass('newButton')->setType('button')->setId('btnAddPoints');
	$deductPointsButton = htmlBase::newElement('button')->setText('Deduct Points')->addClass('newButton')->setType('button')->setId('btnDeductPoints');
	$saveForm->append($TableHidden);
	$saveForm->append($addPointsButton);
	$saveForm->append($deductPointsButton);

	$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main',
						      'css'=>array('text-align:center'),
						      'colspan' => 5,
						      'text' => $saveForm->draw())
						)));
	$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'pageStackContainer',
						      'css'=>array('text-align:center'),
						      'colspan' => 5,
						      'text' => '')
						)));
    $htmlTable2 = htmlBase::newElement('table')
            ->setCellPadding(2)
            ->setCellSpacing(0)
            ->attr('id', 'pointsHistoryTable')
            ->addClass('ui-widget')
            ->css(array(
        'width' => '100%'
    ));
    $htmlTable2->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_POINTS')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_DATE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_ORDER')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_PRODUCT'))
		)
	));
	if (!$QpointsRewardsEarned && !$QpointsRewardsDeducted){
		$htmlTable2->addBodyRow(array(
			'columns' => array(
				array('colspan' => 5, 'addCls' => 'ui-widget-content', 'align' => 'center', 'css' => array('border-top' => 0), 'text' => sysLanguage::get('TEXT_INFO_NO_HISTORY'))
			)
		));
	}else{
		if($QpointsRewardsEarned){
			$htmlTable2->addBodyRow(array(
			                            'columns' => array(
				                            array('css'=>array('text-align:center'),
                                                  'addCls' => 'ui-widget-header',
				                                  'colspan' => 5,
				                                  'text' => sysLanguage::get('TABLE_HEADING_POINTS_EARNINGS'))
			                            )));
			foreach($QpointsRewardsEarned as $pInfo){
				$htmlTable2->addBodyRow(array(
				                            'columns' => array(
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $pInfo['points']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['purchase_type']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d',strtotime($pInfo['date']))),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['orders_id']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['products_id'])
				                            )
				                       ));

			}
		}
		if($QpointsRewardsDeducted){
            $htmlTable2->addBodyRow(array(
                'columns' => array(
                    array('css'=>array('text-align:center'),
                        'addCls' => 'ui-widget-header',
                        'colspan' => 5,
                        'text' => sysLanguage::get('TABLE_HEADING_POINTS_SPENDINGS'))
                )));
			foreach($QpointsRewardsDeducted as $pInfo){

				$htmlTable2->addBodyRow(array(
				                            'columns' => array(
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => '-' . $pInfo['points']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['purchase_type']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d',strtotime($pInfo['date']))),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['orders_id']),
					                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $pInfo['products_id'])
				                            )
				                       ));

			}
		}
	}
    if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
        echo $htmlTable2->draw();
    } else {
        $htmlTable->addBodyRow(array(
            'columns' => array(
                array('text' => $htmlTable2)
            )));
        echo $htmlTable->draw();
    }
?>
