<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] == 1) {
    $indiv = $db->select('SELECT * FROM meals WHERE recipient_id = ? AND main != ? ORDER BY startDate DESC', [$_POST['recipient_id'], 1]);
    $mains = $db->select('SELECT * FROM meals WHERE main = ? ORDER BY startDate DESC', [1]);

    $currSchedule = $db->select('SELECT id FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC',[$_POST['recipient_id'],1,date('Y-m-d'),date('Y-m-d')])[0];
    if($currSchedule == false){
        $currSchedule = $db->select('SELECT id FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC',[1,date('Y-m-d'),date('Y-m-d')])[0];
    }
    if (empty($_POST['selected'])) {
        $selected = false;
    } else {
        $selected = (int) $_POST['selected'];
    }
    echo '<p>Default Schedules</p>';
    echo '<div class="mealScheduleBin">';
    foreach ($mains as $meal) {
        $activeSchedule = ($meal['id'] == $currSchedule['id']) ? ' activeSchedule' : '';
        $active = ($selected == $meal['id']) ? ' active' : '';
        $endDate = (empty($meal['inf'])) ? date('m/d/y', strtotime($meal['endDate'])) : 'Indef';
        echo '<div class="recipMealSelect'. $activeSchedule . $active . '" id="openScheduleBtn' . $meal['id'] . '" onclick="openSchdeule(' . $meal['id'] . ')">' . $meal['name'] . ' (' . date('m/d/y', strtotime($meal['startDate'])) . ' - ' . $endDate . ')</div>';
    }
    if ($mains == false) {
        echo '<div>No Meal Schedules</div>';
    }
    echo '</div>';

    echo '<p>Alternative Schedules</p>';
    echo '<div class="mealScheduleBin">';
    foreach ($indiv as $meal) {
        $activeSchedule = ($meal['id'] == $currSchedule['id']) ? ' activeSchedule' : '';
        $active = ($selected == $meal['id']) ? ' active' : '';
        $endDate = (empty($meal['inf'])) ? date('m/d/y', strtotime($meal['endDate'])) : 'Indef';
        echo '<div class="recipMealSelect' . $activeSchedule . $active . '" id="openScheduleBtn' . $meal['id'] . '" onclick="openSchdeule(' . $meal['id'] . ')">' . $meal['name'] . ' (' . date('m/d/y', strtotime($meal['startDate'])) . ' - ' . $endDate . ')</div>';
    }
    if ($indiv == false) {
        echo '<div>No Meal Schedules</div>';
    }
    echo '</div>';
}
