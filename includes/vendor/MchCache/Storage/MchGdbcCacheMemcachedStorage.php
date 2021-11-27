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

class MchGdbcCacheMemcachedStorage extends MchGdbcCacheBaseStorage
{
	private $memCachedObject = null;
	private $useCompression = false;

    public function __construct($host = '127.0.0.1', $port = 11211, $useCompression = false)
    {

	    if(!$this->isAvailable())
		    return;


	    $this->memCachedObject = new Memcached();

	    $this->memCachedObject->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

	    $serverAlreadyAdded = false;
	    foreach((array)$this->memCachedObject->getServerList() as $arrServerInfo)
	    {
		    $serverAlreadyAdded = (isset($arrServerInfo['host']) && $arrServerInfo['host'] === $host);

		    if($serverAlreadyAdded)
			    break;

		    if(!$serverAlreadyAdded && $host === '127.0.0.1') {
			    $serverAlreadyAdded = ( isset( $arrServerInfo['host'] ) && $arrServerInfo['host'] === 'localhost' );
			    $serverAlreadyAdded ? $host = 'localhost' : null;
		    }

		    if(!$serverAlreadyAdded && $host === 'localhost') {
			    $serverAlreadyAdded = ( isset( $arrServerInfo['host'] ) && $arrServerInfo['host'] === '127.0.0.1' );
			    $serverAlreadyAdded ? $host = '127.0.0.1' : null;
		    }

		    if($serverAlreadyAdded)
			    break;

	    }


	    $this->memCachedObject->setOption(Memcached::OPT_COMPRESSION, !!$useCompression);

	    $this->isAvailable = ($serverAlreadyAdded) ? $serverAlreadyAdded : $this->memCachedObject->addServer($host, $port);

    }
    

    public function write($key, $value, $ttl = 0)
    {
	    (int)$ttl !== 0 ? $ttl += time() : null;

	    return $this->memCachedObject->set($key, $value, $ttl);

    }
    
    public function read($key)
    {
	    $data = $this->memCachedObject->get($key);
	    return (false === $data) ? null : $data;
    }
    

    public function has($key)
    {
        return ($this->read($key) !== null);
    }
    
    public function delete($key)
    {
        return $this->memCachedObject->delete($key);
    }
    
    public function clear()
    {
        return $this->memCachedObject->flush();
    }

	public function isAvailable()
	{
		return (null === $this->isAvailable)
			? $this->isAvailable = class_exists('Memcached')
			: $this->isAvailable;
	}

	public function getStorageType()
	{
		return self::STORAGE_TYPE_MEMORY;
	}

}
