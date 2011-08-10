<?php
//list sub accounts here
$Qaccounts = Doctrine_Query::create()
->from('Customers')
->where('parent = ?', $userAccount->getCustomerId());

$tableGridOrders = htmlBase::newElement('grid')
	->usePagination(false)
	->setPageLimit(10)
	->setQuery($Qaccounts);

$gridHeaderColumns = array(
	array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS')),
	array('text' => sysLanguage::get('TABLE_HEADING_EMAIL')),
	array('text' => sysLanguage::get('TABLE_HEADING_EDIT')),
	array('text' => sysLanguage::get('TABLE_HEADING_DELETE')),
);

$tableGridOrders->addHeaderRow(array(
	'columns' => $gridHeaderColumns
));

$subAccounts = &$tableGridOrders->getResults();

if ($subAccounts){
	foreach($subAccounts as $subAccount){
		$accID = $subAccount['customers_id'];
		$onClickLinkEdit = '<a href="' . itw_app_link('cID=' . $accID.'&appExt=subAccounts', 'manage', 'edit') . '">Edit</a>';
		$onClickLinkDelete = '<a href="' . itw_app_link('cID=' . $accID.'&appExt=subAccounts&action=deleteAccount', 'manage', 'default') . '">Delete</a>';
		$gridBodyColumns = array(
			array('text' => $subAccount['customers_firstname']. ' '.$subAccount['customers_lastname'], 'align' => 'right'),
			array('text' => $subAccount['customers_email_address'], 'align' => 'center'),
			array('text' => $onClickLinkEdit, 'align' => 'center'),
			array('text' => $onClickLinkDelete)
		);
		$tableGridOrders->addBodyRow(array(
			'columns' => $gridBodyColumns
		));
	}
}

$addAccountButton = htmlBase::newElement('button')
->setText(sysLanguage::get('TEXT_ADD_SUBACCOUNTS'))
->setHref(itw_app_link('appExt=subAccounts','manage','create'));

ob_start();
?>
<div class="">
<?php
	echo $addAccountButton->draw();
	?>
	<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_LIST_SUBACCOUNTS'); ?></div>
<?php
			echo $tableGridOrders->draw();
	?>
</div>

<?php
	$pageContents = ob_get_contents();
ob_end_clean();

$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE');

$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null,'account','default'))
	->setType('submit')
	->draw();


$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
?>