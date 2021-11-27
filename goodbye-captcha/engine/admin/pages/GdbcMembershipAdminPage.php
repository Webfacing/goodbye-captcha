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

class GdbcMembershipAdminPage extends GdbcBaseAdminPage
{
	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		$arrModules  = array();
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ULTRA_COMMUNITY))
			$arrModules[] =  GdbcUltraCommunityAdminModule::getInstance();
		
		
		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_WP_MEMBERS))
			$arrModules[] =  GdbcWPMembersAdminModule::getInstance();


		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_USER_PRO))
			$arrModules[] = GdbcUserProAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_UPME))
			$arrModules[] = GdbcUPMEAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_MEMBER_PRESS))
			$arrModules[] = GdbcMemberPressAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_BUDDY_PRESS))
			$arrModules[] =  GdbcBuddyPressAdminModule::getInstance();

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_ULTIMATE_MEMBER))
			$arrModules[] =  GdbcUltimateMemberAdminModule::getInstance();

		$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WPBruiser - Membership Plugins Settings', GoodByeCaptcha::PLUGIN_SLUG), $arrModules)
			)
		);

	}

}