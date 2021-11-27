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

class GdbcSettingsAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_PLUGIN_VERSION            = 'PluginVersion';

	CONST OPTION_TOKEN_SECRET_KEY          = 'TokenSecretKey';
	CONST OPTION_TOKEN_CREATED_TIMESTAMP   = 'TokenCreatedTimestamp';
	CONST OPTION_HIDDEN_INPUT_NAME         = 'HiddenInputName';

	CONST OPTION_MIN_SUBMISSION_TIME       = 'MinSubmissionTime';

	CONST OPTION_DISABLE_IF_USER_LOGGED_IN = 'DisabledIfUserLoggedIn';

	CONST OPTION_MAX_LOGS_DAYS             = 'MaxLogsDays';
	CONST OPTION_BLOCKED_CONTENT_LOG_DAYS  = 'MaxContentLogDays';
	CONST OPTION_TEST_MODE_ACTIVATED       = 'IsTestModeActivated';
	CONST OPTION_CACHE_DIR_PATH            = 'CacheDirPath';
	CONST OPTION_HIDE_SUBSCRIBE_FORM       = 'HideSubscribeForm';

	protected function __construct()
	{
		parent::__construct();

		$this->saveSecuredOptions(false);

	}

	public function getDefaultOptions()
	{
		static $arrDefaultSettingOptions = null;
		if(null !== $arrDefaultSettingOptions)
			return $arrDefaultSettingOptions;

		$arrDefaultSettingOptions = array(

			self::OPTION_MIN_SUBMISSION_TIME  => array(
				'Value'       => 3,
				'LabelText'   => __('Minimum Form Submission Time', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Number of seconds before the submission is considered valid', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

			self::OPTION_BLOCKED_CONTENT_LOG_DAYS  => array(
				'Value'       => 10,
				'LabelText'   => __('Keep Blocked Submitted Content For', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('The blocked submitted data will be saved for the selected number of days', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_SELECT
			),

			self::OPTION_MAX_LOGS_DAYS  => array(
				'Value'       => 30,
				'LabelText'   => __('Automatically Purge Logs Older Than', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Logs older than selected number of days will be automatically purged', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_SELECT
			),

			self::OPTION_DISABLE_IF_USER_LOGGED_IN  => array(
				'Value'       => NULL,
				'LabelText'   => __('Disable Protection For Logged In Users', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('If this option is enabled, the protection will be disabled if the user is logged in', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_TEST_MODE_ACTIVATED => array(
				'Value'       => NULL,
				'LabelText'   => __('Switch WPBruiser to Test Mode', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('While in Test Mode you will receive email notifications at {notification-email}', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

		);

		return $arrDefaultSettingOptions;

	}


//	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
//	{
//		echo '<h3>' . __('WPBruiser General Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
//	}

	public  function renderModuleSettingsField(array $arrSettingsField)
	{
		$optionName = key($arrSettingsField);
		$defaultOptionValues = $this->getDefaultOptionsValues();
		if(null === $optionName || !array_key_exists($optionName, $defaultOptionValues))
			return;

		$optionValue = $this->getOption($optionName);
		if(null === $optionValue && isset($defaultOptionValues[$optionName]))
		{
			if(!is_array($defaultOptionValues[$optionName])) {
				$optionValue = $defaultOptionValues[$optionName];
			}
		}

		$arrSettingsField = $arrSettingsField[$optionName];
		$arrFieldAttributes = array(
			'name'  => $this->getSettingKey() . '[' . $optionName . ']',
			'type'  => !empty($arrSettingsField['InputType']) ? $arrSettingsField['InputType'] : 'text',
			'value' => $optionValue,
			'id'    => $this->getSettingKey() . '-' . $optionName,
		);

		if($arrFieldAttributes['type'] === MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX)
		{
			!empty($arrFieldAttributes['value']) ? $arrFieldAttributes['checked'] = 'checked' : null;
			$arrFieldAttributes['value'] = true;
		}

		if($optionName === self::OPTION_MAX_LOGS_DAYS)
		{
			$arrFieldAttributes['options'] = array();
			for($i = 0; $i <= 6; ++$i) {
				$arrFieldAttributes['options'][ ( 30 * $i ) . ' days' ] = 30 * $i;
			}
		}

		if($optionName === self::OPTION_BLOCKED_CONTENT_LOG_DAYS)
		{
			$arrFieldAttributes['options'] = array();
			for($i = 0; $i <= 6; ++$i) {
				$arrFieldAttributes['options'][ ( 5 * $i ) . ' days' ] = 5 * $i;
			}
		}

		switch ($arrFieldAttributes['type'])
		{
			case MchGdbcHtmlUtils::FORM_ELEMENT_SELECT :

				echo MchGdbcHtmlUtils::createSelectElement($arrFieldAttributes);

				break;

			default :

				echo MchGdbcHtmlUtils::createInputElement($arrFieldAttributes);
		}

		if($optionName === self::OPTION_TEST_MODE_ACTIVATED && !empty($arrSettingsField['Description']))
		{
			$arrSettingsField['Description'] = str_replace('{notification-email}', GdbcEmailNotificationsAdminModule::getInstance()->getOption(GdbcEmailNotificationsAdminModule::OPTION_EMAIL_ADDRESS), $arrSettingsField['Description']);
		}

		if(!empty($arrSettingsField['Description']))
		{
			echo '<p class = "description">' . $arrSettingsField['Description'] . '</p>';

			if($optionName === self::OPTION_MAX_LOGS_DAYS)
			{
				echo '<p class = "description hidden" style = "color:#d54e21">' .  __('By selecting ZERO you TURN OFF logging and you wont be protected against Brute Force attacks !', GoodByeCaptcha::PLUGIN_SLUG)  . '</p>';
			}
		}

	}


	public  function validateModuleSettingsFields($arrSettingOptions)
	{

		$arrSettingOptions = array_map('sanitize_text_field', (array)$arrSettingOptions);

		if (empty($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME])
		    || false === ($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME] = filter_var($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME], FILTER_VALIDATE_INT))
		    || $arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME] < 1
		){
			$this->registerErrorMessage(__('Minimum Submission Time should be a numeric value greater than 0 !', GoodByeCaptcha::PLUGIN_SLUG));
			unset($arrSettingOptions[self::OPTION_MIN_SUBMISSION_TIME]);
		}

		$arrOldSavedOptions = $this->getAllSavedOptions();

		if(!empty($arrOldSavedOptions[self::OPTION_TOKEN_SECRET_KEY]))
			$arrSettingOptions[self::OPTION_TOKEN_SECRET_KEY] = $arrOldSavedOptions[self::OPTION_TOKEN_SECRET_KEY];

		if(!empty($arrOldSavedOptions[self::OPTION_TOKEN_CREATED_TIMESTAMP]))
			$arrSettingOptions[self::OPTION_TOKEN_CREATED_TIMESTAMP] = $arrOldSavedOptions[self::OPTION_TOKEN_CREATED_TIMESTAMP];

		if(!empty($arrOldSavedOptions[self::OPTION_HIDDEN_INPUT_NAME]))
			$arrSettingOptions[self::OPTION_HIDDEN_INPUT_NAME] = $arrOldSavedOptions[self::OPTION_HIDDEN_INPUT_NAME];

		if(!empty($arrOldSavedOptions[self::OPTION_CACHE_DIR_PATH]))
			$arrSettingOptions[self::OPTION_CACHE_DIR_PATH] = $arrOldSavedOptions[self::OPTION_CACHE_DIR_PATH];

		$arrSettingOptions[self::OPTION_PLUGIN_VERSION] = GoodByeCaptcha::PLUGIN_VERSION;

		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));

		return $arrSettingOptions;

	}

	public function saveSecuredOptions($forceNewValues)
	{
		$inputHiddenName = $this->getOption(self::OPTION_HIDDEN_INPUT_NAME);
		if( (false === (!!$forceNewValues)) && !empty($inputHiddenName) )
			return;

//		if ( defined( 'WP_UNINSTALL_PLUGIN' ) )
//			return;

		$arrSettingOptions = array(
			self::OPTION_TOKEN_SECRET_KEY        => MchCrypt::getRandomString(MchCrypt::getCipherKeySize()),
			self::OPTION_TOKEN_CREATED_TIMESTAMP => MchGdbcHttpRequest::getServerRequestTime(),
			self::OPTION_HIDDEN_INPUT_NAME       => empty($inputHiddenName) ? MchGdbcUtils::replaceNonAlphaCharacters(MchCrypt::getRandomString(25)) : $inputHiddenName,
		);

		while( ! isset($arrSettingOptions[self::OPTION_HIDDEN_INPUT_NAME][9]) ) {
			$arrSettingOptions[ self::OPTION_HIDDEN_INPUT_NAME ] = MchGdbcUtils::replaceNonAlphaCharacters( MchCrypt::getRandomString( 25 ) );
		}

		foreach($arrSettingOptions as $optionName => $value){
			$this->saveOption($optionName, $value);
		}
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