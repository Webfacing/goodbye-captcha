<?php

/**
 *
 * @package   WPBruiser
 * @author    Mihai Chelaru
 * @license   GPL-2.0+
 * @link      http://www.wpbruiser.com
 * @copyright 2014 WPBruiser
 *
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

class_exists('GoodByeCaptcha') || require_once 'goodbye-captcha.php';

class GoodByeCaptchaUninstaller
{
	public function __construct()
	{

		if(!current_user_can( 'activate_plugins')){
			exit;
		}

		if(is_callable(array('GoodByeCaptcha', 'isProVersion')) && GoodByeCaptcha::isProVersion() && is_plugin_active(plugin_basename(GoodByeCaptcha::getMainFilePath()))) {
			return;
		}

		GdbcTaskScheduler::unScheduleGdbcTasks();

		foreach(GdbcModulesController::getRegisteredModules() as $moduleName => $arrModuleInfo)
		{
			if( null === ($adminModuleInstance = GdbcModulesController::getAdminModuleInstance($moduleName)) )
				continue;

			$adminModuleInstance->deleteAllSettingOptions(false);
			$adminModuleInstance->deleteAllSettingOptions(true);
		}


		$adminNotice = new GdbcAdminNotice(null, null);
		$adminNotice->deleteAllNotices();

		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS " . GdbcDbAccessController::getAttemptsTableName());

	}

}

new GoodByeCaptchaUninstaller();
