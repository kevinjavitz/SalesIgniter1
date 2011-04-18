<?php
	$GroupName = htmlBase::newElement('input')
	->setName('featured_group_name');

	$GroupNumber = htmlBase::newElement('input')
	->setName('featured_group_number_of_products');

	if (isset($Group)){
		$GroupName->setValue(stripslashes($Group['featured_group_name']));
		$GroupNumber->setValue(stripslashes($Group['featured_group_number_of_products']));
	}
?>

 <table cellpadding="0" cellspacing="0" border="0">

  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_NAME'); ?></td>
   <td class="main"><?php echo $GroupName->draw(); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_NUMBER'); ?></td>
   <td class="main"><?php echo $GroupNumber->draw(); ?></td>
  </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
   </tr>
 </table>
