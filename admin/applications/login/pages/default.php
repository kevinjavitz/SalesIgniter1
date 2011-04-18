<form name="login" action="<?php echo itw_app_link('action=processLogin');?>" method="post">
 <div id="loginDialog" class="ui-inline-dialog-content ui-widget-content main" title="<?php echo htmlspecialchars('<div><span class="ui-icon-red ui-icon-locked" style="position:absolute;top:5px;left:5px;"></span><span style="position:relative;top:1px;left:10px;">' . sysLanguage::get('HEADING_RETURNING_ADMIN') . '</span></div>');?>" style="display:none;">
  <table cellpadding="3" cellspacing="0" border="0" align="center">
   <tr>
    <td class="main"><?php echo sysLanguage::get('ENTRY_LOGIN_EMAIL_ADDRESS'); ?></td>
    <td class="main"><?php echo tep_draw_input_field('email_address'); ?></td>
   </tr>
   <tr>
    <td class="main"><?php echo sysLanguage::get('ENTRY_LOGIN_PASSWORD'); ?></td>
    <td class="main"><?php echo tep_draw_password_field('password'); ?></td>
   </tr>
   <tr>
    <td valign="top" colspan="2" align="right"><?php echo '<a href="' . itw_app_link(null, null, 'forgotten') . '">' . sysLanguage::get('TEXT_PASSWORD_FORGOTTEN') . '</a>'; ?></td>
   </tr>
  </table>
 </div>
</form>