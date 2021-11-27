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


class MchGdbcCacheZendDiskStorage extends MchGdbcCacheBaseStorage
{
	
    public function __construct()
    {}
    
    public function write($key, $value, $ttl = 0)
    {
        return zend_disk_cache_store($key, $value, $ttl);
    }
    
    public function read($key)
    {
        return zend_disk_cache_fetch($key);
    }
    
    public function has($key)
    {
        return (zend_disk_cache_fetch($key) !== false);
    }
    
    
    public function delete($key)
    {
        return zend_disk_cache_delete($key);
    }
    
    public function clear()
    {
        return zend_disk_cache_clear();
    }

	public function isAvailable()
	{
		return (null === $this->isAvailable)
			? $this->isAvailable = function_exists('zend_disk_cache_fetch')
			: $this->isAvailable;
	}

	public function getStorageType()
	{
		return self::STORAGE_TYPE_DISK;
	}
}
