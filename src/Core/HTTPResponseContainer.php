<?php

namespace Janoszen\Boilerplate\Core;

use Psr\Http\Message\ResponseInterface;

/**
 * This is a container for the response. It works around PSR-7 immutability.
 */
class HTTPResponseContainer {
	/**
	 * @var ResponseInterface
	 */
	private $response;

	/**
	 * @param ResponseInterface $response
	 */
	public function __construct(ResponseInterface $response) {
		$this->response = $response;
	}

	/**
	 * @return ResponseInterface
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param ResponseInterface $response
	 */
	public function setResponse($response) {
		$this->response = $response;
	}
}