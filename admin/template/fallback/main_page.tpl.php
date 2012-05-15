<?php
$stylesheetLink = sysConfig::getDirWsCatalog() . 'extensions/templateManager/catalog/globalFiles/stylesheet.php?' .
	'&env=admin' .
	'&' . Session::getSessionName() . '=' . Session::getSessionId() .
	'&tplDir=' . Session::get('tplDir') .
	'&import[]=' . implode('&import[]=', $App->getStylesheetFiles()) .
	(isset($_GET['noCache']) ? '&noCache' : '');

$javascriptLink = sysConfig::getDirWsCatalog() . 'extensions/templateManager/catalog/globalFiles/javascript.php?' .
	'&env=admin' .
	'&' . Session::getSessionName() . '=' . Session::getSessionId() .
	'&tplDir=' . Session::get('tplDir') .
	'&import[]=' . implode('&import[]=', $App->getJavascriptFiles()) .
	(isset($_GET['noCache']) ? '&noCache' : '');
?>
<!DOCTYPE html>
<html <?php echo sysLanguage::getHtmlParams(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo sysLanguage::getCharset(); ?>">
		<title><?php echo sprintf(sysLanguage::get('TITLE'), sysConfig::get('STORE_NAME')); ?></title>
		<base href="<?php echo (($request_type == 'SSL') ? sysConfig::get('HTTPS_SERVER') : sysConfig::get('HTTP_SERVER')) . sysConfig::get('DIR_WS_ADMIN'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $stylesheetLink;?>" />
		<script type="text/javascript">
			var CKEDITOR_BASEPATH = '<?php echo sysConfig::getDirWsAdmin() . 'rental_wysiwyg/';?>';
			var allGetParams = '<?php echo substr(tep_get_all_get_params(), 0, -1);?>';
			var serverName = '<?php echo sysConfig::get('HTTP_HOST');?>';
			var DIR_WS_ADMIN = '<?php echo sysConfig::getDirWsAdmin();?>';
			var DIR_WS_CATALOG = '<?php echo sysConfig::getDirWsCatalog();?>';
			var DIR_FS_ADMIN = '<?php echo sysConfig::getDirFsAdmin();?>';
			var DIR_FS_CATALOG = '<?php echo sysConfig::getDirFsCatalog();?>';
			var ENABLE_SSL = '<?php echo (sysConfig::exists('ENABLE_SSL') ? sysConfig::get('ENABLE_SSL') : 'false');?>';
			var SID = '<?php echo SID;?>';
			var sessionName = '<?php echo Session::getSessionName();?>';
			var sessionId = '<?php echo Session::getSessionId();?>';
			var request_type = '<?php echo $request_type;?>';
			var thisFile = '<?php echo basename($_SERVER['PHP_SELF']);?>';
			var thisApp = '<?php echo $App->getAppName();?>';
			var thisAppPage = '<?php echo $App->getAppPage();?>';
			var thisAppExt = '<?php echo (isset($_GET['appExt']) && !empty($_GET['appExt']) ? $_GET['appExt'] : null);?>';
			var productID = '<?php echo (int)(isset($_GET['pID']) ? $_GET['pID'] : '0');?>';
			
			var jsLanguage = {
				defines: [],
				set: function (k, v){
					this.defines[k] = v;
				},
				get: function (key){
					return this.defines[key] || '';
				}
			};
<?php
	if (sysLanguage::hasJavascriptDefines() === true){
		foreach(sysLanguage::getJavascriptDefines() as $k => $v){
			echo '			jsLanguage.set(\'' . $k . '\', "' . $v . '");' . "\n";
		}
	}
?>
		</script>
		<script type="text/javascript" src="<?php echo $javascriptLink;?>"></script>
<?php
if (isset($_GET['oError'])){
	echo '		<script type="text/javascript">alert(\'Onetime rentals has been disabled. If you would like to enable it, please contact www.itwebexperts.com\');</script>' . "\n";
}

$infoBoxId = $App->getInfoBoxId();

echo '		<script type="text/javascript">' . "\n" .
'			$(document).ready(function (){' . "\n";
if ($infoBoxId == 'new'){
	echo '				showInfoBox(\'new\');' . "\n";
}elseif ($infoBoxId != null){
	echo '				$(\'tbody > .ui-grid-row[infobox_id=' . $infoBoxId . ']\').click();' . "\n";
}else{
	echo '				if ($(\'tbody > .ui-grid-row:eq(0)\').attr(\'infobox_id\')){' . "\n" .
	'					$(\'tbody > .ui-grid-row:eq(0)\').click();' . "\n" .
	'				}' . "\n";
}
echo '			});' . "\n" .
'		</script>' . "\n";
?>
	</head>
	<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">
		<header class="adminHeader"><?php
			require(sysConfig::getDirFsAdmin() . 'includes/header.php');
		?></header>
		<table border="0" width="100%" cellspacing="0" cellpadding="15">
			<tr>
				<td width="100%" valign="top" class="main"><div id="bodyWrapprer" style="width:100%;position:relative;"><?php
					if ($messageStack->size('pageStack') > 0){
						echo $messageStack->output('pageStack', true) . '<br />';
					}

					if (isset($appContent) && file_exists(sysConfig::getDirFsAdmin() . 'applications/' . $appContent)){
						require(sysConfig::getDirFsAdmin() . 'applications/' . $appContent);
					}elseif (isset($appContent) && file_exists($appContent)){
						require($appContent);
					}else{
						require('template/content/' . $pageContent . '.tpl.php');
					}
				?></div></td>
			</tr>
		</table>
		<footer><?php
			require(sysConfig::getDirFsAdmin() . 'includes/footer.php');
		?></footer>
		<div class="sysMsgBlock" style="position:fixed;top:0px;left:0px;text-align:center;width:60%;margin-left:20%;margin-right:20%;display:none;">
		</div>
	</body>
	<div id="expiredSessionWindow" title="Session Has Expired" style="display:none;">
		<p>Your session has expired, please click ok to log back in.</p>
	</div>
</html>