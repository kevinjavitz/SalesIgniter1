<?php
	$form = htmlBase::newElement('form')
	->attr('name', 'new_special')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'action=save'))
	->attr('method', 'post');
	
	if (isset($_GET['sID'])){
		$Qproduct = Doctrine_Query::create()
		->select('p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date')
		->from('Specials s')
		->leftJoin('s.Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('s.specials_id = ?', (int)$_GET['sID'])
		->fetchOne();
		
		$product = $Qproduct->toArray();
		
		$hiddenField = htmlBase::newElement('input')->setType('hidden')->setName('specials_id')->setValue((int)$_GET['sID']);
		$form->append($hiddenField);
	}else{
		$product = array(
			'specials_new_products_price' => 0.0000,
			'expires_date'                => null,
			'Products' => array(
				'products_id' => 0,
				'products_price' => 0.0000,
				'ProductsDescription' => array(
					Session::get('languages_id') => array(
						'products_name' => null,
					)
				)
			)
		);
		
		$specials_array = array();
		$Qspecials = Doctrine_Query::create()
		->select('products_id')
		->from('Specials')
		->execute();
		if ($Qspecials){
			foreach($Qspecials->toArray() as $special){
				$specials_array[] = $special['products_id'];
			}
		}
	}
	
	$priceHiddenInput = htmlBase::newElement('input')->setType('hidden')->setName('products_price');
	if ($product['Products']['products_price'] > 0){
		$priceHiddenInput->setValue($product['Products']['products_price']);
	}
	$form->append($priceHiddenInput);
	
	$formTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	
	if (!is_null($product['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'])){
		$productIdHidden = htmlBase::newElement('input')->setType('hidden')
		->setName('products_id')->setValue($product['Products']['products_id']);
		
		$productInput = $product['Products']['ProductsDescription'][Session::get('languages_id')]['products_name'] . ' <small>(' . $currencies->format($product['Products']['products_price']) . ')</small>' . $productIdHidden->draw();
	}else{
		$productInput = tep_draw_products_pull_down('products_id', 'style="font-size:.9em"', $specials_array);
	}
	
	$priceInput = htmlBase::newElement('input')->setName('specials_price');
	if ($product['specials_new_products_price'] > 0){
		$priceInput->setValue($product['specials_new_products_price']);
	}
	
	$dateInput = htmlBase::newElement('input')->setName('expires_date')->setId('expiryDate');
	if (!is_null($product['expires_date'])){
		$date = explode(' ',$product['expires_date']);
		$dateInput->setValue($date[0]);
	}
	
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_SPECIALS_PRODUCT')),
			array('addCls' => 'main', 'text' => $productInput)
		)
	));
	
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_SPECIALS_SPECIAL_PRICE')),
			array('addCls' => 'main', 'text' => $priceInput)
		)
	));
	
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_SPECIALS_EXPIRES_DATE')),
			array('addCls' => 'main', 'text' => $dateInput->draw())
		)
	));
	
	$savebutton = htmlBase::newElement('button')->css(array(
		'position' => 'absolute',
		'top' => '.3em',
		'right' => '.3em'
	))->setType('submit')->usePreset('save');
	
	$cancelbutton = htmlBase::newElement('button')->css(array(
		'position' => 'absolute',
		'top' => '.3em',
		'right' => '6.3em'
	))->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));
	
	$infoBar = htmlBase::newElement('div')->css(array(
		'text-align' => 'left',
		'position' => 'relative',
		'margin-top' => '.5em'
	))->html(sysLanguage::get('TEXT_SPECIALS_PRICE_TIP'))->append($savebutton)->append($cancelbutton);
	
	$form->append($formTable)->append($infoBar);
?>
<div class="pageHeading"><?php 
 echo sysLanguage::get('HEADING_TITLE');
?></div>
<?php echo $form->draw();?>