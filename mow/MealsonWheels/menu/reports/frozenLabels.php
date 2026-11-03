<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Frozen Labels</h1>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="frozen-labels" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="route_id">Route</label>
            <select name="route_id" id="route_id" required>
                <option value="" selected hidden>Select Route</option>
                <option value="all">All</option>
                <?php
                function sortRoutes($a, $b)
                {
                    $numa = (int) extractNumber($a['name']);
                    $numb = (int) extractNumber($b['name']);
                    return $numa - $numb;
                }
                $routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
                usort($routes, 'sortRoutes');
                foreach ($routes as $r) {
                    echo '<option value="' . $r['id'] . '">' . $r['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="label_date">Date</label>
            <input value="<?= date('Y-m-d') ?>" type="date" name="label_date" id="label_date" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Generate</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(()=>{
        $('#reportForm').on('submit', (e) => {
            e.preventDefault();
            var action = '/viewReport';
            var data = $('#reportForm').serialize();
            window.open(action + '?' + data, '_blank');
        })
    })
</script>