<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1){
    if($_POST['type'] == 'relationship'){
        $query = $db->select('SELECT * FROM relationships WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'gender') {
        $query = $db->select('SELECT * FROM genders WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'ethnicGroup') {
        $query = $db->select('SELECT * FROM ethnicGroups WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'maritalStatus') {
        $query = $db->select('SELECT * FROM maritalStatus WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'clientStatus') {
        $query = $db->select('SELECT * FROM clientStatus WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'physician') {
        $query = $db->select('SELECT * FROM physicians WHERE id = ?',[$_POST['id']])[0];
    } else if ($_POST['type'] == 'clinic') {
        $query = $db->select('SELECT * FROM clinics WHERE id = ?',[$_POST['id']])[0];
    } 
    echo '<form id="optionsEditForm" class="optionsEditForm" action="" method="post" onsubmit="return sendEditContent(this)">';
        echo '<h3>Editing: '.ucfirst(preg_replace('/(?<!^)([A-Z])/',' $1',$_POST['type'])).'</h3>';
        if($_POST['type'] == 'physician'){
            echo '<input name="editid" type="text" value="'.$query['id'].'" readonly required style="display:none">';
            echo '<input name="contentType" type="text" value="'.$_POST['type'].'" readonly required style="display:none">';
            echo '<input name="editname" value="'.$query['firstName'].'" type="text" required autocomplete="off">';
            echo '<input name="editname" value="'.$query['lastName'].'" type="text" required autocomplete="off">';
        } else {
            echo '<input name="editid" type="text" value="'.$query['id'].'" readonly required style="display:none">';
            echo '<input name="contentType" type="text" value="'.$_POST['type'].'" readonly required style="display:none">';
            echo '<input name="editname" value="'.$query['name'].'" type="text" required autocomplete="off">';
        }
        echo '<button type="submit">Update</button>';
        echo '<button type="button" onclick="cancelSelection(this)">Cancel</button>';
    echo '</form>';
}