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

class MchGdbcCacheFileStorage extends MchGdbcCacheBaseStorage
{

    protected $path;
    protected $extension;
	protected $canSkipOnWriting = false;

    public function __construct($path, $canSkipOnWriting = false, $extension = 'cache')
    {
        $this->path = rtrim($path, '/\\');

        $extension = trim($extension, '.');

        $this->extension = empty($extension) ? '' : '.' . $extension;
    }


	public function isAvailable()
	{
		return (null === $this->isAvailable)
			? $this->isAvailable = (@file_exists($this->path) && @is_readable($this->path) && @is_writable($this->path))
			: $this->isAvailable;
	}

	public function getStorageType()
	{
		return self::STORAGE_TYPE_DISK;
	}


    private function getCacheFilePath($key)
    {
        return $this->path . DIRECTORY_SEPARATOR . $key . $this->extension;
    }

    public function write($key, $value, $ttl = 0)
    {
	    $ttl = (int)$ttl;
	    $ttl = ( ($ttl === 0) ? 31556926 : $ttl) + ( empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'] );

        $data = $ttl . PHP_EOL . $value;

	    $cacheFilePath = $this->getCacheFilePath($key);
	    $filePointer = @fopen($cacheFilePath, 'wb');
		if(false === $filePointer)
			return 0;

		if( false === flock( $filePointer, ( $this->canSkipOnWriting ? LOCK_EX|LOCK_NB : LOCK_EX ) ) ){
			fclose($filePointer);
			return 0;
		}
	    $bytesWritten = fwrite($filePointer, $data);
	    flock($filePointer, LOCK_UN);
	    fclose($filePointer);

	    return (false === $bytesWritten) ? 0 : $bytesWritten;
    }

    public function read($key)
    {
	    $cacheFilePath = $this->getCacheFilePath($key);

	    if(!@file_exists($cacheFilePath))
	        return null;

	    $filePointer = @fopen($cacheFilePath, 'rb');
	    if(false === $filePointer)
		    return null;

	    $isLocked = flock( $filePointer, LOCK_SH );

	    if( $isLocked && (( empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'] ) <= (int) trim(fgets($filePointer))) )
	    {
		    $cachedContent = '';

		    while(!feof($filePointer))
		    {
			    $cachedContent .= fgets($filePointer);
		    }

		    flock( $filePointer, LOCK_UN );
		    fclose( $filePointer );

		    return $cachedContent;
	    }


	    $isLocked ? flock( $filePointer, LOCK_UN ) : null;

	    fclose( $filePointer );

	    @unlink($cacheFilePath);

	    //PHP_VERSION_ID < 50300 ? @clearstatcache() : @clearstatcache(true, $cacheFilePath);

	    return null;

    }

    public function has($key)
    {
	    $cacheFilePath = $this->getCacheFilePath($key);

	    return @file_exists($cacheFilePath);

//		if( ! @file_exists($cacheFilePath) )
//			return false;

	    $filePointer = @fopen($cacheFilePath, 'rb');
	    if(false === $filePointer)
		    return false;

	    if(false === flock( $filePointer, LOCK_SH ))
	    {
		    fclose( $filePointer );
		    return false;
	    }

	    //echo trim(fgets($filePointer)) . ' - ' . time();exit;
	    $expired = ( ( empty($_SERVER['REQUEST_TIME']) ? time() : $_SERVER['REQUEST_TIME'] ) < (int) trim(fgets($filePointer)) );

	    flock( $filePointer, LOCK_UN );
	    fclose( $filePointer );

		if($expired)
		{
			@unlink($cacheFilePath);
			//PHP_VERSION_ID < 50300 ? @clearstatcache() : @clearstatcache(true, $cacheFilePath);
		}

        return $expired;
    }


    public function delete($key)
    {
	    $cacheFilePath = $this->getCacheFilePath($key);
	    return @file_exists($cacheFilePath) ? @unlink($cacheFilePath) : false;
    }

    public function clear()
    {
        $pattern = $this->path . '/' . $this->prefix . '*' . $this->extension;

        foreach(glob($pattern) as $file)
        {
            if(!is_dir($file))
            {
                if(@unlink($file) === false)
                {
                    return false;
                }
            }
        }

        return true;
    }

}
