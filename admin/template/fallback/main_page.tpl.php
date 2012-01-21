<!DOCTYPE html>
<html <?php echo sysLanguage::getHtmlParams(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo sysLanguage::getCharset(); ?>">
		<title><?php echo sprintf(sysLanguage::get('TITLE'), sysConfig::get('STORE_NAME')); ?></title>
		<base href="<?php echo (($request_type == 'SSL') ? sysConfig::get('HTTPS_SERVER') : sysConfig::get('HTTP_SERVER')) . sysConfig::get('DIR_WS_ADMIN'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/stylesheet.php?import=<?php echo implode(',', $App->getStylesheetFiles());?>&env=admin&<?php echo Session::getSessionName() . '=' . Session::getSessionId();?>" />
		<script type="text/javascript">
			var CKEDITOR_BASEPATH = '<?php echo sysConfig::getDirWsAdmin() . 'rental_wysiwyg/';?>';
			var allGetParams = '<?php echo substr(tep_get_all_get_params(), 0, -1);?>';
			var serverName = '<?php echo $_SERVER['SERVER_NAME'];?>';
			var DIR_WS_ADMIN = '<?php echo sysConfig::getDirWsAdmin();?>';
			var DIR_WS_CATALOG = '<?php echo sysConfig::getDirWsCatalog();?>';
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
		</script>
		<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/javascript.php?import=<?php echo implode(',', $App->getJavascriptFiles());?>&env=admin&<?php echo Session::getSessionName() . '=' . Session::getSessionId();?>"></script>
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
		<header><?php
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
	</body>
	<div id="expiredSessionWindow" title="Session Has Expired" style="display:none;">
		<p>Your session has expired, please click ok to log back in.</p>
	</div>
</html>