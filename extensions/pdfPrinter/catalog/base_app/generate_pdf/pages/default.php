<?php


require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');

require_once(sysConfig::getDirFsCatalog(). 'dompdf/dompdf_config.inc.php');

require(sysConfig::getDirFsCatalog() . 'includes/classes/pdftemplate.php');

$iName = 'invoice';
if(isset($_GET['oID']) && !isset($_GET['type'])){
	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false && $multiStore->isEnabled() === true){
		$QordersStore = Doctrine_Query::create()
		->from('OrdersToStores')
		->where('orders_id=?', $_GET['oID'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	    $orderStore = (isset($QordersStore[0]['stores_id'])?$QordersStore[0]['stores_id']:0);
		$QInvLayouts = Doctrine_Query::create()
			->select('invoice_layout')
			->from('Stores')
			->where('stores_id=?', $orderStore)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$invLayout = $QInvLayouts[0]['invoice_layout'];
	}else{
		$invLayout = sysConfig::get('EXTENSION_PDF_PRINTER_INVOICE_LAYOUT');
	}
}else{
	$invLayout = sysConfig::get('EXTENSION_PDF_PRINTER_AGREEMENT_LAYOUT');
	$iName = 'agreement';
}

$layout_id = $invLayout;

function addStylesPDF($El, $Styles) {
	if ($El->hasAttr('id') && $El->attr('id') != ''){
		return;
	}

	$css = array();
	foreach($Styles as $sInfo){
		if (substr($sInfo->definition_value, 0, 1) == '{' || substr($sInfo->definition_value, 0, 1) == '['){
			$css[$sInfo->definition_key] = json_decode($sInfo->definition_value);
		}
		else {
			if($sInfo->definition_key == 'position-header'){
				$sInfo->definition_key = 'position';
				$css['top'] = '0px';
				$El->css('top', '0px');
				$css['left'] = '0px';
				$El->css('left', '0px');
				$css['overflow'] = 'hidden';
				$El->css('overflow', 'hidden');
			}

			if($sInfo->definition_key == 'position-footer'){
				$sInfo->definition_key = 'position';
				$css['bottom'] = '0px';
				$El->css('bottom', '0px');
				$css['left'] = '0px';
				$El->css('left', '0px');
				$css['height'] = '50px';
				$El->css('height', '50px');
				$css['overflow'] = 'hidden';
				$El->css('overflow', 'hidden');
			}

			$css[$sInfo->definition_key] = $sInfo->definition_value;
		}
		$El->css($sInfo->definition_key, $css[$sInfo->definition_key]);
	}
}

function addInputsPDF($El, $Config) {
	foreach($Config as $cInfo){
		if ($cInfo->configuration_key != 'id') {
			continue;
		}

		$El->attr('id', $cInfo->configuration_value);
	}
}

function processContainerChildrenPDF($MainObj, &$El) {
	foreach($MainObj->Children as $childObj){
		$NewEl = htmlBase::newElement('div')
			->addClass('container');

		if ($childObj->Configuration->count() > 0){
			addInputsPDF($NewEl, $childObj->Configuration);
		}

		if ($childObj->Styles->count() > 0){
			addStylesPDF($NewEl, $childObj->Styles);
		}

		$El->append($NewEl);
		processContainerColumns($NewEl, $childObj->Columns);
		if ($childObj->Children->count() > 0){
			processContainerChildrenPDF($childObj, $NewEl);
		}
	}
}

function processContainerColumnsPDF(&$Container, $Columns) {
	if (!$Columns) {
		return;
	}

	foreach($Columns as $col){
		$ColEl = htmlBase::newElement('div')
			->addClass('column');

		if ($col->Configuration->count() > 0){
			addInputsPDF($ColEl, $col->Configuration);
		}

		if ($col->Styles->count() > 0){
			addStylesPDF($ColEl, $col->Styles);
		}

		$WidgetHtml = '';
		if ($col->Widgets->count() > 0){
			foreach($col->Widgets as $wid){
				$WidgetSettings = '';
				if ($wid->Configuration->count() > 0){
					foreach($wid->Configuration as $cInfo){
						if ($cInfo->configuration_key == 'widget_settings'){
							$WidgetSettings = json_decode($cInfo->configuration_value);
						}
					}
				}

				$className = 'PDFInfoBox' . ucfirst($wid->identifier);
				if (!class_exists($className)){
					$QboxPath = Doctrine_Query::create()
						->select('box_path')
						->from('PDFTemplatesInfoboxes')
						->where('box_code = ?', $wid->identifier)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					require(sysConfig::getDirFsCatalog(). $QboxPath[0]['box_path'] . 'pdfinfobox.php');
				}
				$Class = new $className;

				if (isset($WidgetSettings->template_file) && !empty($WidgetSettings->template_file)){
					$Class->setBoxTemplateFile($WidgetSettings->template_file);
				}
				if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
					$Class->setBoxId($WidgetSettings->id);
				}
				if (isset($WidgetSettings->widget_title) && !empty($WidgetSettings->widget_title)){
					$Class->setBoxHeading($WidgetSettings->widget_title->{Session::get('languages_id')});
				}

				$Class->setWidgetProperties($WidgetSettings);

				$WidgetHtml .= $Class->show();
			}
		}
		$ColEl->html($WidgetHtml);

		$Container->append($ColEl);
	}
}


$Construct = htmlBase::newElement('p')->attr('id', 'bodyContainer');

$Layout = Doctrine_Core::getTable('PDFTemplateManagerLayouts')->find($layout_id);
if ($Layout->Containers->count() > 0){
	foreach($Layout->Containers as $MainObj){
		if ($MainObj->Parent->container_id > 0) {
			continue;
		}

		$MainEl = htmlBase::newElement('div')
			->addClass('container');

		if ($MainObj->Configuration->count() > 0){
			addInputsPDF($MainEl, $MainObj->Configuration);
		}

		if ($MainObj->Styles->count() > 0){
			addStylesPDF($MainEl, $MainObj->Styles);
		}

		processContainerColumnsPDF($MainEl, $MainObj->Columns);
		if ($MainObj->Children->count() > 0){
			processContainerChildrenPDF($MainObj, $MainEl);
		}
		$Construct->append($MainEl);
	}
}


$boxStylesEntered = array();
$addCss = '';
function parseContainerPDF($Container) {
	global $boxStylesEntered, $addCss;

	if ($Container->Configuration['id'] && $Container->Configuration['id']->configuration_value != ''){
		$Style = new StyleBuilder();
		$Style->setSelector('#' . $Container->Configuration['id']->configuration_value);
		foreach($Container->Styles as $sInfo){
			$Style->addRule($sInfo->definition_key, $sInfo->definition_value);
		}
		$addCss .= $Style->outputCss();
	}

	if ($Container->Children->count() > 0){
		foreach($Container->Children as $ChildObj){
			parseContainerPDF($ChildObj);
		}
	}
	else {
		foreach($Container->Columns as $colInfo){
			if ($colInfo->Configuration['id'] && $colInfo->Configuration['id']->configuration_value != ''){
				$Style = new StyleBuilder();
				$Style->setSelector('#' . $colInfo->Configuration['id']->configuration_value);
				foreach($colInfo->Styles as $sInfo){
					$Style->addRule($sInfo->definition_key, $sInfo->definition_value);
				}
				$addCss .= $Style->outputCss();
			}

			foreach($colInfo->Widgets as $wInfo){
				foreach($wInfo->Configuration as $config){
					if ($config->configuration_key == 'widget_settings'){
						$WidgetSettings = json_decode($config->configuration_value);
						break;
					}
				}
				$className = 'PDFInfoBox' . ucfirst($wInfo->identifier);
				if (!class_exists($className)){
					$Qbox = Doctrine_Query::create()
						->select('box_path')
						->from('PDFTemplatesInfoboxes')
						->where('box_code = ?', $wInfo->identifier)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					require($Qbox[0]['box_path'] . 'pdfinfobox.php');
				}

				$Box = new $className();
				if (method_exists($className, 'buildStylesheet')){
					if ($Box->buildStylesheetMultiple === true || !in_array($className, $boxStylesEntered)){
						if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
							$Box->setBoxId($WidgetSettings->id);
						}
						$Box->setWidgetProperties($WidgetSettings);

						$addCss .= $Box->buildStylesheet();

						$boxStylesEntered[] = $className;
					}
				}
			}
		}
	}
}

$Layout = Doctrine_Core::getTable('PDFTemplateManagerLayouts')->find($layout_id);
if ($Layout){
	$Template = $Layout->Template;

	if ($Layout->Styles->count() > 0){
		$StyleBuilder = new StyleBuilder();
		$StyleBuilder->setSelector('body');
		$rules = array();
		foreach($Layout->Styles as $sInfo){
			$StyleBuilder->addRule($sInfo->definition_key, $sInfo->definition_value);
		}
		$addCss .= $StyleBuilder->outputCss();
	}

	foreach($Layout->Containers as $Container){
		parseContainerPDF($Container);
	}
}

ob_start();
?>
<style type="text/css">
	@page {
		margin: 0;
	}

<?php
	echo $addCss;
	?>
		/*body {
		 margin-top: 3.5cm;
		 margin-bottom: 3cm;
		 margin-left: 1.5cm;
		 margin-right: 1.5cm;
		 font-family: sans-serif;
		 text-align: justify;
	 }*/

	.container{
		display: block;
	}
	.column{
		display: inline-block;
		vertical-align: top;
	}

	hr {
		page-break-after: always;
		border: 0;
	}

	.page-number:before {
		content: counter(page);
	}

</style>
<?php
echo $Construct->draw();
$myPdf = ob_get_contents();
ob_end_clean();
$dompdf = new DOMPDF();
$dompdf->set_base_path(sysConfig::get('DIR_FS_DOCUMENT_ROOT'));
$dompdf->load_html(utf8_decode($myPdf));
$dompdf->render();
//$dompdf->stream('saved_pdf.pdf', array("Attachment" => 0));
$pdf = $dompdf->output();
file_put_contents(sysConfig::getDirFsCatalog(). 'temp/pdf/'.$iName.'_'.(isset($_GET['oID'])?$_GET['oID']:'').'.pdf', $pdf);
header("Location: " .sysConfig::getDirWsCatalog(). 'temp/pdf/'.$iName.'_'.(isset($_GET['oID'])?$_GET['oID']:'').'.pdf');
itwExit();