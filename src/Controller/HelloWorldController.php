<?php

namespace Janoszen\Boilerplate\Controller;

class HelloWorldController extends AbstractController {
	public function helloWorld() {
		return [
			'message' => 'Hello world!',
		];
	}
}