<?php
/**
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

class GdbcWordPressPublicModule extends GdbcBasePublicModule
{
	private $commentValidationHookIndex       = null;
	private $loginFormHookIndex               = null;
	private $loginFormBottomHookIndex         = null;
	private $loginAuthenticateFilterHookIndex = null;

	private $wasCommentFieldsLengthFilterApplied = false;
	private $arrRegistrationHooksIndex = null;

	protected function __construct()
	{
		parent::__construct();

		if($this->getOption(GdbcWordPressAdminModule::WORDPRESS_LOGIN_FORM) !== null)
		{
			$this->activateLoginHooks();
		}

		if($this->getOption(GdbcWordPressAdminModule::WORDPRESS_REGISTRATION_FORM))
		{
			$this->activateRegisterHooks();
		}

		if($this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM) !== null)
		{
			$this->activateCommentsHooks();
		}

		if($this->getOption(GdbcWordPressAdminModule::WORDPRESS_LOST_PASSWORD_FORM))
		{
			$this->activateLostPasswordHooks();
		}

	}


	private function activateCommentsHooks()
	{

		$this->addActionHook('comment_form_top', array($this, 'renderTokenFieldIntoCommentsForm'));
		$this->addActionHook('comment_form'    , array($this, 'renderTokenFieldIntoCommentsForm'));

		$this->commentValidationHookIndex = $this->addFilterHook('preprocess_comment', array($this, 'validateCommentsRequest'), -10);

		if(function_exists('wp_get_comment_fields_max_lengths')) // Available since WordPress 4.5
		{
			$this->addFilterHook('wp_get_comment_fields_max_lengths', array($this, 'getCommentFieldsMaxLength'), 999, 1);
		}

		if(defined('EPOCH_VER')){
			$this->addActionHook('epoch_iframe_footer', array($this, 'renderPublicScriptIntoEpochIframe'));
		}

	}

	public function renderTokenFieldIntoCommentsForm()
	{
		static $alreadyRendered = false;
		if($alreadyRendered)
			return;

		$alreadyRendered = true;

		$this->renderTokenFieldIntoForm();
	}


	public function renderPublicScriptIntoEpochIframe()
	{
		echo parent::getPublicScriptInlineContent();
	}

	public function getCommentFieldsMaxLength($arrFieldsMaxLength)
	{
		$arrFieldsMaxLength = (array)$arrFieldsMaxLength;

		$authorFieldLength  = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_NAME_LENGTH);
		$emailFieldLength   = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH);
		$webSiteFieldLength = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH);
		$contentFieldLength = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH);

		!empty($authorFieldLength)  ? $arrFieldsMaxLength['comment_author']       = $authorFieldLength  : null;
		!empty($emailFieldLength)   ? $arrFieldsMaxLength['comment_author_email'] = $emailFieldLength   : null;
		!empty($webSiteFieldLength) ? $arrFieldsMaxLength['comment_author_url']   = $webSiteFieldLength : null;
		!empty($contentFieldLength) ? $arrFieldsMaxLength['comment_content']      = $contentFieldLength : null;

		$this->wasCommentFieldsLengthFilterApplied = true;

		return $arrFieldsMaxLength;
	}



	private function activateLoginHooks()
	{

		$this->loginFormHookIndex       = $this->addActionHook('login_form', array($this, 'renderTokenFieldIntoLoginForm'));
		$this->loginFormBottomHookIndex = $this->addActionHook('login_form_bottom', array($this, 'getTokenFieldForLoginForm'));

		$this->addFilterHook('wp_authenticate_user',  array($this, 'validateLoginUserAuthentication'), 25, 2);
		$this->loginAuthenticateFilterHookIndex = $this->addFilterHook('authenticate',  array($this, 'validateLoginAuthentication'), 95, 3);

		$this->addActionHook('wp_authenticate', array($this, 'preventBruteForceAuthentication'), 1, 2);

		$this->addActionHook('wp_login_failed', array($this, 'registerXmlRpcFailedLogin'), 1, 1);


		if(GoodByeCaptchaUtils::isGoogleAppsLoginPluginActivated()){
			$this->addActionHook('gal_user_loggedin', array($this, 'isUserAuthenticatedByGoogleAppsLogin'), 1, 1);
		}


		add_filter( 'wp_nav_menu_items', array($this, 'renderTokenFieldIntoAvadaMenuLoginForm'), PHP_INT_MAX, 2 );

	}

	public function renderTokenFieldIntoAvadaMenuLoginForm($items, $args)
	{
		if( ! function_exists('avada_add_login_box_to_nav') )
			return $items;

		if( false === strrpos($items, 'fusion-remember-checkbox') )
			return $items;


		return str_replace('</form>', $this->getTokenFieldHtml() . '</form>', $items);

	}

	public function registerXmlRpcFailedLogin($userName)
	{
		if( ! MchGdbcWpUtils::isXmlRpcRequest() )
			return;

		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_LOGIN_XML_RPC);
		$this->attemptEntity->Notes     = array('username' => sanitize_user($userName));

		GdbcBruteGuardian::logRejectedAttempt($this->getAttemptEntity());

	}

	public function preventBruteForceAuthentication($userName, $password)
	{
		if(empty($userName) || GdbcIPUtils::isClientIpWhiteListed())
			return;

		$validateResponse = $this->validateLoginAuthentication(new WP_Error(), $userName, $password);
		if( ! is_wp_error($validateResponse) )
			return;

		if($validateResponse->get_error_code() !== GoodByeCaptcha::PLUGIN_SLUG)
			return;

		if(GdbcBruteGuardian::isSiteUnderAttack())
			exit;
	}

	public function validateLoginUserAuthentication($wpUser, $password)
	{
		$userName = isset($wpUser->data->user_login) ? $wpUser->data->user_login : '';

		return $this->validateLoginAuthentication($wpUser, $userName, $password);
	}

	public function validateLoginAuthentication($wpUser, $userName, $password)
	{

		if($this->isUserAuthenticatedByGoogleAppsLogin())
			return $wpUser;

		if (is_wp_error($wpUser) && in_array($wpUser->get_error_code(), array('empty_username', 'empty_password')) ) {
			return $wpUser;
		}

		if(MchGdbcWpUtils::isXmlRpcRequest()) {
			return $wpUser;
		}

		if(!GoodByeCaptchaUtils::isPostRequestForWPStandardLogin())
			return $wpUser;

		$arrSubmittedData = array(
			'username' => sanitize_user($userName),
			//'password' => $password
		);

		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_LOGIN_FORM);
		$this->attemptEntity->Notes     = $arrSubmittedData;

		return GdbcRequestController::isValid($this->attemptEntity)
			? $wpUser
			: new WP_Error(GoodByeCaptcha::PLUGIN_SLUG,  __('Invalid username or incorrect password!', GoodByeCaptcha::PLUGIN_SLUG));

	}

	public function renderTokenFieldIntoLoginForm()
	{
		$this->renderTokenFieldIntoForm();
		$this->removeHookByIndex($this->loginFormBottomHookIndex);
	}

	public function getTokenFieldForLoginForm()
	{
		$this->removeHookByIndex($this->loginFormHookIndex);
		return $this->getTokenFieldHtml();
	}




	public function activateRegisterHooks()
	{
		if(empty($this->arrRegistrationHooksIndex)){
			$this->arrRegistrationHooksIndex = array();
		}

		$this->arrRegistrationHooksIndex[] = $this->addActionHook('register_form',             array($this, 'renderTokenFieldIntoForm'));
		$this->arrRegistrationHooksIndex[] = $this->addActionHook('signup_extra_fields',       array($this, 'renderTokenFieldIntoForm'));

		$this->arrRegistrationHooksIndex[] =  $this->addFilterHook('registration_errors',       array($this, 'validateRegisterFormEncryptedToken'), 10, 3 );
		$this->arrRegistrationHooksIndex[] = $this->addFilterHook('wpmu_validate_user_signup', array($this, 'validateMURegisterFormEncryptedToken'), 10, 1);

	}

	public function removeRegistrationHooks()
	{
		foreach( (array)$this->arrRegistrationHooksIndex as $registrationHook){
			$this->removeHookByIndex($registrationHook);
		}
	}

	public function validateMURegisterFormEncryptedToken($results)
	{
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_REGISTRATION_FORM);

		$this->attemptEntity->Notes     = array(
			'username' => !empty($results['user_name']) ? $results['user_name'] : '',
			'email'    => !empty($results['user_email']) ? $results['user_email'] : '',
		);

		if(GdbcRequestController::isValid($this->attemptEntity))
			return $results;

		empty($results['errors']) || !is_wp_error($results['errors']) ? $results['errors'] = new WP_Error() : null;

		$results['errors']->add('gdbc-invalid-token', __('Registration Error!', GoodByeCaptcha::PLUGIN_SLUG));

		return $results;
	}

	public function validateRegisterFormEncryptedToken($wpError, $sanitizedUserName, $userEmail)
	{
		$this->attemptEntity->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_REGISTRATION_FORM);
		$this->attemptEntity->Notes     = array(
			'username' => $sanitizedUserName,
			'email'    => sanitize_email($userEmail)
		);

		if(GdbcRequestController::isValid($this->attemptEntity))
			return $wpError;

		!is_wp_error($wpError) ? $wpError = new WP_Error() : null;

		$wpError->add('gdbc-invalid-token', __('Registration Error!', GoodByeCaptcha::PLUGIN_SLUG));

		return $wpError;
	}


	public function activateLostPasswordHooks()
	{
		$this->addActionHook('lostpassword_form', array($this, 'renderTokenFieldIntoForm'), 10);
		$this->addActionHook('lostpassword_post', array($this, 'validateLostPasswordFormEncryptedToken'), 10);
	}


	public function validateLostPasswordFormEncryptedToken()
	{

		$this->getAttemptEntity()->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_LOST_PASSWORD_FORM);

		$userName = !empty($_POST['user_login']) ?  $_POST['user_login'] : '';

		if(!empty($userName))
		{
			$userName = (strpos($userName, '@') === false) ? sanitize_user($userName) : sanitize_email($userName);
		}

		$this->getAttemptEntity()->Notes = array('username' => $userName);

		if(GdbcRequestController::isValid($this->getAttemptEntity()))
			return;

		wp_safe_redirect(wp_login_url());

		exit;
	}


	public function validateCommentsRequest($arrComment)
	{
		if(is_admin() && current_user_can( 'moderate_comments' ))
			return $arrComment;

		$arrWordPressCommentsType = array('pingback' => 1, 'trackback' => 1);

		if( (!empty($arrComment['comment_type']) && isset($arrWordPressCommentsType[strtolower($arrComment['comment_type'])]) ) ) {
			wp_die( '<p>' . __( 'Link Notifications are disabled!', GoodByeCaptcha::PLUGIN_SLUG ) . '</p>', __( 'Comment Submission Failure' ), array( 'response' => 200 ) );
		}

		$arrComment['comment_post_ID'] = (!empty($arrComment['comment_post_ID']) && is_numeric($arrComment['comment_post_ID'])) ? (int)$arrComment['comment_post_ID'] : 0;

		if ( ! isset( $arrComment['comment_agent'] ) ) {
			$arrComment['comment_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']: '';
		}
		$arrComment['comment_agent'] = substr( $arrComment['comment_agent'], 0, 254 );

		$this->getAttemptEntity()->SectionId = $this->getOptionIdByOptionName(GdbcWordpressAdminModule::WORDPRESS_COMMENTS_FORM);
		$this->getAttemptEntity()->Notes = array_filter($arrComment);

		unset($this->attemptEntity->Notes['user_ID'], $this->attemptEntity->Notes['comment_author_IP'], $this->attemptEntity->Notes['comment_date'], $this->attemptEntity->Notes['comment_date_gmt']);
		unset($this->attemptEntity->Notes['comment_as_submitted'], $this->attemptEntity->Notes['akismet_result']);

		unset($this->attemptEntity->Notes['comment_agent']);

		if(!$this->wasCommentFieldsLengthFilterApplied && is_wp_error($commentFieldsLengthError = $this->validateCommentFieldsLength($arrComment)))
		{
			$this->getAttemptEntity()->ReasonId = GdbcRequestController::REJECT_REASON_COMMENT_FIELD_TOO_LONG;
			GdbcBruteGuardian::logRejectedAttempt($this->getAttemptEntity());

			$errorCode    =  intval($commentFieldsLengthError->get_error_data());
			$errorMessage =  '<p>' . $commentFieldsLengthError->get_error_message() . '</p>';

			if( MchGdbcWpUtils::isAjaxRequest() || defined('EPOCH_API') )
			{
				$errorMessage = strip_tags ($commentFieldsLengthError->get_error_message());
				$errorCode    = 403;
			}

			wp_die( $errorMessage, __( 'Comment Submission Failure' ), array( 'response' => $errorCode, 'back_link' => true ) );

		}


		if( GdbcRequestController::isValid($this->getAttemptEntity()) )
			return $arrComment;

		if(GdbcAjaxController::isWpDiscuzPostCommentAjaxRequest())
		{
			wp_die(json_encode(array('code' => 'wc_invalid_field')));
		}


		$postPermaLink = get_permalink($arrComment['comment_post_ID']);

		empty($postPermaLink) ? wp_safe_redirect(home_url('/')) : wp_safe_redirect($postPermaLink);

		exit;

	}


	private function validateCommentFieldsLength(array $arrComment)
	{
		$arrComment = wp_unslash($arrComment);

		$authorFieldLength  = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_NAME_LENGTH);
		$emailFieldLength   = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH);
		$webSiteFieldLength = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH);
		$contentFieldLength = $this->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH);

		$comment_author       = isset($arrComment['comment_author'])       && is_string($arrComment['comment_author'])       ? trim(strip_tags($arrComment['comment_author'])) : null;
		$comment_content      = isset($arrComment['comment_content'])      && is_string($arrComment['comment_content'])      ? trim($arrComment['comment_content'])            : null;
		$comment_author_url   = isset($arrComment['comment_author_url'])   && is_string($arrComment['comment_author_url'])   ? trim($arrComment['comment_author_url'])         : null;
		$comment_author_email = isset($arrComment['comment_author_email']) && is_string($arrComment['comment_author_email']) ? trim($arrComment['comment_author_email'])       : null;

		if ( isset( $comment_author ) && $authorFieldLength < mb_strlen( $comment_author, '8bit' ) )
		{
			return new WP_Error( 'comment_author_column_length', __( '<strong>ERROR</strong>: your name is too long.' ), 200 );
		}

		if ( isset( $comment_author_email ) && $emailFieldLength < strlen( $comment_author_email ) )
		{
			return new WP_Error( 'comment_author_email_column_length', __( '<strong>ERROR</strong>: your email address is too long.' ), 200 );
		}

		if ( isset( $comment_author_url ) && $webSiteFieldLength < strlen( $comment_author_url ) )
		{
			return new WP_Error( 'comment_author_url_column_length', __( '<strong>ERROR</strong>: your url is too long.' ), 200 );
		}

		if ( empty($comment_content) )
		{
			return new WP_Error( 'require_valid_comment', __( '<strong>ERROR</strong>: please type a comment.' ), 200 );
		}

		if ( $contentFieldLength < mb_strlen( $comment_content, '8bit' ) )
		{
			return new WP_Error( 'comment_content_column_length', __( '<strong>ERROR</strong>: your comment is too long.' ), 200 );
		}


		return true;
	}


	public function isUserAuthenticatedByGoogleAppsLogin($wpUser = null)
	{
		static $isUserAuthenticated = null;

		if(null !== $wpUser) {
			$isUserAuthenticated = is_a($wpUser, 'WP_User');
		}

		return !empty($isUserAuthenticated);
	}

	public function getLoginAuthenticateFilterHookIndex()
	{
		return $this->loginAuthenticateFilterHookIndex;
	}

	public static function isCommentsProtectionActivated()
	{
		return (bool)self::getInstance()->getOption(GdbcWordPressAdminModule::WORDPRESS_COMMENTS_FORM);
	}

	public function getCommentValidationHookIndex()
	{
		return $this->commentValidationHookIndex;
	}

	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}

	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_WORDPRESS);
	}
}