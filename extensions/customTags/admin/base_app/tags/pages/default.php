<?php
	$QCustomTags = Doctrine_Query::create()
	->from('CustomTags ct')
	->leftJoin('ct.TagsToProducts tp')
	->leftJoin('tp.Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('tp.Customers c')
	->where('pd.language_id = ?', Session::get('languages_id'));
    $f = false;
	if(isset($_GET['customers_id']) && $_GET['customers_id'] > 0){
		$QCustomTags->andWhere('c.customers_id = ?', $_GET['customers_id']);
		$f = true;
	}

	if(isset($_GET['products_id']) && $_GET['products_id'] > 0){
		$QCustomTags->andWhere('p.products_id = ?', $_GET['products_id']);
		$f = true;
	}
	if(!$f){
		$QCustomTags->orWhere('tp.tag_to_products_id is null');
	}
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($QCustomTags);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMTAGS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAG_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAG_PRODUCTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAG_CUSTOMER')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));
	
	$tagGrid = &$tableGrid->getResults();
	if ($tagGrid){
		foreach($tagGrid as $tInfo){
			$tagId = $tInfo['tag_id'];
		
			if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $tagId))) && !isset($tObject)){
				$tObject = new objectInfo($tInfo);
			}
		
			$arrowIcon = htmlBase::newElement('icon')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'tID=' . $tagId));

			$statusIcon = htmlBase::newElement('icon');
			if ($tInfo['tag_status'] == '1' ){
				$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
				->setHref(itw_app_link('appExt=customTags&action=setflag&flag=0&tID=' . $tagId,'tags','default'));
			}else if ($tInfo['tag_status'] == '0' ){
				$statusIcon->setType('circleClose')->setTooltip('Click to enable')
				->setHref(itw_app_link('appExt=customTags&action=setflag&flag=1&tID=' . $tagId,'tags','default'));
			}

			$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'tID=' . $tagId);
			if (isset($tObject) && $tagId == $tObject->tag_id){
				$addCls = 'ui-state-default';
				$onClickLink .= itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'action=edit&tID=' . $tagId);
				$arrowIcon->setType('circleTriangleEast');
			} else {
				$addCls = '';
				$arrowIcon->setType('info');
			}

			if($tInfo['tag_status'] == '0'){
				$tagStatus = 'Inactive';
			}else{
				$tagStatus = 'Active';
			}
			$prodNames = '';
			$customerName = '';
			$lastCustomerName = '';
			/*$QProductCustom = Doctrine_Query::create()
				->from('CustomTags ct')
				->leftJoin('ct.TagsToProducts tp')
				->leftJoin('tp.Products p')
				->leftJoin('p.ProductsDescription pd')
				->leftJoin('tp.Customers c')
				->where('pd.language_id = ?', Session::get('languages_id'))
				->andWhere('ct.tag_id = ?', $tInfo['tag_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			        $QProductCustom[0]['TagsToProducts']
			*/

			foreach($tInfo['TagsToProducts'] as $tProduct){
				$prodNames .= $tProduct['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'].', ';
				if($lastCustomerName != $tProduct['Customers']['customers_firstname']. ' '.$tProduct['Customers']['customers_lastname']){
					$customerName .= $tProduct['Customers']['customers_firstname']. ' '.$tProduct['Customers']['customers_lastname']. ', ';
					$lastCustomerName = $tProduct['Customers']['customers_firstname']. ' '.$tProduct['Customers']['customers_lastname'];
				}
			}

			$prodNames = substr($prodNames,0, strlen($prodNames) - 2);
			$customerName = substr($customerName,0, strlen($customerName) - 2);
		
			$tableGrid->addBodyRow(array(
				'addCls'  => $addCls,
				'click'   => 'js_redirect(\'' . $onClickLink . '\');',
				'columns' => array(
					array('text' => $tInfo['tag_name']),
					array('text' => $statusIcon->draw()),
					array('text' => $prodNames),
					array('text' => $customerName),
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
								'action'    => itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'),
								'method'    =>  'post',
								'name'      => 'edit_tags'
							)
			);

			$status = htmlBase::newElement('selectbox')
						->setLabel('Tag Status: ')
						->setLabelPosition('before')
						->setName('tag_status');

			$status->addOption('0', 'Inactive');
			$status->addOption('1', 'Active');

		 	 if (isset($_GET['tID'])) {
		            $tag = Doctrine_Core::getTable('CustomTags')->findOneByTagId($_GET['tID']);
				    $tag_name = $tag->tag_name;

					$status->selectOptionByValue($tag->tag_status);
				    $infoBox->setHeader('<b>Edit Tag</b>');
			 }

			 $htmlTagName = htmlBase::newElement('input')
			            ->setLabel(sysLanguage::get('TEXT_TAG_NAME'))
						->setLabelPosition('before')
					    ->setName('tag_name')
					    ->setValue($tag_name);



 			 $saveButton = htmlBase::newElement('button')
					        ->setType('submit')
					        ->usePreset('save');
			 $cancelButton = htmlBase::newElement('button')
					        ->usePreset('cancel')
			                ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));



			 $infoBox->addContentRow($htmlTagName->draw());
			 $infoBox->addContentRow($status->draw());
			 $infoBox->addButton($saveButton)->addButton($cancelButton);

			 break;
		default:
			if (isset($tObject) && is_object($tObject)) {
				$infoBox->setHeader('<b>' . $tObject->tag_name . '</b>');
				
				$deleteButton = htmlBase::newElement('button')
								->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'action=deleteConfirm&tID=' . $tObject->tag_id));
				$editButton = htmlBase::newElement('button')
								->usePreset('edit')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'action=edit' . '&tID=' . $tObject->tag_id, 'tags', 'default'));
				
				$infoBox->addButton($editButton)->addButton($deleteButton);

			}
			break;
	}
$searchForm = htmlBase::newElement('form')
	->attr('name', 'search')
	->attr('id', 'search')
	->attr('action', itw_app_link('appExt=customTags', 'tags','default'))
	->attr('method', 'get');

$htmlSelectProduct = htmlBase::newElement('selectbox')
	->setLabel('Product Name: ')
	->setLabelPosition('before')
	->setName('products_id');

$htmlSelectProduct->addOption('0', 'Any Product');

$htmlSelectCustomer = htmlBase::newElement('selectbox')
	->setLabel('Customer Name: ')
	->setLabelPosition('before')
	->setName('customers_id');

$htmlSelectCustomer->addOption('0', 'Any Customer');

$lID = (int)Session::get('languages_id');

$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('pd.language_id = ?', $lID)
	->andWhere('p.products_in_box = ?', '0')
	->orderBy('p.products_featured desc, pd.products_name asc, p.products_id desc');

EventManager::notify('AdminProductListingQueryBeforeExecute', &$Qproducts);

$Qproducts = $Qproducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

foreach($Qproducts as $iProduct){
	$htmlSelectProduct->addOption($iProduct['products_id'], $iProduct['ProductsDescription'][0]['products_name']);
}

if(isset($_GET['products_id'])){
	$htmlSelectProduct->selectOptionByValue($_GET['products_id']);
}

if(isset($_GET['customers_id'])){
	$htmlSelectCustomer->selectOptionByValue($_GET['customers_id']);
}


$Qcustomers = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.CustomersMembership cm')
	->leftJoin('c.CustomersInfo i')
	->leftJoin('c.AddressBook a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)')
	->leftJoin('a.Countries co');
EventManager::notify('CustomersListingQueryBeforeExecute', &$Qcustomers);

$Qcustomers = $Qcustomers->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

foreach($Qcustomers as $iCustomer){
	$htmlSelectCustomer->addOption($iCustomer['customers_id'], $iCustomer['customers_firstname']. ' ' .$iCustomer['customers_lastname']);
}

$htmlButton = htmlBase::newElement('button')
->setType('submit')
->setText('Filter');

$searchForm->append($htmlSelectProduct)->append($htmlSelectCustomer)->append($htmlButton);

?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
<?php
echo $searchForm->draw();
	?>
	   <?php echo $tableGrid->draw();?></div>
  </div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>