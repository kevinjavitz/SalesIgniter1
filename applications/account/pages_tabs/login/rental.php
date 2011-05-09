<div><?php
 echo sysLanguage::get('TEXT_NEW_RENTAL_CUSTOMER') . '<br><br>' . sprintf(sysLanguage::get('TEXT_NEW_RENTAL_CUSTOMER_INTRODUCTION'), sysConfig::get('STORE_NAME')) . '<br>';
?></div>
<div style="text-align:right"><br /><?php
 $rentalAccountButton = htmlBase::newElement('button')
 ->setText(sysLanguage::get('IMAGE_BUTTON_CONTINUE'))
 ->setHref(itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL'))
 ->setIcon('circleTriangleEast');
 
 echo $rentalAccountButton->draw();
?></div>