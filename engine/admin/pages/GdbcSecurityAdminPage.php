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

class GdbcSecurityAdminPage  extends GdbcBaseAdminPage
{
	private $blackListedIpsGroupIndex = null;
	private $whiteListedIpsGroupIndex = null;
	private $geoIpCountryGroupIndex   = null;

	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_BRUTE_FORCE))
		{
			$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser Brute Force Settings', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcBruteForceAdminModule::getInstance())
				)
			));
		}

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_WHITE_LISTED_IPS))
		{
			$this->whiteListedIpsGroupIndex = $this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('White Listed IPs', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcWhiteListedIpsAdminModule::getInstance())
				)
			));
		}

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_BLACK_LISTED_IPS))
		{
			$this->blackListedIpsGroupIndex = $this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('Black Listed IPs', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcBlackListedIpsAdminModule::getInstance())
				)
			));
		}

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_COUNTRY_BLOCKING))
		{
			$this->geoIpCountryGroupIndex = $this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('Geo IP Country Blocking', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcGeoIpCountryAdminModule::getInstance())
				)
			));

		}

	}

	private function getBlackListedIpsInputName()
	{
		return esc_attr(GdbcBlackListedIpsAdminModule::getInstance()->getSettingKey() . '[' . GdbcBlackListedIpsAdminModule::OPTION_BLACK_LISTED_IPS . ']');
	}
	private function getWhiteListedIpsInputName()
	{
		return esc_attr(GdbcWhiteListedIpsAdminModule::getInstance()->getSettingKey() . '[' . GdbcWhiteListedIpsAdminModule::OPTION_WHITE_LISTED_IPS . ']');
	}

	public function renderGroupModulesSettings($groupIndex = null)
	{

		if(!is_numeric($groupIndex))
		{
			foreach ( func_get_args() as $receivedArgument )
			{
				if ( ! isset( $receivedArgument['args'] ) || !is_numeric($receivedArgument['args']))
					continue;

				$groupIndex = $receivedArgument['args'];
				break;
			}
		}

		$this->displayGroupModulesMessages($groupIndex);

		if($this->whiteListedIpsGroupIndex == $groupIndex)
		{
			if(null !== GdbcWhiteListedIpsAdminModule::getInstance()->getPartialAdminSettingsFilePath())
			{
				include_once GdbcWhiteListedIpsAdminModule::getInstance()->getPartialAdminSettingsFilePath();
			}

			return;
		}


		if($this->blackListedIpsGroupIndex == $groupIndex)
		{
			if(null !== GdbcBlackListedIpsAdminModule::getInstance()->getPartialAdminSettingsFilePath())
			{
				include_once GdbcBlackListedIpsAdminModule::getInstance()->getPartialAdminSettingsFilePath();
			}

			return;
		}

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_COUNTRY_BLOCKING) && $this->geoIpCountryGroupIndex == $groupIndex)
		{
			if(null !== GdbcGeoIpCountryAdminModule::getInstance()->getPartialAdminSettingsFilePath())
			{
				include_once GdbcGeoIpCountryAdminModule::getInstance()->getPartialAdminSettingsFilePath();
			}

			return;
		}

		return parent::renderGroupModulesSettings($groupIndex);

	}


	public function registerPageMetaBoxes()
	{
		parent::registerPageMetaBoxes();

		if($this->getPageLayoutColumns() <= 1)
			return;

		add_meta_box(
			"gdbc-help-web-attackers-list",
			__('Web Attackers IPs List', GoodByeCaptcha::PLUGIN_SLUG),
			array( $this, 'renderWebAttackersMetaBox' ),
			$this->getAdminScreenId(),
			'side',
			'core',
			null
		);

		add_meta_box(
			"gdbc-help-proxy-anonymizers-list",
			__('Anonymous Proxy IPs List', GoodByeCaptcha::PLUGIN_SLUG),
			array( $this, 'renderProxyAnonymizersMetaBox' ),
			$this->getAdminScreenId(),
			'side',
			'core',
			null
		);

	}

	public function renderWebAttackersMetaBox()
	{
		$textInfo   = __('Provides security against the most well known attackers\' IP Addresses involved in Brute Force Attacks, with the minimum of false positives.', GoodByeCaptcha::PLUGIN_SLUG);


		echo '<p>' . $textInfo . '</p>';
	}

	public function renderProxyAnonymizersMetaBox()
	{
		$textInfo   = __('Provides protection against the most Proxy used IP addresses identified as high risk. ', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo  .= __('The list also contains the most fraudulent TOR network, TOR Nodes and TOR Exit Points IP Addresses.', GoodByeCaptcha::PLUGIN_SLUG);

		echo '<p style="text-align: justify">' . $textInfo . '</p>';
	}

}