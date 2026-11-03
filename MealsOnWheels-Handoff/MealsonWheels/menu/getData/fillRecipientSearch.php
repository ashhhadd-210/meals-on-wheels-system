<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1){
    exit('Access Error');
}
$query = '%'.$_POST['value'].'%';
$recipients = $db->select('SELECT * FROM recipients WHERE (firstName LIKE ? OR lastName LIKE ?) ORDER BY firstName ASC, lastName ASC',[$query,$query]);
if($recipients == false){
    echo '<div class="contentRow">No Results</div>';
} else {
    foreach($recipients as $recip){
        echo '<div class="contentRow" onclick="selectRecipSearch('.$recip['id'].',\''.$recip['firstName'].' '.$recip['lastName'].'\')">'.$recip['firstName'] . ' ' . $recip['lastName'] .'</div>';
    }
}