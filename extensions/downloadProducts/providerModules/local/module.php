<?php
class DownloadProviderLocal extends DownloadProviderModule {

	public function __construct($config = false) {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Local');
		$this->setDescription('Downloads are stored on this server');
		
		$this->init('local');
		
		if ($config !== false && is_array($config)){
			$this->setProviderConfig($config);
		}
	}
	
	public function getStorageFolder(){
		$folder = $this->getConfigData('MODULE_DOWNLOAD_PROVIDER_LOCAL_FOLDER');
		if (substr($folder, 1) != '/' && substr($folder, 1) != '\\'){
			$folder = sysConfig::getDirFsCatalog() . $folder;
		}
		if (substr($folder, -1) != '/' && substr($folder, -1) != '\\'){
			$folder .= '/';
		}
		return $folder;
	}
	
	public function processDownload($dInfo){
		$fileExt = substr($dInfo['file_name'], strpos($dInfo['file_name'], '.') + 1);
		$fileName = str_replace(' ', '_', $dInfo['display_name'] . '.' . $fileExt);
		
		header('Content-type: ' . $this->getHeaderContentType($fileExt));
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		header('Expires: Fri, 01 Jan 2010 05:00:00 GMT');
		
		if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false){
			header('Cache-Control: no-cache');
			header('Pragma: no-cache');
		}
				
		readfile($this->getStorageFolder() . $dInfo['file_name']);

		return true;
	}
	
	public function getFileBrowser(){
		$list = '<ul style="list-style:none;padding:0;margin:0;">';
		$this->recurseDir($this->getStorageFolder(), &$list, true);
		$this->recurseRootFiles($this->getStorageFolder(), &$list);
		$list .= '</ul>';
		
		return $list;
	}
	
	public function recurseDir($dir, &$list, $isRoot = false){
		$Dir = new DirectoryIterator($dir);
		foreach($Dir as $d){
			if ($d->isDot() || ($isRoot === true && $d->isFile() === true)) continue;
			
			if ($d->isDir()){
				$list .= '<li style="border: 1px solid transparent">' . 
					'<span class="ui-icon ui-icon-folder-collapsed" style="vertical-align:middle;"></span>' . 
					'<span class="providerFolder" style="line-height:16px;vertical-align:middle;">' . $d->getBasename() . '</span>' . 
					'<ul style="list-style:none;padding:0;margin:0;margin-left:16px">';
					
				$this->recurseDir($d->getPathname(), &$list);
				
				$list .= '</ul></li>';
			}else{
				$list .= '<li style="border: 1px solid transparent" data-file_path="' . str_replace($this->getStorageFolder(), '', dirname($d->getPathname())) . '/' . $d->getBasename() . '">' . 
					'<span class="ui-icon ui-icon-document" style="vertical-align:middle;"></span>' . 
					'<span class="providerFile" style="line-height:16px;vertical-align:middle;">' . $d->getBasename() . '</span>' . 
				'</li>';
			}
		}
	}
	
	public function recurseRootFiles($dir, &$list){
		$Dir = new DirectoryIterator($dir);
		foreach($Dir as $d){
			if ($d->isDot() || $d->isDir()) continue;
			
			$list .= '<li style="border: 1px solid transparent" data-file_path="' . $d->getBasename() . '">' . 
				'<span class="ui-icon ui-icon-document" style="vertical-align:middle;"></span>' . 
				'<span class="providerFile" style="line-height:16px;vertical-align:middle;">' . $d->getBasename() . '</span>' . 
			'</li>';
		}
	}
}
?>