<?php
	function updateProgressBar($name, $message, $percent){
		$Qcheck = tep_db_query('select name from progress_bar where name="' . $name . '"');
		if (tep_db_num_rows($Qcheck)){
			tep_db_query('update progress_bar set message = "' . addslashes($message) . '", percentage = "' . $percent*100 . '" where name = "' . $name . '"');
		}else{
			tep_db_query('insert into progress_bar (message, percentage, name) values ("' . addslashes($message) . '", "' . $percent*100 . '", "' . $name . '")');
		}
	}

	$progressBarName = 'snapshotStatus';
	$error = false;
	
	if (is_writable(sysConfig::getDirFsCatalog() . 'snapshots/') === false){
		$error = true;
		$messageStack->addSession('pageStack', 'Unable to write to snapshot directory.<br>' . sysConfig::getDirFsCatalog() . 'snapshots/', 'error');
	}
	
	$fileName = 'snapshot_' . date('Ymd_his') . '_' . sysConfig::get('HTTP_DOMAIN_NAME') . '.zip';
	
	updateProgressBar($progressBarName, 'Creating Snapshot Zip File', 0);
	$Snapshot = new ZipArchive();
	if ($Snapshot->open(sysConfig::getDirFsCatalog() . 'snapshots/' . $fileName, ZIPARCHIVE::CREATE) !== TRUE){
		$error = true;
		$messageStack->addSession('pageStack', 'Unable to create zip file.<br>' . sysConfig::getDirFsCatalog() . 'snapshots/' . $fileName, 'error');
	}

	if ($error === false){
		$ignore = array(
			sysConfig::getDirFsCatalog() . '.git/',
			sysConfig::getDirFsCatalog() . 'temp/',
			sysConfig::getDirFsCatalog() . 'min/cache/',
			sysConfig::getDirFsCatalog() . 'snapshots/',
			sysConfig::getDirFsCatalog() . 'images/',
			sysConfig::getDirFsCatalog() . 'ext/modules/payment/paypal_ipn/error_log',
			sysConfig::getDirFsCatalog() . 'error_log',
		);
		
		updateProgressBar($progressBarName, 'Reading Root Directory Recursively', 0);
		$RootDir = new RecursiveDirectoryIterator(sysConfig::getDirFsCatalog());
		$Files = new RecursiveIteratorIterator($RootDir, RecursiveIteratorIterator::SELF_FIRST);
		
		updateProgressBar($progressBarName, 'Calculating Total Number Of Files', 0);
		$numOfFiles = 0;
		foreach ($Files as $File){
			$numOfFiles++;
		}
		
		$fileCount = 0;
		foreach ($Files as $File){
			if ($File->getBasename() == '.' || $File->getBasename() == '..'){
				$fileCount++;
				continue;
			}
			
			$fullPath = $File->getPathname();
			updateProgressBar($progressBarName, 'Checking if file/dir should be processed<br>' . str_replace(sysConfig::getDirFsCatalog(), '', $fullPath), number_format($fileCount/$numOfFiles, 2));
			$process = true;
			foreach($ignore as $path){
				if (substr($fullPath, 0, strlen($path)) == $path){
					$process = false;
				}
			}
			
			if ($process === true){
				$internalPath = str_replace(sysConfig::getDirFsCatalog(), '', $fullPath);
				if ($File->isDir() === true){
					updateProgressBar($progressBarName, 'Adding Dir To Zip<br>' . str_replace(sysConfig::getDirFsCatalog(), '', $internalPath), number_format($fileCount/$numOfFiles, 2));
					$Snapshot->addEmptyDir($internalPath . '/');
				}elseif ($File->isFile() === true){
					updateProgressBar($progressBarName, 'Adding File To Zip<br>' . str_replace(sysConfig::getDirFsCatalog(), '', $internalPath) . '<br><br>' . str_replace(sysConfig::getDirFsCatalog(), '', $fullPath), number_format($fileCount/$numOfFiles, 2));
					$Snapshot->addFile($fullPath, $internalPath);
				}
			}
			$fileCount++;
		}
		$Snapshot->close();
		updateProgressBar($progressBarName, 'Completed', number_format($fileCount/$numOfFiles, 2));
		chmod(sysConfig::getDirFsCatalog() . 'snapshots/' . $fileName, 0755);
		$messageStack->addSession('pageStack', 'Snapshot created successfully.', 'success');
		tep_db_query('delete from progress_bar where name = "' . $progressBarName . '"');
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
	//EventManager::attachActionResponse(itw_app_link(null, 'server_manager', 'default'), 'redirect');
?>