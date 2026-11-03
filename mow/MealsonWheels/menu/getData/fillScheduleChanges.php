<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?',[$_POST['recip_id']])[0];
?>
<div id="changeList">
    <button type="button" class="addConentBtn" onclick="addRouteChange()">Add Route Change</button>
    <?php
    $changes = $db->select('SELECT * FROM routeChanges WHERE recipient_id = ? ORDER BY dateStamp DESC',[$_POST['recip_id']]);
    if($changes != false){
        foreach($changes as $change){
            $route = $db->select('SELECT * FROM routes WHERE id = ?',[$change['route_id']])[0];
            $delete = ($change['ran'] == 1) ? '' : '<button onclick="deleteRouteChange('.$change['id'].',this)" type="button" class="deleteBtn">Delete</button>';
            echo '<div class="contentRow">'.date('m/d/y',strtotime($change['dateStamp'])) . ' - ' . $route['name'].$delete.'</div>';
        }
    } else {
        echo '<div class="contentRow">No Records</div>';
    }
    ?>
</div>