<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<div id="billingLogList">
    <button type="button" class="dark" onclick="openBillingMenu('menu')">Back</button>
    <button type="button" class="addConentBtn" onclick="newBillingMethod()">New Billing Method</button>
    <?php
    $logs = $db->select('SELECT * FROM billings WHERE recipient_id = ? ORDER BY startDate DESC',[$recip['id']]);
    if($logs != false){
        foreach($logs as $log){
            $type = $db->select('SELECT * FROM billingRecipientTypes WHERE id = ?',[$log['billType_id']])[0];
            echo '<div class="contentRow" onclick="viewBillingLog('.$log['id'].')">'.$type['name']. ' - ' . date('m/d/y',strtotime($log['startDate'])) .'</div>';
        }
    } else {
        echo '<div class="contentRow">No billing methods</div>';
    }
    ?>
</div>
<script>
    function viewBillingLog(id){
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/viewBillingLog.php',
            data: {
                id: id
            },
            success: (re)=>{
                $('#billingManagerWrap').html(re)
            }
        })
    }
</script>
<style>
    #billingLogList {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px;
    }
</style>