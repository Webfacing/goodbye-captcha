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

class GdbcEmailNotificationsPublicModule extends GdbcBasePublicModule
{
	public $EmailSubject    = null;
	public $EmailBodyContent = null;

	protected function __construct()
	{
		parent::__construct();

		if( ! function_exists('wp_mail') ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}

		if(!empty($_POST) && $this->getOption(GdbcEmailNotificationsAdminModule::OPTION_ADMIN_LOGGED_IN_DETECTED)) {
			$this->addActionHook( 'wp_login', array( $this, 'userSuccessfullyLoggedIn' ), 0, 1 );
		}

	}

	public function userSuccessfullyLoggedIn($userName)
	{
		if(empty($_POST) || empty($userName))
			return;

		$loggedInUser = get_user_by('login', $userName);
		if(empty($loggedInUser) || !user_can($loggedInUser, 'update_core'))
			return;

		$templateFilePath = dirname(__FILE__) . '/templates/notification-admin-logged-in.php';
		if(!MchGdbcWpUtils::fileExists($templateFilePath))
			return;

		//$this->EmailSubject = __('WPBruiser - Notification', GoodByeCaptcha::PLUGIN_SLUG);

		$detectedDate = get_date_from_gmt ( date( 'Y-m-d H:i:s', MchGdbcHttpRequest::getServerRequestTime() ), 'l, F d, Y');
		$detectedTime = get_date_from_gmt ( date( 'Y-m-d H:i:s', MchGdbcHttpRequest::getServerRequestTime() ), 'H:i:s');

		$arrReplaceableContent = array(
				'{current-site-link}' => MchGdbcWpUtils::getCurrentBlogLink(),
				'{admin-full-name}'   => MchGdbcWpUtils::getAdminFullName(),
				'{date-time}'         => $detectedDate . ' at ' . $detectedTime,
//				'{date}'              => $detectedDate,
//				'{time}'              => $detectedTime,
				'{client-ip}'         => GdbcIPUtils::getClientIpAddress(),
				'{username}'          => $userName
		);

		ob_start();

		require $templateFilePath;

		$this->EmailBodyContent = str_replace(array_keys($arrReplaceableContent), array_values($arrReplaceableContent), ob_get_clean());
		$this->send();
	}

	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_EMAIL_NOTIFICATIONS);
	}

	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}


	public function send($isHtmlFormattedEmail = true)
	{
		$emailHeaders = array();
		$isHtmlFormattedEmail ? $emailHeaders[] = 'Content-Type: text/html; charset=UTF-8' : null;

		$emailAddressToSend = $this->getOption(GdbcEmailNotificationsAdminModule::OPTION_EMAIL_ADDRESS);

		empty($emailAddressToSend) ? $emailAddressToSend = MchGdbcWpUtils::getAdminEmailAddress() : null;

		empty($this->EmailSubject) ? $this->EmailSubject =  __('WPBruiser Notification', GoodByeCaptcha::PLUGIN_SLUG) : null;

		//$emailContent = file_get_contents($this->layoutTemplateFilePath);

		$emailContent = @file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'notification-base-layout.html');

		if(false !== $emailContent)
		{
			$emailContent = str_replace('{email-body-content}', trim($this->EmailBodyContent), $emailContent);
		}
		else
		{
			$emailContent = trim($this->EmailBodyContent);
		}

		@wp_mail($emailAddressToSend, $this->EmailSubject, $emailContent, $emailHeaders);

	}


}