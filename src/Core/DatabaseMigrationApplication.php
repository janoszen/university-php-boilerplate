<?php

namespace Janoszen\Boilerplate\Core;

use Auryn\Injector;

class DatabaseMigrationApplication {
	private $dic;
	private $config;

	public function __construct($config) {
		$this->dic = new Injector();

		foreach ($config['sharedObjects'] as $sharedClass) {
			$this->dic->share($sharedClass);
		}

		foreach ($config['interfaceImplementations'] as $interface => $implementation) {
			$this->dic->alias($interface, $implementation);
		}

		foreach ($config['classParameters'] as $class => $parameters) {
			$this->dic->define($class, $parameters);
		}
		$this->config = $config;
	}

	public function execute() {
		/**
		 * @var DatabaseMigrationProcessor $processor
		 */
		$processor = $this->dic->make(DatabaseMigrationProcessor::class);

		$processor->upgradeDatabase();
	}
}