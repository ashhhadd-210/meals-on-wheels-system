<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$route = $db->select('SELECT * FROM routes WHERE id = ?', [$_POST['route_id']])[0];
if ($route == false) {
    exit('Could not find this route.');
}
$date = new DateTime($_POST['date']);

$query = $db->select('SELECT * FROM systemMeals WHERE dateStamp = ? AND route_id = ?',[$date->format('Y-m-d'),$route['id']])[0];
if($query == false){
    $insert = $db->insert('INSERT INTO systemMeals (dateStamp, route_id, hot, frozen) VALUES (?,?,?,?)',[$date->format('Y-m-d'),$route['id'],$_POST['hot'],$_POST['fro']]);
    if($insert == false){
        exit($db->error);
    } else {
        exit('ok');
    }
} else {
    $db->error = '';
    $db->update('UPDATE systemMeals SET hot = ?, frozen = ? WHERE id = ?',[$_POST['hot'],$_POST['fro'],$query['id']]);
    if(empty($db->error)){
        exit('ok');
    } else {
        exit($db->error);
    }
}
exit('End of script');