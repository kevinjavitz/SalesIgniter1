<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxLanguagesFlags extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('languagesFlags');
	}

	public function buildStylesheet() {
		$WidgetProperties = $this->getWidgetProperties();
		ob_start();
		?>

	.dropdown dd, .dropdown dt, .dropdown ul { margin:0px; padding:0px; z-index:10000;position:relative;}
	.dropdown dd { position:relative; }
	.dropdown a, .dropdown a:visited { color:#816c5b; text-decoration:none; outline:none;padding:2px 0px;}
	.dropdown a:hover { color:#5d4617;}
	.dropdown dt a:hover { color:#5d4617; border: 1px solid #d0c9af;}
	.dropdown dt a {background:#e4dfcb url(arrow.png) no-repeat scroll right center; display:block;
	border:1px solid #d4ca9a; width:40px;}
	.dropdown dt a span {cursor:pointer; display:block;}
	.dropdown dd ul { background:#e4dfcb none repeat scroll 0 0; border:1px solid #d4ca9a; color:#C5C0B0; display:none;
	left:0px; position:absolute; top:2px; width:auto; min-width:40px; list-style:none;}
	.dropdown span.value { display:none;}
	.dropdown dd ul li a { display:block;}
	.dropdown dd ul li a:hover { background-color:#d0c9af;}

	.dropdown img.flag { border:none; vertical-align:middle; margin-left:10px; }
	.flagvisibility { display:none;}
		<?php
  			$cssSource = ob_get_contents();
			ob_end_clean();
		return $cssSource;
	}


	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();

		ob_start();
		?>
	$(".dropdown img.flag").addClass("flagvisibility");

	$(".dropdown dt a").click(function() {
		$(".dropdown dd ul").toggle();
		return false;
	});

	$(".dropdown dd ul li a").click(function() {
		var text = $(this).html();
		var href = $(this).attr('href');
		$(".dropdown dt a").html(text);
		$(".dropdown dd ul").hide();
		if($("#langDropdown").find("dt a").attr('href') != href){
			window.location = $("#langDropdown").find("dt a").attr('href');
		}else{
			return false;
		}
	});



	$(document).bind('click', function(e) {
	var $clicked = $(e.target);
	if (! $clicked.parents().hasClass("dropdown"))
	$(".dropdown dd ul").hide();
	});

	$(".dropdown img.flag").toggleClass("flagvisibility");

		<?php
 		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}

	public function show(){
		$boxWidgetProperties = $this->getWidgetProperties();
		foreach(sysLanguage::getLanguages() as $lInfo) {
			if($lInfo['code'] == Session::get('languages_code')){
				$code = $lInfo['code'];
				$name = '';//$lInfo['showName']('&nbsp;');
				break;
			}

		}
		$htmlText = '<dl id="langDropdown" class="dropdown"><dt><a href="'.itw_app_link(tep_get_all_get_params(array('language', 'currency')) . 'language=' . $code).'">'.$name.'<img class="flag" src="images/flags/'.$code.'.png" alt="" /><span class="value">'.$code.'</span></a></dt><dd><ul>';
		foreach(sysLanguage::getLanguages() as $lInfo) {
			$showName = '';//$lInfo['showName']('&nbsp;');
			$htmlText .= '<li><a href="'.itw_app_link(tep_get_all_get_params(array('language', 'currency')) . 'language=' . $lInfo['code']).'">'.$showName.'<img class="flag" src="images/flags/'.$lInfo['code'].'.png" alt="" /><span class="value">'.$lInfo['code'].'</span></a></li>';
		}

		$htmlText .= '</ul></dd></dl>';

		$this->setBoxContent($htmlText);

		return $this->draw();
	}
}
?>