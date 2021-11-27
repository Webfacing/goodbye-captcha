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

spl_autoload_register(array('GdbcModulesController','autoLoadModulesClasses'));

final class GdbcModulesController
{
	//CONST MODULE_CLASS_PREFIX     = 'GoodByeCaptcha';

	CONST MODULE_SETTINGS         = 'Settings';
	CONST MODULE_WORDPRESS        = 'WordPress';
	CONST MODULE_BRUTE_FORCE      = 'BruteForce';

	CONST MODULE_REPORTS		  = 'Reports';

	CONST MODULE_WOOCOMMERCE      = 'WooCommerce';

	CONST MODULE_MAIL_CHIMP_FOR_WP = 'MailChimpForWp';
	CONST MODULE_MAIL_POET         = 'MailPoet';

	CONST MODULE_NINJA_FORMS      = 'NinjaForms';
	CONST MODULE_CONTACT_FORM_7   = 'ContactForm7';
	CONST MODULE_GRAVITY_FORMS    = 'GravityForms';
	CONST MODULE_HTML_FORMS       = 'HTMLForms';
	CONST MODULE_FAST_SECURE_FORM = 'FastSecureForm';
	CONST MODULE_FORMIDABLE_FORMS = 'FormidableForms';
	CONST MODULE_JETPACK_CONTACT_FORM  = 'JetPackContactForm';


	CONST MODULE_ULTIMATE_MEMBER  = 'UltimateMember';
	CONST MODULE_ULTRA_COMMUNITY  = 'UltraCommunity';
	CONST MODULE_WP_MEMBERS       = 'WPMembers';

	CONST MODULE_USER_PRO         = 'UserPro';
	CONST MODULE_UPME             = 'UPME';
	CONST MODULE_BUDDY_PRESS      = 'BuddyPress';
	CONST MODULE_BB_PRESS         = 'bbPress';

	CONST MODULE_BLACK_LISTED_IPS = 'BlackListedIps';
	CONST MODULE_WHITE_LISTED_IPS = 'WhiteListedIps';

	CONST MODULE_ZM_ALR               = 'ZM-ALR';
	CONST MODULE_QUFORM               = 'Quform';
	
	CONST MODULE_WP_FORMS             = 'WPForms';
	
	CONST MODULE_SEAMLESS_DONATIONS   = 'SeamlessDonations';
	CONST MODULE_WORDPRESS_TWEAKS     = 'WordPressTweaks';

	CONST MODULE_PLANSO_FORMS         = 'PlanSoForms';
	CONST MODULE_EMAIL_NOTIFICATIONS  = 'EmailNotifications';
	CONST MODULE_LICENSES             = 'Licenses';
	CONST MODULE_MEMBER_PRESS         = 'MemberPress';

	CONST MODULE_PROXY_HEADERS          = 'ProxyHeaders';
	CONST MODULE_COUNTRY_BLOCKING       = 'CountryBlocking';
	CONST MODULE_EASY_DIGITAL_DOWNLOADS = 'EDD';
	CONST MODULE_AFFILIATE_WP           = 'AffiliateWP';

	CONST MODULE_EASY_FORMS_FOR_MAILCHIMP = 'EasyFormsForMailChimp';

	private static $arrRegisteredModules = null;
	private static $arrAllModules = array(

			self::MODULE_SETTINGS => array(
					'info'    => array(
							'ModuleId'   => 1,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcSettingsAdminModule'  => '/modules/settings/GdbcSettingsAdminModule.php',
							'GdbcSettingsPublicModule' => '/modules/settings/GdbcSettingsPublicModule.php',
					),
			),

			self::MODULE_WORDPRESS => array(
					'info'    => array(
							'ModuleId' => 2,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcWordPressAdminModule'  => '/modules/wordpress/GdbcWordPressAdminModule.php',
							'GdbcWordPressPublicModule' => '/modules/wordpress/GdbcWordPressPublicModule.php',
					)
			),

			self::MODULE_JETPACK_CONTACT_FORM => array(
					'info'    => array(
							'ModuleId' => 3,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcJetPackContactFormAdminModule'  => '/modules/jetpack-contact-form/GdbcJetPackContactFormAdminModule.php',
							'GdbcJetPackContactFormPublicModule' => '/modules/jetpack-contact-form/GdbcJetPackContactFormPublicModule.php',
					)
			),

			self::MODULE_NINJA_FORMS => array(
					'info'    => array(
							'ModuleId' => 4,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcNinjaFormsAdminModule'  => '/modules/ninja-forms/GdbcNinjaFormsAdminModule.php',
							'GdbcNinjaFormsPublicModule' => '/modules/ninja-forms/GdbcNinjaFormsPublicModule.php',
					)
			),

			self::MODULE_CONTACT_FORM_7 => array(
					'info'    => array(
							'ModuleId' => 5,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcContactForm7AdminModule'  => '/modules/contact-form-7/GdbcContactForm7AdminModule.php',
							'GdbcContactForm7PublicModule' => '/modules/contact-form-7/GdbcContactForm7PublicModule.php',
					)
			),

			self::MODULE_GRAVITY_FORMS => array(
					'info'    => array(
							'ModuleId' => 6,
							'IsLicensed' => true,
					),
					'classes' => array(
						'GdbcGravityFormsAdminModule'  => '/modules/gravity-forms/GdbcGravityFormsAdminModule.php',
						'GdbcGravityFormsPublicModule' => '/modules/gravity-forms/GdbcGravityFormsPublicModule.php',
					)
			),

			self::MODULE_FAST_SECURE_FORM => array(
					'info'    => array(
							'ModuleId' => 7,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcFastSecureFormAdminModule'  => '/modules/fast-secure-form/GdbcFastSecureFormAdminModule.php',
							'GdbcFastSecureFormPublicModule' => '/modules/fast-secure-form/GdbcFastSecureFormPublicModule.php',
					)
			),

			self::MODULE_FORMIDABLE_FORMS => array(
					'info'    => array(
							'ModuleId' => 8,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcFormidableFormsAdminModule'  => '/modules/formidable-forms/GdbcFormidableFormsAdminModule.php',
							'GdbcFormidableFormsPublicModule' => '/modules/formidable-forms/GdbcFormidableFormsPublicModule.php',
					)
			),

			self::MODULE_ULTIMATE_MEMBER => array(
					'info'    => array(
							'ModuleId' => 9,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcUltimateMemberAdminModule'  => '/modules/ultimate-member/GdbcUltimateMemberAdminModule.php',
							'GdbcUltimateMemberPublicModule' => '/modules/ultimate-member/GdbcUltimateMemberPublicModule.php',
					)
			),

			self::MODULE_BUDDY_PRESS => array(
					'info'    => array(
							'ModuleId' => 10,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcBuddyPressAdminModule'  => '/modules/buddy-press/GdbcBuddyPressAdminModule.php',
							'GdbcBuddyPressPublicModule' => '/modules/buddy-press/GdbcBuddyPressPublicModule.php',
					)
			),

			self::MODULE_BB_PRESS => array(
					'info'    => array(
							'ModuleId' => 11,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcBbPressAdminModule'  => '/modules/bb-press/GdbcBbPressAdminModule.php',
							'GdbcBbPressPublicModule' => '/modules/bb-press/GdbcBbPressPublicModule.php',
					)
			),

			self::MODULE_USER_PRO => array(
					'info'    => array(
							'ModuleId' => 12,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcUserProAdminModule'  => '/modules/user-pro/GdbcUserProAdminModule.php',
							'GdbcUserProPublicModule' => '/modules/user-pro/GdbcUserProPublicModule.php',
					)
			),

			self::MODULE_UPME => array(
					'info'    => array(
							'ModuleId' => 13,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcUPMEAdminModule'  => '/modules/upme/GdbcUPMEAdminModule.php',
							'GdbcUPMEPublicModule' => '/modules/upme/GdbcUPMEPublicModule.php',
					)
			),

			self::MODULE_MAIL_CHIMP_FOR_WP => array(
					'info'    => array(
							'ModuleId' => 14,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcMailChimpForWpAdminModule'  => '/modules/mc-for-wp/GdbcMailChimpForWpAdminModule.php',
							'GdbcMailChimpForWpPublicModule' => '/modules/mc-for-wp/GdbcMailChimpForWpPublicModule.php',
					)
			),

			self::MODULE_MAIL_POET => array(
					'info'    => array(
							'ModuleId' => 15,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcMailPoetAdminModule'  => '/modules/mail-poet/GdbcMailPoetAdminModule.php',
							'GdbcMailPoetPublicModule' => '/modules/mail-poet/GdbcMailPoetPublicModule.php',
					)
			),

			self::MODULE_WOOCOMMERCE => array(
					'info'    => array(
							'ModuleId' => 16,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcWooCommerceAdminModule'  => '/modules/woocommerce/GdbcWooCommerceAdminModule.php',
							'GdbcWooCommercePublicModule' => '/modules/woocommerce/GdbcWooCommercePublicModule.php',
					)
			),


			self::MODULE_REPORTS => array(
					'info'    => array(
							'ModuleId'      => 17,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcReportsAdminModule'  => '/modules/reports/GdbcReportsAdminModule.php',
							'GdbcReportsPublicModule' => '/modules/reports/GdbcReportsPublicModule.php',
					),
			),

			self::MODULE_BRUTE_FORCE => array(
					'info'    => array(
							'ModuleId'      => 18,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcBruteForceAdminModule'  => '/modules/brute-force/GdbcBruteForceAdminModule.php',
							'GdbcBruteForcePublicModule' => '/modules/brute-force/GdbcBruteForcePublicModule.php',
					),
			),

			self::MODULE_BLACK_LISTED_IPS => array(
					'info'    => array(
							'ModuleId'      => 19,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcBlackListedIpsAdminModule'  => '/modules/black-listed-ips/GdbcBlackListedIpsAdminModule.php',
							'GdbcBlackListedIpsPublicModule' => '/modules/black-listed-ips/GdbcBlackListedIpsPublicModule.php',
					),
			),

			self::MODULE_WHITE_LISTED_IPS => array(
					'info'    => array(
							'ModuleId'      => 20,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcWhiteListedIpsAdminModule'  => '/modules/white-listed-ips/GdbcWhiteListedIpsAdminModule.php',
							'GdbcWhiteListedIpsPublicModule' => '/modules/white-listed-ips/GdbcWhiteListedIpsPublicModule.php',
					),
			),

			self::MODULE_ZM_ALR => array(
					'info'    => array(
							'ModuleId'      => 21,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcZmAlrAdminModule'  => '/modules/zm-ajax-login-register/GdbcZmAlrAdminModule.php',
							'GdbcZmAlrPublicModule' => '/modules/zm-ajax-login-register/GdbcZmAlrPublicModule.php',
					),
			),

			self::MODULE_SEAMLESS_DONATIONS => array(
					'info'    => array(
							'ModuleId'      => 22,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcSeamlessDonationsAdminModule'  => '/modules/seamless-donations/GdbcSeamlessDonationsAdminModule.php',
							'GdbcSeamlessDonationsPublicModule' => '/modules/seamless-donations/GdbcSeamlessDonationsPublicModule.php',
					),
			),

			self::MODULE_WORDPRESS_TWEAKS => array(
					'info'    => array(
							'ModuleId'      => 23,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcWordPressTweaksAdminModule'  => '/modules/wordpress-tweaks/GdbcWordPressTweaksAdminModule.php',
							'GdbcWordPressTweaksPublicModule' => '/modules/wordpress-tweaks/GdbcWordPressTweaksPublicModule.php',
					),
			),

			self::MODULE_EMAIL_NOTIFICATIONS => array(
					'info'    => array(
							'ModuleId' => 24,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcEmailNotificationsAdminModule'  => '/modules/email-notifications/GdbcEmailNotificationsAdminModule.php',
							'GdbcEmailNotificationsPublicModule' => '/modules/email-notifications/GdbcEmailNotificationsPublicModule.php',
					)
			),

			self::MODULE_PLANSO_FORMS => array(
					'info'    => array(
							'ModuleId' => 25,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcPlanSoFormsAdminModule'  => '/modules/planso-forms/GdbcPlanSoFormsAdminModule.php',
							'GdbcPlanSoFormsPublicModule' => '/modules/planso-forms/GdbcPlanSoFormsPublicModule.php',
					)
			),

			self::MODULE_LICENSES => array(
					'info'    => array(
							'ModuleId' => 26,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcLicensesAdminModule'  => '/modules/licenses/GdbcLicensesAdminModule.php',
							'GdbcLicensesPublicModule' => '/modules/licenses/GdbcLicensesPublicModule.php',
					)
			),

			self::MODULE_MEMBER_PRESS => array(
					'info'    => array(
							'ModuleId' => 27,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcMemberPressAdminModule'  => '/modules/member-press/GdbcMemberPressAdminModule.php',
							'GdbcMemberPressPublicModule' => '/modules/member-press/GdbcMemberPressPublicModule.php',
					)
			),

			self::MODULE_EASY_FORMS_FOR_MAILCHIMP => array(
					'info'    => array(
							'ModuleId' => 28,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcEasyFormsForMailChimpAdminModule'  => '/modules/easy-forms-for-mailchimp/GdbcEasyFormsForMailChimpAdminModule.php',
							'GdbcEasyFormsForMailChimpPublicModule' => '/modules/easy-forms-for-mailchimp/GdbcEasyFormsForMailChimpPublicModule.php',
					)
			),

			self::MODULE_COUNTRY_BLOCKING => array(
					'info'    => array(
							'ModuleId' => 29,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcGeoIpCountryAdminModule'  => '/modules/geo-ip-country/GdbcGeoIpCountryAdminModule.php',
							'GdbcGeoIpCountryPublicModule' => '/modules/geo-ip-country/GdbcGeoIpCountryPublicModule.php',
					)
			),

			self::MODULE_EASY_DIGITAL_DOWNLOADS => array(
				'info'    => array(
					'ModuleId' => 30,
					'IsLicensed' => true,
				),
				'classes' => array(
					'GdbcEDDAdminModule'  => '/modules/easy-digital-downloads/GdbcEDDAdminModule.php',
					'GdbcEDDPublicModule' => '/modules/easy-digital-downloads/GdbcEDDPublicModule.php',
				)
			),

			self::MODULE_AFFILIATE_WP => array(
				'info'    => array(
					'ModuleId' => 31,
					'IsLicensed' => true,
				),
				'classes' => array(
					'GdbcAffiliateWPAdminModule'  => '/modules/affiliate-wp/GdbcAffiliateWPAdminModule.php',
					'GdbcAffiliateWPPublicModule' => '/modules/affiliate-wp/GdbcAffiliateWPPublicModule.php',
				)
			),

			self::MODULE_QUFORM => array(
					'info'    => array(
							'ModuleId' => 32,
							'IsLicensed' => true,
					),
					'classes' => array(
							'GdbcQuformAdminModule'  => '/modules/quform/GdbcQuformAdminModule.php',
							'GdbcQuformPublicModule' => '/modules/quform/GdbcQuformPublicModule.php',
					)
			),

			self::MODULE_PROXY_HEADERS => array(
					'info'    => array(
							'ModuleId' => 33,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcProxyHeadersAdminModule'  => '/modules/proxy-headers/GdbcProxyHeadersAdminModule.php',
							'GdbcProxyHeadersPublicModule' => '/modules/proxy-headers/GdbcProxyHeadersPublicModule.php',
					)
			),

			self::MODULE_WP_MEMBERS => array(
					'info'    => array(
							'ModuleId' => 34,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcWPMembersAdminModule'  => '/modules/wp-members/GdbcWPMembersAdminModule.php',
							'GdbcWPMembersPublicModule' => '/modules/wp-members/GdbcWPMembersPublicModule.php',
					)
			),

			self::MODULE_ULTRA_COMMUNITY => array(
					'info'    => array(
							'ModuleId' => 35,
							'IsLicensed' => false,
					),
					'classes' => array(
							'GdbcUltraCommunityAdminModule'  => '/modules/ultra-community/GdbcUltraCommunityAdminModule.php',
							'GdbcUltraCommunityPublicModule' => '/modules/ultra-community/GdbcUltraCommunityPublicModule.php',
					)
			),
			
			self::MODULE_HTML_FORMS => array(
				'info'    => array(
					'ModuleId' => 36,
					'IsLicensed' => false,
				),
				'classes' => array(
					'GdbcHtmlFormsAdminModule'  => '/modules/html-forms/GdbcHtmlFormsAdminModule.php',
					'GdbcHtmlFormsPublicModule' => '/modules/html-forms/GdbcHtmlFormsPublicModule.php',
				)
			),
			
			self::MODULE_WP_FORMS => array(
				'info'    => array(
					'ModuleId' => 37,
					'IsLicensed' => true,
				),
				'classes' => array(
					'GdbcWpFormsAdminModule'  => '/modules/wp-forms/GdbcWpFormsAdminModule.php',
					'GdbcWpFormsPublicModule' => '/modules/wp-forms/GdbcWpFormsPublicModule.php',
				)
			),
	
	);


	public static function getModuleDisplayName($moduleName, $isForLicensing = false)
	{
		switch ($moduleName)
		{
			case self::MODULE_BRUTE_FORCE              : return 'Brute Force';
			case self::MODULE_BUDDY_PRESS              : return 'BuddyPress';
			case self::MODULE_CONTACT_FORM_7           : return 'Contact Form 7';
			case self::MODULE_FAST_SECURE_FORM         : return !$isForLicensing ? 'Fast Secure Form' : 'Fast Secure Contact Form';
			case self::MODULE_FORMIDABLE_FORMS         : return 'Formidable Forms';
			case self::MODULE_GRAVITY_FORMS            : return 'Gravity Forms';
			case self::MODULE_JETPACK_CONTACT_FORM     : return 'Jetpack Contact Form';
			case self::MODULE_MAIL_CHIMP_FOR_WP        : return 'MailChimp for WP';
			case self::MODULE_MAIL_POET                : return 'MailPoet';
			case self::MODULE_NINJA_FORMS              : return 'Ninja Forms';
			case self::MODULE_PLANSO_FORMS             : return 'Planso Forms';
			case self::MODULE_SEAMLESS_DONATIONS       : return 'Seamless Donations';
			case self::MODULE_ULTIMATE_MEMBER          : return 'Ultimate Member';
			case self::MODULE_USER_PRO                 : return 'UserPro';
			case self::MODULE_EASY_FORMS_FOR_MAILCHIMP : return 'Easy Forms for MailChimp';
			case self::MODULE_MEMBER_PRESS             : return 'MemberPress';
			case self::MODULE_UPME                     : return !$isForLicensing ? 'UPME' : 'User Profiles Made Easy';
			case self::MODULE_EASY_DIGITAL_DOWNLOADS   : return 'Easy Digital Downloads';
			case self::MODULE_COUNTRY_BLOCKING         : return 'Country Blocking';

		}

		return $moduleName;
	}


	public static function getRegisteredModules()
	{
		if(null === self::$arrRegisteredModules)
			self::setRegisteredModules();

		return self::$arrRegisteredModules;
	}

	private static function setRegisteredModules()
	{
		if(null !== self::$arrRegisteredModules)
			return;

		self::$arrRegisteredModules = array();

		$activatedPlugins = defined('WP_PLUGIN_DIR') ? array_merge( array_flip((array) get_option( 'active_plugins', array())), (array) get_site_option( 'active_sitewide_plugins', array() ) ) : array(); // wp_get_mu_plugins()

		if(defined('ABSPATH')){
			unset($activatedPlugins[plugin_basename(GoodByeCaptcha::getMainFilePath())]); // un-setting the plugin itself
		}

		$engineDirPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;

		foreach(self::$arrAllModules as $moduleName => $arrModule)
		{
			self::$arrRegisteredModules[$moduleName] = array();

			foreach ($arrModule['classes'] as $className => $filePath)
			{
				$filePath = $engineDirPath . ( $dirPath = trim( dirname($filePath) , '/\\' ) . DIRECTORY_SEPARATOR . basename($filePath) );

				if(@file_exists($filePath)){
					self::$arrRegisteredModules[$moduleName][$className] = $filePath;
					continue;
				}

				foreach($activatedPlugins as $activePlugin => $value)
				{
					if( (false === strpos($activePlugin, GoodByeCaptcha::PLUGIN_SLUG)) && (false === strpos($activePlugin, "GoodByeCaptcha")) ){
						unset($activatedPlugins[$activePlugin]);continue;
					}

					if(false === strpos($activePlugin, self::getModuleStandAloneDirectoryName($moduleName)))
						continue;

					$filePath = dirname(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . MchGdbcUtils::stripLeftAndRightSlashes($activePlugin) ) . "/engine/$dirPath" ;

					break;
				}

				if(@file_exists($filePath))
				{
					self::$arrRegisteredModules[$moduleName][$className] = $filePath;
					continue;
				}

				# WPBruiser old extensions
				foreach($activatedPlugins as $activePlugin => $value)
				{
					if(false === strpos($activePlugin, "GoodByeCaptcha$moduleName"))
						continue;

					$filePath = @dirname(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $activePlugin ) . "/engine/$dirPath" ;

					break;
				}

				@file_exists($filePath) ? self::$arrRegisteredModules[$moduleName][$className] = $filePath : null;
			}

			if(empty(self::$arrRegisteredModules[$moduleName]))
				unset(self::$arrRegisteredModules[$moduleName]);

		}

	}

	public static function getModuleStandAloneDirectoryName($moduleName)
	{
		return strtolower(GoodByeCaptcha::PLUGIN_SLUG . '-' . MchGdbcUtils::stripNonAlphaNumericCharacters($moduleName));
	}

	public static function getModuleStandAloneDirectoryPath($moduleName)
	{
		if(!self::isModuleRegistered($moduleName))
			return null;

		$moduleClassName = self::getModuleStandAloneClassName($moduleName);
		if(!class_exists($moduleClassName))
		{
			if(!defined('WP_PLUGIN_DIR'))
				return null;

			return 	WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . self::getModuleStandAloneDirectoryName($moduleName);
		}

		$classReflector = new ReflectionClass($moduleClassName);

		return dirname($classReflector->getFileName());

	}

	public static function getModuleStandAloneClassName($moduleName)
	{
		return MchGdbcUtils::stripNonAlphaNumericCharacters(GoodByeCaptcha::PLUGIN_NAME . $moduleName);
	}


	public static function getModuleIdByName($moduleName)
	{
		return isset(self::$arrAllModules[$moduleName]['info']['ModuleId']) ? self::$arrAllModules[$moduleName]['info']['ModuleId'] : null;
	}

	public static function isLicensedModule($moduleIdORmoduleName)
	{
		$moduleName = ((false === filter_var($moduleIdORmoduleName, FILTER_VALIDATE_INT)) ? $moduleIdORmoduleName : self::getModuleNameById($moduleIdORmoduleName));

		return !empty(self::$arrAllModules[$moduleName]['info']['IsLicensed']);
	}

	public static function unRegisterModule($moduleName)
	{
		unset(self::$arrRegisteredModules[(string)$moduleName]);
	}

	public static function getNotLicensedModuleNames()
	{
		$arrFreeModules = array();
		foreach(self::$arrAllModules as $moduleName => $arrAllModuleSettings){
			empty(self::$arrAllModules[$moduleName]['info']['IsLicensed']) ?  $arrFreeModules[] = $moduleName : null;
		}

		return $arrFreeModules;
	}

	public static function getLicensedModuleNames()
	{
		$arrModules = array();
		foreach(self::$arrAllModules as $moduleName => $arrAllModuleSettings){
			!empty(self::$arrAllModules[$moduleName]['info']['IsLicensed']) ?  $arrModules[] = $moduleName : null;
		}

		return $arrModules;
	}

	public static function isModuleIncludedInProBundle($moduleName)
	{
		if(!self::isModuleRegistered($moduleName))
			return false;

		if(!self::isLicensedModule($moduleName))
			return true;

		if( GoodByeCaptcha::isProVersion() ) {
			return 0 === strpos(self::getModuleDirectoryPath($moduleName), dirname(__FILE__));
		}

		return false;
	}

	public static function getModuleNameById($moduleId)
	{
		foreach(self::$arrAllModules as $moduleKey => $moduleValue)
		{
			if (isset($moduleValue['info']['ModuleId']) && $moduleValue['info']['ModuleId'] == $moduleId)
				return $moduleKey;
		}

		return null;
	}

	public static function getModuleOptionDisplayText($moduleId, $optionId)
	{
		if(null === ($moduleAdminInstance = self::getAdminModuleInstance(self::getModuleNameById($moduleId))))
			return null;

		return $moduleAdminInstance->getOptionDisplayTextByOptionId($optionId);
	}

	public static function getModuleOptionId($moduleName, $optionName)
	{
		if(null === ($moduleAdminInstance = self::getAdminModuleInstance($moduleName)))
			return null;

		return $moduleAdminInstance->getOptionIdByOptionName($optionName);
	}

	public static function getModuleDirectoryPath($moduleName)
	{
		if(null === self::$arrRegisteredModules)
			self::setRegisteredModules();

		if(!isset(self::$arrRegisteredModules[$moduleName]) || !is_array(self::$arrRegisteredModules[$moduleName]))
			return null;

		return @dirname(reset(self::$arrRegisteredModules[$moduleName]));
	}

	/**
	 *
	 * @staticvar array $arrInstances
	 * @param string $moduleName
	 * @param int $moduleType
	 * @return \MchGdbcBaseModule | null
	 */
	private static function getModuleInstance($moduleName, $moduleType)
	{
		if(null === self::$arrRegisteredModules)
			self::setRegisteredModules();

		if(!isset(self::$arrRegisteredModules[$moduleName]))
			return null;

		foreach (self::$arrRegisteredModules[$moduleName] as $moduleClassName => $filePath)
		{
			if(1 === $moduleType && (false === strpos($moduleClassName, 'Admin')))
				continue;
			elseif(2 === $moduleType && (false === strpos($moduleClassName, 'Public')))
				continue;

			if(!method_exists($moduleClassName, 'getInstance'))
				return null;

			if(false !== ($moduleInstance = call_user_func(array($moduleClassName, 'getInstance'))))
				return $moduleInstance;
		}

		return null;
	}

	/**
	 * @param string $moduleName Module name
	 *
	 * @return \GdbcBaseAdminModule|null
	 */
	public static function getAdminModuleInstance($moduleName)
	{
		return self::getModuleInstance($moduleName, 1);
	}

	/**
	 * @param string $moduleName Module name
	 *
	 * @return \MchGdbcBasePublicModule|null
	 */
	public static function getPublicModuleInstance($moduleName)
	{
		return self::getModuleInstance($moduleName, 2);
	}

	/**
	 * @param $moduleName string Module name
	 *
	 * @return bool
	 */
	public static function isModuleRegistered($moduleName)
	{
		if(null === self::$arrRegisteredModules)
			self::setRegisteredModules();

		return 	isset(self::$arrRegisteredModules[$moduleName]);
	}

	public static function autoLoadModulesClasses($moduleClassName)
	{
		if( !isset($moduleClassName[15]) || 'Gdbc' !== substr($moduleClassName, 0, 4) )
			return;

		if(null === self::$arrRegisteredModules)
			self::setRegisteredModules();

		foreach(self::$arrRegisteredModules as $arrModuleClasses)
		{
			if(!isset($arrModuleClasses[$moduleClassName]))
				continue;

			return require_once($arrModuleClasses[$moduleClassName]);
		}

	}

}