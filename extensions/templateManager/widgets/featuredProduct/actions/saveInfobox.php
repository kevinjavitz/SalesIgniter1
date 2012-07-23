<?php

if (isset($_POST['thumbnailImage'])){
    $WidgetProperties['thumbnailImage'] = 1;
}else{
    $WidgetProperties['thumbnailImage'] = 0;
}

if (isset($_POST['produts_qty'])){
    $WidgetProperties['qty'] = $_POST['produts_qty'];
}else{
    $WidgetProperties['qty'] = 1;
}

if (isset($_POST['marginLeft'])){
    $WidgetProperties['marginLeft'] = $_POST['marginLeft'];
}else{
    $WidgetProperties['marginLeft'] = '';
}

if (isset($_POST['marginTop'])){
    $WidgetProperties['marginTop'] = $_POST['marginTop'];
}else{
    $WidgetProperties['marginTop'] = '';
}