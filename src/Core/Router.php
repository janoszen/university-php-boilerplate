<?php

namespace Janoszen\Boilerplate\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;

class Router {
	private $errorHandlers;
	private $dispatcher;

	/**
	 * @param array $routes
	 * @param array $errorHandlers
	 */
	public function __construct($routes, $errorHandlers) {
		$this->dispatcher    = \FastRoute\simpleDispatcher(function (RouteCollector $routeCollector) use (&$routes) {
			foreach ($routes as $route) {
				$method   = $route[0];
				$uri      = $route[1];
				$callback = array($route[2], $route[3]);
				$routeCollector->addRoute($method, $uri, $callback);
			}
		});
		$this->errorHandlers = $errorHandlers;
	}

	public function route(ServerRequestInterface $request) {
		$routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
		switch ($routeInfo[0]) {
			case Dispatcher::NOT_FOUND:
				return $this->getNotFoundRoute();
			case Dispatcher::METHOD_NOT_ALLOWED:
				return $this->getMethodNotAllowedResponse($routeInfo[1]);
			case Dispatcher::FOUND:
				return $this->getOkResponse($routeInfo[1][0], $routeInfo[1][1], $routeInfo[2]);
			//@codeCoverageIgnoreStart
			default:
				throw new \Exception('Routing error!');
			//@codeCoverageIgnoreEnd
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getNotFoundRoute() {
		return $this->getErrorHandlerResponse(404);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getServerErrorRoute() {
		list($controller, $action) = $this->errorHandlers[500];
		return new RoutingResponse(500, $controller, $action, []);
	}

	/**
	 * Create a response for an error handler
	 *
	 * @param int   $code
	 * @param array $vars
	 *
	 * @return RoutingResponse
	 */
	private function getErrorHandlerResponse($code, array $vars = []) {
		list($controller, $action) = $this->errorHandlers[$code];
		return new RoutingResponse($code, $controller, $action, $vars);
	}

	/**
	 * Create a method not allowed response.
	 *
	 * @param array $allowedMethods
	 *
	 * @return RoutingResponse
	 */
	private function getMethodNotAllowedResponse(array $allowedMethods) {
		return $this->getErrorHandlerResponse(405, ['allowedMethods' => $allowedMethods]);
	}

	/**
	 * Create an OK response
	 *
	 * @param string $controller
	 * @param string $method
	 * @param array $vars
	 *
	 * @return RoutingResponse
	 */
	private function getOkResponse($controller, $method, $vars) {
		return new RoutingResponse(200, $controller, $method, $vars);
	}

	/**
	 * Process the FastRoute response
	 *
	 * @param array $routeInfo
	 *
	 * @return RoutingResponse
	 * @throws \Exception
	 */
	private function processDispatcherResult(array $routeInfo) {
		switch ($routeInfo[0]) {
			case Dispatcher::NOT_FOUND:
				return $this->getNotFoundRoute();
			case Dispatcher::METHOD_NOT_ALLOWED:
				return $this->getMethodNotAllowedResponse($routeInfo[1]);
			case Dispatcher::FOUND:
				return $this->getOkResponse($routeInfo[1][0], $routeInfo[1][1], $routeInfo[2]);
			//@codeCoverageIgnoreStart
			default:
				throw new \Exception('Routing error!');
			//@codeCoverageIgnoreEnd
		}
	}
	//endregion Routing
}