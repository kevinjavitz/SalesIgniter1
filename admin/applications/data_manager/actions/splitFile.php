<?php
die('Not supported yet, and is it even used enough to build?');
if ($uploaded === true && $split==1) {
	//*******************************
	//*******************************
	// UPLOAD AND SPLIT FILE
	//*******************************
	//*******************************
	// move the file to where we can work with it
	$file = tep_get_uploaded_file('usrfl');
	//echo "Trying to move file...";
	if (is_uploaded_file($file['tmp_name'])) {
		tep_copy_uploaded_file($file, DIR_FS_DOCUMENT_ROOT . $tempdir);
	}

	$infp = fopen(DIR_FS_DOCUMENT_ROOT . $tempdir . $usrfl_name, "r");

	//toprow has the field headers
	$toprow = fgets($infp,32768);

	$filecount = 1;

	echo sprintf(sysLanguage::get('TEXT_INFO_CREATING_SPLIT'), $filecount);
	$tmpfname = DIR_FS_DOCUMENT_ROOT . $tempdir . "EP_Split" . $filecount . ".txt";
	$fp = fopen( $tmpfname, "w+");
	fwrite($fp, $toprow);

	$linecount = 0;
	$line = fgets($infp,32768);
	while ($line){
		// walking the entire file one row at a time
		// but a line is not necessarily a complete row, we need to split on rows that have "EOREOR" at the end
		$line = str_replace('"EOREOR"', 'EOREOR', $line);
		fwrite($fp, $line);
		if (strpos($line, 'EOREOR')){
			// we found the end of a line of data, store it
			$linecount++; // increment our line counter
			if ($linecount >= $maxrecs){
				echo sprintf(sysLanguage::get('TEXT_INFO_ADDED_RECORDS'), $linecount);
				$linecount = 0; // reset our line counter
				// close the existing file and open another;
				fclose($fp);
				// increment filecount
				$filecount++;
				echo sprintf(sysLanguage::get('TEXT_INFO_CREATING_SPLIT'), $filecount);
				$tmpfname = DIR_FS_DOCUMENT_ROOT . $tempdir . "EP_Split" . $filecount . ".txt";
				//Open next file name
				$fp = fopen( $tmpfname, "w+");
				fwrite($fp, $toprow);
			}
		}
		$line=fgets($infp,32768);
	}
	echo "Added $linecount records and closing file...<br><br> ";
	fclose($fp);
	fclose($infp);

	echo sysLanguage::get('TEXT_INFO_SPLIT_FILE');
}
?>