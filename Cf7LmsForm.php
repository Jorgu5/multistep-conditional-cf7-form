<?php


	/**
	 * Plugin Name: Contact Form 7 LMS Form
	 * Plugin URI: https://www.invisiblemachines.ai/
	 * Description: Contact Form 7 Extension for a multi-step LMS form.
	 * Version: 1.0.0.
	 * Author: OneReach.ai
	 * Author URI: https://onereach.ai
	 * Text Domain: learndash-onereach
	 * Domain Path: languages
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	require_once __DIR__ . '/vendor/autoload.php';

	use JetBrains\PhpStorm\NoReturn;
	use Onereach\Cf7LmsForm\Traits\Singleton;
	use Onereach\Cf7LmsForm\Admin\DependencyCheck;
	use Onereach\Cf7LmsForm\Admin\FormGenerator;
	use Onereach\Cf7LmsForm\Admin\FormTemplate;
	use Onereach\Cf7LmsForm\EnqueueAssets;
	use Onereach\Cf7LmsForm\Utilities\AddNewStepCommand;
	use Onereach\Cf7LmsForm\Admin\AjaxController;

	class Cf7LmsForm {

		use Singleton;

		// private RegisterApiRoute $registerApiRoute;
		private EnqueueAssets $enqueueAssets;
		private FormTemplate $formTemplate;

		protected function init(): void {
			$this->setupConstants();
			$this->dependencyCheck();
			// $this->registerApiRoute = RegisterApiRoute::instance();
			$this->enqueueAssets = EnqueueAssets::instance();
			$this->formTemplate  = FormTemplate::instance();
			$this->ajaxSetup();
		}

		/**
		 * @return void
		 */
		#[NoReturn] private function ajaxSetup(): void {
			$formGenerator     = new FormGenerator();
			$addNewStepCommand = new AddNewStepCommand( $formGenerator );
			$ajaxController    = new AjaxController( $addNewStepCommand );
			$ajaxController->registerEndpoints();
		}

		private function setupConstants(): void {
			if ( ! defined( 'CF7_LMS_PLUGIN_FILE' ) ) {
				define( 'CF7_LMS_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'CF7_LMS_PLUGIN_DIR' ) ) {
				define( 'CF7_LMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CF7_LMS_PLUGIN_ASSETS' ) ) {
				define( 'CF7_LMS_PLUGIN_ASSETS', plugin_dir_url( __FILE__ ) . 'dist/' );
			}

			if ( ! defined( 'CF7_LMS_PLUGIN_URL' ) ) {
				define( 'CF7_LMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'CF7_LMS_PLUGIN_BASENAME' ) ) {
				define( 'CF7_LMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			}
		}

		/**
		 * @return void
		 */
		public function dependencyCheck(): void {
			$dependencyChecker = DependencyCheck::instance();

			$pluginsToCheck = [
				'contact-form-7/wp-contact-form-7.php' => [ 'label' => 'Contact Form 7' ],
			];
			$dependencyChecker->setDependencies( $pluginsToCheck );
			$dependencyChecker->setMessage( 'The following plugins are required but not currently active:' );
			$dependencyChecker->checkInactivePluginDependency();
		}

	}

	Cf7LmsForm::instance();

