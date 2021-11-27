<?php
/**
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

class GdbcEmailNotificationsAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_EMAIL_ADDRESS                = 'Email';
	CONST OPTION_TEST_MODE_NOTIFICATION       = 'TestModeNotification';
	CONST OPTION_BRUTE_FORCE_ATTACK_DETECTED  = 'IsUnderAttack';
	CONST OPTION_ADMIN_LOGGED_IN_DETECTED     = 'AdminLoggedIn';

	protected function __construct()
	{
		parent::__construct();
	}

	public function getDefaultOptions()
	{
		static $arrDefaultSettingOptions = null;
		if(null !== $arrDefaultSettingOptions)
			return $arrDefaultSettingOptions;

		$arrDefaultSettingOptions = array(

			self::OPTION_BRUTE_FORCE_ATTACK_DETECTED  => array(
				'Value'       => true,
				'LabelText'   => __('Brute Force Attack Detected', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('A notification email will be sent when a brute force attack is detected', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_ADMIN_LOGGED_IN_DETECTED => array(
					'Value'       => true,
					'LabelText'   => __('An Administrator Signs In', GoodByeCaptcha::PLUGIN_SLUG),
					'Description' => __('A notification email will be sent when a user with administrator capabilities signs in', GoodByeCaptcha::PLUGIN_SLUG),
					'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_EMAIL_ADDRESS  => array(
				'Value'       => MchGdbcWpUtils::getAdminEmailAddress(),
				'LabelText'   => __('Administrator Email Address', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('The email address where WPBruiser will send notifications', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

		);

		return $arrDefaultSettingOptions;

	}


	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{
		echo '<h3>' . __('Email Notifications Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
	}

	public  function renderModuleSettingsField(array $arrSettingsField)
	{
		parent::renderModuleSettingsField($arrSettingsField);
	}


	public  function validateModuleSettingsFields($arrSettingOptions)
	{

		$arrSettingOptions = array_map('sanitize_text_field', (array)$arrSettingOptions);
		if(!empty($arrSettingOptions[self::OPTION_EMAIL_ADDRESS]))
		{
			$arrSettingOptions[self::OPTION_EMAIL_ADDRESS] = sanitize_email($arrSettingOptions[self::OPTION_EMAIL_ADDRESS]);
			if(false === is_email($arrSettingOptions[self::OPTION_EMAIL_ADDRESS])){
				$this->registerErrorMessage(__('Please provide a valid email address!', GoodByeCaptcha::PLUGIN_SLUG));
				unset($arrSettingOptions[self::OPTION_EMAIL_ADDRESS]);
			}
		}

		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));

		return $arrSettingOptions;

	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		return null;
	}

}