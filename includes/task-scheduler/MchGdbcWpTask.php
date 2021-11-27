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

interface MchGdbcWpITask
{
	public function run();
	public function getTaskCronActionHookName();
}

abstract class MchGdbcWpTask implements MchGdbcWpITask
{
	private $isRecurringTask = false;
	private $runningInterval = null;

	public function __construct($runningInterval, $isRecurring)
	{
		$this->runningInterval = (int)floor(abs($runningInterval));
		$this->isRecurringTask = (bool)$isRecurring;
	}

	public function isRecurringTask()
	{
		return $this->isRecurringTask;
	}

	public function getRunningInterval()
	{
		return $this->runningInterval;
	}

	public function getTaskCronActionHookName()
	{
		return get_class($this) . '-' . $this->runningInterval . '-' . var_export($this->isRecurringTask, true);
	}
}
