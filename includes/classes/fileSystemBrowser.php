<?php
class fileSystemBrowser {
	
	public function __construct($directory, $recursive = false){
		if ($recursive === true){
			$this->iterator = new RecursiveDirectoryIterator($directory);
		}else{
			$this->iterator = new DirectoryIterator($directory);
		}
	}
	
	public function fileExists($file = null){
		if (is_null($file) === true) return false;
		
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isDir()){
				continue;
			}
			
			if ($fileObj->getFilename() == $file){
				$this->iterator->rewind();
				return true;
			}
		}
		$this->iterator->rewind();
		return false;
	}
	
	public function dirExists($dir = null){
		if (is_null($dir) === true) return false;
		
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isFile()){
				continue;
			}
			
			if ($fileObj->getBasename() == $dir){
				$this->iterator->rewind();
				return true;
			}
		}
		$this->iterator->rewind();
		return false;
	}
	
	public function getDirectories($exclude = array()){
		$return = array();
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isFile() || (!empty($exclude) && in_array($fileObj->getBasename(), $exclude))){
				continue;
			}
			
			$return[] = array(
				'path' => $fileObj->getPath(),
				'basename' => $fileObj->getBasename()
			);
		}
		$this->iterator->rewind();
		return $return;
	}
	
	public function getFiles($exclude = array()){
		$return = array();
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isDir() || (!empty($exclude) && in_array($fileObj->getFilename(), $exclude))){
				continue;
			}
			
			$return[] = array(
				'path' => $fileObj->getPath(),
				'fileName' => $fileObj->getFilename(),
				'fileName_noExt' => substr($fileObj->getFilename(), 0, strpos($fileObj->getFilename(), '.'))
			);
		}
		$this->iterator->rewind();
		return $return;
	}
	
	public function findFileByExtension($extension = null){
		$return = array();
		if (is_null($extension) === true) return $return;
		
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isDir()){
				continue;
			}
			
			$fileName = $fileObj->getBasename();
			if (substr($fileName, strpos($fileName, '.') + 1, strlen($fileName)) != $extension){
				continue;
			}
			
			$return[] = $fileObj;
		}
		$this->iterator->rewind();
		return $return;
	}
	
	public function findFileByName($name = null){
		$return = array();
		if (is_null($name) === true) return $return;
		
		foreach($this->iterator as $fileObj){
			if ($fileObj->isDot() || $fileObj->isDir() || $fileObj->getFilename() != $name){
				continue;
			}
			
			$return[] = $fileObj;
		}
		$this->iterator->rewind();
		return $return;
	}
	
	public function findByLastModified($date = null){
		die('Not implemented yet, guess there is a reason now');
	}
	
	public function findByDateCreated($date = null){
		die('Not implemented yet, guess there is a reason now');
	}
	
	public function findByPermissions($perms = null){
		die('Not implemented yet, guess there is a reason now');
	}
	
	public function findByGroup($group = null){
		die('Not implemented yet, guess there is a reason now');
	}
	
	public function findByOwner($owner = null){
		die('Not implemented yet, guess there is a reason now');
	}
}
?>