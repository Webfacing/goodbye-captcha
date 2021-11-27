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

class GdbcBruteForceAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_AUTO_BLOCK_IP             = 'AutoBlockIp';
	CONST OPTION_PREVENT_USER_ENUM         = 'PreventUserEnum';
	CONST OPTION_BLOCK_ANONYMOUS_PROXY     = 'AnonymousProxy';
	CONST OPTION_BLOCK_WEB_ATTACKERS       = 'WebAttackers';

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

			self::OPTION_AUTO_BLOCK_IP => array(
				'Id'          => 1,
				'Value'       => NULL,
				'LabelText'   => __('Automatically Block IP Addresses', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Automatically block IP Addresses that are brute-forcing your system', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_PREVENT_USER_ENUM => array(
				'Id'          => 2,
				'Value'       => true,
				'LabelText'   => __('Prevent User Enumeration', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Prevents bots from enumerating users through \'/?author=N\' scans, the oEmbed API, and the WordPress REST API', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('UserEnumeration', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),


			self::OPTION_BLOCK_WEB_ATTACKERS => array(
				'Id'          => 3,
				'Value'       => NULL,
				'LabelText'   => __('Block Web Attackers IPs', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Blocks most dangerous IP addresses involved in brute force attacks, cross-site scripting or SQL injection', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Attacker', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::OPTION_BLOCK_ANONYMOUS_PROXY => array(
				'Id'          => 4,
				'Value'       => NULL,
				'LabelText'   => __('Block Anonymous Proxy IPs', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Blocks most dangerous IP addresses associated with web proxies that shield the originator\'s IP address', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Anonymizer', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

		);

		return $arrDefaultSettingOptions;

	}

	public  function renderModuleSettingsField(array $arrSettingsField)
	{

		$optionName = key($arrSettingsField);
		if(null === $optionName || !array_key_exists($optionName, $this->getDefaultOptionsValues()))
			return;

		parent::renderModuleSettingsField($arrSettingsField);
	}


	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		$arrSettingOptions = array_map('sanitize_text_field', (array)$arrSettingOptions);

		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));

		return $arrSettingOptions;

	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		$attemptEntity->Notes = (array)maybe_unserialize($attemptEntity->Notes);
		$arrContent           = array('table-head-rows' => '', 'table-body-rows' => '');

		$section = null;
		switch($this->getOptionNameByOptionId($attemptEntity->SectionId))
		{
			case self::OPTION_PREVENT_USER_ENUM :
				$section = __('User Enumeration', GoodByeCaptcha::PLUGIN_SLUG);
				break;

			default:
				$section = __('Brute Force', GoodByeCaptcha::PLUGIN_SLUG);
				break;

		}

		$tableHeadRows = '';
		$tableBodyRows = '';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th colspan="2">' . sprintf(__("%s - Blocked Attempt", GoodByeCaptcha::PLUGIN_SLUG), $section) . '</th>';
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

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}


}