<?php

	namespace Onereach\Cf7LmsForm\Admin;

	use DOMDocument;
	use DOMXPath;
	use Exception;
	use InvalidArgumentException;
	use JetBrains\PhpStorm\NoReturn;
	use Onereach\Cf7LmsForm\Utilities\AddNewStepCommand;
	use RuntimeException;

	class AjaxController {
		private AddNewStepCommand $addNewStepCommand;

		/**
		 * @param AddNewStepCommand $addNewStepCommand
		 */
		public function __construct( AddNewStepCommand $addNewStepCommand ) {
			$this->addNewStepCommand = $addNewStepCommand;
		}

		/**
		 * @return void
		 */
		#[NoReturn]
		public function registerEndpoints(): void {
			add_action( 'wp_ajax_add_new_step', [ $this, 'ajaxHandleAddNewStep' ] );
			add_action( 'wp_ajax_nopriv_add_new_step', [ $this, 'ajaxHandleAddNewStep' ] );
		}

		/**
		 * @return void
		 */
		public function ajaxHandleAddNewStep(): void {
			// Security check
			check_ajax_referer( 'add_new_step', 'nonce' );

			$formId = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : null;

			if ( ! $formId || $formId <= 0 ) {
				wp_send_json_error( [ 'message' => 'Invalid form ID.' ] );
				wp_die();
			}

			try {
				$formHtml   = $this->executeNewStepCommand( $formId );
				$stepNumber = self::determineStepNumber( $formId );
				$this->addStepToPostMeta( $formId, $stepNumber, $formHtml );
				wp_send_json_success( [ 'message' => 'Step added.', 'form_html' => $formHtml ] );
			} catch ( Exception $e ) {
				wp_send_json_error( [ 'message' => 'Error occurred: ' . $e->getMessage() ] );
			}

			wp_die();
		}

		/**
		 * @param $formId
		 *
		 * @return string
		 * @throws Exception
		 */
		private function executeNewStepCommand( $formId ): string {
			$formHtml = $this->addNewStepCommand->execute( $formId );
			if ( ! $formHtml ) {
				throw new \RuntimeException( 'Failed to add step.' );
			}

			return $formHtml;
		}

		/**
		 * @param $formId
		 *
		 * @return int|mixed
		 * @throws Exception
		 */
		public static function determineStepNumber( $formId ): mixed {
			$formMeta = get_post_meta( $formId );
			if ( $formMeta === false ) {
				throw new \RuntimeException( "Error fetching post meta for post ID $formId" );
			}

			$stepNumber = 0;
			foreach ( $formMeta as $key => $value ) {
				if ( preg_match( '/^cf7lms_step_(\d+)$/', $key, $matches ) ) {
					$stepNumber = max( $stepNumber, (int) $matches[1] );
				}
			}

			return ++ $stepNumber;
		}

		/**
		 * @param $formId
		 * @param $stepNumber
		 * @param $formHtml
		 *
		 * @return void
		 */
		private function addStepToPostMeta( $formId, $stepNumber, $formHtml ): void {
			if ( ! is_int( $formId ) || ! is_int( $stepNumber ) || ! is_string( $formHtml ) ) {
				throw new InvalidArgumentException( 'Invalid argument type provided' );
			}

			$metaKey = 'cf7lms_step_' . $stepNumber;

			try {
				// Extract textarea content
				$textareaContent = $this->extractTextareaContent( $formHtml, "wpcf7-form" );
			} catch ( InvalidArgumentException $e ) {
				// Handle missing textarea
				error_log( 'Failed to extract textarea content: ' . $e->getMessage() );
				throw $e;
			}

			// Remove textarea from the original form HTML
			$htmlWithoutTextarea = str_replace( $textareaContent, '', $formHtml );
			
			// Save the HTML and textarea content as post meta
			$result = add_post_meta( $formId, $metaKey, [ $htmlWithoutTextarea, $textareaContent ] );

			// Check if add_post_meta was successful
			if ( ! $result ) {
				throw new RuntimeException( 'Failed to add post meta' );
			}
		}


		/**
		 * Extracts the content of a textarea with a specific name from an HTML string.
		 *
		 * @param string $html The HTML string to parse.
		 *
		 * @return string The content of the textarea, or an error message if the textarea is not found.
		 */
		private function extractTextareaContent( string $html ): string {
			$dom = $this->createDomFromHtml( $html );

			return $this->getTextareaContent( $dom );
		}

		/**
		 * Creates a DOMDocument from an HTML string.
		 *
		 * @param string $html The HTML string to parse.
		 *
		 * @return DOMDocument The parsed HTML as a DOMDocument.
		 */
		private function createDomFromHtml( string $html ): DOMDocument {
			$dom = new DOMDocument;
			$dom->loadHTML( $html );

			return $dom;
		}

		/**
		 * Extracts the content of a textarea with a specific name from a DOMDocument.
		 *
		 * @param DOMDocument $dom The DOMDocument to search.
		 *
		 * @return string|null The content of the textarea, or null if the textarea is not found.
		 */
		private function getTextareaContent( DOMDocument $dom ): ?string {
			$xpath    = new DOMXPath( $dom );
			$textarea = $xpath->query( "//textarea[@name='wpcf7-form']" );

			if ( $textarea->length > 0 ) {
				return $textarea->item( 0 )->nodeValue;
			}

			return null;
		}

	}
