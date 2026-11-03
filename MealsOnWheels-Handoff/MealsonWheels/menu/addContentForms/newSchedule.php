<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
?>

<form action="" id="newScheduleForm">
    <input name="newSchedule_recip_id" id="newSchedule_recip_id" type="text" value="<?php echo $_POST['recip_id'] ?>" readonly hidden>
    <h3>New Schedule</h3>
    <div class="formRow">
        <div class="inputRow">
            <label for="scheduleType">Schedule Type</label>
            <select name="scheduleType" id="scheduleType" required>
                <option value="" selected hidden>Select</option>
                <option value="1">Default (System-wide)</option>
                <option value="0">Alternative (This recipient only)</option>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="scheduleStartDate">Start Date</label>
            <input type="date" name="scheduleStartDate" id="scheduleStartDate" required>
        </div>
        <div class="inputRow" id="endDateRow">
            <label for="scheduleEndDate">End Date</label>
            <input type="date" name="scheduleEndDate" id="scheduleEndDate" required>
        </div>
        <div class="inputRow" style="align-items: baseline">
            <label for="indefinite">Indefinite</label>
            <input type="checkbox" name="indefinite" id="indefinite" <?php echo $inf ?>>
        </div>
    </div>
    <script>
        $(document).ready(() => {
            $('#indefinite').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#endDateRow').hide(200)
                    $('#scheduleEndDate').prop('required', false)
                } else {
                    $('#endDateRow').show(200)
                    $('#scheduleEndDate').prop('required', true)
                }
            })
        })
    </script>
    <div class="formRow">
        <p><i>The newest 'Main Schedule' will override all other Main Schedules upon it's start date. A 'Personal Schedule' will override all 'Main Schedules' and the newest will override all other Personal Schedules upon it's start date.</i></p>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="">Select Days</label>
            <div class="daySelectWrap">

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Monday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Tuesday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Wednesday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Thursday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Friday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Saturday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>

                <div class="daySelectItem">
                    <div class="daySelectArea" onclick="selectDay(this)">Sunday</div>
                    <div class="mealSelectCountWrap">
                        <div>
                            <label for="">Default Hot</label>
                            <input min="0" value="0" type="number" class="hotCount">
                        </div>
                        <div>
                            <label for="">Default Frozen</label>
                            <input min="0" value="0" type="number" class="frozenCount">
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Add</button>
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
                url: 'menu/handlers/addContentHandler.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    response = response.trim();
                    if (response == 'ok') {
                        customModalClear();
                        updateMealSchedule();
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