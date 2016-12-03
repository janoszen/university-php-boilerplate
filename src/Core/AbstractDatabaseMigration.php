<?php

namespace Janoszen\Boilerplate\Core;

use PDO;

abstract class AbstractDatabaseMigration {
	/**
	 * @var PDO
	 */
	private $pdo;

	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function upgrade() {
		$this->execute($this->pdo);
	}

	/**
	 * @param PDO $pdo
	 */
	abstract protected function execute(PDO $pdo);
}