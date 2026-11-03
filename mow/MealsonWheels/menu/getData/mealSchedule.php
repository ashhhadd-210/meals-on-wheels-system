<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    $meal = $db->select('SELECT * FROM meals WHERE id = ?', [$_POST['id']])[0];


    if (isset($_POST['mY'])) {
        $_SESSION['schedule_mY'] = $_POST['mY'];
        $year = date('Y', strtotime($_POST['mY']));
        $month = date('m', strtotime($_POST['mY']));
    } else {
        if (isset($_SESSION['schedule_mY'])) {
            $year = date('Y', strtotime($_SESSION['schedule_mY']));
            $month = date('m', strtotime($_SESSION['schedule_mY']));
        } else {
            $year = date('Y');
            $month = date('m');
        }
    }


    $startDate = date('Y-m-d', strtotime($meal['startDate']));
    $endDate = (empty($meal['inf'])) ? date('Y-m-d', strtotime($meal['endDate'])) : '2200-01-01';

    $onDays = array();
    $offDays = $db->select('SELECT * FROM daysOff WHERE recipient_id = ? AND startDate >= ?', [$_POST['recip_id'], $meal['startDate']]);

    if ($meal['monday'] == 1) {
        $onDays[] = 'Monday';
    }
    if ($meal['tuesday'] == 1) {
        $onDays[] = 'Tuesday';
    }
    if ($meal['wednesday'] == 1) {
        $onDays[] = 'Wednesday';
    }
    if ($meal['thursday'] == 1) {
        $onDays[] = 'Thursday';
    }
    if ($meal['friday'] == 1) {
        $onDays[] = 'friday';
    }
    if ($meal['saturday'] == 1) {
        $onDays[] = 'Saturday';
    }
    if ($meal['sunday'] == 1) {
        $onDays[] = 'Sunday';
    }

    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    $monthName = date('F', $firstDayOfMonth);

    $daysInMonth = date('t', $firstDayOfMonth);

    $dayOfWeek = date('w', $firstDayOfMonth);
    echo '<div class="scheduleDisplayWrap">';
    $endDateDisplay = (empty($meal['inf'])) ? date('m/d/y', strtotime($meal['endDate'])) : 'Indef';
    echo '<h2>' . $meal['name'] . ' (' . date('m/d/y', strtotime($meal['startDate'])) . ' - ' . $endDateDisplay . ')<span class="mealScheduleEditBtn" onclick="editMealSchedule(' . $meal['id'] . ')">Edit</span></h2>';
    echo '<input id="currentScheduleId" value="'.$meal['id'].'" hidden>';
    echo '<div class="calendarDateSelectWrap">';
    echo '<select onchange="updateCalendar(this,' . $meal['id'] . ')" id="monthSelect">';
    for ($m = 1; $m <= 12; $m++) {
        $select = ($m == $month) ? ' selected' : '';
        echo '<option value="' . str_pad($m, 2, '0', STR_PAD_LEFT) . '"' . $select . '>' . date('F', mktime(0, 0, 0, $m, 10)) . '</option>';
    }
    echo '</select>';
    echo '<select onchange="updateCalendar(this,' . $meal['id'] . ')" id="yearSelect">';
    for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) {
        $select = ($y == $year) ? ' selected' : '';
        echo '<option value="' . $y . '"' . $select . '>' . $y . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<table border="1" class="schdeuleCalendar">';
    echo '<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>';
    echo "<tr>";

    for ($i = 0; $i < $dayOfWeek; $i++) {
        $dayInPreviousMonth = date('d', strtotime("-" . ($dayOfWeek - $i) . " days", $firstDayOfMonth));
        echo "<td class=\"empty\"><span class=\"dayNumber\">$dayInPreviousMonth</span></td>";
    }

    $currentDay = 1;
    $indefinate = false;
    while ($currentDay <= $daysInMonth) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            echo "</tr><tr>";
        }

        $loopedDate = date('Y-m-d', strtotime($year . '-' . $month . '-' . $currentDay));
        if (($loopedDate >= $startDate && $loopedDate <= $endDate) && (in_array(date('l', strtotime($loopedDate)), $onDays))) {
            $content = '';
            $class = ' onDay';

            foreach ($offDays as $offDay) {
                if (empty($offDay['deactivate'])) {

                    if ($offDay['indefinate'] == 1 && empty($offDay['deactivate'])) {
                        if ($loopedDate >= date('Y-m-d', strtotime($offDay['startDate']))) {
                            $content = $offDay['reason'];
                            $class = ' inScheduleoffDay';
                            $indefinate = true;
                        }
                    } else {
                        if ($loopedDate >= date('Y-m-d', strtotime($offDay['startDate'])) && $loopedDate <= date('Y-m-d', strtotime($offDay['endDate']))) {
                            $class = ' inScheduleoffDay';
                            $content = $offDay['reason'];
                        }
                    }
                }
            }
        } else {
            $content = '';
            $class = '';
            foreach ($offDays as $offDay) {
                if (empty($offDay['deactivate'])) {

                    if ($offDay['indefinate'] == 1 && empty($offDay['deactivate'])) {
                        if ($loopedDate >= date('Y-m-d', strtotime($offDay['startDate']))) {
                            $content = $offDay['reason'];
                            $class = ' offDay';
                            $indefinate = true;
                        }
                    } else {
                        if ($loopedDate >= date('Y-m-d', strtotime($offDay['startDate'])) && $loopedDate <= date('Y-m-d', strtotime($offDay['endDate']))) {
                            $class = ' offDay';
                            $content = $offDay['reason'];
                        }
                    }
                }
            }
        }

        echo "<td class=\"$class\"><span class=\"dayNumber\">$currentDay</span><span class=\"dayContent\">$content</span></td>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 0) {
        for ($j = $dayOfWeek; $j < 7; $j++) {
            $dayInNextMonth = $j - $dayOfWeek + 1;
            echo "<td class=\"empty\"><span class=\"dayNumber\">$dayInNextMonth</span></td>";
        }
    }

    echo "</tr>";
    echo "</table>";

    echo '</div>';
}
exit();
