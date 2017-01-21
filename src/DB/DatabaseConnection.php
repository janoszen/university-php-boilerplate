<?php

namespace Janoszen\Boilerplate\DB;

interface DatabaseConnection {
	/**
	 * Run a parametrized query
	 *
	 * @param string $query
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function query($query, $parameters = []);
}