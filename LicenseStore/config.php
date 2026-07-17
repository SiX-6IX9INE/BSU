<?php
session_start();

if (!defined('BASE')) {
    $docroot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $appdir  = str_replace('\\', '/', __DIR__);
    $basePath = ($docroot !== '' && strpos($appdir, $docroot) === 0)
        ? substr($appdir, strlen($docroot))
        : '/BSU/LicenseStore';
    define('BASE', '/' . trim($basePath, '/') . '/');
}

include("functions/css.php");
include("functions/function.php");

function connDB(){
    $host      = "127.0.0.1";
    $port     = "3306";
    $username = "root";
    $password = "";
    $database = "bsu_store";

    $db_host  = $host . ":" . $port;
    @$conn = mysqli_connect($db_host, $username, $password, $database) or die("Cannont connect to mysql | " . mysqli_connect_error($conn));
    mysqli_set_charset($conn, "utf8");
    return $conn;
}

?>