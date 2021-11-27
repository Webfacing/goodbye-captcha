<?php
/**
 *
 * @package   WPBruiser
 * @author    Mihai Chelaru
 * @link      http://www.wpbruiser.com
 * @copyright 2018 WPBruiser
 *
 * @wordpress-plugin
 * Plugin Name: WPBruiser
 * Plugin URI: http://www.wpbruiser.com
 * Description: An extremely powerful anti-spam plugin that blocks spambots without annoying captcha images.
 * Version: 3.1.43
 * Author: Mihai Chelaru
 * Author URI: http://www.wpbruiser.com
 * Text Domain: wp-bruiser
 * Domain Path: /languages
 */


if(!class_exists('GoodByeCaptcha', false))
{
	class GoodByeCaptcha
	{
		
		CONST PLUGIN_VERSION    = '3.1.43';
		CONST PLUGIN_SLUG       = 'wp-bruiser';
		CONST PLUGIN_NAME       = 'WPBruiser';
		CONST PLUGIN_SITE_URL   = 'https://www.wpbruiser.com';
		CONST PLUGIN_SHORT_CODE = 'wpbr';

		private static $arrClassMap = array(

			'GoodByeCaptchaPublic'        => '/engine/GoodByeCaptchaPublic.php',
			'GoodByeCaptchaAdmin'         => '/engine/GoodByeCaptchaAdmin.php',
			'GdbcAjaxController'          => '/engine/GdbcAjaxController.php',
			'GoodByeCaptchaUtils'         => '/engine/GoodByeCaptchaUtils.php',
			'GdbcRequestController'       => '/engine/GdbcRequestController.php',
			'GdbcIPUtils'                 => '/engine/GdbcIPUtils.php',
			'GdbcUpdatesController'       => '/engine/GdbcUpdatesController.php',
			'GdbcModulesController'       => '/engine/GdbcModulesController.php',
			'GdbcBaseAdminModule'         => '/engine/modules/GdbcBaseAdminModule.php',
			'GdbcBasePublicModule'        => '/engine/modules/GdbcBasePublicModule.php',
			'GdbcBaseAdminPage'           => '/engine/admin/pages/GdbcBaseAdminPage.php',
			'GdbcContactFormsAdminPage'   => '/engine/admin/pages/GdbcContactFormsAdminPage.php',
			'GdbcSettingsAdminPage'       => '/engine/admin/pages/GdbcSettingsAdminPage.php',
			'GdbcWordpressAdminPage'      => '/engine/admin/pages/GdbcWordpressAdminPage.php',
			'GdbcExtensionsAdminPage'     => '/engine/admin/pages/GdbcExtensionsAdminPage.php',
			'GdbcECommerceAdminPage'      => '/engine/admin/pages/GdbcECommerceAdminPage.php',
			'GdbcNotificationsAdminPage'  => '/engine/admin/pages/GdbcNotificationsAdminPage.php',
			'GdbcMembershipAdminPage'     => '/engine/admin/pages/GdbcMembershipAdminPage.php',
			'GdbcSecurityAdminPage'       => '/engine/admin/pages/GdbcSecurityAdminPage.php',
			'GdbcOthersAdminPage'         => '/engine/admin/pages/GdbcOthersAdminPage.php',
			'GdbcLicensesAdminPage'       => '/engine/admin/pages/GdbcLicensesAdminPage.php',
			'GdbcReportsAdminPage'        => '/engine/admin/pages/GdbcReportsAdminPage.php',
			'GdbcWelcomeAdminPage'        => '/engine/admin/pages/GdbcWelcomeAdminPage.php',
			'GdbcAdminNotice'             => '/engine/admin/GdbcAdminNotice.php',
			'GdbcDbAccessController'      => '/engine/db-access/GdbcDbAccessController.php',
			'GdbcBruteGuardian'           => '/engine/GdbcBruteGuardian.php',
			'GdbcAttemptEntity'           => '/engine/entities/GdbcAttemptEntity.php',
			'GdbcNotificationsController' => '/engine/GdbcNotificationsController.php',
			'GdbcLogsCleanerTask'         => '/engine/tasks/GdbcLogsCleanerTask.php',
			'GdbcTaskScheduler'           => '/engine/GdbcTaskScheduler.php',

		);

		private function __construct()
		{}

		public static function startRunning()
		{

			GdbcUpdatesController::updateToCurrentVersion();

			$arrPluginInfo = array(
				'PLUGIN_DOMAIN_PATH' => 'languages',
				'PLUGIN_MAIN_FILE'   => self::getMainFilePath(),
				'PLUGIN_SHORT_CODE'  => self::PLUGIN_SHORT_CODE,
				'PLUGIN_VERSION'     => self::PLUGIN_VERSION,
				'PLUGIN_SLUG'        => self::PLUGIN_SLUG,
			);

			if (MchGdbcWpUtils::isAjaxRequest()) {
				GdbcAjaxController::processRequest();
			} elseif (MchGdbcWpUtils::isUserInDashboard()) {
				GoodByeCaptchaAdmin::getInstance($arrPluginInfo);
			} else {
				GoodByeCaptchaPublic::getInstance($arrPluginInfo);
			}

		}

		public static function classAutoLoad($className)
		{
			if (!isset(self::$arrClassMap[$className]))
				return null;

			$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . trim(self::$arrClassMap[$className], '/\\');

			return file_exists($filePath) ? include $filePath : null;
		}

		public static function isNetworkActivated()
		{
			static $isNetworkActivated = null;

			return null !== $isNetworkActivated ? $isNetworkActivated : $isNetworkActivated = MchGdbcWpUtils::isPluginNetworkActivated(self::getMainFilePath());
		}

		public static function isProVersion()
		{
			return class_exists('WPBruiserPro', false);
		}

		public static function getMainFilePath()
		{
			static $mainFilePath = null;

			return null !== $mainFilePath ? $mainFilePath : ($mainFilePath = self::isProVersion() ? dirname(__FILE__) . DIRECTORY_SEPARATOR . '__wpbruiser.php' : __FILE__);
		}

		public static function activate()
		{
			GoodByeCaptchaAdmin::onPluginActivate();
		}

		public static function deactivate($isForNetwork)
		{}

	}

	require_once dirname(__FILE__) . '/includes/MchGdbcLibAutoloader.php';

	spl_autoload_register(array('GoodByeCaptcha', 'classAutoLoad'), false);


	if (defined('ABSPATH'))
	{

		GdbcIPUtils::getClientIpAddress();

		if (!empty($_GET['gdbc-client']) && file_exists($filePath = dirname(__FILE__) . '/assets/public/scripts/gdbc-client-new.js.php')) {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
			(!defined('LOGGED_IN_COOKIE') && function_exists('wp_cookie_constants')) ? wp_cookie_constants() : null;
			require $filePath;
			exit;
		}

		if (MchGdbcWpUtils::isAjaxRequest()) {
			GdbcAjaxController::processRequest();
		}

		GdbcBruteGuardian::startGuarding();

		register_activation_hook(__FILE__, array('GoodByeCaptcha', 'activate'));

		register_deactivation_hook(__FILE__, array('GoodByeCaptcha', 'deactivate'));

		add_action('plugins_loaded', array('GoodByeCaptcha', 'startRunning'), 0);
	}
}