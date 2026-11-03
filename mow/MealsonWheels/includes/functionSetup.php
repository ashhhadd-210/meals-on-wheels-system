<?php
session_start();
date_default_timezone_set('America/Chicago');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/classes/db.class.php');
$db = new database();
require_once($root . '/classes/security.php');
$security = new security($db);
if(isset($_SESSION['user'])){
    $user = $db->select('SELECT * FROM users WHERE id = ?',[$_SESSION['user_id']])[0];
} else {
    $user = false;
}

include($root . '/includes/functions.php');
$domain = 'http://mow.perspektivemg.com';