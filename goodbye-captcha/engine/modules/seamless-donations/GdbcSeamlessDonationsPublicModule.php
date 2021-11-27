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

final class GdbcSeamlessDonationsPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

		if(!GoodByeCaptchaUtils::isSeamlessDonationsActivated())
			return;

		if(!$this->getOption(GdbcSeamlessDonationsAdminModule::OPTION_SEAMLESS_DONATIONS_PROTECTION_ACTIVATED))
			return;

		$this->registerSeamlessDonationsHooks();

	}

	private function registerSeamlessDonationsHooks()
	{

		add_filter('seamless_donations_form_submit_section', array($this, 'insertHiddenFieldIntoForm'));
		add_filter('seamless_donations_challenge_response_request', array($this, 'validateToken'), 10, 2);
	}

	public function validateToken($isRequestValid, $arrSubmittedFormData)
	{
		$arrSubmittedFormData = array_change_key_case((array)$arrSubmittedFormData, CASE_UPPER);

		unset(
			$arrSubmittedFormData['REFERRINGURL'],
			$arrSubmittedFormData['SUCCESSURL'],
			$arrSubmittedFormData['SESSIONID'],
			$arrSubmittedFormData['REPEATING'],
			$arrSubmittedFormData['INCREASETOCOVER'],
			$arrSubmittedFormData['ANONYMOUS'],
			$arrSubmittedFormData['EMPLOYERMATCH'],
			$arrSubmittedFormData['NONCE'],
			$arrSubmittedFormData['NOTIFY_URL'],
			$arrSubmittedFormData['CMD'],
			$arrSubmittedFormData['P3'],
			$arrSubmittedFormData['T3'],
			$arrSubmittedFormData['A3'],
			$arrSubmittedFormData['SDVERSION'], $arrSubmittedFormData['HONORBYEMAIL'], $arrSubmittedFormData['RETURN']
		);


		$this->attemptEntity->Notes = array_change_key_case(array_filter($arrSubmittedFormData), CASE_LOWER);

		if(GdbcRequestController::isValid($this->attemptEntity))
			return true;

		return __('Invalid request received',  GoodByeCaptcha::PLUGIN_SLUG);

	}

	public function insertHiddenFieldIntoForm($arrSubmitSectionInfo)
	{
		if(! is_array($arrSubmitSectionInfo)){
			$arrSubmitSectionInfo = array();
		}

		if(empty($arrSubmitSectionInfo['elements']) || !is_array($arrSubmitSectionInfo['elements'])) {
			$arrSubmitSectionInfo['elements'] = array();
		}

		foreach($arrSubmitSectionInfo['elements'] as $fieldKey => &$arrFieldInfo)
		{
			if(empty($arrFieldInfo) || !is_array($arrFieldInfo))
				continue;

			$arrFieldInfo['before'] = empty($arrFieldInfo['before']) ? $this->getTokenFieldHtml() : $arrFieldInfo['before'] . $this->getTokenFieldHtml();

			return $arrSubmitSectionInfo;
		}

		$arrSubmitSectionInfo['elements'] = array(
			'gdbc-fake-field' => array(
				'type'  => 'hidden',
				'value' => '',
				'before' => $this->getTokenFieldHtml()
			)
		);

		return $arrSubmitSectionInfo;
	}

	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_SEAMLESS_DONATIONS);
	}


	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}

}
