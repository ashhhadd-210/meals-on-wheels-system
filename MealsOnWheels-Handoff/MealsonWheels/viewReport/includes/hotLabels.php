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

    $offDay = $db->select('SELECT * FROM daysOff WHERE ((startDate <= ? AND endDate >= ? ) OR (startDate <= ? AND indefinate = 1)) AND recipient_id = ? ORDER BY id DESC', [$date->format('Y-m-d'), $date->format('Y-m-d'), $date->format('Y-m-d'), $recip['id']])[0];
    $recip['textClass'] = ($offDay == false) ? '' : ' offToday';
    if ($offDay['indefinate'] == 1) {
        $recip['textClass'] = ' offTemp';
    }
    $recipients[$index] = $recip;
}
echo '<table class="labelTable">';
foreach ($recipients as $recip) {
    $person = false;
    if ($recip['on'] == 0) {
        $recip = false;
    }
    if ($recip != false) {
        echo '<tr>';
        echo '<td class="averyCell' . $recip['textClass'] . '">';
        $person = $recip;
        $diets = explode(',', $person['diets']);
        $dietList = $db->select('SELECT * FROM diets WHERE id IN (' . $person['diets'] . ')', []);
        $dietsName = ($dietList == false) ? 'GEN' : strtoupper(implode(', ', array_column($dietList, 'abbre')));
        if ($recip['textClass'] == ' offToday') {
            echo '<div class="bigx">X</div>';
        } else if ($recip['textClass'] == ' offTemp') {
            echo '<div class="bigTemp">OFF TEMP</div>';
        }

        $accommodations = explode(',', $recip['accommodations']);
        $accommList = $db->select('SELECT * FROM accommodations WHERE id IN (' . $recip['accommodations'] . ')', []);
        $accommName = ($accommList == false) ? '' : implode(', ', array_column($accommList, 'abbr'));

        $allergies = $db->select('SELECT * FROM allergies WHERE id IN (' . $recip['allergies'] . ')', []);
        $allergiesList = ($allergies == false) ? '' : 'ALLERGIES: ' . strtoupper(implode(', ', array_column($allergies, 'abbr')));

        echo '<div class="cellText">';

        echo '<table class="textTable">';
        echo '<tr>';
        echo '<td class="textLeft">';
        echo '<span style="color: ' . $route['color'] . '"><b>' . $route['name'] . '</b></span>';
        echo '<br><span class="routePosition' . $recip['textClass'] . '">#' . $person['routePosition'] . ' ' . $person['lastName'] . ', ' . $person['firstName'] . '</span>';
        echo '<br><div><span class="diet">' . $dietsName . '</span> <span class="addInfo"><span class="allergies">' . $allergiesList . '</span> <span class="accommodations">' . $accommName . '</span></span></div>';
        echo '</td>';

        echo '<td class="textRight">';
        $milk = $db->select('SELECT * FROM milkChoices WHERE id = ?', [$person['hotMilk_id']])[0];
        $milkName = $milk['abbre'] ?? 'NONE';
        echo $milkName;
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        echo '</div>';
        echo '</td>';

        echo '<td class="averyGap"></td>';

        echo '<td class="averyCell">';
        echo '<div class="cellText">';
        echo '<small class="instructions">Peel back lid to vent or slit film in each compartment<br>Heat frozen meal in microwave for 3-5 minutes.<br>Conventional oven pre-heat oven to 350 degrees.<br>Place on cookie sheet in oven for 30 minutes.<br>Do not use toaster oven under any circumstances</small>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
    }
}
echo '</table>';
echo '<div style="page-break-after: always;"></div>';