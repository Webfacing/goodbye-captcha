<?php

/*
 * Copyright (C) 2016 Mihai Chelaru
 *
 */

final class GdbcProxyHeadersAdminModule extends GdbcBaseAdminModule
{

	CONST PROXY_HEADERS_IP  = 'IpProxyHeaders';

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

			self::PROXY_HEADERS_IP => array(
					'Value'      => array(),
					'LabelText'  => null,
					'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_SELECT
			),

		);

		return $arrDefaultSettingOptions;

	}

	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		$arrAlreadySavedHeaders = (array)$this->getOption(self::PROXY_HEADERS_IP);

		if(empty($arrSettingOptions[self::PROXY_HEADERS_IP])){
			$this->registerErrorMessage(__('Invalid Proxy Header Received!', GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::PROXY_HEADERS_IP] = $arrAlreadySavedHeaders;
			return $arrSettingOptions;
		}


		if(MchGdbcUtils::stringStartsWith($arrSettingOptions[self::PROXY_HEADERS_IP], 'remove-'))
		{
			$proxyHeader = str_replace('remove-', '', $arrSettingOptions[self::PROXY_HEADERS_IP]);

			if(($key = array_search($proxyHeader, $arrAlreadySavedHeaders)) !== false) {
				unset($arrAlreadySavedHeaders[$key]);
			}

			$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::PROXY_HEADERS_IP] = array_values($arrAlreadySavedHeaders);
			return $arrSettingOptions;

		}


		if(!in_array($arrSettingOptions[self::PROXY_HEADERS_IP], MchGdbcHttpRequest::getDetectedProxyHeaders())) {
			$this->registerErrorMessage(__('Invalid Proxy Header Received!', GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::PROXY_HEADERS_IP] = $arrAlreadySavedHeaders;
			return $arrSettingOptions;
		}


		if(empty($arrAlreadySavedHeaders) || !is_array($arrAlreadySavedHeaders))
			$arrAlreadySavedHeaders = array();


		$arrSettingOptions[self::PROXY_HEADERS_IP] = sanitize_text_field($arrSettingOptions[self::PROXY_HEADERS_IP]);

		if (in_array($arrSettingOptions[self::PROXY_HEADERS_IP], $arrAlreadySavedHeaders))
		{
			$this->registerErrorMessage(__("The {$arrSettingOptions[self::PROXY_HEADERS_IP]} proxy header is already registered!" , GoodByeCaptcha::PLUGIN_SLUG));
			$arrSettingOptions[self::PROXY_HEADERS_IP] = $arrAlreadySavedHeaders;
			return $arrSettingOptions;
		}


		$arrAlreadySavedHeaders = $arrSettingOptions[self::PROXY_HEADERS_IP];


		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));

		$arrSettingOptions[self::PROXY_HEADERS_IP] = $arrAlreadySavedHeaders;
		return $arrSettingOptions;

	}

	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
	{
		//echo '<h3>' . __('Geo IP - Block Country General Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3>';
	}

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

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		return null;
	}
}