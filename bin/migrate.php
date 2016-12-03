#!/usr/bin/env php
<?php
/**
 * This file automatically runs database migrations.
 */

use Janoszen\Boilerplate\Core\DatabaseMigrationApplication;

require_once(__DIR__ . '/../vendor/autoload.php');

$config      = require(__DIR__ . '/../config/config.php');
$application = new DatabaseMigrationApplication($config);
$application->execute();