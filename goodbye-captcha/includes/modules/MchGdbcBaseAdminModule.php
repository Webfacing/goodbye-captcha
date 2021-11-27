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

abstract class MchGdbcBaseAdminModule extends MchGdbcBaseModule
{
	private $arrDefaultOptionsValues = array();
	private $arrRegisteredMessages   = array();


	public abstract function getDefaultOptions();
	public abstract function validateModuleSettingsFields($arrOptions);

	protected function __construct()
	{
		parent::__construct();

	}

	public function getSettingKey()
	{
		return $this->moduleSettingsKey;
	}

	public function getDefaultOptionsValues()
	{
		if(empty($this->arrDefaultOptionsValues))
		{
			foreach((array)$this->getDefaultOptions() as $optionName => $arrOptionInfo)
			{
				$this->arrDefaultOptionsValues[$optionName] = isset($arrOptionInfo['Value']) ? $arrOptionInfo['Value'] : null;
			}
		}

		return $this->arrDefaultOptionsValues;
	}

	public function saveNetworkSettingOptions(array $arrSettingOptions)
	{
		$this->isUsedNetworkWide = true;
		$arrSettingOptions = $this->validateModuleSettingsFields($arrSettingOptions);

		remove_filter('sanitize_option_' . $this->getSettingKey(), array($this, 'validateModuleSettingsFields'));

		update_site_option($this->getSettingKey(), $arrSettingOptions);

		wp_safe_redirect(add_query_arg('updated', '1'));
	}

	public function saveOption($optionName, $optionValue, $asNetworkOption)
	{
		$this->isUsedNetworkWide = !!$asNetworkOption;
		$arrSavedOptions = $this->getAllSavedOptions($asNetworkOption);

		$arrSavedOptions[$optionName] = $optionValue;

		return ($this->isUsedNetworkWide) ? update_site_option($this->moduleSettingsKey, $arrSavedOptions) : update_option($this->moduleSettingsKey, $arrSavedOptions);
	}

	public function deleteOption($optionName, $asNetworkOption)
	{
		$this->isUsedNetworkWide = !!$asNetworkOption;
		$arrSavedOptions = $this->getAllSavedOptions($this->isUsedNetworkWide);

		unset($arrSavedOptions[$optionName]);

		return ($this->isUsedNetworkWide) ? update_site_option($this->moduleSettingsKey, $arrSavedOptions) : update_option($this->moduleSettingsKey, $arrSavedOptions);

	}

	public function deleteAllSettingOptions($asNetworkOption)
	{
		$this->isUsedNetworkWide = !!$asNetworkOption;
		return ($this->isUsedNetworkWide) ? delete_site_option($this->moduleSettingsKey) : delete_option($this->moduleSettingsKey);
	}

	protected function registerErrorMessage($messageToDisplay)
	{
		$this->registerAdminMessage('ErrorMessage', $messageToDisplay);
		//add_settings_error($this->getSettingKey(), $this->getSettingKey(), $messageToDisplay, 'error');
	}

	protected function registerSuccessMessage($messageToDisplay)
	{
		$this->registerAdminMessage('SuccessMessage', $messageToDisplay);

		//add_settings_error($this->getSettingKey(), $this->getSettingKey(), $messageToDisplay, 'updated');
	}

	protected function registerWarningMessage($messageToDisplay)
	{
		$this->registerAdminMessage('WarningMessage', $messageToDisplay);
	}

	private function registerAdminMessage($messageType, $message)
	{
		$this->arrRegisteredMessages[$messageType] = $message;
	}

	public function getFormattedMessagesForDisplay()
	{
		$arrSavedOptions = $this->getAllSavedOptions($this->isUsedNetworkWide);


		$htmlCode = '<div class = "mch-settings-message" style = "{holder-style}"><h3 style = "margin-bottom: 5px;">{message}</h3></div>';

		$arrMessageType = array(
			'ErrorMessage'   => array(
									'border-left:' => '4px solid #ce4844',
									'background:'  => '#f2dede'
								),

			'SuccessMessage' => array(
									'border-left:' => '4px solid #7ad03a',
									'background:'    => '#dff0d8'
								),

			'WarningMessage' => array(
									'border-left:' => '4px solid #ffba00',
									'background:'    => '#fcf8e3'
								),
		);

		foreach($arrMessageType as $messageType => $arrStyleInfo)
		{
			if(empty($arrSavedOptions[$messageType]))
				continue;

			$holderStyle = '';
			foreach($arrStyleInfo as $styleKey => $value)
				$holderStyle .= $styleKey . $value . ';';

			$htmlCode = str_replace(array('{holder-style}', '{message}'), array($holderStyle, wp_filter_kses($arrSavedOptions[$messageType])), $htmlCode);
			return $htmlCode;
		}

		return null;

	}

	public function saveRegisteredAdminMessages()
	{

		$arrSavedOptions = $this->getAllSavedOptions($this->isUsedNetworkWide);
		$shouldUpdateOptions = !empty($this->arrRegisteredMessages);

		foreach(array('ErrorMessage', 'SuccessMessage', 'WarningMessage') as $messageType)
		{
			$shouldUpdateOptions = (true === $shouldUpdateOptions) ? true : isset($arrSavedOptions[$messageType]);
			unset($arrSavedOptions[$messageType]);
		}

		if(!$shouldUpdateOptions)
			return;

		foreach($this->arrRegisteredMessages as $messageType => $message)
		{
			$arrSavedOptions[$messageType] = $message;
		}

		remove_filter('sanitize_option_' . $this->getSettingKey(), array($this, 'validateModuleSettingsFields'));

		$this->isUsedNetworkWide ? update_site_option($this->moduleSettingsKey, $arrSavedOptions) : update_option($this->moduleSettingsKey, $arrSavedOptions);
	}

}