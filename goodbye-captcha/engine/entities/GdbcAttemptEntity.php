<?php

/*
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

class GdbcAttemptEntity
{
	public $Id          = null;
	public $CreatedDate = null;
	public $ModuleId    = null;
	public $SectionId   = null;
	public $SiteId      = null;
	public $ClientIp    = null;
//	public $CountryId   = null;
	public $ReasonId    = null;
	public $Notes       = null;

	public function __construct($moduleId, $sectionId = 0)
	{
		$this->ModuleId = $moduleId;
		$this->SectionId = $sectionId;
	}

	public static function getInstanceFromRawData($rawDataEntity)
	{
		if(empty($rawDataEntity))
			return null;

		$jsonDecoded = is_string($rawDataEntity) ? json_decode($rawDataEntity, true) : null;
		if(null !== $jsonDecoded)
			$rawDataEntity = $jsonDecoded;

		$rawDataEntity = (null !== $jsonDecoded) ? $jsonDecoded : (array)$rawDataEntity;

		if(empty($rawDataEntity['ModuleId']))
			return null;

		$attemptEntity = new GdbcAttemptEntity($rawDataEntity['ModuleId']);
		foreach($rawDataEntity as $property=>$value)
		{
			if(!property_exists($attemptEntity, $property))
				continue;

			$attemptEntity->{$property} = $value;
		}

		return $attemptEntity;
	}
}
