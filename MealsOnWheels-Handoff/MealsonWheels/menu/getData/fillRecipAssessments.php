<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}

$recip =  $db->select('SELECT * FROM recipients WHERE id = ?', [(int)$_POST['recip_id']])[0];
$assess_links = $db->select('SELECT * FROM assessmentScoreLinks WHERE recipient_id = ?',[$recip['id']]);
foreach($assess_links as $assess){
    $archived = ($assess['archived'] == 1) ? 'background-color: #c7ffc7' : '';
    echo '<div style="'.$archived.'" class="recipScore recipAssessRow" onclick="editRecipAssessment('.$assess['id'].')">';
    echo '<span class="recipAssessRowName">'.$assess['assessmentName'].'</span>';
    $dueDate = $db->select('SELECT * FROM assessmentScores WHERE scoreType_id = ? AND assessment_id = ? AND link_id = ?',[5,$assess['assessment_id'], $assess['id']])[0];
    if($dueDate != false){
        if(!empty($dueDate['textValue'])){
            echo '<span style="margin-left:auto;">Next Due: '.date('m/d/Y',strtotime($dueDate['textValue'])).'</span>';
        }
    }
    echo '<span style="margin-left:auto;">Created: '. date('m/d/y',strtotime($assess['dateCreated'])).'</span>';
    echo '<span>Updated: '. date('m/d/y - g:i A',strtotime($assess['lastUpdated'])).'</span>';
    echo '</div>';
}