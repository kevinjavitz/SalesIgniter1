<?php
    $SupplierName = htmlBase::newElement('input')
    ->setName('suppliers_name');

    $SupplierAddress = htmlBase::newElement('input')
    ->setName('suppliers_address');

    $SupplierPhone = htmlBase::newElement('input')
    ->setName('suppliers_phone');

    $SupplierWeb = htmlBase::newElement('input')
    ->setName('suppliers_website');

    $SupplierNotes = htmlBase::newElement('textarea')
    ->setName('suppliers_notes')
    ->addClass('makeFCK');
	
	if (isset($Supplier)){
        $SupplierName->setValue($Supplier['suppliers_name']);
        $SupplierAddress->setValue($Supplier['suppliers_address']);
        $SupplierPhone->setValue($Supplier['suppliers_phone']);
        $SupplierWeb->setValue($Supplier['suppliers_website']);
        $SupplierNotes->val($Supplier['suppliers_notes']);
	}
?>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SUPPLIER_NAME'); ?></td>
   <td class="main"><?php echo $SupplierName->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SUPPLIER_ADDRESS'); ?></td>
   <td class="main"><?php echo $SupplierAddress->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SUPPLIER_PHONE'); ?></td>
   <td class="main"><?php echo $SupplierPhone->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SUPPLIER_WEB'); ?></td>
   <td class="main"><?php echo $SupplierWeb->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SUPPLIER_NOTES'); ?></td>
   <td class="main"><?php echo $SupplierNotes->draw(); ?></td>
  </tr>
 </table>