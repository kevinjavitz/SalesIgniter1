<?php

$BannerStatusEnabled = htmlBase::newElement('radio')
	->setName('banners_status')
	->setLabel('Published')
	->setValue('1');

$BannerStatusDisabled = htmlBase::newElement('radio')
	->setName('banners_status')
	->setLabel('Not Published')
	->setValue('0');

$BannerStatusRunning = htmlBase::newElement('radio')
	->setName('banners_status')
	->setLabel('Running')
	->setValue('2');
$BannerStatusExpired = htmlBase::newElement('radio')
	->setName('banners_status')
	->setLabel('Expired')
	->setValue('3');


	$BannerDateScheduled = htmlBase::newElement('input')
	->setName('banners_date_scheduled')
	->addClass('useDatepicker');

	$BannerDateExpires = htmlBase::newElement('input')
	->setName('banners_expires_date')
	->addClass('useDatepicker');
        if (isset($Banner)){

            if ($Banner['banners_status'] == '1'){
			$BannerStatusEnabled->setChecked(true);
            }else if ($Banner['banners_status'] == '0'){
			    $BannerStatusDisabled->setChecked(true);
			}else if ($Banner['banners_status'] == '2'){
			    $BannerStatusRunning->setChecked(true);
			}else if ($Banner['banners_status'] == '3'){
			    $BannerStatusExpired->setChecked(true);
			}
	        $BannerDateScheduled->setValue($Banner['banners_date_scheduled']);
	        $BannerDateExpires->setValue($Banner['banners_expires_date']);
    }
?>
<table cellpadding="0" cellspacing="0" border="0">
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BANNER_STATUS'); ?></td>
   <td class="main"><?php echo $BannerStatusEnabled->draw() . $BannerStatusDisabled->draw() . $BannerStatusRunning->draw() . $BannerStatusExpired->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BANNER_DATE_SCHEDULED'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $BannerDateScheduled->draw(); ?></td>
  </tr>
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	<tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BANNER_DATE_EXPIRES'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $BannerDateExpires->draw(); ?></td>
  </tr>
   <tr>
         <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
</table>
 <?php
		
		$BannerName = htmlBase::newElement('input')
		->setName('banners_name');

		$BannerURL = htmlBase::newElement('input')
		->setName('banners_url');

		$BannerBody = htmlBase::newElement('input')
		->setType('file')
		->setName('banners_body');

		$BannerBodyThumbs = htmlBase::newElement('input')
		->setType('file')
		->setName('banners_body_thumbs');

		$BannerHTML = htmlBase::newElement('textarea')
		->setName('banners_html');
		
		$BannerDescription = htmlBase::newElement('ck_editor')
		->setName('banners_description');

		$BannerSmallDescription = htmlBase::newElement('textarea')
		->setName('banners_small_description');

		$BannerProduct = htmlBase::newElement('input')
		->setName('banners_products_id');

		$BannerExpiresClicks = htmlBase::newElement('input')
		->setName('banners_expires_clicks');

		$BannerExpiresViews = htmlBase::newElement('input')
		->setName('banners_expires_views');

		$BannerClicks = htmlBase::newElement('input')
		->setName('banners_clicks')
		->attr('readonly', 'readonly');

		$BannerViews = htmlBase::newElement('input')
		->setName('banners_views')
		->attr('readonly', 'readonly');

		$BannerSortOrder = htmlBase::newElement('input')
		->setName('banners_sort_order');
		
		if (isset($Banner)){
			$BannerName->setValue(stripslashes($Banner['banners_name']));
			$BannerURL->setValue(stripslashes($Banner['banners_url']));

			$BannerHTML->html(stripslashes($Banner['banners_html']))
						->attr('rows', '3')
						->attr('cols', '23');
			$BannerSmallDescription->html(stripslashes($Banner['banners_small_description']))
						->attr('rows', '3')
						->attr('cols', '23');
			$BannerDescription->html(stripslashes($Banner['banners_description']));
			$BannerProduct->setValue(stripslashes($Banner['banners_products_id']));
			$BannerViews->setValue(stripslashes($Banner['banners_views']));
			$BannerClicks->setValue(stripslashes($Banner['banners_clicks']));
			$BannerExpiresViews->setValue(stripslashes($Banner['banners_expires_views']));
			$BannerExpiresClicks->setValue(stripslashes($Banner['banners_expires_clicks']));
			$BannerSortOrder->setValue(stripslashes($Banner['banners_sort_order']));
		}
?>

 <table cellpadding="0" cellspacing="0" border="0">

  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_BANNER_NAME'); ?></td>
   <td class="main"><?php echo $BannerName->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	<tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_URL'); ?></td>
   <td class="main"><?php echo $BannerURL->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_BODY'); ?></td>
   <td class="main"><?php echo $BannerBody->draw()."<br/>";
	   if (isset($Banner) && substr($Banner['banners_body'],strlen($Banner['banners_body'])-3,3)!='swf' && !empty($Banner['banners_body'])){
            echo "<img src='".sysConfig::getDirWsCatalog().'extensions/bannerManager/images/'. $Banner['banners_body']."' />";
	   }else{
		   echo getFlashMovie(sysConfig::getDirWsCatalog().'extensions/bannerManager/images/'. $Banner['banners_body'],"Flash not installed",'150','400');
	   }

   ?>
   
   </td>
  </tr>
<tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_BODY_THUMBS'); ?></td>
   <td class="main"><?php echo $BannerBodyThumbs->draw()."<br/>";
	   if (isset($Banner) && !empty($Banner['banners_body_thumbs'])){
            echo "<img src='".sysConfig::getDirWsCatalog().'extensions/bannerManager/images/'. $Banner['banners_body_thumbs']."' />";
       }
?>

   </td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	   <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_HTML'); ?></td>
   <td class="main"><?php echo $BannerHTML->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

	 <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_SMALL_DESCRIPTION'); ?></td>
   <td class="main"><?php echo $BannerSmallDescription->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_DESCRIPTION'); ?></td>
   <td class="main"><?php echo $BannerDescription->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_EXPIRES_VIEWS'); ?></td>
   <td class="main"><?php echo $BannerExpiresViews->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_EXPIRES_CLICKS'); ?></td>
   <td class="main"><?php echo $BannerExpiresClicks->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_CLICKS'); ?></td>
   <td class="main"><?php echo $BannerClicks->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_BANNER_VIEWS'); ?></td>
   <td class="main"><?php echo $BannerViews->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

 </table>
