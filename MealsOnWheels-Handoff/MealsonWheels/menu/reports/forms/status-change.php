<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Status Change Report</h1>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="status-change" style="display: none;">
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
        <div class="inputRow">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="all" selected>All</option>
                <?php foreach ($db->select('SELECT id,name FROM clientStatus ORDER BY id') as $s) {
                    echo '<option value="' . $s['id'] . '">' . htmlspecialchars($s['name']) . '</option>';
                } ?>
            </select>
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
