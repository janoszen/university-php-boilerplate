<?php

namespace Janoszen\Boilerplate\DB;

class OCI8DatabaseConnection implements DatabaseConnection {

	/**
	 * @var resource
	 */
	private $connection;

	public function __construct(
		$username,
		$password,
		$connection_string,
		$character_set='UTF8',
		$session_mode = \OCI_DEFAULT) {
		$this->connection = oci_connect($username, $connection_string, $character_set, $session_mode);
	}

	/**
	 * Run a parametrized query
	 *
	 * @param string $query
	 * @param array  $parameters
	 *
	 * @return array
	 */
	public function query($query, $parameters = []) {
		$statement = oci_parse($this->connection, $query);
		foreach ($parameters as $param => $name) {
			oci_bind_by_name($statement, $param, $name);
		}
		oci_execute($statement);
		$result = [];
		while (($row = oci_fetch_assoc($statement)) !== false) {
			$result[] = $row;
		}
		return $result;
	}
}