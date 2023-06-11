<?php

	namespace Onereach\Cf7LmsForm\Admin;

	use UnexpectedValueException;
	use WPCF7_TagGenerator;
	use Onereach\Cf7LmsForm\Traits\Singleton;
	use OneReach\Cf7LmsForm\Admin\AjaxController as Util;

	class FormTemplate {
		use Singleton;

		/**
		 * @return void
		 */
		public function init(): void {
			add_filter( 'wpcf7_editor_panels', [ $this, 'addLmsFormPanel' ] );
		}

		/**
		 * @param array $panels
		 *
		 * @return array
		 */
		public function addLmsFormPanel( array $panels ): array {
			$panels['form-panel'] = [
				'title'    => __( 'Form', 'contact-form-7' ),
				'callback' => [ $this, 'displayLmsEditorFormPanel' ],
			];

			return $panels;
		}

		/**
		 * @param $post
		 *
		 * @return void
		 * @throws \Exception
		 */
		public function displayLmsEditorFormPanel( $post ): void {
			$desc_link   = wpcf7_link(
				__( 'https://contactform7.com/editing-form-template/', 'contact-form-7' ),
				__( 'Editing Form Template', 'contact-form-7' )
			);
			$description = __( 'You can edit the form template here. For details, see %s.', 'contact-form-7' );
			$description = sprintf( esc_html( $description ), $desc_link );
			$step_number = Util::determineStepNumber( $post->id() );
			$step_title  = esc_html( __( 'Step - ' . $step_number, 'contact-form-7' ) );

			$tag_generator = WPCF7_TagGenerator::get_instance();
			// Output the tag generator panel
			ob_start();
			$tag_generator->print_buttons();
			$tag_buttons  = ob_get_clean();
			$form_content = esc_textarea( $post->prop( 'form' ) );

			echo '<div class="cf7lms-steps-wrapper">';
			echo $this->renderSteps( $post );
			$this->renderInitialFormTemplate( $description, $tag_buttons, $form_content, $step_title, $step_number );
			echo $this->renderAddStepButton();
			echo '</div>';
		}

		/**
		 * @param string $description
		 * @param string $tag_buttons
		 * @param string $form_content
		 * @param string $step_title
		 * @param int $step_number
		 *
		 * @return void
		 */
		private function renderInitialFormTemplate(
			string $description,
			string $tag_buttons,
			string $form_content,
			string $step_title,
			int $step_number
		): void {
			echo <<<HTML
            <div id="form__step-$step_number" class="cf7lms-step cf7lms-step--$step_number">
                <h2 class="cf7lms-title-form">$step_title</h2>
                <fieldset class="cf7lms-wrap-form">
                    <legend class="cf7lms-description-form">$description</legend>
                    $tag_buttons
                    <textarea id="wpcf7-form-$step_number" name="wpcf7-form" cols="100" rows="24" class="large-text code"
                              data-config-field="form.body">$form_content</textarea>
                </fieldset>
                <div class="cf7lms-step-conditional-field"></div>
            </div>
        HTML;
		}

		/**
		 * @param $post
		 *
		 * @return string
		 */
		private function renderSteps( $post ): string {
			$post_meta     = get_post_meta( $post->id(), '', true );
			$matching_meta = [];
			foreach ( $post_meta as $key => $value ) {
				if ( preg_match( '/^cf7lms_step_(\d+)$/', $key ) ) {
					$matching_meta[ $key ] = $value;
				}
			}

			$html_output = '';
			$step_number = 1;
			foreach ( $matching_meta as $value ) {
				$decoded_html = html_entity_decode( $value[0] );
				$html_output  .= <<<HTML
					<div id="form__step-$step_number" class="cf7lms-step cf7lms-step--$step_number">
					    <div>$decoded_html</div>
					</div>
					HTML;
				$step_number ++;
			}

			return $html_output;
		}


		private function renderAddStepButton(): string {
			return <<<HTML
			<button type="button" class="cf7lms-add-step button button-primary button-large">Add Step</button>
		HTML;
		}

	}
