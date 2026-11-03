<?php
// This script is controlled by users clicking 'Compile Ledge' by clicking on a cell in the program ledger,
// or by an automatic cron job set to trigger every day at 11:55 PM CST
// CRON JOB INFO:
// https://cron-job.org
// LOGIN:
// email: brad@perspektive.media
// paswd: perspektive123
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');

$validToken = '02eiuhg083ehu9qehg91q3uh';

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' &&
    (!isset($_GET['x']) || $_GET['x'] !== $validToken)
) {
    exit('Access Error');
}


if (isset($_POST['date'])) {
    $date = new DateTime($_POST['date']);
} else {
    if(isset($_GET['date'])){
        $date = new DateTime($_GET['date']);
    } else {
        $date = new DateTime();
    }
}

$weekDayNumber = $date->format('N');
$daysInMonth = (int)$date->format('t');

function sortRoutes($a, $b)
{
    $numa = (int) extractNumber($a['name']);
    $numb = (int) extractNumber($b['name']);
    return $numa - $numb;
}
$routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
usort($routes, 'sortRoutes');
$routeHotTotals = [];
$routeFroTotals = [];
$matrixArray = [];

$recipMatrix = '';

foreach ($routes as $r) {

    $matrixArray[$r['id']]['hot'] = 0;
    $matrixArray[$r['id']]['fro'] = 0;
    $recipents = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$r['id'], 1]);
    foreach ($recipents as &$recip) {
        $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [$recip['id'], 1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];
        if ($currSchedule == false) {
            $currSchedule = $db->select('SELECT mealCounts FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC', [1, $date->format('Y-m-d'), $date->format('Y-m-d')])[0];
        }
        $recip['mealMatrix'] = $currSchedule['mealCounts'];
    }
    $routeTotalHot = 0;
    $routeTotalFro = 0;
    for ($i = 1; $i < ($daysInMonth + 1); $i++) {
        if ($i != 0 && $i == (int)$date->format('j')) {
            $loopDate = (new DateTime())->setDate((int)$date->format('Y'), (int)$date->format('m'), $i);

            $recipIndex = 0;
            foreach ($recipents as $rec) {
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
                $dayLists = explode(',', $rec['mealMatrix']);
                $recipData = '';
                for ($j = 0; $j < 7; $j++) {
                    if (($j + 1) == (int)$loopDate->format('N')) {
                        $dayListX = explode('-', $dayLists[$j]);

                        if($offDay){
                            $recipData = '0-0';
                        } else {
                            $recipData = $dayListX[1] . '-' . $dayListX[2];
                            $routeTotalHot += $dayListX[1];
                            $routeTotalFro += $dayListX[2];
                        }
                        
                        // $routeHotTotals[$i] = $routeHotTotals[$i] + $dayListX[1];
                        // $routeFroTotals[$i] = $routeFroTotals[$i] + $dayListX[2];
                    }
                }
                if ($recipData !== '') {
                    $recipMatrix .= ($recipMatrix !== '' ? ',' : '') . $rec['id'] . '-' . $recipData;
                }
            }
        }
    }
    $matrixArray[$r['id']]['hot'] += $routeTotalHot;
    $matrixArray[$r['id']]['fro'] += $routeTotalFro;
}

$matrix = '';
foreach ($matrixArray as $index => $row) {
    $delimit = ($index == 1) ? '' : ',';
    $matrix .= $delimit . $index . '-' . $row['hot'] . '-' . $row['fro'];
}

$db->delete('DELETE FROM mealLedgers WHERE dateStamp = ?', [$date->format('Y-m-d')]);
$insert = $db->insert('INSERT INTO mealLedgers (dateStamp, dataMatrix, recipMatrix) VALUES (?,?,?)', [$date->format('Y-m-d'), $matrix, $recipMatrix]);
if ($insert == false) {
    exit($db->error);
} else {
    exit('ok');
}
