<?php

final class GdbcHtmlFormsPublicModule extends GdbcBasePublicModule
{
	protected function __construct()
	{
		parent::__construct();
		
		if(!$this->getOption(GdbcHtmlFormsAdminModule::OPTION_IS_HTML_FORMS_ACTIVATED))
			return;
		
		$this->activateHtmlFormsHooks();
		
	}
	
	public function activateHtmlFormsHooks()
	{
		add_filter('hf_form_html', array($this, 'injectTokenField'), 1, 1);
		
		add_filter('hf_form_message_blocked_by_gdbc', array($this, 'getErrorMessage'), 10, 1 );
		
		add_filter('hf_validate_form_request_size', '__return_false');
		
		add_filter('hf_validate_form', array($this, 'validateFormsRequest'), PHP_INT_MAX, 1 );
	}
	
	public function getErrorMessage($errorMessage)
	{
		return $errorMessage = __('Your submission seems to be a spam!', GoodByeCaptcha::PLUGIN_SLUG);
	}
	
	public function injectTokenField($formOutputContent)
	{
		return str_replace('</form>', $this->getTokenFieldHtml() . '</form>', $formOutputContent);
	}
	
	
	public function validateFormsRequest($errorCode)
	{
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcHtmlFormsAdminModule::OPTION_IS_HTML_FORMS_ACTIVATED);
		
		return GdbcRequestController::isValid($this->attemptEntity) ? null : 'blocked_by_gdbc';
	}
	
	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_HTML_FORMS);
	}
	
	
	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}
	
	
}