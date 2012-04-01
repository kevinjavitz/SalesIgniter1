<?php
ob_start();
//echo '<pre>';print_r($ShoppingCart);echo '</pre>';
if ($ShoppingCart->hasContents()) {
	$tableListing = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->attr('width', '100%')
		->stripeRows('ui-bar-c', 'ui-bar-d');

	$shoppingCartHeader = array(
		array('addCls' => '', 'text' => sysLanguage::get('TABLE_HEADING_REMOVE'), 'align' => 'center'),
		array('addCls' => '', 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS'), 'align' => 'left'),
		array('addCls' => '', 'text' => sysLanguage::get('TABLE_HEADING_QUANTITY'), 'align' => 'center'),
		array('addCls' => '', 'text' => sysLanguage::get('TABLE_HEADING_TOTAL'), 'align' => 'right'),
	);

	EventManager::notify('ShoppingCartListingAddHeaderColumn', &$shoppingCartHeader);

	$tableListing->addHeaderRow(array(
		'addCls' => 'ui-bar-b',
		'columns' => $shoppingCartHeader
	));

	$any_out_of_stock = 0;
	foreach($ShoppingCart->getProducts() as $cartProduct) {
		$hashId = $cartProduct->getId();
		$purchaseQuantity = $cartProduct->getQuantity();

		$products_name = $cartProduct->getNameHtml();
		$qty = tep_draw_hidden_field('cart_id[]', $hashId);
		$qty .= $cartProduct->getCartQuantityHtml();

		$shoppingCartBodyRow = array(
			array(
				'addCls' => 'productListing-data',
				'text' => '<a class="removeFromCart" href="' . itw_app_link('rType=ajax&action=removeCartProduct&products_id=' . $hashId, 'mobile', 'shoppingCart') . '" data-role="button" data-icon="delete" data-iconpos="notext">Remove From Cart</a>',
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
				'text' => '<b>' . $cartProduct->displayFinalPrice((sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true'), true) . '</b>',
				'attr' => array('align' => 'right', 'valign' => 'top')
			)
		);

		EventManager::notify('ShoppingCartListingAddNewBodyColumn',&$shoppingCartBodyRow, $cartProduct);

		$tableListing->addBodyRow(array(
			'columns' => $shoppingCartBodyRow
		));
	}
	$div = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all');

	EventManager::notify('ShoppingCartListingBeforeListing', &$div);

	$div->append($tableListing);

	EventManager::notify('ShoppingCartListingAfterListing', &$div);

	$div->css(array(
		'text-align' => 'center'
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
		//->attr('data-inline', true)
		->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_CART'))
		->setType('submit')
		->draw();

	$checkoutFormButton = htmlBase::newElement('button')
		//->attr('data-inline', true)
		->setText(sysLanguage::get('TEXT_BUTTON_CHECKOUT'))
		->setHref(itw_app_link(null, 'mobile','checkout', 'SSL'));
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
	'action' => itw_app_link('action=update_product', 'shoppingCart', 'default'),
	'method' => 'post'
));
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
