<div><?php
 echo sysLanguage::get('TEXT_NEW_CUSTOMER') . '<br><br>' . sprintf(sysLanguage::get('TEXT_NEW_CUSTOMER_INTRODUCTION'), sysConfig::get('STORE_NAME')) . '<br>';
?></div>
<div style="text-align:right"><br /><?php
 $newAccountButton = htmlBase::newElement('button')
 ->setText(sysLanguage::get('IMAGE_BUTTON_CONTINUE'))
 ->setHref(itw_app_link(null, 'account', 'create', 'SSL'))
 ->setIcon('circleTriangleEast');

 echo $newAccountButton->draw();
?></div>