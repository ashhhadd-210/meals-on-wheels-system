<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
echo 'Test00';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $user['isAdmin'] == 1){
    if(isset($_GET['l'])){
        $location = $_GET['l'];
        $_SESSION['redirect'] = $location;
        $mod = (isset($_GET['mod'])) ? $_GET['mod'] : false;
        $data = (isset($_GET['data'])) ? $_GET['data'] : false;

        if($mod != false){
            $_SESSION['redirect_mod'] = $mod;
        }

        if($data != false){
            $_SESSION['redirect_data'] = $data;
        }

        $_SESSION['menu'] = $location;
        header('location: /');
        exit();
    } else {
        header('location: /');
        exit();
    }
}
exit();