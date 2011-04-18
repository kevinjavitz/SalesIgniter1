<?php
class FileCache
{
	static public function serve($key, $allowedLifeTime = 86400, $forcedLoad = false)
	{
		$fileName = sysConfig::getDirFsCatalog() . 'cache/' . md5($key) . '.cache';
		if (self::hasValidAge($fileName, $allowedLifeTime, null, $forcedLoad))
		{
			//header('Content-Type: text/css');
			header('Cache-Control:max-age=0');
			header('Connection:Keep-Alive');
			header('Content-Encoding:gzip');
			header('Content-Length:' . filesize($fileName));
			header('Date:' . date('D, j M Y h:i:s T'));
			//header('ETag:"pub1302566400;gz"');
			header('Expires:' . date('D, j M Y h:i:s T', filemtime($fileName) + $allowedLifeTime));
			header('Keep-Alive:timeout=5, max=100');
			header('Last-Modified:' . date('D, j M Y h:i:s T', filemtime($fileName)));
			header('Pragma:no-cache');
			header('Server:Apache');
			header('Vary:Accept-Encoding');

			readfile($fileName);
			exit;
		}
	}

	static public function save($key, $fileData)
	{
		$fileName = sysConfig::getDirFsCatalog() . 'cache/' . md5($key) . '.cache';
		$bytesWritten = file_put_contents($fileName, $fileData, LOCK_EX);

		if ($bytesWritten !== false) return true;
		else return false;
	}

	static public function hasValidAge($fileName, $allowedLifeTime, $forcedExpiration = false, $forcedValidation = false)
	{
		$status = false;

		if ($forcedExpiration) $status = false;
		elseif (($forcedValidation) && (file_exists($fileName))) $status = true;

		else
		{
			$lifeTime = self::fileAge($fileName);
			if ($lifeTime < $allowedLifeTime) $status = true;
		}

		return $status;
	}

	static public function fileAge($fileName)
	{
		return time() - self::timeModified($fileName);
	}

	static public function timeModified($fileName)
	{
		$uTime = 0;

		clearstatcache();

		if (file_exists($fileName))
		{
			$uTime = filemtime($fileName);
		}

		return $uTime;
	}
}
