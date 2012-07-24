<?php
// image size and color
$width = '750';
$height = '1050';
$im = imagecreatetruecolor($width, $height);
$color1 = ImageColorAllocate($im,255,255,255);
$color2 = ImageColorAllocate($im,0,0,0);


// image creation
ImageFilledRectangle($im,0,0,$width,$height,$color1);

/*// determine numeric center of image
$size = ImageTTFBBox(45,0,sysConfig::getDirFsCatalog().'fonts/arial.ttf','yyyy');
$X = (77 - (abs($size[2]- $size[0])))/2;
$Y = ((77 - (abs($size[5] - $size[3])))/2 + (abs($size[5] - $size[3])));*/
$QCustomer = Doctrine_Query::create()
	->from('Customers')
	->where('customers_id = ?', $userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


$url = 'http://'.sysConfig::get('HTTP_HOST'). '/imagick_thumb.php?width=300&height=300&imgSrc='.'images/'.$QCustomer[0]['customers_photo'];
$ch = curl_init();
$timeout = 0;
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

// Getting binary data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

$image = curl_exec($ch);
curl_close($ch);

/*if(!empty($QCustomer[0]['customers_photo'])){
$path = pathinfo($QCustomer[0]['customers_photo']);
switch($path['extension']){
	case 'jpg':
	case 'jpeg':
		$im1 = imagecreatefromjpeg(sysConfig::getDirFsCatalog().'images/'.$QCustomer[0]['customers_photo']);
		break;
	case 'png':
		$im1 = imagecreatefrompng(sysConfig::getDirFsCatalog().'images/'.$QCustomer[0]['customers_photo']);
		break;
	case 'gif':
		$im1 = imagecreatefromgif(sysConfig::getDirFsCatalog().'images/'.$QCustomer[0]['customers_photo']);
		break;
}
*/
$im1 = imagecreatefromstring($image);

$url = 'http://'.sysConfig::get('HTTP_HOST'). '/imagick_thumb.php?width=300&height=300&imgSrc='.'images/'.sysConfig::get('STORE_LOGO');

$ch = curl_init();
$timeout = 0;
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

// Getting binary data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

$image = curl_exec($ch);
curl_close($ch);
$im3 = imagecreatefromstring($image);
/*
$path = pathinfo(sysConfig::get('STORE_LOGO'));
switch($path['extension']){
	case 'jpg':
	case 'jpeg':
		$im3 = imagecreatefromjpeg(sysConfig::getDirFsCatalog().'images/'.sysConfig::get('STORE_LOGO'));
		break;
	case 'png':
		$im3 = imagecreatefrompng(sysConfig::getDirFsCatalog().'images/'.sysConfig::get('STORE_LOGO'));
		break;
	case 'gif':
		$im3 = imagecreatefromgif(sysConfig::getDirFsCatalog().'images/'.sysConfig::get('STORE_LOGO'));
		break;
}
 */

imagecopy($im, $im1, 0, 0, 0, 0, imagesx($im1), imagesy($im1));
imagecopy($im, $im3, $width-imagesx($im3), 0, 0, 0, imagesx($im3), imagesy($im3));
ImageTTFText($im,30,0,0,305,$color2,sysConfig::getDirFsCatalog().'fonts/arial.ttf','Member Name:'.$userAccount->getFullName());
ImageTTFText($im,30,0,0,340,$color2,sysConfig::getDirFsCatalog().'fonts/arial.ttf','Member Number:'.$QCustomer[0]['member_number']);

require(sysConfig::getDirFsCatalog() . 'includes/classes/barcodes/Barcode39.php');

$bc = new Barcode39($QCustomer[0]['member_number']);

$img4 = $bc->draw('string');

//echo imagecreatefromstring($img4);
imagecopy($im, $img4, 0, 370, 0, 0, imagesx($img4), imagesy($img4));

Header('Content-Type: image/jpeg');
Imagejpeg($im);



?>