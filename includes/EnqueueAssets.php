<?php

	namespace Onereach\Cf7LmsForm;

	use Onereach\Cf7LmsForm\Traits\Singleton;

	class EnqueueAssets {

		use Singleton;

		protected function init(): void {
			add_action( 'wp_enqueue_scripts', [ $this, 'registerFrontendScriptsAndStyles' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'registerAdminScriptsAndStyles' ] );
		}

		public function registerScriptsAndStyles(): void {
			if ( is_admin() ) {
				$this->registerAdminScriptsAndStyles();
			} else {
				$this->registerFrontendScriptsAndStyles();
			}
		}

		public function registerAdminScriptsAndStyles(): void {
			if ( $this->isAdminWPCF7Page() ) {
				$this->enqueueScript( 'admin-cf7-lms-form', 'admin/index.js' );
				$this->localizeScript( 'admin-cf7-lms-form', 'cf7lms', [
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'add_new_step' )
				] );
				$this->enqueueStyle( 'admin-cf7-lms-form', 'admin/index.css' );
			}
		}

		public function registerFrontendScriptsAndStyles(): void {
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

		private function localizeScript( string $handle, string $name, array $data ): void {
			wp_localize_script( $handle, $name, $data );
		}
	}
