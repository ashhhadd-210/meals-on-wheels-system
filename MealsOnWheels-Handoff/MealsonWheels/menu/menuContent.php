<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1){
    $menuOption = $security->sanitizeInput($_POST['menu']);
    if(empty($_POST['menu'])){
        $menuOption = $_SESSION['menu'];
    }
    $content = 'error';
    if(file_exists($root . '/menu/menus/' . $menuOption . '.php')){
        ob_start();
        include_once($root . '/menu/menus/' . $menuOption . '.php');
        $content = ob_get_clean();
    }
    echo $content;
}
exit();