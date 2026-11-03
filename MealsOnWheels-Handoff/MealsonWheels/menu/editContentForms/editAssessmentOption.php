<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$question = $db->select('SELECT * FROM assessmentQuestions WHERE id = ?', [$_POST['id']])[0];

$optionType = $db->select('SELECT * FROM assessmentTypes WHERE id = ?', [$question['type_id']])[0];
$assess = $db->select('SELECT * FROM assessments WHERE id = ?', [$question['assessment_id']])[0];
?>
<form action="" id="questionForm">
    <h3>Edit Question</h3>
    <p>Type: <b><?php echo $optionType['name'] ?></b></p>
    <div class="formRow">
        <div class="inputRow">
            <?php
            $hidden = ($question['hidden'] == 1) ? ' checked' : '';
            ?>
            <label for="deprecated"><input type="checkbox" name="deprecated" id="deprecated"<?php echo $hidden ?>>Deprecated (Don't show / no longer used)</label>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="prompt">Prompt</label>
            <?php
            if($optionType['id'] == 5){
                echo '<input type="text" value="'.$question['prompt'].'" disabled>';
                echo '<input type="text" value="'.$question['prompt'].'" name="prompt" style="display:none">';
            } else {
                echo '<input type="text" value="'.$question['prompt'].'" name="prompt" required>';
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
        <?php
        $questionAnswers = $db->select('SELECT * FROM assessmentQuestionsOptions WHERE question_id = ? ORDER BY displayOrder ASC', [$question['id']]);
        if ($questionAnswers != false) {
            foreach ($questionAnswers as $qu) {
                echo '<div id="' . $qu['id'] . '" class="answerOption"><span id="textValue">' . $qu['textContent'] . '</span><button type="button" class="deleteBtn" onclick="deleteOption(' . $qu['id'] . ',this)">Delete</button></div>';
            }
        }
        ?>
    </div>
    <input type="text" name="displayOrders" id="displayOrders" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Save</button>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="button" class="deleteBtn" onclick="deleteQuestion()">Delete</button>
        </div>
    </div>
</form>
<script>
    var addOptions = <?php echo $addOptions ?>;
    $(document).ready(() => {
        $('#optionList').sortable({
            update: (e, ui) => {
                updateMatrix()
            }
        })
        $('#questionForm').on('submit', (e) => {
            e.preventDefault();
            var form = $('#questionForm')[0];
            var formData = new FormData(form);
            formData.append('contentType', 'assessment-question');
            formData.append('assess_id', '<?php echo $assess['id'] ?>');
            formData.append('question_id', '<?php echo $question['id'] ?>');
            if (addOptions) {
                updateMatrix();
                var questionCount = 0;
                $('.answerOption').each((index, element) => {
                    questionCount++;
                    var answerOptionArray = $(element).prop('id') + '||' + $(element).find('#textValue').first().text();
                    formData.append('answerOption[]', answerOptionArray)
                })
                if (questionCount < 1) {
                    customMessage('One answer is required.', 3)
                    return false;
                }
            }

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                processData: false,
                contentType: false,
                data: formData,
                success: (re) => {
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

    function deleteQuestion() {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: 'assessment-question',
                    id: <?php echo $question['id'] ?>
                },
                success: (re) => {
                    if (re.trim() == 'ok') {
                        customModalClear();
                        fillQuestionList();
                    }
                }
            })
        }
    }

    function deleteOption(id, btn) {
        if (confirm('Are you sure?')) {
            if (id == 0) {
                $(btn).parent().remove();
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'menu/handlers/deleteContentHandler.php',
                    data: {
                        contentType: 'assessment-question-option',
                        id: id
                    },
                    success: (re) => {
                        if (re.trim() == 'ok') {
                            $(btn).parent().remove();
                        }
                    }
                })
            }
        }
    }

    function updateMatrix() {
        var matrixString = '';
        $('.answerOption').each((index, el) => {
            var delimit = (index == 0) ? '' : ',';
            matrixString += delimit + $(el).prop('id') + '-' + (index + 1)
        })
        $('#displayOrders').val(matrixString)
    }

    function addAnswer() {
        var option = $('<div>', {
            class: 'answerOption',
            id: '0'
        });

        var textValue = $('<span>', {
            id: 'textValue',
            text: $('#answerText').val()
        })

        var deletebtn = $('<button>', {
            type: 'button',
            onclick: 'deleteOption(0,this)',
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