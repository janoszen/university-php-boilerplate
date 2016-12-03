<?php

namespace Janoszen\Boilerplate\Model\Migration;

use Janoszen\Boilerplate\Core\AbstractDatabaseMigration;
use PDO;

class SampleMigration extends AbstractDatabaseMigration {

	/**
	 * @param PDO $pdo
	 */
	protected function execute(PDO $pdo) {
		$pdo->query(/** @lang MySQL */
			'
				CREATE TABLE sometable (
					id INT PRIMARY KEY AUTO_INCREMENT
				)
			'
		);
	}
}