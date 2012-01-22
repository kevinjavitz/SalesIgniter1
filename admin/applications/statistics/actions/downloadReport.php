<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=" . $_GET['report'] . ".csv");
header("Pragma: no-cache");
header("Expires: 0");

readfile(sysConfig::getDirFsCatalog() . 'admin/csv_export/salesReport.csv');

itwExit();
