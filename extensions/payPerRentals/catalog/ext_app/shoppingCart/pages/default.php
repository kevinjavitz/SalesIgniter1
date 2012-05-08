<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_catalog_shoppingCart_default extends Extension_payPerRentals {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
				'ShoppingCartAddFields',
				'ShoppingCartListingBeforeSubtotal'
		), null, $this);
	}

	public function ShoppingCartListingBeforeSubtotal(&$div){
		global $currencies, $appExtension;
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_TIME_FEES') == 'True'){
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled() === true){
				$QTimeFees = Doctrine_Query::create()
					->from('StoresTimeFees')
					->where('stores_id = ?', Session::get('current_store_id'))
					->orderBy('timefees_id')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}else{
				$QTimeFees = Doctrine_Query::create()
					->from('PayPerRentalTimeFees')
					->orderBy('timefees_id')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}
			$dataArr = array();
			foreach($QTimeFees as $timeFee){
				$dataArr[] = array(
					'label' => '<b>'.$timeFee['timefees_name'].'('.$currencies->format($timeFee['timefees_fee']).')'.'</b>',
					'value' => $timeFee['timefees_id'],
					'addCls' => 'timeFee',
					'labelPosition' => 'after'
				);
			}

			$optionTypeRadiosPickup = htmlBase::newElement('radio')
				->addGroup(array(
					'name' => 'pickup_time',
					'checked' => Session::exists('pickupFees_time')?Session::get('pickupFees_time'):1,
					'separator' => '<br />',
					'data' => $dataArr
			));

			$optionTypeRadiosDelivery = htmlBase::newElement('radio')
				->addGroup(array(
					'name' => 'delivery_time',
					'checked' => Session::exists('deliveryFees_time')?Session::get('deliveryFees_time'):1,
					'separator' => '<br />',
					'data' => $dataArr
				));

			$divText = htmlBase::newElement('div')
			->addClass('methodText')
			->html('Choose Pickup Time');
			$divRadio = htmlBase::newElement('div')
			->addClass('timefeesDiv');
			$divRadio->append($divText)->append($optionTypeRadiosPickup);

			$divTextDelivery = htmlBase::newElement('div')
				->addClass('methodText')
				->html('Choose Delivery Time');
			$divRadioDelivery = htmlBase::newElement('div')
				->addClass('timefeesDiv');
			$divRadioDelivery->append($divTextDelivery)->append($optionTypeRadiosDelivery);

			ob_start();
			?>
			<script type="text/javascript">
				$(document).ready(function(){
					$('.timeFee').click(function(){
						$subtotalDiv = $('.subtotalDiv');
						showAjaxLoader($subtotalDiv, 'xlarge');
						$.ajax({
							cache: false,
							url: js_app_link('app=shoppingCart&appPage=default&action=selectFees'),
							type: 'post',
							data: $('.beforeSubtotal *').serialize(),
							dataType: 'json',
							success: function (data){
								hideAjaxLoader($subtotalDiv);
								$('.subtotalDiv').html(data.html);
							}
						});
					});
					$('.timeFee:checked').trigger('click');
				});
			</script>

			<?php
			$script = ob_get_contents();
			ob_end_clean();
			$divScript = htmlBase::newElement('div');
			$divScript->html($script);

			$div->append($divRadio)->append($divRadioDelivery)->append($divScript);
		}

		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_ENABLE_EXTRA_FEES') == 'True'){
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled() === true){
				$QTimeFees = Doctrine_Query::create()
					->from('StoresExtraFees')
					->where('timefees_mandatory = ?', '0')
					->andWhere('stores_id = ?', Session::get('current_store_id'))
					->orderBy('timefees_id')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}else{
				$QTimeFees = Doctrine_Query::create()
				->from('PayPerRentalExtraFees')
				->where('timefees_mandatory = ?', '0')
				->orderBy('timefees_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}

			$dataArr = array();
			$dataArr[] = array(
				'label' => '<b>'.'No Extra Fee'.'</b>',
				'value' => '0',
				'addCls' => 'extraFee',
				'labelPosition' => 'after'
			);
			foreach($QTimeFees as $timeFee){
				$dataArr[] = array(
					'label' => '<b>'.$timeFee['timefees_name'].'('.$currencies->format($timeFee['timefees_fee']).')'.'</b>',
					'value' => $timeFee['timefees_id'],
					'tooltip' => $timeFee['timefees_description'],
					'addCls' => 'extraFee',
					'labelPosition' => 'after'
				);
			}

			$optionTypeRadiosExtra = htmlBase::newElement('radio')
				->addGroup(array(
					'name' => 'extrafees_time',
					'checked' => Session::exists('extraFees_time')?Session::get('extraFees_time'):0,
					'separator' => '<br />',
					'data' => $dataArr
			));

			$divTextExtra = htmlBase::newElement('div')
				->addClass('methodText')
				->html('Choose Extra Fees');
			$divRadioExtra = htmlBase::newElement('div')
				->addClass('timefeesDiv');
			$divRadioExtra->append($divTextExtra)->append($optionTypeRadiosExtra);


			ob_start();
			?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.extraFee').click(function(){
					$subtotalDiv = $('.subtotalDiv');
					showAjaxLoader($subtotalDiv, 'xlarge');
					$.ajax({
						cache: false,
						url: js_app_link('app=shoppingCart&appPage=default&action=selectFees'),
						type: 'post',
						data: $('.beforeSubtotal *').serialize(),
						dataType: 'json',
						success: function (data){
							hideAjaxLoader($subtotalDiv);
							$('.subtotalDiv').html(data.html);
						}
					});
				});

				$('.extraFee').each(function(){
					$(this).next().attr('tooltip', $(this).attr('tooltip'));
				});
				$('.extraFee:checked').trigger('click');
			});
		</script>

		<?php
  			$script = ob_get_contents();
			ob_end_clean();
			$divScriptExtra = htmlBase::newElement('div');
			$divScriptExtra->html($script);

			$div->append($divRadioExtra)->append($divScriptExtra);
		}
	}
	
	public function ShoppingCartAddFields(&$qty, $purchaseType, $cartProduct){
		global $ShoppingCart;
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHANGE_DATES_BUTTON') == 'True' && Session::exists('isppr_selected') && Session::get('isppr_selected') == true && $purchaseType == 'reservation'){
			$pInfo = $cartProduct->getInfo('reservationInfo');
			$startDate = htmlBase::newElement('input')
			->setType('hidden')
			->addClass('start_date_shop')
			->setName('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.'][start_date]')
			->setValue($pInfo['start_date']);
			$endDate = htmlBase::newElement('input')
				->setType('hidden')
				->addClass('end_date_shop')
				->setName('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.'][end_date]')
				->setValue($pInfo['end_date']);
			$qty .= $startDate->draw().$endDate->draw();

		}
	}        
}
?>