<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Insurance Billing</h1>
<form action="" id="reportForm">
    <div class="formRow">
        <div class="inputRow">
            <label for="startDate">Start Date</label>
            <input type="date" name="startDate" id="startDate" required>
        </div>
        <div class="inputRow">
            <label for="endDate">End Date</label>
            <input type="date" name="endDate" id="endDate" required>
        </div>

    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="recipType">Recipient Status</label>
            <div class="formRow">
                <div><input type="radio" value="all" id="recipType-all" name="recipType" checked><label for="recipType-all">All</label></div>
                <?php
                $statusTypes = $db->select('SELECT * FROM clientStatus');
                foreach ($statusTypes as $type) {
                    echo '<div><input value="' . $type['id'] . '" id="recipType-' . $type['id'] . '" type="radio" name="recipType"><label for="recipType-' . $type['id'] . '">' . $type['name'] . '</label></div>';
                }
                ?>
            </div>
        </div>
    </div>
    <input type="text" name="reportType" value="insuranceBilling" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Generate</button>
        </div>
    </div>
</form>
<script>
    $('#reportForm').on('submit', (e) => {
        e.preventDefault();
        var action = '/viewReport';
        var data = $('#reportForm').serialize();
        window.open(action + '?' + data, '_blank');
    })
</script>