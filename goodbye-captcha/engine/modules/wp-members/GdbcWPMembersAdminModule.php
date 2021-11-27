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

final class GdbcWPMembersAdminModule extends GdbcBaseAdminModule
{

	CONST OPTION_LOGIN_FORM_PROTECTION_ACTIVATED       = 'IsLoginActivated';
	CONST OPTION_REGISTER_FORM_PROTECTION_ACTIVATED    = 'IsRegisterActivated';
//	CONST OPTION_LOST_PASS_FORM_PROTECTION_ACTIVATED   = 'IsLostPasswordActivated';
//	CONST OPTION_CHANGE_PASS_FORM_PROTECTION_ACTIVATED = 'IsChangePasswordActivated';

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

			self::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED    => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText'  => __('Protect Login Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Login', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_REGISTER_FORM_PROTECTION_ACTIVATED  => array(
				'Id'         => 2,
				'Value'      => NULL,
				'LabelText'  => __('Protect Registration Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Registration', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

//			self::OPTION_LOST_PASS_FORM_PROTECTION_ACTIVATED  => array(
//				'Id'         => 3,
//				'Value'      => NULL,
//				'LabelText'  => __('Protect Lost Password Form', GoodByeCaptcha::PLUGIN_SLUG),
//				'DisplayText' => __('LostPassword', GoodByeCaptcha::PLUGIN_SLUG),
//				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
//			),
//
//			self::OPTION_CHANGE_PASS_FORM_PROTECTION_ACTIVATED  => array(
//				'Id'         => 4,
//				'Value'      => NULL,
//				'LabelText'  => __('Protect Change Password Form', GoodByeCaptcha::PLUGIN_SLUG),
//				'DisplayText' => __('ChangePassword', GoodByeCaptcha::PLUGIN_SLUG),
//				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
//			),

		);

		return $arrDefaultSettingOptions;

	}

	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));
		return $arrSettingOptions;
	}

	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{
		echo '<h3>' . __('WP Members General Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		$optionName = $this->getOptionDisplayTextByOptionId($attemptEntity->SectionId);

		$attemptEntity->Notes = (array)maybe_unserialize($attemptEntity->Notes);

		$arrContent = array('table-head-rows' => '', 'table-body-rows' => '');

		if(null === $optionName)
			return $arrContent;

		$tableHeadRows = '';
		$tableBodyRows = '';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th colspan="2">' . sprintf(__("WPMembers Blocked %s Attempt", GoodByeCaptcha::PLUGIN_SLUG), $optionName) . '</th>';
		$tableHeadRows .= '</tr>';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th>' . __('Field', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '<th>' . __('Value', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '</tr>';


		foreach($attemptEntity->Notes as $key => $value)
		{
			$tableBodyRows .='<tr>';
			$tableBodyRows .= '<td>' . self::getBlockedContentDisplayableKey($key) . '</td>';
			$tableBodyRows .= '<td>' . wp_filter_kses(print_r($value, true))  . '</td>';
			$tableBodyRows .='</tr>';
		}

		$arrContent['table-head-rows'] = $tableHeadRows;
		$arrContent['table-body-rows'] = $tableBodyRows;

		return $arrContent;

	}

}