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

abstract class MchGdbcBaseModule
{

	protected $arrRegisteredHooks = null;
	protected $moduleSettingsKey  = null;
	protected $isUsedNetworkWide  = false;

	protected function __construct()
	{
		$this->moduleSettingsKey  = str_replace(array('adminmodule', 'publicmodule'), '', MchGdbcUtils::replaceNonAlphaNumericCharacters(strtolower(get_class($this)), '-'));
		$this->moduleSettingsKey .= '-settings';

		$this->arrRegisteredHooks = array(1 => array(), 2 => array()); // 1 - key for actions, 2 - key for filters

		add_action('init', array($this, 'initializeModuleSettings'), PHP_INT_MAX);
	}

	public function initializeModuleSettings()
	{}

	protected function getAllSavedOptions($asNetworkOption)
	{
		$this->isUsedNetworkWide = (!!$asNetworkOption);
		return ($this->isUsedNetworkWide) ? (array)get_site_option($this->moduleSettingsKey, array()) : (array)get_option($this->moduleSettingsKey, array());
	}

	public function getOption($optionName, $asNetworkOption = true)
	{
		$arrAllSavedOptions = $this->getAllSavedOptions($asNetworkOption);
		return isset($arrAllSavedOptions[$optionName]) ? $arrAllSavedOptions[$optionName] : null;
	}

	public function addActionHook($actionName, array $arrCallback, $priority = 10, $numberOfArgumentsToPass = 1)
	{
		return $this->addHook(1, $actionName, $arrCallback, $priority, $numberOfArgumentsToPass);
	}

	public function addFilterHook($filterName, array $arrCallback, $priority = 10, $numberOfArgumentsToPass = 1)
	{
		return $this->addHook(2, $filterName, $arrCallback, $priority, $numberOfArgumentsToPass);
	}

	private function addHook($hookType, $hookName, array $arrCallback, $priority, $numberOfArgumentsToPass)
	{
		if(1 !== $hookType && 2 !== $hookType)
			return;

		static $hookCounter = 0;
		++$hookCounter;
		$hookIndex  = (1 === $hookType) ? 'a_' : 'f_';
		$hookIndex .= "$hookCounter-$hookType-$hookName-$priority-$numberOfArgumentsToPass";

		$this->arrRegisteredHooks[$hookType][$hookIndex] = array($hookName, $arrCallback, $priority, $numberOfArgumentsToPass);

		return $hookIndex;
	}

	public function removeHookByIndex($hookIndex)
	{
		foreach($this->arrRegisteredHooks as $hookType => $arrIndexedHooks)
		{
			if(!isset($arrIndexedHooks[$hookIndex][3]))
				continue;

			(1 === $hookType) //hookName       , $arrCallBack   , $priority      , $numberOfArguments
				? remove_action($arrIndexedHooks[$hookIndex][0], $arrIndexedHooks[$hookIndex][1], $arrIndexedHooks[$hookIndex][2], $arrIndexedHooks[$hookIndex][3])
				: remove_filter($arrIndexedHooks[$hookIndex][0], $arrIndexedHooks[$hookIndex][1], $arrIndexedHooks[$hookIndex][2], $arrIndexedHooks[$hookIndex][3]);

			unset($this->arrRegisteredHooks[$hookType][$hookIndex]);
		}
	}

	public function isHookRegistered($hookIndex)
	{
		foreach($this->arrRegisteredHooks as $hookType => $arrIndexedHooks)
			if(isset($arrIndexedHooks[$hookIndex][3]))
				return true;

		return false;
	}

	public function registerAttachedHooks()
	{
		static $arrAlreadyRegisteredHooks = array();

		foreach($this->arrRegisteredHooks as $hookType => $arrIndexedHooks)
		{
			foreach($arrIndexedHooks as $hookIndex => $arrHookInfo)
			{
				if(!isset($arrHookInfo[3]) || isset($arrAlreadyRegisteredHooks[$hookIndex]))
					continue;

				(1 === $hookType) //hookName    , $callBack      , $priority      , $numberOfArguments
					? add_action($arrHookInfo[0], $arrHookInfo[1], $arrHookInfo[2], $arrHookInfo[3])
					: add_filter($arrHookInfo[0], $arrHookInfo[1], $arrHookInfo[2], $arrHookInfo[3]);

				$arrAlreadyRegisteredHooks[$hookIndex] = true;
			}

		}
	}

}