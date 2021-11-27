<?php

/*
 * Copyright (C) 2016 Mihai Chelaru
 */

final class GdbcProxyHeadersPublicModule extends GdbcBasePublicModule
{

	protected function __construct()
	{
		parent::__construct();
	}


	protected function getModuleId()
	{
		return GdbcModulesController::getModuleIdByName(GdbcModulesController::MODULE_PROXY_HEADERS);
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}