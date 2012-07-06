<form name="login" action="<?php echo itw_app_link('action=processLogin');?>" method="post">
    <div class="ui-widget ui-widget-content ui-corner-all loginBox" style="border-color:#222222;width:400px;margin-left:auto;margin-right:auto;">
        <div id="logoBar" class="dummy" style="text-align:center;height: 80px;line-height:80px">
            <img src="<?php echo sysConfig::getDirWsAdmin();?>images/seslogo.png" style="vertical-align: middle;">
        </div>
        <div class="ui-widget-body ui-corner-all" style="margin:10px; font-size: 1em;">
            <?php echo sysLanguage::get('ENTRY_LOGIN_EMAIL_ADDRESS'); ?><br>
            <?php echo htmlBase::newElement('input')->setRequired(true)->setName('email_address')->css('width', '370px')->draw(); ?>
            <br><br>
            <?php echo sysLanguage::get('ENTRY_LOGIN_PASSWORD'); ?><br>
            <?php echo htmlBase::newElement('input')->setType('password')->addClass('password')->setName('password')->setRequired(true)->css('width', '370px')->draw(); ?><br><br>
            <?php echo '<a href="' . itw_app_link(null, null, 'forgotten') . '">' . sysLanguage::get('TEXT_PASSWORD_FORGOTTEN') . '</a>'; ?>
        </div>
        <div class="ui-widget-footer" style="margin:10px;text-align:center;">
            <button id="loginButton" type="submit" style="width:150px;"><span>Login</span></button>
        </div>
    </div>
</form>