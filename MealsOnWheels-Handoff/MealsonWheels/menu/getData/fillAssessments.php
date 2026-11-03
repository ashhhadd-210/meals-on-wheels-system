<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$assessments = $db->select('SELECT * FROM assessments ORDER BY hide ASC, name ASC');
if($assessments != false){
    foreach($assessments as $assess){
        $hidden = ($assess['hide'] == 1) ? ' assesshidden' : '';
        echo '<div class="contentRow'.$hidden.'" onclick="editAssessment('.$assess['id'].',this)">'.$assess['name'].'</div>';
    }
} else {
    echo 'No Assessments';
}