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

abstract class MchGdbcBaseAdminPage
{
	private $pageBrowserTitle     = null;
	private $pageMenuTitle        = null;
	private $pluginSlug           = null;
	private $adminScreenId        = null;
	private $groupModulesList     = null;
	private $pageLayoutColumns    = null;

	public function __construct($pageMenuTitle, $pageBrowserTitle, $pluginSlug)
	{
		$this->pageBrowserTitle     = $pageBrowserTitle;
		$this->pageMenuTitle        = $pageMenuTitle;
		$this->pluginSlug           = $pluginSlug;
		$this->groupModulesList     = array();
		$this->pageLayoutColumns    = 1;

		add_action('current_screen', array($this, 'registerModulesSettingsSections'));

		add_action('current_screen', array($this, 'saveModulesNetworkSettingsOptions'));

		add_action('admin_notices',  array($this, 'displayAdminNotices'));
	}

	protected function setPageLayoutColumns($pageLayoutColumns)
	{
		$this->pageLayoutColumns = $pageLayoutColumns;
	}
	protected function getPageLayoutColumns()
	{
		return $this->pageLayoutColumns;
	}

	public function displayAdminNotices()
	{
		global $wp_settings_errors;
		if(empty($wp_settings_errors))
			return;

		if(!$this->isActive())
			return;

		$wp_settings_errors = array_unique((array)$wp_settings_errors, SORT_REGULAR);

		foreach($this->groupModulesList as $groupIndex => $groupedModules)
		{
			foreach ( ((array)$groupedModules->getGroupedModules()) as $moduleIndex => $adminModuleInstance )
			{
				if ( ! ( $adminModuleInstance instanceof MchGdbcBaseAdminModule ) )
					continue;

				settings_errors( $adminModuleInstance->getSettingKey(), false, false );
			}
		}
	}

	protected function displayGroupModulesMessages($groupIndex)
	{
		static $arrMessages = array();
		if(isset($arrMessages[$groupIndex]))
			return;
		foreach($this->groupModulesList as $registeredGroupIndex => $groupedModules)
		{
			if($registeredGroupIndex != $groupIndex)
				continue;

			foreach ( ((array)$groupedModules->getGroupedModules()) as $moduleIndex => $adminModuleInstance )
			{
				if ( ! ( $adminModuleInstance instanceof MchGdbcBaseAdminModule ) )
					continue;

				$message = $adminModuleInstance->getFormattedMessagesForDisplay();
				if(empty($message))
					continue;

				$arrMessages[$groupIndex] = true;
				echo $message;
				break;
			}
		}


	}

	public function registerGroupedModules(array $groupedModulesList)
	{
		foreach((array) $groupedModulesList as $groupedModules)
			if( ($groupedModules instanceof MchGdbcGroupedModules) && $groupedModules->hasModules())
				$this->groupModulesList[] = $groupedModules;

		return  (false !== end($this->groupModulesList)) ? key($this->groupModulesList) : -1;
	}

	protected function getGroupedModules()
	{
		return $this->groupModulesList;
	}

	public function hasRegisteredModules()
	{
		return isset($this->groupModulesList[0]);
	}


	public function saveModulesNetworkSettingsOptions()
	{
		if( empty($_REQUEST['action']) || strcasecmp($_REQUEST['action'], 'update') !== 0 || empty($_REQUEST['_wpnonce'])  || empty($_REQUEST['option_page']))
			return;

		if( ! MchGdbcWpUtils::isAdminInNetworkDashboard() || !$this->isActive())
			return;

		foreach($this->groupModulesList as $groupIndex => $groupedModules)
		{
			$settingsGroup = $this->getSettingGroupId($groupIndex);
			if(0 !== strcmp($settingsGroup, $_REQUEST['option_page']))
				continue;

			if( ! wp_verify_nonce($_REQUEST['_wpnonce'], "$settingsGroup-options") )
				continue;

			foreach( ((array)$groupedModules->getGroupedModules()) as $moduleIndex => $adminModuleInstance )
			{
				$moduleNetworkOptions = isset($_REQUEST[$adminModuleInstance->getSettingKey()]) ? (array)$_REQUEST[$adminModuleInstance->getSettingKey()] : array();
				$adminModuleInstance->saveNetworkSettingOptions($moduleNetworkOptions);
			}

		}

	}

	public function registerModulesSettingsSections()
	{

		add_action('load-' . $this->adminScreenId, array($this, 'registerPageMetaBoxes'));

		foreach($this->groupModulesList as $groupIndex => $groupedModules)
		{

			$settingsGroup = $this->getSettingGroupId($groupIndex);

			foreach(((array)$groupedModules->getGroupedModules()) as $moduleIndex => $adminModuleInstance)
			{
				if(! ($adminModuleInstance instanceof MchGdbcBaseAdminModule) )
					continue;

//				(null === get_option($adminModuleInstance->getSettingKey(), null))
//					? add_option($adminModuleInstance->getSettingKey(), array(), '', 'yes' )
//					: null; // avoid calling sanitize twice -> https://codex.wordpress.org/Function_Reference/register_setting

				if(MchGdbcWpUtils::isAdminInNetworkDashboard())
				{
					if(null === get_site_option($adminModuleInstance->getSettingKey(), null)){
						update_site_option($adminModuleInstance->getSettingKey(), array());
					}
				}
				elseif(null === get_option($adminModuleInstance->getSettingKey(), null))
				{
					add_option($adminModuleInstance->getSettingKey(), array(), '', 'yes' );
				}

				register_setting($settingsGroup, $adminModuleInstance->getSettingKey(), array( $adminModuleInstance, 'validateModuleSettingsFields' ) );

				//$sectionTitle = !empty($this->arrGroupSectionTitle[$groupIndex]) ? (string)$this->arrGroupSectionTitle[$groupIndex] : '';

				add_settings_section($adminModuleInstance->getSettingKey(), null,
									array($adminModuleInstance, 'renderModuleSettingsSectionHeader'), $settingsGroup );

				foreach((array)$adminModuleInstance->getDefaultOptions() as $optionName => $arrOptionInfo)
				{
					if(empty($arrOptionInfo['LabelText']))
						continue;

					add_settings_field($optionName,
						empty($arrOptionInfo['LabelText']) ? '' : esc_html($arrOptionInfo['LabelText']),
						array($adminModuleInstance, 'renderModuleSettingsField'),
						$settingsGroup,
						$adminModuleInstance->getSettingKey(),
						array($optionName => $arrOptionInfo)
					);

				}


				add_action('shutdown', array($adminModuleInstance, 'saveRegisteredAdminMessages'));
			}
		}

	}

	public function renderGroupModulesSettings($groupIndex = null)
	{
		if(!is_numeric($groupIndex))
		{
			foreach ( func_get_args() as $receivedArgument )
			{
				if ( ! isset( $receivedArgument['args'] ) || !is_numeric($receivedArgument['args']))
					continue;

				$groupIndex = $receivedArgument['args'];
				break;
			}
		}

		$this->displayGroupModulesMessages($groupIndex);


		echo '<form method="post" action="' . (MchGdbcWpUtils::isAdminInNetworkDashboard() ? '' : 'options.php') . '">';

			settings_fields( $this->getSettingGroupId($groupIndex) );
			do_settings_sections( $this->getSettingGroupId($groupIndex) );
			echo '<hr />';
			submit_button();
			echo '<hr />';

		echo  '</form>';

	}

	public function registerPageMetaBoxes()
	{
		foreach($this->groupModulesList as $groupIndex => $groupedModules)
		{
			add_meta_box(
				$this->getSettingGroupId($groupIndex),
				$groupedModules->getGroupTitle(),
				array( $this, 'renderGroupModulesSettings' ),
				$this->adminScreenId,
				'advanced',
				'core',
				$groupIndex
			);
		}

	}

	public function renderPageContent()
	{
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

		$code = '<div id="poststuff">';

		$code .= '<div id="post-body" class="metabox-holder columns-'. $this->pageLayoutColumns .'">';
		$code .= '<div id="postbox-container-2" class="postbox-container mch-left-side-holder">';

		ob_start();

			do_action( 'mch-admin-page-top', $this );

			do_meta_boxes($this->adminScreenId, 'top', null );
			do_meta_boxes($this->adminScreenId, 'normal', null );

			do_action( 'mch-admin-page-middle', $this );

			do_meta_boxes($this->adminScreenId, 'advanced', null );
			do_meta_boxes($this->adminScreenId, 'bottom', null );

			do_action( 'mch-admin-page-bottom', $this );

		$code .= ob_get_clean();
		$code .= '</div>';

		$code .= '<div id="postbox-container-1" class="postbox-container mch-right-side-holder">';

		ob_start();

			do_meta_boxes($this->adminScreenId, 'side', null );

		$code .= ob_get_clean();

		$code .= '</div>';

		$code .= '</div>';
		$code .= '</div>';

		echo $code;
	}

	protected function getSettingGroupId($moduleListGroupIndex)
	{
		return $this->getAdminScreenId() . "-group-{$moduleListGroupIndex}";
	}

	public function setAdminScreenId($adminScreenId)
	{
		$this->adminScreenId = $adminScreenId;
	}

	public function getAdminScreenId()
	{
		return $this->adminScreenId;
	}

	public function getAdminUrl()
	{
		if ( ! MchGdbcWpUtils::isUserInNetworkDashboard() )
			return menu_page_url($this->getPageMenuSlug(), false);

		global $_parent_pages;
		$url = null;
		if ( isset( $_parent_pages[$this->getPageMenuSlug()] ) ) {
			$parent_slug = $_parent_pages[ $this->getPageMenuSlug() ];
			if ( $parent_slug && ! isset( $_parent_pages[ $parent_slug ] ) ) {
				$url = self_admin_url( add_query_arg( 'page', $this->getPageMenuSlug(), $parent_slug ) );
			} else {
				$url = self_admin_url( 'admin.php?page=' . $this->getPageMenuSlug() );
			}
		}

		return null === $url ? '' : esc_url($url);
	}

	public function isActive()
	{
		$currentScreen = get_current_screen();
		return (!empty($currentScreen->id) && $this->adminScreenId === str_replace('-network', '', $currentScreen->id));
	}

	public function getPageBrowserTitle()
	{
		return $this->pageBrowserTitle;
	}

	public function getPageMenuTitle()
	{
		return $this->pageMenuTitle;
	}

	public function getPageMenuSlug()
	{
		return MchGdbcUtils::replaceNonAlphaNumericCharacters(strtolower($this->pluginSlug . '-' . $this->pageMenuTitle), '-');
	}

}
