<?php
// $rcs = $db->select('SELECT * FROM routeChanges WHERE ran = ? AND dateStamp <= ?', [1, date('Y-m-d')]);
// print_r($rcs);
function updateRouteChanges()
{
    global $db;

    $routeChanges = $db->select('SELECT * FROM routeChanges WHERE ran = ? AND dateStamp <= ?', [0, date('Y-m-d')]);
    if ($routeChanges != false) {
        // print_r($routeChanges);
        foreach ($routeChanges as $change) {
            // echo 'line<br>';
            $db->update('UPDATE recipients SET route_id = ?, routePosition = ? WHERE id = ?', [$change['route_id'], $change['newPosition'], $change['recipient_id']]);
            $recips = $db->select('SELECT * FROM recipients WHERE route_id = ? ORDER BY routePosition ASC',[$change['route_id']]);
            // print_r($recips);
            $index = 1;
            foreach($recips as $rec){
                if($index != $change['newPosition']){
                    // echo $rec['firstName'] . ' => ' . $index . '<br>';
                    $db->update('UPDATE recipients SET routePosition = ? WHERE id = ?',[$index, $rec['id']]);
                }

                $index++;
            }
            $db->update('UPDATE routeChanges SET ran = ? WHERE id = ?',[1,$change['id']]);
        }
    }
    
}
