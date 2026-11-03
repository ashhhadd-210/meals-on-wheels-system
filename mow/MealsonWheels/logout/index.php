<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
if(!isset($_SESSION['user'])){
    header('location: /login');
    exit();
}
// LOG OUT PAGE
$_SESSION = array();

session_destroy();

header("Location: /");
exit();