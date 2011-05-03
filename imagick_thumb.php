<?php
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	// if the browser has a cached version of this image, send 304
	header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
	exit;
}

	if (!empty($_GET['imgSrc'])){
		if (isset($_GET['path']) && $_GET['path'] == 'rel'){
			if (substr($_GET['imgSrc'], 0, 1) == DIRECTORY_SEPARATOR){
				$_GET['imgSrc'] = substr($_GET['imgSrc'], 1);
			}
			$_GET['imgSrc'] = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $_GET['imgSrc'];
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
					header('Content-Type: image/gif');
					break;
				case 'png':
					header('Content-Type: image/png');
					break;
				case 'jpeg':
				case 'jpg':
					header('Content-Type: image/jpeg');
					break;
				case 'bmp':
					header('Content-Type: image/bmp');
					break;
				default:
					header('Status: 404 Not Found');
					exit;
					break;
			}
			//header('Content-Transfer-Encoding: binary');
			//header('Content-Length: ' . filesize($_GET['imgSrc'])); 
			echo $img;
		}else{
			header('HTTP/1.0 404 Not Found');
		}
	}else{
		header('HTTP/1.0 404 Not Found');
	}
?>