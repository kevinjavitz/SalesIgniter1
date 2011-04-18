<?php
if (isset($_POST['nr_art'])){
	$WidgetProperties['nr_art'] = $_POST['nr_art'];
}
else {
	$WidgetProperties['nr_art'] = 3;
}

if (isset($_POST['showImage'])){
	$WidgetProperties['showImage'] = $_POST['showImage'];
}
else {
	$WidgetProperties['showImage'] = '';
}

if (isset($_POST['showDate'])){
	$WidgetProperties['showDate'] = $_POST['showDate'];
}
else {
	$WidgetProperties['showDate'] = '';
}

if (isset($_POST['showReadMore'])){
	$WidgetProperties['showReadMore'] = $_POST['showReadMore'];
}
else {
	$WidgetProperties['showReadMore'] = '';
}

if (isset($_POST['descLength']) && !empty($_POST['descLength'])){
	$WidgetProperties['descLength'] = $_POST['descLength'];
}
else {
	$WidgetProperties['descLength'] = '200';
}
