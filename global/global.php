<?php

declare(strict_types=1);

ini_set('error_reporting', (string)E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

ob_start ("ob_gzhandler");

session_start();

define('ROOT', $_SERVER['DOCUMENT_ROOT']);

require_once ROOT . '/global/db.php';

$logged = false;
$is_admin = false;

if (isset($_SESSION['user_id'])) {
    $logged = true;
    if ($_SESSION['is_admin']) $is_admin = true;
}
