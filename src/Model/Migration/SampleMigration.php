<?php

namespace Janoszen\Boilerplate\Model\Migration;

use Janoszen\Boilerplate\Core\AbstractDatabaseMigration;
use Janoszen\Boilerplate\DB\DatabaseConnection;
use PDO;

class SampleMigration extends AbstractDatabaseMigration {

	protected function execute() {
		$this->db->query(/** @lang MySQL */
			'
				CREATE TABLE sometable (
					id INT PRIMARY KEY AUTO_INCREMENT
				)
			'
		);
	}
}