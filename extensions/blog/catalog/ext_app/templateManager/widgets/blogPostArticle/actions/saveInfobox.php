<?php

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

if (isset($_POST['showTitle'])){
	$WidgetProperties['showTitle'] = $_POST['showTitle'];
}
else {
	$WidgetProperties['showTitle'] = '';
}


if (isset($_POST['showImage'])){
	$WidgetProperties['showImage'] = $_POST['showImage'];
}
else {
	$WidgetProperties['showImage'] = '';
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
