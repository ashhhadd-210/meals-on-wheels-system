<?php
$route = $db->select('SELECT * FROM  routes WHERE id = ?', [$security->sanitizeInput($_POST['id'])])[0];
$_SESSION['editing_route_id'] = $route['id'];
$levelToEdit = 3;
?>
<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm" autocomplete="off">
<div class="formRow">
        <div class="inputRow" style="width:100%">
            <label for="positions">Positions</label>
            <small><i>Drag to sort</i></small>
            <input type="text" name="positions" id="positions" style="display: none;" readonly>

            <?php
            $recipients = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition', [$route['id'],1]);
            if ($recipients != false) {
                echo '<div id="routePositionOrder">';
                $index = 1;
                foreach ($recipients as $recip) {
                    echo '<div id="' . $recip['id'] . '" class="recipentPositionItem">' . $recip['firstName'] .  ' ' . $recip['lastName'] . ' ('.$index.')' . '</div>';
                    $index++;
                }
                echo '</div>';
            } else {
                echo '<div>No Recipients on this route.</div>';
            }
            ?>

        </div>
    </div>
    <input type="text" value="route" name="contentType" style="display: none;">
    <?php
    if($user['permissionLevel'] >= $levelToEdit){
        echo '
        <div class="formRow">
        <div class="inputRow">
        <label for="routeName">Route Name</label>
        <input value="'.$route['name'].'" type="text" name="routeName" id="routeName">
        </div>
        </div>
        <div class="formRow">
        <div class="inputRow">
        <label for="color">Color</label>
        <input value="'.$route['color'].'" type="color" name="color" id="color">
        </div>
        </div>
        <div class="formRow">
        <div class="inputRow">
        <label for="vendor">Vendor</label>
        <input value="'.$route['vendor'].'" type="text" name="vendor" id="vendor">
        </div>
        </div>';

    }

    ?>
    
    <button class="routeUpdateBtn" type="submit" name="addContentBtn">Update</button>
</form>
<script>
    $(document).ready(function() {
        $('#routePositionOrder').sortable({
            start: function(event, ui) {
                ui.placeholder.height(ui.item.height());
            },
            cursorAt: {
                top: 50,
                left: 50
            },
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                var str = '';
                $('.recipentPositionItem').each(function(index, element) {
                    var delimit = ',';
                    if (index == 0) {
                        delimit = '';
                    }
                    str += delimit + $(element).prop('id') + '-' + (index + 1);
                })
                $('#positions').val(str)
            }
        })
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        customMessage('Success', 1);
                        fillRouteData();
                        // $('#displayContentWrap').fadeOut(100, function() {
                        //     $('#displayContentWrap').html('');
                        //     $('#displayContentWrap').fadeIn(100)
                        // })
                        editContent(null,'route',<?php echo $route['id'] ?>)
                    } else {
                        customMessage(response, 3, false, false);
                    }

                },
                error: function(xhr, status, error) {
                    // Handle errors
                    $('#result').html('An error occurred: ' + error);
                }
            });
        });
    });
</script>
<style>
    .routeUpdateBtn {
        position: sticky;
        bottom: 20px;
        background-color: white;
    }
    .routeUpdateBtn:hover,
    .routeUpdateBtn:active {
        background-color: var(--blue) !important;
    }
</style>