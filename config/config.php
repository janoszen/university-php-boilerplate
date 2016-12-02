<?php

use Janoszen\Boilerplate\Controller\ErrorController;
use Janoszen\Boilerplate\Controller\HelloWorldController;

return [
	'routing' => [
		'errorHandlers' => [
			404 => [ErrorController::class, 'notFound'],
			405 => [ErrorController::class, 'methodNotAllowed'],
			500 => [ErrorController::class, 'error'],
		],
		'routes' => [
			['GET', '/', HelloWorldController::class, 'helloWorld']
		]
	]
];