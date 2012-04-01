<?php
ob_start();
$addressType = (isset($_POST['addressType']) ? $_POST['addressType'] : $_GET['addressType']);
?>
<div id="addressBook" data-role="page" style="background: url(/et_video/templates/moviestore/images/body_bg.png)">
	<div data-role="header">
		<h1>My Address Book</h1>
	</div>
	<div data-role="content">
		<fieldset data-role="controlgroup">
			<legend>Select An Address To Use</legend>
			<?php
			$checked = ($addressType == 'shipping' ? $onePageCheckout->onePage['deliveryAddressId'] : $onePageCheckout->onePage['billingAddressId']);
			$Qaddress = Doctrine_Query::create()
				->from('AddressBook')
				->where('customers_id = ?', (int)$userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qaddress){
				foreach($Qaddress as $aInfo){
					$format_id = tep_get_address_format_id($aInfo['entry_country_id']);
					echo htmlBase::newElement('radio')
						->setId('address_' . $aInfo['address_book_id'])
						->setName('address')
						->setValue($aInfo['address_book_id'])
						->setChecked(($aInfo['address_book_id'] == $checked))
						->setLabel($aInfo['entry_firstname'] . ' ' . $aInfo['entry_lastname'] .
						'<p style="font-size:.8em">' . tep_address_format($format_id, $aInfo, true, ' ', '<br>') . '</p>')
						->setLabelPosition('after')
						->draw();
				}
			}
			?>
		</fieldset>
	</div>
	<div data-role="footer">
		<div class="ui-bar" style="text-align:center;">
			<a href="#" data-rel="back" data-role="button" data-icon="check" id="selectAddress">Use Selected Address</a>
		</div>
	</div>
</div>
<?php
$html = ob_get_contents();
ob_end_clean();

EventManager::attachActionResponse($html, 'html');
?>