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

abstract class GdbcBasePublicModule extends MchGdbcBasePublicModule
{
	CONST REFRESH_TOKENS_SCRIPT_HANDLE = 'wp-bruiser-refresh-tokens';
	CONST MAIN_PUBLIC_SCRIPT_HANDLE    = 'wp-bruiser-public-script';

	private   $submittedData = null;
	protected $attemptEntity = null;

	/**
	 * @return int
	 */
	protected abstract function getModuleId();

	protected function __construct()
	{
		parent::__construct();

		$this->submittedData = array();
		$this->attemptEntity = new GdbcAttemptEntity($this->getModuleId());
	}

	public static function getMainScriptUrl()
	{
		return plugins_url( '/assets/public/scripts/gdbc-public.js', GoodByeCaptcha::getMainFilePath() );
	}

	public static function getRefreshTokensScriptFileContent($appendScriptTag = true)
	{
		$filePath =  dirname(GoodByeCaptcha::getMainFilePath()) . '/assets/public/scripts/gdbc-refresh-tokens.js';

		if(!@is_readable($filePath))
			return null;

		return $appendScriptTag ? '<script type="text/javascript">' . file_get_contents($filePath) . '</script>' : file_get_contents($filePath);

	}


	public static function getRefreshTokensScriptUrl()
	{
		return plugins_url('/assets/public/scripts/gdbc-refresh-tokens.js', GoodByeCaptcha::getMainFilePath());
	}

	public static function getPublicScriptInlineContent($addScriptTag = true)
	{
		$clientUrl = esc_attr(esc_url(home_url('/', MchGdbcWpUtils::isSslRequest() ? 'https' : 'http') . '?gdbc-client=' . GoodByeCaptcha::PLUGIN_VERSION . '-'));

		$inlineContent  = '<script type="text/javascript">';
		$inlineContent .= "!function(t,e){\"use strict\";function n(){if(!a){a=!0;for(var t=0;t<d.length;t++)d[t].fn.call(window,d[t].ctx);d=[]}}function o(){\"complete\"===document.readyState&&n()}t=t||\"docReady\",e=e||window;var d=[],a=!1,c=!1;e[t]=function(t,e){return a?void setTimeout(function(){t(e)},1):(d.push({fn:t,ctx:e}),void(\"complete\"===document.readyState||!document.attachEvent&&\"interactive\"===document.readyState?setTimeout(n,1):c||(document.addEventListener?(document.addEventListener(\"DOMContentLoaded\",n,!1),window.addEventListener(\"load\",n,!1)):(document.attachEvent(\"onreadystatechange\",o),window.attachEvent(\"onload\",n)),c=!0)))}}(\"wpBruiserDocReady\",window);";
		$inlineContent .= "
			(function(){var wpbrLoader = (function(){var g=document,b=g.createElement('script'),c=g.scripts[0];b.async=1;b.src='$clientUrl'+(new Date()).getTime();c.parentNode.insertBefore(b,c);});wpBruiserDocReady(wpbrLoader);window.onunload=function(){};window.addEventListener('pageshow',function(event){if(event.persisted){(typeof window.WPBruiserClient==='undefined')?wpbrLoader():window.WPBruiserClient.requestTokens();}},false);})();
";

		$inlineContent .= '</script>';

		return $inlineContent;
	}


	protected function getAllSavedOptions($asNetworkOption = true)
	{
		return parent::getAllSavedOptions(GoodByeCaptcha::isNetworkActivated());
	}

	public function getOption($optionName, $asNetworkOption = true)
	{
		return parent::getOption($optionName, GoodByeCaptcha::isNetworkActivated());
	}

	protected function setSubmittedData(array $submittedData)
	{
		$this->submittedData = $submittedData;
	}

	protected function getSubmittedData()
	{
		return $this->submittedData;
	}

	/**
	 * @return GdbcAttemptEntity | null
	 */

	protected function getAttemptEntity()
	{
		return $this->attemptEntity;
	}


	public function getOptionIdByOptionName($settingOptionName)
	{

		$adminModuleInstance = GdbcModulesController::getAdminModuleInstance(GdbcModulesController::getModuleNameById($this->getModuleId()));
		if(null === $adminModuleInstance)
			return 0;

		return $adminModuleInstance->getOptionIdByOptionName($settingOptionName);

	}


	public function renderTokenFieldIntoForm()
	{
		echo $this->getTokenFieldHtml();
	}

	public function getTokenFieldHtml()
	{
		$hiddenField = GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);
		if(!isset($hiddenField[0]))
		{
			GdbcSettingsAdminModule::getInstance()->saveSecuredOptions(true);
			$hiddenField = GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);
		}

		return '<input type="hidden" autocomplete="off" autocorrect="off" name="' . esc_attr( $hiddenField ) . '" value="" />';
	}

}
