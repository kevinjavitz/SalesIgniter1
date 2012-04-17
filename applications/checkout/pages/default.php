<?php
	ob_start();
?>
<div class="bar_step1">
	<div class="bar1">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>

</div>
<div class="bar_step2" style="display:none">
	<div class="bar2">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>
</div>
<div class="bar_step3" style="display:none">
	<div class="bar3">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>
</div>
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
		<?php if (sysConfig::get('TERMS_CONDITIONS_CHECKOUT') == 'true' && sysConfig::get('TERMS_CONDITIONS_SHOPPING_CART') == 'false'){?>
		<td class="main"><div id="agreeMessage"><?php echo tep_draw_checkbox_field('terms', '1', false) . '&nbsp;<a href="' . itw_app_link('appExt=infoPages', 'show_page', 'conditions', 'SSL') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'conditions', 'SSL') . '\',\'800\',\'600\');return false;">' . sysLanguage::get('TEXT_AGREE_TO_TERMS') . '</a>';?></div></td>
		<?php }else{ ?>
		<td class="main" style="display:none;"> <?php echo tep_draw_checkbox_field('terms', '1', true);?></td>
		<?php } ?>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
		<td class="main" align="right"><?php
			echo htmlBase::newElement('button')
			->setType('submit')
			->usePreset('continue')
			->setText('TEXT_CONTINUE')
			->setId('continueButton')
			->setName('continueButton')
			->draw();
		?></td>
		<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
	</tr>
</table></div>
<br>
<div class="bar_step1">
	<div class="bar1">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>

</div>
<div class="bar_step2" style="display:none">
	<div class="bar2">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>
</div>
<div class="bar_step3" style="display:none">
	<div class="bar3">
		<div class="text1"><?php echo sysLanguage::get('TEXT_BEGIN'); ?></div>
		<div class="text2"><?php echo sysLanguage::get('TEXT_PAYMENT_SHIPPING'); ?></div>
		<div class="text3"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></div>
	</div>
</div>
	<script type="text/javascript">

			var TEXT_CONFIRM_ORDER = '<?php echo sysLanguage::get('TEXT_CONFIRM_ORDER');?>';

	</script>
	<style type="text/css">
		.bar1{
			background-image:url(<?php echo sysConfig::getDirWsCatalog();?>images/b1.png);
			width:388px;
			height:43px;
			background-repeat: no-repeat;
		}

		.bar2{
			background-image:url(<?php echo sysConfig::getDirWsCatalog();?>images/b2.png);
			width:388px;
			height:43px;
			background-repeat: no-repeat;
		}

		.bar3{
			background-image:url(<?php echo sysConfig::getDirWsCatalog();?>images/b3.png);
			width:388px;
			height:43px;
			background-repeat: no-repeat;
		}

		.text1{
			font-weight: bold;
			color:#7b7b7b;
			display:inline-block;
			margin-top:13px;
			margin-left:30px;
			margin-right:40px;
		}
		.text2{
			margin-top:13px;
			margin-left:30px;
			margin-right:40px;
			font-weight: bold;
			color:#7b7b7b;
			display:inline-block;
		}
		.text3{
			margin-top:13px;
			margin-left:20px;
			margin-right:20px;
			font-weight: bold;
			color:#7b7b7b;
			display:inline-block;
		}

		.bar_step1 .text1{
			color:#ffffff;
			font-weight: bold;
		}
		.bar_step2 .text2{
			color:#ffffff;
			font-weight: bold;
		}
		.bar_step3 .text3{
			color:#ffffff;
			font-weight: bold;
		}
	</style>
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