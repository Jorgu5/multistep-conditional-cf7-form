<?php

	namespace OneReach\Cf7LmsForm\Admin;
	use InvalidArgumentException;
	use UnexpectedValueException;

	/**
	 * Class AbstractFormGenerator
	 */
	abstract class AbstractFormGenerator {
		// This is the template method.
		public function generateForm($post): string {
			if (!$post) {
				throw new InvalidArgumentException('Post is required to generate the form');
			}

			$descLink = $this->createDescriptionLink();
			if (!$descLink) {
				throw new UnexpectedValueException('Description link is empty');
			}

			$description = $this->createDescription($descLink);
			$title = $this->createTitle();
			$tagButtons = $this->generateTagButtons();
			$textArea = $this->generateTextArea($post);

			return <<<HTML
            <h2 class="cf7mls-title-form">{$title}</h2>
            <fieldset class="cf7mls-wrap-form">
                <legend class="cf7mls-description-form">{$description}</legend>
                {$tagButtons}
                <label for="wpcf7-form"></label>
                <textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code" data-config-field="form.body">
                    {$textArea}
                </textarea>
            </fieldset>
        HTML;
		}

		abstract protected function createDescriptionLink(): string;

		abstract protected function createDescription(string $descLink): string;

		abstract protected function createTitle(): string;

		abstract protected function generateTagButtons(): string;

		abstract protected function generateTextArea($post): string;

		abstract protected function generateNewHtmlSection($post): string;
	}