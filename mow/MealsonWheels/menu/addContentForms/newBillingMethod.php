<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<form action="" id="billingMethodForm">
    <h3>New Billing Method</h3>
    <button type="button" class="dark" onclick="openBillingMenu('billingLog')">Back</button>
    <div class="formRow">
        <div class="inputRow">
            <label for="type">Billing Type</label>
            <select name="type" id="type" required>
                <option value="" selected hidden>Select</option>
                <?php
                $types = $db->select('SELECT* FROM billingRecipientTypes');
                foreach ($types as $type) {
                    echo '<option value="' . $type['id'] . '">' . $type['name'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div id="bilingFormInsert"></div>
</form>
<script>
    var sendForm = true;
    $(document).ready(() => {
        $('#billingMethodForm').on('submit',(e)=>{
            e.preventDefault();
            if(!sendForm){
                customMessage('There are no finacial contacts for this recipient');
                return false;
            }
            var formData = new FormData($('#billingMethodForm')[0]);
            formData.append('contentType','billingMethod')
            formData.append('recip_id',<?php echo $recip['id'] ?>)

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                contentType: false,
                processData: false,
                data: formData,
                success: (re)=>{
                    if(re.trim() == 'ok'){
                        openBillingMenu('billingLog');
                    } else {
                        customMessage(re,3)
                    }
                }
            })
        })
        $('#type').on('change', () => {
            if ($('#type').val() == 2) {
                $.ajax({
                    type: 'POST',
                    url: 'menu/addContentForms/billingInsuranceForm.php',
                    success: (re) => [
                        $('#bilingFormInsert').html(re)
                    ]
                })
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'menu/addContentForms/billingOtherForm.php',
                    data: {
                        type: $('#type').val(),
                        recip_id: <?php echo $recip['id'] ?>
                    },
                    success: (re) => [
                        $('#bilingFormInsert').html(re)
                    ]
                })
            }
        })
    })
</script>
<style>
    #billingMethodForm {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }

    #bilingFormInsert {
        display: flex;
        gap: 20px;
        flex-direction: column;
    }
</style>