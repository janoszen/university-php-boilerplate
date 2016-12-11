<?php

namespace Janoszen\Boilerplate\Controller;

class HelloWorldController extends AbstractController {
	public function helloWorld() {
		return [
			'message' => 'Hello world!',
		];
	}

	public function greet($name) {
		return [
			'name' => $name
		];
	}
}