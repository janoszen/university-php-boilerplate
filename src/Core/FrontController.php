<?php

namespace Janoszen\Boilerplate\Core;

use Auryn\Injector;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

class FrontController {
	/**
	 * @var array
	 */
	private $config;

	/**
	 * @param array $config
	 */
	public function __construct($config) {
		$this->config = $config;
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
	}


	public function process() {
		$this->dic->alias(ServerRequestInterface::class, ServerRequest::class);
		$this->dic->alias(ResponseInterface::class, Response::class);

		$request = ServerRequest::fromGlobals();
		$response = new Response();
		$responseContainer = new HTTPResponseContainer($response);
		$this->dic->share($request);
		$this->dic->share($responseContainer);

		/**
		 * @var Router $router
		 */
		$router = $this->dic->make(Router::class);
		$routingResponse = $router->route($request);
		$method = $routingResponse->getMethod();
		$controller = $this->dic->make($routingResponse->getClass());
		$controllerResponse = $this->dic->execute([$controller, $method]);

		// Process controller response
		if (is_string($controllerResponse)) {
			// Controller responded with a string. Output it with
			$responseContainer->setResponse(
				$responseContainer->getResponse()->withBody(stream_for($controllerResponse)));
		} else if (is_array($controllerResponse)) {
			// Controller responded with an array. Pass it to the view.
			$loader = new Twig_Loader_Filesystem(__DIR__ . '/../View');
			$twig = new Twig_Environment($loader, []);
			$templateName = preg_replace('/.*\\\\/', '', $routingResponse->getClass()) .
				'/' . $routingResponse->getMethod() . '.twig';
			$output = $twig->render($templateName, $controllerResponse);
			$responseContainer->setResponse(
				$responseContainer->getResponse()->withBody(stream_for($output)));
		} else if ($controllerResponse instanceof ResponseInterface) {
			// Controller responded with a response object. Output it.
			$responseContainer->setResponse($controllerResponse);
		} else {
			throw new Exception('Invalid controller response: ' . var_export($controllerResponse));
		}

		//Output content from response
		$response = $responseContainer->getResponse();
		header('HTTP/' . $response->getProtocolVersion() . ' ' . $response->getStatusCode() . ' ' .
			$response->getReasonPhrase());
		foreach ($response->getHeaders() as $header => $headerDetails) {
			header($response->getHeaderLine($header));
		}
		echo $response->getBody();
	}


}