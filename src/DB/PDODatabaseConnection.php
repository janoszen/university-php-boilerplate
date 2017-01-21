<?php

namespace Janoszen\Boilerplate\DB;

use PDO;

class PDODatabaseConnection implements DatabaseConnection {
	/**
	 * @var PDO
	 */
	private $pdo;
	public function __construct($dsn, $username, $password) {
		$this->pdo = new PDO($dsn, $username, $password);
	}

	public function query($query, $parameters = []) {
		try {
			$statement = $this->pdo->prepare($query);
			$statement->execute($parameters);
			if (!is_null($statement)) {
				$errorInfo = $statement->errorInfo();
			} else {
				$errorInfo = $this->pdo->errorInfo();
			}
			if ($errorInfo[0] != '' && $errorInfo[0] != '00000') {
				throw new DatabaseException(implode(' ', $errorInfo) . ' in query ' . $query);
			}
			$rows = [];
			while (($rowData = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
				$row = [];
				foreach ($rowData as $field => $value) {
					$row[$field] = $value;
				}
				$rows[] = $row;
			}
			$statement->closeCursor();
			return $rows;
		} catch (DatabaseException $e) {
			throw $e;
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getCode() . ' ' . $e->getMessage(), 0, $e);
		}
	}
}