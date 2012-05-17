<?php
if(sysConfig::get('USE_INTRO_PAGE') == 'false'){
	header('Location: '.itw_app_link(null,'index','default'));
}