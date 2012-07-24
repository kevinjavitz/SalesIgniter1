<link rel="stylesheet" type="text/css" href="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/uniform/css/uniform.default.css" />

<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/ui/jquery.effects.core.js">

</script>

<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/spiner/jquery-spin.js">

</script>

<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/uniform/jquery.uniform.js">

</script>


<script type="text/javascript">
	$(document).ready(function(){
		$('#bodyContainer div.container').not('#logoCont, #menuCont, #footerWrapper, #copyCont, #nextmainHome, #prevmainHome').hide();
		$('#bodyContainer div.container').not('#logoCont, #menuCont, #footerWrapper, #copyCont, #nextmainHome, #prevmainHome').show("fade", {}, 3000);
		$.spin.imageBasePath = '<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/external/spiner/css/';
		$('.spinner').spin();
		//$('.selectuniform').css('width','25px');
		$('.selectuniform').uniform();
		if($('#teamriderprof').size() > 0){
			$('#teamriderprof .pictPostPart').each(function(){
				var pos = $(this).offset();
				var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
				var adder = 0;
				/*if(is_chrome){
					adder = 41;
				} */
				$(this).next().css('top', ((pos.top+adder)+'px'));
				$(this).next().css('left', ((pos.left))+'px');
				var self = $(this);
				$(this).mouseenter(function(){self.fadeOut(500);self.next().fadeIn(500);});
				$(this).next().mouseleave(function(){$(this).fadeOut(500);self.fadeIn(500);});
				$(this).next().click(function(){
					window.location = self.next().find('a').attr('href');
				});
				//$(this).next().hover(function(){$(this).fadeOut(500);$(this).fadeIn(500);});
			});
		}

	});
</script>
