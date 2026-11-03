<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Closed Call List</h1>
<p style="font-size:13px;color:#555;">Lists recipients whose current status is <b>Non Active</b> (closed cases), with contact details and the date they were last changed to Non Active.</p>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="closed-call-list" style="display: none;">
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
