<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    if(isset($_POST['id'])){
        $note = $db->select('SELECT * FROM noteLogs WHERE id = ?',[$_POST['id']])[0];
        ob_clean();
        include_once('noteLogTemplate.php');
        $content = ob_get_clean();
        echo $content;
    } else {
        echo 'There was an issue getting this data';
    }
}
exit();
