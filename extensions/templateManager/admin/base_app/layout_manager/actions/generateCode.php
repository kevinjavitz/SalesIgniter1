
<?php

if(!isset($_POST['genTemplate'])){
	$templates = htmlBase::newElement('selectbox')
	->addClass('genTemplate');
	$TemplatesList = Doctrine_Core::getTable('TemplateManagerTemplates')
	->findAll();
	$p = 0;
	foreach($TemplatesList as $Template){
		if($p == 0 && !isset($_POST['templateName'])){
			$_POST['templateName'] = $Template->Configuration['DIRECTORY']->configuration_value;
		}
		$templates->addOption($Template->Configuration['DIRECTORY']->configuration_value, $Template->Configuration['NAME']->configuration_value);
		$p++;
	}
}
$products = htmlBase::newElement('selectbox')
->addClass('genProduct');

$QProducts = Doctrine_Query::create()
->from('Products p')
->leftJoin('p.ProductsDescription pd')
->where('pd.language_id = ?', Session::get('languages_id'));

EventManager::notify('AdminProductListingTemplateQueryBeforeExecute', &$QProducts);

$QProducts = $QProducts->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


	$products->addOption('0','No Product');
foreach($QProducts as $product){
	$products->addOption($product['products_id'], $product['ProductsDescription'][0]['products_name']);
}

if(!isset($_POST['type']) || $_POST['type'] == 'js'){
ob_start();
	$frameId = 'ses'.$_GET['lID'];
	$url = itw_catalog_app_link(null,'index','default').'?tplDir=codeGeneration&lID='.$_GET['lID'].'&genTemplate='.$_POST['templateName'].'&isjs=1';
	if(isset($_POST['products_id']) && $_POST['products_id'] > 0){
		$frameId .= $_POST['products_id'];
		$url .= '&products_id='.$_POST['products_id'];
	}
	$urlXDMJS = 'http://' . sysConfig::get('HTTP_DOMAIN_NAME'). sysConfig::getDirWsCatalog(). 'ext/codeIntegration/js/easyXDM.min.js';
	$urlSESLibraryJS = 'http://' . sysConfig::get('HTTP_DOMAIN_NAME'). sysConfig::getDirWsCatalog(). 'ext/codeIntegration/js/seslibrary.js';
	$urlSESLibraryPHP = 'http://' . sysConfig::get('HTTP_DOMAIN_NAME'). sysConfig::getDirWsCatalog(). 'ext/codeIntegration/php/seslibrary.php';
	$urlXDMSWF = 'http://' . sysConfig::get('HTTP_DOMAIN_NAME'). sysConfig::getDirWsCatalog(). 'ext/codeIntegration/js/easyxdm.swf';

?>
<div id="<?php echo $frameId; ?>"></div>
<script type="text/javascript" src="<?php echo $urlXDMJS;?>"></script>
<script type="text/javascript" src="<?php echo $urlSESLibraryJS;?>"></script>
<script type="text/javascript">
	var myObj = {name: '<?php echo $frameId; ?>',
		src:'<?php echo $urlSESLibraryPHP;?>?url='+encodeURIComponent('<?php echo $url;?>'),
		swf:'<?php echo $urlXDMSWF;?>'
	};

	SESJSLIBRARY.init([myObj]);
	SESJSLIBRARY.createIframe();
</script>

<?php
$genCode = ob_get_contents();
ob_end_clean();
}else{
ob_start();
?>
$_GET['tplDir'] = 'codeGeneration';
$_GET['genTemplate'] = '<?php echo $_POST['templateName'];?>';

$_GET['lID'] = '<?php echo $_GET['lID'];?>';

<?php
	if(isset($_POST['products_id']) && $_POST['products_id'] > 0){
		?>
$_GET['products_id'] = '<?php echo $_POST['products_id'];?>';
		<?php
}?>chdir('../');
include('includes/application_top.php');
include('extensions/templateManager/mainFiles/main_page.tpl.php');
include('includes/application_bottom.php');
<?php

	$genCode = ob_get_contents();
	ob_end_clean();
}
if(isset($_POST['onlyCode'])){
	$html = '<code><textarea rows="40">'.$genCode.'</textarea></code>';
}else{
	$html = '<select class="genType"><option value="js">js</option><option value="php">php</option></select>'.'&nbsp;Template: '. $templates->draw().'&nbsp;Product'.$products->draw();
	$html .= '<div class="genCode"><code><textarea rows="40">'.$genCode.'</textarea></code></div>';
}

EventManager::attachActionResponse(array(
		'success' => true,
		'html' => $html
	), 'json');
?>