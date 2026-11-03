<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    if ($_POST['contentType'] == 'recipient') {
        $id = $_SESSION['editing_recip_id'];
        $recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$id])[0];
        $firstName = $security->sanitizeInput($_POST['firstName']);
        $middleName = $security->sanitizeInput($_POST['middleName']);
        $lastName = $security->sanitizeInput($_POST['lastName']);
        $suffix = $security->sanitizeInput($_POST['suffix']);
        $nickName = $security->sanitizeInput($_POST['nickName']);
        $gender_id = (empty($_POST['gender'])) ? NULL : $security->sanitizeInput($_POST['gender']);
        $dob = (empty($_POST['dob'])) ? NULL : date('Y-m-d', strtotime($_POST['dob']));
        $maritalStatus_id = (empty($_POST['maritalStatus'])) ? NULL : $security->sanitizeInput($_POST['maritalStatus']);
        $homePhone = cleanNumbers($_POST['homePhone']);
        $cellPhone = cleanNumbers($_POST['cellPhone']);
        $email = $security->sanitizeInput($_POST['email']);
        $addressLine1 = $security->sanitizeInput($_POST['addressLine1']);
        $addressLine2 = $security->sanitizeInput($_POST['addressLine2']);
        $city = $security->sanitizeInput($_POST['city']);
        $state = $security->sanitizeInput($_POST['state']);
        $zip = $security->sanitizeInput($_POST['zip']);
        $ethnicGroup_id = (empty($_POST['ethnicGroup'])) ? NULL : $security->sanitizeInput($_POST['ethnicGroup']);
        $clientStatus_id = (empty($_POST['clientStatus'])) ? NULL : $security->sanitizeInput($_POST['clientStatus']);
        $physician_id = $security->sanitizeInput($_POST['physician_id']);
        $clinic_id = $security->sanitizeInput($_POST['clinic_id']);
        $certStartDate = (empty($_POST['certStartDate'])) ? NULL : date('Y-m-d', strtotime($_POST['certStartDate']));
        $certEndDate = (empty($_POST['certEndDate'])) ? NULL : date('Y-m-d', strtotime($_POST['certEndDate']));
        $notes = $_POST['notes'];

        $recipient = $db->select('SELECT * FROM recipients WHERE id = ?',[$id])[0];


        $diets = '';
        foreach ($_POST['recip_diets'] as $row) {
            $delimit = ($diets == '') ? '' : ',';
            $diets .= $delimit . $row;
        }

        $recip_hot_milk_id = (int) $_POST['recip_hot_milk_id'];
        $recip_frozen_milk_id = (int) $_POST['recip_frozen_milk_id'];


        $allergies = '';
        foreach ($_POST['recip_allergies'] as $row) {
            $delimit = ($allergies == '') ? '' : ',';
            $allergies .= $delimit . $row;
        }

        $accommodations = '';
        foreach ($_POST['recip_accommodations'] as $row) {
            $delimit = ($accommodations == '') ? '' : ',';
            $accommodations .= $delimit . $row;
        }


        // $referredBy = $security->sanitizeInput($_POST['referredBy']);
        $referralReason = $security->sanitizeInput($_POST['referralReason']);
        $referralDate = (empty($_POST['referralDate'])) ? NULL : date('Y-m-d', strtotime($_POST['referralDate']));
        $route_id = (empty($_POST['route'])) ? NULL : (int)$security->sanitizeInput($_POST['route']);

        // if($route_id != null){
        //     if($recipient['route_id'] != $route_id){
        //         $db->insert('INSERT INTO routeChanges ');
        //     }
        // }

        // $routePosition = $security->sanitizeInput($_POST['routePosition']);
        $routeNote = $_POST['routeNote'];
        $relationship_id = (empty($_POST['relationship'])) ? NULL : $security->sanitizeInput($_POST['relationship']);

        if ($recip['clientStatus_id'] != $clientStatus_id) {
            $status = $db->select('SELECT * FROM clientStatus WHERE id = ?', [$clientStatus_id])[0];
            $db->insert('INSERT INTO clientStatusLog (logDate,currentStatus,recipient_id) VALUES (?,?,?)', [date('Y-m-d'), $status['name'], $id]);
        }

        $db->update('UPDATE recipients SET firstName = ?, middleName = ?, lastName = ?, suffix = ?, nickName = ?, notes = ?, gender_id = ?, dob = ?, maritalStatus_id = ?, homePhone = ?, cellPhone = ?, 
        email = ?, addressLine1 = ?, addressLine2 = ?, city = ?, state = ?, zip = ?, ethnicGroup_id = ?, clientStatus_id = ?, physician_id = ?, clinic_id = ?, referralReason = ?, route_id = ?, routeNote = ?, 
        link = ?, diets = ?, hotMilk_id = ?, frozenMilk_id = ?, allergies = ?, accommodations = ? WHERE id = ?', [
            $firstName,
            $middleName,
            $lastName,
            $suffix,
            $nickName,
            $notes,
            $gender_id,
            $dob,
            $maritalStatus_id,
            $homePhone,
            $cellPhone,
            $email,
            $addressLine1,
            $addressLine2,
            $city,
            $state,
            $zip,
            $ethnicGroup_id,
            $clientStatus_id,
            $physician_id,
            $clinic_id,
            $referralReason,
            $route_id,
            $routeNote,
            $relationship_id,
            $diets,
            $recip_hot_milk_id,
            $recip_frozen_milk_id,
            $allergies,
            $accommodations,
            $id
        ]);
        if ($relationship_id == NULL) {
            $db->update('UPDATE recipients SET link = ? WHERE id = ?', [NULL, $relationship_id]);
        } else {
            $db->update('UPDATE recipients SET link = ? WHERE id = ?', [$id, $relationship_id]);
        }
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'contact') {
        $id = $_SESSION['editing_contact_id'];
        $firstName = $security->sanitizeInput($_POST['firstName']);
        $lastName = $security->sanitizeInput($_POST['lastName']);
        $homePhone = cleanNumbers($_POST['homePhone']);
        $cellPhone = cleanNumbers($_POST['cellPhone']);
        $workPhone = cleanNumbers($_POST['workPhone']);
        $email = $security->sanitizeInput($_POST['email']);
        $addressLine1 = $security->sanitizeInput($_POST['addressLine1']);
        $addressLine2 = $security->sanitizeInput($_POST['addressLine2']);
        $city = $security->sanitizeInput($_POST['city']);
        $state = $security->sanitizeInput($_POST['state']);
        $zip = $security->sanitizeInput($_POST['zip']);
        $recips = explode('|', $_POST['inputArray']);

        $db->update('UPDATE contacts SET firstName = ?, lastName = ?, homePhone = ?, cellPhone = ?, workPhone = ?, email = ?, addressLine1 = ?, addressLine2 = ?, city = ?, state = ?, zip = ? WHERE id = ?', [
            $firstName,
            $lastName,
            $homePhone,
            $cellPhone,
            $workPhone,
            $email,
            $addressLine1,
            $addressLine2,
            $city,
            $state,
            $zip,
            $id
        ]);
        foreach ($recips as $recip) {
            $data = explode(',', $recip);
            if ($data[0] == 0) {
                $db->insert('INSERT INTO contact_link (relationship_id, contact_id, recipient_id, emergencyContact, poa, financial) VALUES (?,?,?,?,?,?)', [
                    $data[2],
                    $id,
                    $data[1],
                    $data[3],
                    $data[4],
                    $data[5]
                ]);
            } else {
                $db->update('UPDATE contact_link SET relationship_id = ?, contact_id = ?, recipient_id = ?, emergencyContact = ?, poa = ?, financial = ? WHERE id = ?', [
                    $data[2],
                    $id,
                    $data[1],
                    $data[3],
                    $data[4],
                    $data[5],
                    $data[0]
                ]);
            }
        }
        echo 'ok',
        exit();
    } else if ($_POST['contentType'] == 'relationship') {
        $db->update('UPDATE relationships SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'gender') {
        $db->update('UPDATE genders SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'ethnicGroup') {
        $db->update('UPDATE ethnicGroups SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'maritalStatus') {
        $db->update('UPDATE maritalStatus SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'clientStatus') {
        $db->update('UPDATE clientStatus SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'physician') {
        $db->update('UPDATE physicians SET firstName = ?, lastName = ? WHERE id = ?', [$_POST['firstName'], $_POST['lastName'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'clinic') {
        $db->update('UPDATE clinics SET name = ? WHERE id = ?', [$_POST['editname'], $_POST['editid']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'noteLog') {
        if (isset($_POST['startDelete']) && isset($_POST['confirmDelete'])) {
            $db->delete('DELETE FROM noteLogContacts WHERE log_id = ?', [$_SESSION['noteLogEdit_id']]);
            $db->delete('DELETE FROM noteLogs WHERE id = ?', [$_SESSION['noteLogEdit_id']]);
            echo 'ok';
            exit();
        }
        $noteDec = $security->sanitizeInput($_POST['noteDec']);
        $followUp = (isset($_POST['followUp'])) ? 1 : 0;
        $complete = (isset($_POST['complete'])) ? 1 : 0;
        $emergContactCalled = (isset($_POST['emergContactCalled'])) ? 1 : 0;
        $emsCalled = (isset($_POST['emsCalled'])) ? 1 : 0;
        $referralMade = (isset($_POST['referralMade'])) ? 1 : 0;
        $followUpDate = (empty($_POST['followUpDate'])) ? NULL : date('Y-m-d', strtotime($_POST['followUpDate']));

        if ($followUp != 1) {
            $followUpDate = NULL;
        }

        $db->update(
            'UPDATE noteLogs SET note = ?, followUp = ?, followUpDate = ?, complete = ?, emergContactCalled = ?, emsCalled = ?, referralMade = ? WHERE id = ?',
            [$noteDec, $followUp, $followUpDate, $complete, $emergContactCalled, $emsCalled, $referralMade, $_SESSION['noteLogEdit_id']]
        );
        if (!empty($_POST['newContactDesc']) || !empty($_POST['extName'])) {
            if (!empty($_POST['extName'])) {
                $contactPerson = NULL;
            } else {
                $contactPerson = $_POST['contactPerson'];
            }
            $newContactDateStamp = (empty($_POST['newContactDateStamp'])) ? date('Y-m-d') : date('Y-m-d', strtotime($_POST['newContactDateStamp']));
            $newContactTimeStamp = (empty($_POST['newContactTimeStamp'])) ? date('H:i:s') : date('H:i:s', strtotime($_POST['newContactTimeStamp']));
            $db->insert(
                'INSERT INTO noteLogContacts (log_id,contact_id,contactDesc,dateStamp,timeStamp,author_id,makeStamp,extName) VALUES (?,?,?,?,?,?,?,?)',
                [$_SESSION['noteLogEdit_id'], $contactPerson, $_POST['newContactDesc'], $newContactDateStamp, $newContactTimeStamp, $user['id'], date('Y-m-d H:i:s'), $_POST['extName']]
            );
        }
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'route') {
        if ($user['permissionLevel'] >= 3) {
            $db->update('UPDATE routes SET name = ?, color = ?, vendor = ? WHERE id = ?', [$_POST['routeName'], $_POST['color'], $_POST['vendor'], $_SESSION['editing_route_id']]);
        }
        if (!empty($_POST['positions'])) {
            $rows = explode(',', $_POST['positions']);

            foreach ($rows as $row) {
                $data = explode('-', $row);
                $db->update('UPDATE recipients SET routePosition = ? WHERE id = ?', [$data[1], $data[0]]);
            }
        }
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'diet') {
        if ($user['permissionLevel'] >= 3) {
            $db->update('UPDATE diets SET name = ?, abbre = ?, description = ? WHERE id = ?', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr'], $_SESSION['editing_diet_id']]);
        }
        exit('ok');
    } else if ($_POST['contentType'] == 'accommodations') {
        if ($user['permissionLevel'] >= 3) {
            $db->update('UPDATE accommodations SET name = ?, abbr = ?, description = ? WHERE id = ?', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr'], $_SESSION['editing_accommodation_id']]);
        }
        exit('ok');
    } else if ($_POST['contentType'] == 'cert') {
        if ($user['permissionLevel'] >= 3) {
            // $referralDate = empty($_POST['referralDate']) ? NULL : date('Y-m-d', strtotime($_POST['referralDate']));
            $db->update('UPDATE certificateLogs SET startDate = ?, endDate = ?, physician = ?, clinic = ? WHERE id = ?', [
                date('Y-m-d', strtotime($_POST['startDate'])),
                date('Y-m-d', strtotime($_POST['endDate'])),
                $_POST['cert_physician_id'],
                $_POST['cert_clinic_id'],
                $_POST['id']
            ]);
        }
        exit('ok');
    } else if ($_POST['contentType'] == 'schedule') {
        $days = explode(',', $_POST['dayMatrix']);
        $daySelects = array();
        foreach ($days as $day) {
            $breakDown = explode('-', $day);
            $daySelects[] = $breakDown[0];
        }
        $inf = (isset($_POST['indefinite'])) ? 1 : 0;
        $endDate = ($inf == 1) ? NULL : date('Y-m-d', strtotime($_POST['scheduleEndDate']));

        $db->update('UPDATE meals SET monday = ?, tuesday = ?, wednesday = ?, thursday = ?, friday = ?, saturday = ?, sunday = ?, startDate = ?, endDate = ?, mealCounts = ?, inf = ? WHERE id = ?', [
            $daySelects[0],
            $daySelects[1],
            $daySelects[2],
            $daySelects[3],
            $daySelects[4],
            $daySelects[5],
            $daySelects[6],
            date('Y-m-d', strtotime($_POST['scheduleStartDate'])),
            $endDate,
            $_POST['dayMatrix'],
            $inf,
            $_POST['schedule_id']
        ]);
        exit('ok');
    } else if ($_POST['contentType'] == 'offDay') {
        $offTemp = (isset($_POST['offTemp'])) ? 1 : 0;
        $deactivate = (isset($_POST['deactivate'])) ? 1 : 0;
        $endDate = (isset($_POST['offTemp'])) ? NULL : date('Y-m-d', strtotime($_POST['offDateEndDate']));
        $db->update('UPDATE daysOff SET indefinate = ?, startDate = ?, endDate = ?, deactivate = ?, reason = ? WHERE id = ?', [
            $offTemp,
            date('Y-m-d', strtotime($_POST['offDayStartDate'])),
            $endDate,
            $deactivate,
            $_POST['reason'],
            $_POST['id']
        ]);
        exit('ok');
    } else if ($_POST['contentType'] == 'assessment-question') {
        $assess_id = (int) $_POST['assess_id'];
        $question_id = (int) $_POST['question_id'];
        $hidden = (isset($_POST['deprecated'])) ? 1 : 0;
        $index = 1;
        $db->update('UPDATE assessmentQuestions SET prompt = ?, hidden = ? WHERE id = ?', [$_POST['prompt'], $hidden, $question_id]);
        foreach ($_POST['answerOption'] as $option) {
            $data = explode('||', $option);
            if ($option[0] == 0) {
                $db->insert('INSERT INTO assessmentQuestionsOptions (assessment_id, question_id, textContent, displayOrder) VALUES (?,?,?,?)', [$assess_id, $question_id, $data[1], $index]);
            } else {
                $db->update('UPDATE assessmentQuestionsOptions SET textContent = ?, displayOrder = ? WHERE id = ?', [
                    $data[1],
                    $index,
                    $data[0]
                ]);
            }
            $index++;
        }
        exit('ok');
    } else if ($_POST['contentType'] == 'assessment') {
        if (!empty($_POST['quest_matrix'])) {
            $data = explode(',', $_POST['quest_matrix']);
            foreach ($data as $row) {
                $rowData = explode('-', $row);
                $db->update('UPDATE assessmentQuestions SET displayOrder = ? WHERE id = ?', [$rowData[1], $rowData[0]]);
            }
        }
        $hidden = (isset($_POST['hidden'])) ? 1 : 0;
        $db->update('UPDATE assessments SET name = ?, hide = ? WHERE id = ?', [$_POST['name'], $hidden, $_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'recip-assessment') {
        $archived = (isset($_POST['archived'])) ? 1 : 0;
        $db->delete('DELETE FROM assessmentScores WHERE link_id = ?',[$_POST['link_id']]);
        $db->update('UPDATE assessmentscoreLinks SET lastUpdated = ?, archived = ? WHERE id = ?',[date('Y-m-d H:i:s'), $archived, $_POST['link_id']]);
        $checkboxes = [];
        $otherInputs = [];
        $allInputs = [];
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $checkboxes[] = $key;
            } else {
                if ($key != 'assess_id' && $key != 'recip_id' && $key != 'contentType' && $key != 'link_id') {
                    $otherInputs[] = $key;
                }
            }
        }
        foreach ($checkboxes as $checkboxInput) {
            foreach ($_POST[$checkboxInput] as $input) {
                $allInputs[] = [$checkboxInput, $input];
            }
        }
        foreach ($otherInputs as $input) {
            $allInputs[] = [$input, $_POST[$input]];
        }
        $assessment = $db->select('SELECT * FROM assessments WHERE id = ?', [$_POST['assess_id']])[0];
        $scoreInsert = (int) $_POST['link_id'];
        if ($scoreInsert == false) {
            exit('There was an issue saving');
        } else {
            foreach ($allInputs as $row) {
                $value = (ctype_digit($row[1])) ? $row[1] : NULL;
                $textValue = (ctype_digit($row[1])) ? NULL : $row[1];
                $question = $db->select('SELECT * FROM assessmentQuestions WHERE id = ?',[$row[0]])[0];
                $type = (int) $question['type_id'];
                $db->insert('INSERT INTO assessmentScores (assessment_id, scoreType_id, question_id, value, textValue, link_id) VALUES (?,?,?,?,?,?)', [
                    $_POST['assess_id'],
                    $type,
                    $row[0],
                    $value,
                    $textValue,
                    $scoreInsert
                ]);
            }
        }
        exit('ok');
    }
}
exit();
