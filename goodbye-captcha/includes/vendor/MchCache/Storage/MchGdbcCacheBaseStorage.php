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


interface MchGdbcCacheIStorage
{

	CONST STORAGE_TYPE_MEMORY = 1;
	CONST STORAGE_TYPE_DISK   = 2;
	CONST STORAGE_TYPE_DB     = 3;


	public function isAvailable();

	public function getStorageType();

	public function read($key);

	public function write($key, $value, $ttl = 0);

	public function delete($key);

	public function has($key);

	public function clear();

}

abstract class MchGdbcCacheBaseStorage implements MchGdbcCacheIStorage
{
	protected $isAvailable = null;

	public function __construct()
	{}
}