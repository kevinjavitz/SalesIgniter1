<?php
	ob_start();
//echo '<pre>';print_r($ShoppingCart);echo '</pre>';
if ($ShoppingCart->countContents() > 0) {
	$tableListing = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->attr('width', '100%');
	
	$shoppingCartHeader = array(
		array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_REMOVE'), 'align' => 'center'),
		array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS'), 'align' => 'center'),
		array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_QUANTITY'), 'align' => 'center'),
		array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_TOTAL'), 'align' => 'center'),
	);

	EventManager::notify('ShoppingCartListingAddHeaderColumn', &$shoppingCartHeader);

	$tableListing->addBodyRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => $shoppingCartHeader
	));

	$any_out_of_stock = 0;
	foreach($ShoppingCart->getProducts() as $cartProduct) {
		$pID_string = $cartProduct->getIdString();
		$purchaseType = $cartProduct->getPurchaseType();
		$purchaseQuantity = $cartProduct->getQuantity();

		if (($i/2) == floor($i/2)) {
			$addCls = 'productListing-even';
		} else {
			$addCls = 'productListing-odd';
		}

		$products_name = '<table border="0" cellspacing="2" cellpadding="2">' .
			'  <tr>' .
				'    <td class="productListing-data" align="center">' . $cartProduct->getImageHtml() . '</td>' .
				'    <td class="productListing-data" valign="top">' . $cartProduct->getNameHtml() . '</td>' .
			'  </tr>' .
		'</table>';
		$qty = tep_draw_hidden_field('products_id[]', $pID_string) .
		tep_draw_hidden_field('purchase_type[]', $purchaseType);

		/* @TODO: Get into pay per rental extension */
		if ($purchaseType == 'reservation'){
			$qty .= tep_draw_hidden_field('cart_quantity[]', $purchaseQuantity, 'size="4"') . $purchaseQuantity;
		}else{
			$qty .= tep_draw_input_field('cart_quantity[]', $purchaseQuantity, 'size="4"');
		}

		$shoppingCartBodyRow = array(
			array(
				'addCls' => 'productListing-data',
				'text' => tep_draw_checkbox_field('cart_delete[]', $pID_string),
				'attr' => array('align' => 'center', 'valign' => 'top')
			),
			array(
				'addCls' => 'productListing-data',
				'text' => $products_name,
				'attr' => array('align' => 'left', 'valign' => 'top')
			),
			array(
				'addCls' => 'productListing-data',
				'text' => $qty,
				'attr' => array('align' => 'center', 'valign' => 'top')
			),
			array(
				'addCls' => 'productListing-data',
				'text' => '<b>' . $currencies->display_price($cartProduct->getFinalPrice(), $cartProduct->productClass->getTaxRate(), $purchaseQuantity) . '</b>',
				'attr' => array('align' => 'right', 'valign' => 'top')
			)
		);

		EventManager::notify('ShoppingCartListingAddNewBodyColumn',&$shoppingCartBodyRow, $cartProduct);
		
		$tableListing->addBodyRow(array(
			'addCls'  => $addCls,
			'columns' => $shoppingCartBodyRow
		));
	}
	$div = htmlBase::newElement('div')
	->addClass('ui-widget ui-widget-content ui-corner-all');

	EventManager::notify('ShoppingCartListingBeforeListing', &$div);
	
	$div->append($tableListing);

	EventManager::notify('ShoppingCartListingAfterListing', &$div);
	
	$div->css(array(
		'margin-top' => '1em',
		'text-align' => 'center',
		'padding' => '.5em'
	));
	
	echo $div->draw();
?>
<div class="main" style="text-align:right;"><span class="smallText" style="float:left;"></span><b><?php echo sysLanguage::get('SUB_TITLE_SUB_TOTAL'); ?> <?php echo $currencies->format($ShoppingCart->showTotal()); ?></b></div>
<div style="clear:both;"></div>
<?php
if (sysConfig::exists('MODULE_SHIPPING_FREE_SHOW_TEXT')){
	 /*Free shipping add*/
		require(DIR_WS_MODULES . 'shipping/freeshipping.php');
		$freeShipping = new freeshipping();

	    	$quotes = $freeShipping->quote('check');
			if ($quotes['methods'][0]['title'] == 'none'){
				$div = htmlBase::newElement('div')
						->html('<b style="margin-top:7px;">' . sprintf('Add %s to cart for free shipping', $currencies->format(((float)sysConfig::get('MODULE_SHIPPING_FREE_AMOUNT') - $ShoppingCart->showTotal()))) . '</b>');

				if (sysConfig::get('MODULE_SHIPPING_FREE_SHOW_TEXT') == 'True'  && $freeShipping->enabled){
				?>
				<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar" style="text-align:right;padding:.8em;margin-top:.5em;">
				<?php
					echo $div->draw();
					?>
					</div>
					<div style="clear:both;"></div>
<?php
				}
			}
	}
   /*End */
	?>
	<?php
	if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'true' && !Session::exists('agreed_terms')){
	?>
			<script type="text/javascript">
				$(document).ready(function (){
					var errMsg;
					$('.checkoutFormButton').click(function(){

	                	<?php
				            if(sysConfig::get('TERMS_INITIALS') == 'false'){
								echo 'return popupWindowInitials(\'' . itw_app_link('action=getTerms', 'shoppingCart', 'default') . '\',false,300,300);';
		                    }else{
			                    echo 'return popupWindowInitials(\'' . itw_app_link('action=getTerms', 'shoppingCart', 'default') . '\',true,300,300);';
		                    }
						?>
					});
				});
			</script>
<?php
	}
	
	$pageButtons = htmlBase::newElement('button')
     ->setName('update_product')
     ->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_CART'))
     ->setType('submit')
     ->draw();

     $back = sizeof($navigation->path)-2;

     $checkoutFormButton = htmlBase::newElement('button')
     ->setText(sysLanguage::get('TEXT_BUTTON_CHECKOUT'))
     ->setName('checkout')
     ->setType('submit');
	 if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'true'){    
		 $checkoutFormButton->addClass('checkoutFormButton');
	 }
     $pageButtons .= $checkoutFormButton->draw();
} else {
	$div = htmlBase::newElement('div')
	->addClass('ui-widget ui-widget-content ui-corner-all')
	->html(sysLanguage::get('TEXT_CART_EMPTY'))
	->css(array(
	'margin-top' => '1em',
	'text-align' => 'center',
	'padding' => '2em'
	));

	echo $div->draw();
	
	$pageButtons = htmlBase::newElement('button')
     ->usePreset('continue')
     ->setHref(itw_app_link(null, 'index', 'default'))
     ->draw();
}
	$contents = EventManager::notifyWithReturn('ShoppingCartAfterListing');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}

	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageForm', array(
		'name' => 'cart_quantity',
		'action' => itw_app_link(null, 'shoppingCart', 'default'),
		'method' => 'post'
	));
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
