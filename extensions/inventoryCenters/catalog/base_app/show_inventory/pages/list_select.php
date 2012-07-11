<?php
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].'/inventoryCenters/show_inventory/list_result.php';
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].'/inventoryCenters/show_inventory/list_result.php';
    }

    header('Location: '.$pageURL.'?continent='.Session::get('isppr_continent').'&country='.Session::get('isppr_country').'&state='.Session::get('isppr_state').'&city='.Session::get('isppr_city')) ;

?>