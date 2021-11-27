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

class GdbcWordPressTweaksPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();

		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_COMMENTS_FORM_NOTES_FIELDS))
		{
			$this->addFilterHook('comment_form_defaults', array($this, 'hideFormNotesFields'));
		}

		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_COMMENTS_FORM_WEBSITE_FIELD))
		{
			$this->addFilterHook('comment_form_default_fields', array($this, 'hideFormWebSiteField'));
		}

		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_REMOVE_RSD_HEADER))
		{
			remove_action('wp_head', 'rsd_link');
		}

		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_REMOVE_WLW_HEADER))
		{
			remove_action('wp_head', 'wlwmanifest_link');
		}

		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_HIDE_VERSION))
		{
			foreach(array('html', 'xhtml', 'atom', 'rss2', 'rdf', 'comment', 'export') as $generatorType) {
				add_filter("get_the_generator_{$generatorType}", '__return_empty_string');
			}
		}

		if(MchGdbcWpUtils::isXmlRpcRequest() && (bool)$this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_XML_RPC_FULLY_DISABLED))
		{
			$this->blockXmlRpcRequest();
		}

		if(MchGdbcWpUtils::isXmlRpcRequest() && (bool)$this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_XML_RPC_PINGBACKS_DISABLED))
		{
			add_filter('xmlrpc_methods', array($this, 'removeXPingBackXmlRpcMethods'));
		}


		if($this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_XML_RPC_FULLY_DISABLED) || $this->getOption(GdbcWordPressTweaksAdminModule::WORDPRESS_XML_RPC_PINGBACKS_DISABLED))
		{
			add_filter('wp_headers', array($this, 'removeXPingBackHeader'), 9999);
			add_filter('bloginfo_url', array($this, 'filterXPingBackLink'), 9999, 2);
		}


	}


	private function blockXmlRpcRequest()
	{
		if(empty($_POST) || GdbcIPUtils::isClientIpWhiteListed())
			return;

		if(GoodByeCaptchaUtils::isJetPackPluginActivated() && MchGdbcTrustedIPRanges::isIPInAutomatticRanges(GdbcIPUtils::getClientIpAddress(), MchGdbcIPUtils::getIpAddressVersion(GdbcIPUtils::getClientIpAddress()))) {
			return;
		}

		$this->getAttemptEntity()->ModuleId  = GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_WORDPRESS);
		$this->getAttemptEntity()->SectionId = GdbcModulesController::getModuleOptionId(GdbcModulesController::MODULE_WORDPRESS, GdbcWordPressAdminModule::WORDPRESS_LOGIN_XML_RPC);
		$this->getAttemptEntity()->ReasonId  = GdbcRequestController::REJECT_REASON_SERVICE_UNAVAILABLE;

		GdbcBruteGuardian::logRejectedAttempt($this->getAttemptEntity());

		if (!headers_sent()) {
			header('Connection: close');
			header('Content-Type: text/xml');
			header('Date: ' . date('r'));
		}

		echo '<?xml version="1.0"?><methodResponse><fault><value><struct><member><name>faultCode</name><value><int>405</int></value></member><member><name>faultString</name><value><string>XML-RPC services are disabled on this site!</string></value></member></struct></value></fault></methodResponse>';
		exit;

	}

	public function removeXPingBackXmlRpcMethods($arrXmlRpcMethods)
	{
		if(empty($_POST) || GdbcIPUtils::isClientIpWhiteListed())
			return $arrXmlRpcMethods;

		if(GoodByeCaptchaUtils::isJetPackPluginActivated() && MchGdbcTrustedIPRanges::isIPInAutomatticRanges(GdbcIPUtils::getClientIpAddress(), MchGdbcIPUtils::getIpAddressVersion(GdbcIPUtils::getClientIpAddress()))) {
			return $arrXmlRpcMethods;
		}

		unset( $arrXmlRpcMethods['pingback.ping'] );
		unset( $arrXmlRpcMethods['pingback.extensions.getPingbacks'] );

		return $arrXmlRpcMethods;
	}

	public function removeXPingBackHeader($arrHeaders)
	{
		unset( $arrHeaders['X-Pingback'] );
		return $arrHeaders;
	}

	public function filterXPingBackLink($output, $show )
	{
		return ('pingback_url' === $show) ? '' : $output;
	}

	public function hideFormWebSiteField($arrDefaultFields)
	{
		unset($arrDefaultFields['url']);
		return $arrDefaultFields;
	}

	public function hideFormNotesFields($arrDefaultFields)
	{
		$arrDefaultFields = (array)$arrDefaultFields;
		$arrDefaultFields['comment_notes_before'] = '';
		$arrDefaultFields['comment_notes_after'] = '';

		return $arrDefaultFields;
	}

	/**
	 * @return int
	 */
	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_WORDPRESS_TWEAKS);
	}

	public static function getInstance()
	{
		static $publicInstance = null;
		return null !== $publicInstance ? $publicInstance : $publicInstance = new self();
	}


}