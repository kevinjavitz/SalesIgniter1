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



		//if ($purchaseType == 'reservation'){
		$qty = tep_draw_input_field('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.'][quantity]', $purchaseQuantity, 'size="4"');
		EventManager::notify('ShoppingCartAddFields',&$qty, $purchaseType, $cartProduct);
		//}else{
		//	$qty = tep_draw_input_field('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.']', $purchaseQuantity, 'size="4"');
		//}

		$shoppingCartBodyRow = array(
			array(
				'addCls' => 'productListing-data',
				'text' => tep_draw_checkbox_field('cart_delete['.$cartProduct->getUniqID() .']', $purchaseType),
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

	$divShipInfo = htmlBase::newElement('a')
	->html('Shipping Information?')
	->addClass('shipInfo');
	
	$div->append($tableListing);

	EventManager::notify('ShoppingCartListingAfterListing', &$div);
	
	$div->css(array(
		'margin-top' => '1em',
		'text-align' => 'center',
		'padding' => '.5em'
	));

	$PageForm = htmlBase::newElement('form')
	->attr('name','cart_quantity')
	->attr('action', itw_app_link(null, 'shoppingCart', 'default'))
	->attr('method','post');

	$pageButtonsHtml = htmlBase::newElement('button')
     ->setName('update_product')
	 ->addClass('updateProductButton')
     ->setText(sysLanguage::get('TEXT_BUTTON_UPDATE_CART'))
     ->setType('submit');

	$link = itw_app_link(null,'products','all');
	$lastPath = $navigation->getPath(0);
	if ($lastPath){
		$getVars = array();
		if (is_array($lastPath['get'])){
			foreach($lastPath['get'] as $k => $v){
				if($k == 'app' || $k == 'appPage')
					continue;
				$getVars[] = $k . '=' . $v;
			}
		}else{
			$getVars[] = $lastPath['get'];
		}

		$link = itw_app_link(implode('&', $getVars), $lastPath['app'], $lastPath['appPage'], $lastPath['mode']);
	}

	$continueButtonHtml = htmlBase::newElement('button')
		->setName('continue')
		->setText(sysLanguage::get('TEXT_BUTTON_CONTINUE_CART'))
		->setHref($link);

	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHANGE_DATES_BUTTON') == 'True'){
		$changeDatesButtonHtml = htmlBase::newElement('button')
		->setName('changeDates')
		->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_DATES'))
		->addClass('changeDatesButton');
		ob_start();
		?>
	<script type="text/javascript">
		function nobeforeDays(date){
			today = new Date();
			if(today.getTime() <= date.getTime() - (1000 * 60 * 60 * 24 * <?php echo $datePadding;?> - (24 - date.getHours()) * 1000 * 60 * 60)){
				return [true,''];
			}else{
				return [false,''];
			}
		}
		function makeDatePicker(pickerID){
			var minRentalDays = <?php
                                if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'True'){
				echo (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
				$minDays = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS');
			}else{
				$minDays = 0;
				echo '0';
			}
				if(Session::exists('button_text')){
					$butText = Session::get('button_text');
				}else{
					$butText = '';
				}
				?>;
			var selectedDateId = null;
			var startSelectedDate;

			var dates = $(pickerID+' .dstart,'+pickerID+' .dend').datepicker({
				dateFormat: '<?php echo getJsDateFormat(); ?>',
				changeMonth: true,
				beforeShowDay: nobeforeDays,
				onSelect: function(selectedDate) {

					var option = this.id == "dstart" ? "minDate" : "maxDate";
					if($(this).hasClass('dstart')){
						myid = "dstart";
						option = "minDate";
					}else{
						myid = "dend";
						option = "maxDate";
					}
					var instance = $(this).data("datepicker");
					var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);

					var dateC = new Date('<?php echo (Session::exists('isppr_curDate')?Session::get('isppr_curDate'):'01-01-2011');?>');
					if(date.getTime() == dateC.getTime()){
						if(myid == "dstart"){
							$(this).closest('form').find('.hstart').html('<?php echo (Session::exists('isppr_selectOptionscurdays')?Session::get('isppr_selectOptionscurdays'):'');?>');
						}else{
							$(this).closest('form').find('.hend').html('<?php echo (Session::exists('isppr_selectOptionscurdaye')?Session::get('isppr_selectOptionscurdaye'):'');?>');
						}
					}else{
						if(myid == "dstart"){
							$(this).closest('form').find('.hstart').html('<?php echo (Session::exists('isppr_selectOptionsnormaldays')?Session::get('isppr_selectOptionsnormaldays'):'');?>');
						}else{
							$(this).closest('form').find('.hend').html('<?php echo (Session::exists('isppr_selectOptionsnormaldaye')?Session::get('isppr_selectOptionsnormaldaye'):'');?>');
						}
					}


					if(myid == "dstart"){
						var days = "0";
						if ($(this).closest('form').find('select.pickupz option:selected').attr('days')){
							days = $(this).closest('form').find('select.pickupz option:selected').attr('days');
						}
						//startSelectedDate = new Date(selectedDate);
						dateFut = new Date(date.setDate(date.getDate() + parseInt(days)));
						dates.not(this).datepicker("option", option, dateFut);
					}
					f = true;
					if(myid == "dend"){
						datest = new Date(selectedDate);
						if ($(this).closest('form').find('.dstart').val() != ''){
							startSelectedDate = new Date($(this).closest('form').find('.dstart').val());
							if (datest.getTime() - startSelectedDate.getTime() < minRentalDays *24*60*60*1000){
								alert('<?php echo sprintf(sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ERROR_MIN_DAYS'), $minDays);?>');
								$(this).val('');
								f = false;
							}
						}else{
							f = false;
						}
					}

					if (selectedDateId != this.id && selectedDateId != null && f){
						selectedDateId = null;
					}
					if (f){
						selectedDateId = this.id;
					}

				}
			});
		}
		$(document).ready(function (){
			$('.changeDatesButton').click(function(){

				$( '<div id="dialog-mesage" title="Choose Dates"><input class="tField" name="tField" ><div class="destBD"><span class="start_text">Start: </span><input class="picker dstart" name="dstart" ></div><div class="destBD"><span class="end_text">End: </span><input class="picker dend" name="dend" ></div><?php sysConfig::get('EXTENSION_PAY_PER_RENTALS_INFOBOX_CONTENT');?></div>' ).dialog({
					modal: false,
					autoOpen: true,
					open: function (e, ui){
						makeDatePicker('#dialog-mesage');
						$(this).find('.tField').hide();
					},
					buttons: {
						Submit: function() {

							$('.start_date_shop').val($(this).find('.dstart').val());
							$('.end_date_shop').val($(this).find('.dend').val());
							$('.updateProductButton').trigger('click');
							$(this).dialog( "close" );
						}
					}
				});

				return false;
			});

		});
	</script>
	                            <?php
 	  					$script = ob_get_contents();
		ob_end_clean();
		$divScript = htmlBase::newElement('div')
		->html($script);
	}

     $checkoutFormButton = htmlBase::newElement('button')
     ->setText(sysLanguage::get('TEXT_BUTTON_CHECKOUT'))
     ->setHref(itw_app_link(null, 'checkout','default','SSL'));
	 if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'true'){
		 $checkoutFormButton->addClass('checkoutFormButton');
	 }
	$div3 = htmlBase::newElement('div');
	$div3->html('<div class="main" style="text-align:right;"><span class="smallText" style="float:left;"></span><b>'. sysLanguage::get('SUB_TITLE_SUB_TOTAL'). $currencies->format($ShoppingCart->showTotal()).'</b></div><div style="clear:both;"></div>');
?>
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
	$div2 = htmlBase::newElement('div')
	->addClass('shopButtons ui-widget-header ui-infobox-header ui-corner-all')
	->css(array(
		'margin-top' => '15px'
    ));
	$div2->append($pageButtonsHtml)->append($checkoutFormButton)->append($continueButtonHtml);
	if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHANGE_DATES_BUTTON') == 'True'){
		$div2->append($changeDatesButtonHtml)->append($divScript);
	}
	$div4 = htmlBase::newElement('div')
    ->css(array(
		'margin-bottom' => '10px'
    ));
	$PageForm->append($div)->append($div3)->append($div2)->append($div4);
	echo '<style type="text/css">.shopButtons a.ui-button-text-only .ui-button-text{ padding: .4em 1em;} .shopButtons .updateProductButton .ui-button-text{ padding: .4em 1em;}</style>'.$PageForm->draw();
	
	$pageButtons = '';
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
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
