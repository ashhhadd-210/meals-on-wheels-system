<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Physician Certification Due</h1>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="physician-cert-due" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="daysAhead">Due within (days)</label>
            <input value="30" min="0" type="number" name="daysAhead" id="daysAhead" required>
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
