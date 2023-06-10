<?php

	/**
	 * Setup a multistep functionality on the dashboard
	 */

	namespace Onereach\Cf7LmsForm\Admin;

	use Exception;
	use Onereach\Cf7LmsForm\Traits\Singleton;
	use WP_REST_Request;
	use WP_REST_Response;
	use WPCF7_TagGenerator;

	class RegisterApiRoute {

		use Singleton;

		public function init(): void {
			add_action( 'rest_api_init', [ $this, 'registerRoute' ] );
		}

		public function registerRoute(): void {
			register_rest_route( 'cf7lms/v1', '/new-step', [
				'methods'  => 'GET',
				'callback' => [ $this, 'generateNewSectionEndpoint' ]
			] );
		}

		public function generateNewSectionEndpoint( WP_REST_REQUEST $request ): WP_REST_Response {
			try {
				$instance = new FormTemplate();
				// print request params to debug in error log
				$form_id = $request->get_params()['id'];
				$post    = get_post( $form_id );
				$html    = $instance->generateNewHtmlSection( $post );

				return new WP_REST_Response( $html, 200 );
			} catch ( Exception $e ) {
				return new WP_REST_Response( [
					'message' => $e->getMessage(),
				], 500 );
			}
		}

	}