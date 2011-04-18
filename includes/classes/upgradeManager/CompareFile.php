<?php
	class UpgradeManagerCompareFile {

		public function __construct($rootPath, $upgradePath){
			$this->rootPath = $rootPath;
		    $this->upgradePath = $upgradePath;
		    $this->backupPath = sysConfig::getDirFsCatalog() . 'backups/upgrades/' . date('Ymdhis') . '/';

			/*
			 * Possible Options
			 *   Replace   - Will replace the file and put the old one in a safe directory
			 *   Report    - Will report on the difference but no update will happen
			 *   Update    - Will attempt to do a line by line update and put the old file in a safe directory
			 */
			//$this->upgradeMethod = sysConfig::get('SYSTEM_UPGRADE_DIFF_HANDLE_METHOD');
			$this->upgradeMethod = 'Replace';
			$this->dirPerms = 0755;

			$checkDirs = array(
				'backups/',
				'backups/upgrades/',
				'backups/upgrades/' . date('Ymdhis') . '/'
			);
			foreach($checkDirs as $dir){
				if (!is_dir($this->rootPath . $dir)){
					mkdir($this->rootPath . $dir, $this->dirPerms);
				}
			}
		}

		public function compare($filePath){
			$old = $this->rootPath . $filePath;
			$new = $this->upgradePath . $filePath;
			
			$oldExists = file_exists($old);
			$newExists = file_exists($new);
			
			if ($oldExists && !$newExists){
				$this->removeFile($filePath);
			}elseif (!$oldExists && $newExists){
				$this->addFile($filePath);
			}elseif ($this->upgradeMethod == 'Replace'){
				$this->replaceFile($filePath);
			}else{
				switch($this->upgradeMethod){
					case 'Report':
						$this->compareContentsReport($filePath);
						break;
					case 'Update':
						$this->compareContentsUpdate($filePath);
						break;
				}
			}
		}
		
		private function compareContentsReport($data){
			for($i=0; $i<sizeof($data['oldContent']); $i++){
				if (!isset($data['newContent'][$i]) && !isset($data['oldContent'][$i])){
					continue;
				}elseif (
					!isset($data['newContent'][$i]) && isset($data['oldContent'][$i]) ||
					isset($data['newContent'][$i]) && !isset($data['oldContent'][$i]) || 
					$data['newContent'][$i] != $data['oldContent'][$i]
				){
					$diffs = array(
						'old' => $this->rootPath . $data['file'],
						'new' => $this->upgradePath . $data['file']
					);
					break;
				}
			}
			return $diffs;
		}
		
		private function compareContentsUpdate($data){
			$newFileContent = '';
			$inClientComment = false;
			/*
			 * @TODO: Need to deal with multi-line commands, like htmlBase and doctrine
			 */
			for($i=0; $i<sizeof($data['oldContent']); $i++){
				if (!isset($data['newContent'][$i]) && !isset($data['oldContent'][$i])){
					continue;
				}elseif (
					!isset($data['newContent'][$i]) && isset($data['oldContent'][$i]) ||
					isset($data['newContent'][$i]) && !isset($data['oldContent'][$i]) || 
					$data['newContent'][$i] != $data['oldContent'][$i]
				){
					if (
						stristr($data['oldContent'][$i], '/* custom edit') && 
						stristr($data['oldContent'][$i], '--BEGIN-- */')
					){
						$inClientComment = true;
					}
					
					if ($inClientComment === false){
						$newFileContent .= $data['newContent'][$i];
					}else{
						$newFileContent .= $data['oldContent'][$i];
					}
					
					if (
						stristr($data['oldContent'][$i], '/* custom edit') && 
						stristr($data['oldContent'][$i], '--END-- */')
					){
						$inClientComment = false;
					}
				}else{
					$newFileContent .= $data['newContent'][$i];
				}
			}
			
			if (!empty($newFileContent)){
				$this->writeFiles(array(
					'file' => $data['file'],
					'newContent' => $newFileContent
				));
			}
		}
		
		private function backupFile($filePath){
			global $messageStack;
			$this->checkBackupDirectories($filePath);

			//echo 'Backing Up File: ' . $this->rootPath . $filePath . "\n" . 'To: ' . $this->backupPath . $filePath . "\n\n";
			if (!copy($this->rootPath . $filePath, $this->backupPath . $filePath)){
				if (is_dir($this->rootPath . $filePath) || is_dir($this->backupPath . $filePath)){
					echo 'ERROR: Directory Passed To "' . __FUNCTION__ . '"' . "\n" . 
					'Arg #1: ' . $this->rootPath . $filePath . "\n" . 
					'Arg #2: ' . $this->backupPath . $filePath . "\n";
				}else{
					echo 'ERROR: Copy Failed In Function "' . __FUNCTION__ . '"' . "\n" . 
					'Arg #1: ' . $this->rootPath . $filePath . "\n" . 
					'Arg #2: ' . $this->backupPath . $filePath . "\n";
				}
				if ($messageStack->size('footerStack') > 0){
					echo $messageStack->output('footerStack');
				}
				die();
			}
			
			return file_exists($this->backupPath . $filePath);
		}
		
		private function removeFile($filePath){
			if ($this->backupFile($filePath)){
				//echo 'Removing File: ' . $this->rootPath . $filePath . "\n\n";
				unlink($this->rootPath . $filePath);
			}
		}
		
		private function addFile($filePath){
			global $messageStack;
			$this->checkRootDirectories($filePath);
			//echo 'Adding File: ' . $this->upgradePath . $filePath . "\n" . 'To: ' . $this->rootPath . $filePath . "\n\n";
			
			if (!copy($this->upgradePath . $filePath, $this->rootPath . $filePath)){
				if (is_dir($this->rootPath . $filePath) || is_dir($this->backupPath . $filePath)){
					echo 'ERROR: Directory Passed To "' . __FUNCTION__ . '"' . "\n" . 
					'Arg #1: ' . $this->backupPath . $filePath . "\n" . 
					'Arg #2: ' . $this->rootPath . $filePath . "\n";
				}else{
					echo 'ERROR: Copy Failed In Function "' . __FUNCTION__ . '"' . "\n" . 
					'Arg #1: ' . $this->backupPath . $filePath . "\n" . 
					'Arg #2: ' . $this->rootPath . $filePath . "\n";
				}
				if ($messageStack->size('footerStack') > 0){
					echo $messageStack->output('footerStack');
				}
				die();
			}
		}
		
		private function replaceFile($filePath){
			global $messageStack;
			if ($this->backupFile($filePath)){
				//echo 'Replacing File: ' . $this->rootPath . $filePath . "\n" . 'With: ' . $this->upgradePath . $filePath . "\n\n";
				if (!is_dir($this->rootPath . $filePath)){
					unlink($this->rootPath . $filePath);
					copy(
						$this->upgradePath . $filePath,
						$this->rootPath . $filePath
					);
				}else{
					echo 'ERROR: Directory Passed To "' . __FUNCTION__ . '"' . "\n" . 
					'unlink Arg: ' . $this->rootPath . $filePath . "\n" . 
					'Arg #1: ' . $this->upgradePath . $filePath . "\n" . 
					'Arg #2: ' . $this->rootPath . $filePath . "\n";
					if ($messageStack->size('footerStack') > 0){
						echo $messageStack->output('footerStack');
					}
					die();
				}
			}
		}
		
		private function writeFiles($data){
			if ($this->backupFile($data['file'])){
				//echo 'Overwriting File: ' . $this->rootPath . $data['file'] . "\n" . 'Using: ' . $this->upgradePath . $data['file'] . "\n\n";
				$replacedFile = fopen($this->rootPath . $data['file'], 'w+');
				ftruncate($replacedFile, -1);
				fwrite($replacedFile, $data['newContent']);
				fclose($replacedFile);
			}
		}
		
		public function checkBackupDirectories($path){
			$folders = explode('/', $path);
			array_pop($folders);

			$lastFolder = '';
			foreach($folders as $folder){
				if (!is_dir($this->backupPath . $lastFolder . $folder)){
					//echo 'Making Dir: ' . $this->backupPath . $lastFolder . $folder . "\n\n";
					mkdir($this->backupPath . $lastFolder . $folder, $this->dirPerms);
				}
				$lastFolder .= $folder . '/'; 
			}
		}
		
		public function checkRootDirectories($path){
			$folders = explode('/', $path);
			array_pop($folders);

			$lastFolder = '';
			foreach($folders as $folder){
				if (!is_dir($this->rootPath . $lastFolder . $folder)){
					//echo 'Making Dir: ' . $this->rootPath . $lastFolder . $folder . "\n\n";
					mkdir($this->rootPath . $lastFolder . $folder, $this->dirPerms);
				}
				$lastFolder .= $folder . '/'; 
			}
		}
	}
?>