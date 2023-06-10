<?php

	namespace Onereach\Cf7LmsForm\Traits;

	trait Singleton
	{
		private static self $instance;

		private function __construct()
		{
			$this->init();
		}

		private function __clone()
		{
			// Prevent the instance from being cloned.
		}

		public function __wakeup()
		{
			// Prevent the instance from being unserialized.
		}

		public static function instance(): static
		{
			if ( ! isset(static::$instance)) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		private function init(): void {
		}
	}
