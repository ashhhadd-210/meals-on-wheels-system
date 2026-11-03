<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Under 60</h1>
<p style="font-size:13px;color:#555;">Active recipients whose age is under 60 as of the selected date.</p>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="under-60" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="asOf">As of date</label>
            <input value="<?= date('Y-m-d') ?>" type="date" name="asOf" id="asOf" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow"><button type="submit">Generate</button></div>
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
