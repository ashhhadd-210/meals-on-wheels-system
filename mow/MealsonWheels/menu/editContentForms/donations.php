<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<div id="donationWrap">
    <button type="button" class="dark" onclick="openBillingMenu('menu')">Back</button>
    <button type="button" class="addConentBtn" onclick="newDonation()">New Donation</button>
    <?php
    $donations = $db->select('SELECT * FROM donations WHERE recipient_id = ? ORDER BY dateStamp DESC',[$recip['id']]);
    if($donations == false){
        echo '<div class="contentRow">No Donations</div>';
    } else {
        foreach($donations as $donation){
            echo '<div class="contentRow">'.date('m/d/Y',strtotime($donation['dateStamp'])).'</div>';
        }
    }
    ?>
</div>
<style>
    #donationWrap {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>