<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../config/OracleDb.php';

$oracle = OracleDb::getInstance();
