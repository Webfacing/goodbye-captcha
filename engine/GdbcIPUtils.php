<?php

/*
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

final class GdbcIPUtils
{
	public static function isClientIpBlockedByCountry()
	{
		return self::isIpBlockedByCountry(self::getClientIpAddress());
	}

	public static function getClientIpAddress()
	{
		return MchGdbcHttpRequest::getClientIp(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_PROXY_HEADERS) ? (array)GdbcProxyHeadersPublicModule::getInstance()->getOption(GdbcProxyHeadersAdminModule::PROXY_HEADERS_IP) : array());
	}

	public static function isClientIpBlackListed()
	{
		return self::isIpBlackListed(self::getClientIpAddress());
	}

	public static function isClientIpWhiteListed()
	{
		return self::isIpWhiteListed(self::getClientIpAddress());
	}

	public static function isIpWhiteListed($ipAddress)
	{
		static $arrWhiteVerifiedIPs = array();
		if(isset($arrWhiteVerifiedIPs[$ipAddress]))
			return $arrWhiteVerifiedIPs[$ipAddress];

		if( self::isIpInFormattedRanges($ipAddress, GdbcWhiteListedIpsPublicModule::getInstance()->getOption(GdbcWhiteListedIpsAdminModule::OPTION_WHITE_LISTED_IPS)) )
			$arrWhiteVerifiedIPs[$ipAddress] = true;

		return isset($arrWhiteVerifiedIPs[$ipAddress]);

	}

	public static function isIpBlackListed($ipAddress)
	{
		static $arrBlackVerifiedIPs = array();
		if(isset($arrBlackVerifiedIPs[$ipAddress]))
			return $arrBlackVerifiedIPs[$ipAddress];

		if(self::isIpInFormattedRanges($ipAddress, GdbcBlackListedIpsPublicModule::getInstance()->getOption(GdbcBlackListedIpsAdminModule::OPTION_BLACK_LISTED_IPS)))
			$arrBlackVerifiedIPs[$ipAddress] = true;

		return isset($arrBlackVerifiedIPs[$ipAddress]);
	}

	public static function isIpBlockedByCountry($ipAddress)
	{
		if(!GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_COUNTRY_BLOCKING))
			return false;

		return GdbcGeoIpCountryPublicModule::getInstance()->isCountryIdBlocked(self::getCountryIdByIpAddress($ipAddress));

	}

	public static function isIpAddressBlocked($ipAddress)
	{
		if(self::isIpBlackListed($ipAddress))
			return true;

		if(self::isIpBlockedByCountry($ipAddress))
			return true;

		if(null === ($bruteForceModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::MODULE_BRUTE_FORCE)))
			return false;

		if($bruteForceModuleInstance->getOption(GdbcBruteForceAdminModule::OPTION_BLOCK_WEB_ATTACKERS) && MchGdbcUnTrustedIPRanges::isAttachingHostIP($ipAddress))
			return true;

		if($bruteForceModuleInstance->getOption(GdbcBruteForceAdminModule::OPTION_BLOCK_ANONYMOUS_PROXY) && MchGdbcUnTrustedIPRanges::isAnonymizerProxyIP($ipAddress))
			return true;

		return false;

	}

	public static function isClientIpProxyAnonymizer($checkBruteForceModuleSettings = true)
	{
		static $isAnonymizer = null;
		if(null !== $isAnonymizer)
			return $isAnonymizer;

		$bruteForceModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::MODULE_BRUTE_FORCE);
		if(null === $bruteForceModuleInstance)
			return $isAnonymizer = false;

		if( $checkBruteForceModuleSettings && (! $bruteForceModuleInstance->getOption(GdbcBruteForceAdminModule::OPTION_BLOCK_ANONYMOUS_PROXY)) )
			return $isAnonymizer = false;

		return $isAnonymizer = MchGdbcUnTrustedIPRanges::isAnonymizerProxyIP(GdbcIPUtils::getClientIpAddress());
	}

	public static function isClientIpWebAttacker($checkBruteForceModuleSettings = true)
	{
		static $isAttacker = null;
		if(null !== $isAttacker)
			return $isAttacker;

		$bruteForceModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::MODULE_BRUTE_FORCE);
		if(null === $bruteForceModuleInstance)
			return $isAttacker = false;

		if( $checkBruteForceModuleSettings && (! $bruteForceModuleInstance->getOption(GdbcBruteForceAdminModule::OPTION_BLOCK_WEB_ATTACKERS)) )
			return $isAttacker = false;

		return $isAttacker = MchGdbcUnTrustedIPRanges::isAttachingHostIP(GdbcIPUtils::getClientIpAddress());

	}

	public static function isIpProxyAnonymizer($ipAddress)
	{
		return  MchGdbcUnTrustedIPRanges::isAnonymizerProxyIP($ipAddress);
	}

	public static function isIpWebAttacker($ipAddress)
	{
		return  MchGdbcUnTrustedIPRanges::isAttachingHostIP($ipAddress);
	}


	public static function isIpInFormattedRanges($ipAddress, $arrFormattedRanges)
	{
		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress);

		if( -1 === $ipVersion )
			return false;

		if(empty($arrFormattedRanges[$ipVersion]))
			return false;


		if($ipVersion === MchGdbcIPUtils::IP_VERSION_6)
		{
			$ipAddress = MchGdbcIPUtils::compressIPV6($ipAddress);
			if(isset($arrFormattedRanges[$ipVersion][$ipAddress]))
				return  true;

			foreach($arrFormattedRanges[$ipVersion] as $blockedIPRange => $value)
			{
				if(false === strpos($blockedIPRange, '/'))
					continue;

				if( ! MchGdbcIPUtils::isIpInCIDRRange($ipAddress, $blockedIPRange, MchGdbcIPUtils::IP_VERSION_6, true) )
					continue;

				return true;
			}

			return false;
		}

		$ipNumber = MchGdbcIPUtils::ipAddressToNumber($ipAddress, MchGdbcIPUtils::IP_VERSION_4);
		if(isset($arrFormattedRanges[$ipVersion][$ipNumber])) // single IP
			return true;

		foreach($arrFormattedRanges[$ipVersion] as $minIpNumber => $maxIpNumber)
		{
			if( (1 !== $maxIpNumber) && ($minIpNumber <= $ipNumber) && ($ipNumber <= $maxIpNumber) )
				return true;
		}

		return false;

	}


	public static function removeIpFromFormattedRange($ipAddress, $arrFormattedRange)
	{
		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress);
		if(-1 === $ipVersion)
			return $arrFormattedRange;

		if($ipVersion === MchGdbcIPUtils::IP_VERSION_6)
		{
			unset($arrFormattedRange[MchGdbcIPUtils::compressIPV6($ipAddress)]);
			return $arrFormattedRange;
		}

		$ipNumber = MchGdbcIPUtils::ipAddressToNumber($ipAddress, $ipVersion);
		if(isset($arrFormattedRange[$ipNumber]) && 1 == $arrFormattedRange[$ipNumber])
		{
			unset($arrFormattedRange[$ipNumber]);
			return $arrFormattedRange;
		}

		$arrSingleIPs = array();
		$arrNewRanges = array();
		foreach($arrFormattedRange as $minValue => $maxValue)
		{
			if(1 == $maxValue)
			{
				$arrSingleIPs[$minValue] = 	$maxValue;
				continue;
			}

			if( ($minValue > $ipNumber) || ($ipNumber > $maxValue) )
			{
				$arrNewRanges[] = array($minValue, $maxValue);
				continue;
			}

			if($minValue == $ipNumber)
			{
				if($minValue + 1 <= $maxValue) {
					$arrNewRanges[] = array( $minValue + 1, $maxValue );
				}
				unset($arrFormattedRange[$minValue]);
				continue;
			}

			if($maxValue == $ipNumber)
			{
				if($minValue <= $maxValue - 1) {
					$arrNewRanges[] = array( $minValue, $maxValue - 1 );
				}
				unset($arrFormattedRange[$minValue]);
				continue;
			}

			if($minValue == $ipNumber - 1)
			{
				$arrSingleIPs[$minValue] = 1;
				$arrNewRanges[] = array( $ipNumber + 1, $maxValue );
				continue;
			}

			if($maxValue == $ipNumber + 1)
			{
				$arrSingleIPs[$maxValue] = 1;
				$arrNewRanges[] = array( $minValue, $ipNumber -1 );
				continue;
			}

			$arrNewRanges[] = array( $minValue, $ipNumber -1 );
			$arrNewRanges[] = array( $ipNumber + 1,  $maxValue);

		}

		$arrFormattedRange = $arrSingleIPs; unset($arrSingleIPs);

		for($i = 0, $rangeLength = count($arrNewRanges); $i < $rangeLength; ++$i)
		{
			if($arrNewRanges[$i][0] < $arrNewRanges[$i][1])
				continue;

			$arrFormattedRange[$arrNewRanges[$i][0]] = 1;
			unset($arrNewRanges[$i]);
		}

		$arrNewRanges = MchGdbcUtils::overlapIntervals($arrNewRanges);

		foreach($arrNewRanges as $arrRange){
			$arrFormattedRange[ $arrRange[0] ] = $arrRange[1];
		}

		return $arrFormattedRange;

	}

	public static function getFormattedIpRangeForDb($receivedIpAddress)
	{
		if(empty($receivedIpAddress))
			return array();

		$receivedIpAddress = trim($receivedIpAddress);

		$arrPreparedData = array();
		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($receivedIpAddress);
		if(-1 !== $ipVersion) // single IP
		{
			if($ipVersion === MchGdbcIPUtils::IP_VERSION_4)
			{
				$arrPreparedData[MchGdbcIPUtils::IP_VERSION_4] = array(MchGdbcIPUtils::ipAddressToNumber($receivedIpAddress), 1);
			}
			else
			{
				$arrPreparedData[MchGdbcIPUtils::IP_VERSION_6] = array(MchGdbcIPUtils::compressIPV6($receivedIpAddress), 1);
			}

			return $arrPreparedData;
		}

		$sanitizedRange = MchGdbcIPUtils::sanitizeCIDRRange($receivedIpAddress);
		if(null !== $sanitizedRange) // CIDR Block
		{
			$ipVersion = MchGdbcIPUtils::getIpAddressVersion(MchGdbcIPUtils::sanitizeIpAddress($sanitizedRange));
			if($ipVersion === MchGdbcIPUtils::IP_VERSION_4)
			{
				$sanitizedRange = MchGdbcIPUtils::getCIDRRangeBounds($sanitizedRange);
				if(empty($sanitizedRange[0]) || empty($sanitizedRange[1]))
					return array();

				$sanitizedRange[0] = MchGdbcIPUtils::ipAddressToNumber($sanitizedRange[0]);
				$sanitizedRange[1] = MchGdbcIPUtils::ipAddressToNumber($sanitizedRange[1]);

				$arrPreparedData[$ipVersion] = $sanitizedRange;

			}
			else // IPV6
			{
				list($ipv6, $bits) = explode('/', $sanitizedRange, 2);

				$ipv6 = MchGdbcIPUtils::compressIPV6($ipv6);

				$arrPreparedData[ $ipVersion ] = $bits == MchGdbcIPUtils::IP_V6_MAX_BITS ? array($ipv6, 1) : array("$ipv6/$bits", 1);
			}

			return $arrPreparedData;
		}
		$arrSanitizedRange = explode('-', $receivedIpAddress, 2);
		if(2 !== count($arrSanitizedRange))
			return array();

		$sanitizedLowIp  =  MchGdbcIPUtils::sanitizeIpAddress(MchGdbcIPUtils::sanitizeCIDRRange($arrSanitizedRange[0]));
		$sanitizedHighIp =  MchGdbcIPUtils::sanitizeIpAddress(MchGdbcIPUtils::sanitizeCIDRRange($arrSanitizedRange[1]));

		if(!MchGdbcIPUtils::isValidIpAddress($sanitizedLowIp) || !MchGdbcIPUtils::isValidIpAddress($sanitizedHighIp)) {
			return array();
		}

		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($sanitizedLowIp);
		if($ipVersion !== MchGdbcIPUtils::getIpAddressVersion($sanitizedHighIp))
			return array();

		if( $ipVersion !== MchGdbcIPUtils::IP_VERSION_4 ) // non standard range allowed just for IPv4
			return array();

		$minIpNumber = MchGdbcIPUtils::ipAddressToNumber($sanitizedLowIp, MchGdbcIPUtils::IP_VERSION_4);
		$maxIpNumber = MchGdbcIPUtils::ipAddressToNumber($sanitizedHighIp, MchGdbcIPUtils::IP_VERSION_4);

		if($minIpNumber >= $maxIpNumber) // single IP
		{
			$arrPreparedData[$ipVersion] = array($minIpNumber, 1);
		}
		else
		{
			$arrPreparedData[$ipVersion] = array($minIpNumber, $maxIpNumber);
		}

		return $arrPreparedData;

	}


	public static function getFormattedIpRangesForDisplay($arrSavedIpRanges)
	{

		$arrIps = array();
		foreach((array)$arrSavedIpRanges as $ipVersion => $arrSavedIps)
		{

			foreach($arrSavedIps as $savedIp => $value)
			{
				if($ipVersion == MchGdbcIPUtils::IP_VERSION_6)
				{
					$arrIps[] = $savedIp . '|' . MchGdbcIPUtils::getCIDRNumberOfHosts($savedIp);
					continue;
				}

				if($value === 1)
				{
					$arrIps[] = MchGdbcIPUtils::ipAddressFromNumber( $savedIp, MchGdbcIPUtils::IP_VERSION_4 ) . '|' . 1;

					continue;
				}

//				$arrRange = explode('/', $savedIp);
//				if(isset($arrRange[1]) && !isset($arrRange[2]))
//				{
//					$arrIps[] = $savedIp . '|' . MchGdbcIPUtils::getCIDRNumberOfHosts($savedIp);
//
//					continue;
//				}

				$arrRange = array($savedIp , $value);
				if(is_numeric($arrRange[0]) && is_numeric($arrRange[1]))
				{
					$displayIp  = MchGdbcIPUtils::ipAddressFromNumber($arrRange[0], MchGdbcIPUtils::IP_VERSION_4) . ' - ' . MchGdbcIPUtils::ipAddressFromNumber($arrRange[1], MchGdbcIPUtils::IP_VERSION_4);
					$displayIp .= '|' . ($arrRange[1] - $arrRange[0] + 1);
					$arrIps[] = $displayIp;

					continue;
				}

			}
		}

		return array_reverse($arrIps);
	}



	public static function getCountryNameByIpAddress($ipAddress)
	{
		return GoodByeCaptchaUtils::getCountryNameById(self::getCountryIdByIpAddress($ipAddress));
	}

	public static function getCountryCodeByIpAddress($ipAddress)
	{
		return GoodByeCaptchaUtils::getCountryCodeById(self::getCountryIdByIpAddress($ipAddress));
	}

	public static function getCountryIdByIpAddress($ipAddress)
	{
		return MchGdbcIpCountryLocator::getCountryIdByIpAddress($ipAddress);
	}


	private function __construct(){}
}