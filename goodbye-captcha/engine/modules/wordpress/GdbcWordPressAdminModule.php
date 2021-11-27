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

class GdbcWordPressAdminModule extends GdbcBaseAdminModule
{
	CONST WORDPRESS_LOGIN_FORM            = 'LoginActivated';
	CONST WORDPRESS_COMMENTS_FORM         = 'CommentsActivated';
	CONST WORDPRESS_LOST_PASSWORD_FORM    = 'LostPasswordActivated';
	CONST WORDPRESS_REGISTRATION_FORM     = 'UserRegisterActivated';
	CONST WORDPRESS_LOGIN_XML_RPC         = 'LoginXmlRpc';

	CONST WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH = "CommentsContentLength";
	CONST WORDPRESS_COMMENTS_FORM_NAME_LENGTH    = "CommentsNameLength";
	CONST WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH   = "CommentsEmailLength";
	CONST WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH = "CommentsWebSiteLength";


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

			self::WORDPRESS_LOGIN_FORM => array(
				'Id'          => 2,
				'Value'       => NULL,
				'LabelText'   => __('Protect Login Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Login', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_LOST_PASSWORD_FORM  => array(
				'Id'          => 3,
				'Value'       => NULL,
				'LabelText'   => __('Protect Lost Password Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Lost Password', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_REGISTRATION_FORM => array(
				'Id'          => 4,
				'Value'       => NULL,
				'LabelText'   => __('Protect Registration Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Registration', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_LOGIN_XML_RPC => array(
				'Id'          => 5,
				'Value'       => NULL,
				'LabelText'   => null,
				'DisplayText' => __('XML-RPC Login', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_HIDDEN
			),

			self::WORDPRESS_COMMENTS_FORM  => array(
				'Id'          => 1,
				'Value'       => NULL,
				'LabelText'   => __('Protect Comments Form', GoodByeCaptcha::PLUGIN_SLUG),
				'DisplayText' => __('Comments', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('If this option is not selected the Fields Maximum Length options have no effect!', GoodByeCaptcha::PLUGIN_SLUG),
				//'Description' => __('It seamlessly working with <a target="_blank" href="https://wordpress.org/plugins/wpdiscuz">wpDiscuz</a> - the AJAX realtime commenting system', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_CHECKBOX
			),

			self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH => array(
				'Id'          => 6,
				'Value'       => 65525,
				'LabelText'   => __('Comment Field Maximum Length', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Sets the maximum number of characters for the Comment field', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

			self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH => array(
				'Id'          => 7,
				'Value'       => 245,
				'LabelText'   => __('Comment Name Field Maximum Length', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Sets the maximum number of characters for the Name field', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

			self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH => array(
				'Id'          => 8,
				'Value'       => 100,
				'LabelText'   => __('Comment Email Field Maximum Length', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Sets the maximum number of characters for the Email field', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),

			self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH => array(
				'Id'          => 9,
				'Value'       => 200,
				'LabelText'   => __('Comment Website Field Maximum Length', GoodByeCaptcha::PLUGIN_SLUG),
				'Description' => __('Sets the maximum number of characters for the Website field', GoodByeCaptcha::PLUGIN_SLUG),
				'InputType'   => MchGdbcHtmlUtils::FORM_ELEMENT_INPUT_TEXT
			),


		);
									 // Available since WordPress 4.5
		$arrCommentFieldsMaxLength = function_exists('wp_get_comment_fields_max_lengths') ? wp_get_comment_fields_max_lengths() : $this->getCommentFieldsMaximumLengths();

		if(!empty($arrCommentFieldsMaxLength['comment_content']))
			$arrDefaultSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]['Value'] = (int)$arrCommentFieldsMaxLength['comment_content'];

		if(!empty($arrCommentFieldsMaxLength['comment_author_url']))
			$arrDefaultSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]['Value'] = (int)$arrCommentFieldsMaxLength['comment_author_url'];

		if(!empty($arrCommentFieldsMaxLength['comment_author_email']))
			$arrDefaultSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]['Value']   = (int)$arrCommentFieldsMaxLength['comment_author_email'];

		if(!empty($arrCommentFieldsMaxLength['comment_author']))
			$arrDefaultSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]['Value']    = (int)$arrCommentFieldsMaxLength['comment_author'];

		return $arrDefaultSettingOptions;

	}

	public  function validateModuleSettingsFields($arrSettingOptions)
	{

		$arrSettingOptions = array_map('sanitize_text_field', (array)$arrSettingOptions);
		$arrDefaultOptionsValues = $this->getDefaultOptionsValues();

		$arrOldSettingsValues = $this->getAllSavedOptions();

		if(!empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]))
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH] = absint($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]);
		else
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH] = 0;

		if(!empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]))
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH] = absint($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]);
		else
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH] = 0;

		if(!empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]))
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH] = absint($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]);
		else
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH] = 0;

		if(!empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]))
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH] = absint($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]);
		else
			$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH] = 0;


		if(empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]) || $arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH] > $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH])
		{
			$this->registerErrorMessage(__("The Comment Field Maximum Length should be a numeric value between 1 and {$arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]} !", GoodByeCaptcha::PLUGIN_SLUG));

			if(!empty($arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH]))
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH] = $arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH];
			else
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_CONTENT_LENGTH] = $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH];

		}

		if(empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]) || $arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH] > $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH])
		{
			$this->registerErrorMessage(__("The Email Field Maximum Length should be a numeric value between 1 and {$arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]} !", GoodByeCaptcha::PLUGIN_SLUG));
			if(!empty($arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH]))
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH] = $arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH];
			else
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH] = $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_EMAIL_LENGTH];
		}

		if(empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]) || $arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH] > $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH])
		{
			$this->registerErrorMessage(__("The Email Field Maximum Length should be a numeric value between 1 and {$arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]} !", GoodByeCaptcha::PLUGIN_SLUG));
			if(!empty($arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]))
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH] = $arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH];
			else
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH] = $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH];
		}

		if(empty($arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]) || $arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH] > $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH])
		{
			$this->registerErrorMessage(__("The Website Field Maximum Length should be a numeric value between 1 and {$arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_NAME_LENGTH]}!", GoodByeCaptcha::PLUGIN_SLUG));
			if(!empty($arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH]))
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH] = $arrOldSettingsValues[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH];
			else
				$arrSettingOptions[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH] = $arrDefaultOptionsValues[self::WORDPRESS_COMMENTS_FORM_WEBSITE_LENGTH];
		}

		$this->registerSuccessMessage(__('Your changes were successfully saved!', GoodByeCaptcha::PLUGIN_SLUG));
		return $arrSettingOptions;
	}

//	public  function renderModuleSettingsSectionHeader(array $arrSectionInfo)
//	{
//		echo '<h3>' . __('WordPress Standard Forms Protection', GoodByeCaptcha::PLUGIN_SLUG) . '</h3><hr />';
//	}


	public function getFormattedBlockedContent(GdbcAttemptEntity $attemptEntity)
	{
		$optionName = $this->getOptionNameByOptionId($attemptEntity->SectionId);

		$attemptEntity->Notes = (array)maybe_unserialize($attemptEntity->Notes);

		$arrContent = array('table-head-rows' => '', 'table-body-rows' => '');

		if(null === $optionName)
			return $arrContent;

		$tableHeadRows = '';
		$tableBodyRows = '';

		$tableHeadRows .= '<tr>';
			$tableHeadRows .= '<th colspan="2">' . sprintf(__("Blocked %s Attempt", GoodByeCaptcha::PLUGIN_SLUG), $this->getOptionDisplayTextByOptionId($attemptEntity->SectionId)) . '</th>';
		$tableHeadRows .= '</tr>';

		$tableHeadRows .= '<tr>';
			$tableHeadRows .= '<th>' . __('Field', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
			$tableHeadRows .= '<th>' . __('Value', GoodByeCaptcha::PLUGIN_SLUG) . '</th>';
		$tableHeadRows .= '</tr>';

		if(isset($attemptEntity->Notes['comment_content']))
		{
			$commentContent = $attemptEntity->Notes['comment_content'];
			unset($attemptEntity->Notes['comment_content']);
			$attemptEntity->Notes['comment_content'] = 	$commentContent;
			unset($commentContent);
		}

		if(isset($attemptEntity->Notes['comment_parent']))
		{
			$parentCommentLink = (string)get_comment_link(absint($attemptEntity->Notes['comment_parent']));
			$parentCommentFiledValue = __('Comment Id ', GoodByeCaptcha::PLUGIN_SLUG);

			if(strpos($parentCommentLink, 'http') === 0) {
				$attemptEntity->Notes['comment_parent'] = '<a target = "blank" href = '. esc_attr($parentCommentLink) .'>' . $parentCommentFiledValue . absint($attemptEntity->Notes['comment_parent']) . '</a>';
			}
			else{
				$attemptEntity->Notes['comment_parent'] =  $parentCommentFiledValue . absint($attemptEntity->Notes['comment_parent']);
			}
		}

		if(isset($attemptEntity->Notes['comment_post_ID']))
		{
			$permaLink = get_permalink(absint($attemptEntity->Notes['comment_post_ID']));
			$title     = get_the_title(absint($attemptEntity->Notes['comment_post_ID']));

			if(!empty($title))
			{
				unset($attemptEntity->Notes['comment_post_ID']);
				$attemptEntity->Notes = array_merge(array('post' => '<a href="'.esc_attr($permaLink).'">'. esc_html($title) . '</a>'), $attemptEntity->Notes);
			}
		}

		if(isset($attemptEntity->Notes['user_id']))
		{
			if($wpUser = get_user_by('id', absint($attemptEntity->Notes['user_id']))){
				$attemptEntity->Notes['username'] = $wpUser->user_login;
			}

			unset($attemptEntity->Notes['user_id']);
		}

		foreach($attemptEntity->Notes as $key => $value)
		{
			$tableBodyRows .='<tr>';
				$tableBodyRows .= '<td>' . self::getBlockedContentDisplayableKey($key) . '</td>';
				$tableBodyRows .= '<td>' . wp_kses_stripslashes(wp_filter_kses(print_r($value, true)))  . '</td>';
			$tableBodyRows .='</tr>';
		}

		$arrContent['table-head-rows'] = $tableHeadRows;
		$arrContent['table-body-rows'] = $tableBodyRows;

		return $arrContent;
	}


	/**
	 * Retrieves the maximum character lengths for the comment form fields.
	 *
	 * @since 4.5.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array Maximum character length for the comment form fields.
	 */
	private function getCommentFieldsMaximumLengths()
	{
		global $wpdb;
		$lengths = array(
			'comment_author'       => 245,
			'comment_author_email' => 100,
			'comment_author_url'   => 200,
			'comment_content'      => 65525,
		);

		if( $wpdb->is_mysql && is_callable( array($wpdb, 'get_col_length') ) )
		{
			foreach ( $lengths as $column => $length )
			{
				$col_length = $wpdb->get_col_length( $wpdb->comments, $column );
				$max_length = 0;
				// No point if we can't get the DB column lengths
				if ( is_wp_error( $col_length ) ) {
					break;
				}
				if ( ! is_array( $col_length ) && (int) $col_length > 0 ) {
					$max_length = (int) $col_length;
				} elseif ( is_array( $col_length ) && isset( $col_length['length'] ) && intval( $col_length['length'] ) > 0 ) {
					$max_length = (int) $col_length['length'];
					if ( ! empty( $col_length['type'] ) && 'byte' === $col_length['type'] ) {
						$max_length = $max_length - 10;
					}
				}
				if ( $max_length > 0 ) {
					$lengths[ $column ] = $max_length;
				}
			}
		}

		return $lengths;
	}


	public static function getInstance()
	{
		static $adminInstance = null;
		return null !== $adminInstance ? $adminInstance : $adminInstance = new self();
	}

}