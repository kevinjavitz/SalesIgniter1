<?php
	function updateProgressBar($name, $message, $percent){
		$Qcheck = tep_db_query('select name from progress_bar where name="' . $name . '"');
		if (tep_db_num_rows($Qcheck)){
			tep_db_query('update progress_bar set message = "' . addslashes($message) . '", percentage = "' . $percent*100 . '" where name = "' . $name . '"');
		}else{
			tep_db_query('insert into progress_bar (message, percentage, name) values ("' . addslashes($message) . '", "' . $percent*100 . '", "' . $name . '")');
		}
	}

	$LeftSide = $_GET['left'];
	$RightSide = $_GET['right'] . '.zip';
	$progressBarName = 'fileCompare';
	
	if ($LeftSide == 'current'){
		$LeftSide = sysConfig::getDirFsCatalog();
		$error = false;
		$Snapshot = new ZipArchive();
		if ($Snapshot->open(sysConfig::getDirFsCatalog() . 'snapshots/' . $RightSide) !== true){
			$error = true;
			$messageStack->addSession('pageStack', 'Unable to open zip file.<br>' . sysConfig::getDirFsCatalog() . 'snapshots/' . $LeftSide, 'error');
		}else{
			$tempDir = sysConfig::getDirFsCatalog() . 'temp/compare/' . date('Ymdhis');
			updateProgressBar($progressBarName, 'Extracting Zip File To Temp Directory<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $tempDir) . '</div>', 0);
			if ($Snapshot->extractTo($tempDir) === false){
				$error = true;
				$messageStack->addSession('pageStack', 'There was an error extracting the zip file To:<br><br><div style="width:300px;word-wrap:break-word;">' . $tempDir . '</div>', 'error');
			}
			$Snapshot->close();
		}
		
		if ($error === false){
			//ignore_user_abort(TRUE);
			set_time_limit(60*5);
			
			$ignore = array(
				sysConfig::getDirFsCatalog() . '.git/',
				sysConfig::getDirFsCatalog() . 'min/cache/',
				sysConfig::getDirFsCatalog() . 'temp/',
				sysConfig::getDirFsCatalog() . 'snapshots/',
				sysConfig::getDirFsCatalog() . 'images/',
				sysConfig::getDirFsCatalog() . 'ext/modules/payment/paypal_ipn/error_log',
				sysConfig::getDirFsCatalog() . 'error_log',
			);
		
			$Report = new CompareReports();
			$Report->left_side = $LeftSide;
			$Report->right_side = sysConfig::getDirFsCatalog() . 'snapshots/' . $RightSide;
			
			$RootDir = new RecursiveDirectoryIterator($LeftSide);
			$Files = new RecursiveIteratorIterator($RootDir, RecursiveIteratorIterator::SELF_FIRST);
			updateProgressBar($progressBarName, 'Calculating Total Number Of Files', 0);
			$numOfFiles = 0;
			foreach ($Files as $File){
				$numOfFiles++;
			}
			
			$fileCount = 0;
			foreach ($Files as $File){
				$fullPath = $File->getPathname();
				$process = true;
				foreach($ignore as $path){
					if (substr($fullPath, 0, strlen($path)) == $path){
						$process = false;
					}
				}
			
				$fileCount++;
				if ($process === true){
					$internalPath = str_replace(sysConfig::getDirFsCatalog(), '', $fullPath);
					
					if ($File->isDir() === true){
					}elseif ($File->isFile() === true){
						$curFilePath = $File->getPathname();
						$zipFilePath = $tempDir . '/' . $internalPath;
						
						updateProgressBar($progressBarName, 'Reading Right Side File<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $zipFilePath) . '</div>', number_format($fileCount/$numOfFiles, 2));
						
						if (file_exists($zipFilePath)){
							$rightSideFileContent = file_get_contents($zipFilePath);
							
							updateProgressBar($progressBarName, 'Reading Left Side File<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $curFilePath) . '</div>', number_format($fileCount/$numOfFiles, 2));
							
							if (file_exists($curFilePath)){
								$leftSideFileContent = file_get_contents($curFilePath);
							
								updateProgressBar($progressBarName, 'Comparing Files<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $curFilePath) . '</div><br>To<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $zipFilePath) . '</div>', number_format($fileCount/$numOfFiles, 2));
								for($i=0; $i<sizeof($leftSideFileContent); $i++){
									updateProgressBar($progressBarName, 'Comparing Files<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $curFilePath) . '</div><br>To<br><br><div style="width:300px;word-wrap:break-word;">' . str_replace(sysConfig::getDirFsCatalog(), '', $zipFilePath) . '</div><br><br>Line: ' . $i, number_format($fileCount/$numOfFiles, 2));
									if (!isset($rightSideFileContent[$i]) && !isset($leftSideFileContent[$i])){
										continue;
									}elseif (
										!isset($rightSideFileContent[$i]) && isset($leftSideFileContent[$i]) ||
										isset($rightSideFileContent[$i]) && !isset($leftSideFileContent[$i]) || 
										$rightSideFileContent[$i] != $leftSideFileContent[$i]
									){
										$ReportDiff = new CompareReportsDiffs();
										$ReportDiff->message = 'Found at least one difference between the files';
										$ReportDiff->left_file = $curFilePath;
										$ReportDiff->right_file = $zipFilePath;
										$Report->CompareReportsDiffs->add($ReportDiff);
										break;
									}
								}
							}else{
								$ReportDiff = new CompareReportsDiffs();
								$ReportDiff->message = 'Missing File On Left Side';
								$ReportDiff->left_file = $curFilePath;
								$ReportDiff->right_file = $zipFilePath;
							
								$Report->CompareReportsDiffs->add($ReportDiff);
							}
						}else{
							$ReportDiff = new CompareReportsDiffs();
							$ReportDiff->message = 'Missing File On Right Side';
							$ReportDiff->left_file = $curFilePath;
							$ReportDiff->right_file = $zipFilePath;
							
							$Report->CompareReportsDiffs->add($ReportDiff);
						}
					}
				}
			}
		}
	}
	
	if (isset($Report)){
		updateProgressBar($progressBarName, 'Saving Report', 1);
		$Report->save();
		$json = array(
			'success' => true,
			'report_id' => $Report->report_id
		);
		//unlink($tempDir);
		tep_db_query('delete from progress_bar where name = "' . $progressBarName . '"');
	}else{
		$json = array(
			'success' => false
		);
	}
	
	EventManager::attachActionResponse($json, 'json');
?>