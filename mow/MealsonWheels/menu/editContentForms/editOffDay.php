<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$dayOff = $db->select('SELECT * FROM daysOff WHERE id = ?', [$_POST['id']])[0];
?>
<button type="button" onclick="fillOffDays()">Back</button>
<form action="" method="post" id="offDayForm">
    <h3>Edit Off Time</h3>
    <div class="formRow">
        <div class="inputRow" style="align-items: baseline;border:1px solid #c6c6c6;border-radius:5px">
            <label for="deactivate">Deactivate</label>
            <input type="checkbox" name="deactivate" id="deactivate" <?php echo ((empty($dayOff['deactivate'])) ? '' : ' checked') ?>>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="offDayStartDate">Start Date</label>
            <input value="<?php echo date('Y-m-d', strtotime($dayOff['startDate'])) ?>" type="date" name="offDayStartDate" required>
        </div>
        <?php
        $endDate = (empty($dayOff['indefinate'])) ? date('Y-m-d', strtotime($dayOff['endDate'])) : '';
        $endDateDispaly = (empty($dayOff['indefinate'])) ? '' : 'display:none';
        $indefCheck = (empty($dayOff['indefinate'])) ? '' : ' checked';
        $endDateRequired = (empty($dayOff['indefinate'])) ? ' required' : '';
        ?>
        <div class="inputRow" id="offDayEndDateRow" style="<?php echo $endDateDispaly ?>">
            <label for="offDateEndDate">End Date</label>
            <input value="<?php echo $endDate ?>" type="date" name="offDateEndDate" id="offDateEndDate" <?php echo $endDateRequired ?>>
        </div>
        <div class="inputRow" style="align-items: baseline;">
            <label for="offTemp">Off Temp</label>
            <input type="checkbox" name="offTemp" id="offTemp" <?php echo $indefCheck ?>>
        </div>
        <script>
            $(document).ready(function() {
                $('#offTemp').on('change', function() {
                    if ($(this).prop('checked')) {
                        $('#offDayEndDateRow').hide(200)
                        $('#offDateEndDate').prop('required', false)
                    } else {
                        $('#offDayEndDateRow').show(200)
                        $('#offDateEndDate').prop('required', true)
                    }
                })
            })
        </script>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="reason">Reason</label>
            <input value="<?php echo $dayOff['reason'] ?>" type="text" name="reason" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Update</button>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="button" class="deleteBtn" onclick="deleteOffDay()">Delete</button>
        </div>
    </div>
</form>
<script>
    function deleteOffDay() {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: 'offDay',
                    id: <?php echo $_POST['id'] ?>
                },
                success: function(response) {
                    if(response == 'ok'){
                        fillOffDays();
                    } else {
                        customMessage(response,3)
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }
    }
    $(document).ready(function() {
        $('#offDayForm').on('submit', function(e) {

            e.preventDefault();
            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append('contentType', 'offDay')
            formData.append('id', <?php echo $_POST['id'] ?>)

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        // updateCalendar($('#currentScheduleId').val());
                        fillOffDays();
                    } else {
                        customMessage(response, 3)
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            })
        })
    })
</script>
<style>
    #offDayForm {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
</style>