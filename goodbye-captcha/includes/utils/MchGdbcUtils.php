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

final class MchGdbcUtils
{
	public static function stripNonAlphaCharacters($strText)
	{
		return preg_replace("/[^a-z]/i", '', $strText );
	}

	public static function stripNonAlphaNumericCharacters($strText)
	{
		return preg_replace("/[^A-Za-z0-9 ]/", '', $strText);
	}

	public static function replaceNonAlphaCharacters($strText, $token = '-')
	{
		$strText = str_replace(' ', '-', $strText);
		$strText = preg_replace('/[^A-Za-z\-]/', '-', $strText);
		$strText = preg_replace('/-+/', $token, trim($strText, '-'));

		return $token === '-' ? $strText : str_replace('-', $token, $strText);
	}

	public static function replaceNonAlphaNumericCharacters($strText, $token = '-')
	{
		$strText = str_replace(' ', '-', $strText);
		$strText = preg_replace('/[^A-Za-z0-9\-]/', '-', $strText);
		$strText = preg_replace('/-+/', $token, trim($strText, '-'));
		return $token === '-' ? $strText : str_replace('-', $token, $strText);
	}

	public static function stripLeftAndRightSlashes($str)
	{
		return trim($str, '/\\');
	}

	public static function stringStartsWith($string, $stringToFind)
	{
		return 0 === strpos($string, $stringToFind);
	}

	public static function stringEndsWith($string, $stringToFind, $caseSensitive = true)
	{
		return 0 === substr_compare($string, $stringToFind, -($count = strlen($stringToFind)) , $count, $caseSensitive ? false : true);
	}

	public static function normalizeNewLine($strText, $to = PHP_EOL )
	{
		if ( ! is_string($strText) )
			return $strText;

		$arrNewLine = array( "\r\n", "\r", "\n" );

		if ( ! in_array($to, $arrNewLine) )
			return $strText;

		return str_replace($arrNewLine, $to, $strText);
	}

	public static function overlapIntervals(array $arrIntervals)
	{
		if(!isset($arrIntervals[1]))
			return $arrIntervals;

		$arrIntervals = array_values($arrIntervals);
		usort($arrIntervals, array(__CLASS__, 'sortIntervals'));
		$n = 0; $len = count($arrIntervals);
		for ($i = 1; $i < $len; ++$i)
		{
			if ($arrIntervals[$i][0] > $arrIntervals[$n][1] + 1) {
				$n = $i;
			}
			else
			{
				if ($arrIntervals[$n][1] < $arrIntervals[$i][1])
					$arrIntervals[$n][1] = $arrIntervals[$i][1];

				unset($arrIntervals[$i]);
			}
		}

		return array_values($arrIntervals);
	}

	private static function sortIntervals($firstArray, $secondArray)
	{
		//print_r($firstArray); print_r($secondArray);
		
		return $firstArray[0] - $secondArray[0];
	}


	public static function longDigitBaseConvert($longDigit, $sourceBase, $destBase, $minDigits = 1)
	{
		$longDigit   = strtolower($longDigit);
		$sourceBase  = (int)$sourceBase;
		$destBase    = (int)$destBase;
		$minDigits   = (int)$minDigits;

		if($minDigits < 1 || $sourceBase < 2 || $destBase < 2 || $sourceBase > 36 || $destBase > 36)
			return null;


		static $gmpExtensionLoaded = null;
		(null === $gmpExtensionLoaded) ?  $gmpExtensionLoaded = extension_loaded( 'gmp' ) : null;

		$result = null;

		if( $gmpExtensionLoaded )
		{
			$longDigit = ltrim( $longDigit, '0' );
			$result = gmp_strval( gmp_init( $longDigit ? $longDigit : '0', $sourceBase ), $destBase );
			return str_pad( $result, $minDigits, '0', STR_PAD_LEFT );
		}

		static $bcmathExtensionLoaded = null;
		(null === $bcmathExtensionLoaded) ?  $bcmathExtensionLoaded = extension_loaded( 'bcmath' ) : null;

		static $arrBaseChars = array(
				10 => 'a', 11 => 'b', 12 => 'c', 13 => 'd', 14 => 'e', 15 => 'f',
				16 => 'g', 17 => 'h', 18 => 'i', 19 => 'j', 20 => 'k', 21 => 'l',
				22 => 'm', 23 => 'n', 24 => 'o', 25 => 'p', 26 => 'q', 27 => 'r',
				28 => 's', 29 => 't', 30 => 'u', 31 => 'v', 32 => 'w', 33 => 'x',
				34 => 'y', 35 => 'z',

				'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
				'6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11,
				'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17,
				'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23,
				'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29,
				'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
		);


		if($bcmathExtensionLoaded)
		{
			$decimal = '0';
			foreach ( str_split($longDigit) as $char ) {
				$decimal = bcmul( $decimal, $sourceBase );
				$decimal = bcadd( $decimal, $arrBaseChars[$char] );
			}

			for ( $result = ''; bccomp( $decimal, 0 ); $decimal = bcdiv( $decimal, $destBase, 0 ) ) {
				$result .= $arrBaseChars[bcmod( $decimal, $destBase )];
			}

			return str_pad( strrev( $result ), $minDigits, '0', STR_PAD_LEFT );
		}


		$inDigits = array();
		foreach ( str_split($longDigit) as $char ) {
			$inDigits[] = $arrBaseChars[$char];
		}

		$result = '';
		while ( $inDigits )
		{
			$work = 0;
			$workDigits = array();

			foreach ( $inDigits as $digit )
			{
				$work *= $sourceBase;
				$work += $digit;

				if ( $workDigits || $work >= $destBase ) {
					$workDigits[] = (int)( $work / $destBase );
				}

				$work %= $destBase;
			}

			$result  .= $arrBaseChars[$work];
			$inDigits = $workDigits;
		}

		return str_pad( strrev( $result ), $minDigits, '0', STR_PAD_LEFT );

	}


	public static function getAllWebProxyHeaders()
	{
		return array(
				'HTTP_CLIENT_IP',
				'HTTP_X_CLIENT_IP',
				'HTTP_REAL_IP',
				'HTTP_X_REAL_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_X_FORWARDED_HOST',
				'HTTP_X_FORWARDED_SERVER',
				'HTTP_FORWARDED_FOR',
				'HTTP_VIA',
				'HTTP_FORWARDED',
				'HTTP_FORWARDED_FOR_IP',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_INCAP_CLIENT_IP',
				'HTTP_CF_CONNECTING_IP',
				'HTTP_X_SUCURI_CLIENTIP',
				'VIA',
				'X_FORWARDED_FOR',
				'FORWARDED_FOR',
				'X_FORWARDED',
				'FORWARDED',
				'CLIENT_IP',
				'FORWARDED_FOR_IP',
				'HTTP_PROXY_CONNECTION',
		);
	}

}