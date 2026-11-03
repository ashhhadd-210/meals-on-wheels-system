<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}

$assess = $db->select('SELECT * FROM assessments WHERE id = ?', [$_POST['assess_id']])[0];
$recip =  $db->select('SELECT * FROM recipients WHERE id = ?', [(int)$_POST['recip_id']])[0];

$questions = $db->select('SELECT * FROM assessmentQuestions WHERE assessment_id = ? AND hidden = ? ORDER BY displayOrder ASC', [$assess['id'],0]);
?>

<form action="" id="assessmentForm">
    <h1><?php echo $assess['name'] ?></h1>

    <?php
    $rowAmount = 0;
    foreach ($questions as $qu) {
        echo '<div class="formRow">';
        echo '<div class="inputRow">';
        echo '<label for="'.$qu['id'].'">'.$qu['prompt'].'</label>';
        if($qu['type_id'] == 1){
            echo '<select name="'.$qu['id'].'">';
            $options = $db->select('SELECT * FROM assessmentQuestionsOptions WHERE question_id = ?',[$qu['id']]);
            echo '<option value="" selected hidden>Select</option>';
            foreach($options as $opt){
                echo '<option value="'.$opt['id'].'">'.$opt['textContent'].'</option>';
            }
            echo '</select>';
        } else if ($qu['type_id'] == 4) {
            $options = $db->select('SELECT * FROM assessmentQuestionsOptions WHERE question_id = ?',[$qu['id']]);
            foreach($options as $opt){
                echo '<label for="'.$qu['id'].'"><input type="checkbox" name="'.$qu['id'].'[]" value="'.$opt['id'].'"> '.$opt['textContent'].'</label>';
            }
        } else if ($qu['type_id'] == 5) {
            echo '<input type="date" name="'.$qu['id'].'" value="'.date('Y-m-d').'" required>';
        } else if ($qu['type_id'] == 2){
            echo '<input type="text" name="'.$qu['id'].'">';
        }
        echo '</div>';
        echo '</div>';
        echo '<hr>';
    }
    ?>

    <div class="formRow">
        <div class="inputRow">
            <label for="dateCreated">Date Created</label>
            <input type="date" name="dateCreated" id="dateCreated" value="<?php echo date('Y-m-d') ?>" required>
        </div>
    </div>

    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Save</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(()=>{
        $('#assessmentForm').on('submit',(e)=>{
            e.preventDefault();
            var form = $('#assessmentForm')[0];
            var formData = new FormData(form);
            formData.append('assess_id',<?php echo $assess['id'] ?>)
            formData.append('recip_id',<?php echo $recip['id'] ?>)
            formData.append('contentType','recip-assessment')

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                data: formData,
                processData: false,
                contentType: false,
                success: (re)=>{
                    if(re.trim() == 'ok'){
                        customModalClear()
                        fillRecipAssessments();
                    } else {
                        customMessage(re,3)
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