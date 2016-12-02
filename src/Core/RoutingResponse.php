<?php

namespace Janoszen\Boilerplate\Core;

class RoutingResponse {
	/**
	 * @var int
	 */
	private $statusCode;
	/**
	 * @var string
	 */
	private $class;
	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var array
	 */
	private $parameters = [];

	/**
	 * @param int    $statusCode The HTTP status code to use by default
	 * @param string $class      Class to call
	 * @param string $method     Method to call within the class
	 * @param array  $parameters Additional parameters to be passed to the method to be called.
	 */
	public function __construct($statusCode, $class, $method, $parameters = []) {
		$this->statusCode = $statusCode;
		$this->class      = $class;
		$this->method     = $method;
		$this->parameters = $parameters;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}
}
