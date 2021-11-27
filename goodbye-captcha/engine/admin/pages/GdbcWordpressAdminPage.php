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

class GdbcWordpressAdminPage  extends GdbcBaseAdminPage
{
	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);

		if(GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_WORDPRESS))
		{
			$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('WordPress Standard Forms Settings', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcWordPressAdminModule::getInstance(),
					)
				)
			));


			$this->registerGroupedModules(array(
				new MchGdbcGroupedModules(__('Tweaking WordPress', GoodByeCaptcha::PLUGIN_SLUG), array(
						GdbcWordPressTweaksAdminModule::getInstance(),
					)
				)
			));

		}

	}

	public function registerPageMetaBoxes()
	{
		parent::registerPageMetaBoxes();

		if($this->getPageLayoutColumns() <= 1)
			return;

		add_meta_box(
				"gdbc-help-xml-rpc",
				__('Disabling XML-RPC Service', GoodByeCaptcha::PLUGIN_SLUG),
				array( $this, 'renderXmlRpcMetaBox' ),
				$this->getAdminScreenId(),
				'side',
				'core',
				null
		);

		add_meta_box(
				"gdbc-help-xml-rpc-pingback",
				__('Disabling XML-RPC Pingbacks', GoodByeCaptcha::PLUGIN_SLUG),
				array( $this, 'renderXmlRpcPingbacksMetaBox' ),
				$this->getAdminScreenId(),
				'side',
				'core',
				null
		);

	}

	public function renderXmlRpcMetaBox()
	{
		$textInfo  = __('XML-RPC is used in WordPress as an API for third-party clients such as WordPress mobile apps, popular weblog clients like Windows Writer or popular plugins such as Jetpack.', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= __('If you use any application which calls your XML-RPC Service, <b>do not</b> Completely Disable XML-RPC. Otherwise, completely disabling XML-RPC it is strongly recommended.', GoodByeCaptcha::PLUGIN_SLUG);

		echo '<p style="text-align: justify">' . $textInfo . '</p>';


		$textInfo  = '<div class = "mch-meta-notice-info">';
		$textInfo .= __('<span><b>Feel free to Completely Disable XML-RPC if you are using Jetpack plugin</b>. WPBruiser simply allows Jetpack\'s XML-RPC Requests</span>', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= '</div>';

		echo $textInfo;

	}


	public function renderXmlRpcPingbacksMetaBox()
	{
		$textInfo  = __('XML-RPC is used for Pingbacks and Trackbacks which can be heavily misused to start <a href="https://blog.sucuri.net/2014/03/more-than-162000-wordpress-sites-used-for-distributed-denial-of-service-attack.html">DDoS attacks</a>.', GoodByeCaptcha::PLUGIN_SLUG);

		echo '<p style="text-align: justify">' . $textInfo . '</p>';

		$textInfo  = '<div class = "mch-meta-notice-info">';
		$textInfo .= __('<span>If the <b>Completely Disable XML-RPC</b> option is checked, enabling/disabling this option has no effect!</span>', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= '</div>';

		echo $textInfo;
	}


}