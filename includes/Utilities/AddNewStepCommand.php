<?php

	namespace Onereach\Cf7LmsForm\Utilities;

	use Onereach\Cf7LmsForm\Admin\FormGenerator;

	class AddNewStepCommand {

		private FormGenerator $formGenerator;

		public function __construct( FormGenerator $formGenerator ) {
			$this->formGenerator = $formGenerator;
		}

		public function execute( int $post_id ): string {
			$post = get_post_meta( $post_id );

			return $this->formGenerator->generateNewHtmlSection( $post );
		}
	}
