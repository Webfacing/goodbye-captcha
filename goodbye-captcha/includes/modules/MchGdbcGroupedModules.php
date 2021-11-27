<?php
/**
 * Copyright (C) 2016 Mihai Chelaru
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

class MchGdbcGroupedModules
{
	private $groupedModulesList = null;
	private $groupTitle         = null;
	private $groupDescription   = null;

	public function __construct($groupTitle = null, array $groupedModulesList = null)
	{
		$this->groupTitle = $groupTitle;
		$this->groupedModulesList = array();

		foreach((array)$groupedModulesList as $adminModule)
			if($adminModule instanceof MchGdbcBaseAdminModule)
				$this->groupedModulesList[] = $adminModule;

	}

	public function addModule(MchGdbcBaseAdminModule $adminModule)
	{
		$this->groupedModulesList[] = $adminModule;
	}

	public function getGroupedModules()
	{
		return $this->groupedModulesList;
	}

	public function hasModules()
	{
		return isset($this->groupedModulesList[0]);
	}

	public function getGroupTitle()
	{
		return $this->groupTitle;
	}

	public function getGroupDescription()
	{
		return $this->groupDescription;
	}

	public function setGroupTitle( $groupTitle ) {
		$this->groupTitle = $groupTitle;
	}

	public function setGroupDescription( $groupDescription ) {
		$this->groupDescription = $groupDescription;
	}

}