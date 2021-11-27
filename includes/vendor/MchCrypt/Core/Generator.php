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

final class MchCrypt_Core_Generator
{

	private static $canGenerateSecure = null;

	public static function canGenerateSecure() 
	{
		return (null !== self::$canGenerateSecure) ? 
						 self::$canGenerateSecure  : 
						 self::$canGenerateSecure  = function_exists('random_bytes') || self::canUseMCrypt() || self::canUseOpenSSL() || self::canUseDevURandom();
		
	}
	
    public static function generateRandomString($length, $secure = true, $characters = null)
    {
		if(($length = (int)$length) > 256)
		{
			$length = 256;
		}
		
		$randomString     = '';
		$characterslength = 94;

		(!empty($characters)) ? $characterslength = strlen($characters) : $characters = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

		$arrNumberInfo     = self::getNumberInfo($characterslength);
		$arrNumberInfo[0] *= $length ;
		$arrNumberInfo[1]  = 256 - (256 % $length);

		while(!isset($randomString[$length -1]))
		{
			$randomBytes = self::generateRandomBytes($arrNumberInfo[0], $secure);

			for ($i = 0; $i < $arrNumberInfo[0]; ++$i) 
			{
				if (ord($randomBytes[$i]) <= $arrNumberInfo[1]) 
				{
					$randomString .= $characters[ord($randomBytes[$i]) % $characterslength];
				}
			}
		}

		return !isset($randomString[$length]) ? $randomString : substr($randomString, 0, $length);
    }


    public static function generateRandomIntegerInRange($min, $max, $secure = true)
    {
		$randomNumber  = 0;
		
		if(($rangeNumber = ($max = (int)$max) - ($min = (int)$min)) <= 0)
			return $min;
		
		$arrNumberInfo = self::getNumberInfo($rangeNumber);

		do
		{
			$randomNumber = hexdec(bin2hex(self::generateRandomBytes($arrNumberInfo[0], $secure))) & $arrNumberInfo[1];	
		}
		while($randomNumber > $rangeNumber);
		
		return $min + $randomNumber;

    }
	
	
    private static function getNumberInfo($number)
    {
		static $rangeInfo = array();

		if(isset($rangeInfo[$number]))
		{
			return $rangeInfo[$number];
		}
		
		$rangeInfo[$number]   = array();
		$bitsCounter          = (int)(floor(log($number, 2) + 1));

		
		$rangeInfo[$number]   = array();
		$rangeInfo[$number][] = (int) max(ceil($bitsCounter / 8), 1);
		
		if( ($bitsCounter == (PHP_INT_SIZE * 8)) || ($bitsCounter == (PHP_INT_SIZE * 8) - 1))
		{
			$rangeInfo[$number][] = (8 === PHP_INT_SIZE) ? 0x7fffffffffffffff : 0xccccccc;
		}
		else
		{
			$rangeInfo[$number][] = (int)((1 << $bitsCounter) - 1);
		}
		
		return $rangeInfo[$number];
    }
	
	
	public static function generateRandomBytes($length, $secure = true)
	{
		
		$randomResult = '';
		if(function_exists('random_bytes'))
		{
			return random_bytes($length);
		}
		elseif($length <= 32 || !$secure)
		{
			$randomResult = self::getRandomBytes($length, $secure);
		}
		else
		{
			for($i = 0, $iterations = ceil($length / 32); $i < $iterations ; ++$i)
			{
				$randomResult .= self::getRandomBytes(32, true);
			}
		}
		
		while(!isset($randomResult[$length - 1])) // this should not happen but...
		{
			$randomResult .= self::getBytesUsingMTRand(($length % 32) + 1);	
		}		
		
		return substr($randomResult, 0, $length);			
	}

	
    private static function getRandomBytes($length, $secure = true)
    {
		
		if(!$secure)
		{
			return self::getBytesUsingMTRand($length);
		}
		
		if(!self::canGenerateSecure())
		{
			throw new Exception('Cannot generate secure random bytes!');
		}
		
		if(null !== ($randomResult  = self::getBytesUsingOpenSSL($length)))
			return $randomResult;

	    if(null !== ($randomResult  = self::getBytesUsingDevURandom($length)))
		    return $randomResult;

	    if(null !== ($randomResult  = self::getBytesUsingMCrypt($length)))
		    return $randomResult;

//		$openSSLResult = self::getBytesUsingOpenSSL($length);
//		$uRandomResult = self::getBytesUsingDevURandom($length);
//	    $mCryptResult  = self::getBytesUsingMCrypt($length);

//		if(null !== $openSSLResult)
//		{
//			$randomResult = $openSSLResult;
//		}
//
//
//		if(null !== $uRandomResult)
//		{
//			(null === $randomResult)           ?
//				$randomResult = $uRandomResult :
//				$randomResult ^= hash_hmac('sha256', $uRandomResult, $randomResult, true);
//
//		}
//
//	    if(null !== $mCryptResult)
//	    {
//		    (null === $randomResult)          ?
//			    $randomResult = $mCryptResult :
//			    $randomResult ^= hash_hmac('sha256', $mCryptResult, $randomResult, true);
//	    }
//
	    if((null === $randomResult) && self::isWindowsOS())
		{
			$randomResult = self::getBytesUsingCapicom($length);
		}

		
		return $randomResult;
    }



    private static function getBytesUsingCapicom($length)
    {
		static $canUseCapicom = null;

		if(null !== $canUseCapicom && !$canUseCapicom) 
		{
			return null;
		}

		if((null === $canUseCapicom) && (false === ($canUseCapicom = class_exists('\\COM', false))))
		{
			return null;
		}

		try 
		{
			$comRandomBytes = new COM('CAPICOM.Utilities.1');
			return str_pad(base64_decode($comRandomBytes->GetRandom($length, 0)), $length, chr(0));
		} 
		catch (Exception $ex) 
		{
			return null;
		}

    }	


	public static function canUseDevURandom()
	{
		static $canUseURandom = null;

		return null !== $canUseURandom ? $canUseURandom : $canUseURandom = (@is_readable('/dev/urandom'));
	}
	
    private static function getBytesUsingDevURandom($length)
    {
		if(!self::canUseDevURandom())
			return null;

		if(PHP_VERSION_ID >= 50303)
		{
			if(! ($fileResource = fopen('/dev/urandom', 'rb')) || 0 !== stream_set_read_buffer($fileResource, 0))
			{
				fclose($fileResource);
				return null;
			}

			$randomBytes = fread($fileResource, $length);
			fclose($fileResource);

			return $randomBytes;
		}

		$randomBytes = file_get_contents('/dev/urandom', false, null, -1, $length);

		return (false !== $randomBytes) ? $randomBytes : null; 
    }

	public static function canUseMCrypt()
	{
		static $canUseMCrypt = null;									//http://bugs.php.net/55169
		return null !== $canUseMCrypt ? $canUseMCrypt : $canUseMCrypt = (((PHP_VERSION_ID >= 50307) || !self::isWindowsOS()) &&
							  function_exists('mcrypt_create_iv'));
	}
	
    private static function getBytesUsingMCrypt($length)
    {
		if(!self::canUseMCrypt())
		{
			return null;
		}
		
		return (false !== ($randomBytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM))) ? $randomBytes : null;
    }


	public static function canUseOpenSSL()
	{
		static $canUseOpenSSL = null;

		return null !== $canUseOpenSSL ? $canUseOpenSSL : $canUseOpenSSL = (((PHP_VERSION_ID >= 50304) || !self::isWindowsOS()) && 
							  function_exists('openssl_random_pseudo_bytes'));
	}

	
    private static function getBytesUsingOpenSSL($length)
    {
		if(!self::canUseOpenSSL())
		{
			return null;
		}
		
		$strongCryptoCreated = false;
		$randomBytes = openssl_random_pseudo_bytes($length, $strongCryptoCreated);

		return $strongCryptoCreated ? $randomBytes : null;

    }


    private static function getBytesUsingMTRand($length) 
    {
		$randomBytes = '';

		for($i = 0; $i < $length; $i++) 
		{
			$randomBytes .= chr((mt_rand() ^ mt_rand()) % 256);
		}

		return $randomBytes;
    }

	
    public static function isWindowsOS()
    {
		return ('so' !== PHP_SHLIB_SUFFIX);
    }	
	
    private function __construct() 
    {}
}