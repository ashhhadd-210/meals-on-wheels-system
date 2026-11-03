<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$route = $db->select('SELECT * FROM routes WHERE id = ?', [$_POST['route_id']])[0];
if ($route == false) {
    exit('Could not find this route.');
}
$date = new DateTime($_POST['date']);
$recipents = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$route['id'], 1]);
if ($recipents != false) {
    foreach ($recipents as &$recip) {
        $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [$recip['id'], 1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];
        if ($currSchedule == false) {
            $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];
        }
        $recip['mealMatrix'] = $currSchedule['mealCounts'];
    }
}
$dayNumber = (int)$date->format('N');

$ledger = $db->select('SELECT * FROM mealLedgers WHERE dateStamp = ?', [$date->format('Y-m-d')])[0];

// echo $hotCount.'-'.$frozenCount;
?>
<div style="padding:20px">
    <div class="formRow" style="align-items: center;">
        <h3><?= '<span style="color:'.$route['color'].'">'.$route['name'].'</span> - ' . $date->format('D M Y (m/d/y)') ?></h3>

        <button style="margin-left: auto;" type="button" class="addConentBtn" onclick="insertLedger()"><?= (($ledger == false) ? 'Compile' : 'Recompile') ?> Ledger</button>
    </div>
    <div class="inputRow">
        <?php
        if (count($recipents) > 0) {
            $hotCount = 0;
            $frozenCount = 0;
            foreach ($recipents as $rec) {
                echo '<div class="recipDrillWrap">';
                echo '<div>';
                echo '<a style="display:flex; gap: 5px; align-items: center;" href="/functions/redirects.php/?re=recipient&id='.$rec['id'].'">
                <svg style="width:15px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                <h3>' . $rec['firstName'] . ' ' . $rec['lastName'] . '</h3></a>';
                echo '</div>';
                if ($ledger == false) {

                    $dayLists = explode(',', $rec['mealMatrix']);
                    for ($j = 0; $j < 7; $j++) {
                        if (($j + 1) == $dayNumber) {

                            $checkDate = $date->format('Y-m-d');
                            $offDay = $db->select(
                                'SELECT reason 
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

                            if ($offDay) {

                                echo '<div class="recipDrillTotalWrap">';
                                echo '<div class="recipDrillTotal">';
                                echo '<p>Hot Meals</p>';
                                echo '<span>0</span>';
                                echo '<small style="display:block;margin-top:5px;color:red;">' . $offDay['reason'] . '</small>';
                                echo '</div>';
                                echo '<div class="recipDrillTotal">';
                                echo '<p>Frozen Meals</p>';
                                echo '<span>0</span>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                $dayListX = explode('-', $dayLists[$j]);
                                $hotCount += $dayListX[1];
                                $frozenCount += $dayListX[2];

                                echo '<div class="recipDrillTotalWrap">';
                                echo '<div class="recipDrillTotal">';
                                echo '<p>Hot Meals</p>';
                                echo '<span>' . $dayListX[1] . '</span>';
                                echo '</div>';
                                echo '<div class="recipDrillTotal">';
                                echo '<p>Frozen Meals</p>';
                                echo '<span>' . $dayListX[2] . '</span>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                    }
                } else {
                    $checkDate = $date->format('Y-m-d');
                    $offDay = $db->select(
                        'SELECT reason 
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

                    if($offDay){
                        $offDayText = '<small style="display:block;margin-top:5px;color:red;">' . $offDay['reason'] . '</small>';
                    } else {
                        $offDayText = '';
                    }

                    $recipMatrixList = explode(',', $ledger['recipMatrix']);
                    foreach ($recipMatrixList as $rml) {
                        $frozen = 0;
                        $hot = 0;
                        list($recipId, $hot, $frozen) = explode('-', $rml);
                        if($offDay){
                            $frozen = 0;
                            $hot = 0;
                        }
                        if ($recipId == $rec['id']) {
                            $hotCount += $hot;
                            $frozenCount += $frozen;

                            echo '<div class="recipDrillTotalWrap hasLedger">';
                            echo '<div class="recipDrillTotal">';
                            echo '<p>Hot Meals</p>';
                            echo '<span>' . $hot . '</span>';
                            echo $offDayText;
                            echo '</div>';
                            echo '<div class="recipDrillTotal">';
                            echo '<p>Frozen Meals</p>';
                            echo '<span>' . $frozen . '</span>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
                echo '</div>';
            }

            echo '<div class="recipDrillWrap' . (($ledger == false) ? '' : ' hasLedger') . '" style="background-color:#c6c6c6">';
            echo '<h3>Total Recipient Meals</h3>';
            echo '<div class="recipDrillTotalWrap">';
            echo '<div class="recipDrillTotal">';
            echo '<p>Hot Meals</p>';
            echo '<span>' . $hotCount . '</span>';
            echo '</div>';
            echo '<div class="recipDrillTotal">';
            echo '<p>Frozen Meals</p>';
            echo '<span>' . $frozenCount . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            $systemMeals = $db->select('SELECT * FROM systemMeals WHERE dateStamp = ? AND route_id = ?', [$date->format('Y-m-d'), $route['id']])[0];
            if ($systemMeals == false) {
                $systemMeals_hot = 0;
                $systemMeals_fro = 0;
                $totalMeals_hot = $hotCount;
                $totalMeals_fro = $frozenCount;
            } else {
                $systemMeals_hot = $systemMeals['hot'];
                $systemMeals_fro = $systemMeals['frozen'];
                $totalMeals_hot = $hotCount + $systemMeals['hot'];
                $totalMeals_fro = $frozenCount + $systemMeals['frozen'];
            }

            echo '<div class="recipDrillWrap" style="background-color:#c6c6c6">';
            echo '<h3>Sytem Meals</h3>';
            echo '<div class="recipDrillTotalWrap" style="align-items: end">';
            echo '<div class="recipDrillTotal">';
            echo '<p>Hot Meals</p>';
            echo '<input style="width: 100px" type="number" id="systemMeals_hot" value="' . $systemMeals_hot . '" min="0">';
            echo '</div>';
            echo '<div class="recipDrillTotal">';
            echo '<p>Frozen Meals</p>';
            echo '<input style="width: 100px" type="number" id="systemMeals_fro" value="' . $systemMeals_fro . '" min="0">';
            echo '</div>';
            echo '<button type="button" class="addConentBtn" onclick="updateSystemMeals()">Save</button>';
            echo '</div>';
            echo '</div>';

            echo '<div class="recipDrillWrap" style="background-color:#c6c6c6">';
            echo '<h3>Total</h3>';
            echo '<div class="recipDrillTotalWrap">';
            echo '<div class="recipDrillTotal">';
            echo '<p>Hot Meals</p>';
            echo '<span>' . $totalMeals_hot . '</span>';
            echo '</div>';
            echo '<div class="recipDrillTotal">';
            echo '<p>Frozen Meals</p>';
            echo '<span>' . $totalMeals_fro . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p>No Recipients on this route</p>';
        }
        ?>
    </div>
</div>


<script>
    function updateSystemMeals() {
        var systemMeals_hot = $('#systemMeals_hot').val().trim();
        var systemMeals_fro = $('#systemMeals_fro').val().trim();
        if (systemMeals_hot == '') {
            customMessage('Enter a hot meal amount', 3);
            return false;
        }
        if (systemMeals_fro == '') {
            customMessage('Enter a frozen meal amount', 3);
            return false;
        }
        $.ajax({
            url: 'menu/functions/ledger/updateSystemMeals.php',
            type: 'POST',
            data: {
                hot: systemMeals_hot,
                fro: systemMeals_fro,
                route_id: <?= $route['id'] ?>,
                date: `<?= $date->format('Y-m-d') ?>`
            },
            success: (re) => {
                if (re.trim() == 'ok') {
                    customMessage('System meals updated!', 1, false, true);
                    clearModal();
                    getLedger();
                } else {
                    customMessage(re, 3);
                }
            },
            error: (xhr, status, error) => {
                console.error("Error:", status, error);
            }
        });
    }

    function insertLedger() {
        $.ajax({
            url: 'menu/functions/ledger/insertNewLedger.php',
            type: 'POST',
            data: {
                date: `<?php echo $date->format('Y-m-d') ?>`,
                route_id: <?php echo $route['id'] ?>
            },
            success: (re) => {
                if (re.trim() == 'ok') {
                    customMessage('Added', 1, false, true);
                    clearModal();
                    getLedger();
                } else {
                    customMessage(re, 3);
                }
            },
            error: (xhr, status, error) => {
                console.error("Error:", status, error);
            }
        });
    }
</script>