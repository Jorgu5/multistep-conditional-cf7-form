<?php

	namespace Onereach\Cf7LmsForm\Admin;

	use UnexpectedValueException;
	use WPCF7_TagGenerator;

	class FormGenerator extends AbstractFormGenerator {
		protected function createDescriptionLink(): string {
			return wpcf7_link(
				__( 'https://contactform7.com/editing-form-template/', 'contact-form-7' ),
				__( 'Editing Form Template', 'contact-form-7' )
			);
		}

		protected function createDescription( string $descLink ): string {
			$description = __( 'You can edit the form template here. For details, see %s.', 'contact-form-7' );

			return sprintf( esc_html( $description ), $descLink );
		}

		protected function createTitle(): string {
			return esc_html( __( 'Form', 'contact-form-7' ) );
		}

		protected function generateTagButtons(): string {
			ob_start();
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->print_buttons();

			return ob_get_clean();
		}

		protected function generateTextArea( $post ): string {
			if ( ! isset( $post->form ) ) {
				throw new UnexpectedValueException( 'Post has no form property' );
			}

			return esc_textarea( $post->prop( 'form' ) );
		}

		public function generateNewHtmlSection( $post ): string {
			return $this->generateForm( $post );
		}
	}