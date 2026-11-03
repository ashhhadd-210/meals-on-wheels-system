<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    if ($_POST['contentType'] == 'recipient') {
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
        $referredBy = $security->sanitizeInput($_POST['referredBy']);
        $referralReason = $security->sanitizeInput($_POST['referralReason']);
        $referralDate = (empty($_POST['referralDate'])) ? NULL : date('Y-m-d', strtotime($_POST['referralDate']));
        $route_id = (empty($_POST['route'])) ? NULL : $security->sanitizeInput($_POST['route']);
        $routePosition = $security->sanitizeInput($_POST['routePosition']);
        $routeNote = $_POST['routeNote'];
        $relationship_id = (empty($_POST['relationship'])) ? NULL : $security->sanitizeInput($_POST['relationship']);

        $check = $db->select('SELECT * FROM recipients WHERE firstName = ?', [$firstName])[0];
        if ($check != false) {
            if ($check['lastName'] == $lastName) {
                if (empty($dob)) {
                    echo 'Date of birth is required';
                    exit();
                } else {
                    if (date('Y-m-d', strtotime($check['dob'])) == $dob) {
                        echo 'This recipient already exists';
                        exit();
                    }
                }
            }
        }

        $insert = $db->insert('INSERT INTO recipients (firstName, middleName, lastName, suffix, nickName, gender_id, dob, maritalStatus_id, homePhone, cellPhone, 
        email, addressLine1, addressLine2, city, state, zip, ethnicGroup_id, clientStatus_id, physician_id, clinic_id, certStartDate, certEndDate, 
        notes, referredBy, referralReason, referralDate, route_id, routePosition, routeNote, link) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
            $firstName,
            $middleName,
            $lastName,
            $suffix,
            $nickName,
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
            $certStartDate,
            $certEndDate,
            $notes,
            $referredBy,
            $referralReason,
            $referralDate,
            $route_id,
            $routePosition,
            $routeNote,
            $relationship_id
        ]);
        if ($insert == false) {
            echo $db->error;
            exit();
        }
        if ($relationship_id != NULL) {
            $db->update('UPDATE recipients SET link = ? WHERE id = ?', [$insert, $relationship_id]);
        }
        if ($clientStatus_id != NULL) {
            $status = $db->select('SELECT * FROM clientStatus WHERE id = ?', [$clientStatus_id])[0];
            $db->insert('INSERT INTO clientStatusLog (logDate,currentStatus,recipient_id) VALUES (?,?,?)', [date('Y-m-d'), $status['name'], $insert]);
        }

        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'contact') {
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

        $check = $db->select('SELECT * FROM contacts WHERE firstName = ? AND lastName = ?',[trim($firstName),trim($lastName)])[0];
        if($check != false){
            exit('This contact already exists');
        }
        $insert = $db->insert('INSERT INTO contacts (firstName,lastName,homePhone,cellPhone,workPhone,email,addressLine1,addressLine2,city,state,zip) VALUES (?,?,?,?,?,?,?,?,?,?,?)', [
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
            $zip
        ]);
        if ($insert != false) {
            foreach ($recips as $recip) {
                $data = explode(',', $recip);
                $db->insert('INSERT INTO contact_link (relationship_id, contact_id, recipient_id, emergencyContact, poa, financial) VALUES (?,?,?,?,?,?)', [
                    $data[1],
                    $insert,
                    $data[0],
                    $data[2],
                    $data[3],
                    $data[4]
                ]);
            }
            echo 'ok',
            exit();
        } else {
            echo 'There was an issue saving.';
        }
    } else if ($_POST['contentType'] == 'relationship') {
        $db->insert('INSERT INTO relationships (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'gender') {
        $db->insert('INSERT INTO genders (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'ethnicGroup') {
        $db->insert('INSERT INTO ethnicGroups (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'maritalStatus') {
        $db->insert('INSERT INTO maritalStatus (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'clientStatus') {
        $db->insert('INSERT INTO clientStatus (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'physician') {
        $db->insert('INSERT INTO physicians (firstName,lastName) VALUES (?,?)', [$_POST['firstName'], $_POST['lastName']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'clinic') {
        $db->insert('INSERT INTO clinics (name) VALUES (?)', [$_POST['value']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'noteLog') {
        $followUpDate = (empty($_POST['followUpDate']) ? NULL : date('Y-m-d', strtotime($_POST['followUpDate'])));
        if ($_POST['followUp'] != 1) {
            $followUpDate = NULL;
        }
        $db->insert(
            'INSERT INTO noteLogs (note,dateStamp,author_id,followUp,recipient_id,followUpDate,complete,emergContactCalled,emsCalled,referralMade) VALUES (?,?,?,?,?,?,?,?,?,?)',
            [$_POST['note'], date('Y-m-d H:i:s'), $user['id'], $_POST['followUp'], $_SESSION['editing_recip_id'], $followUpDate, 0, $_POST['emergContactCalled'], $_POST['emsCalled'], $_POST['referralMade']]
        );
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'route') {
        $db->insert('INSERT INTO routes (name,color,vendor) VALUES (?,?,?)', [$_POST['routeName'], $_POST['color'], $_POST['vendor']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'mealSchedule') {
        $db->insert('INSERT INTO meals (recipient_id, name) VALUES (?,?)', [$_SESSION['editing_recip_id'], $_POST['mealName']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'diet') {
        $db->insert('INSERT INTO diets (name,abbre,description) VALUES (?,?,?)', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'accommodation') {
        $db->insert('INSERT INTO accommodations (name,abbr,description) VALUES (?,?,?)', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'allergies') {
        $db->insert('INSERT INTO allergies (name,abbr,description) VALUES (?,?,?)', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'milks') {
        $db->insert('INSERT INTO milkChoices (name,abbre,decs) VALUES (?,?,?)', [$_POST['dietName'], $_POST['dietAbbre'], $_POST['descr']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'cert') {
        $physician_id = (empty($_POST['physician_id'])) ?  0 : $_POST['physician_id'];
        $clinic_id = (empty($_POST['clinic_id'])) ?  0 : $_POST['clinic_id'];
        // $referralDate = (empty($_POST['referralDate'])) ? NULL : date('Y-m-d',strtotime($_POST['referralDate']));

        $insert = $db->insert('INSERT INTO certificateLogs (recipient_id, startDate, endDate, physician, clinic) VALUES (?,?,?,?,?)', [
            $_POST['recipient_id'],
            date('Y-m-d', strtotime($_POST['startDate'])),
            date('Y-m-d', strtotime($_POST['endDate'])),
            $physician_id,
            $clinic_id
        ]);

        if ($insert == false) {
            exit('There was an issue saving.');
        } else {
            exit('ok');
        }
    } else if ($_POST['contentType'] == 'schedule') {
        $type = (int) $_POST['scheduleType'];
        $name = ($type == 1) ? 'Main Schedule' : 'Personal Schedule';
        $recip_id = ($type == 1) ? 0 : (int) $_POST['newSchedule_recip_id'];
        $days = explode(',', $_POST['dayMatrix']);
        $daySelects = array();
        foreach ($days as $day) {
            $breakDown = explode('-', $day);
            $daySelects[] = $breakDown[0];
        }
        $inf = (isset($_POST['indefinite'])) ? 1 : 0;
        $endDate = (isset($_POST['indefinite'])) ? NULL : date('Y-m-d', strtotime($_POST['scheduleEndDate']));
        $insert = $db->insert('INSERT INTO meals (recipient_id,name,monday,tuesday,wednesday,thursday,friday,saturday,sunday,startDate,endDate,main,mealCounts,inf) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
            $recip_id,
            $name,
            $daySelects[0],
            $daySelects[1],
            $daySelects[2],
            $daySelects[3],
            $daySelects[4],
            $daySelects[5],
            $daySelects[6],
            date('Y-m-d', strtotime($_POST['scheduleStartDate'])),
            $endDate,
            $type,
            $_POST['dayMatrix'],
            $inf
        ]);
        if (!$insert) {
            exit('There was an issue saving this schedule');
        } else {
            exit('ok');
        }
    } else if ($_POST['contentType'] == 'offDay') {
        $endDate = (isset($_POST['offTemp'])) ? NULL : date('Y-m-d', strtotime($_POST['offDateEndDate']));
        $offTemp = (isset($_POST['offTemp'])) ? 1 : 0;

        $insert = $db->insert('INSERT INTO daysOff (recipient_id, indefinate, startDate, endDate, deactivate, reason) VALUES (?,?,?,?,?,?)', [
            $_POST['recip_id'],
            $offTemp,
            date('Y-m-d', strtotime($_POST['offDayStartDate'])),
            $endDate,
            0,
            $_POST['reason']
        ]);

        if (!$insert) {
            exit('There was an issue saving. ' . $db->error);
        } else {
            exit('ok');
        }
    } else if ($_POST['contentType'] == 'assessment-init') {
        $insert = $db->insert('INSERT INTO assessments (name,hide) VALUES (?,?)', [$_POST['name'],0]);
        if ($insert == false) {
            $msg = 'There was an issue saving.';
        } else {
            $msg = 'ok';
        }


        header('Content-Type: application/json');
        $response = array(
            'msg' => $msg,
            'id' => $insert
        );
        echo json_encode($response);
        exit();
    } else if ($_POST['contentType'] == 'assessment-question') {
        $assess_id = (int) $_POST['assess_id'];
        $type_id = (int) $_POST['type_id'];
        $indexQuery = (int) count($db->select('SELECT id FROM assessmentQuestions WHERE assessment_id = ?', [$_POST['assess_id']]));
        $indexQuery++;

        $questionInsert = $db->insert('INSERT INTO assessmentQuestions (assessment_id, prompt, displayOrder, type_id, hidden) VALUES (?,?,?,?,?)', [$assess_id, $_POST['prompt'], $indexQuery, $type_id, 0]);

        if ($questionInsert == false) {
            exit('There was an issue saving this.');
        } else {
            $optionIndex = 1;
            foreach ($_POST['answerOption'] as $option) {
                $db->insert('INSERT INTO assessmentQuestionsOptions (assessment_id, question_id, textContent, displayOrder, hidden) VALUES (?,?,?,?,?)', [$assess_id, $questionInsert, $option, $optionIndex, 0]);
                $optionIndex++;
            }
        }
        exit('ok');
    } else if ($_POST['contentType'] == 'recip-assessment') {
        exit($_POST['dateCreated']);
        $checkboxes = [];
        $otherInputs = [];
        $allInputs = [];
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $checkboxes[] = $key;
            } else {
                if ($key != 'assess_id' && $key != 'recip_id' && $key != 'contentType') {
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


        $assessment = $db->select('SELECT * FROM assessments WHERE id = ?',[$_POST['assess_id']])[0];
        $scoreInsert = $db->insert('INSERT INTO assessmentScoreLinks (recipient_id, assessment_id, dateCreated, lastUpdated, assessmentName, archived) VALUES (?,?,?,?,?,?)',[$_POST['recip_id'], $assessment['id'], date('Y-m-d',strtotime($_POST['dateCreated'])), date('Y-m-d H:i:s'),$assessment['name'],0]);
        if($scoreInsert == false){
            exit('There was an issue saving');
        } else {
            foreach($allInputs as $row){
                $value = (ctype_digit($row[1])) ? $row[1] : NULL;
                $textValue = (ctype_digit($row[1])) ? NULL : $row[1];
                $question = $db->select('SELECT * FROM assessmentQuestions WHERE id = ?',[$row[0]])[0];
                $type = (int) $question['type_id'];
                $db->insert('INSERT INTO assessmentScores (assessment_id, scoreType_id, question_id, value, textValue, link_id) VALUES (?,?,?,?,?,?)',[
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
    } else if ($_POST['contentType'] == 'routeChange') {
        $recip = $db->select('SELECT * FROM recipients WHERE id = ?',[$_POST['recip_id']])[0];
        if($_POST['route_id'] == $recip['route_id']){
            exit('Select a new route');
        }
        if(date('Y-m-d',strtotime($_POST['date'])) < date('Y-m-d')){
            exit('Date must be present or future');
        }
        $insert = $db->insert('INSERT INTO routeChanges (dateStamp, recipient_id, route_id, ran, newPosition) VALUES (?,?,?,?,?)',[
            date('Y-m-d',strtotime($_POST['date'])), $recip['id'], $_POST['route_id'], 0, $_POST['position']
        ]);
        if($insert != false){
            if(date('Y-m-d',strtotime($_POST['date'])) == date('Y-m-d')){
                exit('reload');
            } else {
                exit('ok');
            }
        } else {
            exit($db->error);
        }
    } else if ($_POST['contentType'] == 'billingMethod') {
        if($_POST['type'] == 1){
            $insert = $db->insert('INSERT INTO billings (recipient_id, billType_id, startDate) VALUES (?,?,?)',[$_POST['recip_id'], $_POST['type'], date('Y-m-d',strtotime($_POST['startDate']))]);
        } else if($_POST['type'] == 2){
            $endDate = (empty($_POST['endDate'])) ? NULL : date('Y-m-d',strtotime($_POST['endDate']));
            $issueDate = (empty($_POST['issueDate'])) ? NULL : date('Y-m-d',strtotime($_POST['issueDate']));
            $unitedApproved = $_POST['unitedApproved'];
            $insert = $db->insert('INSERT INTO billings (recipient_id, billType_id, startDate, subscriber_id, authorizationNumber, endDate, issueDate, unitedApproved, frequency) VALUES (?,?,?,?,?,?,?,?,?)',[
                $_POST['recip_id'], $_POST['type'], date('Y-m-d',strtotime($_POST['startDate'])), $_POST['subscriber_id'], $_POST['authorizationNumber'], $endDate, $issueDate, $unitedApproved, $_POST['frequency']
            ]);
        } else if($_POST['type'] == 3){
            $insert = $db->insert('INSERT INTO billings (recipient_id, billType_id, startDate) VALUES (?,?,?)',[$_POST['recip_id'], $_POST['type'], date('Y-m-d',strtotime($_POST['startDate']))]);
        }

        if($insert == false){
            exit($db->error);
        } else {
            exit('ok');
        }
    }
}
exit();
