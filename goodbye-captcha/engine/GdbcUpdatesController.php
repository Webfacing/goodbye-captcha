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

final class GdbcUpdatesController
{
	public static function updateToCurrentVersion()
	{

		if(null === ($settingsModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::MODULE_SETTINGS)))
			return;

		$savedPluginVersion = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_PLUGIN_VERSION);

		if(null === $savedPluginVersion) // fresh install. Save default options
		{
			foreach(array_keys(GdbcModulesController::getRegisteredModules()) as $moduleName)
			{
				if(null === ($adminModuleInstance = GdbcModulesController::getAdminModuleInstance($moduleName)))
					continue;

				foreach($adminModuleInstance->getDefaultOptionsValues() as $optionName => $optionValue)
				{
					if( ! is_scalar($optionValue) )
						continue;

					$adminModuleInstance->saveOption($optionName, $optionValue);
				}
			}
		}


		if(MchGdbcWpUtils::isUserInDashboard() && self::isUpdateFromV1())
		{
			$arrActiveSites = MchGdbcWpUtils::isMultiSite() ? MchGdbcWpUtils::getAllBlogIds() : array(get_current_blog_id());

			if(GdbcDbAccessController::attemptsTableExists()) {
				foreach ( $arrActiveSites as $blogId ) {
					self::migrateTableDataFromV1( $blogId );
				}
			}
		}

		if( 0 === version_compare($savedPluginVersion, GoodByeCaptcha::PLUGIN_VERSION) )
			return;

		$arrActiveSites = MchGdbcWpUtils::isMultiSite() ? MchGdbcWpUtils::getAllBlogIds() : array(get_current_blog_id());

		if( -1 === version_compare($savedPluginVersion, '2.0') )
		{
			GdbcTaskScheduler::unScheduleGdbcTasks();
			//delete_site_option('gdbc-blocked-attempts');

			self::updateToVersion_2_0();

			if(GdbcDbAccessController::attemptsTableExists()) {
				foreach ( $arrActiveSites as $blogId ) {
					self::migrateTableDataFromV1( $blogId );
				}
			}

		}

		if( -1 === version_compare($savedPluginVersion, '2.0.1') )
		{
			if(GdbcDbAccessController::attemptsTableExists()) {
				foreach ( $arrActiveSites as $blogId ) {
					self::migrateTableDataFromV1( $blogId );
				}
			}
		}

		if( -1 === version_compare($savedPluginVersion, '3.0.1') )
		{
			if(null !== ($wordPressModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::MODULE_WORDPRESS)))
			{
				$arrDefaultOptionsValues = $wordPressModuleInstance->getDefaultOptionsValues();

				if(!empty($arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]))
					$wordPressModuleInstance->saveOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH, $arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]);

				if(!empty($arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]))
					$wordPressModuleInstance->saveOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH, $arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]);

				if(!empty($arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]))
					$wordPressModuleInstance->saveOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_NAME_LENGTH, $arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]);

				if(!empty($arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]))
					$wordPressModuleInstance->saveOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH, $arrDefaultOptionsValues[GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]);
			}
		}


		if( -1 === version_compare($savedPluginVersion, '3.0.5') )
		{
			$settingsModuleInstance->deleteOption(GdbcSettingsAdminModule::OPTION_CACHE_DIR_PATH, GoodByeCaptcha::isNetworkActivated());
		}

		if( -1 === version_compare($savedPluginVersion, '3.0.10') )
		{
			if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_EMAIL_NOTIFICATIONS)){
				GdbcEmailNotificationsAdminModule::getInstance()->saveOption(GdbcEmailNotificationsAdminModule::OPTION_ADMIN_LOGGED_IN_DETECTED, true);
			}
		}

		$settingsModuleInstance->saveOption(GdbcSettingsAdminModule::OPTION_PLUGIN_VERSION, GoodByeCaptcha::PLUGIN_VERSION);

		GoodByeCaptchaUtils::flushSiteCache();

		if(GoodByeCaptcha::isNetworkActivated()){
			foreach($arrActiveSites as $blogId){
				$blogId != get_current_blog_id() ? GoodByeCaptchaUtils::flushSiteCache($blogId) : null;
			}
		}

	}

	private static function updateToVersion_2_0()
	{
		global $wpdb;

		$suppressOldValue = $wpdb->suppress_errors(true);
		$wpdb->hide_errors();

		GdbcDbAccessController::createAttemptsTable();

		if(self::isUpdateFromV1())
		{
			$arrBlogs = MchGdbcWpUtils::isMultiSite() ? MchGdbcWpUtils::getAllBlogIds() : array( get_current_blog_id() );

			foreach ( $arrBlogs as $blogId )
			{
				$blogTablePrefix = $wpdb->get_blog_prefix( $blogId );

				$gdbcTableName = $blogTablePrefix . 'gdbc_attempts';

				if ( $wpdb->get_var( "SHOW TABLES LIKE '$gdbcTableName'" ) !== $gdbcTableName ) {
					continue;
				}

				$arrQueryResult = $wpdb->get_results( "SELECT * FROM $gdbcTableName LIMIT 1" );

				if ( isset( $arrQueryResult[0]->IsDeleted ) ) {
					$wpdb->query( "RENAME TABLE $gdbcTableName TO {$gdbcTableName}_old" );
				}
				elseif( empty( $arrQueryResult ) )
				{
					$wpdb->query("DROP TABLE IF EXISTS $gdbcTableName");
				}

			}
		}

		GdbcDbAccessController::createAttemptsTable();

		$wpdb->suppress_errors($suppressOldValue);

	}

	private static function migrateTableDataFromV1($blogId)
	{

		if(!self::isUpdateFromV1())
			return;

		global $wpdb;
		$blogTablePrefix = $wpdb->get_blog_prefix($blogId);

		$gdbcOldTableName = $blogTablePrefix . 'gdbc_attempts_old' ;

		if($wpdb->get_var("SHOW TABLES LIKE '$gdbcOldTableName'") !== $gdbcOldTableName)
			return;

		$alreadyBlockedIpsList = (array)$wpdb->get_results("SELECT DISTINCT ClientIp FROM $gdbcOldTableName WHERE IsIpBlocked <> 0 AND IsDeleted = 0");

		foreach($alreadyBlockedIpsList as $ipAddressObject)
		{
			$clientIp = MchGdbcIPUtils::ipAddressFromBinary($ipAddressObject->ClientIp);
			if(!MchGdbcIPUtils::isValidIpAddress($clientIp))
				continue;

			if(GdbcIPUtils::isIpBlackListed($clientIp))
				continue;

			GdbcBlackListedIpsAdminModule::getInstance()->registerBlackListedIp($clientIp);

		}

		unset($alreadyBlockedIpsList, $ipAddressObject, $clientIp);


		$arrModulesIdMapping = array( // key - oldModuleId
			1  => GdbcModulesController::MODULE_WORDPRESS             ,
			2  => GdbcModulesController::MODULE_JETPACK_CONTACT_FORM  ,
			3  => GdbcModulesController::MODULE_BUDDY_PRESS           ,
			4  => GdbcModulesController::MODULE_NINJA_FORMS           ,
			5  => GdbcModulesController::MODULE_CONTACT_FORM_7        ,
			6  => GdbcModulesController::MODULE_GRAVITY_FORMS         ,
			7  => GdbcModulesController::MODULE_FAST_SECURE_FORM      ,
			8  => GdbcModulesController::MODULE_FORMIDABLE_FORMS      ,
			9  => GdbcModulesController::MODULE_MAIL_CHIMP_FOR_WP     ,
			11 => GdbcModulesController::MODULE_USER_PRO             ,
			12 => GdbcModulesController::MODULE_ULTIMATE_MEMBER       ,
			13 => GdbcModulesController::MODULE_WOOCOMMERCE           ,
			14 => GdbcModulesController::MODULE_UPME                 ,
			15 => GdbcModulesController::MODULE_PLANSO_FORMS          ,
			16 => GdbcModulesController::MODULE_SEAMLESS_DONATIONS    ,
		);



		$minDateTime = date('Y-m-d H:i:s',  strtotime(((-1) * (30)) . ' days', current_time( 'timestamp' )));
		$maxDateTime = date('Y-m-d H:i:s',  current_time( 'timestamp' ));

		$gdbcAttemptsQuery = "
					SELECT Id, UNIX_TIMESTAMP(CreatedDate) AS CreatedDate, ModuleId, SectionId, ClientIp, CountryId, ReasonId
					FROM $gdbcOldTableName WHERE IsDeleted = 0 AND CreatedDate BETWEEN '$minDateTime' AND '$maxDateTime' order by CreatedDate DESC LIMIT 500;
		";


		$gdbcAttemptsList = (array)$wpdb->get_results($gdbcAttemptsQuery);

		$oldSettingsOptions = get_site_option('gdbcsettingsadminmodule-settings');
		if(!empty($oldSettingsOptions['TrustedIps'][0]) && MchGdbcIPUtils::isValidIpAddress($oldSettingsOptions['TrustedIps'][0]))
		{
			if(!GdbcIPUtils::isIpWhiteListed($oldSettingsOptions['TrustedIps'][0])){
				GdbcWhiteListedIpsAdminModule::getInstance()->registerWhiteListedIp($oldSettingsOptions['TrustedIps'][0]);
			}
		}

		$oldSettingsOptions = get_site_option('gdbcwordpressadminmodule-settings');
		if(!empty($oldSettingsOptions['IsCommentsFormActivated']))
		{
			GdbcWordPressAdminModule::getInstance()->saveOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM, true);
		}

		if(!empty($oldSettingsOptions['IsLoginFormActivated']))
		{
			GdbcWordPressAdminModule::getInstance()->saveOption(GdbcWordPressAdminModule::WORDPRESS_LOGIN_FORM, true);
		}
		if(!empty($oldSettingsOptions['IsLostPasswordFormActivated']))
		{
			GdbcWordPressAdminModule::getInstance()->saveOption(GdbcWordPressAdminModule::WORDPRESS_LOST_PASSWORD_FORM, true);
		}
		if(!empty($oldSettingsOptions['IsUserRegistrationFormActivated']))
		{
			GdbcWordPressAdminModule::getInstance()->saveOption(GdbcWordPressAdminModule::WORDPRESS_REGISTRATION_FORM, true);
		}

		$oldSettingsOptions = get_site_option('gdbcultimatememberadminmodule-settings');
		if(!empty($oldSettingsOptions['IsUMLoginActivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ULTIMATE_MEMBER))
		{
			GdbcUltimateMemberAdminModule::getInstance()->saveOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOGIN_FORM, true);
		}
		if(!empty($oldSettingsOptions['IsUMRegisterActivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ULTIMATE_MEMBER))
		{
			GdbcUltimateMemberAdminModule::getInstance()->saveOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_REGISTER_FORM, true);
		}
		if(!empty($oldSettingsOptions['IsUMLostPasswordActivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ULTIMATE_MEMBER))
		{
			GdbcUltimateMemberAdminModule::getInstance()->saveOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOST_PASSWORD_FORM, true);
		}

		$oldSettingsOptions = get_site_option('gdbcsubscriptionsadminmodule-settings');
		if(!empty($oldSettingsOptions['IsMCLActivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_MAIL_CHIMP_FOR_WP))
		{
			GdbcMailChimpForWpAdminModule::getInstance()->saveOption(GdbcMailChimpForWpAdminModule::OPTION_MODULE_MAIL_CHIMP_FOR_WP, true);
		}

		$oldSettingsOptions = get_site_option('gdbcpopularformsadminmodule-settings');
		if(!empty($oldSettingsOptions['IsJCFctivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_JETPACK_CONTACT_FORM))
		{
			GdbcJetPackContactFormAdminModule::getInstance()->saveOption(GdbcJetPackContactFormAdminModule::OPTION_IS_JETPACK_CONTACT_FORM_ACTIVATE, true);
		}
		if(!empty($oldSettingsOptions['IsPFActivated']) && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_PLANSO_FORMS))
		{
			GdbcPlanSoFormsAdminModule::getInstance()->saveOption(GdbcPlanSoFormsAdminModule::OPTION_PLANSO_GENERAL_FORM, true);
		}

		delete_site_option('gdbcsettingsadminmodule-settings');
		delete_site_option('gdbcwordpressadminmodule-settings');
		delete_site_option('gdbcultimatememberadminmodule-settings');
		delete_site_option('gdbcsubscriptionsadminmodule-settings');
		delete_site_option('gdbcpopularformsadminmodule-settings');

		if(empty($gdbcAttemptsList)) {

			foreach(GdbcModulesController::getRegisteredModules() as $moduleName => $arrModuleInfo)
			{
				if(null === ($adminModuleInstance = GdbcModulesController::getAdminModuleInstance($moduleName)))
					continue;

				$oldOptionKey = strtolower(get_class($adminModuleInstance)) . '-settings';

				MchGdbcWpUtils::isMultiSite() && function_exists('delete_blog_option') ? delete_blog_option($blogId, $oldOptionKey) : delete_option($oldOptionKey);
			}


			$wpdb->query("DROP TABLE IF EXISTS $gdbcOldTableName");
			return true;
		}

		$arrSelectedIds = array();
		foreach($gdbcAttemptsList as $gdbcAttempt)
		{

			empty($gdbcAttempt->SectionId) ? $gdbcAttempt->SectionId = 0 : null;

			$newModuleName = isset($arrModulesIdMapping[$gdbcAttempt->ModuleId]) ? $arrModulesIdMapping[$gdbcAttempt->ModuleId] : null;
			$newModuleId = GdbcModulesController::getModuleIdByName($newModuleName);

			if(empty($newModuleId))
				continue;

			$attemptEntity = new GdbcAttemptEntity($newModuleId);

			$attemptEntity->ModuleId    = $newModuleId;
			$attemptEntity->SectionId   = !empty($gdbcAttempt->SectionId) ? $gdbcAttempt->SectionId : 0;
			$attemptEntity->SiteId      = $blogId;
			$attemptEntity->CreatedDate = $gdbcAttempt->CreatedDate;
			$attemptEntity->ReasonId    = $gdbcAttempt->ReasonId;
			$attemptEntity->ClientIp    = MchGdbcIPUtils::ipAddressFromBinary($gdbcAttempt->ClientIp);


			if(0 !== GdbcDbAccessController::registerAttempt($attemptEntity)) {
				$arrSelectedIds[] = $gdbcAttempt->Id;
			}

		}

		$wpdb->query("DELETE FROM $gdbcOldTableName WHERE Id IN (" . implode(',', $arrSelectedIds) . ")");

		return true;
	}


	private static function isUpdateFromV1()
	{
		return false !== get_site_option('gdbc-blocked-attempts');
	}


	private function __construct()
	{}
}