<?php
if(isset($_GET['isjs'])){
	?>
	<!DOCTYPE html>
<html <?php echo sysLanguage::getHtmlParams();?>>
<head>
	<?php
	$title	= sysConfig::get('STORE_NAME');
	$desc	= sysConfig::get('STORE_NAME_ADDRESS');
	$keys	= sysConfig::get('STORE_NAME');

	EventManager::notify('PageLayoutHeaderTitle', &$title);
	EventManager::notify('PageLayoutHeaderMetaDescription', &$desc);
	EventManager::notify('PageLayoutHeaderMetaKeyword', &$keys);

	echo sprintf('		<title>%s</title>', $title) . "\n";
	echo sprintf('		<meta name="description" content="%s" />', $desc) . "\n";
	echo sprintf('		<meta name="keywords" content="%s" />', $keys) . "\n";

	$contents = EventManager::notifyWithReturn('PageLayoutHeaderCustomMeta');
	if (!empty($contents)){
		foreach($contents as $html){
			echo '		' . $html . "\n";
		}
	}
	?>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo sysLanguage::getCharset();?>" />
	<base href="<?php echo (($request_type == 'SSL') ? sysConfig::get('HTTPS_SERVER') : sysConfig::get('HTTP_SERVER')) . sysConfig::getDirWsCatalog(); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/stylesheet.php?import=<?php echo implode(',', $stylesheets);?>&layout_id=<?php echo $templateLayoutId;?>&tplDir=<?php echo Session::get('tplDir');?>" />
	<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>ext/codeIntegration/js/easyXDM.min.js"></script>
	<script type="text/javascript">
		<?php
		if(isset($_GET['redirectUrl'])){
		?>
		var myRedirect = '<?php echo urldecode($_GET['redirectUrl']);?>';
		<?php
		}else{
		?>
		var myRedirect = '';
		<?php
		}
		?>
		var thisFile = '<?php echo basename($_SERVER['PHP_SELF']);?>';
		var serverName = '<?php echo sysConfig::get('HTTP_HOST');?>';
		var DIR_WS_CATALOG = '<?php echo DIR_WS_CATALOG;?>';
		var ENABLE_SSL = '<?php echo sysConfig::get('ENABLE_SSL');?>';
		var SID = '<?php echo SID;?>';
		var sessionId = '<?php echo Session::getSessionId();?>';
		var sessionName = '<?php echo Session::getSessionName();?>';
		var request_type = '<?php echo $request_type;?>';
	</script>
	<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/javascript.php?import=<?php echo implode(',', $javascriptFiles);?>&layout_id=<?php echo $templateLayoutId;?>&tplDir=<?php echo Session::get('tplDir');?>"></script>
</head>
<body>
<noscript>
	<div class="noscript">
		<div class="noscript-inner">
			<p>
				<strong>JavaScript seem to be disabled in your browser.</strong>
			</p>
			<p>
				You must have JavaScript enabled in your browser to utilize the functionality of this website.
			</p>
		</div>
	</div>
</noscript>
	<?php
 	if (sysConfig::get('DEMO_STORE') == 'on'){
	?>
<p class="demo-notice">
	This is a DEMO of Sales Igniter Rental Software. For more info <a href="http://www.rental-e-commerce-software.com" style="color:#ffffff;">Click Here</a>.
</p>
	<?php
 }

	echo $templateLayoutContent;
	?>
<script type="text/javascript">
	window.onload = function(){
		parent.socket.postMessage(document.body.clientHeight || document.body.offsetHeight || document.body.scrollHeight);
	};
</script>
</body>
</html>
<?php
}else{
?>

	<link rel="stylesheet" type="text/css" href="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/stylesheet.php?import=<?php echo implode(',', $stylesheets);?>&layout_id=<?php echo $templateLayoutId;?>&tplDir=<?php echo Session::get('tplDir');?>" />
	<script type="text/javascript">
		var thisFile = '<?php echo basename($_SERVER['PHP_SELF']);?>';
		var serverName = '<?php echo sysConfig::get('HTTP_HOST');?>';
		var DIR_WS_CATALOG = '<?php echo DIR_WS_CATALOG;?>';
		var ENABLE_SSL = '<?php echo sysConfig::get('ENABLE_SSL');?>';
		var SID = '<?php echo SID;?>';
		var sessionId = '<?php echo Session::getSessionId();?>';
		var sessionName = '<?php echo Session::getSessionName();?>';
		var request_type = '<?php echo $request_type;?>';
	</script>
	<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/catalog/globalFiles/javascript.php?import=<?php echo implode(',', $javascriptFiles);?>&layout_id=<?php echo $templateLayoutId;?>&tplDir=<?php echo Session::get('tplDir');?>"></script>

<?php
	echo $templateLayoutContent;
}
?>

