<?php

namespace Janoszen\Boilerplate\Core;

use Janoszen\Boilerplate\DB\DatabaseConnection;
use Janoszen\Boilerplate\DB\DatabaseException;
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
		try {
			$connection->query(/** @lang MySQL */
				'
				CREATE TABLE migrations (
					class_name VARCHAR(255),
					
					CONSTRAINT pk_class_name PRIMARY KEY (class_name)
				);
				');
		} catch (DatabaseException $ignore) {

		}

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