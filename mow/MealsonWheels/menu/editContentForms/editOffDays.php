<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<div class="offDaysWrap">
   <h2>Off Times - <?php echo $recip['firstName'] . ' ' . $recip['lastName'] ?></h2> 
    
    <div id="offDaysContent">

    </div>
    <button style="margin-top: auto;" type="button" onclick="saveUpdateOffDays()">Update</button>
</div>
<script>
    function editAOffDay(id){
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/editOffDay.php',
            data: {
                id: id
            },
            success: function(response) {
                $('#offDaysContent').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }
    function addOffDay(){
        $.ajax({
            type: 'POST',
            url: 'menu/addContentForms/newOffDay.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>
            },
            success: function(response) {
                $('#offDaysContent').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }
    $(document).ready(function() {
        fillOffDays()
    })

    function fillOffDays(toggle) {
        if(toggle == null || toggle == ''){
            toggle = 'less';
        }

        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillOffDays.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>,
                show: toggle
            },
            success: function(response) {
                $('#offDaysContent').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }
</script>
<style>
.offDaysWrap {
	margin: 20px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}
.offDayListWrap {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
.offDayRow {
	border: 1px solid #c6c6c6;
	padding: 10px;
	border-radius: 5px;
	cursor: pointer;
	transition: all 200ms ease-in-out;
}
.offDayRow:hover {
    background-color: #c6c6c6;
}
.offDaysHeader {
	margin-bottom: 20px;
}
.offDayRow.deavtive {
	background-color: #ffc5c5;
}
</style>