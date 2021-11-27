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

final class GdbcAjaxController
{
	CONST ACTION_RETRIEVE_TOKEN  = 'gdbcRetrieveToken';
	CONST AJAX_NONCE_VALUE       = __CLASS__;

	public static function processRequest()
	{
		if( ! GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_SETTINGS) )
			return;

		if(self::isPublicGdbcAjaxRequest() ) {

			self::sendAjaxHeaders();

			if ( ! self::clientCanRetrieveToken() ) {
				wp_send_json_error();
			}

			$arrTokenData = GdbcRequestController::getEncryptedToken();

			wp_send_json_success( $arrTokenData );

			exit;
		}

		if( ! did_action ('plugins_loaded') )
			return;


		foreach(array(

			        GdbcModulesController::MODULE_MAIL_CHIMP_FOR_WP,
					GdbcModulesController::MODULE_MAIL_POET,
					GdbcModulesController::MODULE_ZM_ALR,
			        GdbcModulesController::MODULE_NINJA_FORMS,
			        GdbcModulesController::MODULE_USER_PRO,
					GdbcModulesController::MODULE_EASY_FORMS_FOR_MAILCHIMP,
					GdbcModulesController::MODULE_ULTIMATE_MEMBER,
			        GdbcModulesController::MODULE_WOOCOMMERCE,
			        GdbcModulesController::MODULE_AFFILIATE_WP,
			        GdbcModulesController::MODULE_EASY_DIGITAL_DOWNLOADS,
			        GdbcModulesController::MODULE_QUFORM,
					GdbcModulesController::MODULE_HTML_FORMS,
					GdbcModulesController::MODULE_WP_FORMS,
					GdbcModulesController::MODULE_ULTRA_COMMUNITY,

		        ) as $moduleName){

			if(null === ($publicModuleInstance = GdbcModulesController::getPublicModuleInstance($moduleName)))
				continue;

			$publicModuleInstance->registerAttachedHooks();
		}


		if(self::isWpDiscuzPostCommentAjaxRequest()) // Support for wpDiscuz Plugin
		{
			GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_WORDPRESS)->registerAttachedHooks();
		}

		if(defined('LOGIN_WITH_AJAX_VERSION'))// Support for Login With Ajax Plugin
		{
			GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_WORDPRESS)->registerAttachedHooks();
		}

		if(self::isAdminAjaxRequestValid())
		{
			$arrAjaxAdminReportsActions = array(
				'retrieveInitialDashboardData',
				'retrieveLatestAttemptsTable',
				'retrieveTotalAttemptsPerModule',
				'retrieveDetailedAttemptsForChart',
				'retrieveDetailedAttemptsPerModule',
				'retrieveFormattedBlockedContent',
				'retrieveAttemptsPerModuleAndSection',
				'retrieveAttemptsPerClientIp',
				'manageClientIpAddress',
			);

			foreach ($arrAjaxAdminReportsActions as $adminAjaxActionRequest) {
				add_action('wp_ajax_' . $adminAjaxActionRequest, array(GdbcReportsAdminModule::getInstance(), $adminAjaxActionRequest));
			}

			foreach(GoodByeCaptchaAdmin::getAdminRegisteredNotices() as $adminNotice){
				if( !($adminNotice instanceof GdbcAdminNotice) || !$adminNotice->isDismissible())
					continue;

				add_action('wp_ajax_gdbc-dismiss-' . $adminNotice->getFormattedNoticeKey(), array($adminNotice, 'dismiss'));
			}

			add_action('wp_ajax_gdbc-user-subscribed-newsletter', array(__CLASS__, 'userSubscribedToNewsLetter'));

		}


		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_LICENSES) && isset($_REQUEST['action']) && $_REQUEST['action'] === 'update-plugin')
		{
			GdbcLicensesAdminModule::getInstance()->registerAttachedHooks();
		}

	}

	public static function userSubscribedToNewsLetter()
	{
		GdbcSettingsAdminModule::getInstance()->saveOption(GdbcSettingsAdminModule::OPTION_HIDE_SUBSCRIBE_FORM, true);
	}

	private static function clientCanRetrieveToken()
	{

		$settingsModuleInstance = GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_SETTINGS);
		if(null === $settingsModuleInstance)
			return false;

		if(null === ($hiddenInputName = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME)))
			return false;

		if(empty($_POST[$hiddenInputName]))
			return false;


		if(!isset($_SERVER['HTTP_ACCEPT']) || false === stripos($_SERVER['HTTP_ACCEPT'], 'json'))
			return false;

		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (0 !== strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest')))
			return false;

		require_once( ABSPATH . WPINC . '/pluggable.php' );

		( !defined('LOGGED_IN_COOKIE') && function_exists('wp_cookie_constants') ) ? wp_cookie_constants() : null;

		if(false === wp_verify_nonce($_POST[$hiddenInputName], __CLASS__))
			return false;

		if(GdbcIPUtils::isClientIpBlackListed())
			return false;

		return true;

	}


	public static function isWpDiscuzPostCommentAjaxRequest()
	{
		return !empty($_POST['wpdiscuz_unique_id']) && !empty($_POST['postId']) && MchGdbcWpUtils::isAjaxRequest() && GoodByeCaptchaUtils::isWPDiscuzPluginActivated();
	}

	private static function sendAjaxHeaders()
	{
		wp_magic_quotes();
		send_origin_headers();

		@header('X-Robots-Tag: noindex' );

		send_nosniff_header();
		nocache_headers();

		@header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		@header('Content-Type: application/json; charset=' . get_option( 'blog_charset' ));
	}

	private static function isPublicGdbcAjaxRequest()
	{
		if(empty($_POST['browserInfo']) || empty($_POST['action']) || (self::ACTION_RETRIEVE_TOKEN !== $_POST['action']))
			return false;

		return true;
	}

	public static function getAjaxNonce()
	{
		require_once( ABSPATH . WPINC . '/pluggable.php' );

		return wp_create_nonce(__CLASS__);
	}

	public static function isAdminAjaxRequestValid()
	{
		if(!isset($_POST['ajaxRequestNonce']))
			return false;

		if(!MchGdbcWpUtils::isAdminLoggedIn())
			return false;


		return check_ajax_referer(self::AJAX_NONCE_VALUE, 'ajaxRequestNonce', false);
	}
}