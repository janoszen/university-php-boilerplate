<?php

namespace Janoszen\Boilerplate\Core;

use Janoszen\Boilerplate\DB\DatabaseConnection;
use PDO;

class DatabaseMigrationProcessor {
	/**
	 * @var DatabaseConnection
	 */
	private $db;
	/**
	 * @var AbstractDatabaseMigration[]
	 */
	private $migrations;

	/**
	 * @param DatabaseConnection          $db
	 * @param AbstractDatabaseMigration[] $migrations
	 */
	public function __construct(DatabaseConnection $db, $migrations) {
		$this->db = $db;
		$this->migrations = $migrations;
	}

	/**
	 * @return DatabaseConnection
	 */
	protected function getConnection() {
		return $this->db;
	}

	/**
	 * @return void
	 */
	public function upgradeDatabase() {
		$connection = $this->getConnection();
		$connection->query(/** @lang MySQL */
			'
			CREATE TABLE IF NOT EXISTS migrations (
				id BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
				class_name VARCHAR(255),
				
				UNIQUE u_class_name (class_name)
			) ENGINE=InnoDB;
			');

		$executedMigrations = $connection->query(/** @lang MySQL */
			'SELECT class_name FROM migrations');
		$dueMigrations      = $this->migrations;
		foreach ($executedMigrations as $row) {
			if (in_array($row['class_name'], $dueMigrations)) {
				unset($dueMigrations[array_search($row['class_name'], $dueMigrations)]);
			}
		}

		foreach ($dueMigrations as $dueMigration) {
			/**
			 * @var AbstractDatabaseMigration $migrationClass
			 */
			$migrationClass = new $dueMigration($connection);
			$migrationClass->upgrade();
			$connection
				->query('INSERT INTO migrations (class_name) VALUES (?)', [$dueMigration]);
		}
	}
}