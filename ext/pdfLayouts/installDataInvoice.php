<?php
$Layout = Doctrine_Core::getTable('PDFTemplateManagerLayouts')->create();
$Layout->layout_name = 'invoice1';
$Layout->Styles['margin']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"5","left_unit":"mm"}';
$Layout->Styles['width']->definition_value = '215mm';
$Layout->Styles['padding']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Layout->Configuration['width']->configuration_value = '215';
$Layout->Configuration['width_unit']->configuration_value = 'mm';
$Layout->Configuration['isheader']->configuration_value = 'false';
$Layout->Configuration['id']->configuration_value = '';
$Layout->Configuration['isfooter']->configuration_value = 'false';
$Layout->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"5","left_unit":"mm"}';
$Layout->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';

$Container[1] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[1]);
$Container[1]->sort_order = '1';
$Container[1]->Styles['width']->definition_value = '210mm';
$Container[1]->Configuration['isheader']->configuration_value = 'false';
$Container[1]->Configuration['width_unit']->configuration_value = 'mm';
$Container[1]->Configuration['width']->configuration_value = '210';
$Container[1]->Configuration['id']->configuration_value = '';
$Container[1]->Configuration['isfooter']->configuration_value = 'false';

$Column[1] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[1]);
$Column[1]->sort_order = '1';
$Column[1]->Styles['width']->definition_value = '129mm';
$Column[1]->Configuration['isheader']->configuration_value = 'false';
$Column[1]->Configuration['width_unit']->configuration_value = 'mm';
$Column[1]->Configuration['width']->configuration_value = '129';
$Column[1]->Configuration['id']->configuration_value = '';
$Column[1]->Configuration['isfooter']->configuration_value = 'false';

if (!isset($Box['customImage'])){
 $Box['customImage'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customImage');
    if (!is_object($Box['customImage']) || $Box['customImage']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/customImage/', 'customImage', 'null');
       $Box['customImage'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customImage');
   }
}

$Widget[3] = $Column[1]->Widgets->getTable()->create();
$Column[1]->Widgets->add($Widget[3]);
$Widget[3]->identifier = 'customImage';
$Widget[3]->sort_order = '1';
$Widget[3]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"image_source":"/extensions/pdfPrinter/images/logo.png","image_link":""}';

$Column[2] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[2]);
$Column[2]->sort_order = '2';
$Column[2]->Styles['width']->definition_value = '41mm';
$Column[2]->Configuration['isfooter']->configuration_value = 'false';
$Column[2]->Configuration['width']->configuration_value = '41';
$Column[2]->Configuration['id']->configuration_value = '';
$Column[2]->Configuration['width_unit']->configuration_value = 'mm';
$Column[2]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['invoiceNumber'])){
 $Box['invoiceNumber'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceNumber');
    if (!is_object($Box['invoiceNumber']) || $Box['invoiceNumber']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/invoiceNumber/', 'invoiceNumber', 'null');
       $Box['invoiceNumber'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceNumber');
   }
}

$Widget[4] = $Column[2]->Widgets->getTable()->create();
$Column[2]->Widgets->add($Widget[4]);
$Widget[4]->identifier = 'invoiceNumber';
$Widget[4]->sort_order = '1';
$Widget[4]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Invoice Number:"},"text":"","type":"left"}';

$Column[8] = $Container[1]->Columns->getTable()->create();
$Container[1]->Columns->add($Column[8]);
$Column[8]->sort_order = '3';
$Column[8]->Styles['width']->definition_value = '40mm';
$Column[8]->Configuration['width']->configuration_value = '40';
$Column[8]->Configuration['id']->configuration_value = '';
$Column[8]->Configuration['width_unit']->configuration_value = 'mm';
$Column[8]->Configuration['isfooter']->configuration_value = 'false';
$Column[8]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['invoiceDate'])){
 $Box['invoiceDate'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceDate');
    if (!is_object($Box['invoiceDate']) || $Box['invoiceDate']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/invoiceDate/', 'invoiceDate', 'null');
       $Box['invoiceDate'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceDate');
   }
}

$Widget[5] = $Column[8]->Widgets->getTable()->create();
$Column[8]->Widgets->add($Widget[5]);
$Widget[5]->identifier = 'invoiceDate';
$Widget[5]->sort_order = '1';
$Widget[5]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Invoice Date:"},"text":"","type":"left","short":false}';

$Container[2] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[2]);
$Container[2]->sort_order = '2';
$Container[2]->Styles['padding']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[2]->Styles['width']->definition_value = '210mm';
$Container[2]->Styles['margin']->definition_value = '{"top":"5","top_unit":"mm","right":"0","right_unit":"mm","bottom":"05","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[2]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[2]->Configuration['margin']->configuration_value = '{"top":"5","top_unit":"mm","right":"0","right_unit":"mm","bottom":"05","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[2]->Configuration['isfooter']->configuration_value = 'false';
$Container[2]->Configuration['id']->configuration_value = '';
$Container[2]->Configuration['isheader']->configuration_value = 'false';
$Container[2]->Configuration['width']->configuration_value = '210';
$Container[2]->Configuration['width_unit']->configuration_value = 'mm';

$Column[3] = $Container[2]->Columns->getTable()->create();
$Container[2]->Columns->add($Column[3]);
$Column[3]->sort_order = '1';
$Column[3]->Styles['width']->definition_value = '110mm';
$Column[3]->Configuration['isfooter']->configuration_value = 'false';
$Column[3]->Configuration['width']->configuration_value = '110';
$Column[3]->Configuration['isheader']->configuration_value = 'false';
$Column[3]->Configuration['id']->configuration_value = '';
$Column[3]->Configuration['width_unit']->configuration_value = 'mm';

if (!isset($Box['billingInformation'])){
 $Box['billingInformation'] = $PDFTemplatesInfoboxes->findOneByBoxCode('billingInformation');
    if (!is_object($Box['billingInformation']) || $Box['billingInformation']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/billingInformation/', 'billingInformation', 'null');
       $Box['billingInformation'] = $PDFTemplatesInfoboxes->findOneByBoxCode('billingInformation');
   }
}

$Widget[6] = $Column[3]->Widgets->getTable()->create();
$Column[3]->Widgets->add($Widget[6]);
$Widget[6]->identifier = 'billingInformation';
$Widget[6]->sort_order = '1';
$Widget[6]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Billing Details"},"firstname":false,"lastname":false,"name":true,"fulladdress":true,"streetaddress":false,"city":false,"postcode":false,"suburb":false,"state":false,"country":false,"telephone":false,"dob":false,"gender":false,"cif":false,"vat":false,"company":false,"email":false}';

$Column[4] = $Container[2]->Columns->getTable()->create();
$Container[2]->Columns->add($Column[4]);
$Column[4]->sort_order = '2';
$Column[4]->Styles['width']->definition_value = '100mm';
$Column[4]->Configuration['isfooter']->configuration_value = 'false';
$Column[4]->Configuration['width']->configuration_value = '100';
$Column[4]->Configuration['id']->configuration_value = '';
$Column[4]->Configuration['width_unit']->configuration_value = 'mm';
$Column[4]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['deliveryInformation'])){
 $Box['deliveryInformation'] = $PDFTemplatesInfoboxes->findOneByBoxCode('deliveryInformation');
    if (!is_object($Box['deliveryInformation']) || $Box['deliveryInformation']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/deliveryInformation/', 'deliveryInformation', 'null');
       $Box['deliveryInformation'] = $PDFTemplatesInfoboxes->findOneByBoxCode('deliveryInformation');
   }
}

$Widget[7] = $Column[4]->Widgets->getTable()->create();
$Column[4]->Widgets->add($Widget[7]);
$Widget[7]->identifier = 'deliveryInformation';
$Widget[7]->sort_order = '1';
$Widget[7]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":"Delivery Details"},"firstname":false,"lastname":false,"name":true,"fulladdress":true,"streetaddress":false,"city":false,"postcode":false,"suburb":false,"state":false,"country":false,"telephone":false,"dob":false,"gender":false,"cif":false,"vat":false,"company":false,"email":false}';

$Container[3] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[3]);
$Container[3]->sort_order = '3';
$Container[3]->Styles['padding']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[3]->Styles['width']->definition_value = '210mm';
$Container[3]->Styles['margin']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"5","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[3]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[3]->Configuration['margin']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"5","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[3]->Configuration['isfooter']->configuration_value = 'false';
$Container[3]->Configuration['id']->configuration_value = '';
$Container[3]->Configuration['isheader']->configuration_value = 'false';
$Container[3]->Configuration['width']->configuration_value = '210';
$Container[3]->Configuration['width_unit']->configuration_value = 'mm';

$Column[5] = $Container[3]->Columns->getTable()->create();
$Container[3]->Columns->add($Column[5]);
$Column[5]->sort_order = '1';
$Column[5]->Styles['width']->definition_value = '210mm';
$Column[5]->Configuration['isfooter']->configuration_value = 'false';
$Column[5]->Configuration['width']->configuration_value = '210';
$Column[5]->Configuration['id']->configuration_value = '';
$Column[5]->Configuration['width_unit']->configuration_value = 'mm';
$Column[5]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['invoiceListing'])){
 $Box['invoiceListing'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceListing');
    if (!is_object($Box['invoiceListing']) || $Box['invoiceListing']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/invoiceListing/', 'invoiceListing', 'null');
       $Box['invoiceListing'] = $PDFTemplatesInfoboxes->findOneByBoxCode('invoiceListing');
   }
}

$Widget[8] = $Column[5]->Widgets->getTable()->create();
$Column[5]->Widgets->add($Widget[8]);
$Widget[8]->identifier = 'invoiceListing';
$Widget[8]->sort_order = '1';
$Widget[8]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"box.tpl","widget_title":{"1":""},"tableHeading":true,"showQty":true,"showBarcode":false,"showName":true,"showModel":false,"showTax":true,"showPrice":true,"showPriceTax":true,"showTotal":true,"showTotalTax":true,"showExtraInfo":true}';

$Container[4] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[4]);
$Container[4]->sort_order = '4';
$Container[4]->Styles['width']->definition_value = '210mm';
$Container[4]->Configuration['isfooter']->configuration_value = 'false';
$Container[4]->Configuration['width']->configuration_value = '210';
$Container[4]->Configuration['id']->configuration_value = '';
$Container[4]->Configuration['width_unit']->configuration_value = 'mm';
$Container[4]->Configuration['isheader']->configuration_value = 'false';

$Column[6] = $Container[4]->Columns->getTable()->create();
$Container[4]->Columns->add($Column[6]);
$Column[6]->sort_order = '1';
$Column[6]->Styles['width']->definition_value = '146mm';
$Column[6]->Configuration['isfooter']->configuration_value = 'false';
$Column[6]->Configuration['width']->configuration_value = '146';
$Column[6]->Configuration['id']->configuration_value = '';
$Column[6]->Configuration['width_unit']->configuration_value = 'mm';
$Column[6]->Configuration['isheader']->configuration_value = 'false';

$Column[7] = $Container[4]->Columns->getTable()->create();
$Container[4]->Columns->add($Column[7]);
$Column[7]->sort_order = '2';
$Column[7]->Styles['width']->definition_value = '64mm';
$Column[7]->Configuration['isfooter']->configuration_value = 'false';
$Column[7]->Configuration['width']->configuration_value = '64';
$Column[7]->Configuration['id']->configuration_value = '';
$Column[7]->Configuration['width_unit']->configuration_value = 'mm';
$Column[7]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['subtotalValue'])){
 $Box['subtotalValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('subtotalValue');
    if (!is_object($Box['subtotalValue']) || $Box['subtotalValue']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/subtotalValue/', 'subtotalValue', 'null');
       $Box['subtotalValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('subtotalValue');
   }
}

$Widget[9] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[9]);
$Widget[9]->identifier = 'subtotalValue';
$Widget[9]->sort_order = '1';
$Widget[9]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"boxHeaderSameLine.tpl","widget_title":{"1":"Subtotal"},"text":"","type":"left"}';

if (!isset($Box['taxValue'])){
 $Box['taxValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('taxValue');
    if (!is_object($Box['taxValue']) || $Box['taxValue']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/taxValue/', 'taxValue', 'null');
       $Box['taxValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('taxValue');
   }
}

$Widget[10] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[10]);
$Widget[10]->identifier = 'taxValue';
$Widget[10]->sort_order = '2';
$Widget[10]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"boxHeaderSameLine.tpl","widget_title":{"1":"Tax"},"text":"","type":"left"}';

if (!isset($Box['shippingValue'])){
 $Box['shippingValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('shippingValue');
    if (!is_object($Box['shippingValue']) || $Box['shippingValue']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/shippingValue/', 'shippingValue', 'null');
       $Box['shippingValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('shippingValue');
   }
}

$Widget[11] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[11]);
$Widget[11]->identifier = 'shippingValue';
$Widget[11]->sort_order = '3';
$Widget[11]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"boxHeaderSameLine.tpl","widget_title":{"1":"Shipping"},"text":"","type":"left"}';

if (!isset($Box['totalValue'])){
 $Box['totalValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('totalValue');
    if (!is_object($Box['totalValue']) || $Box['totalValue']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/totalValue/', 'totalValue', 'null');
       $Box['totalValue'] = $PDFTemplatesInfoboxes->findOneByBoxCode('totalValue');
   }
}

$Widget[12] = $Column[7]->Widgets->getTable()->create();
$Column[7]->Widgets->add($Widget[12]);
$Widget[12]->identifier = 'totalValue';
$Widget[12]->sort_order = '4';
$Widget[12]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"boxHeaderSameLine.tpl","widget_title":{"1":"Total"},"text":"","type":"left"}';

$Container[5] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[5]);
$Container[5]->sort_order = '5';
$Container[5]->Styles['width']->definition_value = '210mm';
$Container[5]->Styles['margin']->definition_value = '{"top":"5","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[5]->Styles['padding']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[5]->Configuration['width']->configuration_value = '210';
$Container[5]->Configuration['isheader']->configuration_value = 'false';
$Container[5]->Configuration['isfooter']->configuration_value = 'false';
$Container[5]->Configuration['id']->configuration_value = '';
$Container[5]->Configuration['width_unit']->configuration_value = 'mm';
$Container[5]->Configuration['margin']->configuration_value = '{"top":"5","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[5]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';

$Column[9] = $Container[5]->Columns->getTable()->create();
$Container[5]->Columns->add($Column[9]);
$Column[9]->sort_order = '1';
$Column[9]->Styles['width']->definition_value = '210mm';
$Column[9]->Configuration['width']->configuration_value = '210';
$Column[9]->Configuration['id']->configuration_value = '';
$Column[9]->Configuration['width_unit']->configuration_value = 'mm';
$Column[9]->Configuration['isfooter']->configuration_value = 'false';
$Column[9]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['customText'])){
 $Box['customText'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customText');
    if (!is_object($Box['customText']) || $Box['customText']->count() <= 0){
       installInfobox('extensions/infoPages/catalog/pdfinfoboxes/customText/', 'customText', 'infoPages');
       $Box['customText'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customText');
   }
}

$Widget[13] = $Column[9]->Widgets->getTable()->create();
$Column[9]->Widgets->add($Widget[13]);
$Widget[13]->identifier = 'customText';
$Widget[13]->sort_order = '1';
$Widget[13]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"selected_page":"34"}';

$Container[6] = $Layout->Containers->getTable()->create();
$Layout->Containers->add($Container[6]);
$Container[6]->sort_order = '6';
$Container[6]->Styles['width']->definition_value = '210mm';
$Container[6]->Styles['margin']->definition_value = '{"top":"10","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[6]->Styles['padding']->definition_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[6]->Configuration['isfooter']->configuration_value = 'false';
$Container[6]->Configuration['padding']->configuration_value = '{"top":"0","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[6]->Configuration['margin']->configuration_value = '{"top":"10","top_unit":"mm","right":"0","right_unit":"mm","bottom":"0","bottom_unit":"mm","left":"0","left_unit":"mm"}';
$Container[6]->Configuration['id']->configuration_value = '';
$Container[6]->Configuration['width']->configuration_value = '210';
$Container[6]->Configuration['isheader']->configuration_value = 'false';
$Container[6]->Configuration['width_unit']->configuration_value = 'mm';

$Column[10] = $Container[6]->Columns->getTable()->create();
$Container[6]->Columns->add($Column[10]);
$Column[10]->sort_order = '1';
$Column[10]->Styles['width']->definition_value = '166mm';
$Column[10]->Configuration['isfooter']->configuration_value = 'false';
$Column[10]->Configuration['width']->configuration_value = '166';
$Column[10]->Configuration['id']->configuration_value = '';
$Column[10]->Configuration['width_unit']->configuration_value = 'mm';
$Column[10]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['customLine'])){
 $Box['customLine'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customLine');
    if (!is_object($Box['customLine']) || $Box['customLine']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/customLine/', 'customLine', 'null');
       $Box['customLine'] = $PDFTemplatesInfoboxes->findOneByBoxCode('customLine');
   }
}

$Widget[14] = $Column[10]->Widgets->getTable()->create();
$Column[10]->Widgets->add($Widget[14]);
$Widget[14]->identifier = 'customLine';
$Widget[14]->sort_order = '1';
$Widget[14]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"text":"Signature","type":"top","width":"25"}';

$Column[11] = $Container[6]->Columns->getTable()->create();
$Container[6]->Columns->add($Column[11]);
$Column[11]->sort_order = '2';
$Column[11]->Styles['width']->definition_value = '44mm';
$Column[11]->Configuration['isfooter']->configuration_value = 'false';
$Column[11]->Configuration['id']->configuration_value = '';
$Column[11]->Configuration['width']->configuration_value = '44';
$Column[11]->Configuration['width_unit']->configuration_value = 'mm';
$Column[11]->Configuration['isheader']->configuration_value = 'false';

if (!isset($Box['currentDate'])){
 $Box['currentDate'] = $PDFTemplatesInfoboxes->findOneByBoxCode('currentDate');
    if (!is_object($Box['currentDate']) || $Box['currentDate']->count() <= 0){
       installInfobox('includes/modules/pdfinfoboxes/currentDate/', 'currentDate', 'null');
       $Box['currentDate'] = $PDFTemplatesInfoboxes->findOneByBoxCode('currentDate');
   }
}

$Widget[15] = $Column[11]->Widgets->getTable()->create();
$Column[11]->Widgets->add($Widget[15]);
$Widget[15]->identifier = 'currentDate';
$Widget[15]->sort_order = '1';
$Widget[15]->Configuration['widget_settings']->configuration_value = '{"id":"","template_file":"noFormatingBox.tpl","widget_title":{"1":""},"text":"Date","type":"top","short":false}';
$Layout->save();
