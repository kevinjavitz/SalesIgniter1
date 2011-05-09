<?php
class SystemCacheFile {
	private $data = array();

	public function __construct($key, $path){
		$this->key = str_replace(' ', '-', $key);
		$this->fileName = $this->key . '.cache';
		$this->realPath = $path;

		$filename = $this->realPath . $this->fileName;
		if (file_exists($filename) && is_readable($filename)){
			$data = file_get_contents($filename);
			$this->data = @unserialize($data);
		}
	}

	public function setData($key, $data){
		$this->data[$key] = $data;
	}

	public function hasData($key){
		return array_key_exists($key, $this->data);
	}

	public function hasExpired(){
		return (empty($this->data) || time() > $this->data['expires']);
	}

	public function getData($key){
		if (isset($this->data[$key])){
			return $this->data[$key];
		}
		return false;
	}

	public function save(){
		$file = fopen($this->realPath . $this->fileName,'w');
		if (!$file) throw new Exception('Could not write to cache');
		// Serializing along with the TTL
		$data = serialize($this->data);
		if (fwrite($file, $data)===false) {
			throw new Exception('Could not write to cache');
		}
		fclose($file);
	}

	public function clear(){
		unlink($this->realPath . $this->fileName);
	}
}

class SystemCacheApc {

}

class SystemCacheMemcache {

}

class SystemCache {
	private $cacheDriver = 'file';
	private $cacheKey = false;
	private $cachePath = false;
	private $contentType = false;
	private $CacheClass = false;
	private $expires = false;
	private $lastModified = false;
	private $cacheContent = '';
	private $addedHeaders = array();

	public function __construct($key = '', $path = ''){
		if (!empty($key)){
			$this->cacheKey = $key;
		}else{
			$this->cacheKey = md5('no-key-' . $_SERVER['REQUEST_URI'] . '-' . $_SERVER['QUERY_STRING']);
		}

		if (!empty($path)){
			$this->cachePath = $path;
		}else{
			$this->cachePath = realpath(dirname(__FILE__) . '/../../') . '/cache/';
		}
	}

	public function setDriver($driver){
		$this->cacheDriver = $driver;
	}

	public function setAddedHeaders($headers){
		$this->addedHeaders = $headers;
		if ($this->CacheClass){
			$this->CacheClass->setData('addedHeaders', $headers);
		}
	}

	public function setContentType($type){
		$this->contentType = $type;
		if ($this->CacheClass){
			$this->CacheClass->setData('contentType', $type);
		}
	}

	public function setExpires($time){
		$this->expires = $time;
		if ($this->CacheClass){
			$this->CacheClass->setData('expires', $time);
		}
	}

	public function setKey($key){
		$this->cacheKey = $key;
		if ($this->CacheClass){
			$this->CacheClass->setData('key', $key);
		}
	}

	public function setPath($path){
		$this->cachePath = $path;
		if ($this->CacheClass){
			$this->CacheClass->setData('path', $path);
		}
	}

	public function setContent($content){
		$this->cacheContent = $content;
		if ($this->CacheClass){
			$this->CacheClass->setData('content', $content);
		}
	}

	public function setLastModified($time){
		$this->lastModified = $time;
		if ($this->CacheClass){
			$this->CacheClass->setData('lastModified', $time);
		}
	}

	public function loadData(){
		$className = 'SystemCache' . ucfirst($this->cacheDriver);
		$this->CacheClass = new $className($this->cacheKey, $this->cachePath);
		if ($this->contentType !== false) $this->CacheClass->setData('contentType', $this->contentType);
		if ($this->expires !== false) $this->CacheClass->setData('expires', $this->expires);
		if ($this->lastModified !== false) $this->CacheClass->setData('lastModified', $this->lastModified);
		//if ($this->cacheKey !== false) $this->CacheClass->setData('key', $this->cacheKey);
		//if ($this->cachePath !== false) $this->CacheClass->setData('path', $this->cachePath);
		if ($this->cacheContent != '') $this->CacheClass->setData('content', $this->cacheContent);

		$expired = $this->CacheClass->hasExpired();
		if ($expired === true){
			if (file_exists($this->cachePath . $this->cacheKey)){
				$this->CacheClass->clear();
			}
		}
		return ($expired === false);
	}

	public function store(){
		if (!$this->CacheClass) $this->loadData();

		$this->CacheClass->save();
	}

	public function output($return = false, $wHeaders = false){
		if (!$this->CacheClass) $this->loadData();

		if ($wHeaders === true){
			$this->serveHeaders();
		}

		if ($return === false){
			echo $this->CacheClass->getData('content');
			return null;
		}
		return $this->CacheClass->getData('content');
	}

	public function clear($key, $path = ''){
		$className = 'SystemCache' . ucfirst($this->cacheDriver);
		$Cache = new $className($key);
		if (!empty($path)){
			$Cache->setPath($path);
		}
		$Cache->clear();
	}

	private function serveHeaders(){
		header("Cache-Control: public");
		if ($this->CacheClass->hasData('expires')){
			header("Expires: " . date(DATE_RFC822, $this->CacheClass->getData('expires')));
		}else
		if ($this->expires !== false){
			header("Expires: 0");
		}

		if ($this->CacheClass->hasData('contentType')){
			header('Content-Type: ' . $this->CacheClass->getData('contentType') . ';');
		}else
		if ($this->contentType !== false){
			header('Content-Type: ' . $this->contentType . ';');
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		//echo $_SERVER['HTTP_IF_MODIFIED_SINCE'] . ' :: ' . strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) . '<br>';
			if ($this->CacheClass->hasData('lastModified')){
				//echo $this->CacheClass->getData('lastModified') . ' :: ' . strtotime($this->CacheClass->getData('lastModified'));
				$lastModified = strtotime($this->CacheClass->getData('lastModified'));
				if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified){
					header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
					exit;
				}
			}
		}

		if ($this->CacheClass->hasData('lastModified')){
			header('Last-Modified: ' . $this->CacheClass->getData('lastModified'));
		}

		if ($this->CacheClass->hasData('addedHeaders')){
			foreach($this->CacheClass->getData('addedHeaders') as $k => $v){
				header($k . ': ' . $v);
			}
		}
	}
}