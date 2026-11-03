<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    function sendMessage($msg){
        echo $msg;
        exit();
    }
    $email = $security->sanitizeInput($_POST['email']);
    $pswd = $security->sanitizeInput($_POST['pswd']);

    $query = $db->select('SELECT * FROM users WHERE userEmail = ?',[$email])[0];
    if(empty($query)){
        sendMessage('Could not find that user.');
    } else {
        if(password_verify($pswd,$query['userPassword'])){
            $_SESSION['user'] = 'true';
            $_SESSION['user_id'] = $query['id'];
            $_SESSION['user_email'] = $query['userEmail'];
            $_SESSION['user_firstName'] = $query['firstName'];
            $_SESSION['user_lastName'] = $query['lastName'];

            $db->update('UPDATE users SET lastLogin = ?, lastIP = ? WHERE id = ?',[date('Y-m-d H:i:s'),getUserIP(),$query['id']]);
            sendMessage('ok');
        } else {
            sendMessage('Incorrect Password');
        }
    }
}