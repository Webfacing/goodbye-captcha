<?php

final class GdbcUltraCommunityPublicModule extends GdbcBasePublicModule
{
	
	protected function __construct()
	{
		parent::__construct();
		
		if(!class_exists('\UltraCommunity\UltraCommException')) {
			return;
		}
		
		if($this->getOption(GdbcUltraCommunityAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED))
		{
			$this->registerLoginHooks();
		}
		
		if($this->getOption(GdbcUltraCommunityAdminModule::OPTION_REGISTER_FORM_PROTECTION_ACTIVATED))
		{
			$this->registerRegistrationHooks();
		}

//		if($this->getOption(GdbcUserProAdminModule::OPTION_LOST_PASS_FORM_PROTECTION_ACTIVATED))
//		{
//			$this->registerLostPasswordHooks();
//		}
//
//
//		if($this->getOption(GdbcUserProAdminModule::OPTION_CHANGE_PASS_FORM_PROTECTION_ACTIVATED))
//		{
//			$this->registerChangePasswordHooks();
//		}
	}
	
	private function registerLoginHooks()
	{
		add_action('uc_action_login_form_bottom', array($this, 'renderTokenFieldIntoForm'));
		add_action('uc_action_before_user_log_in', array($this, 'validateLogin'), 10, 1);
		
	}
	
	
	private function registerRegistrationHooks()
	{
		add_action('uc_action_registration_form_bottom', array($this, 'renderTokenFieldIntoForm'));
		add_filter('uc_action_before_user_registration', array($this, 'validateRegistration'));
	}
	
	
	public function validateLogin($userName)
	{
		
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcUltraCommunityAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED);
		$this->attemptEntity->Notes = array('username' => $userName);
		$this->attemptEntity->Notes = array_map( is_email($this->attemptEntity->Notes['username']) ? 'sanitize_email' : 'sanitize_user' , $this->attemptEntity->Notes);
		
		if(GdbcRequestController::isValid($this->attemptEntity))
			return;
		
		throw new  \UltraCommunity\UltraCommException(__('Invalid Username or Password!', GoodByeCaptcha::PLUGIN_SLUG));
		
	}
	
	public function validateRegistration($userEntity)
	{
		
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcUltraCommunityAdminModule::OPTION_REGISTER_FORM_PROTECTION_ACTIVATED);
		
		if(GdbcRequestController::isValid($this->attemptEntity))
			return;
		
		throw new  \UltraCommunity\UltraCommException(__('We\'ve encountered an error while processing your request!', GoodByeCaptcha::PLUGIN_SLUG));
		
	}
	
	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_ULTRA_COMMUNITY);
	}
	
	
	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}
	
}
