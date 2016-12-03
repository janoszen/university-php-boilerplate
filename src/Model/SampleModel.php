<?php

namespace Janoszen\Boilerplate\Model;

use PDO;

class SampleModel {
	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * @param PDO $pdo
	 */
	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function getById($id) {
		$statement = $this->pdo->prepare('SELECT * FROM sometable WHERE id=?');
		$queryResult = $statement->execute([$id]);

		//... Process $queryResult as needed. Probably load it into an object.
	}
}