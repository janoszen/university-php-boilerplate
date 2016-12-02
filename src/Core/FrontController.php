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
	}


	public function process() {
		$dic = new Injector();

		$dic->alias(ServerRequestInterface::class, ServerRequest::class);
		$dic->alias(ResponseInterface::class, Response::class);

		$request = ServerRequest::fromGlobals();
		$response = new Response();
		$responseContainer = new HTTPResponseContainer($response);
		$dic->share($request);
		$dic->share($responseContainer);

		/**
		 * @var Router $router
		 */
		$router = $dic->make(Router::class, [
			':routes'=> $this->config['routing']['routes'],
			':errorHandlers' =>$this->config['routing']['errorHandlers']
		]);
		$routingResponse = $router->route($request);
		$method = $routingResponse->getMethod();
		$controller = $dic->make($routingResponse->getClass());
		$controllerResponse = $dic->execute([$controller, $method]);

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