<?php
	if(isset($Plan)){
		$Plan->streaming_allowed = (int)(isset($_POST['streaming_allowed']) ? $_POST['streaming_allowed'] : '0');
		$Plan->streaming_no_of_views = $_POST['streaming_no_of_views'];
		$Plan->streaming_views_period = $_POST['streaming_views_period'];
		$Plan->streaming_views_time = $_POST['streaming_views_time'];
		$Plan->streaming_views_time_period = $_POST['streaming_views_time_period'];
		$Plan->streaming_access_hours = $_POST['streaming_access_hours'];
		$Plan->save();
	}
?>