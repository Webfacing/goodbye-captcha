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

class GdbcWordPressTweaksAdminModule extends GdbcBaseAdminModule
{

	CONST WORDPRESS_REMOVE_RSD_HEADER           = 'HideRSDHeader';
	CONST WORDPRESS_REMOVE_WLW_HEADER           = 'HideWLWHeader';

	CONST WORDPRESS_HIDE_VERSION                = 'HideVersion';

	CONST WORDPRESS_XML_RPC_FULLY_DISABLED      = 'XmlRpcFullyDisabled';
	CONST WORDPRESS_XML_RPC_PINGBACKS_DISABLED   = 'XmlRpcPingDisabled';

	CONST WORDPRESS_COMMENTS_TRACKBACKS_DISABLED   = 'CommentsTrackBackDisabled';

	CONST WORDPRESS_COMMENTS_FORM_WEBSITE_FIELD = 'CommentsWebsiteFieldHidden';
	CONST WORDPRESS_COMMENTS_FORM_NOTES_FIELDS  = 'CommentsNoteHidden'; // hides allowed tags and text like "Your email address will not be published"

	protected function __construct()
	{
		parent::__construct();
	}

	public function getDefaultOptions()
	{
		static $arrDefaultSettingOptions = null;
		if(null !== $arrDefaultSettingOptions)
			return $arrDefaultSettingOptions;

		$arrDefaultSettingOptions = array(

			self::WORDPRESS_HIDE_VERSION => array(
				'Id'          => 1,
				'Value'       => NULL,
				'LabelText'   => __('Hide WordPress Version', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('This will hide your WordPress version information from potential attackers', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_REMOVE_RSD_HEADER => array(
				'Id'          => 2,
				'Value'       => NULL,
				'LabelText'   => __('Remove RSD Header', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Removes the RSD (Really Simple Discovery) header. The header is useful only if your blog is integrated with external services such as Flickr', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_REMOVE_WLW_HEADER => array(
				'Id'          => 3,
				'Value'       => NULL,
				'LabelText'   => __('Remove WLW Header', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Removes the WLW (Windows Live Writer Header) header. The header is useful only if you use Windows Live Writer', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_XML_RPC_FULLY_DISABLED => array(
				'Id'          => 4,
				'Value'       => NULL,
				'LabelText'   => __('Completely Disable XML-RPC', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('<b>It seamlessly works with Jetpack plugin</b>. Do not enable if there is other system such as Android/IOS app that uses your XML-RPC service.', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_XML_RPC_PINGBACKS_DISABLED => array(
				'Id'          => 5,
				'Value'       => NULL,
				'LabelText'   => __('Disable XML-RPC Pingbacks', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Removes the Pingbacks methods from the XML-RPC service. This will also remove the X-Pingback header', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),


//			self::WORDPRESS_COMMENTS_TRACKBACKS_DISABLED => array(
//				'Id'          => 6,
//				'Value'       => NULL,
//				'LabelText'   => __('Disable Comments Trackbacks', GoodByeCaptcha::PLUGIN_SLUG),
//				'Description' => __('Blocks all Comments Trackbacks', GoodByeCaptcha::PLUGIN_SLUG),
//				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
//			),


			self::WORDPRESS_COMMENTS_FORM_WEBSITE_FIELD => array(
				'Id'          => 7,
				'Value'       => NULL,
				'LabelText'   => __('Hide Comments Website Field', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Hides Comments Form Website Url', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),


			self::WORDPRESS_COMMENTS_FORM_NOTES_FIELDS => array(
				'Id'          => 8,
				'Value'       => NULL,
				'LabelText'   => __('Hide Comments Form Notes Fields', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Hides form allowed tags and text like "Your email address will not be published"', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

		);

		return $arrDefaultSettingOptions;

	}

	public  function validateModuleSettingsFields($arrSettingOptions)
	{
		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));
		return $arrSettingOptions;
	}

//	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
//	{
//		echo '<h3>' . __('Tweaking WordPress', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
//	}


	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		return null;
	}

	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}