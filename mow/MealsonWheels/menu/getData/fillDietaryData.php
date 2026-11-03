<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    
    echo '<div class="inputRow"><button type="button" class="addConentBtn" onclick="addContent(\''.$_POST['type'].'\')">New</button></div>';
    if($_POST['type'] == 'diets'){
        $diets = $db->select('SELECT * FROM diets ORDER BY name ASC');
        if($diets != false){
            foreach($diets as $row){
                echo '<div class="contentRow" onclick="editContent(this,\''.$_POST['type'].'\','.$row['id'].')">'.$row['name'].'</div>';
            }
        } else {
            echo '<span>No Data</span>';
        }
    } else if($_POST['type'] == 'allergies'){
        $allergies = $db->select('SELECT * FROM allergies ORDER BY name ASC');
        if($allergies != false){
            foreach($allergies as $row){
                echo '<div class="contentRow" onclick="editContent(this,\''.$_POST['type'].'\','.$row['id'].')">'.$row['name'].'</div>';
            }
        } else {
            echo '<span>No Data</span>';
        }
    } else if($_POST['type'] == 'accommodations') {
        $accommodations = $db->select('SELECT * FROM accommodations ORDER BY name ASC');
        if($accommodations != false){
            foreach($accommodations as $row){
                echo '<div class="contentRow" onclick="editContent(this,\''.$_POST['type'].'\','.$row['id'].')">'.$row['name'].'</div>';
            }
        } else {
            echo '<span>No Data</span>';
        }
    } else if($_POST['type'] == 'milks') {
        $milks = $db->select('SELECT * FROM milkChoices ORDER BY name ASC');
        if($milks != false){
            foreach($milks as $row){
                echo '<div class="contentRow" onclick="editContent(this,\''.$_POST['type'].'\','.$row['id'].')">'.$row['name'].'</div>';
            }
        } else {
            echo '<span>No Data</span>';
        }
    }

}
exit();
