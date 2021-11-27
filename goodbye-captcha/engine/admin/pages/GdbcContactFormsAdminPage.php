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

class GdbcContactFormsAdminPage extends GdbcBaseAdminPage
{
	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		$modulesList = array();
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_HTML_FORMS))
			$modulesList[] = GdbcHtmlFormsAdminModule::getInstance();
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_JETPACK_CONTACT_FORM))
			$modulesList[] = GdbcJetPackContactFormAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_CONTACT_FORM_7))
			$modulesList[] = GdbcContactForm7AdminModule::getInstance();
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_WP_FORMS))
			$modulesList[] = GdbcWpFormsAdminModule::getInstance();
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_FAST_SECURE_FORM))
			$modulesList[] = GdbcFastSecureFormAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_FORMIDABLE_FORMS))
			$modulesList[] = GdbcFormidableFormsAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_GRAVITY_FORMS))
			$modulesList[] = GdbcGravityFormsAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_NINJA_FORMS))
			$modulesList[] = GdbcNinjaFormsAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_QUFORM))
			$modulesList[] = GdbcQuformAdminModule::getInstance();

		$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser - Popular Contact Forms Settings', GoodByeCaptcha::PLUGIN_SLUG), $modulesList)
			)
		);

	}



}