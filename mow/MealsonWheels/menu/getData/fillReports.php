<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1){
    exit('Access Error');
}

$reports = $db->select('SELECT * FROM reports ORDER BY displayOrder ASC');

if($reports == false){
    exit('<div class="contentRow">No Reports</div>');
} else {
    foreach($reports as $report){
        $color = (isLightColor($report['color'])) ? '#333' : 'white';
        echo '<div style="background-color: '.$report['color'].'; color: '.$color.'" class="contentRow" onclick="openReport(this,'.$report['id'].')">'.$report['name'].'</div>';
    }
}
exit();