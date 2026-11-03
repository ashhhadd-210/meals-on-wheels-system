<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$schedule = $db->select('SELECT * FROM meals WHERE id = ?', [$_POST['id']])[0];
?>

<form action="" id="newScheduleForm">
    <input name="schedule_id" id="schedule_id" type="text" value="<?php echo $schedule['id'] ?>" readonly hidden>
    <h3>Edit Schedule</h3>
    <div class="formRow">
        <div class="inputRow">
            <label for="scheduleType">Schedule Type</label>
            <select name="scheduleType" id="scheduleType" required>
                <?php
                $type = ($schedule['main'] == 1) ? 'Default (System-wide)' : 'Individual (This recipient only)';
                echo '<option value="0">' . $type . '</option>'
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="scheduleStartDate">Start Date</label>
            <input type="date" value="<?php echo $schedule['startDate'] ?>" name="scheduleStartDate" id="scheduleStartDate" required>
        </div>
            <?php
            $inf = (empty($schedule['inf'])) ? '' : ' checked';
            $endDateVis = (empty($schedule['inf'])) ? '' : ' display:none';
            $endDateReq = (empty($schedule['inf'])) ? 'required' : '';
            ?>
        <div class="inputRow" id="endDateRow" style="<?php echo $endDateVis ?>">
            <label for="scheduleEndDate">End Date</label>
            <input type="date" value="<?php echo $schedule['endDate'] ?>" name="scheduleEndDate" id="scheduleEndDate"<?php echo $endDateReq ?>>
        </div>
        <div class="inputRow" style="align-items: baseline">
            <label for="indefinite">Indefinite</label>
            <input type="checkbox" name="indefinite" id="indefinite"<?php echo $inf ?>>
        </div>
        <script>
            $(document).ready(()=>{
                $('#indefinite').on('change', function(){
                    if($(this).prop('checked')){
                        $('#endDateRow').hide(200)
                        $('#scheduleEndDate').prop('required',false)
                    } else {
                        $('#endDateRow').show(200)
                        $('#scheduleEndDate').prop('required',true)
                    }
                })
            })
        </script>
    </div>
    <div class="formRow">
        <p><i>The newest 'Main Schedule' will override all other Main Schedules upon it's start date. A 'Personal Schedule' will override all 'Main Schedules' and the newest will override all other Personal Schedules upon it's start date.</i></p>
    </div>
    <?php
    $days = explode(',', $schedule['mealCounts']);
    $daySelects = array();
    foreach ($days as $day) {
        $breakDown = explode('-', $day);
        $daySelects[] = $breakDown;
    }
    $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    ?>
    <div class="formRow">
        <div class="inputRow">
            <label for="">Select Days</label>
            <div class="daySelectWrap">
                <?php
                for ($i = 0; $i < 7; $i++) {
                    echo '<div class="daySelectItem">';
                    $selected = ($daySelects[$i][0] == 1) ? ' selected' : '';
                    $nothidden = ($daySelects[$i][0] == 1) ? 'display:block' : '';
                    $hotCount = (empty($daySelects[$i][1])) ? 0 : $daySelects[$i][1];
                    $frozenCount = (empty($daySelects[$i][2])) ? 0 : $daySelects[$i][2];
                    echo '<div class="daySelectArea' . $selected . '" onclick="selectDay(this)">' . $dayNames[$i] . '</div>';
                    echo '<div style="' . $nothidden . '" class="mealSelectCountWrap">';
                    echo '<div>';
                    echo '<label for="">Default Hot</label>';
                    echo '<input min="0" value="' . $hotCount . '" type="number" class="hotCount">';
                    echo '</div>';
                    echo '<div>';
                    echo '<label for="">Default Frozen</label>';
                    echo '<input min="0" value="' . $frozenCount . '" type="number" class="frozenCount">';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Update</button>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button onclick="deleteSchedule()" class="deleteBtn" type="button">Delete</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(() => {
        $('#newScheduleForm').on('submit', function(e) {
            e.preventDefault();
            var dayMatrix = '';

            var daysItems = $('.daySelectArea');
            daysItems.each((index, element) => {
                var delimit = (index == 0) ? '' : ',';
                var selected = ($(element).hasClass('selected')) ? '1' : '0';
                var hotCount = $(element).parent().find('.hotCount').first().val();
                var frozenCount = $(element).parent().find('.frozenCount').first().val();
                dayMatrix += delimit + selected + '-' + hotCount + '-' + frozenCount;
            })

            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append('dayMatrix', dayMatrix)
            formData.append('contentType', 'schedule')

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        updateMealSchedule($('#schedule_id').val());
                        openSchdeule($('#schedule_id').val())
                        customModalClear();
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

    function selectDay(btn) {
        var infoWrap = $(btn).parent().find('.mealSelectCountWrap').first()
        if ($(btn).hasClass('selected')) {
            $(btn).removeClass('selected')
            $(infoWrap).hide(100)
            $(btn).parent().find('.hotCount').first().val(0);
            $(btn).parent().find('.frozenCount').first().val(0);
        } else {
            $(btn).addClass('selected')
            $(infoWrap).show(100)
        }
    }

    function deleteSchedule() {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: 'schedule',
                    id: <?php echo $schedule['id'] ?>
                },
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        updateMealSchedule($('#schedule_id').val());
                        customModalClear();
                        $('#mealScheduleCalendarWrap').html('')
                    } else {
                        customMessage(response, 3)
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }
    }
</script>
<style>
    #newScheduleForm {
        width: 95%;
        margin: 0px auto 50px;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .daySelectWrap {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .daySelectItem {
        border: 1px solid #c6c6c6;
        border-radius: 5px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 150px;
    }

    .daySelectArea {
        cursor: pointer;
        border-radius: 5px;
        transition: all 200ms ease-in-out;
        padding: 10px;
        width: 100%;
        text-align: center;
    }

    .daySelectArea.selected {
        background-color: var(--goodGreen);
    }

    .mealSelectCountWrap {
        margin-top: 30px;
        display: none;
        width: 100%;
    }

    .mealSelectCountWrap div label {
        display: block;
    }

    .hotCount {
        width: 90px;
    }

    .frozenCount {
        width: 90px;
    }
</style>