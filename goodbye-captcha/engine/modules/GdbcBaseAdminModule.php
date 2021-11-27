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

abstract class GdbcBaseAdminModule extends MchGdbcBaseAdminModule
{

	protected function __construct()
	{
		parent::__construct();
	}

	public abstract function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity);

	protected function getAllSavedOptions($asNetworkOption = true)
	{
		return parent::getAllSavedOptions(GoodByeCaptcha::isNetworkActivated());
	}

	public function getOption($optionName, $asNetworkOption = true)
	{
		return parent::getOption($optionName, GoodByeCaptcha::isNetworkActivated());
	}

	public function saveOption($optionName, $optionValue, $asNetworkOption = true)
	{
		return parent::saveOption($optionName, $optionValue,  GoodByeCaptcha::isNetworkActivated());
	}

	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{
		//echo '<h3>' . __('WordPress General Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
	}

	public function renderModuleSettingsField(array $arrSettingsField)
	{
		$arrDefaultValues = $this->getDefaultOptionsValues();
		$optionName = key($arrSettingsField);
		if(null === $optionName || !array_key_exists($optionName, $arrDefaultValues))
			return;

		$optionValue = $this->getOption($optionName);
//		if(null === $optionValue && isset($arrDefaultValues[$optionName]))
//		{
//			if(!is_array($arrDefaultValues[$optionName])) {
//				$optionValue = $arrDefaultValues[$optionName];
//			}
//		}

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


		switch ($arrFieldAttributes['type'])
		{
			case MchGdbcHtmlUtils::FORM_ELEMENT_SELECT :

				echo MchGdbcHtmlUtils::createSelectElement($arrFieldAttributes);

				break;

			default :

				echo MchGdbcHtmlUtils::createInputElement($arrFieldAttributes);
		}

		if(!empty($arrSettingsField['Description']))
		{
			echo '<p class = "description">' . $arrSettingsField['Description'] . '</p>';
		}

	}

	protected function getFormattedFieldDescription($description)
	{
		return  '<p class = "description">' . esc_html( $description );  '</p>';
	}


	public function getOptionDisplayTextByOptionId($settingOptionId)
	{
		$settingOptionId = (int)$settingOptionId;

		foreach($this->getDefaultOptions() as $arrOptionInfo)
		{
			if (isset($arrOptionInfo['Id']) &&  $arrOptionInfo['Id'] === $settingOptionId && isset($arrOptionInfo['DisplayText']))
				return esc_html($arrOptionInfo['DisplayText']);
		}

		return null;
	}

	public function getOptionIdByOptionName($settingOptionName)
	{
		$arrDefaultSettingOptions = $this->getDefaultOptions();

		return isset($arrDefaultSettingOptions[$settingOptionName]['Id']) ? $arrDefaultSettingOptions[$settingOptionName]['Id'] : 0;
	}

	public function getOptionNameByOptionId($settingOptionId)
	{
		$settingOptionId = (int)$settingOptionId;
		foreach((array)$this->getDefaultOptions() as $optionName => $arrOptionInfo)
		{
			if (isset($arrOptionInfo['Id']) &&  (int)$arrOptionInfo['Id'] === $settingOptionId)
				return $optionName;
		}

		return null;
	}

	protected static function getBlockedContentDisplayableKey($blockedContentKey)
	{
		$arrMappedKeys = array(

			'username'             => __('Username', GoodByeCaptcha::PLUGIN_SLUG),
			'password'             => __('Password', GoodByeCaptcha::PLUGIN_SLUG),
			'email'                => __('Email', GoodByeCaptcha::PLUGIN_SLUG),
			'post'                 => __('Post', GoodByeCaptcha::PLUGIN_SLUG),
			'page'                 => __('Page', GoodByeCaptcha::PLUGIN_SLUG),

			'comment_post_ID'      => __('Post Id', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_author'       => __('Author', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_author_email' => __('Email', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_author_url'   => __('Website', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_content'      => __('Content', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_parent'       => __('As Reply to', GoodByeCaptcha::PLUGIN_SLUG),
			'comment_agent'        => __('Browser', GoodByeCaptcha::PLUGIN_SLUG),

			'your-name'    => __('Name', GoodByeCaptcha::PLUGIN_SLUG),
			'your-email'   => __('Email', GoodByeCaptcha::PLUGIN_SLUG),
			'your-subject' => __('Subject', GoodByeCaptcha::PLUGIN_SLUG),
			'your-message' => __('Message', GoodByeCaptcha::PLUGIN_SLUG),

//			'page-url' => __('Page URL', GoodByeCaptcha::PLUGIN_SLUG),
//			'post-url' => __('Post URL', GoodByeCaptcha::PLUGIN_SLUG),

			'page-url' => __('URL', GoodByeCaptcha::PLUGIN_SLUG),
			'post-url' => __('URL', GoodByeCaptcha::PLUGIN_SLUG),


			'ADDRESS'   => __('Address', GoodByeCaptcha::PLUGIN_SLUG),
			'FNAME'     => __('First Name', GoodByeCaptcha::PLUGIN_SLUG),
			'LNAME'     => __('Last Name', GoodByeCaptcha::PLUGIN_SLUG),
			'NNAME'     => __('Nick Name', GoodByeCaptcha::PLUGIN_SLUG),
			'GROUPINGS' => __('Group', GoodByeCaptcha::PLUGIN_SLUG),
			'EMAIL'     => __('Email', GoodByeCaptcha::PLUGIN_SLUG),


			'honoreestate' => __('State', GoodByeCaptcha::PLUGIN_SLUG),
			'honoreeprovince' => __('Province', GoodByeCaptcha::PLUGIN_SLUG),
			'honoreecountry' => __('Country', GoodByeCaptcha::PLUGIN_SLUG),

			'firstname' => __('First Name', GoodByeCaptcha::PLUGIN_SLUG),
			'lastname' => __('Last Name', GoodByeCaptcha::PLUGIN_SLUG),
			'phone' => __('Phone', GoodByeCaptcha::PLUGIN_SLUG),


			'country' => __('Country', GoodByeCaptcha::PLUGIN_SLUG),
			'coupon' => __('Coupon', GoodByeCaptcha::PLUGIN_SLUG),
			'item_name' => __('Item', GoodByeCaptcha::PLUGIN_SLUG),
			'amount' => __('Amount', GoodByeCaptcha::PLUGIN_SLUG),
			'paymentmethod' => __('Method', GoodByeCaptcha::PLUGIN_SLUG),


			'secretkey' => __('Secret key', GoodByeCaptcha::PLUGIN_SLUG),
			'authorid'  => __('Author Id', GoodByeCaptcha::PLUGIN_SLUG),
		);

		$blockedKey = trim($blockedContentKey);
		return isset($arrMappedKeys[$blockedKey]) ? $arrMappedKeys[$blockedKey] : esc_html($blockedContentKey);
	}


	public function getFormElementName($optionName)
	{
		if(!array_key_exists($optionName, (array)$this->getDefaultOptions()))
			return null;

		return esc_attr($this->getSettingKey() . '[' . $optionName . ']');
	}
}