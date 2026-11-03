<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/menu/menuOptions.php');
$optionNames = array_column($options, 'name');
if (isset($_POST['menu']) && in_array($_POST['menu'], $optionNames)) {
    $_SESSION['menu'] = $_POST['menu'];
    echo 'ok';
} else {
    echo 'error';
}
