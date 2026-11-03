<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$billing = $db->select('SELECT * FROM billings WHERE id = ?', [$_POST['id']])[0];

?>
<button type="button" class="dark" onclick="openBillingMenu('billingLog')">Back</button>
<div class="formRow">
    <div class="inputRow">
        <label for="">Type</label>
        <?php
        $type = $db->select('SELECT * FROM billingRecipientTypes WHERE id = ?', [$billing['billType_id']])[0];
        ?>
        <input type="text" value="<?php echo $type['name'] ?>" readonly disabled>
    </div>
</div>
<?php
if ((int) $type['id'] == 2) {
    include($root . '/menu/editContentForms/viewInsuranceBilling.php');
}
if ((int) $type['id'] == 3) {
    include($root . '/menu/editContentForms/viewContactBilling.php');
}
?>
<div class="formRow">
    <div class="inputRow">
        <button class="deleteBtn" type="button" onclick="deleteBillingLog()">Delete</button>
    </div>
</div>
<script>
    function deleteBillingLog() {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    id: <?php echo $billing['id'] ?>,
                    contentType: 'recip-billingLog'
                },
                success: (re) => {
                    if (re.trim() == 'ok') {
                        openBillingMenu('billingLog')
                    } else {
                        customMessage(re, 3)
                    }
                }
            })
        }
    }
</script>