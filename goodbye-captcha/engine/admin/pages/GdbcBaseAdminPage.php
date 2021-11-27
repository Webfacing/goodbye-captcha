<?php
/**
 * Copyright (C) 2016 Mihai Chelaru
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

abstract class GdbcBaseAdminPage extends MchGdbcBaseAdminPage
{
	protected static $arrPageInstances = array();

	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		parent::__construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug);
		$this->setPageLayoutColumns(2);
		self::$arrPageInstances[get_class($this)] = $this;
	}

	public function registerPageMetaBoxes()
	{

		parent::registerPageMetaBoxes();

		if($this->getPageLayoutColumns() <= 1)
			return;

		add_meta_box(
			"gdbc-help-metabox",
			__('Need help? Have questions...?', GoodByeCaptcha::PLUGIN_SLUG),
			array( $this, 'renderNeedHelpMetaBox' ),
			$this->getAdminScreenId(),
			'side',
			'core',
			null
		);

		$arrPremiumExtensions = self::getPremiumExtensions(true);
		if(!empty($arrPremiumExtensions) && is_array($arrPremiumExtensions))
		{
			add_meta_box(
				"gdbc-available-extension-metabox",
				__('Do you need more protection?', GoodByeCaptcha::PLUGIN_SLUG),
				array( $this, 'renderAvailableExtensionMetaBox' ),
				$this->getAdminScreenId(),
				'side',
				'core',
				null
			);

		}


//		if( ! GdbcSettingsAdminModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDE_SUBSCRIBE_FORM) )
//		{
//			add_meta_box(
//				"gdbc-subscribe-metabox",
//				__('Get exclusive email updates', GoodByeCaptcha::PLUGIN_SLUG),
//				array($this, 'renderSubscriptionMetaBox'),
//				$this->getAdminScreenId(),
//				'side',
//				'core',
//				null
//			);
//		}

		add_meta_box(
			"gdbc-help-metabox-test",
			__('How to verify if it works?', GoodByeCaptcha::PLUGIN_SLUG),
			array( $this, 'renderHowToTestMetaBox' ),
			$this->getAdminScreenId(),
			'side',
			'low',
			null
		);

	}

	public function renderAvailableExtensionMetaBox()
	{
		$arrPremiumExtensions = self::getPremiumExtensions(true);

		if(!empty($arrPremiumExtensions) && is_array($arrPremiumExtensions))
			shuffle($arrPremiumExtensions);
		else
			return;

		$availableExtension =reset($arrPremiumExtensions);

		$moduleName = GdbcModulesController::getModuleDisplayName($availableExtension['module']);

		$outputHtml  = "<h3 style=\"text-align:center\">There is a WPBruiser Extension for</h3>";
		$outputHtml .= "<h3 style=\"text-align:center\"><a  href=\"{$availableExtension['url']}\">$moduleName</a></h3>";
		$outputHtml .= '<div><img class="logo-help" src="' . $availableExtension['img-src'] . '" /></div>';
		$outputHtml .= '<p class="contact-help"> <a class = "button-primary" href="' . $availableExtension['url'] . '" target="_blank">'. __('Get this Extension', GoodByeCaptcha::PLUGIN_SLUG) .'</a></p>';

		echo $outputHtml;

	}

	public function renderSubscriptionMetaBox()
	{
		$emailValue = esc_attr(MchGdbcWpUtils::getAdminEmailAddress());

		$subscribeFormHtml  = '<form id = "gdbc-subscribe-frm">';
		$subscribeFormHtml .= '<p>';

		$subscribeFormHtml .= '<label for = "gdbc-Email">' . __('Your Email Address', GoodByeCaptcha::PLUGIN_SLUG) . '</label>';
		$subscribeFormHtml .= '<input id = "gdbc-Email" value="'.$emailValue.'" class="regular-text"  type="email" required="" name="EMAIL" autocomplete="off" autocorrect="off" />';

		$subscribeFormHtml .= '</p>';

		$buttonValue = esc_attr(__('Subscribe', GoodByeCaptcha::PLUGIN_SLUG));

		$subscribeFormHtml .= '<input type="submit" value="'.$buttonValue.'" class="button button-primary" />';

		$subscribeFormHtml .= '</p>';

		$subscribeFormHtml .= '<input type="hidden" name="b_5a2f4e669c2e2427b7e6d8ad9_5da2802c23" tabindex="-1" value="">';

		$subscribeFormHtml .= '</form>';

		echo $subscribeFormHtml;
	}

	public function renderNeedHelpMetaBox()
	{
		$img = plugins_url('/assets/admin/images/wpbr-logo.png', GoodByeCaptcha::getMainFilePath());

		$display_div = '<div><img class="logo-help" src="' . esc_attr($img) . '" /></div>';

		$display_div .= '<p class="contact-help"> <a class = "button-primary" href="' . GoodByeCaptcha::PLUGIN_SITE_URL . '/contact" target="_blank">Contact Us</a></p>';

		echo $display_div;
	}

	public function renderHowToTestMetaBox()
	{
		$settingsPageUrl      = isset(self::$arrPageInstances['GdbcSettingsAdminPage']) ? self::$arrPageInstances['GdbcSettingsAdminPage']->getAdminUrl() : '';
		$notificationsPageUrl = isset(self::$arrPageInstances['GdbcNotificationsAdminPage']) ? self::$arrPageInstances['GdbcNotificationsAdminPage']->getAdminUrl() : '';

		$textInfo  = __('In order to verify if WPBruiser works as expected, just go to, ', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= MchGdbcHtmlUtils::createAnchorElement(__('Settings Page', GoodByeCaptcha::PLUGIN_SLUG), array('href' => $settingsPageUrl));
		$textInfo .= __(' and <b>Switch the plugin to Test Mode</b>. <br/>While in test mode, the plugin just verifies if it can properly protect the enabled options and sends email notifications to the email address you set in ', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= MchGdbcHtmlUtils::createAnchorElement(__('Notifications Page', GoodByeCaptcha::PLUGIN_SLUG), array('href' => $notificationsPageUrl));

		$textInfo .= __(' For example, if you want to test the login form protection, just enable protection for the WordPress Login Form, logout from your dashboard and login again. The plugin will send an email notification letting you know if you can keep the protection activated.', GoodByeCaptcha::PLUGIN_SLUG);

		$textInfo .= __(' In case something goes wrong, a warning message will be shown.', GoodByeCaptcha::PLUGIN_SLUG);

		echo '<p style="text-align: justify">' . $textInfo . "</p>";

		$textInfo  = '<div class = "mch-meta-notice-warning">';
		$textInfo .= __('<b>Turn off Test Mode as soon as you\'re done with testing!</b>', GoodByeCaptcha::PLUGIN_SLUG);
		$textInfo .= '</div>';

		echo $textInfo;

	}


	public static function getPremiumExtensions($forRightSide = false)
	{

		$arrPremiumExtensions = array(

			array(
				'name'          => 'Contact Form 7',
				'url'           => 'http://www.wpbruiser.com/downloads/contact-form-7/',
				'img-src'       => '//ps.w.org/contact-form-7/assets/icon-128x128.png',
				'descr'         => 'Let WPBruiser protect your Contact Form 7 form without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url'  => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module'        => GdbcModulesController::MODULE_CONTACT_FORM_7,
				'detected'      => GoodByeCaptchaUtils::isContactForm7Activated(),
			),

			array(
				'name'          => 'WooCommerce',
				'url'           => 'http://www.wpbruiser.com/downloads/woocommerce/',
				'img-src'       => '//ps.w.org/woocommerce/assets/icon-128x128.png',
				'descr'         => 'Let WPBruiser protect your WooCommerce forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('eCommerce', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url'  => 'http://www.wpbruiser.com/downloads/category/ecommerce/',
				'module'        => GdbcModulesController::MODULE_WOOCOMMERCE,
				'detected'      => GoodByeCaptchaUtils::isWooCommerceActivated(),
			),

			array(
				'name' => 'Country Blocking',
				'url'  => 'http://www.wpbruiser.com/downloads/country-blocking/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/country-blocking-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'Add an additional layer of security to your WordPress site by blocking users/spambots from specific countries.',
				'category-name' => __('Utilities', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url'  => 'http://www.wpbruiser.com/downloads/category/utilities/',
				'module'        => GdbcModulesController::MODULE_COUNTRY_BLOCKING,
				'detected'      => !GdbcModulesController::isModuleRegistered(GdbcModulesController::MODULE_COUNTRY_BLOCKING)
			),

			array(
				'name' => 'Gravity Forms',
				'url'  => 'http://www.wpbruiser.com/downloads/gravity-forms/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/gravity-forms-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'Let WPBruiser protect your Gravity Forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_GRAVITY_FORMS,
				'detected' => GoodByeCaptchaUtils::isGravityFormsActivated(),

			),
			
			array(
				'name' => 'WPForms',
				'url'  => 'http://www.wpbruiser.com/downloads/wpforms/',
				'img-src' => 'https://ps.w.org/wpforms-lite/assets/icon-128x128.png',
				'descr' => 'All your WPForms will be protected by WPBruiser without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_WP_FORMS,
				'detected' => function_exists('wpforms'),
			
			),
			
			
			array(
				'name' => 'Ninja Forms',
				'url'  => 'http://www.wpbruiser.com/downloads/ninja-forms/',
				'img-src' =>  esc_attr(plugins_url('/assets/admin/images/ninja-forms-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'Let WPBruiser protect your Ninja Forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_NINJA_FORMS,
				'detected' => GoodByeCaptchaUtils::isNinjaFormsActivated(),

			),

			array(
				'name' => 'Formidable Forms',
				'url'  => 'http://www.wpbruiser.com/downloads/formidable-forms/',
				'img-src' => '//ps.w.org/formidable/assets/icon-128x128.png',
				'descr' => 'Let WPBruiser protect your Formidable Forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_FORMIDABLE_FORMS,
				'detected' => GoodByeCaptchaUtils::isFormidableFormsActivated(),

			),

			array(
				'name' => 'Fast Secure Contact Form',
				'url'  => 'http://www.wpbruiser.com/downloads/fast-secure-contact-form/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/fscf-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'Let WPBruiser protect your Fast Secure Contact Form without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_FAST_SECURE_FORM,
				'detected' => GoodByeCaptchaUtils::isFastSecureFormActivated(),

			),

			array(
				'name' => 'Quform',
				'url'  => 'http://www.wpbruiser.com/downloads/quform/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/quform-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'Let WPBruiser protect your forms created with Quform plugin without any hard to read captcha images or any other user interaction',
				'category-name' => __('Contact Forms', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/contact-forms/',
				'module' => GdbcModulesController::MODULE_QUFORM,
				'detected' => GoodByeCaptchaUtils::isQuFormActivated(),

			),

			array(
				'name' => 'User Profiles Made Easy',
				'url'  => 'http://www.wpbruiser.com/downloads/upme/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/upme-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'WPBruiser will protect the Login and Registration forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Membership', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/membership/',
				'module' => GdbcModulesController::MODULE_UPME,
				'detected' => GoodByeCaptchaUtils::isUserProfileMadeEasyActivated(),

			),

			array(
				'name' => 'MemberPress',
				'url'  => 'http://www.wpbruiser.com/downloads/memberpress/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/memberpress-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'WPBruiser will protect the Login and Registration forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Membership', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/membership/',
				'module' => GdbcModulesController::MODULE_MEMBER_PRESS,
				'detected' => GoodByeCaptchaUtils::isMemberPressPluginActivated(),

			),

			array(
				'name' => 'BuddyPress',
				'url'  => 'http://www.wpbruiser.com/downloads/buddypress/',
				'img-src' => '//ps.w.org/buddypress/assets/icon.svg',
				'descr' => 'WPBruiser will protect the Registration form without any hard to read captcha images or any other user interaction',
				'category-name' => __('Membership', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/membership/',
				'module' => GdbcModulesController::MODULE_BUDDY_PRESS,
				'detected' => GoodByeCaptchaUtils::isBuddyPressPluginActivated(),

			),

			array(
				'name' => 'User Pro',
				'url'  => 'http://www.wpbruiser.com/downloads/userpro/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/userpro-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'WPBruiser will protect the Login, Registration and Lost Password forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('Membership', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/membership/',
				'module' => GdbcModulesController::MODULE_USER_PRO,
				'detected' => GoodByeCaptchaUtils::isUserProPluginActivated(),

			),

			array(
				'name' => 'MailPoet',
				'url'  => 'http://www.wpbruiser.com/downloads/mailpoet/',
				'img-src' => '//ps.w.org/wysija-newsletters/assets/icon.svg',
				'descr' => 'Let WPBruiser protect your MailPoet subscriptions forms. All fake subscriptions will be rejected before adding them to your MailPoet lists',
				'category-name' => __('Subscriptions', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/subscriptions/',
				'module' => GdbcModulesController::MODULE_MAIL_POET,
				'detected' => GoodByeCaptchaUtils::isMailPoetActivated(),
			),

			array(
				'name' => 'Easy Forms for MailChimp',
				'url'  => 'http://www.wpbruiser.com/downloads/easy-forms-for-mailchimp/',
				'img-src' => '//ps.w.org/yikes-inc-easy-mailchimp-extender/assets/icon-128x128.png',
				'descr' => 'Let WPBruiser protect your Easy Forms for MailChimp subscriptions forms. All fake subscriptions will be rejected before adding them to your MailChimp lists',
				'category-name' => __('Subscriptions', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url' => 'http://www.wpbruiser.com/downloads/category/subscriptions/',
				'module' => GdbcModulesController::MODULE_EASY_FORMS_FOR_MAILCHIMP,
				'detected' => GoodByeCaptchaUtils::isEasyFormsForMailChimpPluginActivated(),
			),

			array(
				'name' => 'Easy Digital Downloads',
				'url'  => 'http://www.wpbruiser.com/downloads/easy-digital-downloads/',
				'img-src' => '//ps.w.org/easy-digital-downloads/assets/icon-128x128.png',
				'descr' => 'WPBruiser will protect the Login and Registration forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('eCommerce', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url'  => 'http://www.wpbruiser.com/downloads/category/ecommerce/',
				'module'        => GdbcModulesController::MODULE_EASY_DIGITAL_DOWNLOADS,
				'detected'      => GoodByeCaptchaUtils::isEasyDigitalDownloadsActivated()
			),

			array(
				'name' => 'AffiliateWP',
				'url'  => 'http://www.wpbruiser.com/downloads/affiliatewp/',
				'img-src' => esc_attr(plugins_url('/assets/admin/images/affiliatewp-logo-128x128.png', GoodByeCaptcha::getMainFilePath())),
				'descr' => 'WPBruiser will protect the Login and Registration forms without any hard to read captcha images or any other user interaction',
				'category-name' => __('eCommerce', GoodByeCaptcha::PLUGIN_SLUG),
				'category-url'  => 'http://www.wpbruiser.com/downloads/category/ecommerce/',
				'module'        => GdbcModulesController::MODULE_AFFILIATE_WP,
				'detected'      => GoodByeCaptchaUtils::isAffiliateWPActivated()
			),

		);

		foreach($arrPremiumExtensions as $index => $arrExtensionInfo)
		{
			if(GdbcModulesController::isModuleIncludedInProBundle($arrExtensionInfo['module']))
				unset($arrPremiumExtensions[$index]);
		}

		if( !$forRightSide ) {
			return $arrPremiumExtensions;
		}

		foreach($arrPremiumExtensions as $index => $arrExtensionInfo)
		{
			if(empty($arrExtensionInfo['detected']) || GdbcModulesController::isModuleRegistered($arrExtensionInfo['module']))
				unset($arrPremiumExtensions[$index]);
		}

		return $arrPremiumExtensions;

	}

	public static function getPageUrlByName($pageName)
	{
		if(empty($pageName))
			return null;

		foreach (array_keys(self::$arrPageInstances) as $pageClassName)
		{
			if(strcasecmp($pageName, str_replace(array('Gdbc', 'AdminPage'), '', $pageClassName)) !== 0)
				continue;

			return self::$arrPageInstances[$pageClassName]->getAdminUrl();
		}

		return null;
	}
}