<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1){
    $data = $security->sanitizeInput($_POST['value']);
    $query = '%'.$data.'%';
    $recips = $db->select('SELECT id,firstName,lastName FROM recipients WHERE firstName LIKE ? OR lastName LIKE ?',[$query,$query]);
    if($recips != false){
        echo '<div class="searchRecipResultWrap">';
        foreach($recips as $recip){
            echo '<div onclick="addRecipient(' . $recip['id'] . ',\'' . $recip['firstName']. ' ' . $recip['lastName'] . '\')" class="recipSearchRow"><span class="recipientName">'.$recip['firstName']. ' ' . $recip['lastName'].'</span></div>';
        }
        echo '</div>';
        exit();
    } else {
        echo 'none';
    }
}
exit();