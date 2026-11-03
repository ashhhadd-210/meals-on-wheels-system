<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$optionType = $db->select('SELECT * FROM assessmentTypes WHERE id = ?', [$_POST['type_id']])[0];
$assess = $db->select('SELECT * FROM assessments WHERE id = ?', [$_POST['assess_id']])[0];
?>
<form action="" id="questionForm">
    <h3>New Question</h3>
    <p>Type: <b><?php echo $optionType['name'] ?></b></p>

    <div class="formRow">
        <div class="inputRow">
            <label for="prompt">Prompt</label>
            <?php
            if($optionType['id'] == 5){
                echo '<input type="text" value="Next Due Date" disabled>';
                echo '<input type="text" value="Next Due Date" name="prompt" style="display:none">';
            } else {
                echo '<input type="text" name="prompt" required>';
            }
            ?>
            
        </div>
    </div>
    <p>Answers</p>
    <?php
    if ($optionType['id'] == 2) {
        echo '<input type="text" placeholder="Text Answer" disabled>';
        $addOptions = 'false';
    } else if ($optionType['id'] == 5){
        echo '<div class="formRow">';
        echo '<div class="inputRow">';
        echo '<label>Next Due Date <i> - Example:</i></label>';
        echo '<input type="text" placeholder="'.date('m/d/Y').'" disabled>';
        
        echo '</div>';
        echo '</div>';
        $addOptions = 'false';
    } else {
        echo '<div class="formRow">';
        echo '<input type="text" id="answerText" placeholder="Answer Text">';
        echo '<button class="addConentBtn" type="button" onclick="addAnswer()">Add</button>';
        echo '</div>';
        $addOptions = 'true';
    }
    ?>
    <hr>
    <div id="optionList">

    </div>

    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Add</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(() => {
        $('#optionList').sortable()
        $('#questionForm').on('submit', (e) => {
            e.preventDefault();
            var form = $('#questionForm')[0];
            var formData = new FormData(form);
            formData.append('contentType', 'assessment-question');
            formData.append('assess_id', '<?php echo $assess['id'] ?>');
            formData.append('type_id', '<?php echo $_POST['type_id'] ?>');
            var addOptions = <?php echo $addOptions ?>;
            if(addOptions){
                var questionCount = 0;
                $('.answerOption').each((index,element)=>{
                    questionCount++;
                    formData.append('answerOption[]',$(element).find('#textValue').first().text())
                })
                if(questionCount < 1){
                    customMessage('One answer is required.',3)
                    return false;
                }
            }

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                processData: false,
                contentType: false,
                data: formData,
                success: (re) => {
                    console.log(re)
                    if (re == 'ok') {
                        customModalClear();
                        fillQuestionList();
                    } else {
                        customMessage(re, 3)
                    }
                }
            })
        })
    })

    function addAnswer() {
        var option = $('<div>', {
            class: 'answerOption',
        });

        var textValue = $('<span>',{
            id: 'textValue',
            text: $('#answerText').val()
        })

        var deletebtn = $('<button>', {
            type: 'button',
            onclick: 'this.parentNode.remove()',
            text: 'Delete',
            class: 'deleteBtn'
        })

        option.append(textValue);
        option.append(deletebtn);
        $('#optionList').append(option);
        $('#answerText').val('')
    }
</script>
<style>
    #questionForm {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px;
    }

    .answerOption {
        border: 1px solid #c6c6c6;
        padding: 5px 20px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #eee;
    }

    #optionList {
        width: 390px;
        margin: 10px auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>