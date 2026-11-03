<?php

echo '<h4 class="subTitle">'.$recip['firstName']. ' ' . $recip['lastName'].'</h4>';
echo '<div class="reportDate">'.date('m/d/Y - g:i a').'</div>';
if(isset($_GET['activeStatus'])){
    $status = $db->select('SELECT * FROM clientStatus WHERE id = ?',[$recip['clientStatus_id']])[0];
    $statusLog = $db->select('SELECT * FROM clientStatusLog WHERE recipient_id = ? ORDER BY id DESC',[$recip['id']])[0];
    echo '<div class="reportSection">';
    echo '<h4>Active Status</h4>';
    echo '<p class="sectionContent">'.$statusLog['currentStatus'].' - '.date('m/d/y',strtotime($statusLog['logDate'])).'</p>';
    echo '</div>';
}
if(isset($_GET['address'])){
    echo '<div class="reportSection">';
    echo '<h4>Address</h4>';
    echo '<p class="sectionContent">'.$recip['addressLine1'] . ' ' . $recip['addressLine2']  . ' ' . $recip['city'] . ', ' . $recip['state'] .' '. $recip['zip'] .'</p>';
    echo '</div>';
}
if(isset($_GET['contactInfo'])){
    echo '<div class="reportSection">';
    echo '<h4>Contact Info</h4>';
    echo '<p class="sectionContent"><b>Home:</b> '.((empty($recip['homePhone'])) ? 'NONE' : formatPhone($recip['homePhone'])).' <b>Cell:</b> '.((empty($recip['cellPhone'])) ? 'NONE' : formatPhone($recip['cellPhone'])).' <b>Email:</b> '.$recip['email'].'</p>';
    echo '</div>';
}
if(isset($_GET['extra'])){
    echo '<div class="reportSection">';
    echo '<h4>Misc. Info</h4>';
    $gender = $db->select('SELECT * FROM genders WHERE id = ?',[$recip['gender_id']])[0];
    $genderDisplay = ($gender == false) ? 'Unknown' : $gender['name'];
    echo '<div class="reportSubSection"><b>Gender</b><br>'.$genderDisplay.'</div>';
    echo '<div class="reportSubSection"><b>DOB</b><br>'.date('m/d/Y',strtotime($recip['dob'])).'</div>';
    $marital = $db->select('SELECT * FROM maritalStatus WHERE id = ?',[$recip['maritalStatus_id']])[0];
    $martialStatus = ($marital == false) ? 'Unknown' : $marital['name'];
    echo '<div class="reportSubSection"><b>Marital Status</b><br>'.$martialStatus.'</div>';
    $ethnic = $db->select('SELECT * FROM ethnicGroups WHERE id = ?',[$recip['ethnicGroup_id']])[0];
    $enthnicity = ($ethnic == false) ? 'Unknown' : $ethnic['name'];
    echo '<div class="reportSubSection"><b>Ethnic Group</b><br>'.$enthnicity.'</div>';
    echo '</div>';
}
if(isset($_GET['contacts'])){
    echo '<div class="reportSection">';
    echo '<h4>Contacts</h4>';
    $contactLinks = $db->select('SELECT * FROM contact_link WHERE recipient_id = ?',[$recip['id']]);
    if($contactLinks == false){
        echo 'No Contacts';
    } else {
        foreach($contactLinks as $link){
            $contact = $db->select('SELECT * FROM contacts WHERE id = ?',[$link['contact_id']])[0];
            echo '<p class="sectionContent">'.$contact['firstName']. ' ' . $contact['lastName'] .  ' | h: '. formatPhone($contact['homePhone']) .' | c: '.formatPhone($contact['cellPHone']).' | Email:'.$contact['email'].' | Emergency: '.(($link['emergencyContact'] == 1) ? 'Yes' : 'No').' | POA: '.(($link['poa'] == 1) ? 'Yes' : 'No').' | Financial: '.(($link['financial'] == 1) ? 'Yes' : 'No').'</p>';
        }
    }
    echo '</div>';
}

if(isset($_GET['quickNotes'])){
    echo '<div class="reportSection">';
    echo '<h4>Quick Notes</h4>';
    echo '<p class="sectionContent">'.$recip['notes'].'</p>';
    echo '</div>';
}

if(isset($_GET['diets'])){
    echo '<div class="reportSection" id="dietarySection">';
    echo '<h4>Dietary</h4>';

    echo '<div class="reportSubSection">';
    echo '<h5>Diets</h5>';
    $recip_diets = explode(',',$recip['diets']);
    $diets = $db->select('SELECT * FROM diets ORDER BY name ASC');
    foreach($diets as $diet){
        $hasDiet = '';
        if(in_array($diet['id'],$recip_diets)){
            $hasDiet = ' has';
        }
        echo '<div class="dietaryRow'.$hasDiet.'">'.$diet['name'].'</div>';
    }
    echo '</div>';

    echo '<div class="reportSubSection" style="left:200px">';
    echo '<h5>Allergies</h5>';
    $recip_allergies = explode(',',$recip['allergies']);
    $allergies = $db->select('SELECT * FROM allergies ORDER BY name ASC');
    foreach($allergies as $allergy){
        $hasDiet = '';
        if(in_array($allergy['id'],$recip_allergies)){
            $hasDiet = ' has';
        }
        echo '<div class="dietaryRow'.$hasDiet.'">'.$allergy['name'].'</div>';
    }
    echo '</div>';



    echo '</div>';
}

if(isset($_GET['billing'])){
    echo '<div class="reportSection">';
    echo '<h4>Billing Info</h4>';
    $billing = $db->select('SELECT * FROM billings WHERE recipient_id = ? ORDER BY id DESC',[$recip['id']])[0];
    $billingType = $db->select('SELECT * FROM billingRecipientTypes WHERE id = ?',[$billing['billType_id']])[0];
    echo '<p class="sectionContent">Current Billing Method:<br><b>'.$billingType['name'].'</b></p>';
    if($billingType['id'] == 2){
        echo '<p>Sub ID: '.$billing['subscriber_id'].' | Auth #: '.$billing['authorizationNumber'].' | '.date('m/d/y',strtotime($billing['startDate'])) . ' - '.date('m/d/y',strtotime($billing['endDate'])).' | Issued: '.date('m/d/y',strtotime($billing['issueDate'])).' | Units: '.$billing['unitedApproved'].'-'.$billing['frequency'].'</p>';
    } else if ($billingType['id'] == 3){
        echo '<p>Finacial Contacts:</p><br>';
        $billContacts = $db->select('SELECT contacts.* FROM contact_link JOIN contacts ON contacts.id = contact_link.contact_id WHERE contact_link.recipient_id = ? AND contact_link.financial = ?',[$recip['id'],1]);
        foreach($billContacts as $c){
            echo $c['firstName'] . ' ' . $c['lastName'];
            if(!empty($c['homePhone'])){
                echo ' c:'.formatPhone($c['homePhone']);
            }
            if(!empty($c['cellPhone'])){
                echo ' h:'.formatPhone($c['cellPhone']);
            }
            if(!empty($c['workPhone'])){
                echo ' w:'.formatPhone($c['workPhone']);
            }
            if(!empty($c['addressLine1'])){
                echo ' - '. $c['addressLine1'];
            }
            if(!empty($c['addressLine2'])){
                echo ' '. $c['addressLine'];
            }
            if(!empty($c['city'])){
                echo ' '. $c['city'];
            }
            if(!empty($c['state'])){
                echo ', '. $c['state'];
            }
            if(!empty($c['zip'])){
                echo ' '. $c['zip'];
            }
            echo '<br><br>';
        }
    } 
    echo '</div>';
}