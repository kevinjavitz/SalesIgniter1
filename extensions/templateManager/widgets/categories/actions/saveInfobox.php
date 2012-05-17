<?php
if(isset($_POST['selected_category'])){
	$WidgetProperties['selected_category'] = $_POST['selected_category'];
}

if(isset($_POST['default_expanded_category'])){
	$WidgetProperties['default_expanded_category'] = $_POST['default_expanded_category'];
}

if(isset($_POST['widgetId']) && !empty($_POST['widgetId'])){
	$WidgetProperties['widgetId'] = $_POST['widgetId'];
} else{
	$WidgetProperties['widgetId'] = 'categoriesBoxMenu';
}

if(isset($_POST['categories']) && !empty($_POST['categories'])){
	$WidgetProperties['excludedCategories'] = implode(';',$_POST['categories']);
} else{
	$WidgetProperties['excludedCategories'] = '';
}
if (isset($_POST['showCurrentSubcategory'])){
	$WidgetProperties['showCurrentSubcategory'] = $_POST['showCurrentSubcategory'];
}
else {
	$WidgetProperties['showCurrentSubcategory'] = '';
}
if (isset($_POST['showSubcategory'])){
	$WidgetProperties['showSubcategory'] = $_POST['showSubcategory'];
}
else {
	$WidgetProperties['showSubcategory'] = '';
}

if (isset($_POST['showAlways'])){
	$WidgetProperties['showAlways'] = $_POST['showAlways'];
}
else {
	$WidgetProperties['showAlways'] = '';
}


?>