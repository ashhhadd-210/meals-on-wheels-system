<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?',[$_POST['recip_id']])[0];
?>
<form action="" id="routeChangeForm">
    <button type="button" class="dark" onclick="fillScheduleChanges()">Back</button>
    <div class="formRow">
        <div class="inputRow">
            <label for="">Recipient</label>
            <input style="opacity: 0.5;" type="text" value="<?php echo $recip['firstName'] . ' ' . $recip['lastName'] ?>" readonly>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="route_id">Route</label>
            <select name="route_id" id="route_id" required>
                <option value="" hidden disabled selected>Select</option>
                <?php
                function sortRoutes($a, $b)
                {
                    $numa = (int) extractNumber($a['name']);
                    $numb = (int) extractNumber($b['name']);
                    return $numa - $numb;
                }
                $routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
                usort($routes, 'sortRoutes');
                foreach($routes as $route){
                    $selected = ($recip['route_id'] == $route['id']) ? ' selected' : '';
                    echo '<option value="'.$route['id'].'"'.$selected.'>'.$route['name'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="position">New Position</label>
            <input type="number" min="1" name="position" id="position" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="date">Date</label>
            <input type="date" name="date" id="date" required>
        </div>
    </div>

    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Add</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(()=>{
        $('#routeChangeForm').on('submit',(e)=>{
            e.preventDefault()
            var formData = new FormData($('#routeChangeForm')[0]);
            formData.append('recip_id',<?php echo $recip['id'] ?>)
            formData.append('contentType','routeChange')
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                contentType: false,
                processData: false,
                data: formData,
                success: (re)=>{
                    re = re.trim();
                    if(re == 'ok'){
                        customModalClear()
                    } else if(re == 'reload') {
                        window.location.reload()
                    } else {
                        customMessage(re,3)
                    }
                }
            })
        })
    })
</script>
<style>
    #routeChangeForm {
	padding: 20px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}
</style>