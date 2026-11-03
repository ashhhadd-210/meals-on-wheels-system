<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}

$link = $db->select('SELECT * FROM assessmentScoreLinks WHERE id = ?', [$_POST['link_id']])[0];

$assess = $db->select('SELECT * FROM assessments WHERE id = ?', [$link['assessment_id']])[0];
if ($link['assessmentName'] == $assess['name']) {
    $displayName = $link['assessmentName'];
} else {
    $displayName = $link['assessmentName'] . ' (' . $assess['name'] . ')';
}
$questions = $db->select('SELECT * FROM assessmentQuestions WHERE assessment_id = ? ORDER BY displayOrder ASC', [$assess['id']]);
?>
<button type="button" onclick="toggleEditAssessment(this)">Edit Assessment</button>
<form action="" id="assessmentForm">
    <h1><?php echo $displayName ?></h1>

    <?php
    $rowAmount = 0;
    foreach ($questions as $qu) {
        echo '<div class="formRow">';
        echo '<div class="inputRow">';
        echo '<label for="' . $qu['id'] . '">' . $qu['prompt'] . '</label>';
        if ($qu['type_id'] == 1) {
            $answer = $db->select('SELECT * FROM assessmentScores WHERE question_id = ? AND link_id = ?', [$qu['id'],$link['id']])[0];
            echo '<select name="' . $qu['id'] . '">';
            $options = $db->select('SELECT * FROM assessmentQuestionsOptions WHERE question_id = ?', [$qu['id']]);
            echo '<option value="" selected hidden>Select</option>';
            foreach ($options as $opt) {
                $selected = ($answer['value'] == $opt['id']) ? ' selected' : '';
                echo '<option value="' . $opt['id'] . '"' . $selected . '>' . $opt['textContent'] . '</option>';
            }
            echo '</select>';
        } else if ($qu['type_id'] == 4) {
            $options = $db->select('SELECT * FROM assessmentQuestionsOptions WHERE question_id = ?', [$qu['id']]);
            $ids = [];
            $answer = $db->select('SELECT * FROM assessmentScores WHERE question_id = ? AND link_id = ?', [$qu['id'],$link['id']]);
            foreach($answers as $ans){
                $ids[] = $ans['value'];
            }
            foreach ($options as $opt) {
                $selected = (in_array($opt['id'], $ids)) ? ' checked' : '';
                echo '<label for="' . $qu['id'] . '"><input type="checkbox" name="' . $qu['id'] . '[]" value="' . $opt['id'] . '"'.$selected.'> ' . $opt['textContent'] . '</label>';
            }
        } else if ($qu['type_id'] == 5) {
            $answer = $db->select('SELECT * FROM assessmentScores WHERE question_id = ? AND link_id = ?', [$qu['id'],$link['id']])[0];
            echo '<input type="date" name="'.$qu['id'].'" value="'.date('Y-m-d',strtotime($answer['textValue'])).'" required>';
        } else if ($qu['type_id'] == 2) {
            $answer = $db->select('SELECT * FROM assessmentScores WHERE question_id = ? AND link_id = ?', [$qu['id'],$link['id']])[0];
            echo '<input type="text" name="' . $qu['id'] . '" value="'.$answer['textValue'].'">';
        }
        echo '</div>';
        echo '</div>';
        echo '<hr>';
    }
    ?>

    <div class="formRow">
        <div><input type="checkbox" name="archived" id="archived"<?php echo (($link['archived'] == 1) ? ' checked' : '') ?>><label for="archived">Archived</label></div>
    </div>

    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Save</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(() => {
        $('#assessmentForm').on('submit', (e) => {
            e.preventDefault();
            var form = $('#assessmentForm')[0];
            var formData = new FormData(form);
            formData.append('assess_id', <?php echo $assess['id'] ?>)
            formData.append('link_id', '<?php echo $link['id'] ?>')
            formData.append('contentType', 'recip-assessment')

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                processData: false,
                contentType: false,
                success: (re) => {
                    if (re.trim() == 'ok') {
                        customModalClear()
                        fillRecipAssessments();
                    } else {
                        customMessage(re, 3)
                    }
                }
            })
        })
    })
</script>
<style>
    #assessmentForm {
        width: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
</style>