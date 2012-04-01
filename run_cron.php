<?php

putenv('SHELL=/bin/bash');
putenv('TERM=vt100');
set_time_limit(0);

require('includes/application_top.php');
error_reporting(0);
foreach (glob("cron/*.php") as $filename){
		require $filename;
   }
require('includes/application_bottom.php');
?>