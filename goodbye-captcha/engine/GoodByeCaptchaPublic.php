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

final class GoodByeCaptchaPublic extends MchGdbcBasePublicPlugin
{

	//private $clientScriptUrl     = null;
	private $formHiddenInputName = null;

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

		$settingsModuleInstance = GdbcModulesController::getPublicModuleInstance(GdbcModulesController::MODULE_SETTINGS);
		if(null === $settingsModuleInstance)
			return;

		//$this->clientScriptUrl     = plugins_url( '/assets/public/scripts/gdbc-public.js', self::$PLUGIN_MAIN_FILE );
		$this->formHiddenInputName = $settingsModuleInstance->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);

		foreach(array_keys((array)GdbcModulesController::getRegisteredModules()) as $moduleName)
		{
			$moduleInstance = GdbcModulesController::getPublicModuleInstance($moduleName);

			if( ! ($moduleInstance instanceof MchGdbcBaseModule) )
				continue;

			call_user_func(array($moduleInstance, 'registerAttachedHooks'));
		}

	}


	public function initializePlugin()
	{
		parent::initializePlugin();
	}

	public function registerAfterSetupThemeHooks()
	{
		add_action('login_enqueue_scripts', array($this, 'enqueuePublicScriptsAndStyles'));
	}

	public static function getInstance(array $arrPluginInfo)
	{
		static $gdbcPublicInstance = null;
		return null !== $gdbcPublicInstance ? $gdbcPublicInstance : $gdbcPublicInstance = new self($arrPluginInfo);
	}


	public function enqueuePublicScriptsAndStyles()
	{

		$printScriptsHook = (!!apply_filters('wpbruiser_scripts_in_head', false)) ? 'wp_print_scripts' : 'wp_print_footer_scripts';
		add_action($printScriptsHook, array($this, 'renderPublicScript'), 0 );

	}

	public function renderPublicScript()
	{
		static $scriptRendered = false;
		if($scriptRendered) {
			return;
		}

		$scriptRendered = true;
		echo GdbcBasePublicModule::getPublicScriptInlineContent();

	}



	private function __clone()
	{}

}