<?php

	namespace Onereach\Cf7LmsForm\Utilities;

	use Onereach\Cf7LmsForm\Admin\FormGenerator;

	class AddNewStepCommand {

		private FormGenerator $formGenerator;

		public function __construct( FormGenerator $formGenerator ) {
			$this->formGenerator = $formGenerator;
		}

		/**
		 * @throws \Exception
		 */
		public function execute( int $post_id ): string {
			return $this->formGenerator->generateNewHtmlSection( $post_id );
		}
	}
