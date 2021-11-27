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

class GdbcMailChimpForWpAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_MODULE_MAIL_CHIMP_FOR_WP       = 'IsMC4WPActivated';

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

			self::OPTION_MODULE_MAIL_CHIMP_FOR_WP => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText'  => __('MailChimp for WordPress', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
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
		echo '<h3>' . __('Popular Subscriptions Plugins Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
		echo '<h4>' . __('Enable protection for the following popular subscriptions plugins:', GoodByeCaptcha::PLUGIN_SLUG) . '</h4>';

	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{

		$attemptEntity->Notes = (array)maybe_unserialize($attemptEntity->Notes);

		$arrContent = array('table-head-rows' => '', 'table-body-rows' => '');


		$tableHeadRows = '';
		$tableBodyRows = '';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th colspan="2">' . __("MailChimp for WP Blocked Subscription", GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '</tr>';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th>' . __('Field', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '<th>' . __('Value', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '</tr>';

		if(isset($attemptEntity->Notes['ADDRESS']) && is_array($attemptEntity->Notes['ADDRESS']))
		{
			$attemptEntity->Notes['ADDRESS']  = implode(', ', array_values($attemptEntity->Notes['ADDRESS']));
		}


		foreach($attemptEntity->Notes as $key => $value)
		{

			if($key === 'GROUPINGS' && is_array($value))
			{
				$groupId = key($value);
				$tableBodyRows .='<tr>';
					$tableBodyRows .= '<td>' . self::getBlockedContentDisplayableKey($key) . ' ' . $groupId . '</td>';
					$tableBodyRows .= '<td>' . wp_filter_kses(implode(', ', array_values($value[$groupId]) ))  . '</td>';
				$tableBodyRows .='</tr>';

				continue;
			}

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