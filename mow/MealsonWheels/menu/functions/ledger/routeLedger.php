<?php
function sortRoutes($a, $b)
{
    $numa = (int) extractNumber($a['name']);
    $numb = (int) extractNumber($b['name']);
    return $numa - $numb;
}
$routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
usort($routes, 'sortRoutes');
echo (($ledgerQuery == false) ? 'No Ledger' : 'Ledger Found');


echo '<table id="ledgerTable">';
echo '<tr>';
echo '<th>Recipient</th>';
foreach($routes as $r){
    echo '<th>'.$r['name'].'</th>';
}
echo '</tr>';
$recipients = $db->select('SELECT * FROM recipients ORDER BY lastName, firstName');
foreach ($recipients as $re) {
    $mealNumbers = [0,0];
    if($ledgerQuery == false){
        $currSchedule = $db->select('SELECT id FROM meals WHERE recipient_id = ? AND main != ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC',[$re['id'],1,$date->format('Y-m-d'),$date->format('Y-m-d')])[0];
        if($currSchedule == false){
            $currSchedule = $db->select('SELECT id FROM meals WHERE main = ? AND startDate <= ? AND (endDate IS NULL OR endDate > ?) ORDER BY startDate DESC, id DESC',[1,$date->format('Y-m-d'),$date->format('Y-m-d')])[0];
        }
    }
    echo '<tr>';
    echo '<td>' . $re['firstName'] . ' ' . $re['lastName'] . '</td>';
    foreach($routes as $br){
        if($ledgerQuery == false){
            if($br['id'] == $re['route_id']){
                echo '<td>'.$mealNumbers[0] . ' - ' . $mealNumbers[1] .'</td>';
                echo '<td>'.$route_id.'</td>';
            } else {
                echo '<td>N.O.R</td>';
            }
        }
    }
    echo '</tr>';
}

echo '</table>';