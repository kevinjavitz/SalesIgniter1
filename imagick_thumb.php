<?php
$cacheKey = 'image-' . md5($_GET['path'] . '-' . $_GET['imgSrc'] . '-' . $_GET['width'] . '-' . $_GET['height']);

	require('includes/classes/system_cache.php');
	$ImageCache = new SystemCache($cacheKey);
if ($ImageCache->loadData() === true){
	$ImageCache->output(false, true);
	exit;
}
else {
	if (!empty($_GET['imgSrc'])){
		if (isset($_GET['path']) && $_GET['path'] == 'rel'){
			if (substr($_GET['imgSrc'], 0, 1) == DIRECTORY_SEPARATOR){
				$_GET['imgSrc'] = substr($_GET['imgSrc'], 1);
			}
			$_GET['imgSrc'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . $_GET['imgSrc'];
		}
		
		if (file_exists($_GET['imgSrc'])){
			$img = new Imagick($_GET['imgSrc']);
			$img->setCompression(imagick::COMPRESSION_ZIP);
			$img->setImageCompressionQuality(50); 
			//$img->trimImage(.5);
			if( isset($_GET['bestFit'])){
				$bestFit = $_GET['bestFit'];
			}else{
				$bestFit = true;
			}
			if (isset($_GET['width']) && isset($_GET['height'])){
				$img->thumbnailImage($_GET['width'],$_GET['height'],$bestFit);
			}else{
				if (isset($_GET['width'])){
					$img->thumbnailImage($_GET['width'],0,true);
				}elseif (isset($_GET['height'])){
					$img->thumbnailImage(0,$_GET['height'],true);
				}
			}
			
			$path_parts = pathinfo($_GET['imgSrc']); 
			$ext = strtolower($path_parts['extension']);
			
			// Determine Content Type
			switch($ext){
				case 'gif':
					$ContentType = 'image/gif';
					break;
				case 'png':
					$ContentType = 'image/png';
					break;
				case 'jpeg':
				case 'jpg':
					$ContentType = 'image/jpeg';
					break;
				case 'bmp':
					$ContentType = 'image/bmp';
					break;
				default:
					header('Status: 404 Not Found');
					exit;
					break;
			}
			//header('Content-Transfer-Encoding: binary');
			//header('Content-Length: ' . filesize($_GET['imgSrc']));
			ob_start();
			echo $img;
			$imgContent = ob_get_contents();
			ob_end_clean();

			$ImageCache->setContentType($ContentType);
			$ImageCache->setContent($imgContent);
			$ImageCache->setExpires(time() + (60 * 60 * 24 * 7));
			$ImageCache->setLastModified(date(DATE_RSS, time()));
			$ImageCache->store();

			$ImageCache->output(false, true);
		}else{
			header('HTTP/1.0 404 Not Found');
		}
	}else{
		header('HTTP/1.0 404 Not Found');
	}
}
?>