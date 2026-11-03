<?php

foreach ($recipients as $index => $recip) {
    $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [$recip['id'], 1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];

    if ($currSchedule == false) {
        $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];
    }

    $recip['mealMatrix'] = $currSchedule['mealCounts'];

    $days = explode(',', $recip['mealMatrix']);
    $day = $days[((int)$date->format('N') - 1)];
    $dayValue = explode('-', $day);
    $recip['on'] = ($dayValue[0] == 1) ? 1 : 0;
    $recip['froAmount'] = $dayValue[2];

    $offDay = $db->select('SELECT * FROM daysOff WHERE ((startDate <= ? AND endDate >= ? ) OR (startDate <= ? AND indefinate = 1)) AND recipient_id = ? ORDER BY id DESC', [$date->format('Y-m-d'), $date->format('Y-m-d'), $date->format('Y-m-d'), $recip['id']])[0];
    $recip['textClass'] = ($offDay == false) ? '' : ' offToday';
    if ($offDay['indefinate'] == 1) {
        $recip['textClass'] = ' offTemp';
    }
    $recipients[$index] = $recip;
}


echo '<table class="labelTable">';

$chunks = array_chunk($recipients, 8);
foreach ($chunks as $chunkIndex => $chunk) {
    // echo "<tr><td colspan='2'><strong>Page " . ($chunkIndex + 1) . "</strong></td></tr>";

    // Split 8 recipients into rows of 2
    $rows = array_chunk($chunk, 2);

    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $recip) {
            echo '<td class="recipBlock">';
            if($recip['on'] == 1){

                echo '<h3 style="color: ' . $route['color'] . '">' . strtoupper($route['name']) . '</h3>';
                echo '<h2>' . strtoupper($recip['lastName'] . ', ' . $recip['firstName']) . '</h2>';
                $diets = explode(',', $recip['diets']);
                $dietList = $db->select('SELECT * FROM diets WHERE id IN (' . $recip['diets'] . ')', []);
                $dietsName = ($dietList == false) ? 'GEN' : strtoupper(implode(', ', array_column($dietList, 'abbre')));
                $milk = $db->select('SELECT * FROM milkChoices WHERE id = ?', [$recip['hotMilk_id']])[0];
                $milkName = $milk['abbre'] ?? 'NONE';
                echo '<h3>' . $dietsName . '<span class="spacer"></span>' . $milkName . '</h3>';
                $accommList = $db->select('SELECT * FROM accommodations WHERE id IN (' . $recip['accommodations'] . ')', []);
                $accommName = ($accommList == false) ? '' : implode(', ', array_column($accommList, 'abbr'));
                echo '<h3 class="accommodations">' . $accommName . '</h3>';
                $allergies = $db->select('SELECT * FROM allergies WHERE id IN (' . $recip['allergies'] . ')', []);
                $allergiesList = ($allergies == false) ? '' : 'ALLERGIES: ' . strtoupper(implode(', ', array_column($allergies, 'abbr')));
                echo '<h3 class="allergies">' . $allergiesList . '</h3>';
                echo '<h2>' . $recip['froAmount'] . ' FROZ</h2>';
            }
            
            echo '</td>';
        }

        // If there's only 1 recipient in the last row, add an empty <td>
        if (count($row) < 2) {
            echo '<td class="recipBlock"></td>';
        }

        echo '</tr>';
    }

}

echo '</table>';
echo '<div style="page-break-after: always;"></div>';