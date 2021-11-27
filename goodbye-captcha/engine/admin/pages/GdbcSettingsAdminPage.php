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

class GdbcSettingsAdminPage  extends GdbcBaseAdminPage
{
	private $proxyHeadersGroupIndex = null;

	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_SETTINGS))
		{
			$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser General Settings', GoodByeCaptcha::PLUGIN_SLUG), array(
					GdbcSettingsAdminModule::getInstance())
				)
			));
		}

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_PROXY_HEADERS))
		{
			$this->proxyHeadersGroupIndex = $this->registerGroupedModules(array(
					new MchGdbcGroupedModules(__('Trusted Proxy Headers', GoodByeCaptcha::PLUGIN_SLUG), array(
									GdbcProxyHeadersAdminModule::getInstance())
					)
			));
		}

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

		if($this->proxyHeadersGroupIndex == $groupIndex && GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_PROXY_HEADERS))
		{
			//$securityPageUrl = isset(self::$arrPageInstances['GdbcSecurityAdminPage']) ? self::$arrPageInstances['GdbcSecurityAdminPage']->getAdminUrl() : null;

			include_once GdbcProxyHeadersAdminModule::getInstance()->getPartialAdminSettingsFilePath();
			return;
		}


		return parent::renderGroupModulesSettings($groupIndex);

	}


}