<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    $note_id = $_SESSION['noteLogEdit_id'];
    $note = $db->select('SELECT * FROM noteLogs WHERE id = ?',[$note_id])[0];
    $noteContacts = $db->select('SELECT * FROM noteLogContacts WHERE log_id = ?', [$note_id]);
    foreach ($noteContacts as $row) {
        echo '<div style="width:100%">';
        echo '<select name="contactPerson" id="contactPerson">';
        echo '<option value="">Select</option>';

        $persons = $db->select('SELECT c.id AS contactID, c.firstName, c.lastName, r.id, r.name AS reName 
                FROM contact_link cl INNER JOIN contacts c ON cl.contact_id = c.id 
                INNER JOIN relationships r ON cl.relationship_id = r.id 
                WHERE cl.recipient_id = ?', [$note['recipient_id']]);

        foreach ($persons as $person) {
            echo '<option value="' . $person['contactID'] . '"'.(($person['contactID'] == $row['contact_id']) ? ' selected' : '').'>' . $person['firstName'] . ' ' . $person['lastName'] . ' - ' . $person['reName'] . '</option>';
        }

        echo '</select>';
        echo '</div>';
    }
}
exit();
