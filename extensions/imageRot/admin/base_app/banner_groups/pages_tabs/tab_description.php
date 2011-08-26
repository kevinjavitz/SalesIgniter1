<?php
$BannerGroupShowArrows = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_arrows'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_arrows',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupIsRotator = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_is_rotator'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_is_rotator',
	'data' => array(
		array(
			'label' => 'Yes',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'No',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupIsExpiring = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_is_expiring'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_is_expiring',
	'data' => array(
		array(
			'label' => 'Yes',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'No',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupShowNumbers = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_numbers'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_numbers',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupShowThumbnails = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_thumbnails'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_thumbnails',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupShowDescription = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_description'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_description',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoRotate = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_rotate'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_rotate',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Manual',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupShowCustom = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_custom'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_custom',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupShowThumbsDesc = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_show_thumbs_desc'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_show_thumbs_desc',
	'data' => array(
		array(
			'label' => 'Show',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Show',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupUseAutoResize = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_use_autoresize'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_use_autoresize',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'From HTML',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupUseThumbs = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_use_thumbs'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_use_thumbs',
	'data' => array(
		array(
			'label' => 'Use',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Use',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideNumbers = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_numbers'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_numbers',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideCustom = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_custom'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_custom',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideArrows = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_arrows'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_arrows',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideThumbs = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_thumbs'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_thumbs',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideThumbsDesc = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_thumbs_desc'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_thumbs_desc',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupAutoHideTitle = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_auto_hide_title'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_auto_hide_title',
	'data' => array(
		array(
			'label' => 'Auto',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Hide',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));

$BannerGroupHoverPause = htmlBase::newElement('radio')
->addGroup(array(
	'checked' => (isset($Group) ? $Group['banner_group_hover_pause'] : 0),
	'separator' => '<br />',
	'name' => 'banner_group_hover_pause',
	'data' => array(
		array(
			'label' => 'Pause',
			'labelPosition' => 'after',
			'value' => '1'
		),
		array(
			'label' => 'Don\'t Pause',
			'labelPosition' => 'after',
			'value' => '0'
		)
	)
));
?>
<table cellpadding="3" cellspacing="3" border="0" width="100%">
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_ARROWS'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_NUMBERS'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupShowArrows->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupShowNumbers->draw(); ?></td>
 </tr>
  <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_EXPIRING'); ?></u></b></td>
    <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_ROTATOR'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupIsExpiring->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupIsRotator->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_THUMBNAILS'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_DESCRIPTION'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupShowThumbnails->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupShowDescription->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_ROTATE'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_SHOW_CUSTOM'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupAutoRotate->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupShowCustom->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_SHOW_THUMBS_DESC'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_USE_AUTORESIZE'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupShowThumbsDesc->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupUseAutoResize->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_USE_THUMBS'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_NUMBERS'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupUseThumbs->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupAutoHideNumbers->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_ARROWS'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_CUSTOM'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupAutoHideArrows->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupAutoHideCustom->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_THUMBS'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_THUMBS_DESC'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupAutoHideThumbs->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupAutoHideThumbsDesc->draw(); ?></td>
 </tr>
 <tr>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_AUTO_HIDE_TITLE'); ?></u></b></td>
  <td class="main"><b><u><?php echo sysLanguage::get('TEXT_GROUP_HOVER_PAUSE'); ?></u></b></td>
 </tr>
 <tr>
  <td class="main"><?php echo $BannerGroupAutoHideTitle->draw(); ?></td>
  <td class="main"><?php echo $BannerGroupHoverPause->draw(); ?></td>
 </tr>
</table>
 <?php

		$GroupName = htmlBase::newElement('input')
		->setName('banner_group_name');

		$GroupTime = htmlBase::newElement('input')
		->setName('banner_group_time');

		$GroupEffect = htmlBase::newElement('selectbox')
		->setName('banner_group_effect');

		$GroupEffect->addOption('none', 'none');
		$GroupEffect->addOption('fade', 'fade');
		$GroupEffect->addOption('rain', 'rain');
		$GroupEffect->addOption('swirl', 'swirl');
		$GroupEffect->addOption('straight', 'straight');
		$GroupEffect->addOption('slideLeft', 'slideLeft');
		$GroupEffect->addOption('slideTop', 'slideTop');
		$GroupEffect->addOption('curtain', 'curtain');
		$GroupEffect->addOption('zipper', 'zipper');
		$GroupEffect->addOption('wave', 'wave');
		$GroupEffect->addOption('fountainTop', 'fountainTop');
		$GroupEffect->addOption('fountainAlternate', 'fountainAlternate');
		$GroupEffect->addOption('fountainBottom', 'fountainBottom');
		$GroupEffect->addOption('curtainAlternate', 'curtainAlternate');
		$GroupEffect->addOption('topLeft', 'topLeft');
		$GroupEffect->addOption('topRight', 'topRight');
		$GroupEffect->addOption('topRandom', 'topRandom');
		$GroupEffect->addOption('bottomLeft', 'bottomLeft');
		$GroupEffect->addOption('bottomRight', 'bottomRight');
		$GroupEffect->addOption('bottomRandom', 'bottomRandom');

		$GroupEffectTime = htmlBase::newElement('input')
		->setName('banner_group_effect_time');

		$GroupWidth = htmlBase::newElement('input')
		->setName('banner_group_width');

		$GroupHeight = htmlBase::newElement('input')
		->setName('banner_group_height');

		$GroupThumbsWidth = htmlBase::newElement('input')
		->setName('banner_group_thumbs_width');

		$GroupThumbsHeight = htmlBase::newElement('input')
		->setName('banner_group_thumbs_height');

		$GroupSpw = htmlBase::newElement('input')
		->setName('banner_group_spw');

		$GroupSph = htmlBase::newElement('input')
		->setName('banner_group_sph');

		$GroupStrips = htmlBase::newElement('input')
		->setName('banner_group_strips');

		$GroupDescriptionOpacity = htmlBase::newElement('input')
		->setName('banner_group_description_opacity');

		if (isset($Group)){
			$GroupName->setValue(stripslashes($Group['banner_group_name']));
			$GroupTime->setValue(stripslashes($Group['banner_group_time']));
			$GroupEffectTime->setValue(stripslashes($Group['banner_group_effect_time']));
			$GroupDescriptionOpacity->setValue(stripslashes($Group['banner_group_description_opacity']));
			$GroupWidth->setValue(stripslashes($Group['banner_group_width']));
			$GroupHeight->setValue(stripslashes($Group['banner_group_height']));
			$GroupThumbsWidth->setValue(stripslashes($Group['banner_group_thumbs_width']));
			$GroupThumbsHeight->setValue(stripslashes($Group['banner_group_thumbs_height']));
			$GroupSpw->setValue(stripslashes($Group['banner_group_spw']));
			$GroupSph->setValue(stripslashes($Group['banner_group_sph']));
			$GroupStrips->setValue(stripslashes($Group['banner_group_strips']));
			$GroupEffect->selectOptionByValue(stripslashes($Group['banner_group_effect']));

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
	    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	   </tr>
	   <tr>
	    <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_TIME'); ?></td>
	    <td class="main"><?php echo $GroupTime->draw(); ?></td>
	   </tr>
	 <tr>
	    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	   </tr>
	   <tr>
	    <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_EFFECT'); ?></td>
	    <td class="main"><?php echo $GroupEffect->draw(); ?></td>
	   </tr>
	 <tr>
	    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	   </tr>

	 <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_EFFECT_TIME'); ?></td>
	  <td class="main"><?php echo $GroupEffectTime->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	 <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_WIDTH'); ?></td>
	  <td class="main"><?php echo $GroupWidth->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	 <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_HEIGHT'); ?></td>
	  <td class="main"><?php echo $GroupHeight->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_THUMBS_WIDTH'); ?></td>
	  <td class="main"><?php echo $GroupThumbsWidth->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_THUMBS_HEIGHT'); ?></td>
	  <td class="main"><?php echo $GroupThumbsHeight->draw(); ?></td>
	 </tr>
       <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_SPW'); ?></td>
	  <td class="main"><?php echo $GroupSpw->draw(); ?></td>
	 </tr>
        <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_SPH'); ?></td>
	  <td class="main"><?php echo $GroupSph->draw(); ?></td>
	 </tr>
       <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	  <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_STRIPS'); ?></td>
	  <td class="main"><?php echo $GroupStrips->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>

	 <tr>
	  <td class="main"><?php echo sysLanguage::get('TEXT_GROUP_DESCRIPTION_OPACITY'); ?></td>
	  <td class="main"><?php echo $GroupDescriptionOpacity->draw(); ?></td>
	 </tr>
   <tr>
	  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	 </tr>


 </table>