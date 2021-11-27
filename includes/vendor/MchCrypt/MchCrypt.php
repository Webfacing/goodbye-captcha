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

if (!defined('PHP_VERSION_ID')) 
{
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	
	unset($version);
}


if( ! function_exists( 'mchCryptAutoLoader' ) )
{

	function mchCryptAutoLoader($className)
	{

		static $arrClassMap = array(
			'MchCrypt_Core_Generator' => '/Core/Generator.php',
			'MchCrypt_Core_Crypter'   => '/Core/Crypter.php',
			'PhpCrypt'                => '/PhpCrypt/phpCrypt.php',
			'Crypt_Blowfish'          => '/PhpSecLib-0.3.10/Crypt/Blowfish.php',
		);

		return isset($arrClassMap[$className]) ? include_once dirname(__FILE__) . $arrClassMap[$className] : null;

	}

	spl_autoload_register('mchCryptAutoLoader');

}

final class MchCrypt
{
	CONST DERIVED_KEY_ITERATIONS = 1000;
	
	public static function getRandomIntegerInRange($min = 1, $max = PHP_INT_MAX, $forceSecureRandomBytes = false)
	{
		return MchCrypt_Core_Generator::generateRandomIntegerInRange($min, $max, ((bool)$forceSecureRandomBytes) ? true : self::canGenerateSecureRandomBytes());
	}
    
	public static function getRandomString($strLength = 64, $forceSecureRandomBytes = false, $characters = null)
	{
		return MchCrypt_Core_Generator::generateRandomString($strLength, ((bool)$forceSecureRandomBytes) ? true : self::canGenerateSecureRandomBytes(), $characters);	
	}
	
	public static function getRandomToken($tokenLength = 32, $isForUrl = true, $forceSecureRandomBytes = false)
	{
		$randomToken = base64_encode(self::getRandomString($tokenLength, $forceSecureRandomBytes));

		return (!$isForUrl) ? $randomToken : str_replace( array('+', '/', '='), array('-', '_', ''), $randomToken);
	}
	

	public static function getRandomBytes($length, $forceSecure = false)
	{
		return MchCrypt_Core_Generator::generateRandomBytes($length, ((bool)$forceSecure) ? true : self::canGenerateSecureRandomBytes());
	}
	
	public static function getCipherKeySize($cipherId = MchCrypt_Core_Crypter::CIPHER_BLOWFISH, $encryptionModeId = MchCrypt_Core_Crypter::MODE_CBC)
	{
		$crypter = new MchCrypt_Core_Crypter($cipherId, $encryptionModeId);
		return $crypter->getCipherKeySize();
	}
	
	
	private static function deriveKey($secretKey, $salt, $length)
	{
		
		if(PHP_VERSION_ID >= 50500)
		{
			return hash_pbkdf2('sha256', $secretKey, $salt, self::DERIVED_KEY_ITERATIONS, $length, true);
		}
				
		$blockCount = ceil($length / 32);

		$hash = '';
		for($i = 1; $i <= $blockCount; ++$i)
		{
			$last = $xorsum = hash_hmac('sha256', $salt . pack("N", $i), $secretKey, true);
			for ($j = 1; $j < self::DERIVED_KEY_ITERATIONS ; ++$j) 
			{
				$xorsum ^= ($last = hash_hmac('sha256', $last, $secretKey, true));
			}

			$hash .= $xorsum;
		}
		
		return substr($hash, 0, $length);
	}
	
    private static function compareDerivedKeys($firstDerivedKey, $secondDerivedKey)
    {
        $firstDerivedKey        = (string) $firstDerivedKey;
        $secondDerivedKey       = (string) $secondDerivedKey;
        $firstDerivedKeyLength  = strlen($firstDerivedKey);
        $secondDerivedKeyLength = strlen($secondDerivedKey);
		
        $result = 0;
        for ($i = 0, $length = min($firstDerivedKeyLength, $secondDerivedKeyLength); $i < $length; ++$i) 
		{
            $result |= ord($firstDerivedKey[$i]) ^ ord($secondDerivedKey[$i]);
        }
		
       return  (0 === ($result |= $firstDerivedKeyLength ^ $secondDerivedKeyLength));

    }
	
	public static function encryptToken($secretKey, $strTextToEncrypt, $cipherId = MchCrypt_Core_Crypter::CIPHER_BLOWFISH, $encryptionModeId = MchCrypt_Core_Crypter::MODE_CBC)
	{
		try
		{
			$crypter = new MchCrypt_Core_Crypter($cipherId, $encryptionModeId);

			$derivedKey = self::deriveKey($secretKey, $crypter->getRandomSalt(), 2 * $crypter->getCipherKeySize());

			$cipherSecretKey = substr($derivedKey, 0, $crypter->getCipherKeySize());

			$crypter->setSecretKey($cipherSecretKey);

			$encryptedData = @$crypter->encrypt(self::tryToCompressString($strTextToEncrypt));

			$tokenKeyHmac = substr($derivedKey, $crypter->getCipherKeySize());

			$hashedToken  = hash_hmac('md5', $encryptedData, $tokenKeyHmac, true);

			$encryptedToken = $hashedToken . $encryptedData;
			
			
			return str_replace( array('+', '/', '='), array('-', '_', ''), base64_encode($encryptedToken));
			
		}
		catch(Exception $ex)
		{
			return null;
			//$exClass = get_class($ex);
			//throw new $exClass($ex->getMessage());
		}
		
		
	}

	
	public static function decryptToken($secretKey, $strEncryptedToken, $cipherId = MchCrypt_Core_Crypter::CIPHER_BLOWFISH, $encryptionModeId = MchCrypt_Core_Crypter::MODE_CBC)
	{
		try
		{
			$strEncryptedToken = str_replace(array('-','_'), array('+','/'), $strEncryptedToken);
			
			if(0 !== ($mod4 = strlen($strEncryptedToken) % 4))
			{
				$strEncryptedToken .= substr('====', $mod4);
			}

			$hmacSize = 16;
			$strEncryptedToken = base64_decode($strEncryptedToken);

			if(false === $strEncryptedToken || !isset($strEncryptedToken[$hmacSize -1]))
				return null;

			$hashedToken   = substr($strEncryptedToken, 0, $hmacSize);
			$encryptedData = substr($strEncryptedToken, $hmacSize);

			if(empty($encryptedData) || empty($hashedToken))
				return null;

			$crypter  = new MchCrypt_Core_Crypter($cipherId, $encryptionModeId);

			$derivedKey      = self::deriveKey($secretKey, substr($encryptedData, 0, $crypter->getCipherSaltSize()), 2 * $crypter->getCipherKeySize());
			$cipherSecretKey = substr($derivedKey, 0, $crypter->getCipherKeySize());

			if(empty($cipherSecretKey))
				return null;
			
			$crypter->setSecretKey($cipherSecretKey);
			
			$tokenKeyHmac = substr($derivedKey, $crypter->getCipherKeySize());
			
			$expectedHashedToken = hash_hmac('md5', $encryptedData, $tokenKeyHmac, true);
			
			
			return self::compareDerivedKeys($expectedHashedToken, $hashedToken) ? self::decompressString( @$crypter->decrypt($encryptedData) ) : null;
			
		}
		catch(Exception $ex)
		{
			return null;
		}
		
	}
	
	
	private static function tryToCompressString($strToCompress)
	{

		if(!isset($strToCompress[0]))
			return $strToCompress;
					
		if(self::isZLibAvailable())
			return gzdeflate($strToCompress, 9);
	
		if(self::isBZip2Available())
			return bzcompress($strToCompress, 9);

		if(self::isLzfAvailable())
			return lzf_compress($strToCompress);
		
		return $strToCompress;
	}
	
	private static function decompressString($compressedStr)
	{
		if(!isset($compressedStr[0]))
			return $compressedStr;

		if(self::isZLibAvailable())
			return gzinflate($compressedStr);
		
		if(self::isBZip2Available())
			return bzdecompress($compressedStr);
		
		if(self::isLzfAvailable())
			return lzf_decompress($compressedStr);
		
		return $compressedStr;
		
	}
	
	public static function isWindowsOS()
	{
		return MchCrypt_Core_Generator::isWindowsOS();
	}
	
	public static function canUseMCrypt()
	{
		return MchCrypt_Core_Generator::canUseMCrypt();
	}	
	
	public static function canGenerateSecureRandomBytes()
	{
		return MchCrypt_Core_Generator::canGenerateSecure();
	}
	
	public static function isZLibAvailable()
	{
		static $isLoaded = null;
		return null !== $isLoaded ? $isLoaded : $isLoaded = extension_loaded('zlib');
	}
	
	public static function isBZip2Available()
	{
		static $isLoaded = null;
		return null !== $isLoaded ? $isLoaded : $isLoaded = extension_loaded('bz2');
	}
	
	public static function isLzfAvailable()
	{
		static $isLoaded = null;
		return null !== $isLoaded ? $isLoaded : $isLoaded = extension_loaded('lzf');
		
	}
	
	private function __construct() 
	{}
}
