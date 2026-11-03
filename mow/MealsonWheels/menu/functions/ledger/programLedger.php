<div>
    <svg class="ledgerLockIcon locked" onclick="toggleLedgerLock()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
    <svg class="ledgerLockIcon unlocked" onclick="toggleLedgerLock()" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>

</div>
<?php

echo '<div id="ledgerTableWrap">';
echo '<table id="ledgerTable" class="locked">';
echo '<tr>';
echo '<th>Route</th>';
for ($i = 0; $i < ($daysInMonth + 1); $i++) {
    if ($i != 0) {
        echo '<th>' . $i . '</th>';
    }
}
echo '</tr>';
function sortRoutes($a, $b)
{
    $numa = (int) extractNumber($a['name']);
    $numb = (int) extractNumber($b['name']);
    return $numa - $numb;
}
$routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
usort($routes, 'sortRoutes');
$routeHotTotals = [0];
$routeFroTotals = [0];
foreach ($routes as $r) {
    echo '<tr>';
    echo '<td class="routeRowTitle">';
    echo $r['name'];
    echo '</td>';

    $recipents = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ?', [$r['id'], 1]);


    for ($i = 0; $i < ($daysInMonth + 1); $i++) {
        if ($i != 0) {
            $loopDate = (new DateTime())->setDate((int)$date->format('Y'), (int)$date->format('m'), $i);
            foreach ($recipents as &$recip) {
                $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [$recip['id'], 1, $loopDate->format('Y-m-d'), $loopDate->format('Y-m-d')])[0];
                if ($currSchedule == false) {
                    $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [1, $loopDate->format('Y-m-d'), $loopDate->format('Y-m-d')])[0];
                }
                $recip['mealMatrix'] = $currSchedule['mealCounts'];
            }

            $ledger = $db->select('SELECT * FROM mealLedgers WHERE dateStamp = ?', [$loopDate->format('Y-m-d')])[0];

            if ($ledger != false) {
                $ledgerMatrix = explode(',', $ledger['dataMatrix']);
                foreach ($ledgerMatrix as $lm) {
                    $matrixData = explode('-', $lm);
                    if ((int)$matrixData[0] == (int)$r['id']) {
                        echo '<td class="hasLedger" onclick="drillProgramView(this,' . $r['id'] . ',`' . $loopDate->format('Y-m-d') . '`)">' . $matrixData[1] . '-' . $matrixData[2] . '</td>';
                    }
                }
            } else {


                $hotCount = 0;
                $frozenCount = 0;


                foreach ($recipents as $rec) {
                    $dayLists = explode(',', $rec['mealMatrix']);
                    for ($j = 0; $j < 7; $j++) {
                        if (($j + 1) == (int)$loopDate->format('N')) {
                            
                            $checkDate = $loopDate->format('Y-m-d');
                            $offDay = $db->select(
                                'SELECT id 
                                FROM daysOff 
                                WHERE (
                                    (indefinate = 0 AND startDate <= ? AND endDate >= ?) 
                                    OR 
                                    (indefinate = 1 AND startDate <= ?)
                                ) 
                                AND deactivate = 0 
                                AND recipient_id = ?',
                                [$checkDate, $checkDate, $checkDate, $rec['id']]
                            )[0];
                            
                            if(!$offDay){
                                $dayListX = explode('-', $dayLists[$j]);
                                $hotCount += $dayListX[1];
                                $frozenCount += $dayListX[2];
                                $routeHotTotals[$i] = $routeHotTotals[$i] + $dayListX[1];
                                $routeFroTotals[$i] = $routeFroTotals[$i] + $dayListX[2];
                            }
                        }
                    }
                }
                echo '<td onclick="drillProgramView(this,' . $r['id'] . ',`' . $loopDate->format('Y-m-d') . '`)">' . $hotCount . '-' . $frozenCount . '</td>';
            }
        }
    }

    echo '</tr>';
}
echo '<tr>';
echo '<td class="routeRowTitle">System Meals</td>';
$totalSystemMeals_hot = 0;
$totalSystemMeals_fro = 0;
for ($i = 0; $i < ($daysInMonth + 1); $i++) {
    if ($i != 0) {
        $systemSearchDate = ((new DateTime())->setDate((int)$date->format('Y'), (int)$date->format('m'), $i))->setTime(0, 0, 0);
        $systemMeals = $db->select('SELECT * FROM systemMeals WHERE dateStamp = ?', [$systemSearchDate->format('Y-m-d')]);
        $systemMeals_hot = 0;
        $systemMeals_fro = 0;
        if ($systemMeals != false) {
            foreach ($systemMeals as $sm) {
                $systemMeals_hot += $sm['hot'];
                $systemMeals_fro += $sm['frozen'];
            }
        }
        echo '<td>' . $systemMeals_hot . '-' . $systemMeals_fro . '</td>';
        $totalSystemMeals_hot += $systemMeals_hot;
        $totalSystemMeals_fro += $systemMeals_fro;
    }
}

echo '</tr>';
echo '<tr>';
echo '<td class="routeRowTitle">Total</td>';
for ($i = 1; $i < ($daysInMonth + 1); $i++) {
    $loopDate = (new DateTime())->setDate((int)$date->format('Y'), (int)$date->format('m'), $i);
    $ledger = $db->select('SELECT * FROM mealLedgers WHERE dateStamp = ?', [$loopDate->format('Y-m-d')])[0];

    if ($ledger != false) {
        $ledgerTotalHot = $totalSystemMeals_hot;
        $ledgerTotalFro = $totalSystemMeals_fro;
        $ledgerMatrix = explode(',', $ledger['dataMatrix']);
        foreach ($ledgerMatrix as $lm) {
            $matrixData = explode('-', $lm);
            if (in_array((int)$matrixData[0], array_column($routes, 'id'))) {
                $ledgerTotalHot += $matrixData[1];
                $ledgerTotalFro += $matrixData[2];
            }
        }
        echo '<td class="hasLedger">' . $ledgerTotalHot . '-' . $ledgerTotalFro . '</td>';
    } else {
        echo '<td>' . $routeHotTotals[$i] . '-' . $routeFroTotals[$i] . '</td>';
    }
}

echo '</tr>';
echo '</table>';
echo '</div>';
