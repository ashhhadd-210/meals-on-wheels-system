<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if (!$user) {
    exit('Access Error');
}
if (!isset($_GET['re'])) {
    header('location: /');
    exit();
}

switch ($_GET['re']) {
    case 'recipient':
        $_SESSION['menu'] = 'recipients';
        $_SESSION['redirect'] = $_GET['id'];
        header('location: /');
        exit();
        break;

    default:
        header('location: /');
        exit();
        break;
}
