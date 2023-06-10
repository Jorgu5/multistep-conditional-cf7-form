<?php

	namespace Onereach\Cf7LmsForm;
	use Onereach\Cf7LmsForm\Traits\Singleton;

	class EnqueueAssets {

		use Singleton;

		protected function init(): void {
			$this->registerScriptsAndStyles();
		}

		public function registerScriptsAndStyles(): void {
			if ( is_admin() ) {
				$this->registerAdminScriptsAndStyles();
			} else {
				$this->registerFrontendScriptsAndStyles();
			}
		}

		private function registerAdminScriptsAndStyles(): void {
			if ( $this->isAdminWPCF7Page() ) {
				$this->enqueueScript( 'admin-cf7-lms-form', 'admin/index.js' );
				$this->enqueueStyle( 'admin-cf7-lms-form', 'admin/index.css' );
			}
		}

		private function registerFrontendScriptsAndStyles(): void {
			$this->enqueueScript( 'front-cf7-lms-form', 'index.js' );
			$this->enqueueStyle( 'front-cf7-lms-form', 'index.css' );
		}

		private function isAdminWPCF7Page(): bool {
			return isset( $_GET['page'] ) && $_GET['page'] === 'wpcf7';
		}

		private function enqueueScript( string $handle, string $path ): void {
			wp_enqueue_script( $handle, CF7_LMS_PLUGIN_ASSETS . $path, [ 'jquery' ], '1.0.0', true );
		}

		private function enqueueStyle( string $handle, string $path ): void {
			wp_enqueue_style( $handle, CF7_LMS_PLUGIN_ASSETS . $path, [], '1.0.0', 'all' );
		}

	}