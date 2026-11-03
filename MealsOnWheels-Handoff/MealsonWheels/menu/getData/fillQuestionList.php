<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1){
    exit('Access Error');
}
$qustions = $db->select('SELECT * FROM assessmentQuestions WHERE assessment_id = ? ORDER BY displayOrder ASC',[$_POST['assess_id']]);

if($qustions == false){
    echo '<div class="contentRow">No Questions</div>';
} else {
    foreach($qustions as $qu){
        $hiddenClass = ($qu['hidden'] == 1) ? ' assesshidden' : '';

        echo '<div id="'.$qu['id'].'" class="contentRow questionListItem'.$hiddenClass.'" onclick="editQuestion('.$qu['id'].')">'.$qu['prompt'].'</div>';
    }
}