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

final class GdbcUltimateMemberPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

//		if(!GoodByeCaptchaUtils::isUltimateMemberActivated())
//			return;

		if($this->getOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOGIN_FORM)){
			$this->registerLoginHooks();
		}

		if($this->getOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_REGISTER_FORM)){
			$this->registerRegistrationHooks();
		}

		if($this->getOption(GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOST_PASSWORD_FORM)){
			$this->registerLostPasswordHooks();
		}
	}

	private function registerLoginHooks()
	{
		add_action('um_submit_form_errors_hook_login', array($this, 'validateFormEncryptedToken'), 1);
		add_action('um_after_login_fields', array($this, 'renderHiddenFieldIntoForm'));

		if(MchGdbcWpUtils::isAjaxRequest()){
			add_action('um_after_form', array($this, 'renderRefreshTokensScript'), 10, 1);
		}

	}

	public function renderRefreshTokensScript($arrRequestInfo)
	{
		if((empty($arrRequestInfo['mode'])  ||  'login' !== $arrRequestInfo['mode']))
			return;

		echo GdbcBasePublicModule::getRefreshTokensScriptFileContent(true);
	}

	public function registerRegistrationHooks()
	{
		add_action('um_submit_form_errors_hook__registration', array($this, 'validateFormEncryptedToken'), 1);
		add_action('um_after_register_fields', array($this, 'renderHiddenFieldIntoForm'));
	}

	public function registerLostPasswordHooks()
	{
		add_action('um_reset_password_page_hidden_fields', array($this, 'renderHiddenFieldIntoForm'), 10);
		add_action('um_reset_password_errors_hook',  array($this, 'validateFormEncryptedToken'), 1);
	}

	public function renderHiddenFieldIntoForm()
	{
		echo $this->getTokenFieldHtml();
	}

	public function validateFormEncryptedToken($arrRequestInfo)
	{
		if(MchGdbcWpUtils::isUserLoggedIn())
			return;

		$umSection = !empty($arrRequestInfo['_um_password_reset']) ?  GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOST_PASSWORD_FORM : null;
		if(null === $umSection && !empty($arrRequestInfo['mode']))
		{
			('login' === $arrRequestInfo['mode']) ? $umSection = GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOGIN_FORM : ('register' === $arrRequestInfo['mode'] ? $umSection =  GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_REGISTER_FORM : null);
		}

		global $ultimatemember;

		if(null === $umSection)
		{
			wp_redirect(esc_url(add_query_arg('err', '1')));
			exit;
		}

		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName($umSection);

		$arrSubmittedData = array();
		if(!empty($arrRequestInfo['custom_fields']) && is_serialized($arrRequestInfo['custom_fields']))
		{
			$arrFields = (array)maybe_unserialize($arrRequestInfo['custom_fields']);
			unset($arrRequestInfo['custom_fields']);

			foreach((array)$arrRequestInfo as $formFieldName => $value)
			{
				if(!isset($arrFields[$formFieldName]['label']))
					continue;
				if(isset($arrFields[$formFieldName]['type']) && $arrFields[$formFieldName]['type'] === 'password')
					continue;

				$arrSubmittedData[$arrFields[$formFieldName]['label']] = $value;
			}
		}

		if(isset($_POST['username_b']) && $umSection === GdbcUltimateMemberAdminModule::OPTION_ULTIMATE_MEMBER_LOST_PASSWORD_FORM)
		{
			is_email($_POST['username_b']) ?
				$arrSubmittedData['email'] = sanitize_email($_POST['username_b']) :
				$arrSubmittedData['username'] = sanitize_user($_POST['username_b']);
		}

		$this->getAttemptEntity()->Notes = $arrSubmittedData;

		if(GdbcRequestController::isValid($this->attemptEntity))
			return;

		wp_redirect(esc_url(add_query_arg('err', '1')));
		exit;
	}


	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_ULTIMATE_MEMBER);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}
