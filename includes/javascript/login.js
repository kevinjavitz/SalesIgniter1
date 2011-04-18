function session_win() {
	window.open("<?php echo itw_app_link('appExt=infoPages', 'show_page', 'info_shopping_cart'); ?>","info_shopping_cart","height=460,width=430,toolbar=no,statusbar=no,scrollbars=yes").focus();
}

$(document).ready(function (){
	$('#tabs').tabs();
});