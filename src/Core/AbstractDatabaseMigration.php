<?php

namespace Janoszen\Boilerplate\Core;

use Janoszen\Boilerplate\DB\DatabaseConnection;
use PDO;

abstract class AbstractDatabaseMigration {
	/**
	 * @var DatabaseConnection
	 */
	protected $db;

	public function __construct(DatabaseConnection $db) {
		$this->db = $db;
	}

	public function upgrade() {
		$this->execute();
	}

	abstract protected function execute();
}