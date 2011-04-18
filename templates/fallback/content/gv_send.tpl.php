<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if ($action == 'process') {
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><?php echo sysLanguage::get('TEXT_SUCCESS'); ?><br /><br /><?php echo 'gv '.$id1; ?></div>
<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar"><a href="<?php echo itw_app_link(null, 'index', 'default', 'NONSSL'); ?>"><?php echo htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw(); ?></a></div>
<?php
  }
  
  if ($action == 'send' && $error === false){
    // validate entries
      $gv_amount = (double) $gv_amount;
      $send_name = $userAccount->getFullName();
?>
      <tr>
        <td><form action="<?php echo itw_app_link('action=process', 'gv_send', 'default', 'NONSSL'); ?>" method="post"><div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo sprintf(MAIN_MESSAGE, $currencies->format($_POST['amount']), stripslashes($_POST['to_name']), $_POST['email'], stripslashes($_POST['to_name']), $currencies->format($_POST['amount']), $send_name); ?></td>
          </tr>
<?php
      if ($_POST['message']) {
?>
           <tr>
            <td class="main"><?php echo sprintf(PERSONAL_MESSAGE, $userAccount->getFirstName()); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo stripslashes($_POST['message']); ?></td>
          </tr>
<?php
      }
?>
         </table></div>
<?php
      echo tep_draw_hidden_field('send_name', $send_name) . 
           tep_draw_hidden_field('to_name', stripslashes($_POST['to_name'])) . 
           tep_draw_hidden_field('email', $_POST['email']) . 
           tep_draw_hidden_field('amount', $gv_amount) . 
           tep_draw_hidden_field('message', stripslashes($_POST['message']));
?>
<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar"><?php
	echo htmlBase::newElement('button')->setType('submit')->usePreset('continue')->setText(sysLanguage::get('TEXT_BUTTON_SEND'))->draw();
	
	echo htmlBase::newElement('button')
	->usePreset('back')
	->setType('submit')
	->setName('back')
	->css(array(
		'float' => 'left'
	))
	->draw();
?></div>
</form></td>
      </tr>
<?php
  } elseif ($action == '' || (isset($error) && $error === true)){
  	$toNameInput = htmlBase::newElement('input')->setName('to_name');
  	$toEmailInput = htmlBase::newElement('input')->setName('email');
  	$amountInput = htmlBase::newElement('input')->setName('amount');
  	$messageInput = htmlBase::newElement('textarea')->setName('message')->attr('cols', '50')->attr('rows', '15');
  	
  	if (isset($_POST) && !empty($_POST)){
 	 	$toNameInput->setValue($_POST['to_name']);
 	 	$toEmailInput->setValue($_POST['email']);
 	 	$amountInput->setValue($_POST['amount']);
 	 	$messageInput->html($_POST['message']);
  	}
?>
      <tr>
        <td class="main"><?php echo HEADING_TEXT; ?></td>
      </tr>
      <tr>
        <td><form action="<?php echo itw_app_link('action=send', 'gv_send', 'default', 'NONSSL'); ?>" method="post"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_NAME'); ?><br><?php echo $toNameInput->draw();?></td>
          </tr>
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL'); ?><br><?php echo $toEmailInput->draw(); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_AMOUNT; ?><br><?php echo $amountInput->draw(); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ENTRY_MESSAGE; ?><br><?php echo $messageInput->draw(); ?></td>
          </tr>
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
<?php
    $back = sizeof($navigation->path)-2;
?>
            <td class="main"><?php echo htmlBase::newElement('button')->usePreset('back')->setHref(tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']))->draw(); ?></td>
            <td class="main" align="right"><?php echo htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw(); ?></td>
          </tr>
        </table></form></td>
      </tr>
<?php
  }
?>
    </table>