<?php

class MchGdbcIpCountryLocator
{
	public static function getCountryIdByIpAddress($ipAddress, $ipVersion = null)
	{
		empty($ipVersion) ? $ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress) : null;

		if( -1 === $ipVersion)
			return null;

		if($ipVersion === MchGdbcIPUtils::IP_VERSION_4)
			return self::getCountryIdByIPV4($ipAddress);

		if(null !== ($mappedIPV4 = MchGdbcIPUtils::extractMappedIPV4FromIPv6($ipAddress)))
			return self::getCountryIdByIPV4($mappedIPV4);

		return self::getCountryIdByIPV6($ipAddress);
	}

		
	private static function getCountryIdByIPV4($ipAddress)
	{
		if(null === ($ipNumber = MchGdbcIPUtils::ipAddressToNumber($ipAddress, MchGdbcIPUtils::IP_VERSION_4)))
			return null;

		$dataFilePath = null;
		
		if(16777216 <= $ipNumber && $ipNumber <= 1357878193)
			$dataFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'country-ips' . DIRECTORY_SEPARATOR . 'ipv4' . DIRECTORY_SEPARATOR . 'country-ipv4-0.dat';
		elseif(1357878194 <= $ipNumber && $ipNumber <= 2585330456)
			$dataFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'country-ips' . DIRECTORY_SEPARATOR . 'ipv4' . DIRECTORY_SEPARATOR . 'country-ipv4-1.dat';
		elseif(2585330457 <= $ipNumber && $ipNumber <= 3389308927)
			$dataFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'country-ips' . DIRECTORY_SEPARATOR . 'ipv4' . DIRECTORY_SEPARATOR . 'country-ipv4-2.dat';
		elseif(3389308928 <= $ipNumber && $ipNumber <= 3758096383)
			$dataFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'country-ips' . DIRECTORY_SEPARATOR . 'ipv4' . DIRECTORY_SEPARATOR . 'country-ipv4-3.dat';

		if(null === $dataFilePath)
			return null;

		if(false === ($fileHandle = @fopen($dataFilePath, 'rb')))
			return null;

		fseek($fileHandle, 0, SEEK_END);
		$startPosition = 0;
		$endPosition   = ftell($fileHandle);
		fseek($fileHandle, SEEK_SET);

		$bufferSize = 10;

		if(($endPosition % $bufferSize) !== 0) // data file corrupted
		{
			fclose($fileHandle);
			return null;
		}

		while($startPosition < $endPosition)
		{
			$midPosition = ceil( (($startPosition + $endPosition) / 2)  / $bufferSize ) * $bufferSize;

			fseek($fileHandle, $midPosition - $bufferSize, SEEK_SET);
			$data = fread($fileHandle, $bufferSize);
			$data = unpack("NminIp/NmaxIp/ncid", $data);

			if( empty($data['minIp']) || empty($data['maxIp']) )
				break;

			if($data['minIp'] < 0) $data['minIp'] += 4294967296;

			if($ipNumber < $data['minIp']){
				$endPosition = $midPosition - $bufferSize;
				continue;
			}

			if($data['maxIp'] < 0) $data['maxIp'] += 4294967296;

			if($ipNumber > $data['maxIp']){
				$startPosition = $midPosition;
				continue;
			}

			fclose($fileHandle);
			return !empty($data['cid']) ? $data['cid'] : null;
		}

		fclose($fileHandle);
		return null;
	}

	private static function getCountryIdByIPV6($ipAddress)
	{
		if(null === ($ipBinary = MchGdbcIPUtils::ipAddressToBinary($ipAddress, MchGdbcIPUtils::IP_VERSION_6)))
			return null;

		$dataFilePath = null;
		
		if(MchGdbcIPUtils::ipAddressToBinary('2001:200::', MchGdbcIPUtils::IP_VERSION_6) <= $ipBinary && $ipBinary <= MchGdbcIPUtils::ipAddressToBinary('2c0f:fff0:ffff:ffff:ffff:ffff:ffff:ffff', MchGdbcIPUtils::IP_VERSION_6))
			$dataFilePath = dirname(__FILE__)  . DIRECTORY_SEPARATOR . 'country-ips' . DIRECTORY_SEPARATOR . 'ipv6' . DIRECTORY_SEPARATOR . 'country-ipv6-0.dat';

		if(null === $dataFilePath)
			return null;

		if(false === ($fileHandle = @fopen($dataFilePath, 'rb')))
			return null;

		fseek($fileHandle, 0, SEEK_END);
		$startPosition = 0;
		$endPosition   = ftell($fileHandle);
		fseek($fileHandle, SEEK_SET);

		$bufferSize = 34;

		if(($endPosition % $bufferSize) !== 0) // data file corrupted
		{
			fclose($fileHandle);
			return null;
		}

		while($startPosition < $endPosition)
		{
			$midPosition = ceil( (($startPosition + $endPosition) / 2)  / $bufferSize ) * $bufferSize;

			fseek($fileHandle, $midPosition - $bufferSize, SEEK_SET);

			if($ipBinary < fread($fileHandle, 16)){
				$endPosition = $midPosition - $bufferSize;
				continue;
			}

			if($ipBinary > fread($fileHandle, 16)){
				$startPosition = $midPosition;
				continue;
			}

			$data = @unpack("ncid", fread($fileHandle, 2));

			fclose($fileHandle);
			return !empty($data['cid']) ? $data['cid'] : null;

		}

		fclose($fileHandle);
		return null;
	}

}
		