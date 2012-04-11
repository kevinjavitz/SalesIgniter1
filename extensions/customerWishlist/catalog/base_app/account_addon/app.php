<?php
/*
	Royalties System Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/external/fullcalendar/fullcalendar.js');
	$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');

	$App->addStylesheetFile('ext/jQuery/external/fullcalendar/fullcalendar.css');
	$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
	
function customerWishlistSocialSharing( $uid ) {
	$_url = itw_app_link('wishlist='.$uid,'customerWishlist/account_addon','manage_wishlist');
	$_title = 'My Wishlist';
	$html = '';
	
	if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_TWITTER') == 'True'){
		$dataUrl = '';
		$dataCount = '';
		$url = 'data-url="'.$_url.'" ';
		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
				$dataCount  = 'data-count="horizontal"';
			}else{
				$dataCount = 'data-count="vertical"';
			}
		}else{
			$dataCount  = 'data-count="none"';
		}
		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SIZE') == 'Medium'){
			$dataSize = 'data-size="medium"';
		}else{
			$dataSize = 'data-size="large"';
		}

		$html .='<div style="display:inline-block;width:90px;"><a href="https://twitter.com/share" '.$dataUrl.' '.$url.' class="twitter-share-button" data-lang="en" '.$dataCount.' '.$dataSize. '>Tweet</a>
				 <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';
	}

	if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_FACEBOOK') == 'True'){
		$dataCount = '';
		$url = 'data-href="'.$_url.'" ';
		$html .='<div style="display:inline-block;" id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, \'script\', \'facebook-jssdk\'));</script>';

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
				$dataCount  = 'data-layout="button_count"';
			}else{
				$dataCount = 'data-layout="box_count"';
			}
		}else{
			$dataCount  = '';
		}
		$html .='<div style="display:inline-block;width:90px" class="fb-like" '.$url.' data-send="false" '.$dataCount.' data-width="450" data-show-faces="false"></div>';
	}

	if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_GPLUS') == 'True'){
		$dataCount = '';
		$url = 'data-href="'.$_url.'" ';

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
				$dataCount  = '<g:plusone></g:plusone>';
			}else{
				$dataCount = '<g:plusone size="tall"></g:plusone>';
			}
		}else{
			$dataCount  = '<g:plusone annotation="inline"></g:plusone>';
		}

		$html .= '<div style="display:inline-block;width:90px;position:relative;z-index:100;">'.$dataCount.'</div>';
		$html .='<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
	}

	if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_DIGG') == 'True'){
		$html .= '<script type="text/javascript">
(function() {
var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];
s.type = \'text/javascript\';
s.async = true;
s.src = \'http://widgets.digg.com/buttons.js\';
s1.parentNode.insertBefore(s, s1);
})();
</script>';
		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
				$html .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton DiggCompact" href="http://digg.com/submit?url='.urlencode($_url).'&amp;title='.urlencode($_title).'"></a></div>';
			}else{
				$html .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton DiggMedium" href="http://digg.com/submit?url='.urlencode($_url).'&amp;title='.urlencode($_title).'"></a></div>';
			}
		}else{
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SIZE') == 'Medium'){
				$html .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton" href="http://digg.com/submit?url='.urlencode($_url).'&amp;title='.urlencode($_title).'"><img src="http://developers.diggstatic.com/sites/all/themes/about/img/follow_buttons/Follow-On-Digg-Mini.png" alt="" title=""  /></a></div>';
			}else{
				$html .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton" href="http://digg.com/submit?url='.urlencode($_url).'&amp;title='.urlencode($_title).'"><img src="http://developers.diggstatic.com/sites/all/themes/about/img/follow_buttons/Follow-On-Digg-Small.png" alt="" title=""  /></a></div>';
			}
		}
	}

	if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_LINKEDIN') == 'True'){

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
				$html .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.$_url.'" data-counter="right"></script></div>';
			}else{
				$html .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.$_url.'" data-counter="top"></script></div>';
			}
		}else{
			$html .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.$_url.'"></script></div>';
		}
	}
	
	return $html;
}
?>