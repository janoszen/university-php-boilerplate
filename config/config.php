<?php

use Janoszen\Boilerplate\Controller\ErrorController;
use Janoszen\Boilerplate\Controller\HelloWorldController;
use Janoszen\Boilerplate\Core\DatabaseMigrationProcessor;
use Janoszen\Boilerplate\Core\Router;
use Janoszen\Boilerplate\DB\DatabaseConnection;
use Janoszen\Boilerplate\DB\OCI8DatabaseConnection;
use Janoszen\Boilerplate\DB\PDODatabaseConnection;
use Janoszen\Boilerplate\Model\Migration\SampleMigration;

/**
 * This is the configuration for the Auryn dependency injector. It is processed by the FrontController
 *
 * @see https://github.com/rdlowrey/auryn
 */
return [
	/**
	 * By default, the Auryn DIC creates a new instance for an object every time it's needed. If you list a class
	 * here, Auryn won't do that, but reuse the class that already exists.
	 *
	 * Since PHP runs on a per-request basis, you are not really doing any harm by putting your classes here, just
	 * keep it in mind when hunting bugs.
	 */
	'sharedObjects' => [
		PDODatabaseConnection::class
	],
	/**
	 * Interfaces are nice. You can list interfaces and their implementations here in order to create a dependency
	 * inversion.
	 *
	 * For example:
	 *
	 * ```
	 * MyInterface::class => MyImplementation::class,
	 * ```
	 */
	'interfaceImplementations' => [
		//DatabaseConnection::class => PDODatabaseConnection::class
		DatabaseConnection::class => OCI8DatabaseConnection::class
	],
	/**
	 * Provide the class constructor parameters here. Remember, these are named parameters only, if you need classes,
	 * those will be automatically instantiated from your typehints.
	 */
	'classParameters' => [
		/**
		 * URL routing parameters. Use this to direct requests to your controllers
		 */
		Router::class => [
			':errorHandlers' => [
				404 => [ErrorController::class, 'notFound'],
				405 => [ErrorController::class, 'methodNotAllowed'],
				500 => [ErrorController::class, 'error'],
			],
			':routes' => [
				['GET', '/', HelloWorldController::class, 'helloWorld'],
				['GET', '/greet/{name:[a-zA-Z]+}', HelloWorldController::class, 'greet']
			]
		],
		/**
		 * Database connector. Make sure you are using prepared statements to avoid SQL injection vulnerabilities.
		 *
		 * @see http://php.net/manual/en/pdo.prepared-statements.php
		 */
		PDODatabaseConnection::class => [
			':dsn'      => 'mysql:host=localhost;dbname=test',
			':username' => 'root',
			':password' => '',
		],
		OCI8DatabaseConnection::class => [
			':username' => 'test',
			':password' => 'odohjamoaDobaic8',
			':connection_string' => 'test.cxwa5gl57rna.eu-central-1.rds.amazonaws.com/TEST'
		],
		/**
		 * Database migration classes.
		 */
		DatabaseMigrationProcessor::class => [
			':migrations' => [
				SampleMigration::class
			]
		]
	],
];