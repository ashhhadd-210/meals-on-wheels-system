<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$_SESSION['menuToggle'] = $_POST['value'];
exit();