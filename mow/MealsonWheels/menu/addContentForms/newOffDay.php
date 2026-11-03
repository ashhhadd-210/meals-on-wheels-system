<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<button type="button" onclick="fillOffDays()">Back</button>
<form action="" method="post" id="offDayForm">
    <h3>New Off Time</h3>
    <div class="formRow">
        <div class="inputRow">
            <label for="offDayStartDate">Start Date</label>
            <input type="date" name="offDayStartDate" required>
        </div>
        <div class="inputRow" id="offDayEndDateRow">
            <label for="offDateEndDate">End Date</label>
            <input type="date" name="offDateEndDate" id="offDateEndDate" required>
        </div>
        <div class="inputRow" style="align-items: baseline;">
            <label for="offTemp">Off Temp</label>
            <input type="checkbox" name="offTemp" id="offTemp">
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
            <input type="text" name="reason" id="reason" required>
        </div>
    </div>
    <div class="formRow">
        <button class="addConentBtn" type="button" onclick="fillReason(this)">Appointment</button>
        <button class="addConentBtn" type="button" onclick="fillReason(this)">Out Of Town</button>
        <button class="addConentBtn" type="button" onclick="fillReason(this)">Facility</button>
        <button class="addConentBtn" type="button" onclick="fillReason(this)">Email Hospital</button>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Add</button>
        </div>
    </div>
</form>
<script>
    function fillReason(btn){
        if($('#reason').val().trim() == ''){
            $('#reason').val($(btn).text());
        } else {
            $('#reason').val($('#reason').val() + ', ' + $(btn).text());
        }
    }
    $(document).ready(function() {
        $('#offDayForm').on('submit', function(e) {

            e.preventDefault();
            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append('contentType', 'offDay')
            formData.append('recip_id', <?php echo $_POST['recip_id'] ?>)

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        updateMealSchedule();
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