<?php
$assess = $db->select('SELECT * FROM assessments WHERE id = ?', [$_POST['id']])[0];
?>
<h2>Edit <?php echo $assess['name'] ?></h2>
<form action="" id="newContentForm">
    <div class="formRow">
        <div class="inputRow">
            <label for="name">Name</label>
            <input value="<?php echo $assess['name'] ?>" type="text" name="name" id="name" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow" style="align-items: baseline;">
            <?php
            $checked = ($assess['hide'] == 1) ? ' checked' : '';
            ?>
            <label for="hidden"><input type="checkbox" name="hidden" id="hidden"<?php echo $checked ?>>Hidden</label>
            
        </div>
    </div>
    <p>Add Questions</p>
    <div class="formRow">
        <select id="optionType">
            <option value="" hidden selected>Select</option>
            <?php
            $optionTypes = $db->select('SELECT * FROM assessmentTypes ORDER BY name ASC');
            foreach ($optionTypes as $option) {
                echo '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
            }
            ?>
        </select>
        <div class="inputRow">
            <button type="button" class="addConentBtn" onclick="addOption()">Add Question</button>
        </div>
    </div>

    <div id="optionListWrap"></div>
    <input type="text" name="quest_matrix" id="quest_matrix" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Save</button>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="button" onclick="deleteAssessment()" class="deleteBtn">Delete</button>
        </div>
    </div>
</form>
<style>
    .questionListItem.assesshidden {
        text-decoration: line-through;
    }
</style>
<script>
    $(document).ready(() => {
        fillQuestionList()
        $('#optionListWrap').sortable({
            placeholder: "ui-state-highlight",
            update: (e, ul) => {
                var quest_matrix = '';
                $('.questionListItem').each((index, el) => {
                    var delimit = (index == 0) ? '' : ',';
                    quest_matrix += delimit + $(el).prop('id') + '-' + (index + 1);
                })
                $('#quest_matrix').val(quest_matrix);
            }
        })
    })

    function deleteAssessment() {
        if (confirm('Are you sure?')) {
            if (confirm('This action will delete this assessment and any scores attached to it.')) {
                $.ajax({
                    type: 'POST',
                    url: 'menu/handlers/deleteContentHandler.php',
                    data: {
                        contentType: 'assessment',
                        id: <?php echo $assess['id'] ?>
                    },
                    success: (re) => {
                        if (re.trim() == 'ok') {
                            fillAssessments();
                            $('#displayContentWrap').html('');
                        }
                    }
                })
            }
        }
    }

    function fillQuestionList() {
        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillQuestionList.php',
            data: {
                assess_id: <?php echo $assess['id'] ?>,
            },
            success: (re) => {
                $('#optionListWrap').html(re);
            }
        })
    }

    function addOption() {
        if($('#optionType').val() == ''){
            customMessage('Select a question type',3)
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'menu/addContentForms/newAssessmentOption.php',
            data: {
                assess_id: <?php echo $assess['id'] ?>,
                type_id: $('#optionType').val()
            },
            success: (re) => {
                newModal();
                $('#modalBody').append(re);
            }
        })
    }

    function editQuestion(id) {
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/editAssessmentOption.php',
            data: {
                id: id,
            },
            success: (re) => {
                newModal();
                $('#modalBody').append(re);
            }
        })
    }
    $(document).ready(() => {
        $('#newContentForm').on('submit', (e) => {
            e.preventDefault();
            var form = $('#newContentForm')[0];
            var formData = new FormData(form);
            formData.append('contentType', 'assessment');
            formData.append('id', <?php echo $assess['id'] ?>);
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                processData: false,
                contentType: false,
                data: formData,
                success: (re) => {
                    console.log(re)
                    if (re.trim() == 'ok') {
                        fillAssessments();
                        $('#displayContentWrap').html('');
                    } else {
                        customMessage(re, 3)
                    }
                }
            })
        })
    })
</script>