<?php

	/**
	 * Set up plugin dependency check.
	 *
	 * @package Contact Form 7 LMS
	 * @since 1.0.0
	 */

	namespace Onereach\Cf7LmsForm\Admin;

	use Onereach\Cf7LmsForm\Traits\Singleton;

	final class DependencyCheck
	{

		use Singleton;

		/**
		 * The displayed message shown to the user on admin pages.
		 *
		 * @var string $admin_notice_message
		 */
		public string $admin_notice_message = '';

		/**
		 * The array of plugin) to check Should be key => label paird. The label can be anything to display
		 *
		 * @var array $plugins_to_check
		 */
		public array $plugins_to_check = array();

		/**
		 * Array to hold the inactive plugins. This is populated during the
		 * admin_init action via the function call to check_inactive_plugin_dependency()
		 *
		 * @var array $plugins_inactive
		 */
		private array $plugins_inactive = array();

		public function __construct()
		{
			$this->loadRequiredPluginFiles();
			add_action('plugins_loaded', array($this, 'checkInactivePluginDependency'));
		}

		/**
		 * @return void
		 */
		private function loadRequiredPluginFiles(): void
		{
			if (!function_exists('get_plugins')) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if (!function_exists('is_plugin_active')) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
		}

		/**
		 * @param bool $setAdminNotice
		 *
		 * @return array
		 */
		public function checkInactivePluginDependency( bool $setAdminNotice = true): array {
			if (empty($this->plugins_to_check)) {
				return $this->plugins_inactive;
			}

			$activePlugins = get_option('active_plugins');

			foreach ($this->plugins_to_check as $pluginKey => $pluginData) {
				$this->checkPluginActivity($activePlugins, $pluginKey, $pluginData);
			}

			if (!empty($this->plugins_inactive) && $setAdminNotice) {
				add_action('admin_notices', array($this, 'notifyRequired'));
			}

			return $this->plugins_inactive;
		}

		/**
		 * @param array $activePlugins
		 * @param string $pluginKey
		 * @param array $pluginData
		 *
		 * @return void
		 */
		private function checkPluginActivity(array $activePlugins, string $pluginKey, array $pluginData): void
		{
			if (! in_array( $pluginKey, $activePlugins, true ) ) {
				if (is_multisite() && !is_plugin_active_for_network($pluginKey)) {
					$this->plugins_inactive[$pluginKey] = $pluginData;
				} else {
					$this->plugins_inactive[$pluginKey] = $pluginData;
				}
			} else {
				if ( (!empty($pluginData['class'])) && (!class_exists($pluginData['class']))) {
					$this->plugins_inactive[$pluginKey] = $pluginData;
				}
			}
		}

		/**
		 * @return void
		 */
		public function notifyRequired(): void
		{
			if (empty($this->admin_notice_message) || empty($this->plugins_inactive)) {
				return;
			}

			$pluginsListStr = $this->getPluginsListStr();
			if (!empty($pluginsListStr)) {
				$adminNoticeMessage = sprintf($this->admin_notice_message . '<br />%s', $pluginsListStr);
				$this->displayAdminNotice($adminNoticeMessage);
			}
		}

		/**
		 * @return string
		 */
		private function getPluginsListStr(): string
		{
			$pluginsListStr = '';
			foreach ($this->plugins_inactive as $plugin) {
				if (!empty($pluginsListStr)) {
					$pluginsListStr .= ', ';
				}
				$pluginsListStr .= $plugin['label'] . (isset($plugin['min_version']) && !empty($plugin['min_version']) ? ' v' . $plugin['min_version'] : '');
			}
			return $pluginsListStr;
		}

		/**
		 * @param string $adminNoticeMessage
		 *
		 * @return void
		 */
		private function displayAdminNotice(string $adminNoticeMessage): void
		{
			?>
			<div class="notice notice-error ld-notice-error is-dismissible">
				<p><?php echo wp_kses_post($adminNoticeMessage); ?></p>
			</div>
			<?php
		}

		/**
		 * @param array $plugins
		 *
		 * @return void
		 */
		public function setDependencies(array $plugins): void
		{
			$this->plugins_to_check = $plugins;
		}

		/**
		 * @param string $message
		 *
		 * @return void
		 */
		public function setMessage(string $message): void
		{
			$this->admin_notice_message = $message;
		}
	}
