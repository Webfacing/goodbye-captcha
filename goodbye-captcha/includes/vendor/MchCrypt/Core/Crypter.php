<?php

/* 
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

final class MchCrypt_Core_Crypter
{
	
    CONST CIPHER_DES          = 1;
    CONST CIPHER_RC2          = 2;
    CONST CIPHER_3DES         = 3;
    CONST CIPHER_ARC4         = 4;
    CONST CIPHER_GOST         = 5;
    CONST CIPHER_3WAY         = 6;
    CONST CIPHER_ENIGMA       = 7;
    CONST CIPHER_AES_128      = 8;
    CONST CIPHER_AES_192      = 9;
    CONST CIPHER_AES_256      = 10;
    CONST CIPHER_CAST_128     = 11;
    CONST CIPHER_CAST_256     = 12;
    CONST CIPHER_BLOWFISH     = 13;
    CONST CIPHER_VIGENERE     = 14;
    CONST CIPHER_SKIPJACK     = 15;
    CONST CIPHER_SIMPLEXOR    = 16;
    CONST CIPHER_RIJNDAEL_128 = 17;
    CONST CIPHER_RIJNDAEL_192 = 18;
    CONST CIPHER_RIJNDAEL_256 = 19;	



    CONST MODE_CBC    = 1;
    CONST MODE_CFB    = 2;
    CONST MODE_CTR    = 3;
    CONST MODE_ECB    = 4;
    CONST MODE_NCFB   = 5;
    CONST MODE_NOFB   = 6;
    CONST MODE_OFB    = 7;
	
    private static $arrCipherNames = array(
                                            self::CIPHER_DES          => 'DES',
                                            self::CIPHER_RC2          => 'RC2',
                                            self::CIPHER_3DES         => '3DES',
                                            self::CIPHER_ARC4         => 'ARC4',
                                            self::CIPHER_GOST         => 'GOST',
                                            self::CIPHER_3WAY         => '3-Way',
                                            self::CIPHER_ENIGMA       => 'Enigma',
                                            self::CIPHER_AES_128      => 'AES-128',
                                            self::CIPHER_AES_192      => 'AES-192',
                                            self::CIPHER_AES_256      => 'AES-256',
                                            self::CIPHER_CAST_128     => 'CAST-128',
                                            self::CIPHER_CAST_256     => 'CAST-256',
                                            self::CIPHER_BLOWFISH     => 'Blowfish',
                                            self::CIPHER_VIGENERE     => 'Vigenere',
                                            self::CIPHER_SKIPJACK     => 'Skipjack',
                                            self::CIPHER_SIMPLEXOR    => 'SimpleXOR',
                                            self::CIPHER_RIJNDAEL_128 => 'Rijndael-128',
                                            self::CIPHER_RIJNDAEL_192 => 'Rijndael-192',
                                            self::CIPHER_RIJNDAEL_256 => 'Rijndael-256',	
                                    );


    private static $arrModeNames = array(
                                            self::MODE_CBC  => 'CBC',
                                            self::MODE_CFB  => 'CFB',
                                            self::MODE_CTR  => 'CTR',
                                            self::MODE_ECB  => 'ECB',
											self::MODE_OFB  => 'OFB',
                                            self::MODE_NCFB => 'NCFB',
                                            self::MODE_NOFB => 'NOFB',
		
										);	


    private $cipherId       = null;
    private $cipherName     = null;

    private $secretKey      = null;

    private $encryptionMode = null;
    private $randomSalt     = null;

	
    public function __construct($cipherId = self::CIPHER_BLOWFISH, $encryptionModeId = self::MODE_CBC) 
    {
        $this->cipherId       = (int)$cipherId;
        $this->cipherName     = strtolower(self::$arrCipherNames[$cipherId]);
        $this->encryptionMode = strtolower(self::$arrModeNames[$encryptionModeId]);

//		if(!$this->isValidCipherId($cipherId) || (0 === $this->getCipherKeySize()))
//		{
//
//		}
		
    }

	
	public function getRandomSalt()
	{
		return null !== $this->randomSalt ? $this->randomSalt :  $this->randomSalt = MchCrypt::getRandomBytes($this->getCipherSaltSize());
	}
	
	public function getCipherName()
	{
		return $this->cipherName;
	}
	
    public function setSecretKey($strKey)
    {
        if(empty($strKey))
        {
            throw new InvalidArgumentException('The key cannot be empty!');
        }

//        if(!self::isMCryptExtensionLoaded())
//        {
//			return $this->secretKey = $strKey;
//        }
//
//        $keyLength = strlen($strKey);
//
//        $cipherSupportedKeySizes = mcrypt_module_get_supported_key_sizes($this->cipherName);
//
//        if(empty($cipherSupportedKeySizes))
//        {
//            if($keyLength <= $this->getCipherKeySize())
//            {
//				$this->secretKey = $strKey;
//				return;
//            }
//
//            throw new InvalidArgumentException("The size of the key must be between 1 and " . $this->getCipherKeySize() . " bytes!");
//        }
//
//        if(!in_array($keyLength, $cipherSupportedKeySizes))
//        {
//			throw new InvalidArgumentException('The accepted key sizes are: ' . implode( ' or ', $cipherSupportedKeySizes ));
//        }

        $this->secretKey = $strKey;
    }

    private function isValidCipherId($chiperId)
    {
        if(!isset(self::$arrCipherNames[$chiperId]))
        {
            throw new OutOfBoundsException('The chiperId parameter should be an integer between 1 and ' . count(self::$arrCipherNames));
        }

        return true;
    }


    public function encrypt($strTextToEncrypt) 
    {
		if(null === $this->secretKey)
		{
			throw new RuntimeException('Please provide a secret key for cryptor!');
		}
		
		if(empty($strTextToEncrypt))
		{
			throw new InvalidArgumentException('The data that will be encrypted cannot be empty!');
		}

		if(null === $this->randomSalt)
		{
			$this->randomSalt = $this->getRandomSalt();
		}
		
		
		if(!self::isMCryptExtensionLoaded())
		{
			
			$phpCryptCipher = new PhpCrypt($this->secretKey, self::$arrCipherNames[$this->cipherId], strtoupper($this->encryptionMode), PhpCrypt::PAD_PKCS7);

			$phpCryptCipher->IV($this->randomSalt);
			
			return $this->randomSalt . $phpCryptCipher->encrypt($strTextToEncrypt);
		}
		
		
		
        $padLength = $this->getCipherBlockSize() - (strlen($strTextToEncrypt) % $this->getCipherBlockSize());
        $strTextToEncrypt .= str_repeat(chr($padLength), $padLength);

        return $this->randomSalt . mcrypt_encrypt($this->cipherName, $this->secretKey, $strTextToEncrypt, $this->encryptionMode, $this->randomSalt);

    }

    public function decrypt($strEncrypted) 
    {
        $salt          = substr($strEncrypted, 0, $this->getCipherSaltSize());
        $encryptedData = substr($strEncrypted, $this->getCipherSaltSize());
		
		if(!self::isMCryptExtensionLoaded())
		{
			$phpCryptCipher = new PhpCrypt($this->secretKey, self::$arrCipherNames[$this->cipherId], strtoupper($this->encryptionMode), PhpCrypt::PAD_PKCS7);

			$phpCryptCipher->IV($salt);
			
			return $this->randomSalt . $phpCryptCipher->decrypt($encryptedData);
		}
		
        $decryptedData = mcrypt_decrypt($this->cipherName, $this->secretKey, $encryptedData, $this->encryptionMode, $salt);

        return substr($decryptedData, 0, -ord($decryptedData[strlen($decryptedData) - 1]));

    }

    public function getCipherKeySize()
    {
		
		$arrSize = array();
		
		$arrSize[self::CIPHER_DES]          = 8;
		$arrSize[self::CIPHER_RC2]          = 128;
		$arrSize[self::CIPHER_GOST]         = 32;
		$arrSize[self::CIPHER_CAST_128]     = 16;
		$arrSize[self::CIPHER_CAST_256]     = 32;
		$arrSize[self::CIPHER_BLOWFISH]     = 56;
		$arrSize[self::CIPHER_RIJNDAEL_128] = 32;
		$arrSize[self::CIPHER_RIJNDAEL_192] = 32;
		$arrSize[self::CIPHER_RIJNDAEL_256] = 32;
		
		return isset($arrSize[$this->cipherId]) ? $arrSize[$this->cipherId] : 0;
		
    }
	
    public function getCipherSaltSize()
    {
		return $this->getCipherBlockSize();
    }

    private function getCipherBlockSize()
    {
		static $arrSize = array(
			self::CIPHER_DES          => 8,
			self::CIPHER_RC2          => 8,
			self::CIPHER_GOST         => 8,
		    self::CIPHER_CAST_128     => 8,
		    self::CIPHER_CAST_256     => 16,
		    self::CIPHER_BLOWFISH     => 8,
		    self::CIPHER_RIJNDAEL_128 => 16,
		    self::CIPHER_RIJNDAEL_192 => 24,
		    self::CIPHER_RIJNDAEL_256 => 32
		);

		return isset($arrSize[$this->cipherId]) ? $arrSize[$this->cipherId] : 0;
    }


	private static function isMCryptExtensionLoaded()
	{
		return MchCrypt_Core_Generator::canUseMCrypt();

//        static $isLoaded = null;
//
//        return (null !== $isLoaded) ? $isLoaded : $isLoaded = extension_loaded('mcrypt');
	}
	
}