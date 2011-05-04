<?php
	echo '<link rel="stylesheet" href="extensions/bannerManager/catalog/ext_app/banners/javascript/styles.css" type="text/css" />';


		echo '<script type="text/javascript" src="extensions/bannerManager/catalog/ext_app/banners/javascript/jquery.coin.js"></script>';
		echo '<!--[if IE]><script type="text/javascript" src="extensions/bannerManager/catalog/ext_app/banners/javascript/fix_eolas.js" defer="defer"></script><![endif]-->';


		echo '<script type="text/javascript">';
?>
	$(document).ready(function (){

<?php if( !$bannerG[0]["banner_group_is_rotator"]){ ?>
		$('#<?php echo $bannerG[0]["banner_group_name"]; ?>').rentalslide({

		width: <?php echo $bannerG[0]['banner_group_width']; ?>,
		height: <?php echo $bannerG[0]['banner_group_height']; ?>,
		spw: <?php echo $bannerG[0]['banner_group_spw']; ?>,
		sph: <?php echo $bannerG[0]['banner_group_sph']; ?>,
		strips: <?php echo $bannerG[0]['banner_group_strips']; ?>,
delay: <?php echo $bannerG[0]['banner_group_time']; ?>,
sDelay: <?php echo $bannerG[0]['banner_group_effect_time']; ?>,
topacity: <?php echo $bannerG[0]['banner_group_description_opacity']; ?>,
titleSpeed: 1000,
effect: '<?php echo $bannerG[0]["banner_group_effect"]; ?>',
hoverPause: <?php echo $bannerG[0]['banner_group_hover_pause'] == 0?'false':'true'; ?> ,
showArrows: <?php echo $bannerG[0]['banner_group_show_arrows']== 0?'false':'true'; ?>,
showNumbers : <?php echo $bannerG[0]['banner_group_show_numbers']== 0?'false':'true'; ?>,
showCustom: <?php echo $bannerG[0]['banner_group_show_custom']== 0?'false':'true'; ?>,
showThumbnails: <?php echo $bannerG[0]['banner_group_show_thumbnails']== 0?'false':'true'; ?>,
showThumbsDesc: <?php echo $bannerG[0]['banner_group_show_thumbs_desc']== 0?'false':'true'; ?>,
autoHideNumber: <?php echo $bannerG[0]['banner_group_auto_hide_numbers']== 0?'false':'true'; ?>,
autoHideCustom: <?php echo $bannerG[0]['banner_group_auto_hide_custom']== 0?'false':'true'; ?>,
autoHideArrows: <?php echo $bannerG[0]['banner_group_auto_hide_arrows']== 0?'false':'true'; ?>,
autoHideThumbs: <?php echo $bannerG[0]['banner_group_auto_hide_thumbs']== 0?'false':'true'; ?>,
autoHideThumbsDesc: <?php echo $bannerG[0]['banner_group_auto_hide_thumbs_desc']== 0?'false':'true'; ?>,
autoHideTitle: <?php echo $bannerG[0]['banner_group_auto_hide_title']== 0?'false':'true'; ?>,
navigationDirection:"horizontal",
useAutoResize: <?php echo $bannerG[0]['banner_group_use_autoresize']== 0?'false':'true'; ?>,
useThumbs: <?php echo $bannerG[0]['banner_group_use_thumbs']== 0?'false':'true'; ?>,
thumbsWidth:<?php echo $bannerG[0]['banner_group_thumbs_width']; ?>,
thumbsHeight:<?php echo $bannerG[0]['banner_group_thumbs_height']; ?>,
nImages:<?php echo count($bannerD); ?>,
 'onMyClick':function(bn){/* not really needed because I make an action with the redirect of banner*/},
 'onView':function(bn){

<?php if($bannerG[0]['banner_group_is_expiring'] == 1){ ?>
 				$.ajax({
					url: js_app_link('appExt=bannerManager&app=banner_actions&appPage=default'+'&action=viewBanner&bid='+bn),
					cache: false,
					type: 'get',
					dataType: 'html',
					success: function (html){
					}
				});
				<?php }?>
 },
 'addB': function(){/* ajax call to an action with returns the added banners live call---which are */},
 'removeB': function(){}
		 });
<?php }else{?>
//is rotator javascript
gid = <?php echo $bannerG[0]['banner_group_id'];?>;
start = 0;
$.ajax({
					url: js_app_link('appExt=bannerManager&app=banner_actions&appPage=default'+'&action=getProductsId'),
					type: 'post',
					data:'gid='+gid+'&start='+start,
					success: function (data){
						start = data.start;

						$('#<?php echo $bannerG[0]['banner_group_name'];?>').html(data.html).css({ opacity: 0 }).fadeTo("slow",1);
					}
				});
setInterval(function(){$.ajax({
					url: js_app_link('appExt=bannerManager&app=banner_actions&appPage=default'+'&action=getProductsId'),
					type: 'post',
					data:'gid='+gid+'&start='+start,
					success: function (data){
						start = data.start;
						$('#<?php echo $bannerG[0]['banner_group_name'];?>').html(data.html).css({ opacity: 0 }).fadeTo("slow",1);
					}
				});},'<?php echo $bannerG[0]['banner_group_time']; ?>');

<?php }?>
});

<?php

	echo '</script>';
	
	echo '<div id="'.$bannerG[0]['banner_group_name'].'">';
	if( !$bannerG[0]["banner_group_is_rotator"]){ 
		$k1 = 0;
		foreach($bannerD as $banners){

		if (tep_not_null($banners['banners_html'])){

			if(tep_not_null($banners['banners_url']))
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'>".$banners['banners_html']."</a>";
			else
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' onClick='return false;' style='cursor:default;' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'>".$banners['banners_html']."</a>";
		}else if(tep_not_null($banners['banners_body']) && substr($banners['banners_body'],strlen($banners['banners_html'])-3,3) == 'swf'){

			if(tep_not_null($banners['banners_url']))
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'>".getFlashMovie(sysConfig::getDirWsCatalog().'extensions/bannerManager/images/'.$banners['banners_body'],$banners['banners_small_description'],$bannerG[0]['banner_group_width'],$bannerG[0]['banner_group_height'])."</a>";
			else
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' onClick='return false;' style='cursor:default;' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'>".getFlashMovie(sysConfig::getDirWsCatalog().'extensions/bannerManager/images/'.$banners['banners_body'],$banners['banners_small_description'],$bannerG[0]['banner_group_width'],$bannerG[0]['banner_group_height'])."</a>";
		}else{

			if(tep_not_null($banners['banners_url']))
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'><img src='imagick_thumb.php?path=rel&imgSrc=". 'extensions/bannerManager/images/'.$banners['banners_body']."&width=".$bannerG[0]['banner_group_width']."&height=".$bannerG[0]['banner_group_height']."'/><span>".$banners['banners_small_description']."</span></a>";
			else
				echo "<a id='".$bannerG[0]['banner_group_name']."chtml".$k1."' onClick='return false;' style='cursor:default;' href='".itw_app_link('appExt=bannerManager&action=clickBanner&bid='.$banners['banners_id']."&url=".$banners['banners_url'],'banner_actions','default')."'><img src='imagick_thumb.php?path=rel&imgSrc=". 'extensions/bannerManager/images/'.$banners['banners_body']."&width=".$bannerG[0]['banner_group_width']."&height=".$bannerG[0]['banner_group_height']."'/><span>".$banners['banners_small_description']."</span></a>";
		}
		$k1++;
		}
	}
?>

<?php
	echo '</div>';	
?>