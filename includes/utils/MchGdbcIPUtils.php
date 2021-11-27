<?php
/**
 * Copyright (C) 2015 Mihai Chelaru
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


final class MchGdbcIPUtils
{
	CONST IP_VERSION_4 = 4;
	CONST IP_VERSION_6 = 6;

	CONST IP_V4_MAX_BITS = 32;
	CONST IP_V6_MAX_BITS = 128;

	public static function ipAddressToBinary($ipAddress, $ipVersion = null)
	{
		static $arrBinaryIp = array();
		if(isset($arrBinaryIp[$ipAddress]))
			return $arrBinaryIp[$ipAddress];

		(null === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) :  null;

		if( -1 === $ipVersion)
			return null;

		(count($arrBinaryIp) > 20) ? array_shift($arrBinaryIp) : null;

		if($ipVersion === self::IP_VERSION_4)
		{
			if(self::hasIpV4Support()) {
				return (false !== ($binStr = inet_pton($ipAddress))) ? $arrBinaryIp[$ipAddress] = $binStr : null;
			}

			return $arrBinaryIp[$ipAddress] = pack('N', ip2long($ipAddress));
		}

		if(self::hasIPV6Support()) {
			return (false !== ($binStr = inet_pton($ipAddress))) ? $arrBinaryIp[$ipAddress] = $binStr : null;
		}

		$binary = explode(':', $ipAddress);
		$binaryCount = count($binary);
		if (($doub = array_search('', $binary, 1)) !== false)
		{
			$length = (!$doub || $doub === ($binaryCount - 1) ? 2 : 1);
			array_splice($binary, $doub, $length, array_fill(0, 8 + $length - $binaryCount, 0));
		}

		$binary = array_map('hexdec', $binary);
		array_unshift($binary, 'n*');

		return $arrBinaryIp[$ipAddress] = call_user_func_array('pack', $binary);


	}

	public static function ipAddressFromBinary($binaryString)
	{
		$strLength = strlen($binaryString);

		if(4 === $strLength && !self::hasIpV4Support())
			return self::ipV4FromBinary($binaryString);

		if(16 === $strLength && !self::hasIPV6Support())
			return self::ipV6FromBinary($binaryString);

		return ($strLength === 4 || $strLength === 16) ?
			(false !== ($ipAddress = inet_ntop($binaryString))) ? $ipAddress : null : null;

	}

	public static function isPublicIpAddress($ipAddress, $ipVersion = null)
	{
		(null === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) : -1;

		if($ipVersion === self::IP_VERSION_4 && 0 === strpos($ipAddress, '127.0.0'))
			return false;

		if($ipVersion === self::IP_VERSION_6 && (0 === strpos($ipAddress, '::') ? '::1' === $ipAddress : '::1' === self::compressIPV6($ipAddress)))
			return false;

		return false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	public static function isIPV4MappedIPV6($ipAddress)
	{
		if( self::IP_VERSION_6 !== self::getIpAddressVersion($ipAddress))
			return false;

		return 1 === preg_match("/^::ffff:(.+)$/i", $ipAddress);
	}


	public static function extractMappedIPV4FromIPv6($ipAddress)
	{
		if( self::IP_VERSION_6 !== self::getIpAddressVersion($ipAddress))
			return null;

		if(1 !== preg_match("/^::ffff:(.+)$/i", $ipAddress, $matches) || empty($matches[1]) || self::IP_VERSION_4 !== self::getIpAddressVersion($matches[1]))
			return null;

		return $matches[1];
	}

	public static function compressIPV6($ipAddress, $shouldValidate = false)
	{
		if($shouldValidate && (self::IP_VERSION_6 !== self::getIpAddressVersion($ipAddress)))
			return null;

		return self::hasIPV6Support() ? inet_ntop(inet_pton($ipAddress)) : self::ipAddressFromBinary(self::ipAddressToBinary($ipAddress));
	}

	public static function expandIPV6($ipAddress, $shouldValidate = false)
	{
		if($shouldValidate && (self::IP_VERSION_6 !== self::getIpAddressVersion($ipAddress)))
			return null;

		return self::hasIPV6Support() ? implode(':', str_split(bin2hex(inet_pton($ipAddress)), 4)) : implode(':', str_split(bin2hex(self::ipAddressToBinary($ipAddress, self::IP_VERSION_6)), 4));
	}

	public static function ipAddressToNumber($ipAddress, $ipVersion = -1, $cacheResult = false)
	{
		static $arrCache = array();
		if(isset($arrCache[$ipVersion][$ipAddress]))
			return $arrCache[$ipVersion][$ipAddress];

		(-1 === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) : -1;
		if(-1 === $ipVersion)
			return null;

		if($ipVersion === self::IP_VERSION_4) {
			return $cacheResult ? $arrCache[$ipVersion][$ipAddress] = sprintf( '%u', ip2long( $ipAddress ) ) : sprintf( '%u', ip2long( $ipAddress ) );
		}

		$bytes = 16;
		$ipv6long = '';

		$binaryIp = self::ipAddressToBinary($ipAddress, self::IP_VERSION_6);
		while ($bytes > 0)
		{
			$bin = sprintf('%08b',(ord($binaryIp[$bytes-1])));
			$ipv6long = $bin.$ipv6long;
			$bytes--;
		}

		$out = new Math_BigInteger($ipv6long, 2);

		return $cacheResult ? $arrCache[$ipVersion][$ipAddress] = $out->toString() : $out->toString();

	}

	public static function ipAddressFromNumber($number, $ipVersion)
	{
		if($ipVersion === self::IP_VERSION_4)
		{
			return long2ip(-(4294967295 - ($number - 1)));
		}

		$binNumber = new Math_BigInteger($number);
		$binNumber = str_pad($binNumber->toBits(), 128, '0', STR_PAD_LEFT);

		$bytes = 0;
		$ipv6 = '';
		while ($bytes < 8)
		{
			$part = dechex(bindec(substr($binNumber, ($bytes *16 ), 16)));
			$part = str_pad($part, 4, '0', STR_PAD_LEFT);
			$ipv6 .= $part.':';
			++$bytes;
		}

		return substr($ipv6, 0, strlen($ipv6) -1);
	}


	public static function isValidIpAddress($ipAddress)
	{
		return (-1 !== self::getIpAddressVersion($ipAddress));
	}


	private static function ipV6FromBinary($binaryString)
	{
		$ip = bin2hex($binaryString);
		$ip = substr(chunk_split($ip, 4, ':'), 0, -1);
		$ip = explode(':', $ip);
		$res = '';

		foreach($ip as $index => $seg)
		{
			if(isset($seg [0]))
			{
				while ($seg[0] == '0')
					$seg = substr($seg, 1);
			}
			
			if ($seg != '')
			{
				$res .= $seg;
				if ($index < count($ip) - 1)
					$res .= $res == '' ? '' : ':';
			}
			else
			{
				if (strpos($res, '::') === false)
					$res .= ':';

			}
		}

		return $res;

	}

	private static function ipV4FromBinary($binaryString)
	{
		$decode = unpack('N', $binaryString);
		return isset($decode[1]) ? long2ip($decode[1]) : null;
	}

	public static function getIpAddressVersion($ipAddress)
	{
		static $arrIpVersions = array();
		if(isset($arrIpVersions[$ipAddress]))
			return $arrIpVersions[$ipAddress];

		if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return $arrIpVersions[$ipAddress] = self::IP_VERSION_4;

		if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			return $arrIpVersions[$ipAddress] = self::IP_VERSION_6;

		return -1;
	}


	public static function sanitizeIpAddress($ipString)
	{
		$ipString = trim($ipString);
		false !== ($posSlash = strpos($ipString, '/')) ? $ipString = substr($ipString, 0, $posSlash) : null;

		if(false === ($posColon = strrpos($ipString, ':')))
			return $ipString;

		$posDot   = strrpos($ipString, '.');
		$posRBrac = strpos($ipString, ']');

		($posRBrac !== false && $ipString[0] === '[') ? $ipString = substr($ipString, 1, $posRBrac - 1) : null;

		if ($posDot !== false)
		{
			$posColon > $posDot ? $ipString = substr($ipString, 0, $posColon) : null;
		}
		elseif (strpos($ipString, ':') === $posColon)
		{
			$ipString = substr($ipString, 0, $posColon);
		}

		return $ipString;
	}


	public static function sanitizeCIDRRange($ipRangeString)
	{
		static $arrSanitizedRanges = array();
		if(isset($arrSanitizedRanges[$ipRangeString]))
			return $arrSanitizedRanges[$ipRangeString];

		$sanitizedKey = $ipRangeString;

		$ipRangeString = trim($ipRangeString);
		if (empty($ipRangeString))
			return null;

		$bits = null;
		if (false !== strpos($ipRangeString, '*'))
		{
			if (preg_match('~(^|\.)\*\.\d+(\.|$)~D', $ipRangeString))
				return null;

			$bits = 32 - (8 * substr_count($ipRangeString, '*'));
			$ipRangeString = str_replace('*', '0', $ipRangeString);
		}

		if (false !== ($pos = strpos($ipRangeString, '/'))) {
			$bits = substr($ipRangeString, $pos + 1);
			$ipRangeString = substr($ipRangeString, 0, $pos);
		}

		if(-1 === ($ipVersion = self::getIpAddressVersion($ipRangeString)))
			return null;

		$maxBits = ($ipVersion === 4) ? 32 : 128;

		(null === $bits) ? $bits = $maxBits : null;

		return  ($bits < 0 || $bits > $maxBits) ? null : $arrSanitizedRanges[$sanitizedKey] = "$ipRangeString/$bits";

	}

	public static function getCIDRRangeBounds($ipCIDR)
	{
		if (null === ($ipCIDR = self::sanitizeCIDRRange($ipCIDR)))
			return array();

		static $arrCachedRangeBounds = array();
		if(isset($arrCachedRangeBounds[$ipCIDR]))
			return $arrCachedRangeBounds[$ipCIDR];

		list($range, $bits) = explode('/', $ipCIDR, 2);

		$high = $low = self::ipAddressToBinary($range);
		if (null === $low)
			return array();

		$lowLen = strlen($low);
		$i      = $lowLen - 1;
		$bits   = ($lowLen * 8) - $bits;
		for ($n = (int)($bits / 8); $n > 0; $n--, $i--)
		{
			$low[$i]  = chr(0);
			$high[$i] = chr(255);
		}

		if ($n = $bits % 8)
		{
			$low[$i]  = chr(ord($low[$i]) & ~((1 << $n) - 1));
			$high[$i] = chr(ord($high[$i]) | ((1 << $n) - 1));
		}

		return $arrCachedRangeBounds[$ipCIDR] = array(self::ipAddressFromBinary($low), self::ipAddressFromBinary($high));
	}


	public static function generateRandomIPV4($justPublic = true)
	{
		$ipAddress = null;

		while(true)
		{
			mt_srand();
			$ipAddress = mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);

			if(!$justPublic)
				break;

			if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
				break;

		}

		return $ipAddress;
	}

	public static function generateRandomIPV6($justPublic = true)
	{
		$ipAddress = null;

		while(true)
		{
			//mt_srand();
			$ipAddress = self::compressIPV6(wordwrap('2001' . substr(sha1(mt_rand()), -28), 4, ':', true));

			if(!$justPublic)
				break;

			if(false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
				break;
		}

		return $ipAddress;
	}

	public static function isIpInCIDRRange($ipAddress, $cidrRange, $ipVersion = -1, $alreadySanitized = false)
	{
		$ipVersion = (int)$ipVersion;
		if(!$alreadySanitized) {
			$ipAddress = self::sanitizeIpAddress( $ipAddress );
			if ( null === ( $cidrRange = self::sanitizeCIDRRange( $cidrRange ) ) ) {
				return false;
			}
		}

		list($ipAddressRange, $netMask) = explode('/', $cidrRange, 2);

		$netMask = (int)$netMask;

		if(-1 === $ipVersion)
		{
			$ipVersion = self::getIpAddressVersion($ipAddress);
			if ( -1 === $ipVersion ) {
				return false;
			}

			if( $ipVersion !== self::getIpAddressVersion($ipAddressRange) ) {
				return false;
			}
		}

		if($ipVersion === self::IP_VERSION_4)
		{
			if($netMask === self::IP_V4_MAX_BITS){
				return $ipAddressRange === $ipAddress;
			}

			return 0 === self::compareIPV4($ipAddress, $ipAddressRange, $netMask);
		}

		return 0 === self::compareIPV6($ipAddress, $ipAddressRange, $netMask);

	}

	public static function getMaxIpAddressFromCIDR($ipRangeCIDR)
	{
		$arrInfo = self::getCIDRRangeBounds($ipRangeCIDR);

		return isset($arrInfo[1]) ? $arrInfo[1] : null;

	}

	public static function getMinIpAddressFromCIDR($ipRangeCIDR)
	{
		$arrInfo = self::getCIDRRangeBounds($ipRangeCIDR);

		return isset($arrInfo[0]) ? $arrInfo[0] : null;
	}


	private static function getIpMaxPrefix($ipAddressORCIDR, $ipVersion = -1)
	{
		if(null === ($ipAddressORCIDR = self::sanitizeCIDRRange($ipAddressORCIDR)))
			return -1;

		list($ipAddress, $cidrRange) = explode('/', $ipAddressORCIDR, 2);

		if(empty($ipAddress) || !isset($cidrRange) || $cidrRange < 0)
			return -1;

		(-1 === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) :  null;

		$maxBits = ($ipVersion === 4) ? 32 : 128;

		$ipToNumber = self::ipAddressToNumber($ipAddress, $ipVersion);

		while($cidrRange > 0)
		{
			if(4 === $ipVersion)
			{
				//$mask = pow(2, $maxBits) - pow(2, $maxBits - ($cidrRange -1));
				//$mask = self::ipAddressToNumber(self::getIpAddressNetMask("$ipAddress/" . ($cidrRange - 1)));
				$mask =  ((1 << 32) -1) << (32 - ($cidrRange - 1)) ;

				if ( ((int)$mask & (int)$ipToNumber) != (int)$ipToNumber )
					return $cidrRange;
			}
			else
			{
				$maxBitsBigInt  = self::mathBigIntPow(2, $maxBits);
				$cidrBitsBigInt = self::mathBigIntPow(2, $maxBits - ($cidrRange -1));
				$mask = $maxBitsBigInt->subtract($cidrBitsBigInt);

				$ipToNumberBigInt = new Math_BigInteger($ipToNumber);

				if($ipToNumberBigInt->compare($ipToNumberBigInt->bitwise_and($mask), $ipToNumberBigInt) != 0)
					return $cidrRange;
			}

			$cidrRange --;
		}

		return $cidrRange;

	}

	private static function mathBigIntPow($number, $exp)
	{
		if(0 === $exp)
			return new Math_BigInteger(1);
		if(1 === $exp)
			return new Math_BigInteger($number);

		$odd  = $exp % 2;
		$exp -= $odd;

		$number = new Math_BigInteger($number);
		$multiplyResult = $number->multiply($number);

		$result = self::mathBigIntPow($multiplyResult->value, $exp / 2);

		(1 === $odd) ? $result = $result->multiply($number) : null;

		return $result;

	}

	public static function combineCIDRRanges(array $arrCIDRRanges)
	{
		$arrCIDRRanges = self::getSortedCIDRs($arrCIDRRanges);
		$newArrCIDRRanges = array();

		while(!empty($arrCIDRRanges))
		{
			$ipCIDR = array_shift($arrCIDRRanges);
			$startIpAddress = self::getMinIpAddressFromCIDR($ipCIDR);

			$ipVersion =  self::getIpAddressVersion($startIpAddress);

			$max = new Math_BigInteger(self::ipAddressFromNumber($startIpAddress, $ipVersion));

			$max = $max->add(new Math_BigInteger(self::getCIDRNumberOfHosts($ipCIDR)));

			while(!empty($arrCIDRRanges))
			{
				$compareIpCIDR = self::getMinIpAddressFromCIDR($arrCIDRRanges[0]);
				$compareIpNumber = new Math_BigInteger(self::ipAddressToNumber($compareIpCIDR, self::getIpAddressVersion($compareIpCIDR)));

				if($max->compare($compareIpNumber) >= 0)
					break;

				$compareIpCIDR = array_shift( $arrCIDRRanges );

				$newMax = $compareIpNumber->add( new Math_BigInteger( self::getCIDRNumberOfHosts( $compareIpCIDR ) ) );

				if ( $newMax->compare( $max ) > 0 ) {
					$max = $newMax;
				}

			}

			$newIpAddressNumber = $max->subtract(new Math_BigInteger(1));
			$newIpAddress = self::ipAddressFromNumber($newIpAddressNumber->value, $ipVersion);

			$rangeCIDR = self::getCIDRListFromRange($startIpAddress, $newIpAddress);

			$newArrCIDRRanges = array_merge($newArrCIDRRanges, $rangeCIDR);
		}

		return $newArrCIDRRanges;
	}

	public static function getCIDRListFromRange($startIp, $endIp)
	{

		$ipVersion = self::getIpAddressVersion($startIp);

		if(-1 === $ipVersion || $ipVersion !== self::getIpAddressVersion($endIp))
			return array();

		$arrIpAddressRange = array();

		if($ipVersion === self::IP_VERSION_4)
		{
			$startIpNumber = self::ipAddressToNumber($startIp, self::IP_VERSION_4);
			$endIpNumber   = self::ipAddressToNumber($endIp,   self::IP_VERSION_4);

			if($startIpNumber >= $endIpNumber)
				return array(self::sanitizeCIDRRange($startIp));

			$log2Value = log(2);
			while($endIpNumber >= $startIpNumber)
			{
				$startIpFromNumber = self::ipAddressFromNumber($startIpNumber, self::IP_VERSION_4);
				$prefix = self::getIpMaxPrefix($startIpFromNumber);
				$diff = 32 - floor( log( $endIpNumber - $startIpNumber + 1) / $log2Value );

				$prefix < $diff ? $prefix = $diff : null;

				$arrIpAddressRange[] = $startIpFromNumber . "/$prefix";
				$startIpNumber += pow(2, 32 - $prefix);
			}

			return $arrIpAddressRange;
		}

//		if($ipVersion === self::IP_VERSION_6)
//		{
//			$startIPBin = str_pad(self::ipAddressToBitRepresentation($startIp, self::IP_VERSION_6), 128, '0', STR_PAD_LEFT);
//			$endIPBin   = str_pad(self::ipAddressToBitRepresentation($endIp, self::IP_VERSION_6), 128, '0', STR_PAD_LEFT);
//			$IPIncBin   = $startIPBin;
//
//			echo "$IPIncBin";exit;
//
//			while (strcmp($IPIncBin, $endIPBin) <= 0)
//			{
//				$longNetwork = 128;
//				$IPNetBin = $IPIncBin;
//				while (($IPIncBin[$longNetwork - 1] == '0') && (strcmp(substr_replace($IPNetBin, '1', $longNetwork - 1, 1), $endIPBin) <= 0))
//				{
//					$IPNetBin[$longNetwork - 1] = '1';
//					$longNetwork--;
//				}
//
//				$arrIpAddressRange[] = self::ipAddressFromBitRepresentation($IPIncBin) . "/$longNetwork";
//				$IPIncBin = self::ipAddressFromBitRepresentation(self::addbin2bin(chr(1), self::ipAddressToBitRepresentation($IPNetBin)));
//				$IPIncBin = str_pad($IPIncBin,  128, '0', STR_PAD_LEFT);
//				print_r($arrIpAddressRange);
//				//$IPIncBin = str_pad(wfHelperBin::bin2str(wfHelperBin::addbin2bin(chr(1), wfHelperBin::str2bin($IPNetBin))), 128, '0', STR_PAD_LEFT);
//			}
//
//			return $arrIpAddressRange;
//		}

		return $arrIpAddressRange;

	}

	private static function sortCIDRCallback($firstCIDR, $secondCIDR)
	{
		$firstArrRange  = self::getCIDRRangeBounds(self::sanitizeCIDRRange($firstCIDR));
		$secondArrRange = self::getCIDRRangeBounds(self::sanitizeCIDRRange($secondCIDR));

		list($firstIpAddress, $firstCIDRRange)   = $firstArrRange;
		list($secondIpAddress, $secondCIDRRange) = $secondArrRange;

		if(0 !== ( $comp = strcmp(self::ipAddressToNumber($firstIpAddress), self::ipAddressToNumber($secondIpAddress))))
			return $comp;

		return strcmp($firstCIDRRange, $secondCIDRRange);
	}

	private static function getSortedCIDRs(array $arrCIDR)
	{

		usort($arrCIDR, array(__CLASS__, 'sortCIDRCallback'));
		return $arrCIDR;

		//$arrCIDR = array_map('self::sanitizeCIDRRange', $arrCIDR);

		//print_r($arrCIDR);exit;
	}


	public static function getIpAddressNetMask($ipAddressORCIDR, $ipVersion = null)
	{
		if(null === ($ipAddressORCIDR = self::sanitizeCIDRRange($ipAddressORCIDR)))
			return null;

		list($ipAddress, $cidrRange) = explode('/', $ipAddressORCIDR, 2);

		if(empty($ipAddress) || !isset($cidrRange) || $cidrRange < 0)
			return null;

		(null === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) :  null;

		if($ipVersion === self::IP_VERSION_4 && $cidrRange <= 32)
		{
			return long2ip( ((1 << 32) -1) << (32 - $cidrRange) );
		}

		if($ipVersion === self::IP_VERSION_6 && $cidrRange <= 128)
		{
			$hexMask = '';
			foreach(str_split(str_repeat("1", (128 - (128 - $cidrRange))).str_repeat("0", 128 - $cidrRange), 4) as $segment)
				$hexMask .= base_convert( $segment, 2, 16);

			return substr(preg_replace("/([A-f0-9]{4})/", "$1:", $hexMask), 0, -1);
		}

		return null;

	}

	public static function getIpAddressNetwork($ipAddress, $ipVersion = null)
	{
		return self::getMinIpAddressFromCIDR($ipAddress);
	}

	public static function getIpAddressBroadcast($ipAddress, $ipVersion = null)
	{
		return self::getMaxIpAddressFromCIDR($ipAddress);
	}


	public static function getCIDRNumberOfHosts($ipAddressOrCIDR)
	{
		if(null === ($ipAddressOrCIDR = self::sanitizeCIDRRange($ipAddressOrCIDR)))
			return null;

		$arrRangeBounds = self::getCIDRRangeBounds($ipAddressOrCIDR);
		if(empty($arrRangeBounds))
			return null;

		$ipVersion = self::getIpAddressVersion($arrRangeBounds[1]);

		if(self::IP_VERSION_4 === $ipVersion)
		{
			$arrCidr = explode('/', $ipAddressOrCIDR, 2);
			return pow(2, 32 - $arrCidr[1]);
			//echo 1 + (self::ipAddressToNumber($arrRangeBounds[1], $ipVersion) - self::ipAddressToNumber($arrRangeBounds[0], $ipVersion)) . "\n";
			//return 1 + (self::ipAddressToNumber($arrRangeBounds[1], $ipVersion) - self::ipAddressToNumber($arrRangeBounds[0], $ipVersion));
		}

		$numberOfHosts = new Math_BigInteger(self::ipAddressToNumber($arrRangeBounds[1], $ipVersion));
		$numberOfHosts = $numberOfHosts->subtract(new Math_BigInteger(self::ipAddressToNumber($arrRangeBounds[0], $ipVersion)));
		$numberOfHosts = $numberOfHosts->add(new Math_BigInteger(1));

		return $numberOfHosts->toString();
	}


	public static function getAllIpAddressesFromCIDR($ipRangeCIDR)
	{
		if(null === ($ipRangeCIDR = self::sanitizeCIDRRange($ipRangeCIDR)))
			return null;

		$arrRangeBounds = self::getCIDRRangeBounds($ipRangeCIDR);
		if(empty($arrRangeBounds))
			return null;

		$ipVersion = self::getIpAddressVersion($arrRangeBounds[1]);

		$lowIpNumber  = self::ipAddressToNumber($arrRangeBounds[0], $ipVersion);
		$highIpNumber = self::ipAddressToNumber($arrRangeBounds[1], $ipVersion);

		$arrAllIps = array(self::IP_VERSION_6 === $ipVersion ? self::expandIPV6($arrRangeBounds[0], false) : $arrRangeBounds[0]);

		if(8 === PHP_INT_SIZE && self::IP_VERSION_4 === $ipVersion)
		{
			for(++$lowIpNumber;$lowIpNumber < $highIpNumber; ++$lowIpNumber)
			{
				$arrAllIps[] = self::ipAddressFromNumber($lowIpNumber, $ipVersion);
			}
		}
		else
		{
			$bigNumberOne = new Math_BigInteger(1);
			$lowIpNumber = new Math_BigInteger($lowIpNumber);
			$lowIpNumber = $lowIpNumber->add($bigNumberOne);
			$highIpNumber = new Math_BigInteger($highIpNumber);

			while(!$lowIpNumber->equals($highIpNumber))
			{
				$arrAllIps[] = self::ipAddressFromNumber($lowIpNumber->toString(), $ipVersion);
				$lowIpNumber = $lowIpNumber->add($bigNumberOne);
			}
		}

		$arrAllIps[] = self::IP_VERSION_6 === $ipVersion ? self::expandIPV6($arrRangeBounds[1], false) : $arrRangeBounds[1];

		return $arrAllIps;

	}


	private static function compareIPV4($firstIpAddress, $secondIpAddress, $netMask)
	{
		return substr_compare(sprintf('%032b', ip2long($firstIpAddress)), sprintf('%032b', ip2long($secondIpAddress)), 0, $netMask);
	}

	private static function compareIPV6($firstIpAddress, $secondIpAddress, $netMask)
	{
		$bytesAddr = unpack("n*", self::ipAddressToBinary($secondIpAddress));
		$bytesTest = unpack("n*", self::ipAddressToBinary($firstIpAddress));

		for ($i = 1, $ceil = ceil($netMask / 16); $i <= $ceil; ++$i)
		{
			($left = $netMask - 16 * ($i-1)) > 16 ? $left = 16 : null;

			$mask = ~(0xffff >> $left) & 0xffff;

			if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask))
				return -1;
		}

		return 0;
	}

	public static function getCIDRFromNetMask($netMask, $shouldValidate = true)
	{
		if($shouldValidate)
		{
			$netMask = self::sanitizeIpAddress( $netMask );
			if ( self::IP_VERSION_4 !== self::getIpAddressVersion( $netMask ) ) {
				return - 1;
			}

		}

		$bits = 32 - log( ( (ip2long($netMask)) ^ 4294967295) + 1, 2 );

		return ($bits > 0 && (false !== filter_var($bits, FILTER_VALIDATE_INT))) ? $bits : -1;

	}

	private static function ipAddressToBitRepresentation($ipAddress, $ipVersion = null)
	{
		(null === $ipVersion) ? $ipVersion = self::getIpAddressVersion($ipAddress) : null;

		if(-1 === $ipVersion)
			return null;

		if($ipVersion === self::IP_VERSION_4)
		{
			return base_convert(self::ipAddressToNumber($ipAddress, self::IP_VERSION_4), 10, 2);
		}

		$ipAddress = self::ipAddressToBinary($ipAddress, self::IP_VERSION_6);

		$bits = 15;
		$ipbin = '';
		while ($bits >= 0)
		{
			$ipbin = sprintf('%08b', (ord($ipAddress[$bits]))) . $ipbin;
			$bits--;
		}

		return $ipbin;
	}

	private static function ipAddressFromBitRepresentation($ipAddressBitRepresented)
	{
		if(!isset($ipAddressBitRepresented[32])) // is ipv4
		{
			return self::ipAddressFromNumber(base_convert($ipAddressBitRepresented, 2, 10), self::IP_VERSION_4);
		}

		$ipAddressBitRepresented = str_pad($ipAddressBitRepresented, 128, '0',STR_PAD_LEFT);

		$bits = 0;
		$ipv6 = '';
		while ($bits <= 7)
		{
			$ipv6 .= dechex(bindec(substr($ipAddressBitRepresented , ($bits * 16) ,16))) . ':';
			$bits++;
		}

		return $ipv6;
	}

	private static function hasIpV4Support()
	{
		static $hasSupport = null;

		if(null !== $hasSupport)
			return $hasSupport;

		return $hasSupport = (!(PHP_VERSION_ID < 50300 && ('so' !== PHP_SHLIB_SUFFIX))) && @inet_pton('127.0.0.1');
	}

	private static function hasIPV6Support()
	{

		static $ipv6Supported = null;

		if(null !== $ipv6Supported)
			return $ipv6Supported;

		return $ipv6Supported =  self::hasIpV4Support() && ((extension_loaded('sockets') && defined('AF_INET6')) || @inet_pton('::1'));
	}


	public static function getCountryCode($ipAddress)
	{
//		static $countryCode = 0;
//		if(0 !== $countryCode)
//			return $countryCode;
//
//		if( !empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) )
//		{
//			return $countryCode = sanitize_text_field( strtoupper( $_SERVER['HTTP_CF_IPCOUNTRY'] ) );
//		}


		return null;

		$ipAddressVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress);
		if( -1 === $ipAddressVersion)
			return $countryCode = null;

		$maxMindGeoIp = new MchMaxMindGeoIp();

		try
		{
			$maxMindGeoIp->geoip_open( null, 0, $ipAddressVersion );

			$countryCode = ( $ipAddressVersion === MchGdbcIPUtils::IP_VERSION_4 ? $maxMindGeoIp->geoip_country_code_by_addr( $ipAddress ) : $maxMindGeoIp->geoip_country_code_by_addr_v6( $ipAddress ) );

			$maxMindGeoIp->geoip_close();

			$countryCode = ! empty( $countryCode ) ?  strtoupper( $countryCode )  : null;
		}
		catch(Exception $e)
		{
			$countryCode = null;
			$maxMindGeoIp->geoip_close();
		}

		return $countryCode;
	}

}