<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$cert = $db->select('SELECT * FROM certificateLogs WHERE id = ?', [$_POST['id']])[0];
?>
<form action="" method="post" id="editCertForm">
    <input type="text" style="display: none;" name="id" value="<?php echo $cert['id'] ?>">
    <input type="text" style="display: none;" name="contentType" value="cert">
    <div class="formRow">
        <div class="inputRow">
            <label for="startDate">Start Date</label>
            <input type="date" value="<?php echo date('Y-m-d', strtotime($cert['startDate'])) ?>" name="startDate" required>
        </div>
        <div class="inputRow">
            <label for="endDate">End Date</label>
            <input type="date" value="<?php echo date('Y-m-d', strtotime($cert['endDate'])) ?>" name="endDate" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="physician_id">Physician</label>
            <select name="cert_physician_id" id="cert_physician_id" required>
                <?php
                $physicians = $db->select('SELECT * FROM physicians ORDER BY firstName');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($physicians as $physician) {
                    $slected = ($physician['id'] == $cert['physician']) ? ' selected' : '';
                    echo '<option value="' . $physician['id'] . '"' . $slected . '>' . $physician['firstName'] . ' ' . $physician['lastName'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="cert_clinic_id">Clinic</label>
            <select name="cert_clinic_id" id="cert_clinic_id">
                <?php
                $clinics = $db->select('SELECT * FROM clinics ORDER BY name');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($clinics as $clinic) {
                    $slected = ($clinic['id'] == $cert['clinic']) ? ' selected' : '';
                    echo '<option value="' . $clinic['id'] . '"' . $slected . '>' . $clinic['name'] .  '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Update</button>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button onclick="deleteCert()" class="deleteBtn" type="button">Delete</button>
        </div>
    </div>
</form>
<script>
    function deleteCert() {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    id: <?php echo $cert['id'] ?>,
                    contentType: 'cert'
                },
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        customModalClear()
                        fillCertData()
                    } else {
                        customMessage(response, 3)
                    }
                }
            })
        }
    }
    $(document).ready(function() {
        $('#editCertForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: $(this).serialize(),
                success: function(response) {
                    customModalClear()
                    fillCertData()
                }
            })
        })
    })
</script>
<style>
    #editCertForm {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
</style>