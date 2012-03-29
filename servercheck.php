<?php


$requirements = array(
	'php_min_version' => '5.3',
	'php_extensions' => array(
		'pdo_mysql' => true,
		'imagick' => true,
		'gd' => true,
		'ionCube Loader' => true,
		'curl' => true,
		'mbstring' => true,
		'openssl' => true,
		'zlib' => true,
		'json' => true,
		'mcrypt' => true,
		'zip' => true,
		'ftp' => true,
		'suhosin' => false
	),
	'php_settings' => array(
		'register_globals' => 0,
		//'register_long_arrays' => 0,
		'magic_quotes_gpc' => 0,
		'magic_quotes' => 0,
		'file_uploads' => 1,
		'memory_limit' => 64,
		'session.auto_start' => 0,
		'session.use_trans_sid' => 0
	)
);
$successIcon = '<span class="ui-icon-colored ui-icon-green ui-icon-check"></span>';
$warningIcon = '<span class="ui-icon-colored ui-icon-yellow ui-icon-alert"></span>';
$errorIcon = '<span class="ui-icon-colored ui-icon-red ui-icon-closethick"></span>';
$infoIcon = '<span class="ui-icon-colored ui-icon-blue ui-icon-info" style="display:inline-block;"></span>';

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sales Igniter Checker</title>
	<meta name="robots" content="noindex,nofollow">
	<link rel="stylesheet" type="text/css" href="ext/jQuery/themes/smoothness/ui.all.css">
	<script type="text/javascript" src="ext/jQuery/jQuery.js"></script>
	<script type="text/javascript" src="ext/jQuery/ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="ext/jQuery/ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="ext/jQuery/ui/jquery.ui.button.js"></script>
	<script type="text/javascript">

		var curStep = 'auth';
		$(document).ready(function (){

			$('.phpVersion').append('<tr>' +
				'<td align="left"><?php echo $requirements['php_min_version'];?></td>' +
				'<td align="right"><?php echo PHP_VERSION;?></td>' +
				'<td align="center" style="width:20px;"><?php echo (version_compare(PHP_VERSION, $requirements->php_min_version) ? $successIcon : $errorIcon);?></td>' +
				'</tr>');
		<?php
			//check suhosin

  	foreach($requirements['php_extensions'] as $name => $req){
		   if($name != 'suhosin'){
			?>
			$('.phpExt').append('<tr>' +
				'<td align="left"><?php echo $name;?></td>' +
				'<td align="right"><?php echo (extension_loaded($name) ? 'Installed' : 'Not Installed');?></td>' +
				'<td align="center" style="width:20px;"><?php echo (extension_loaded($name) == $req ? $successIcon : $errorIcon);?></td>' +
				'</tr>');
			   <?php
}else{
			   if(extension_loaded($name)){
			   ?>
			   $('.phpExt').append('<tr>' +
				   '<td align="left"><?php echo $name;?></td>' +
				   '<td align="right"><?php echo (extension_loaded($name) ? 'suhosin.get.max_value_length must be set to 1012' : '');?></td>' +
				   '<td align="center" style="width:20px;"></td>' +
				   '</tr>');
				   <?php
			   }
}
   	}
		//memory limit
		foreach($requirements['php_settings'] as $name => $req){
			if($name != 'memory_limit'){
			?>
			$('.phpSettings').append('<tr>' +
				'<td align="left"><?php echo $name;?></td>' +
				'<td align="right"><?php echo ((int) ini_get($name) == 0 ? 'Off' : 'On');?></td>' +
				'<td align="center" style="width:20px;"><?php echo ((int) ini_get($name) == $req ? $successIcon : $errorIcon);?></td>' +
				'</tr>');
				<?php
			}else{
				?>
				$('.phpSettings').append('<tr>' +
					'<td align="left"><?php echo $name.'(64MB+)';?></td>' +
					'<td align="right"><?php echo ((int) ini_get($name) >=64 ? 'Ok' : 'Required 64MB+ ');?></td>' +
					'<td align="center" style="width:20px;"><?php echo ((int) ini_get($name) >= $req ? $successIcon : $errorIcon);?></td>' +
					'</tr>');
				<?php
			}
   	}
		?>
		});
	</script>
	<style>
		body { font-size: 100%;font-family:Helvetica, Tahoma, Arial;color:#302e2e; }
		td { font-size: .8em; }
		div { position: relative; }
		.errorBox > span { line-height:2em;margin-left:.5em; }
		.pageHeader > span { line-height:2em;margin-left:.5em; }
		.pageInfoboxHeader > span { line-height:2em;margin-left:.5em; }
		.reqBox { border-color:#ffcb05; }
		.reqHeader { border-color:#f58220;background-color:#f58220;color:#4c4c4c;margin:.2em; }
		.reqHeader > span { line-height:2em;margin-left:.5em; }
		.ui-icon-colored { width: 16px; height: 16px;display: block; text-indent: -99999px; overflow: hidden; background-repeat: no-repeat; }
		.ui-icon-red { background-image: url(ext/jQuery/themes/icons/ui-icons_cc0000_256x240.png); }
		.ui-icon-green { background-image: url(ext/jQuery/themes/icons/ui-icons_2ef91f_256x240.png); }
		.ui-icon-blue { background-image: url(ext/jQuery/themes/icons/ui-icons_2e83ff_256x240.png); }
		.ui-icon-yellow { background-image: url(ext/jQuery/themes/icons/ui-icons_f2ec64_256x240.png); }
	</style>


<script type="text/javascript">

</script>
</head>
	<body>
	<table cellpadding="5" cellspacing="0" border="0" width="100%">
		<tr>
			<td valign="top" style="width:250px;">
				<div class="ui-widget-content reqBox">
					<div class="ui-widget-header reqHeader">
						<span>System Requirements</span>
					</div>
					<div><table cellpadding="3" cellspacing="3" border="0" width="100%" class="phpVersion">
						<tr>
							<td><b><u>PHP Version</u></b></td>
						</tr>
					</table>
						<table cellpadding="3" cellspacing="3" border="0" width="100%" class="phpExt">
							<tr>
								<td colspan="3"><b><u>PHP Extensions</u></b></td>
							</tr>
						</table>
						<table cellpadding="3" cellspacing="3" border="0" width="100%" class="phpSettings">
							<tr>
								<td colspan="3"><b><u>PHP Settings</u></b></td>
							</tr>
						</table></div>
				</div>
			</td>
		</tr>
		</table>
	</body>
</html>