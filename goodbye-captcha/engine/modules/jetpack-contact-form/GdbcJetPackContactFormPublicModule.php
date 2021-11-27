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

final class GdbcJetPackContactFormPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

		if(!$this->getOption(GdbcJetPackContactFormAdminModule::OPTION_IS_JETPACK_CONTACT_FORM_ACTIVATE))
			return;

		if(!GoodByeCaptchaUtils::isJetPackContactFormModuleActivated())
			return;

		$this->registerJetPackContactFormHooks();

	}
	
	public function registerJetPackContactFormHooks()
	{
		
		add_filter('grunion_contact_form_field_html', array($this, 'insertGoodByeCaptchaToken'), 1000, 3);
		
		defined('JETPACK__VERSION') && version_compare(JETPACK__VERSION, '3.4-beta', '>')
			? add_filter('jetpack_contact_form_is_spam', array($this, 'validateContactFormEncryptedToken'), 1, 2)
			: add_filter('contact_form_is_spam', array($this, 'validateContactFormEncryptedToken'));
		
		
		$inputTokenField = $this->getTokenFieldHtml();
		
		add_filter('do_shortcode_tag', function($formOutputHtml, $shortCodeTag ) use ($inputTokenField){
			
			if($shortCodeTag !== 'contact-form')
				return $formOutputHtml;
			
			$hiddenField = GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);
			
			if(false === strpos($formOutputHtml, $hiddenField))
			{
				$formOutputHtml = str_replace('</form>', $inputTokenField . '</form>', $formOutputHtml);
			}
			
			return $formOutputHtml;
			
			
		}, 1000, 2);
		
		
	}
	
	public function validateContactFormEncryptedToken($isSpam, $arrPostedData)
	{

		$arrClassProperties = get_class_vars('Grunion_Contact_Form');

		$grunionForm = isset($arrClassProperties['last']) ? Grunion_Contact_Form::$last : null;
		$arrGrunionFormFieldIds = (null !== $grunionForm && is_callable(array($grunionForm, 'get_field_ids'))) ? $grunionForm->get_field_ids() : array();

		$arrSubmittedData = array();
		if(!empty($arrGrunionFormFieldIds['all']) && is_array($arrGrunionFormFieldIds['all']))
		{
			$arrGrunionFormFieldIds = !empty($arrGrunionFormFieldIds['extra']) && is_array($arrGrunionFormFieldIds['extra'])
									  ? array_merge( $arrGrunionFormFieldIds['all'], $arrGrunionFormFieldIds['extra'] )
				                      : $arrGrunionFormFieldIds['all'];

			foreach($arrGrunionFormFieldIds  as $fieldId )
			{
				$formField = isset( $grunionForm->fields[ $fieldId ] ) ? $grunionForm->fields[ $fieldId ] : null;
				if ( null === $formField || ! isset( $formField->value ) || ! is_callable(array( $formField, 'get_attribute' )) ) {
					continue;
				}

				$fieldLabel = $formField->get_attribute('label');

				!empty($fieldLabel) ? $arrSubmittedData[$fieldLabel] = MchGdbcUtils::normalizeNewLine($formField->value) : null;
			}
		}

		$arrSubmittedData = array();
		if(empty($arrSubmittedData))
		{
			$arrPostedMappedKeys = array(
				'comment_author'       => __('Name'   , GoodByeCaptcha::PLUGIN_SLUG),
				'comment_author_email' => __('Email'  , GoodByeCaptcha::PLUGIN_SLUG),
				'comment_author_url'   => __('Website', GoodByeCaptcha::PLUGIN_SLUG),
				'comment_content'      => __('Message', GoodByeCaptcha::PLUGIN_SLUG),
			);

			foreach($arrPostedMappedKeys as $postedKey => $newKey)
			{
				if(!isset($arrPostedData[$postedKey]))
					continue;

				$arrSubmittedData[$newKey] = $arrPostedData[$postedKey];
				unset($arrPostedData[$postedKey]);
			}
		}

		$postId = empty($_POST['contact-form-id']) ? 0 : stripslashes($_POST['contact-form-id']);
		$postId = (false !== strpos($postId, 'widget-')) ? absint(substr($postId, 7)) : absint($postId);

		if($title = get_the_title($postId)){
			$arrSubmittedData = array_merge(array('form-title' => $title), $arrSubmittedData);
		}

		$this->getAttemptEntity()->Notes = $arrSubmittedData;

		if(GdbcRequestController::isValid($this->getAttemptEntity()))
			return false;

		if(null !== $grunionForm && in_array('errors', (array)get_class_vars($grunionForm)))
		{
			is_wp_error($grunionForm->errors)
				? $grunionForm->errors->add($this->PLUGIN_SLUG,           __('Your entry appears to be spam!', GoodByeCaptcha::PLUGIN_SLUG))
				: $grunionForm->errors = new WP_Error($this->PLUGIN_SLUG, __('Your entry appears to be spam!', GoodByeCaptcha::PLUGIN_SLUG));
		}

		return new WP_Error();
	}

	public function insertGoodByeCaptchaToken($fieldBlock, $fieldLabel, $postId)
	{

		static $pageContactFormDetected   = false;
		static $widgetContactFormDetected = false;

		if($pageContactFormDetected && $widgetContactFormDetected)
			return $fieldBlock;

		$arrAttributes = shortcode_parse_atts($fieldBlock);

		$fieldId   = isset($arrAttributes['id'])   ? $arrAttributes['id']   : null;
		$fieldName = isset($arrAttributes['name']) ? $arrAttributes['name'] : null;


		if(null === $fieldName || $fieldId !== $fieldName)
			return $fieldBlock;

		$arrNameParts = explode('-', $fieldName);
		if( !isset($arrNameParts[0]) || empty($arrNameParts))
			return $fieldBlock;

		if(!$widgetContactFormDetected)
		{
			$widgetContactFormDetected = ( false !== strpos( $arrNameParts[0], 'widget' ) );

			if ( $widgetContactFormDetected && isset( $arrNameParts[2] ) && is_numeric( $arrNameParts[2] ) ) {
				return $fieldBlock . $this->getTokenFieldHtml();
			}
		}

		if(!$pageContactFormDetected)
		{
			$postId = (string) $postId;

			if ( ! empty( $postId ) && ( substr( $arrNameParts[0], - strlen( $postId ) ) === $postId ) ) {
				$pageContactFormDetected = true;
				return $fieldBlock . $this->getTokenFieldHtml();
			}

			if ( empty( $postId ) && preg_match( "/g([0-9]+)/", $arrNameParts[0], $matches ) && $arrNameParts[0] === $matches[0] ) {
				$pageContactFormDetected = true;
				return $fieldBlock . $this->getTokenFieldHtml();
			}
		}

		return $fieldBlock;

	}

	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_JETPACK_CONTACT_FORM);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}