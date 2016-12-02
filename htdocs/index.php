<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$config = require(__DIR__ . '/../config/config.php');
$front = new \Janoszen\Boilerplate\Core\FrontController($config);
$front->process();
