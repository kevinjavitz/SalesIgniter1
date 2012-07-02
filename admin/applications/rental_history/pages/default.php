<?php
//build params
$params = array(
	'orderBy' => (isset( $_GET['orderBy'] ) ? $_GET['orderBy'] : 'date'),
	'sort' => (isset( $_GET['sort'] ) ? $_GET['sort'] : 'DESC'),
	'filter' => (isset( $_GET['filter'] ) ? $_GET['filter'] : 'all'),
	'page' => (isset( $_GET['page'] ) ? $_GET['page'] : 1)
);

//small function for building the &key=value part of URL
function _buildUrl($p) {
	$r = '';
	foreach($p as $k => $v) { $r .= '&'.$k.'='.$v; }
	return substr($r,1);
}

//setting sql for filtering
$future = date( 'Y-m-d H:i:s', strtotime( '-30 day' , time() ) );
switch( $params['filter'] ) {
	case 'sent':
		$where = 'rP.shipment_date != \'0000-00-00 00:00:00\' AND rP.date_added > \''.$future.'\'';
		break;
	case 'return':
		$where = 'rP.return_date != \'0000-00-00 00:00:00\' AND rP.date_added > \''.$future.'\'';
		break;
	case 'all':
	default:
		$where = '( rP.shipment_date != \'0000-00-00 00:00:00\' OR rP.return_date != \'0000-00-00 00:00:00\' ) AND rP.date_added > \''.$future.'\'';
		break;
}

//setting actual sql for sorting
switch( $params['orderBy'] ) {
	case 'customer':
		$_orderBy = 'c.customers_firstname'; break;
	case 'product':
		$_orderBy = 'pD.products_name'; break;
	case 'date':
	default:
		$_orderBy = "sort_date"; break;
}

//create the query
$Qrentals = Doctrine_Query::create()
	->select('rP.*, p.*, pD.*, c.*, aB.*, (CASE rP.return_date WHEN \'0000-00-00 00:00:00\' THEN rP.shipment_date ELSE rP.return_date END) AS sort_date')
	->from('RentedProducts rP')
	->leftJoin('rP.Products as p')
		->leftJoin('p.ProductsDescription pD')
	->leftJoin('rP.Customers c')
		->leftJoin('c.AddressBook aB')
	->where($where)
	->orderBy($_orderBy.' '.$params['sort'].', pD.products_name ASC')
	->groupBy('rP.products_barcode');

//create grid
$reportGrid = htmlBase::newElement('newGrid')->setQuery($Qrentals);
$reportGrid->usePagination(true);
$reportGrid->setPageLimit(25);
$reportGrid->setCurrentPage($params['page']);

//START - sorting
	$_params = $params;
	$_params['sort'] = $params['sort'] == 'DESC' ? 'ASC' : 'DESC'; //switch the sorting
	$_sortText = $params['sort'] == 'DESC' ? '&darr;' : '&uarr;';
	
	$_params['orderBy'] = 'customer';
	$sortName = $params['orderBy'] == 'customer' ? sysLanguage::get('TABLE_HEADING_NAME').' '.$_sortText : sysLanguage::get('TABLE_HEADING_NAME');
	$sortName = '<a href="'.itw_app_link(_buildUrl($_params), 'rental_history', 'default').'">'.$sortName.'</a>';
	
	$_params['orderBy'] = 'product';
	$sortProduct = $params['orderBy'] == 'product' ? sysLanguage::get('TABLE_HEADING_PRODUCT').' '.$_sortText : sysLanguage::get('TABLE_HEADING_PRODUCT');
	$sortProduct = '<a href="'.itw_app_link(_buildUrl($_params), 'rental_history', 'default').'">'.$sortProduct.'</a>';
	
	$_params['orderBy'] = 'date';
	$sortDate = $params['orderBy'] == 'date' ? sysLanguage::get('TABLE_HEADING_DATE').' '.$_sortText : sysLanguage::get('TABLE_HEADING_DATE');
	$sortDate = '<a href="'.itw_app_link(_buildUrl($_params), 'rental_history', 'default').'">'.$sortDate.'</a>';
//END - sorting
$reportGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => $sortName),
		array('text' => sysLanguage::get('TABLE_HEADING_ADDRESS')),
		array('text' => $sortProduct),
		array('text' => sysLanguage::get('TABLE_HEADING_BARCODE')),
		array('text' => $sortDate),
		array('text' => sysLanguage::get('TABLE_HEADING_STATUS'))
	)
));

//add each row
$rentalHistory = &$reportGrid->getResults();
if ($rentalHistory){
	foreach($rentalHistory as $rInfo){
		$status = $rInfo['return_date'] == '0000-00-00 00:00:00' ? 'Sent' : 'Returned';
		
		$Qbarcode = Doctrine_Query::create()
			->select('barcode')
			->from('ProductsInventoryBarcodes')
			->where('barcode_id=?', $rInfo['products_barcode'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$reportGrid->addBodyRow(array(
			'columns' => array(
				array('align' => 'center', 'text' => $rInfo['Customers']['customers_firstname'].' '.$rInfo['Customers']['customers_lastname']),
				array('align' => 'center', 'text' => $rInfo['Customers']['AddressBook'][0]['entry_street_address']),
				array('align' => 'center', 'text' => $rInfo['Products']['ProductsDescription'][1]['products_name']),
				array('align' => 'center', 'text' => $Qbarcode[0]['barcode']),
				array('align' => 'center', 'text' => date( 'Y-m-d', strtotime( $rInfo['sort_date'] ) )),
				array('align' => 'center', 'text' => $status)
			)
		));
	}
}

//add grid buttons
$gridButtons = Array();
$_params = $params;

$_params['filter'] = 'all';
if($params['filter'] != 'all' )
	$gridButtons[] = htmlBase::newElement('button')->setText('Show All')->setHref( itw_app_link(_buildUrl($_params), 'rental_history', 'default') );

$_params['filter'] = 'sent';
if($params['filter'] != 'sent' )
	$gridButtons[] = htmlBase::newElement('button')->setText('Hide Returned')->setHref( itw_app_link(_buildUrl($_params), 'rental_history', 'default') );

$_params['filter'] = 'return';
if($params['filter'] != 'return' )
	$gridButtons[] = htmlBase::newElement('button')->setText('Hide Sent')->setHref( itw_app_link(_buildUrl($_params), 'rental_history', 'default') );
//EventManager::notify('OrdersGridButtonsBeforeAdd', &$gridButtons);

$reportGrid->addButtons($gridButtons);
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $reportGrid->draw();?></div>
		</div>
	</div>
</div>