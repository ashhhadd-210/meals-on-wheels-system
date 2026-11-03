<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_POST['recip_id']])[0];
?>
<div id="scheduleChangeWrap">

</div>
<script>
    $(document).ready(() => {
        fillScheduleChanges();
    })

    function deleteRouteChange(id, btn) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: 'routeChange',
                    id: id
                },
                success: (re) => {
                    if (re.trim() == 'ok') {
                        $(btn).parent().remove()
                    } else {
                        customMessage(re, 3)
                    }
                }
            })
        }
    }

    function fillScheduleChanges() {
        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillScheduleChanges.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>
            },
            success: (re) => {
                $('#scheduleChangeWrap').html(re)
            }
        })
    }

    function addRouteChange() {
        $.ajax({
            type: 'POST',
            url: 'menu/addContentForms/newRouteChange.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>
            },
            success: (re) => {
                $('#scheduleChangeWrap').html(re)
            }
        })
    }
</script>
<style>
    #changeList {
        display: flex;
        flex-direction: column;
        padding: 20px;
        gap: 20px;
    }
</style>