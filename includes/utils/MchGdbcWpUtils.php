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

final class MchGdbcWpUtils
{
	public static function getSiteNameById($siteId)
	{
		return get_blog_option($siteId, 'blogname', null);
	}

	public static function isUserLoggedIn()
	{
		return is_user_logged_in();
	}

	public static function isAdminLoggedIn()
	{
		return self::isSuperAdminLoggedIn();
	}

	public static function isSuperAdminLoggedIn()
	{
		return is_super_admin();
	}

	public static function isUserInDashboard()
	{
		return  ( ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && is_admin() );
	}

	public static function isAdminInDashboard()
	{
		return self::isAdminLoggedIn() && self::isUserInDashboard();
	}


	public static function isUserInNetworkDashboard()
	{
		return  is_network_admin();
	}

	public static function isAdminInNetworkDashboard()
	{
		return self::isAdminLoggedIn() && self::isUserInNetworkDashboard();
	}

	public static function isAjaxRequest()
	{
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX && is_admin());
	}

	public static function isXmlRpcRequest()
	{
		return defined('XMLRPC_REQUEST') && XMLRPC_REQUEST;
	}

//	public static function isStandardWordPressLoginRequest()
//	{
//		return function
//	}

	public static function isMultiSite()
	{
		return is_multisite();
	}

	public static function getAdminEmailAddress()
	{
		return get_bloginfo('admin_email');
	}

	public static function getAdminDisplayName()
	{
		if(! function_exists('get_user_by') )
			require_once(ABSPATH .'wp-includes/pluggable.php');

		$adminUser = get_user_by('email', get_bloginfo('admin_email')); //get_option( 'admin_email' );
		if(false === $adminUser)
			return null;

		return !empty($adminUser->display_name) ? $adminUser->display_name : null;
	}


	public static function getAdminFullName()
	{
		if(! function_exists('get_user_by') )
			require_once(ABSPATH .'wp-includes/pluggable.php');

		$adminUser = get_user_by('email', get_bloginfo('admin_email')); //get_option( 'admin_email' );
		if(false === $adminUser)
			return null;

		$adminFullName  = empty($adminUser->first_name) ? '' : $adminUser->first_name;
		$adminFullName .= empty($adminUser->last_name)  ? '' : ' ' . $adminUser->last_name;

		return trim($adminFullName);

	}


	public static function isPluginNetworkActivated($pluginFilePath)
	{
		if(!self::isMultiSite())
			return false;

		function_exists( 'is_plugin_active_for_network' ) || require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		return  !empty($pluginFilePath) ? is_plugin_active_for_network(plugin_basename($pluginFilePath)) : false;
	}

	public static function isPermaLinkActivated()
	{
		return (bool)(get_option('permalink_structure'));
	}

//	public static function getServerRequestTime($withMicroSecondPrecision = false)
//	{
//		static $requestTime = null;
//		if(null !== $requestTime && !$withMicroSecondPrecision)
//			return $requestTime;
//
//		if($withMicroSecondPrecision && isset($_SERVER['REQUEST_TIME_FLOAT'])){
//			return $_SERVER['REQUEST_TIME_FLOAT'];
//		}
//
//		return $requestTime = ( empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'] );
//	}

	public static function getAjaxUrl()
	{

		if( defined('SUNRISE_LOADED')  && !function_exists('dm_redirect_admin')){ //&& MchGdbcWpUtils::isPluginNetworkActivated('/wordpress-mu-domain-mapping/domain_mapping.php')
			foreach(wp_get_active_network_plugins() as $pluginName)
			{
				if(false === strpos($pluginName, '/domain_mapping.php'))
					continue;

				wp_register_plugin_realpath( $pluginName );

				include_once( $pluginName );
				break;
			}

		}
		
		return admin_url( 'admin-ajax.php', 'relative' );
		
		$ajaxUrl = admin_url('admin-ajax.php', self::isSslRequest() ? 'admin' : 'http');

		if(0 === strpos(self::getCurrentPageUrl(), 'https') && 0 !== strpos($ajaxUrl, 'https'))
			return  str_replace('http:', 'https:', $ajaxUrl);

		if(0 === strpos(self::getCurrentPageUrl(), 'http:') && 0 !== strpos($ajaxUrl, 'http:'))
			return str_replace('https:', 'http:', $ajaxUrl);

		return $ajaxUrl;
	}

	public static function isSslRequest()
	{
		static $isSsl = null;
		if(null !== $isSsl)
			return $isSsl;

		if (isset($_SERVER['HTTP_CF_VISITOR']) && false !== strpos($_SERVER['HTTP_CF_VISITOR'], 'https'))
			return $isSsl = true;

		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0)
			return $isSsl = true;

//		if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443)) # wp is_ssl() function is looking for port 443 as well
//			return $isSsl = true;

		if(isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
			return $isSsl = true;

		if(stripos(get_option('siteurl'), 'https') === 0)
			return $isSsl = true;

		return $isSsl = is_ssl();
	}

	public static function getCurrentPageUrl()
	{
		static $pageUrl = null;

		if(null !== $pageUrl)
			return $pageUrl;

//		if(is_front_page())
//			return $pageUrl = home_url('/', self::isSslRequest());

		$pageUrl = self::isSslRequest() ? 'https://' : 'http://';

		if(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != 80))
			$pageUrl .= $_SERVER['SERVER_NAME' ]. ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$pageUrl .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return $pageUrl = esc_url($pageUrl);

	}

	public static function getCurrentBlogLink()
	{
		return '<a href = "'. esc_url(get_bloginfo('url')) .'">' . get_bloginfo('name') . '</a>';
	}

	public static function getAllBlogIds()
	{
		global $wpdb;

		if( empty($wpdb->blogs) )
			return array();

		return false === ( $arrBlogs = $wpdb->get_col(  "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'" ) ) ? array() : $arrBlogs;

	}


	public static function getDirectoryPathForCache()
	{
		$arrPossibleDirectoryPath = array(
			//dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_temp',
			WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'cache',
			WP_CONTENT_DIR,
		);

		$arrUploadDirInfo = wp_upload_dir();
		if(MchGdbcWpUtils::isMultiSite()){
			switch_to_blog( 1 );
			$arrUploadDirInfo = wp_upload_dir();
			restore_current_blog();
		}

		(empty($arrUploadDirInfo['error']) && !empty($arrUploadDirInfo['basedir']))
		? $arrPossibleDirectoryPath[] = $arrUploadDirInfo['basedir'] : null;

		defined('WP_TEMP_DIR') ? $arrPossibleDirectoryPath[] = WP_TEMP_DIR : null;

//		$arrPossibleDirectoryPath[] = @sys_get_temp_dir();
//		$arrPossibleDirectoryPath[] = @ini_get('upload_tmp_dir');
//
//		!empty($_SERVER['TMP'])     ? $arrPossibleDirectoryPath[] = $_SERVER['TMP']    : null;
//		!empty($_SERVER['TEMP'])    ? $arrPossibleDirectoryPath[] = $_SERVER['TEMP']   : null;
//		!empty($_SERVER['TMPDIR'])  ? $arrPossibleDirectoryPath[] = $_SERVER['TMPDIR'] : null;
//
//		$arrPossibleDirectoryPath[] = ('so' !== PHP_SHLIB_SUFFIX) ? 'C:/Temp' : '/tmp';

		foreach($arrPossibleDirectoryPath as $directoryPath)
		{
			$tempDirPath = rtrim($directoryPath, '/\\');
			if(self::isDirectoryUsable($tempDirPath, false) )
				return $tempDirPath;
		}

		return null;
	}


	private static function isPathAccessible($path)
	{
		$openBaseDirSettings = strtolower( str_replace( '\\', '/', ini_get( 'open_basedir' ) ) );
		if(empty($openBaseDirSettings))
			return true;

		$path = trailingslashit( strtolower( str_replace( '\\', '/', $path ) ) );

		foreach( (array)explode( PATH_SEPARATOR, $openBaseDirSettings ) as $openPath)
		{
			if(empty($openPath))
				continue;

			if( 0 === strpos($path, $openPath) )
				return true;
		}

		return false;

	}

	public static function isDirectoryUsable($directoryPath, $createIfNotExists = false)
	{
		PHP_VERSION_ID < 50300 ? @clearstatcache() : @clearstatcache(true, $directoryPath);

		if(!@is_dir($directoryPath) || !@is_readable($directoryPath))
		{

			if(!self::isPathAccessible($directoryPath))
				return false;

			if($createIfNotExists && !self::createDirectory($directoryPath))
				return false;
		}

		return function_exists('wp_is_writable') ? wp_is_writable($directoryPath) && @is_readable($directoryPath): @is_writable($directoryPath)  && @is_readable($directoryPath);
	}

	public static function createDirectory($directoryPath)
	{
		return wp_mkdir_p(rtrim($directoryPath, '/\\'));
	}

	public static function writeContentToFile($content, $filePath, $exclusiveLock = true)
	{
		$filePointer = @fopen($filePath, 'wb');
		if(false === $filePointer)
			return 0;

		if( false === flock( $filePointer, ( $exclusiveLock ? LOCK_EX : LOCK_EX|LOCK_NB ) ) ){
			fclose($filePointer);
			return 0;
		}

		$bytesWritten = fwrite($filePointer, $content);
		flock($filePointer, LOCK_UN);
		fclose($filePointer);

		return (false === $bytesWritten) ? 0 : $bytesWritten;

	}

	public static function deleteFile($filePath)
	{
		return @unlink( $filePath );
	}

	public static function fileExists($filePath, $clearStatCache = true)
	{
		if($clearStatCache) {
			PHP_VERSION_ID < 50300 ? @clearstatcache() : @clearstatcache(true, $filePath);
		}

		return @file_exists($filePath);
	}

	public static function getDirectoryFiles($dirPath)
	{
		if( (! @is_dir($dirPath) ) || ( ! @is_readable($dirPath) ) )
			return array();

		$arrFiles = array();

		foreach ( new DirectoryIterator ( $dirPath ) as $file ) {
			if(!$file->isFile()) continue;
			$arrFiles[] = $file->getPathName();
		}

		return $arrFiles;
	}

	public static function getDirectorySubDirectories($directoryPath)
	{
		$arrSubDirectories = glob(rtrim($directoryPath, '/\\') . '/*' , GLOB_ONLYDIR | GLOB_NOSORT);

		return false === $arrSubDirectories ? array() : $arrSubDirectories;
	}

	public static function deleteDirectoryContent($directoryPath)
	{
		$directoryPath = rtrim($directoryPath, '/\\');
		if(empty($directoryPath) || !@is_dir($directoryPath))
			return;

		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath), RecursiveIteratorIterator::CHILD_FIRST) as $fileInfo){
			$fileInfo->isDir() ? @rmdir($fileInfo->getRealPath()): @unlink($fileInfo->getRealPath());
		}

		@rmdir($directoryPath);

	}

	private function __construct(){}
}