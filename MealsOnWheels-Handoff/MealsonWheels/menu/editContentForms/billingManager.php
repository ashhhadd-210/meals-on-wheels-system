<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<div id="billingManagerWrap">
    <h3>Billing Manager</h3>
    <button type="button" class="dark" onclick="openBillingMenu('billingLog')">Billing Logs</button>
    <button type="button" class="dark" onclick="openBillingMenu('donations')">Donations</button>
</div>
<script>
    function newBillingMethod(){
        $.ajax({
                type: 'POST',
                url: 'menu/addContentForms/newBillingMethod.php',
                data: {
                    recip_id: <?php echo $recip['id'] ?>
                },
                success: (re)=>{
                    $('#billingManagerWrap').html(re)
                }
            })
    }
    function openBillingMenu(type) {
        if (type == 'menu') {
            $('#billingManagerWrap').html($('#menuContent').html())
        } else {
            $.ajax({
                type: 'POST',
                url: 'menu/editContentForms/'+type+'.php',
                data: {
                    recip_id: <?php echo $recip['id'] ?>
                },
                success: (re)=>{
                    $('#billingManagerWrap').html(re)
                }
            })
        }
    }
</script>
<style>
    #billingManagerWrap {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }
</style>
<div id="menuContent" style="display: none;">
    <h3>Billing Manager</h3>
    <button type="button" class="dark" onclick="openBillingMenu('billingLog')">Billing Logs</button>
    <button type="button" class="dark" onclick="openBillingMenu('donations')">Donations</button>
</div>