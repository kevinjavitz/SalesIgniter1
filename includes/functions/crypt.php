<?php
/*
  $Id: crypt.php,v 1.1.1.1 2004/03/04 23:40:48 ccwjr Exp $

    Copyright (c) 2005 

  Released under the GNU General Public License
*/

if (!defined('CC_KEY')) define('CC_KEY', 'Ys8oEwebV0CjrFoB');

if (!function_exists('parseCC')){
  function parseCC($cc){
      $cardNum = cc_decrypt($cc);
    return str_repeat('X', (strlen($cardNum) > 4 ? (strlen($cardNum) - 4) : 14)  ) . ((strlen($cardNum) > 4) ? substr($cardNum, -4) : '');
  }

  function cc_encrypt($text) {
   $key = CC_KEY;
   $key = md5($key);
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
   
  
  return base64_encode($crypttext);
   }
   
function cc_decrypt($enc) {
$key = CC_KEY;
$enc =base64_decode($enc);
$key = md5($key);
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $enc, MCRYPT_MODE_ECB, $iv);
$decrypttext1 = trim($decrypttext);
return ($decrypttext1) ;
}
}
?>