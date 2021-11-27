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

class MchGdbcCacheMemcacheStorage extends MchGdbcCacheBaseStorage
{
	private $memCacheObject = null;
	private $useCompression = false;

    public function __construct($host = '127.0.0.1', $port = 11211, $useCompression = false)
    {
	    if(!$this->isAvailable())
	        return;

	    $this->memCacheObject = new Memcache();

	    @$this->memCacheObject->connect($host, $port);

		$this->isAvailable = (bool)@$this->memCacheObject->getServerStatus($host);

	    $this->useCompression = empty($useCompression) ? 0 : MEMCACHE_COMPRESSED;;
    }
    

    public function write($key, $value, $ttl = 0)
    {
	    (int)$ttl !== 0 ? $ttl += time() : null;

	    return $this->memCacheObject->set($key, $value, $this->useCompression, $ttl);

    }


    public function read($key)
    {
        return $this->memCacheObject->get($key);
    }
    
    public function has($key)
    {
        return false !== $this->memCacheObject->get($key);
    }
    

    public function delete($key)
    {
        return $this->memCacheObject->delete($key);
    }
    
    public function clear()
    {
        return $this->memCacheObject->flush();
    }

	public function isAvailable()
	{
		return (null === $this->isAvailable)
			? $this->isAvailable = class_exists('Memcache')
			: $this->isAvailable;
	}

	public function getStorageType()
	{
		return self::STORAGE_TYPE_MEMORY;
	}

	public function __destruct()
	{
		if(null === $this->memCacheObject)
			return;

		$this->memCacheObject->close();
	}

}
