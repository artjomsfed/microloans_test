<?php
declare(strict_types=1);

require_once 'FunctionAccessChecker.php';

//Set up dependencies
$host = 'microloans_test-mariadb';
$db = 'microloans_test';
$user = 'microloans_db_user';
$pass= 'microloans_db_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

//Instantiate checker
$accessChecker = new FunctionAccessChecker($pdo);

$username = 'User3';
$functionName = 'Function3';

$isAllowed = $accessChecker->isAccessAllowed($username, $functionName);
