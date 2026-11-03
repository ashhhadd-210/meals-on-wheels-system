<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
if($_POST['type'] == 3){
    $links = $db->select('SELECT * FROM contact_link WHERE recipient_id = ? AND financial = ?',[$recip['id'],1]);
    if($links != false){
        echo '<small>Contact(s):</small>';
        foreach($links as $link){
            $contact = $db->select('SELECT * FROM contacts WHERE id = ?',[$link['contact_id']])[0];
            echo '<div class="contentRow">'.$contact['firstName'] . ' ' . $contact['lastName'].'</div>';
        }
    } else {
        echo '<div class="contentRow">No finacial contacts for '.$recip['firstName'] . ' ' . $recip['lastName'].'</div>';
        echo '<script>sendForm = false</script>';
    }
}
?>

<div class="formRow">
    <div class="inputRow">
        <label for="startDate">Start Date *</label>
        <input type="date" name="startDate" id="startDate" required>
    </div>
</div>

<div class="formRow">
    <div class="inputRow">
        <button type="submit">Add</button>
    </div>
</div>