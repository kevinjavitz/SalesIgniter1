<?php
	class DiffFile {
		
		public $testMode = false;
		public $filesToChange = array();
		public $firstChunk = false;
		public $fileAction = null;
		public $error = false;
		public $error_text = '';
		public $updatesDir = null;
		public $updatesDirFtp = null;
		
		public function __construct($updateZip){
			$this->finalActions = array();
			$this->updateZip = $updateZip;
			$this->updatesDir = sysConfig::getDirFsAdmin() . 'applications/ses_update/updates';
			$this->updatesDirFtp = str_replace(sysConfig::getDirFsCatalog(), '', $this->updatesDir);
			$this->errorInfo = array();

			$this->Ftp = new SystemFTP();
			$this->Ftp->connect();

			if ($this->error === false){
				$this->updateNumber = str_replace('.zip', '', basename($updateZip));
			}
		}
		
		public function storeError($errorInfo){
			$this->errorInfo[] = $errorInfo;
		}
		
		public function storeFatalError($errorInfo){
			$this->error = true;
			$this->errorInfo[] = $errorInfo;
		}
		
		public function processUpdate(){
			$ZipArchive = new ZipArchive();
			$ZipStatus = $ZipArchive->open($this->updateZip);
			if ($ZipStatus === true){
				$patchesArr = array();
				for($i = 0; $i < $ZipArchive->numFiles; $i++){
					$filePath = $ZipArchive->getNameIndex($i);
					if (dirname($filePath) == 'patches'){
						$patchesArr[substr(basename($filePath), 0, -6)] = $filePath;
					}
				}

				if (!empty($patchesArr)){
					ksort($patchesArr);
					
					foreach($patchesArr as $filePath){
						$this->diffFile = 'zip://' . $this->updateZip . '#' . $filePath;

						$diffs = $this->separateBlocks(file($this->diffFile));

						foreach($diffs as $diffCnt => $dInfo){
							$targetFile = $dInfo['oldFile'];
							
							if (!isset($this->finalActions[$targetFile])){
								$this->finalActions[$targetFile] = array('type' => 'ignore');
							}
							
							if ($dInfo['action'] == 'delete'){
								if (file_exists(sysConfig::getDirFsCatalog() . $targetFile)){
									$this->Ftp->deleteFile($targetFile);
								}
							}elseif ($dInfo['action'] == 'fromZip'){
								$this->Ftp->copyFile(
									'zip://' . $this->updateZip . '#' . $targetFile,
									$targetFile
								);
							}elseif ($dInfo['action'] == 'create' || $dInfo['action'] == 'update'){
								$this->lastLineChanged = -1;
								if ($dInfo['action'] == 'update'){
									$this->Ftp->makeWritable($targetFile);
									$currentFile = file(sysConfig::getDirFsCatalog() . $targetFile);
									$this->Ftp->unmakeWritable($targetFile);
									array_walk($currentFile, 'striprn');
								}else{
									$currentFile = array();
								}
								
								foreach($dInfo['blocks'] as $blockCnt => $bInfo){
									$this->doDiff(&$currentFile, $bInfo, $dInfo);
								}

								if ($this->hasFatalError() === false){
									$this->Ftp->updateFileFromString(
										$targetFile,
										implode("\n", $currentFile)
									);
								}
							}
							$diffs[$diffCnt] = null;
							unset($diffs[$diffCnt]);
						
							if ($this->hasFatalError() === true){
								break;
							}
						}

						if ($this->hasFatalError() === true){
							break;
						}
					}
					
					if ($this->hasFatalError() === false && $this->testMode === false){
						if (isset($_GET['forUpdater'])){
							Doctrine_Query::create()
							->update('Configuration')
							->set('configuration_value', '?', $this->updateNumber)
							->where('configuration_key = ?', 'SYSTEM_UPDATER_LAST_UPDATE')
							->execute();
						}else{
							$QcommitDate = Doctrine_Query::create()
							->select('update_latest_commit')
							->from('SesUpdates')
							->where('update_number = ?', $this->updateNumber)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
							Doctrine_Query::create()
							->update('Configuration')
							->set('configuration_value', '?', date(DATE_RSS, $QcommitDate[0]['update_latest_commit']))
							->where('configuration_key = ?', 'SYSTEM_LAST_UPDATE')
							->execute();
							$this->logMsg('Date Updated: ' . date(DATE_RSS, $QcommitDate[0]['update_latest_commit']));
						}
					}
				}
			}else{
				$this->storeFatalError(array(
					'resolution' => 'none',
					'message' => 'Unable to open zip ( ' . $this->getZipErrorText($ZipStatus) . ' )'
				));
			}
		}
		
		private function getZipErrorText($code){
			switch($code){
				case ZIPARCHIVE::ER_OK          : $errorMsg = 'No error.'; break;
				case ZIPARCHIVE::ER_MULTIDISK   : $errorMsg = 'Multi-disk zip archives not supported.'; break;
				case ZIPARCHIVE::ER_RENAME      : $errorMsg = 'Renaming temporary file failed.'; break;
				case ZIPARCHIVE::ER_CLOSE       : $errorMsg = 'Closing zip archive failed'; break;
				case ZIPARCHIVE::ER_SEEK        : $errorMsg = 'Seek error'; break;
				case ZIPARCHIVE::ER_READ        : $errorMsg = 'Read error'; break;
				case ZIPARCHIVE::ER_WRITE       : $errorMsg = 'Write error'; break;
				case ZIPARCHIVE::ER_CRC         : $errorMsg = 'CRC error'; break;
				case ZIPARCHIVE::ER_ZIPCLOSED   : $errorMsg = 'Containing zip archive was closed'; break;
				case ZIPARCHIVE::ER_NOENT       : $errorMsg = 'No such file.'; break;
				case ZIPARCHIVE::ER_EXISTS      : $errorMsg = 'File already exists'; break;
				case ZIPARCHIVE::ER_OPEN        : $errorMsg = 'Can\'t open file'; break;
				case ZIPARCHIVE::ER_TMPOPEN     : $errorMsg = 'Failure to create temporary file.'; break;
				case ZIPARCHIVE::ER_ZLIB        : $errorMsg = 'Zlib error'; break;
				case ZIPARCHIVE::ER_MEMORY      : $errorMsg = 'Memory allocation failure'; break;
				case ZIPARCHIVE::ER_CHANGED     : $errorMsg = 'Entry has been changed'; break;
				case ZIPARCHIVE::ER_COMPNOTSUPP : $errorMsg = 'Compression method not supported.'; break;
				case ZIPARCHIVE::ER_EOF         : $errorMsg = 'Premature EOF'; break;
				case ZIPARCHIVE::ER_INVAL       : $errorMsg = 'Invalid argument'; break;
				case ZIPARCHIVE::ER_NOZIP       : $errorMsg = 'Not a zip archive'; break;
				case ZIPARCHIVE::ER_INTERNAL    : $errorMsg = 'Internal error'; break;
				case ZIPARCHIVE::ER_INCONS      : $errorMsg = 'Zip archive inconsistent'; break;
				case ZIPARCHIVE::ER_REMOVE      : $errorMsg = 'Can\'t remove file'; break;
				case ZIPARCHIVE::ER_DELETED     : $errorMsg = 'Entry has been deleted'; break;
				default                         : $errorMsg = 'Unknown Error.';
			}
			return $errorMsg;
		}
		
		private function separateBlocks($diffFileArr){
			$diffs = array();
			$blockCnt = -1;
			$diffCnt = 0;
			array_walk($diffFileArr, 'striprn');
			foreach($diffFileArr as $k => $curLine){
				if (substr($curLine, 0, 10) == 'diff --git'){
					if (isset($diffs[$diffCnt])){
						$diffCnt++;
					}
					
					$files = explode(' ', substr($curLine, 11));
					$blockCnt = -1;
					$diffs[$diffCnt] = array(
						'beginLine' => $k,
						'endLine' => $k - 1,
						'oldFile' => trim($files[0]),
						'newFile' => trim($files[1]),
						'action' => 'update',
						'blocks' => array()
					);
				}elseif (isset($diffs[$diffCnt])){
					if (substr($curLine, 0, 17) == 'deleted file mode'){
						$diffs[$diffCnt]['action'] = 'delete';
					}elseif (substr($curLine, 0, 13) == 'new file mode'){
						$diffs[$diffCnt]['action'] = 'create';
					}elseif (
						substr($curLine, 0, 16) == 'GIT binary patch' && 
						$diffs[$diffCnt]['action'] != 'delete'
					){
						$diffs[$diffCnt]['action'] = 'fromZip';
					}elseif (
						$diffs[$diffCnt]['action'] != 'ignore' && 
						$diffs[$diffCnt]['action'] != 'fromZip' && 
						$diffs[$diffCnt]['action'] != 'delete'
					){
						if (substr($curLine, 0, 3) == '---'){
						}elseif (substr($curLine, 0, 3) == '+++'){
						}elseif (substr($curLine, 0, 2) == '@@'){
							$blockCnt++;
							$diffs[$diffCnt]['blocks'][$blockCnt]['srcInfo'] = $curLine;
						}elseif (isset($curLine{0}) && substr($curLine, 0, 3) != '-- '){
							if ($curLine{0} == ' ' || $curLine{0} == '-' || $curLine{0} == '+'){
								$diffs[$diffCnt]['blocks'][$blockCnt]['diff'][] = $curLine;
							}
						}
					}
				}
			}
			
			return $diffs;
		}
		
		public function doDiff(&$currentFile, $blockInfo, $dInfo){
			$blockSrcInfo = $blockInfo['srcInfo'];
			$blockLines = $blockInfo['diff'];
			$source = array();
			$dest = array();
			$this->blockHasError = false;
			
			$m = array();
			if (preg_match('/@@ -(\\d+)(,(\\d+))?\\s+\\+(\\d+)(,(\\d+))?\\s+@@/', $blockSrcInfo, $m)){
				$srcStart = (int) $m[1]-1;	// -1 because our arrays are 0 based
				$destStart = (int) $m[4]-1;	// -1 because our arrays are 0 based
				if (!isset($m[3]) || $m[3] === ''){
					$srcSize = 1;
				}else{
					$srcSize = (int) $m[3];
				}
				
				if (!isset($m[6]) || $m[6] === ''){
					$destSize = 1;
				}else{
					$destSize = (int) $m[6];
				}
			}else{
				$this->blockHasError = true;
				$this->storeError(array(
					'resolution' => 'none',
					'message' => 'Invalid source info line'
				));
			}
			
			if ($this->blockHasError === false && $this->hasFatalError() === false){
				$diffBlockIdx = 0;
				while ($srcSize > 0 || $destSize > 0){
					if (isset($blockLines[$diffBlockIdx])) {	// make sure we haven't reached the end of the diff array
						$type = substr($blockLines[$diffBlockIdx], 0, 1);
						$diffLine = substr($blockLines[$diffBlockIdx], 1);
						if ($diffLine === false){
							$diffLine = '';
						}
					}else{
						$this->blockHasError = true;
						$this->storeError(array(
							'resolution' => 'none',
							'message' => 'Unexpected end of block'
						));
						break;
					}
							
					if ($type == ' '){
						$source[] = $diffLine;
						$dest[] = $diffLine;
						--$srcSize;
						--$destSize;
					}elseif ($type == '-'){
						$source[] = $diffLine;
						--$srcSize;
					}elseif ($type == '+'){
						$dest[] = $diffLine;
						--$destSize;
					}elseif ($type == '\\'){
					}else{
						$this->blockHasError = true;
						$this->storeError(array(
							'resolution' => 'none',
							'message' => 'Unexpected end of block'
						));
						break;
					}
					$diffBlockIdx++;
				}
			}
			
			if ($this->blockHasError === false && $this->hasFatalError() === false){
				if (!empty($srcSize) || !empty($destSize) || (empty($source) && empty($dest))) {
					$this->blockHasError = true;
					$this->storeFatalError(array(
						'resolution' => 'none',
						'message' => 'Unexpected end of block'
					));
				}
				
				if ($this->blockHasError === false && $this->hasFatalError() === false){
					if (!empty($source)){
						$this->logMsg('Checking For Possible Modification Locations');
						$possibleLocations = $this->findPossibleLocations($currentFile, $source);
						
						if (empty($possibleLocations)) {
							$this->logMsg('Failed To Find Possible Modification Locations');
							$this->blockHasError = true;
							$this->storeError(array(
								'resolution' => 'compare',
								'message' => sprintf(sysLanguage::get('TEXT_ERROR_SOURCE_CHANGED'), $dInfo['oldFile'], $dInfo['beginLine']),
								'source'  => $source,
								'destination' => $dest
							));
						} elseif (count($possibleLocations)>1) {
							$this->blockHasError = true;
							$this->logMsg('Found Multiple Possible Modification Locations');
							if (!in_array($destStart, $possibleLocations)) {
								$this->storeError(array(
									'resolution' => 'compare',
									'message' => sprintf(sysLanguage::get('TEXT_ERROR_MULTIPLE_MATCHES'), $dInfo['oldFile'], $dInfo['endLine']),
									'source'  => $source,
									'destination' => $dest
								));
							}
						} elseif (count($possibleLocations)==1) {
							reset($possibleLocations);
							$destStart = current($possibleLocations);
						}
					}

					if ($this->blockHasError === false && $this->hasFatalError() === false){
						// if we are here then there was no error and we can apply the diff!!!
						array_splice($currentFile, $destStart, count($source), $dest);
						$this->lastLineChanged = $destStart + count($dest);
					}
				}
			}
		}

		private function findPossibleLocations($currentFile, $source){
			$possible_locations = array_keys($currentFile, strval($source[0]));
			foreach($possible_locations as $k => $possibleLineNum){
				foreach($source as $sourceLineNum => $sourceLine){
					if (
						$possibleLineNum < $this->lastLineChanged || 
						!isset($currentFile[$possibleLineNum + $sourceLineNum]) || 
						$sourceLine != $currentFile[$possibleLineNum + $sourceLineNum]
					){
						unset($possible_locations[$k]);
					}
				}
			}
			return $possible_locations;
		}

		public function __destruct(){
			$this->Ftp->disconnect();
		}

		public function hasError(){
			return (!empty($this->errorInfo));
		}
		
		public function hasFatalError(){
			return ($this->error === true);
		}
		
		public function getError(){
			$errMsg = 'The Following Errors Occured During Patch File Processing<br>' . 
			'To Resolve These Issues It Is Required That You Run A Compare On The Files Involved<br><br>';
			
			foreach($this->errorInfo as $err){
				$errMsg .= $err['message'] . '<br>';
			}
			
			return array(
				'resolution' => 'none',
				'message' => $errMsg
			);
		}
		
		public function logMsg($msg){
			if (file_exists(dirname(__FILE__) . '/update_log')){
				error_log($msg . "\n", 3, dirname(__FILE__) . '/update_log');
			}
		}
	}
?>
