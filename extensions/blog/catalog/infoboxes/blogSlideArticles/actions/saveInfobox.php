<?php
if (isset($_POST['nr_art'])){
	$WidgetProperties['nr_art'] = $_POST['nr_art'];
}
else {
	$WidgetProperties['nr_art'] = 3;
}

if (isset($_POST['imageWidth'])){
	$WidgetProperties['image_width'] = $_POST['imageWidth'];
}
else {
	$WidgetProperties['image_width'] = '175';
}

if (isset($_POST['imageHeight'])){
	$WidgetProperties['image_height'] = $_POST['imageHeight'];
}
else {
	$WidgetProperties['image_height'] = '175';
}

if (isset($_POST['nr_art'])){
	$WidgetProperties['nr_art'] = $_POST['nr_art'];
}
else {
	$WidgetProperties['nr_art'] = 3;
}

if (isset($_POST['widgetId'])){
	$WidgetProperties['widgetId'] = $_POST['widgetId'];
}
else {
	$WidgetProperties['widgetId'] = 'widgetId';
}

if (isset($_POST['new_selected_category'])){
	$WidgetProperties['new_selected_category'] = $_POST['new_selected_category'];
}
else {
	$WidgetProperties['new_selected_category'] = -1;
}


if (isset($_POST['showImage'])){
	$WidgetProperties['showImage'] = $_POST['showImage'];
}
else {
	$WidgetProperties['showImage'] = '';
}

if (isset($_POST['showTitle'])){
	$WidgetProperties['showTitle'] = $_POST['showTitle'];
}
else {
	$WidgetProperties['showTitle'] = '';
}

if (isset($_POST['displayQty'])){
	$WidgetProperties['displayQty'] = $_POST['displayQty'];
}
else {
	$WidgetProperties['displayQty'] = '3';
}

if (isset($_POST['moveQty'])){
	$WidgetProperties['moveQty'] = $_POST['moveQty'];
}
else {
	$WidgetProperties['moveQty'] = '3';
}

if (isset($_POST['speed'])){
	$WidgetProperties['speed'] = $_POST['speed'];
}
else {
	$WidgetProperties['speed'] = '5000';
}

if (isset($_POST['duration'])){
	$WidgetProperties['duration'] = $_POST['duration'];
}
else {
	$WidgetProperties['duration'] = '3000';
}

if (isset($_POST['easing'])){
	$WidgetProperties['easing'] = $_POST['easing'];
}
else {
	$WidgetProperties['easing'] = 'swing';
}




if (isset($_POST['imageHasLink'])){
	$WidgetProperties['imageHasLink'] = $_POST['imageHasLink'];
}
else {
	$WidgetProperties['imageHasLink'] = '';
}

if (isset($_POST['showVideo'])){
	$WidgetProperties['showVideo'] = $_POST['showVideo'];
}
else {
	$WidgetProperties['showVideo'] = '';
}

if (isset($_POST['showVideoImage'])){
	$WidgetProperties['showVideoImage'] = $_POST['showVideoImage'];
}
else {
	$WidgetProperties['showVideoImage'] = '';
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

if (isset($_POST['showDesc'])){
	$WidgetProperties['showDesc'] = $_POST['showDesc'];
}
else {
	$WidgetProperties['showDesc'] = '';
}

if (isset($_POST['descLength']) && !empty($_POST['descLength'])){
	$WidgetProperties['descLength'] = $_POST['descLength'];
}
else {
	$WidgetProperties['descLength'] = '200';
}
