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

final class GdbcZmAlrAdminModule extends GdbcBaseAdminModule
{

	CONST OPTION_ZM_ALR_LOGIN_FORM         = 'IsLoginActivated';
	CONST OPTION_ZM_ALR_REGISTER_FORM      = 'IsRegisterActivated';

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

			self::OPTION_ZM_ALR_LOGIN_FORM    => array(
				'Id'          => 1,
				'Value'       => NULL,
				'LabelText'   => __('Protect Login Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Login', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_ZM_ALR_REGISTER_FORM  => array(
				'Id'          => 2,
				'Value'       => NULL,
				'LabelText'   => __('Protect Register Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Register', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

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
		echo '<h3>' . __('ZM Ajax Login Register General Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3>';
	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		print_r($attemptEntity);exit;
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}