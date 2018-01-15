<?php
/**
@auth:	amjad
@date:	00-00-0000
@info:	to encrypte and decrypt the password of the user and the client in physiotec system.

*/
class Cipher {
        
	private $securekey, $iv;
	private $className = 'Cipher';

    //-------------------------------------------------------------------------------------------------------------	
	function __construct($textkey) {
		$this->securekey = hash('sha256', $textkey, TRUE);
		#$size     = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
		#$this->iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
		$this->iv = '**2<81>‘š<8f>ÞDÞóàðJ';
	}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassName(){
		return $this->className;
		}

	//-------------------------------------------------------------------------------------------------------------	
	public function getClassObject(){
		return $this;
		}

    //-------------------------------------------------------------------------------------------------------------	
	public function encrypt($input) {
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
		}

        //-------------------------------------------------------------------------------------------------------------	
	public function decrypt($input) {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
		}
}


//END
