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

class GdbcWelcomeAdminPage extends GdbcBaseAdminPage
{
	CONST GDBC_WELCOME_PAGE_CACHE_KEY   = __CLASS__;

	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		$this->setPageLayoutColumns(1);

		if(null !== GoodByeCaptchaUtils::getAvailableCacheStorage(null) && GoodByeCaptchaUtils::getAvailableCacheStorage(null)->has(self::GDBC_WELCOME_PAGE_CACHE_KEY))
		{
			GoodByeCaptchaUtils::getAvailableCacheStorage(null)->delete(self::GDBC_WELCOME_PAGE_CACHE_KEY);
		}
	}

	public function renderPageContent()
	{
		$templateFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'welcome-page.html';

		if( ! MchGdbcWpUtils::fileExists($templateFilePath) )
			return;

		$aboutPluginText = 'Thank you for installing! WPBruiser is an anti-spam and security plugin based on algorithms that identify spam bots without any annoying and hard to read captcha images.';


		$arrPageDirectives = array(
			'{page-main-title}' => __('Welcome to WPBruiser ' . GoodByeCaptcha::PLUGIN_VERSION, GoodByeCaptcha::PLUGIN_SLUG),
			'{about-plugin}'    => __($aboutPluginText, GoodByeCaptcha::PLUGIN_SLUG),
		);


		echo str_replace(array_keys($arrPageDirectives), array_values($arrPageDirectives), file_get_contents($templateFilePath));


	}




}