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
		$connection_string
	) {
		$this->connection = \oci_connect($username, $connection_string);
		if (!$this->connection) {
			$error = oci_error();
			throw new DatabaseException($error['message']);
		}
	}

	public function query($query, $parameters = []) {
		$statement = oci_parse($this->connection, $query);
		foreach ($parameters as $param => $name) {
			\oci_bind_by_name($statement, $param, $name);
		}
		if (!\oci_execute($statement)) {
			if ($statement === null) {
				$error = oci_error($this->connection);
			} else {
				$error = oci_error($statement);
			}
			throw new DatabaseException($error['message']);
		}
		$result = [];
		while (($row = \oci_fetch_assoc($statement)) !== false) {
			$result[] = $row;
		}
		return $result;
	}
}