<?php
/*
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

final class GoodByeCaptchaUtils
{
	public static function isAffiliateWPActivated()
	{
		return class_exists('Affiliate_WP');
	}

	public static function isEasyDigitalDownloadsActivated()
	{
		return class_exists('Easy_Digital_Downloads');
	}

	public static function isZmAlrActivated()
	{
		return defined('ZM_ALR_NAMESPACE');
	}

	public static function isUserProfileMadeEasyActivated()
	{
		return defined('upme_path');
	}

	public static function isWooCommerceActivated()
	{
		return class_exists('WooCommerce');
	}

	public static function isUltimateMemberActivated()
	{
		return class_exists('UM_API') || class_exists( 'UM' ) ;
	}

	public static function isUjiCountDownActivated()
	{
		return class_exists('Uji_Countdown');
	}

	public static function isQuFormActivated()
	{
		return class_exists('iPhorm');
	}

	public static function isMailPoetActivated()
	{
		return defined( 'WYSIJA' );
	}

	public static function isMailChimpForWPActivated()
	{
		return function_exists('__mc4wp_load_plugin') || function_exists('__mc4wp_premium_load') || function_exists('mc4wp_load_plugin') || function_exists('mc4wp_pro_load_plugin');
	}

	public static function isNinjaFormsActivated()
	{
		return class_exists('Ninja_Forms');
	}

	public static function isPlanSoFormsActivated()
	{
		return function_exists('psfb_register');
	}

	public static function isSeamlessDonationsActivated()
	{
		return function_exists('seamless_donations_init');
	}

	public static function isGravityFormsActivated()
	{
		return class_exists('GFForms');
	}

	public static function isContactForm7Activated()
	{
		return class_exists('WPCF7_ContactForm');
	}

	public static function isFastSecureFormActivated()
	{
		return class_exists('FSCF_Util');
	}

	public static function isFormidableFormsActivated()
	{
		return class_exists('FrmSettings');
	}

	public static function isUserProPluginActivated()
	{
		return class_exists('userpro_api');
	}

	public static function isWPRocketPluginActivated()
	{
		return defined('WP_ROCKET_FILE');
	}

	public static function isAutoptimizePluginActivated()
	{
		return defined('AUTOPTIMIZE_CACHE_DIR');
	}

	public static function isGoogleAppsLoginPluginActivated()
	{
		return class_exists('core_google_apps_login');
	}

	public static function isMemberPressPluginActivated()
	{
		return defined('MEPR_VERSION');
	}

	public static function isBuddyPressPluginActivated()
	{
		return class_exists('BP_Core');
	}

	public static function isEasyFormsForMailChimpPluginActivated()
	{
		return class_exists('Yikes_Inc_Easy_Mailchimp_Extender');
	}

	public static function isWPDJSPluginActivated() // WP Deferred JavaScripts
	{
		return defined('WDJS_VERSION');
	}

	public static function isWPDiscuzPluginActivated()
	{
		return class_exists('WpdiscuzCore');
	}

//	public static function setCookie($cookieKey, $cookieValue, $cookieTime, $path = null, $httpOnly = true)
//	{
//		if(headers_sent()) return;
//		return setcookie($cookieKey, $cookieValue, $cookieTime  + (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()), empty($path) ? COOKIEPATH : $path, COOKIE_DOMAIN, is_ssl(), $httpOnly);
//	}
//
//	public static function getCookie($cookieKey)
//	{
//		return isset($_COOKIE[$cookieKey]) ? $_COOKIE[$cookieKey] : null;
//	}
//
//	public static function deleteCookie($cookieKey)
//	{
//		if(headers_sent()) return;
//		return setcookie($cookieKey, null, (isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()) - 3600);
//	}

	public static function isJetPackContactFormModuleActivated()
	{
		return self::isJetPackModuleActivated('contact-form');
	}

	public static function isJetPackCommentsModuleActivated()
	{
		return self::isJetPackModuleActivated('comments');
	}

	public static function isValidReferer()
	{
		static $validReferer = null;
		if(null !== $validReferer)
			return $validReferer;

		$referer    = wp_get_referer();
		$actualHost = parse_url(home_url(), PHP_URL_HOST);

		return $validReferer = (!empty($referer) && !empty($actualHost) && false !== stripos($referer, $actualHost));
	}

	public static function isNginxWebServer()
	{
		if(empty($_SERVER['SERVER_SOFTWARE']))
			return false;

		return (false !== stripos($_SERVER['SERVER_SOFTWARE'], 'nginx')) && (@php_sapi_name() === 'fpm-fcgi');
	}

	public static function isJetPackPluginActivated()
	{
		return class_exists('Jetpack');
	}

	private static function isJetPackModuleActivated($moduleName)
	{
		static $arrActivatedModules = array();
		if(isset($arrActivatedModules[$moduleName]))
			return $arrActivatedModules[$moduleName];

		return $arrActivatedModules[$moduleName] = ((null !== ($arrJetPackModules = self::getJetPackActiveModules())) &&
													in_array(strtolower($moduleName), $arrJetPackModules, true));
	}

	private static function getJetPackActiveModules()
	{
		static $isActivated = null;
		(null === $isActivated) ? $isActivated = class_exists( 'Jetpack' ) : null;

		if( !$isActivated)
			return null;

		static $arrJetPackOptions = null;
		if(null !== $arrJetPackOptions)
			return $arrJetPackOptions;

		$arrJetPackOptions = get_option('jetpack_active_modules');
		if(false === $arrJetPackOptions)
			return null;

		foreach ($arrJetPackOptions as &$moduleName)
			$moduleName = strtolower(trim($moduleName));

		return $arrJetPackOptions;
	}


	public static function getCountryIdByCode($countryCode)
	{
		$countryCode = trim(strtoupper($countryCode));

		foreach (self::getCountryDataSource() as $key => $value)
			if ($countryCode === $value[1])
				return $key;

		return 0;
	}

	public static function getCountryCodeById($countryId)
	{
		$arrCountry = self::getCountryDataSource();
		return isset($arrCountry[$countryId][1]) ? $arrCountry[$countryId][1] : null;
	}

	public static function getCountryNameById($countryId)
	{
		$arrCountry = self::getCountryDataSource();
		return isset($arrCountry[$countryId][0]) ? $arrCountry[$countryId][0] : null;
	}

	public static function getCountryDataSource()
	{
		return array(
			1 => array("Afghanistan", "AF"),
			2 => array("Aland Islands", "AX"),
			3 => array("Albania", "AL"),
			4 => array("Algeria", "DZ"),
			5 => array("American Samoa", "AS"),
			6 => array("Andorra", "AD"),
			7 => array("Angola", "AO"),
			8 => array("Anguilla", "AI"),
			9 => array("Antarctica", "AQ"),
			10 => array("Antigua and Barbuda", "AG"),
			11 => array("Argentina", "AR"),
			12 => array("Armenia", "AM"),
			13 => array("Aruba", "AW"),
			14 => array("Australia", "AU"),
			15 => array("Austria", "AT"),
			16 => array("Azerbaijan", "AZ"),
			17 => array("Bahamas", "BS"),
			18 => array("Bahrain", "BH"),
			19 => array("Bangladesh", "BD"),
			20 => array("Barbados", "BB"),
			21 => array("Belarus", "BY"),
			22 => array("Belgium", "BE"),
			23 => array("Belize", "BZ"),
			24 => array("Benin", "BJ"),
			25 => array("Bermuda", "BM"),
			26 => array("Bhutan", "BT"),
			27 => array("Bolivia", "BO"),
			28 => array("Bosnia and Herzegovina", "BA"),
			29 => array("Botswana", "BW"),
			30 => array("Bouvet island", "BV"),
			31 => array("Brazil", "BR"),
			32 => array("British Indian Ocean", "IO"),
			33 => array("Brunei Darussalam", "BN"),
			34 => array("Bulgaria", "BG"),
			35 => array("Burkina Faso", "BF"),
			36 => array("Burundi", "BI"),
			37 => array("Cambodia", "KH"),
			38 => array("Cameroon", "CM"),
			39 => array("Canada", "CA"),
			40 => array("Cape Verde", "CV"),
			41 => array("Cayman Islands", "KY"),
			42 => array("Central African Republic", "CF"),
			43 => array("Chad", "TD"),
			44 => array("Chile", "CL"),
			45 => array("China", "CN"),
			46 => array("Christmas Island", "CX"),
			47 => array("Cocos Islands", "CC"),
			48 => array("Colombia", "CO"),
			49 => array("Comoros", "KM"),
			50 => array("Congo", "CG"),
			51 => array("Congo", "CD"),
			52 => array("Cook Islands", "CK"),
			53 => array("Costa Rica", "CR"),
			54 => array("Cote d'Ivoire", "CI"),
			55 => array("Croatia", "HR"),
			56 => array("Cuba", "CU"),
			57 => array("Cyprus", "CY"),
			58 => array("Czech Republic", "CZ"),
			59 => array("Denmark", "DK"),
			60 => array("Djibouti", "DJ"),
			61 => array("Dominica", "DM"),
			62 => array("Dominican republic", "DO"),
			63 => array("Ecuador", "EC"),
			64 => array("Egypt", "EG"),
			65 => array("El Salvador", "SV"),
			66 => array("Equatorial Guinea", "GQ"),
			67 => array("Eritrea", "ER"),
			68 => array("Estonia", "EE"),
			69 => array("Ethiopia", "ET"),
			70 => array("Falkland Islands", "FK"),
			71 => array("Faroe Islands", "FO"),
			72 => array("Fiji", "FJ"),
			73 => array("Finland", "FI"),
			74 => array("France", "FR"),
			75 => array("French Guiana", "GF"),
			76 => array("French Polynesia", "PF"),
			77 => array("French Southern Territories", "TF"),
			78 => array("Gabon", "GA"),
			79 => array("Gambia", "GM"),
			80 => array("Georgia", "GE"),
			81 => array("Germany", "DE"),
			82 => array("Ghana", "GH"),
			83 => array("Gibraltar", "GI"),
			84 => array("Greece", "GR"),
			85 => array("Greenland", "GL"),
			86 => array("Grenada", "GD"),
			87 => array("Guadeloupe", "GP"),
			88 => array("Guam", "GU"),
			89 => array("Guatemala", "GT"),
			90 => array("Guernsey", "GG"),
			91 => array("Guinea", "GN"),
			92 => array("Guinea-Bissau", "GW"),
			93 => array("Guyana", "GY"),
			94 => array("Haiti", "HT"),
			95 => array("Heard and Mcdonald Islands", "HM"),
			96 => array("Vatican", "VA"),
			97 => array("Honduras", "HN"),
			98 => array("Hong Kong", "HK"),
			99 => array("Hungary", "HU"),
			100 => array("Iceland", "IS"),
			101 => array("India", "IN"),
			102 => array("Indonesia", "ID"),
			103 => array("Iran", "IR"),
			104 => array("Iraq", "IQ"),
			105 => array("Ireland", "IE"),
			106 => array("Isle of Man", "IM"),
			107 => array("Israel", "IL"),
			108 => array("Italy", "IT"),
			109 => array("Jamaica", "JM"),
			110 => array("Japan", "JP"),
			111 => array("Jersey", "JE"),
			112 => array("Jordan", "JO"),
			113 => array("Kazakhstan", "KZ"),
			114 => array("Kenya", "KE"),
			115 => array("Kiribati", "KI"),
			116 => array("Korea", "KR"),
			117 => array("Korea - North", "KP"),
			118 => array("Kuwait", "KW"),
			119 => array("Kyrgyzstan", "KG"),
			120 => array("Lao Republic", "LA"),
			121 => array("Latvia", "LV"),
			122 => array("Lebanon", "LB"),
			123 => array("Lesotho", "LS"),
			124 => array("Liberia", "LR"),
			125 => array("Libyan Arab Jamahiriya", "LY"),
			126 => array("Liechtenstein", "LI"),
			127 => array("Lithuania", "LT"),
			128 => array("Luxembourg", "LU"),
			129 => array("Macao", "MO"),
			130 => array("Macedonia", "MK"),
			131 => array("Madagascar", "MG"),
			132 => array("Malawi", "MW"),
			133 => array("Malaysia", "MY"),
			134 => array("Maldives", "MV"),
			135 => array("Mali", "ML"),
			136 => array("Malta", "MT"),
			137 => array("Marshall Islands", "MH"),
			138 => array("Martinique", "MQ"),
			139 => array("Mauritania", "MR"),
			140 => array("Mauritius", "MU"),
			141 => array("Mayotte", "YT"),
			142 => array("Mexico", "MX"),
			143 => array("Micronesia", "FM"),
			144 => array("Moldova", "MD"),
			145 => array("Monaco", "MC"),
			146 => array("Mongolia", "MN"),
			147 => array("Montenegro", "ME"),
			148 => array("Montserrat", "MS"),
			149 => array("Morocco", "MA"),
			150 => array("Mozambique", "MZ"),
			151 => array("Myanmar", "MM"),
			152 => array("Namibia", "NA"),
			153 => array("Nauru", "NR"),
			154 => array("Nepal", "NP"),
			155 => array("Netherlands", "NL"),
			156 => array("Netherlands Antilles", "AN"),
			157 => array("New Caledonia", "NC"),
			158 => array("New Zealand", "NZ"),
			159 => array("Nicaragua", "NI"),
			160 => array("Niger", "NE"),
			161 => array("Nigeria", "NG"),
			162 => array("Niue", "NU"),
			163 => array("Norfolk Island", "NF"),
			164 => array("Northern Mariana Islands", "MP"),
			165 => array("Norway", "NO"),
			166 => array("Oman", "OM"),
			167 => array("Pakistan", "PK"),
			168 => array("Palau", "PW"),
			169 => array("Palestinian Territory Occupied", "PS"),
			170 => array("Panama", "PA"),
			171 => array("Papua New Guinea", "PG"),
			172 => array("Paraguay", "PY"),
			173 => array("Peru", "PE"),
			174 => array("Philippines", "PH"),
			175 => array("Pitcairn", "PN"),
			176 => array("Poland", "PL"),
			177 => array("Portugal", "PT"),
			178 => array("Puerto rico", "PR"),
			179 => array("Qatar", "QA"),
			180 => array("Reunion", "RE"),
			181 => array("Romania", "RO"),
			182 => array("Russian Federation", "RU"),
			183 => array("Rwanda", "RW"),
			184 => array("Saint Barthelemy", "BL"),
			185 => array("Saint Helena", "SH"),
			186 => array("Saint Kitts and Nevis", "KN"),
			187 => array("Saint Lucia", "LC"),
			188 => array("Saint Martin", "MF"),
			189 => array("Saint Pierre and Miquelon", "PM"),
			190 => array("Saint Vincent", "VC"),
			191 => array("Samoa", "WS"),
			192 => array("San Marino", "SM"),
			193 => array("Sao Tome and Principe", "ST"),
			194 => array("Saudi Arabia", "SA"),
			195 => array("Senegal", "SN"),
			196 => array("Serbia", "RS"),
			197 => array("Seychelles", "SC"),
			198 => array("Sierra Leone", "SL"),
			199 => array("Singapore", "SG"),
			200 => array("Slovakia", "SK"),
			201 => array("Slovenia", "SI"),
			202 => array("Solomon Islands", "SB"),
			203 => array("Somalia", "SO"),
			204 => array("South Africa", "ZA"),
			205 => array("South Georgia and Islands", "GS"),
			206 => array("Spain", "ES"),
			207 => array("Sri Lanka", "LK"),
			208 => array("Sudan", "SD"),
			209 => array("Suriname", "SR"),
			210 => array("Svalbard and Jan Mayen", "SJ"),
			211 => array("Swaziland", "SZ"),
			212 => array("Sweden", "SE"),
			213 => array("Switzerland", "CH"),
			214 => array("Syrian Arab Republic", "SY"),
			215 => array("Taiwan", "TW"),
			216 => array("Tajikistan", "TJ"),
			217 => array("Tanzania", "TZ"),
			218 => array("Thailand", "TH"),
			219 => array("Timor-Leste", "TL"),
			220 => array("Togo", "TG"),
			221 => array("Tokelau", "TK"),
			222 => array("Tonga", "TO"),
			223 => array("Trinidad and Tobago", "TT"),
			224 => array("Tunisia", "TN"),
			225 => array("Turkey", "TR"),
			226 => array("Turkmenistan", "TM"),
			227 => array("Turks and Caicos Islands", "TC"),
			228 => array("Tuvalu", "TV"),
			229 => array("Uganda", "UG"),
			230 => array("Ukraine", "UA"),
			231 => array("United Arab Emirates", "AE"),
			232 => array("United Kingdom", "GB"),
			233 => array("United States", "US"),
			234 => array("United States Minor Islands", "UM"),
			235 => array("Uruguay", "UY"),
			236 => array("Uzbekistan", "UZ"),
			237 => array("Vanuatu", "VU"),
			238 => array("Venezuela", "VE"),
			239 => array("Vietnam", "VN"),
			240 => array("Virgin Islands British", "VG"),
			241 => array("Virgin Islands U.S.", "VI"),
			242 => array("Wallis and Futuna", "WF"),
			243 => array("Western Sahara", "EH"),
			244 => array("Yemen", "YE"),
			245 => array("Zambia", "ZM"),
			246 => array("Zimbabwe", "ZW"),
			247 => array("South Sudan", "SS"),
			248 => array("Sint Maarten", "SX"),
			249 => array("Curacao", "CW"),
			250 => array("Bonaire", "BQ")
		);
	}


	public static function isPostRequestForWPStandardLogin()
	{
		return !empty($_POST) && function_exists('login_header') && !MchGdbcWpUtils::isAjaxRequest();
	}

	public static function isLoginAttemptEntity(GdbcAttemptEntity $attemptEntity)
	{
		foreach(self::getAllPossibleLoginAttemptEntities() as $loginAttemptEntity)
		{
			if( ($loginAttemptEntity->ModuleId == $attemptEntity->ModuleId) && ($loginAttemptEntity->SectionId == $attemptEntity->SectionId) )
				return true;
		}

		return false;
	}

	public static function getAllPossibleLoginAttemptEntities()
	{
		static $loginEntitiesList = null;
		if(null !== $loginEntitiesList)
			return $loginEntitiesList;

		$loginEntitiesList = array();

		foreach(GdbcModulesController::getRegisteredModules() as $moduleName => $arrModuleClasses)
		{
			switch($moduleName)
			{
				case GdbcModulesController::MODULE_WORDPRESS :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcWordPressAdminModule::WORDPRESS_LOGIN_FORM);
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcWordPressAdminModule::WORDPRESS_LOGIN_XML_RPC);
					break;

				case GdbcModulesController::MODULE_ULTIMATE_MEMBER :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_ULTRA_COMMUNITY :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcUltraCommunityAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED);
					break;

				case GdbcModulesController::MODULE_WP_MEMBERS :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcWPMembersAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED);
					break;

				case GdbcModulesController::MODULE_WOOCOMMERCE :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName),  GdbcWooCommerceAdminModule::WOOCOMMERCE_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_USER_PRO :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName),  GdbcUserProAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED);
					break;

				case GdbcModulesController::MODULE_UPME :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName),  GdbcUPMEAdminModule::UPME_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_ZM_ALR :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcZmAlrAdminModule::OPTION_ZM_ALR_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_PLANSO_FORMS :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcPlanSoFormsAdminModule::OPTION_PLANSO_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_MEMBER_PRESS :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcMemberPressAdminModule::OPTION_MEMBER_PRESS_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_EASY_DIGITAL_DOWNLOADS :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcEDDAdminModule::EDD_LOGIN_FORM);
					break;

				case GdbcModulesController::MODULE_AFFILIATE_WP :
					$loginEntitiesList[] = new GdbcAttemptEntity(GdbcModulesController::getModuleIdByName($moduleName), GdbcAffiliateWPAdminModule::AFFILIATE_WP_LOGIN_FORM);
					break;

			}
		}

		foreach($loginEntitiesList as $index => &$attemptEntity)
		{
			$attemptEntity->SectionId = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::getModuleNameById($attemptEntity->ModuleId))->getOptionIdByOptionName($attemptEntity->SectionId);
			unset($attemptEntity->Id, $attemptEntity->ClientIp, $attemptEntity->CreatedDate, $attemptEntity->Notes, $attemptEntity->ReasonId, $attemptEntity->SiteId);
		}

		return $loginEntitiesList;
	}




	public static function flushSiteCache($siteId = 0)
	{
		$siteId = absint($siteId);
		if($siteId === 0)
			$siteId = get_current_blog_id();

//		$blogDetails = get_blog_details($siteId, false);
//		if(empty($blogDetails))
//			return;

//		unset($blogDetails);

		$shouldSwitchSite = ($siteId !== get_current_blog_id());

		($shouldSwitchSite) ? switch_to_blog( $siteId) : null;

		if(function_exists('w3tc_flush_all')) { // w3tc
			w3tc_flush_all();
		}

		if(function_exists('wp_cache_clear_cache')){ // wp super cache
			wp_cache_clear_cache();
		}

		if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){ // wp fastest cache
			$GLOBALS['wp_fastest_cache']->deleteCache();
		}

		if(class_exists('zencache') && method_exists('zencache', 'clear')){ // zencache
			zencache::clear();
		}

		if(self::isAutoptimizePluginActivated() && is_callable( array('autoptimizeCache','clearall') )){
			autoptimizeCache::clearall();
		}

		if(self::isWPRocketPluginActivated()){
			if(function_exists('do_rocket_purge_cron')){
				do_rocket_purge_cron();
			}
			else
			{
				function_exists('rocket_clean_domain') ? rocket_clean_domain() : null;
				function_exists('rocket_clean_minify') ? rocket_clean_minify() : null;
				function_exists('run_rocket_bot')      ? run_rocket_bot( 'cache-preload' ) : null;
			}
		}


		($shouldSwitchSite) ? restore_current_blog() : null;
	}


	/*
	 * return /MchGdbcCache
	 */
	public static function getAvailableCacheStorage($dirPathForFileStorage)
	{
		static $cacheStorage = false;
		if(false !== $cacheStorage)
			return $cacheStorage;

		$arrPossibleCacheStorage = array(
			!empty($dirPathForFileStorage) ? new MchGdbcCacheFileStorage($dirPathForFileStorage, true, 'wbr') : null,
			new MchGdbcWordPressTransientsStorage(false),
			new MchGdbcCacheAPCUStorage(),
			new MchGdbcCacheAPCStorage(),
			new MchGdbcCacheXCacheStorage(),
			new MchGdbcCacheZendMemoryStorage(),
			new MchGdbcCacheZendDiskStorage(),
			new MchGdbcWordPressTransientsStorage(true),
		);

		foreach ($arrPossibleCacheStorage as $cacheStorageObject) {
			if (null === $cacheStorageObject || !$cacheStorageObject->isAvailable())
				continue;

			return $cacheStorage = new MchGdbcCache($cacheStorageObject);
		}

		return $cacheStorage = null;
	}


}