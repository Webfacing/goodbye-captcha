<?php
/*
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

class MchGdbcWordPressTransientsStorage extends MchGdbcCacheBaseStorage
{
	private $canUseDataBase = true;

	public function __construct($canUseDataBase = true)
	{
		$this->canUseDataBase = (boolean)$canUseDataBase;
		parent::__construct();

	}

	public function write($key, $value, $ttl = 0)
	{
		return set_transient($this->sanitizeTransientKey($key), $value, $ttl);
	}

	public function read($key)
	{
		$value = get_transient($this->sanitizeTransientKey($key));
		return false !== $value ? $value : null;
	}

	public function has($key)
	{
		return null !== $this->read($key);
	}


	public function delete($key)
	{
		return delete_transient($this->sanitizeTransientKey($key));
	}

	private function sanitizeTransientKey($key)
	{
		$key = sanitize_key( $key );
		return isset($key[40]) ? md5($key) : $key;
	}

	public function clear()
	{
		return ;
	}

	public function isAvailable()
	{
		if(null !== $this->isAvailable)
			return $this->isAvailable;

		if($this->canUseDataBase)
		{
			return $this->isAvailable = true;
		}

		return ($this->isAvailable = file_exists( WP_CONTENT_DIR . '/object-cache.php' ));

	}

	public function getStorageType()
	{
		return self::STORAGE_TYPE_DISK;
	}

}