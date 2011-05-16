<?php
	ob_start();
?>
<noscript><?php
echo tep_get_pages_content(13);
?></noscript>
<style>
.pstrength-minchar {
    font-size : 10px;
}
</style>
<script type="text/javascript">
	var CONTINUE_TO_HOMEPAGE = '<?php echo sysLanguage::get('TEXT_CONTINUE_TO_HOMEPAGE')?>';
</script>
	<div class="checkoutContent">
	<?php
		ob_start();
		require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/addresses.php');
		$pageHtml = ob_get_contents();
		ob_end_clean();
		echo $pageHtml;
	?>
	</div>
<br>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:.3em;"><table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
		<td class="main" id="checkoutMessage"><?php echo '<b>' . sysLanguage::get('TITLE_CONTINUE_CHECKOUT_PROCEDURE') . '</b><br>' . sysLanguage::get('TEXT_CONTINUE_CHECKOUT_PROCEDURE'); ?></td>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
		<?php if (sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'false'){?>
		<td class="main"><div id="agreeMessage"><?php echo tep_draw_checkbox_field('terms', '1', false) . '&nbsp;<a href="' . itw_app_link('appExt=infoPages', 'show_page', 'conditions') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'conditions', 'SSL') . '\',\'800\',\'600\');return false;">' . sysLanguage::get('TEXT_AGREE_TO_TERMS') . '</a>';?></div></td>
		<?php }else{ ?>
		<td class="main" style="display:none;"> <?php echo tep_draw_checkbox_field('terms', '1', true);?></td>
		<?php } ?>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
		<td class="main" align="right"><?php
			echo htmlBase::newElement('button')
			->setType('submit')
			->usePreset('continue')
			->setId('continueButton')
			->setName('continueButton')
			->draw();
		?></td>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
	</tr>
</table></div>
<br>
<div id="bar_step1">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%" align="right"><span class="ui-icon ui-icon-green ui-icon-bullet" style="vertical-align:middle;"></span></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
			<td width="50%" align="center"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="1" height="5" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td align="center" width="25%" class="checkoutBarCurrent"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></td>
			<td align="center" width="50%" class="checkoutBarTo"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING');?></td>
			<td align="center" width="25%" class="checkoutBarTo"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></td>
		</tr>
	</table>
</div>
<div id="bar_step2" style="display:none">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%" align="right"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="1" height="5" style="vertical-align:middle;"></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
			<td width="50%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
					<td align="center"><span class="ui-icon ui-icon-green ui-icon-bullet" style="vertical-align:middle;"></span></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="1" height="5" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td align="center" width="25%" class="checkoutBarFrom"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></td>
			<td align="center" width="50%" class="checkoutBarCurrent"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING');?></td>
			<td align="center" width="25%" class="checkoutBarTo"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></td>
		</tr>
	</table>
</div>
<div id="bar_step3" style="display:none">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td align="right"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="1" height="5" style="vertical-align:middle;"></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
			<td width="50%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
				</tr>
			</table></td>
			<td width="25%" align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%"><img src="<?php echo sysConfig::getDirWsCatalog();?>images/pixel_silver.gif" width="100%" height="1" style="vertical-align:middle;"></td>
					<td align="left"><span class="ui-icon ui-icon-green ui-icon-bullet" style="vertical-align:middle;"></span></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td align="center" width="25%" class="checkoutBarFrom"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></td>
			<td align="center" width="50%" class="checkoutBarFrom"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING');?></td>
			<td align="center" width="25%" class="checkoutBarCurrent"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></td>
		</tr>
	</table>
</div>
	<script type="text/javascript">

			var TEXT_CONFIRM_ORDER = '<?php echo sysLanguage::get('TEXT_CONFIRM_ORDER');?>';

	</script>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageContent->set('pageForm', array(
		'name' => 'checkout',
		'action' => 'Javascript:void(0)',
		'method' => 'post'
	));
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageContent', $pageContents);
?>