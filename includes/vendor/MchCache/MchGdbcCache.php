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

class MchGdbcCache
{

    private $cacheStorage = null;

    public function __construct(MchGdbcCacheIStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public function read($key)
    {
        return $this->cacheStorage->read($key);
    }

    public function write($key, $value, $ttl = 0)
    {
        return $this->cacheStorage->write($key, $value, $ttl);
    }

    public function delete($key)
    {
        return $this->cacheStorage->delete($key);
    }

    public function has($key)
    {
        return $this->cacheStorage->has($key);
    }

    public function clear()
    {
        return $this->cacheStorage->clear();
    }

    public function getCacheStorage()
    {
	    return $this->cacheStorage;
    }

}
