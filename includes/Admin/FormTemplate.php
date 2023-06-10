<?php

	namespace Onereach\Cf7LmsForm\Admin;

	use UnexpectedValueException;
	use WPCF7_TagGenerator;
	use Onereach\Cf7LmsForm\Traits\Singleton;

	class FormTemplate {
		use Singleton;

		public function init() {
			add_filter( 'wpcf7_editor_panels', [ $this, 'addLmsFormPanel' ] );
		}

		public function addLmsFormPanel( array $panels ): array {
			$panels['form-panel'] = [
				'title'    => __( 'Form', 'contact-form-7' ),
				'callback' => [ $this, 'displayLmsEditorFormPanel' ],
			];

			return $panels;
		}

		public function displayLmsEditorFormPanel( $post ): void {
			$desc_link   = wpcf7_link(
				__( 'https://contactform7.com/editing-form-template/', 'contact-form-7' ),
				__( 'Editing Form Template', 'contact-form-7' )
			);
			$description = __( 'You can edit the form template here. For details, see %s.', 'contact-form-7' );
			$description = sprintf( esc_html( $description ), $desc_link );
			$step_title  = esc_html( __( 'Step', 'contact-form-7' ) );

			$tag_generator = WPCF7_TagGenerator::get_instance();
			ob_start();
			$tag_generator->print_buttons();
			$tag_buttons  = ob_get_clean();
			$form_content = esc_textarea( $post->prop( 'form' ) );

			$this->renderFormTemplate( $description, $tag_buttons, $form_content, $step_title );
		}

		private function renderFormTemplate(
			string $description,
			string $tag_buttons,
			string $form_content,
			string $step_title
		): void {
			echo <<<HTML
            <div class="cf7lms-step-wrapper">
                <h2 class="cf7lms-title-form">$step_title</h2>
                <fieldset class="cf7lms-wrap-form">
                    <legend class="cf7lms-description-form">$description</legend>
                    $tag_buttons
                    <textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code"
                              data-config-field="form.body">$form_content</textarea>
                    <div id="cf7lms-app"></div>
                    <div id="cf7lms_PostBoxUpgradePro" style="display:none;"></div>
                </fieldset>
                <div class="cf7lms-step-conditional-field"></div>
                <button class="cf7lms-add-step">Add new step</button>
            </div>
        HTML;
		}
	}
