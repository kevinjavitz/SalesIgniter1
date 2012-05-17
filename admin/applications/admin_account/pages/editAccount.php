<?php
	
	$Qaccount = Doctrine_Query::create()
	->select('admin_id, admin_firstname, admin_lastname, admin_email_address')
	->from('Admin')
	->where('admin_id = ?', Session::get('login_id'));
	
	$Account = $Qaccount->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
	$Qaccount->free();
	unset($Qaccount);
	
	$adminId = $Account['admin_id'];
	$adminFirstname = $Account['admin_firstname'];
	$adminLastname = $Account['admin_lastname'];
	$adminEmailAddress = $Account['admin_email_address'];

	if (Session::exists('confirm_account') === true) {
		Session::remove('confirm_account');
	}
	
	$infoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_INFO_FIRSTNAME')),
			array('addCls' => 'main', 'text' => tep_draw_input_field('admin_firstname', $adminFirstname))
		)
	));
	
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_INFO_LASTNAME')),
			array('addCls' => 'main', 'text' => tep_draw_input_field('admin_lastname', $adminLastname))
		)
	));
	
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_INFO_EMAIL')),
			array('addCls' => 'main', 'text' => tep_draw_input_field('admin_email_address', $adminEmailAddress))
		)
	));
	
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_INFO_PASSWORD')),
			array('addCls' => 'main', 'text' => tep_draw_password_field('admin_password'))
		)
	));
	
	$infoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_INFO_PASSWORD_CONFIRM')),
			array('addCls' => 'main', 'text' => tep_draw_password_field('admin_password_confirm'))
		)
	));
?>
 <form name="account" action="<?php echo itw_app_link('action=saveAccount');?>" method="post">
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:100%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $infoTable->draw();?>
   </div>
  </div>
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
     <td align="right"><?php
      $backButton = htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(null, null, 'default', 'SSL'));
      echo $backButton->draw();

      $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
      echo $saveButton->draw() . tep_draw_hidden_field('id_info', $adminId);
     ?></td>
    </tr>
   </table>
 </div>
 </form>