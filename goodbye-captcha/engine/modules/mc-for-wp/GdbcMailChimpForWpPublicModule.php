<?php
/**
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

class GdbcMailChimpForWpPublicModule extends GdbcBasePublicModule
{
	private $mailChimpFormLists = array();

	public function __construct()
	{

		parent::__construct();

		if(!$this->getOption(GdbcMailChimpForWpAdminModule::OPTION_MODULE_MAIL_CHIMP_FOR_WP))
			return;

		add_filter('mc4wp_form_before_fields', array($this, 'getTokenFieldHtml'));


		add_filter('mc4wp_form_errors', array($this, 'validateSubscriptionRequest'), 10, 2);


		//add_filter('mc4wp_valid_form_request', array($this, 'validateOldSubscriptionRequest'), 10, 2);
		
		
		//MailChimp Top Bar
		add_action('mctb_before_submit_button', array($this, 'renderTokenFieldIntoForm'));
		add_filter('mctb_validate', array($this, 'validateMailChimpTopBarRequest'));
		
		
		add_filter('mc4wp_form_data', function($arrFormData, $formObject){
			
			$arrTokens = array(GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME),  GdbcRequestController::getPostedBrowserInfoInputName());
			
			foreach($arrTokens as $token) {
				unset($arrFormData[$token], $arrFormData[strtoupper($token)]);
			}
			
			return $arrFormData;
			
		}, PHP_INT_MAX, 2);
		
	}

	public function validateMailChimpTopBarRequest($isValid)
	{
		$this->getAttemptEntity()->Notes = array('email' => (empty( $_POST['email'] ) || ! is_email( $_POST['email'] )) ? null : strtolower($_POST['email']));
		
		$isValid = GdbcRequestController::isValid($this->getAttemptEntity());
		
		return $isValid;
	}
	
	public function validateSubscriptionRequest($arrErrors, $mcForm)
	{
		if( ! is_object($mcForm) )
			return $arrErrors;

		!is_array($arrErrors) ? $arrErrors = array() : null;

		$submittedData = (array)@$mcForm->get_data();

		$arrCapturedData = array();
		foreach($submittedData as $fieldName => $fieldValue)
		{
			if(is_scalar($fieldValue))
			{
				$arrCapturedData[$fieldName] = $fieldValue;
				continue;
			}
		}

		$this->getAttemptEntity()->Notes = $arrCapturedData;

		if( ! GdbcRequestController::isValid($this->getAttemptEntity()) ) {
			$arrErrors[] = 'spam';
		}

		return $arrErrors;

	}


	public function validateOldSubscriptionRequest($isRequestValid, $submittedData)
	{
		$submittedData     = array_change_key_case((array)$submittedData, CASE_UPPER);
		$submittedPostData = array_change_key_case((array)$_POST,         CASE_UPPER);

		foreach($submittedData as $submittedKey => $submittedValue)
		{
			if(!isset($submittedPostData[$submittedKey]))
				unset($submittedData[$submittedKey]);
		}

		unset($submittedPostData, $submittedKey, $submittedValue);

		$arrCapturedData = array();
		foreach((array)$submittedData as $fieldName => $fieldValue)
		{
			if(is_scalar($fieldValue))
			{
				$arrCapturedData[$fieldName] = $fieldValue;
				continue;
			}

			if(strtolower($fieldName) === 'address')
			{
				if(is_array($fieldValue))
				{
					$fieldValue = array_merge(array('addr1' => '','city' => '','state' => '', 'zip' => ''), $fieldValue );
				}
				elseif(is_string($fieldValue))
				{
					$arrAddress = explode(',', $fieldValue);
					$fieldValue = array(
										'addr1' => isset($arrAddress[0]) ? $arrAddress[0] : null,
					                    'city'  => isset($arrAddress[1]) ? $arrAddress[1] : null,
					                    'state' => isset($arrAddress[2]) ? $arrAddress[2] : null,
					                    'zip'   => isset($arrAddress[3]) ? $arrAddress[3] : null,
					);
					$fieldValue = array_filter($fieldValue);
				}

				$arrCapturedData[$fieldName] = (array)$fieldValue;
				continue;
			}

			if(strtolower($fieldName) === 'groupings' && is_array($fieldValue))
			{
				foreach($fieldValue as $groupId => &$groupData)
				{
					if(!is_string($groupData))
						continue;

					$groupData = explode(',', sanitize_text_field($groupData));
				}

				$arrCapturedData[$fieldName] = (array)$fieldValue;
			}
		}

		$this->getAttemptEntity()->Notes = $arrCapturedData;

		return GdbcRequestController::isValid($this->getAttemptEntity()) ? array() : 'spam';

	}

	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_MAIL_CHIMP_FOR_WP);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}