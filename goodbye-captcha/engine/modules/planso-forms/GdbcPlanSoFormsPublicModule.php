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

final class GdbcPlanSoFormsPublicModule  extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

		if( ! GoodByeCaptchaUtils::isPlanSoFormsActivated() )
			return;

		if( !$this->getOption(GdbcPlanSoFormsAdminModule::OPTION_PLANSO_GENERAL_FORM) && !$this->getOption(GdbcPlanSoFormsAdminModule::OPTION_PLANSO_LOGIN_FORM) &&  !$this->getOption(GdbcPlanSoFormsAdminModule::OPTION_PLANSO_REGISTER_FORM) && !$this->getOption(GdbcPlanSoFormsAdminModule::OPTION_PLANSO_PAYPAL_FORM) )
			return;

		$this->activatePlanSoFormsHooks();

	}

	public function activatePlanSoFormsHooks()
	{

		add_filter('psfb_form_after_hidden_fields', array($this, 'renderHiddenFieldIntoPlanSoForms'), 10, 1);
		add_filter('psfb_validate_form_request', array($this, 'validatePlanSoFormsRequest'), 10, 2);

	}

	public function renderHiddenFieldIntoPlanSoForms(array $arrFormInfo)
	{
		$arrFormInfo['out']  = !isset($arrFormInfo['out']) ? '' : (string)$arrFormInfo['out'];
		$arrFormInfo['out'] .= $this->getTokenFieldHtml();

		return $arrFormInfo;
	}

	public function validatePlanSoFormsRequest($validationAlreadyPassed, $arrFormFields)
	{
		$planSoFormType = GdbcPlanSoFormsAdminModule::OPTION_PLANSO_GENERAL_FORM;
		if(isset($arrFormFields['j']->registration->active) && (string)$arrFormFields['j']->registration->active == 'registration'){
			$planSoFormType = GdbcPlanSoFormsAdminModule::OPTION_PLANSO_REGISTER_FORM;
		}
		elseif(isset($arrFormFields['j']->registration->active) && (string)$arrFormFields['j']->registration->active == 'login'){
			$planSoFormType = GdbcPlanSoFormsAdminModule::OPTION_PLANSO_LOGIN_FORM;
		}
		elseif(isset($arrFormFields['j']->paypal->paypal_payment_activate) && (bool)$arrFormFields['j']->paypal->paypal_payment_activate === true) {
			$planSoFormType = GdbcPlanSoFormsAdminModule::OPTION_PLANSO_PAYPAL_FORM;
		}

		if( ! $this->getOption($planSoFormType) )
			return;

		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName($planSoFormType);

		$arrCapturedData = ( !empty($arrFormFields['mail_replace']) && is_array($arrFormFields['mail_replace']) ) ? $arrFormFields['mail_replace'] : array();
		$arrCapturedData['form-title'] = !empty($arrFormFields['title']) ? $arrFormFields['title'] : null;

		$this->attemptEntity->Notes = $arrCapturedData;

		return GdbcRequestController::isValid($this->attemptEntity) ? true : __('There was an error while processing your request!', $this->PLUGIN_SLUG);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

	/**
	 * @return int
	 */
	protected function getModuleId() {
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_PLANSO_FORMS);
	}
}