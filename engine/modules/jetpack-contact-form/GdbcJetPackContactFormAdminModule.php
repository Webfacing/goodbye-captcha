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

final class GdbcJetPackContactFormAdminModule  extends GdbcBaseAdminModule
{
	CONST OPTION_IS_JETPACK_CONTACT_FORM_ACTIVATE  = 'IsJPCFActivated';

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

			self::OPTION_IS_JETPACK_CONTACT_FORM_ACTIVATE => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText'  => __('Jetpack Contact Form', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

		);

		return $arrDefaultSettingOptions;

	}


	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		return $arrSettingOptions;
	}

	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{
//		echo '<h3>' . __('Popular Contact Forms Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
//		echo '<h4>' . __('Enable protection for the following popular contact forms:', GoodByeCaptcha::PLUGIN_SLUG) . '</h4>';
	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		$attemptEntity->Notes = (array)maybe_unserialize($attemptEntity->Notes);
		$arrContent           = array('table-head-rows' => '', 'table-body-rows' => '');
		$formTitle            = isset($attemptEntity->Notes['form-title']) ? $attemptEntity->Notes['form-title'] : '';

		unset($attemptEntity->Notes['form-title']);

		$tableHeadRows = '';
		$tableBodyRows = '';

		$tableHeadRows .= '<tr>';
		$tableHeadRows .= '<th colspan="2">' . sprintf(__("%s - Blocked Attempt", GoodByeCaptcha::PLUGIN_SLUG), $formTitle) . '</th>';
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