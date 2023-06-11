<?php

	namespace OneReach\Cf7LmsForm\Admin;

	use InvalidArgumentException;
	use UnexpectedValueException;
	use Onereach\Cf7LmsForm\Admin\AjaxController as Util;

	/**
	 * Class AbstractFormGenerator
	 */
	abstract class AbstractFormGenerator {
		// This is the template method.
		/**
		 * @throws \Exception
		 */
		public function generateForm( $post_id ): string {
			$post = get_post_meta( $post_id );
			if ( ! $post ) {
				throw new InvalidArgumentException( 'Post is required to generate the form' );
			}

			$descLink = $this->createDescriptionLink();
			if ( ! $descLink ) {
				throw new UnexpectedValueException( 'Description link is empty' );
			}

			$description = $this->createDescription( $descLink );
			$title       = $this->createTitle( $post );
			$tagButtons  = $this->generateTagButtons();
			$textArea    = $this->generateTextArea( $post );
			$stepId      = Util::determineStepNumber( $post_id );

			return <<<HTML
            <h2 class="cf7mls-title-form">{$title}</h2>
            <fieldset class="cf7mls-wrap-form">
                <legend class="cf7mls-description-form">{$description}</legend>
                {$tagButtons}
                <label for="wpcf7-form"></label>
                <textarea id="wpcf7-form-{$stepId}" name="wpcf7-form" cols="100" rows="24" class="large-text code" data-config-field="form.body">
                    {$textArea}
                </textarea>
            </fieldset>
        HTML;
		}

		abstract protected function createDescriptionLink(): string;

		abstract protected function createDescription( string $descLink ): string;

		abstract protected function createTitle(): string;

		abstract protected function generateTagButtons(): string;

		abstract protected function generateTextArea( $post ): string;

		abstract protected function generateNewHtmlSection( $post_id ): string;

	}