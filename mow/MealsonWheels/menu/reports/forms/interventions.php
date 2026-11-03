<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Interventions</h1>
<p style="font-size:13px;color:#555;">Note-log entries where an intervention was recorded (emergency contact called, EMS called, or referral made) within the date range.</p>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="interventions" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="startDate">Start Date</label>
            <input value="<?= date('Y-01-01') ?>" type="date" name="startDate" id="startDate" required>
        </div>
        <div class="inputRow">
            <label for="endDate">End Date</label>
            <input value="<?= date('Y-m-d') ?>" type="date" name="endDate" id="endDate" required>
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
