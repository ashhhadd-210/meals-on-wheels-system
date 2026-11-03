<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1 && $user['permissionLevel'] >= 2) {
    if ($_POST['contentType'] == 'recipient') {
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'contact') {
        $db->delete('DELETE FROM contacts WHERE id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM contact_link WHERE contact_id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'relationship') {
        $query = $db->select('SELECT * FROM relationships WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM contact_link WHERE relationship_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' contact links';
            exit();
        } else {
            $db->delete('DELETE FROM relationships WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'gender') {
        $query = $db->select('SELECT * FROM genders WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE gender_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM genders WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'ethnicGroup') {
        $query = $db->select('SELECT * FROM ethnicGroups WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE ethnicGroup_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM ethnicGroups WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'maritalStatus') {
        $query = $db->select('SELECT * FROM maritalStatus WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE maritalStatus_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM maritalStatus WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'clientStatus') {
        $query = $db->select('SELECT * FROM clientStatus WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE clientStatus_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM clientStatus WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'physician') {
        $query = $db->select('SELECT * FROM physicians WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE physician_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM physicians WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'clinic') {
        $query = $db->select('SELECT * FROM clinics WHERE id = ?', [$_POST['id']])[0];
        $useCount = 0;
        $useCount += count($db->select('SELECT id FROM recipients WHERE clinic_id = ?', [$query['id']]));
        if ($useCount > 0) {
            echo 'This option is being used in ' . $useCount . ' recipients';
            exit();
        } else {
            $db->delete('DELETE FROM clinics WHERE id = ?', [$_POST['id']]);
            echo 'ok';
            exit();
        }
    } else if ($_POST['contentType'] == 'contactLink') {
        $db->delete('DELETE FROM contact_link WHERE id = ?', [$_POST['id']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'noteLogContact') {
        $db->delete('DELETE FROM noteLogContacts WHERE id = ?', [$_POST['id']]);
        echo 'ok';
        exit();
    } else if ($_POST['contentType'] == 'diet') {
        $db->delete('DELETE FROM diets WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'accommodations') {
        $db->delete('DELETE FROM accommodations WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'allergies') {
        $db->delete('DELETE FROM allergies WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'milks') {
        $db->delete('DELETE FROM milkChoices WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'cert') {
        $db->delete('DELETE FROM certificateLogs WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'schedule') {
        $db->delete('DELETE FROM meals WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'offDay') {
        $db->delete('DELETE FROM daysOff WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'assessment-question-option') {
        $db->delete('DELETE FROM assessmentQuestionsOptions WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'assessment-question') {
        $db->delete('DELETE FROM assessmentQuestionsOptions WHERE question_id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM assessmentQuestions WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'assessment') {
        $db->delete('DELETE FROM assessmentQuestionsOptions WHERE assessment_id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM assessmentQuestions WHERE assessment_id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM assessments WHERE id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM assessmentScoreLinks WHERE assessment_id = ?', [$_POST['id']]);
        $db->delete('DELETE FROM assessmentScores WHERE assessment_id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'routeChange') {
        $db->delete('DELETE FROM routeChanges WHERE id = ?', [$_POST['id']]);
        exit('ok');
    } else if ($_POST['contentType'] == 'recip-billingLog') {
        $db->delete('DELETE FROM billings WHERE id = ?', [$_POST['id']]);
        exit('ok');
    }
} else {
    exit('You do not have permission for this action.');
}
