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

final class GdbcZmAlrPublicModule extends GdbcBasePublicModule
{
	private $arrStatusLoginError    = null;
	private $arrStatusRegisterError = null;

	private $arrCapturedData = null;
	protected function __construct()
	{
		parent::__construct();

		if(!GoodByeCaptchaUtils::isZmAlrActivated())
			return;

		$this->arrCapturedData = array();

		$this->arrStatusLoginError = array('gdbc-login-error' => array(
			'description' => __('Invalid username or password!', GoodByeCaptcha::PLUGIN_SLUG),
			'cssClass'    => 'error-container',
			'code'        => 'show_notice'
		));

		$this->arrStatusRegisterError = array('gdbc-register-error' => array(
			'description' => __('An error occurred while registering your account!', GoodByeCaptcha::PLUGIN_SLUG),
			'cssClass'    => 'error-container',
			'code'        => 'show_notice'
		));


		if($this->getOption(GdbcZmAlrAdminModule::OPTION_ZM_ALR_LOGIN_FORM)){
			$this->registerLoginHooks();
		}

		if($this->getOption(GdbcZmAlrAdminModule::OPTION_ZM_ALR_REGISTER_FORM)){
			$this->registerRegistrationHooks();
		}

		add_filter('zm_alr_status_codes', array($this, 'registerGdbcStatusCode'), 10, 1);
	}


	public function registerGdbcStatusCode($arrStatusCode)
	{
		$arrStatusCode = (array)$arrStatusCode;
		$arrStatusCode[key($this->arrStatusLoginError)]    = reset($this->arrStatusLoginError);
		$arrStatusCode[key($this->arrStatusRegisterError)] = reset($this->arrStatusRegisterError);

		return $arrStatusCode;
	}

	private function registerLoginHooks()
	{
		$this->addFilterHook('zm_alr_login_above_fields', array($this, 'renderHiddenFieldIntoForm'), 99, 1);
		$this->addFilterHook('zm_alr_login_form_params' , array($this, 'captureSubmittedData'), 1, 1);

		$this->addFilterHook('zm_alr_login_submit_pre_status_error' , array($this, 'validateLoginRequest'), 1, 1);

	}

	public function registerRegistrationHooks()
	{
		$this->addFilterHook('zm_alr_register_above_fields', array($this, 'renderHiddenFieldIntoForm'), 99, 1);
		$this->addFilterHook('zm_alr_register_setup_new_user_args' , array($this, 'captureSubmittedData'), 1, 1);

		$this->addFilterHook('zm_alr_register_submit_pre_status_error' , array($this, 'validateRegisterRequest'), 1, 1);

	}

	public function validateLoginRequest($preStatus)
	{
		$this->attemptEntity->Notes     = $this->arrCapturedData;
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcZmAlrAdminModule::OPTION_ZM_ALR_LOGIN_FORM);


		if(GdbcRequestController::isValid($this->attemptEntity))
			return $preStatus;

		reset($this->arrStatusLoginError);
		return key($this->arrStatusLoginError);
	}

	public function validateRegisterRequest($preStatus)
	{
		$this->attemptEntity->Notes = $this->arrCapturedData;
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcZmAlrAdminModule::OPTION_ZM_ALR_REGISTER_FORM);

		if(GdbcRequestController::isValid($this->attemptEntity))
			return $preStatus;

		reset($this->arrStatusRegisterError);
		return key($this->arrStatusRegisterError);
	}


	public function captureSubmittedData($arrSubmittedData)
	{
		$this->arrCapturedData['username'] = isset($arrSubmittedData['user_login']) ? sanitize_user($arrSubmittedData['user_login']) : null;
		$this->arrCapturedData['email']    = isset($arrSubmittedData['email'])      ? sanitize_email($arrSubmittedData['email'])     : null;
		if(null === $this->arrCapturedData['email'])
			unset($this->arrCapturedData['email']);

		return $arrSubmittedData;
	}

	public function renderHiddenFieldIntoForm($aboveFieldsHtml)
	{
		$aboveFieldsHtml .= $this->getTokenFieldHtml();

		if( ! MchGdbcWpUtils::isAjaxRequest() )
			return $aboveFieldsHtml;

		//return '<script type="text/javascript">(new jQuery.GdbcClient()).requestTokens();</script>' . $aboveFieldsHtml;

		return GdbcBasePublicModule::getRefreshTokensScriptFileContent(true) . $aboveFieldsHtml;

	}


	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_ZM_ALR);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}
