<?php
	$QRentIssues = Doctrine_Query::create()
	->from('RentIssues')
	->where('parent_id = ?', '0');

$tableGrid = htmlBase::newElement('grid')
->usePagination(true)
->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
->setQuery($QRentIssues);

$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ISSUE_ID')),
			array('text' => sysLanguage::get('TABLE_HEADING_RENTED_PRODUCT')),
			array('text' => sysLanguage::get('TABLE_HEADING_REPORTED_DATE')),
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMER_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

$rIssue = &$tableGrid->getResults();
if ($rIssue){
	foreach($rIssue as $fInfo){
		$rIssueId = $fInfo['issue_id'];

		if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $rIssueId))) && !isset($fObject)){
			$fObject = new objectInfo($fInfo);
		}

		$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $rIssueId));

		$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'fID=' . $rIssueId);
		if (isset($fObject) && $rIssueId == $fObject->issue_id){
			$addCls = 'ui-state-default';
			$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit&fID=' . $rIssueId);
			$arrowIcon->setType('circleTriangleEast');
		} else {
			$addCls = '';
			$arrowIcon->setType('info');
		}
	    $Qcustomer = Doctrine_Query::create()
		->from('Customers')
		->where('customers_id = ?', $fInfo['customers_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if($fInfo['status'] == 'O'){
			$status = 'Opened';
		}else{
			$status = 'Closed';
		}
		$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $fInfo['issue_id']),
					array('text' => $fInfo['products_name']),
					array('text' => sprintf(sysLanguage::getDateFormat('short'), strtotime($fInfo['reported_date']))),
					array('text' => $Qcustomer[0]['customers_firstname']. ' '.$Qcustomer[0]['customers_lastname']),
					array('text' => $status),
					array('text' => $arrowIcon->draw(), 'align' => 'right')
				)
			));
	}
}

$infoBox = htmlBase::newElement('infobox');
$infoBox->setButtonBarLocation('top');

switch ($action){
	case 'edit':
		$infoBox->setForm(array(
				'action'    => itw_app_link(tep_get_all_get_params(array('action')) . 'action=saveIssue'),
				'method'    =>  'post',
				'name'      => 'edit_issue'
			)
		);

		if (isset($_GET['fID'])) {
			$rIssue = Doctrine_Core::getTable('RentIssues')->find($_GET['fID']);

			$infoBox->setHeader('<b>Reply Issue</b>');

			$QIssues = Doctrine_Query::create()
			->from('RentIssues')
			->where('parent_id = ?', $_GET['fID'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$Qcustomer = Doctrine_Query::create()
			->from('Customers')
			->where('customers_id = ?', $rIssue->customers_id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$replyMsg = $Qcustomer[0]['customers_firstname'].' '.$Qcustomer[0]['customers_lastname'].': '.$rIssue->feedback.'<br/>';
			foreach($QIssues as $dissue){
				if($dissue['customers_id'] > 0){
					$replyMsg .= $Qcustomer[0]['customers_firstname'].' '.$Qcustomer[0]['customers_lastname'].': '.$dissue['feedback']."<br>";
				}else{
					$replyMsg .= '&nbsp;&nbsp;&nbsp;Me: '.$dissue['feedback']."<br>";
				}
			}

			$replyText = htmlBase::newElement('textarea')
			->attr('rows', '5')
			->attr('cols','20')
			->attr('name','feedback');

			$saveButton = htmlBase::newElement('button')
			->setType('submit')
			->usePreset('save')
			->setText(sysLanguage::get('TEXT_REPLY'));

			$closeButton = htmlBase::newElement('button')
			->setType('submit')
			->usePreset('cancel')
			->setText(sysLanguage::get('TEXT_CLOSE'))
			->setHref(itw_app_link('action=closeIssue&fID='.$_GET['fID'], 'rental_queue', 'issues'));

			$cancelButton = htmlBase::newElement('button')
			->usePreset('close')
			->setText(sysLanguage::get('TEXT_CANCEL'))
			->setHref(itw_app_link(null, 'rental_queue', 'issues'));

		$infoBox->addContentRow($replyMsg);
		$infoBox->addContentRow('Reply: '.$replyText->draw());
		$infoBox->addContentRow(tep_draw_hidden_field('customers_id', $rIssue->customers_id). tep_draw_hidden_field('products_id', $rIssue->products_id));
		$infoBox->addButton($saveButton)->addButton($closeButton)->addButton($cancelButton);
		}

		break;
	default:
		if (isset($fObject) && is_object($fObject)) {
			$infoBox->setHeader('<b>' . $fObject->feedback . '</b>');

			$deleteButton = htmlBase::newElement('button')
				->setType('submit')
				->usePreset('delete')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=deleteIssueConfirm&fID=' . $fObject->issue_id));
			$editButton = htmlBase::newElement('button')
				->setType('submit')
				->usePreset('edit')
				->setHref(itw_app_link(tep_get_all_get_params(array('action', 'fID')) . 'action=edit' . '&fID=' . $fObject->issue_id, 'rental_queue', 'issues'));

			$infoBox->addButton($editButton)->addButton($deleteButton);

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
	<div style="text-align:right;"><?php
		?></div>
</div>
<div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>