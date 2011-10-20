<?php
if (isset($_POST['filter_start'])){
    $filters = false;
    foreach($_POST['filter_start'] as $key =>$val){
        $filters[] = array('start' => $val, 'stop' => $_POST['filter_stop'][$key]);
    }
    $WidgetProperties['filters'] = $filters;
}
?>