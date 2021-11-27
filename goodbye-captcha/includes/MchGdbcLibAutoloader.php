<?php

/*
 * Copyright (C) 2016 Mihai Chelaru
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

(PHP_VERSION_ID < 50300)
	? spl_autoload_register( array( 'MchGdbcLibAutoloader', 'autoLoadLibraryClasses' ), false)
	: spl_autoload_register( array( 'MchGdbcLibAutoloader', 'autoLoadLibraryClasses' ), false, true );

final class MchGdbcLibAutoloader
{
	public static function autoLoadLibraryClasses($className)
	{
		static $arrClassMap = null;
		if(null === $arrClassMap) {

			$arrClassMap = array(

				'MchGdbcBaseModule'       => '/modules/MchGdbcBaseModule.php',
				'MchGdbcBasePublicModule'  => '/modules/MchGdbcBasePublicModule.php',
				'MchGdbcBaseAdminModule'  => '/modules/MchGdbcBaseAdminModule.php',
				'MchGdbcGroupedModules' => '/modules/MchGdbcGroupedModules.php',

				'MchGdbcBasePlugin'       => '/plugin/MchGdbcBasePlugin.php',
				'MchGdbcBaseAdminPlugin'  => '/plugin/MchGdbcBaseAdminPlugin.php',
				'MchGdbcBasePublicPlugin' => '/plugin/MchGdbcBasePublicPlugin.php',
				'MchGdbcBaseAdminPage'    => '/plugin/MchGdbcBaseAdminPage.php',
				'MchGdbcPluginUpdater'    => '/plugin/MchGdbcPluginUpdater.php',

				'MchGdbcUtils'       => '/utils/MchGdbcUtils.php',
				'MchGdbcWpUtils'       => '/utils/MchGdbcWpUtils.php',
				'MchGdbcHtmlUtils'     => '/utils/MchGdbcHtmlUtils.php',
				'MchGdbcIPUtils'    => '/utils/MchGdbcIPUtils.php',

				'MchCrypt'               => '/vendor/MchCrypt/MchCrypt.php',
				'Crypt_Blowfish'          => '/vendor/MchCrypt/PhpSecLib-0.3.10/Crypt/Blowfish.php',
				'Math_BigInteger'          => '/vendor/MchCrypt/PhpSecLib-0.3.10/Math/BigInteger.php',

				'MchGdbcHttpRequest' => '/vendor/MchHttp/MchGdbcHttpRequest.php',
				'MchGdbcTrustedIPRanges' =>'/vendor/MchHttp/MchGdbcTrustedIPRanges.php',
				'MchGdbcUnTrustedIPRanges' =>'/vendor/MchHttp/MchGdbcUnTrustedIPRanges.php',
				'MchGdbcIpCountryLocator' => '/vendor/MchHttp/MchGdbcIpCountryLocator.php',
				'MchGdbcHttpUtil' => '/vendor/MchHttp/MchGdbcHttpUtil.php',
				//'MchMaxMindGeoIp' => '/vendor/MaxMind/MchMaxMindGeoIp.php',

				'MchGdbcCache' => '/vendor/MchCache/MchGdbcCache.php',
				'MchGdbcCacheFileStorage' => '/vendor/MchCache/Storage/MchGdbcCacheFileStorage.php',
				'MchGdbcWordPressTransientsStorage' => '/vendor/MchCache/Storage/MchGdbcWordPressTransientsStorage.php',
				'MchGdbcCacheAPCUStorage' => '/vendor/MchCache/Storage/MchGdbcCacheAPCUStorage.php',
				'MchGdbcCacheAPCStorage' => '/vendor/MchCache/Storage/MchGdbcCacheAPCStorage.php',
				'MchGdbcCacheXCacheStorage' => '/vendor/MchCache/Storage/MchGdbcCacheXCacheStorage.php',
				'MchGdbcCacheZendMemoryStorage' => '/vendor/MchCache/Storage/MchGdbcCacheZendMemoryStorage.php',
				'MchGdbcCacheZendDiskStorage' => '/vendor/MchCache/Storage/MchGdbcCacheZendDiskStorage.php',

				'MchGdbcCacheBaseStorage' => '/vendor/MchCache/Storage/MchGdbcCacheBaseStorage.php',


				'MchGdbcWpTaskScheduler' => '/task-scheduler/MchGdbcWpTaskScheduler.php',
				'MchGdbcWpTask' => '/task-scheduler/MchGdbcWpTask.php',
				'MchGdbcAdminNotice' => '/notices/MchGdbcAdminNotice.php',


			);
		}

		return isset($arrClassMap[$className]) ? file_exists($filePath = dirname(__FILE__) . $arrClassMap[$className])
			? include ($filePath)
			: null
			: null;
	}

	private function __clone()
	{}

	private function __construct()
	{}

}