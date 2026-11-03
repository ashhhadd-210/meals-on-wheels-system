<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1){
    exit('Access Error');
}
$logs = $db->select('SELECT * FROM certificateLogs WHERE recipient_id = ? ORDER BY startDate ASC',[$_POST['recipient_id']]);

if($logs != false){
    foreach($logs as $log){
        $physician = $db->select('SELECT * FROM physicians WHERE id = ?',[$log['physician']])[0];
        $clinic = $db->select('SELECT * FROM clinics WHERE id = ?',[$log['clinic']])[0];
        echo '<div onclick="editCert('.$log['id'].')" class="certLogItem">'. date('m/d/Y',strtotime($log['startDate'])) . ' - ' .  date('m/d/Y',strtotime($log['endDate'])) .' ('.$physician['firstName'] . ' ' . $physician['lastName'].' | '. $clinic['name'] .')</div>';
    }
} else {
    echo '<div class="certLogItem">No Certificates</div>';
}
?>
