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

class MchGdbcHttpRequest
{
	CONST PROXY_CLOUD_FLARE        = 1;
	CONST PROXY_INCAPSULA          = 2;
	CONST PROXY_RACK_SPACE         = 3;
	CONST PROXY_AMAZON_EC2         = 4;
	CONST PROXY_SUCURI_CLOUD_PROXY = 5;
	CONST PROXY_AMAZON_CLOUD_FRONT = 6;

	private static $proxyServerId = -1;

	public static function getServerRequestTime($withMicroSecondPrecision = false)
	{
		static $requestTime = null;
		if(null !== $requestTime && !$withMicroSecondPrecision)
			return $requestTime;

		if($withMicroSecondPrecision && isset($_SERVER['REQUEST_TIME_FLOAT'])){
			return $_SERVER['REQUEST_TIME_FLOAT'];
		}

		return $requestTime = empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'];
	}

	public static function isThroughProxy()
	{
		return count(self::getDetectedProxyHeaders()) > 0;
	}

	public static function getDetectedProxyServiceId()
	{
		(-1 === self::$proxyServerId) ? self::getClientIp() : null;

		return self::$proxyServerId;
	}

	public static function getDetectedProxyServiceName()
	{
		switch (self::getDetectedProxyServiceId())
		{
			case self::PROXY_CLOUD_FLARE :
				return 'CloudFlare';
			case self::PROXY_INCAPSULA   :
				return 'Incapsula';
			case self::PROXY_RACK_SPACE   :
				return 'RackSpace';
			case self::PROXY_SUCURI_CLOUD_PROXY   :
				return 'Sucuri CloudProxy';
			case self::PROXY_AMAZON_CLOUD_FRONT :
				return 'Amazon CloudFront';
			case self::PROXY_AMAZON_EC2 :
				return 'Amazon EC2';
		}

		return null;
	}

	public static function getTrustedServiceProxyHeaders($serviceProxyId)
	{
		switch($serviceProxyId)
		{
			case self::PROXY_CLOUD_FLARE :
				return array('HTTP_CF_CONNECTING_IP');
			case self::PROXY_INCAPSULA   :
				return array('HTTP_INCAP_CLIENT_IP');
			case self::PROXY_RACK_SPACE   :
				return array('HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_FORWARDED_FOR');
			case self::PROXY_SUCURI_CLOUD_PROXY   :
				return array('HTTP_X_SUCURI_CLIENTIP');
			case self::PROXY_AMAZON_CLOUD_FRONT :
				return array('HTTP_X_FORWARDED_FOR');
			case self::PROXY_AMAZON_EC2 :
				return array('HTTP_X_FORWARDED_FOR');

		}

		return array();
	}

	public static function getDetectedProxyHeaders(){
		$arrProxyHeaders  = array();
		foreach ((array)MchGdbcUtils::getAllWebProxyHeaders() as $proxyHeader) {
			isset($_SERVER[$proxyHeader]) ? $arrProxyHeaders[] = $proxyHeader : null;
		}

		return $arrProxyHeaders;
	}

	public static function getClientIp(array $arrTrustedProxyHeaders = array())
	{
		static $clientIp = 0;
		if(0 !== $clientIp )
			return $clientIp;

		// Handle NGINX Proxies
		(!empty($_SERVER['HTTP_REMOTE_ADDR']) && empty( $_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'] : null;

		if(empty($_SERVER['REMOTE_ADDR']) || -1 === ($ipVersion = MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR'])))
			return null;

		if( $clientIp = self::getIpAddressFromCloudFlare() ){
			self::$proxyServerId = self::PROXY_CLOUD_FLARE;
			return $clientIp;
		}

		if( $clientIp = self::getIpAddressFromSucuriCloudProxy() ){
			self::$proxyServerId = self::PROXY_SUCURI_CLOUD_PROXY;
			return $clientIp;
		}

		if( $clientIp = self::getIpAddressFromIncapsula() ){
			self::$proxyServerId = self::PROXY_INCAPSULA;
			return $clientIp;
		}

		if( $clientIp = self::getIpAddressFromRackSpace() ){
			self::$proxyServerId = self::PROXY_RACK_SPACE;
			return $clientIp;
		}

		if( $clientIp = self::getIpAddressFromAmazonCloudFront() ){
			self::$proxyServerId = self::PROXY_AMAZON_CLOUD_FRONT;
			return $clientIp;
		}


		if( $clientIp = self::getIpAddressFromAmazonEC2() ){
			self::$proxyServerId = self::PROXY_AMAZON_EC2;
			return $clientIp;
		}


//		$arrProxyHeaders = array(
//			'HTTP_X_FORWARDED_FOR',
//			'HTTP_CLIENT_IP',
//			'HTTP_X_REAL_IP',
//			'HTTP_X_FORWARDED',
//			'HTTP_FORWARDED'
//		);
//
//		if(!empty($arrTrustedProxyIps) && in_array($_SERVER['REMOTE_ADDR'], $arrTrustedProxyIps, true))
//		{
//			foreach ($arrProxyHeaders as $proxyHeader)
//			{
//				if(null !== ($clientIp = self::getClientIpAddressFromProxyHeader($proxyHeader)))
//					return $clientIp;
//			}
//		}


		foreach($arrTrustedProxyHeaders as $trustedProxyHeader)
		{
			$clientIp = self::getClientIpAddressFromProxyHeader($trustedProxyHeader);

			if(isset($clientIp))
				break;
		}

		self::$proxyServerId = 0;

		empty($clientIp) ? $clientIp = $_SERVER['REMOTE_ADDR'] : null;

		return $clientIp;
	}


	public static function getClientIpAddressFromProxyHeader($proxyHeader)
	{
		if(empty($_SERVER[$proxyHeader]))
			return null;

		$arrClientIps = explode(',', $_SERVER[$proxyHeader]);

		if (empty($arrClientIps[0]))
			return null;

		$arrClientIps[0] = str_replace(' ', '', $arrClientIps[0]);

		if (preg_match('{((?:\d+\.){3}\d+)\:\d+}', $arrClientIps[0], $match))
			$arrClientIps[0] = trim($match[1]);

		return (-1 !== MchGdbcIPUtils::getIpAddressVersion($arrClientIps[0])) ? $arrClientIps[0] : null;

	}

	private static function getIpAddressFromRackSpace()
	{
		if(0 !== strpos($_SERVER['REMOTE_ADDR'], '10.'))
			return null;

		$arrProxyHeaders = array('HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_FORWARDED_FOR');

		foreach($arrProxyHeaders as $proxyHeader)
		{
			if(empty($_SERVER[$proxyHeader]))
				continue;

			if(null === ($ipAddress = self::getClientIpAddressFromProxyHeader($proxyHeader)))
				continue;

			if( ! MchGdbcTrustedIPRanges::isIPInRackSpaceRanges($_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR'])) )
				continue;

			return $ipAddress;
		}

		return null;
	}

	private static function getIpAddressFromIncapsula()
	{
		if(empty($_SERVER['HTTP_INCAP_CLIENT_IP']) || (-1 === ($ipVersion = MchGdbcIPUtils::getIpAddressVersion($_SERVER['HTTP_INCAP_CLIENT_IP']))))
			return null;

		return MchGdbcTrustedIPRanges::isIPInIncapsulaRanges( $_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['HTTP_INCAP_CLIENT_IP'] : null;

	}

	private static function getIpAddressFromCloudFlare()
	{

		if(empty($_SERVER['HTTP_CF_CONNECTING_IP']) || -1 === ($ipVersion = MchGdbcIPUtils::getIpAddressVersion($_SERVER['HTTP_CF_CONNECTING_IP'])))
			return null;

		return MchGdbcTrustedIPRanges::isIPInCloudFlareRanges( $_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : null;

	}

	private static function getIpAddressFromSucuriCloudProxy()
	{
		if(empty($_SERVER['HTTP_X_SUCURI_CLIENTIP']) || -1 === ($ipVersion = MchGdbcIPUtils::getIpAddressVersion($_SERVER['HTTP_X_SUCURI_CLIENTIP'])))
			return null;

		$ipAddress = MchGdbcTrustedIPRanges::isIPInSucuriCloudProxyRanges($_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['HTTP_X_SUCURI_CLIENTIP'] : null;

		if(null !== $ipAddress)
			return $ipAddress;

		$hostAddress = null;
		if(!empty($_SERVER['SERVER_ADDR']))
			$hostAddress = $_SERVER['SERVER_ADDR'];
		elseif(!empty($_SERVER['LOCAL_ADDR']))
			$hostAddress = $_SERVER['LOCAL_ADDR'];
		elseif(!empty($_SERVER['SERVER_NAME']))
			$hostAddress = @gethostbyname($_SERVER['SERVER_NAME'] . '.');

		if(!MchGdbcIPUtils::isPublicIpAddress($hostAddress))
			return null;

		$hostName = @gethostbyaddr($hostAddress);

		return @preg_match('/^cloudproxy[0-9]+\.sucuri\.net$/', $hostName) ? $_SERVER['HTTP_X_SUCURI_CLIENTIP'] : null;

	}

	private static function getIpAddressFromAmazonEC2()
	{
		if(null === ($proxyIpAddress = self::getClientIpAddressFromProxyHeader('HTTP_X_FORWARDED_FOR')))
			return null;

		if( ! MchGdbcIPUtils::isPublicIpAddress($_SERVER['REMOTE_ADDR']) && MchGdbcIPUtils::isPublicIpAddress($proxyIpAddress) )
		{
			$isAmazon =  ( !empty($_SERVER['SERVER_SOFTWARE'])  && (false !== stripos($_SERVER['SERVER_SOFTWARE'], 'Amazon')) ) ? true : ( !empty($_SERVER['SERVER_SIGNATURE'])  && (false !== stripos($_SERVER['SERVER_SIGNATURE'], 'Amazon')) );

			if($isAmazon) {
				return $proxyIpAddress;
			}
		}


		return MchGdbcTrustedIPRanges::isIPInAmazonEC2Ranges( $_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR']) ) ? $proxyIpAddress : null;
	}

	private static function getIpAddressFromAmazonCloudFront()
	{
		if( !empty($_SERVER['HTTP_X_AMZ_CF_ID']) || (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Amazon CloudFront') || (!empty($_SERVER['HTTP_VIA']) &&  false !== stripos($_SERVER['HTTP_VIA'], 'CloudFront')) )
		{

			if(null === ($proxyIpAddress = self::getClientIpAddressFromProxyHeader('HTTP_X_FORWARDED_FOR')))
				return null;

			return MchGdbcTrustedIPRanges::isIPInAmazonCloudFrontRanges( $_SERVER['REMOTE_ADDR'], MchGdbcIPUtils::getIpAddressVersion($_SERVER['REMOTE_ADDR']) ) ? $proxyIpAddress : null;

		}

		return null;
	}

}