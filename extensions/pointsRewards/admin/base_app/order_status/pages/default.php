<?php
$saveForm = htmlBase::newElement('form')
		->attr('name', 'pointsRewardsSaveStatus')
		->attr('action', itw_app_link('appExt=pointsRewards&action=save', 'order_status', 'default'))
		->attr('method', 'post');

$Qstatus = Doctrine_Query::create()
		->select('s.orders_status_id, sd.orders_status_name')
		->from('OrdersStatus s')
		->leftJoin('s.OrdersStatusDescription sd')
		->where('sd.language_id = ?', (int) Session::get('languages_id'))
		->orderBy('s.orders_status_id');

$tableGrid = htmlBase::newElement('newGrid')
		->usePagination(true)
		->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
		->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
		->setQuery($Qstatus);

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->setText('Save')->addClass('newButton')->setType('submit')
));

$tableGrid->addHeaderRow(array(
                              'columns' => array(
	                              array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_STATUS')),
	                              array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
                              )
                         ));

$OrdersStatus = &$tableGrid->getResults();
if ($OrdersStatus){
	$id = 0;
	foreach($OrdersStatus as $sInfo){

		$id = $sInfo['orders_status_id'];
		$name = $sInfo['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'];

		$royaltiesSystemOrderStatus =  Doctrine_Core::getTable('pointsRewardsOrderStatuses')->findOneByOrdersStatusId($id);
		//$royaltiesSystemOrderStatus = $royaltiesSystemOrderStatusTable->findOneByOrdersStatusId($id);
		$checkBox = htmlBase::newElement('checkbox')
				->setName('status_id[]')
				->setValue($id);

		if($royaltiesSystemOrderStatus && $royaltiesSystemOrderStatus->orders_status_id == $id){
			$checkBox->setChecked(true);
		}
		$tableGrid->addBodyRow(array(
		                            'rowAttr' => array('data-status_id' => $id),
		                            'columns' => array(
			                            array('text' => $name),
			                            array('align' => 'center',
			                                  'text' => $checkBox->draw())
		                            )
		                       ));

	}
}
$saveForm->append($tableGrid);
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
	?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $saveForm->draw();?></div>
		</div>
	</div>
</div>