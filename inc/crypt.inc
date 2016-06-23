<?

function encrypto($string,$password) {

  $td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
  $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);

  mcrypt_generic_init ($td, $password, $iv);

  $encrypted = mcrypt_generic ($td, $string);

  mcrypt_generic_end ($td);

 return($encrypted);

}

function decrypto($encrypted,$password) {

  $td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, "");
  $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);

  mcrypt_generic_init ($td, $password, $iv);
  
  $decrypted = mdecrypt_generic ($td, $encrypted);

  mcrypt_generic_end ($td);

  return($decrypted);

}

?>
