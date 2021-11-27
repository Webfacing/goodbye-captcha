<?php

final class GdbcHtmlFormsAdminModule extends GdbcBaseAdminModule
{
	CONST OPTION_IS_HTML_FORMS_ACTIVATED = 'IsHTMLFormsActivated';

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

			self::OPTION_IS_HTML_FORMS_ACTIVATED => array(
				'Id'         => 1,
				'Value'      => NULL,
				'LabelText'  => __('HTML Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'  => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX,
				'Description' => __('Protects all your forms built with <a target="_blank" href="https://wordpress.org/plugins/html-forms/">HTML Forms Plugin</a>', GoodByeCaptcha::PLUGIN_SLUG),
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
		echo '<h3>' . __('Popular Contact Forms Settings', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
		echo '<h4>' . __('Enable protection for the following popular contact forms:', GoodByeCaptcha::PLUGIN_SLUG) . '</h4>';
	}

	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		return array();
	}


	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}
}