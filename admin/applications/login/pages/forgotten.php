 <?php
 	if (Session::exists('password_forgotten_tries') === true && Session::get('password_forgotten_tries') >= 4){
 		echo sysLanguage::get('TEXT_FORGOTTEN_FAIL');
 		echo '<div style="text-align:right">' . 
 		      htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(null, null, 'default'))->draw() . 
 		     '</div>';
 	}else{
?> 		
 <div id="forgottenDialog" title="<?php echo htmlspecialchars('<div><span class="ui-icon-red ui-icon-locked" style="position:absolute;top:5px;left:5px;"></span><span style="position:relative;top:1px;left:10px;">' . sysLanguage::get('HEADING_PASSWORD_FORGOTTEN') . '</span></div>');?>" style="display:none;">
<form name="forgotten" action="<?php echo itw_app_link('action=getPassword');?>" method="post">
<?php
 		if (isset($info_message)){
 			echo $info_message;
 		}
 		echo tep_draw_hidden_field('log_times', (Session::exists('password_forgotten_tries') ? Session::get('password_forgotten_tries') : '1'));
 ?> 		
  <table cellpadding="3" cellspacing="0" border="0" align="center">
   <tr>
    <td class="main"><?php echo sysLanguage::get('ENTRY_FIRSTNAME'); ?></td>
    <td class="main"><?php echo tep_draw_input_field('firstname'); ?></td>
   </tr>
   <tr>
    <td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS'); ?></td>
    <td class="main"><?php echo tep_draw_input_field('email_address'); ?></td>
   </tr>
  </table>
 </form>
 </div>
<?php
 	}
?>