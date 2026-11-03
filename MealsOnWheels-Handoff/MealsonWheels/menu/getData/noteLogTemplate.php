<form action="index.php" method="post" id="editContactLogForm" style="padding:20px;display:flex;flex-direction:column;gap:30px">
    <?php
    $author = $db->select('SELECT id,firstName,lastName FROM users WHERE id = ?', [$note['author_id']])[0];
    $_SESSION['noteLogEdit_id'] = $note['id'];
    ?>
    <h3><?php echo date('m/d/y \a\t h:i A', strtotime($note['dateStamp'])) . ' by ' . $author['firstName'] . ' ' . $author['lastName'] ?></h3>
    <div class="formRow">
        <div class="inputRow">
            <label for="noteDesc">Note Description</label>
            <input value="<?php echo $note['note'] ?>" type="text" name="noteDec" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow" style="align-items: first baseline;">
            <label for="followUp">Follow Up?</label>
            <input type="checkbox" name="followUp" id="followUp" <?php echo (($note['followUp'] == 1) ? ' checked' : '') ?>>
        </div>
        <div class="inputRow" style="align-items: first baseline;">
            <label for="emergContactCalled">Emergency Contacts called</label>
            <input type="checkbox" name="emergContactCalled" id="emergContactCalled" <?php echo (($note['emergContactCalled'] == 1) ? ' checked' : '') ?>>
        </div>
        <div class="inputRow" style="align-items: first baseline;">
            <label for="emsCalled">EMS called</label>
            <input type="checkbox" name="emsCalled" id="emsCalled" <?php echo (($note['emsCalled'] == 1) ? ' checked' : '') ?>>
        </div>
        <div class="inputRow" style="align-items: first baseline;">
            <label for="referralMade">Referral Made</label>
            <input type="checkbox" name="referralMade" id="referralMade" <?php echo (($note['referralMade'] == 1) ? ' checked' : '') ?>>
        </div>
        <div class="inputRow" style="align-items: first baseline;">
            <label for="complete">Complete</label>
            <input type="checkbox" name="complete" id="complete" <?php echo (($note['complete'] == 1) ? ' checked' : '') ?>>
        </div>
        <div class="inputRow">
            <label for="followUpDate">Follow Up Date</label>
            <input type="date" name="followUpDate" id="followUpDate" value="<?php echo ((empty($note['followUpDate'])) ? '' : date('Y-m-d', strtotime($note['followUpDate']))) ?>">
        </div>
    </div>
    <h4 style="margin:0px">Contact Trace <!--<span style="cursor:pointer;font-weight:100;font-size:13px" onclick="editNoteLogContacts(this)">Edit</span>--></h4>
    <div class="formRow" id="noteLogBin">
        <?php
        $contactsMade = $db->select('SELECT * FROM noteLogContacts WHERE log_id = ? ORDER BY dateStamp ASC,timeStamp ASC', [$note['id']]);
        if ($contactsMade != false) {
            foreach ($contactsMade as $trace) {
                if(!empty($trace['contact_id'])){
                    $curCon = $db->select('SELECT id,firstName,lastName FROM contacts WHERE id = ?', [$trace['contact_id']])[0];
                    $displayName = '<span onclick="viewContact(' . $curCon['id'] . ')" class="contactTraceName">' . $curCon['firstName'] . ' ' . $curCon['lastName'] . '</span>';
                } else {
                    $displayName = '<span style="color: #5b5be1;" class="contactTraceName">'.$trace['extName'].'</span>';
                }
                $author = $db->select('SELECT id,firstName.lastName FROM users WHERE id = ?',[$trace['author_id']])[0];
                echo '<div style="width:100%" class="contactTraceItem">'.$displayName.'<span>' . date('m/d/y', strtotime($trace['dateStamp'])) . ' - ' . date('h:i A', strtotime($trace['timeStamp'])) . '</span><span class="contactDesc">' . $trace['contactDesc'] . '</span>
                <span style="margin-left:auto;font-size:14px">'.$author['firstName'] . ' '. substr($author['lastName'],0,1) . ' (' . ((empty($trace['makeStamp'])) ? '' : date('m/d/y H:i',strtotime($trace['makeStamp']))) . ')</span><span style="cursor:pointer;color:red" onclick="deleteNoteLogContact(this,'.$trace['id'].')">Delete</span></div>';
            }
        } else {
            echo '<span>No contacts made</span>';
        }
        ?>
    </div>
    <h3>New Contact Made</h3>
    <div style="display:flex;flex-direction:column;gap:20px;border:1px solid #eee;padding:20px">
        <div class="formRow">
            <div class="inputRow">
                <label for="contactPerson">Contact Person</label>
                <select name="contactPerson" id="contactPerson">
                    <option value="">Select</option>
                    <?php
                    $persons = $db->select('SELECT c.id AS contactID, c.firstName, c.lastName, r.id, r.name AS reName 
                FROM contact_link cl INNER JOIN contacts c ON cl.contact_id = c.id 
                INNER JOIN relationships r ON cl.relationship_id = r.id 
                WHERE cl.recipient_id = ?', [$note['recipient_id']]);

                    foreach ($persons as $person) {
                        echo '<option value="' . $person['contactID'] . '">' . $person['firstName'] . ' ' . $person['lastName'] . ' - ' . $person['reName'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="display: flex; align-items:center">or</div>
            <div class="inputRow">
                <label for="extName">External Name</label>
                <input type="text" name="extName" id="extName" autocomplete="off">
            </div>
        </div>
        <div class="formRow">
            <div class="inputRow">
                <label for="newContactDesc">Contact Description</label>
                <input type="text" name="newContactDesc" id="newContactDesc" autocomplete="off">
            </div>
        </div>
        <small><i>Leave below blank if this is current</i></small>
        <div class="formRow">
            <div class="inputRow">
                <label for="newContactDateStamp">Date</label>
                <input type="date" name="newContactDateStamp" id="newContactDateStamp">
            </div>
            <div class="inputRow">
                <label for="newContactTimeStamp">Time</label>
                <input type="time" name="newContactTimeStamp" id="newContactTimeStamp">
            </div>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow" style="align-items: baseline;">
            <label for="startDelete">Delete This Note</label>
            <input type="checkbox" name="startDelete" id="startDelete">
        </div>
        <div class="inputRow" id="confirmDeleteWrap" style="align-items: baseline; display:none;background-color: #e6b4b4;padding: 10px;">
            <label for="confirmDelete">Confirm Delete?</label>
            <input type="checkbox" name="confirmDelete" id="confirmDelete">
        </div>
    </div>
    <button class="noteLogEditSubmitBtn" type="submit">Update</button>
    <input type="text" name="contentType" value="noteLog" readonly required style="display: none;">
</form>
<script>
    $(document).ready(function(){
        $('#startDelete').on('change',function(){
            if($('#startDelete').prop('checked')){
                $('#confirmDeleteWrap').show(200)
            } else {
                $('#confirmDeleteWrap').hide(200);
                $('#confirmDelete').prop('checked',false)
            }
        })
    })
    function deleteNoteLogContact(btn,id){
        if(confirm('Are you sure? This action is immediate.')){
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: 'noteLogContact',
                    id: id
                },
                success: function(response) {
                    if (response == 'ok') {
                        $(btn).parent().remove();
                    } else {
                        customMessage(response, 3, false, false);
                    }

                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.log(error)
                }
            });
        }
    }
    function editNoteLogContacts(btn) {
        
    }
    $(document).ready(function() {
        $('#editContactLogForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize()
            console.log(formData)
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        customModalClear();
                        fillnoteLog()
                    } else {
                        customMessage(response, 3, false, false);
                    }

                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.log(error)
                }
            });
        })
    })
</script>