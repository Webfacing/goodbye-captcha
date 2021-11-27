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

final class GdbcBlackListedIpsAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_BLACK_LISTED_IPS     = 'BlackListedIps';

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

			self::OPTION_BLACK_LISTED_IPS => array(
				'Id'         => 1,
				'Value'      => null,
				'LabelText'  => null,
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

		);

		return $arrDefaultSettingOptions;

	}

	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		$arrSettingOptions  = array_map('sanitize_text_field', (array)$arrSettingOptions);
		$arrAlreadySavedIps = (array)$this->getOption(self::OPTION_BLACK_LISTED_IPS);

		$action = 'add';
		if(!empty($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS]) && strpos($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS], 'remove-') !== false)
		{
			$action = 'remove';
			$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = str_replace('remove-', '', $arrSettingOptions[self::OPTION_BLACK_LISTED_IPS]);
		}

		$preparedData = empty($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS]) ? array() : GdbcIPUtils::getFormattedIpRangeForDb($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS]);
		$ipVersion = key($preparedData);

		if('remove' === $action && !empty($preparedData[$ipVersion]))
		{
			$minIpValue = $preparedData[$ipVersion][0];
			$maxIpValue = $preparedData[$ipVersion][1];

			if( isset($arrAlreadySavedIps[$ipVersion][$minIpValue]) && $arrAlreadySavedIps[$ipVersion][$minIpValue] == $maxIpValue)
			{
				$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));
				unset($arrAlreadySavedIps[$ipVersion][$minIpValue]);
			}

			$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;
			return $arrSettingOptions;
		}

		if(empty($preparedData[$ipVersion]) || !is_array($preparedData[$ipVersion]))
		{
			$this->registerErrorMessage(__('Invalid IP/CIDR/Range provided!', GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;

			return $arrSettingOptions;
		}

		if(!in_array($ipVersion, array(MchGdbcIPUtils::IP_VERSION_4, MchGdbcIPUtils::IP_VERSION_6)))
		{
			$this->registerErrorMessage(__('Invalid IP/CIDR/Range provided', GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;

			return $arrSettingOptions;
		}

		if(1 === $preparedData[$ipVersion][1])
		{
			if (GdbcIPUtils::isIpBlackListed($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS]))
			{
				$this->registerErrorMessage(sprintf(__("The IP  %s is already black-listed!", GoodByeCaptcha::PLUGIN_SLUG), esc_html($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS])));
				$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;

				return $arrSettingOptions;
			}
		}

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4] = array();

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6] = array();

		$arrIpRanges = array();
		$arrSingleIPs = array();
		if(1 !== $preparedData[$ipVersion][1])
		{
			foreach($arrAlreadySavedIps[$ipVersion] as $minValue => $maxValue)
			{
				if( 1 === $maxValue )
				{
					$arrSingleIPs[] = $minValue;
					continue;
				}

				if( ($minValue <= $preparedData[$ipVersion][0]) && ($preparedData[$ipVersion][1] <= $maxValue) )
				{
					$this->registerErrorMessage(sprintf(__("The IP Range  %s is already black-listed!", GoodByeCaptcha::PLUGIN_SLUG), esc_html($arrSettingOptions[self::OPTION_BLACK_LISTED_IPS])));
					$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;

					return $arrSettingOptions;
				}

				$arrIpRanges[] = array($minValue, $maxValue);
			}

			$arrIpRanges[] = $preparedData[$ipVersion];
		}

		if(!empty($arrIpRanges)) {
			$arrIpRanges = MchGdbcUtils::overlapIntervals($arrIpRanges);

			$arrAlreadySavedIps[$ipVersion] = array();
			// Add single IPs always on top of Ranges
			foreach($arrSingleIPs as $ipNumber){
				if(!isset($arrAlreadySavedIps[$ipVersion][$ipNumber]))
					$arrAlreadySavedIps[$ipVersion][$ipNumber] = 1;
			}

			foreach($arrIpRanges as $arrRange){
				$arrAlreadySavedIps[$ipVersion][ $arrRange[0] ] = $arrRange[1];
			}
		}
		else
		{
			$arrAlreadySavedIps[$ipVersion][$preparedData[$ipVersion][0]] = $preparedData[$ipVersion][1];
		}


		$arrSettingOptions[self::OPTION_BLACK_LISTED_IPS] = $arrAlreadySavedIps;

		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));

		return $arrSettingOptions;
	}

	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{}

	public function getPartialAdminSettingsFilePath()
	{
		$filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'admin-settings.php';
		return is_file($filePath) ? $filePath : null;
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

	public function registerBlackListedIp($ipAddress)
	{
		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress);

		if($ipVersion !== MchGdbcIPUtils::IP_VERSION_4 && $ipVersion !== MchGdbcIPUtils::IP_VERSION_6)
			return false;

		$arrAlreadySavedIps = $this->getOption(self::OPTION_BLACK_LISTED_IPS);
		(null === $arrAlreadySavedIps) ? $arrAlreadySavedIps = array() : null;

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4] = array();

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6] = array();

		$preparedDataForDb = GdbcIPUtils::getFormattedIpRangeForDb($ipAddress);

		if(!isset($preparedDataForDb[$ipVersion][0]))
			return false;

		if(isset($arrAlreadySavedIps[$ipVersion][ $preparedDataForDb[$ipVersion][0] ])) // single IP
			return true;

		$arrAlreadySavedIps[$ipVersion][$preparedDataForDb[$ipVersion][0]] = $preparedDataForDb[$ipVersion][1];


		return $this->saveOption(self::OPTION_BLACK_LISTED_IPS, $arrAlreadySavedIps);

	}

	public function unRegisterBlackListedIp($ipAddress)
	{
		$ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress);

		if($ipVersion !== MchGdbcIPUtils::IP_VERSION_4 && $ipVersion !== MchGdbcIPUtils::IP_VERSION_6)
			return false;


		$arrAlreadySavedIps = $this->getOption(self::OPTION_BLACK_LISTED_IPS);
		(null === $arrAlreadySavedIps) ? $arrAlreadySavedIps = array() : null;

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_4] = array();

		if(!isset($arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6]))
			$arrAlreadySavedIps[MchGdbcIPUtils::IP_VERSION_6] = array();

		if(empty($arrAlreadySavedIps[$ipVersion]))
			return false;

		$arrFormattedRanges = GdbcIPUtils::removeIpFromFormattedRange($ipAddress, $arrAlreadySavedIps[$ipVersion]);
		if($arrFormattedRanges == $arrAlreadySavedIps[$ipVersion])
			return false;

		$arrAlreadySavedIps[$ipVersion] = $arrFormattedRanges;

		return $this->saveOption(self::OPTION_BLACK_LISTED_IPS, $arrAlreadySavedIps);

	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		return null;
	}


}