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

class GdbcOthersAdminPage  extends GdbcBaseAdminPage
{
	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		$modulesList = array();


		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_MAIL_CHIMP_FOR_WP))
			$modulesList[] = GdbcMailChimpForWpAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_EASY_FORMS_FOR_MAILCHIMP))
			$modulesList[] = GdbcEasyFormsForMailChimpAdminModule::getInstance();


		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_MAIL_POET))
			$modulesList[] = GdbcMailPoetAdminModule::getInstance();


		$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser - Subscriptions Plugins Settings', GoodByeCaptcha::PLUGIN_SLUG), $modulesList)
			)
		);

		$modulesList = array();
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ZM_ALR))
			$modulesList[] = GdbcZmAlrAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_PLANSO_FORMS))
			$modulesList[] = GdbcPlanSoFormsAdminModule::getInstance();


		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_SEAMLESS_DONATIONS))
			$modulesList[] = GdbcSeamlessDonationsAdminModule::getInstance();

		$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser - Other Popular Plugins', GoodByeCaptcha::PLUGIN_SLUG), $modulesList)
			)
		);



	}

}
