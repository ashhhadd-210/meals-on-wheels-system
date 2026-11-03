<?php
$links = $db->select('SELECT * FROM contact_link WHERE recipient_id = ? AND financial = ?',[$billing['recipient_id'],1]);
if($links != false){
    echo '<small>Financial Contact(s):</small>';
    foreach($links as $link){
        $contact = $db->select('SELECT * FROM contacts WHERE id = ?',[$link['contact_id']])[0];
        echo '<div class="contentRow">'.$contact['firstName'] . ' ' . $contact['lastName'].'</div>';
    }
} else {
    echo '<div class="contentRow">No finacial contacts for '.$recip['firstName'] . ' ' . $recip['lastName'].'</div>';
    echo '<script>sendForm = false</script>';
}
?>
<div class="formRow">
    <div class="inputRow">
        <label for="">Start Date</label>
        <input type="text" value="<?php echo date('m/d/Y',strtotime($billing['startDate'])) ?>" readonly>
    </div>
</div>