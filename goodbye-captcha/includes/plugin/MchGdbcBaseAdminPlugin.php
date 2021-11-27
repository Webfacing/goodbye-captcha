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

abstract class MchGdbcBaseAdminPlugin extends MchGdbcBasePlugin
{

	protected $adminPagesList = array();

	public abstract function enqueueAdminScriptsAndStyles();

	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);

		add_action('admin_enqueue_scripts', array( $this, 'enqueueAdminScriptsAndStyles' ));
		add_action('admin_init', array($this, 'initializeAdminPlugin'));
	}


	public function registerAdminPage(MchGdbcBaseAdminPage $adminPage)
	{
		$this->adminPagesList[] = $adminPage;
	}

	public function getRegisteredAdminPages()
	{
		return $this->adminPagesList;
	}

	/**
	 * @return MchGdbcBaseAdminPage | null
	 */
	protected function getActivePage()
	{
		foreach($this->getRegisteredAdminPages() as $adminPage)
			if($adminPage->isActive())
				return $adminPage;

		return null;
	}

	public function initializeAdminPlugin()
	{}
	
	private function __clone()
	{}

}