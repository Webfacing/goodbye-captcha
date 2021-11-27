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

final class GdbcWPMembersPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

		if($this->getOption(GdbcWPMembersAdminModule::OPTION_LOGIN_FORM_PROTECTION_ACTIVATED))
		{
			$this->registerLoginHooks();
		}

		if($this->getOption(GdbcWPMembersAdminModule::OPTION_REGISTER_FORM_PROTECTION_ACTIVATED))
		{
			$this->registerRegistrationHooks();
		}

	}

	private function registerLoginHooks()
	{
		add_filter('wpmem_login_hidden_fields', array($this, 'renderTokenField'), 10, 2);
		add_filter('authenticate',  array(GdbcWordPressPublicModule::getInstance(), 'validateLoginAuthentication'), 95, 3);
	}


	private function registerRegistrationHooks()
	{
		add_action('wpmem_register_hidden_fields', array($this, 'renderTokenField'), 10, 2);
		add_action('wpmem_pre_register_data', array($this, 'validateRegistration'), 10, 1);
	}


	public function validateRegistration($submittedForm)
	{

		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcWPMembersAdminModule::OPTION_REGISTER_FORM_PROTECTION_ACTIVATED);
		$this->attemptEntity->Notes = array(
			'username' => !empty($submittedForm['username']) ? sanitize_user($submittedForm['username']) : null,
			'email'    => !empty($submittedForm['user_email']) ? sanitize_email($submittedForm['user_email']): null,
		);

		if(GdbcRequestController::isValid($this->attemptEntity))
			return;

		global $wpmem_themsg;

		$wpmem_themsg = __('An error occurred while processing your request!', GoodByeCaptcha::PLUGIN_SLUG);

	}



	public function renderTokenField($hiddenFields, $action)
	{

		if(!in_array($action, array('login', 'new'))) // login for login form and new for registration
			return $hiddenFields;

		if('login' === $action)
		{
			if(GdbcWordPressPublicModule::getInstance()->getOption(GdbcWordPressAdminModule::WORDPRESS_LOGIN_FORM))
				return $hiddenFields;
		}


		return $hiddenFields . $this->getTokenFieldHtml();

	}


	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_WP_MEMBERS);
	}


	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}

}
