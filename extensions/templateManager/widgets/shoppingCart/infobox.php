<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxShoppingCart extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('shoppingCart');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SHOPPINGCART'));
		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link(null, 'shoppingCart', 'default'));
		}

	}

	public function show(){
		global $ShoppingCart, $currencies;

		if ($ShoppingCart->countContents() > 0){
			$productAdded = false;
			if (Session::exists('new_products_id_in_cart') === true){
				$productAdded = true;
				$newProductId = Session::exists('new_products_id_in_cart');
				Session::remove('new_products_id_in_cart');
			}

			$boxContent = htmlBase::newElement('table')->css('width', '100%')->setCellPadding(2)->setCellSpacing(0);

			foreach($ShoppingCart->getProducts() as $cartProduct){
				$pID_string = $cartProduct->getIdString();

				$quantity = htmlBase::newElement('span')
				->html($cartProduct->getQuantity() . '&nbsp;x&nbsp;');

				$productName = htmlBase::newElement('a')
				->setHref(itw_app_link('products_id=' . $pID_string, 'product', 'info'));

				$deleteIcon = htmlBase::newElement('icon')->changeElement('a')
				->setType('circleClose')
				->addClass('iconDeleteFromCart');

				$deleteIcon->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'action=removeCartProduct&pID=' . $cartProduct->getUniqID().'&purchaseTypeVal='.$cartProduct->getPurchaseType()));

				if ($productAdded === true && $newProductId == $pID_string){
					$quantity->addClass('newItemInCart');

					$productName->append(htmlBase::newElement('span')->html($cartProduct->productClass->getName()));
				}else{
					$productName->html($cartProduct->productClass->getName());
				}

				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => $quantity),
						array('addCls' => 'main', 'text' => $productName),
						array('addCls' => 'main ui-icon-red', 'text' => $deleteIcon)
					)
				));
			}
			$htmlButton = htmlBase::newElement('a')
						->setHref(itw_app_link(null, 'checkout', 'default', 'SSL'))
						->attr('id','infoboxCheckout')
						->addClass('checkoutFormButton')
						->html(sysLanguage::get('INFOBOX_SHOPPINGCART_CHECKOUT'));

			$boxContent->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'colspan' => '3', 'text' => tep_draw_separator())
				)
			));

			$boxContent->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'colspan' => '3',
						'align' => 'right',
						'text' => $currencies->format($ShoppingCart->showTotal())
					)
				)
			));

			EventManager::notify('InfoBoxShoppingCartBeforeDraw', &$boxContent);
			$this->addGiftVoucher(&$boxContent);
			$this->addCouponInfo(&$boxContent);
          	$htmlContent = $boxContent->draw() . '<br/>' . $htmlButton->draw();
		}else{
			$boxContent = htmlBase::newElement('span')->html(sysLanguage::get('INFOBOX_SHOPPINGCART_EMPTY'));
            $htmlContent = $boxContent->draw();
		}

		$this->setBoxContent($htmlContent);

		return $this->draw();
	}

	public function addCouponInfo(&$boxContent){
		if (Session::exists('cc_id') === true && Session::get('cc_id') > 0) {
			$Qcoupon = Doctrine_Query::create()
			->from('Coupons c')
			->leftJoin('c.CouponsDescription cd')
			->where('c.coupon_id = ?', Session::get('cc_id'))
			->andWhere('cd.language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (count($Qcoupon)){
				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => '3', 'text' => tep_draw_separator())
					)
				));

				$boxContent->addBodyRow(array(
					'columns' => array(
						array(
							'addCls' => 'main',
							'colspan' => '3',
							'text' => sysLanguage::get('INFOBOX_SHOPPINGCART_COUPON') . $Qcoupon[0]['CouponsDescription'][0]['coupon_name']
						)
					)
				));
			}
		}
	}

	public function addGiftVoucher(&$boxContent){
		global $currencies;
		if (sysConfig::get('MODULE_ORDER_TOTAL_GV_STATUS') != 'true') return;

		$userAccount =& Session::getReference('userAccount');
		if ($userAccount->isLoggedIn() === true){
			require('includes/modules/order_total/ot_gv.php');
			$giftVoucher = new ot_gv;

			$voucherAvailableAmount = $giftVoucher->getCustomerGvAmount();
			if ($voucherAvailableAmount > 0){
				$voucherBalance = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
				$voucherBalance->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => sysLanguage::get('INFOBOX_SHOPPINGCART_VOUCHER_BALANCE')),
						array('addCls' => 'main', 'text' => $currencies->format($voucherAvailableAmount))
					)
				));
				$voucherBalance->addBodyRow(array(
					'columns' => array(
						array(
							'addCls' => 'main',
							'colspan' => '2',
							'text' => '<a href="'. itw_app_link(null, 'gv_send', 'default') . '">' . sysLanguage::get('INFOBOX_SHOPPINGCART_SEND_TO_FRIEND') . '</a>'
						)
					)
				));

				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => '3', 'text' => tep_draw_separator())
					)
				));

				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => '3', 'text' => $voucherBalance)
					)
				));
			}
		}

		if (Session::exists('gv_id') === true){
			$Qcoupon = Doctrine_Query::create()
			->select('coupon_amount')
			->from('Coupons')
			->where('coupon_id = ?', Session::get('gv_id'))
			->fetchOne();
			if ($Qcoupon){
				$couponAmount = htmlBase::newElement('table')->setCellPadding(2)->setCellSpacing(0);
				$couponAmount->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => sysLanguage::get('INFOBOX_SHOPPINGCART_VOUCHER_REDEEMED')),
						array('addCls' => 'main', 'text' => $currencies->format($Qcoupon['coupon_amount']))
					)
				));

				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => '3', 'text' => tep_draw_separator())
					)
				));

				$boxContent->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'colspan' => '3', 'text' => $couponAmount)
					)
				));
			}
		}
	}
}
?>