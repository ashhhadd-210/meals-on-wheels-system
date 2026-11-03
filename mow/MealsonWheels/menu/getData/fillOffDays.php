<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}

if($_POST['show'] == 'less'){
    $offDays  = $db->select('SELECT * FROM daysOff WHERE recipient_id = ? AND startDate >= ? ORDER BY startDate ASC', [$_POST['recip_id'], date('Y-m-d')]);
    $btnFill = 'all';
    $btnText = 'Show All';
} else if ($_POST['show'] == 'all') {
    $offDays  = $db->select('SELECT * FROM daysOff WHERE recipient_id = ? ORDER BY startDate DESC', [$_POST['recip_id']]);
    $btnFill = 'less';
    $btnText = 'Show Upcoming';
}
?>
<div class="offDaysHeader">
    <button class="addConentBtn" type="button" onclick="addOffDay()">Add</button>
</div>

<?php
echo '<div class="offDayListWrap">';
foreach ($offDays as $days) {
    $endDate = (empty($days['indefinate'])) ? date('m/d/y', strtotime($days['endDate'])) : 'off Temp';
    $deactive = (empty($days['deactivate'])) ? '' : ' deavtive';
    echo '<div class="offDayRow'.$deactive.'" onclick="editAOffDay(' . $days['id'] . ')">' . date('m/d/y', strtotime($days['startDate'])) . ' - ' . $endDate . ' ('.$days['reason'].')</div>';
}
echo '<div onclick="fillOffDays(\''.$btnFill.'\')" class="offDayRow toggleBtn">'.$btnText.'</div>';
echo '</div>';
?>
<style>
    .offDayRow.toggleBtn {
	background-color: var(--blue);
	color: white;
}
</style>