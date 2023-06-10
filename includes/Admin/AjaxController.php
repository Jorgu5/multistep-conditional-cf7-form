<?php

	namespace Onereach\Cf7LmsForm\Admin;

	use Exception;
	use JetBrains\PhpStorm\NoReturn;
	use Onereach\Cf7LmsForm\Utilities\AddNewStepCommand;

	class AjaxController {

		private AddNewStepCommand $addNewStepCommand;

		public function __construct( AddNewStepCommand $addNewStepCommand ) {
			$this->addNewStepCommand = $addNewStepCommand;
		}

		#[NoReturn] public function registerEndpoints(): void {
			add_action( 'wp_ajax_add_new_step', [ $this, 'ajaxHandleAddNewStep' ] );
			add_action( 'wp_ajax_nopriv_add_new_step', [ $this, 'ajaxHandleAddNewStep' ] );
		}

		public function ajaxHandleAddNewStep(): void {
			// Security check
			check_ajax_referer( 'add_new_step', 'nonce' );

			$formId = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : null;

			if ( ! $formId || $formId <= 0 ) {
				wp_send_json_error( [ 'message' => 'Invalid form ID.' ] );
				wp_die();
			}

			try {
				$result = $this->addNewStepCommand->execute( $formId );
				if ( $result ) {
					// send $result to the client which is the new HTML section
					wp_send_json_success( [ 'message' => 'Step added.', 'form_html' => $result ] );
					// add post meta to the form with the new HTML section
					update_post_meta( $formId, 'cf7_lms_step', $result );
					// TODO: add post meta to the form with the new HTML section
					// Retrieve the HTML on the frontend side.
					// Add specific ID's to handle the form submissions and updating proper meta.

				} else {
					wp_send_json_error( [ 'message' => 'Failed to add step.' ] );
				}
			} catch ( Exception $e ) {
				wp_send_json_error( [ 'message' => 'Error occurred: ' . $e->getMessage() ] );
			}

			wp_die();
		}
	}
