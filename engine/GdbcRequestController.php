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

final class GdbcRequestController
{

	CONST TOKEN_SEPARATOR = '|';

	CONST REJECT_REASON_TOKEN_INVALID          = 1;
	CONST REJECT_REASON_TOKEN_MISSING          = 2;
	CONST REJECT_REASON_TOKEN_EXPIRED          = 3;
	CONST REJECT_REASON_TOKEN_SUBMITTED_EARLY  = 4;
	CONST REJECT_REASON_CLIENT_IP_BLOCKED      = 5;
	CONST REJECT_REASON_BROWSER_INFO_MISSING   = 6;
	CONST REJECT_REASON_BROWSER_INFO_INVALID   = 7;
	CONST REJECT_REASON_CLIENT_IP_UNDETECTABLE = 8;
	CONST REJECT_REASON_USER_ENUMERATION       = 9;
	CONST REJECT_REASON_PROXY_ANONYMIZER       = 10;
	CONST REJECT_REASON_WEB_ATTACKER           = 11;
	CONST REJECT_REASON_SERVICE_UNAVAILABLE    = 12;
	//CONST REJECT_REASON_LINK_NOTIFICATION      = 13; // used for trackbacks and pingbacks
	CONST REJECT_REASON_COMMENT_FIELD_TOO_LONG = 14;
	CONST REJECT_REASON_COUNTRY_IP_BLOCKED     = 15;

	private static $rejectReasonCode     = null;
	private static $browserInfoInputName = null;

	public static function isValid(GdbcAttemptEntity $attemptEntity)
	{
		static $isRequestValid = null;

		if(null !== $isRequestValid)
			return $isRequestValid;

		$settingsModuleInstance = GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_SETTINGS);
		if(null === $settingsModuleInstance)
			return $isRequestValid = false;

		$isTestModeActivated = (bool)$settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_TEST_MODE_ACTIVATED);

		if( (!$isTestModeActivated) && GdbcIPUtils::isClientIpWhiteListed())
			return $isRequestValid = true;

		if( self::isReceivedTokenValid($attemptEntity) && GdbcIPUtils::isClientIpBlackListed()){
			self::$rejectReasonCode = self::REJECT_REASON_CLIENT_IP_BLOCKED;
		}

		if((null === self::$rejectReasonCode) && GdbcIPUtils::isClientIpBlockedByCountry()){
			self::$rejectReasonCode = self::REJECT_REASON_COUNTRY_IP_BLOCKED;
		}

		if( (null === self::$rejectReasonCode) && GoodByeCaptchaUtils::isLoginAttemptEntity($attemptEntity))
		{
			if ( GdbcIPUtils::isClientIpWebAttacker() ) {
				self::$rejectReasonCode = self::REJECT_REASON_WEB_ATTACKER;
			}
			elseif ( GdbcIPUtils::isClientIpProxyAnonymizer() ) {
				self::$rejectReasonCode = self::REJECT_REASON_PROXY_ANONYMIZER;
			}
		}


		if($isTestModeActivated){
			GdbcNotificationsController::sendTestModeEmailNotification($attemptEntity);
			self::$rejectReasonCode = null;
		}

		if(null === self::$rejectReasonCode){
			return $isRequestValid = true;
		}

		$attemptEntity->ReasonId = self::getRejectReasonId();
		GdbcBruteGuardian::logRejectedAttempt($attemptEntity);

		return $isRequestValid = false;

	}


	private static function isReceivedTokenValid(GdbcAttemptEntity $attemptEntity)
	{
		if(self::$rejectReasonCode !== null) {
			return false;
		}

		$settingsModuleInstance = GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_SETTINGS);
		if(null === $settingsModuleInstance)
			return false;

		$tokenSecretKey  = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_TOKEN_SECRET_KEY);
		$hiddenInputName = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);

		$minSubmissionTime  = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_MIN_SUBMISSION_TIME);

		$isProtectionDisabled = ((bool)$settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_DISABLE_IF_USER_LOGGED_IN)) && MchGdbcWpUtils::isUserLoggedIn();
		if($isProtectionDisabled) {
			return true;
		}

		if(null === GdbcIPUtils::getClientIpAddress())
		{
			self::$rejectReasonCode = self::REJECT_REASON_CLIENT_IP_UNDETECTABLE;
			return false;
		}

		$receivedToken = isset($_POST[$hiddenInputName]) ? $_POST[$hiddenInputName] : null;

		if(null === $receivedToken){
			self::$rejectReasonCode = self::REJECT_REASON_TOKEN_MISSING;
			return false;
		}

		if(!isset($receivedToken[10])) {
			self::$rejectReasonCode = self::REJECT_REASON_TOKEN_INVALID;
			return false;
		}


		$arrDecryptedToken = json_decode(MchCrypt::decryptToken($tokenSecretKey, $receivedToken), true);

		if( !isset($arrDecryptedToken[0]) || false === ($tokenIndex = strpos($arrDecryptedToken[0], self::TOKEN_SEPARATOR)) )
		{
			self::$rejectReasonCode = self::REJECT_REASON_TOKEN_INVALID;
			return false;
		}

		self::$browserInfoInputName = substr($arrDecryptedToken[0], 0, $tokenIndex);

		$receivedBrowserInfoInput = isset($_POST[self::$browserInfoInputName]) ? $_POST[self::$browserInfoInputName] : null;

		if( null === $receivedBrowserInfoInput )
		{
			self::$rejectReasonCode = self::REJECT_REASON_BROWSER_INFO_MISSING;
			return false;
		}

		$receivedBrowserInfoInput = MchGdbcUtils::replaceNonAlphaNumericCharacters($receivedBrowserInfoInput, '');

		if($arrDecryptedToken[0] !== self::$browserInfoInputName . self::TOKEN_SEPARATOR . $receivedBrowserInfoInput)
		{
			self::$rejectReasonCode = self::REJECT_REASON_BROWSER_INFO_INVALID;
			return false;
		}

		array_shift($arrDecryptedToken);

		$arrTokenData = self::getTokenData();

		$timeSinceGenerated = ((int)array_pop($arrTokenData)) - ((int)array_pop($arrDecryptedToken));
		if($timeSinceGenerated < $minSubmissionTime)
		{
			if( ! GoodByeCaptchaUtils::isLoginAttemptEntity($attemptEntity) ){
				self::$rejectReasonCode = self::REJECT_REASON_TOKEN_SUBMITTED_EARLY;
				return false;
			}
		}

		if(count(array_diff($arrDecryptedToken, $arrTokenData)) !== 0)
		{
			self::$rejectReasonCode = self::REJECT_REASON_TOKEN_INVALID;
			return false;
		}

		unset($_POST[self::$browserInfoInputName], $_POST[$hiddenInputName]);

		global $ultimatemember;

		if(isset($ultimatemember->form))
		{
			unset($ultimatemember->form->post_form[self::$browserInfoInputName], $ultimatemember->form->post_form[$hiddenInputName]);
			unset($ultimatemember->form->post_form['submitted'][self::$browserInfoInputName], $ultimatemember->form->post_form['submitted'][$hiddenInputName]);
		}

		return true;

	}


	public static function getEncryptedToken()
	{
		if( ! isset($_POST['browserInfo']) || null === ($arrBrowserInfo = json_decode(stripcslashes($_POST['browserInfo']), true)))
			return array();

		foreach ((array)$arrBrowserInfo as $prop => $propValue)
		{
			if(!is_array($propValue) && false === strpos($prop, ' '))
				continue;

			unset($arrBrowserInfo[$prop]);
		}

		if( ($arrBrowserInfoLength = count($arrBrowserInfo)) < 3)
			return array();

		$arrKeysToSave = array_flip((array)array_rand($arrBrowserInfo, mt_rand(3, $arrBrowserInfoLength - 1)));

		foreach ($arrKeysToSave as $key => &$val)
		{
			$val = var_export($arrBrowserInfo[$key], true);
		}

		$arrTokenData = self::getTokenData();
		$browserField = MchGdbcUtils::replaceNonAlphaCharacters(MchCrypt::getRandomString(25), '-');

		array_unshift($arrTokenData, $browserField . self::TOKEN_SEPARATOR . MchGdbcUtils::replaceNonAlphaNumericCharacters(implode('', array_values($arrKeysToSave)), ''));

		return array(
			'token'       => MchCrypt::encryptToken(GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_TOKEN_SECRET_KEY), json_encode($arrTokenData)),
			$browserField => implode(self::TOKEN_SEPARATOR, array_keys($arrKeysToSave))
		);

	}


	private static function getTokenData()
	{
		$arrData   = array();

		$arrData[] = get_current_blog_id();
		//$arrData[] = GdbcIPUtils::getClientIpAddress();
		$arrData[] = GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_TOKEN_CREATED_TIMESTAMP);
		$arrData[] = MchGdbcHttpRequest::getServerRequestTime();

		return array_filter($arrData);
	}

	public static function tokenAlreadyRejected()
	{
		return null !== self::$rejectReasonCode;
	}

	public static function getRejectReasonId()
	{
		return self::$rejectReasonCode;
	}

	public static function getRejectReasonDescription($reasonId)
	{
		static $arrReasonDescription = null;
		if(null === $arrReasonDescription)
		{
			$arrReasonDescription =  array(

				self::REJECT_REASON_TOKEN_INVALID           => __('Invalid Token',          GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_TOKEN_MISSING           => __('Token Not Submitted',    GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_TOKEN_EXPIRED           => __('Token Expired',          GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_TOKEN_SUBMITTED_EARLY   => __('Token Submitted Early',  GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_CLIENT_IP_BLOCKED       => __('Client IP Blocked',      GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_BROWSER_INFO_MISSING    => __('Browser Info Missing',   GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_BROWSER_INFO_INVALID    => __('Browser Info Invalid',   GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_CLIENT_IP_UNDETECTABLE  => __('Undetectable Client IP', GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_USER_ENUMERATION        => __('User Enumeration',       GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_PROXY_ANONYMIZER        => __('Proxy Anonymizer',       GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_WEB_ATTACKER            => __('Web Attacker',           GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_SERVICE_UNAVAILABLE     => __('Service Unavailable',    GoodByeCaptcha::PLUGIN_SLUG),
				//self::REJECT_REASON_LINK_NOTIFICATION     => __('Link Notification',      GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_COMMENT_FIELD_TOO_LONG  => __('Comment Field Too Long', GoodByeCaptcha::PLUGIN_SLUG),
				self::REJECT_REASON_COUNTRY_IP_BLOCKED      => __('Blocked Country',        GoodByeCaptcha::PLUGIN_SLUG),
			);
		}

		return isset($arrReasonDescription[$reasonId]) ? $arrReasonDescription[$reasonId] : __('Unknown', GoodByeCaptcha::PLUGIN_SLUG);

	}


	public static function getPostedBrowserInfoInputName()
	{
		return self::$browserInfoInputName;
	}

	public static function redirectToHomePage()
	{
		wp_redirect( home_url() ); exit;
	}

}
