<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Assessment Due</h1>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="assessment-due" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="asOf">Due on or before</label>
            <input value="<?= date('Y-m-d') ?>" type="date" name="asOf" id="asOf" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Generate</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(() => {
        $('#reportForm').on('submit', (e) => {
            e.preventDefault();
            window.open('/viewReport?' + $('#reportForm').serialize(), '_blank');
        })
    })
</script>
