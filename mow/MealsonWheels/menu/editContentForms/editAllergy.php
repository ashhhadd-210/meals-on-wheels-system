<?php
$diet = $db->select('SELECT * FROM  allergies WHERE id = ?', [$security->sanitizeInput($_POST['id'])])[0];
$_SESSION['editing_allergy_id'] = $diet['id'];
$levelToEdit = 3;
?>
<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm" autocomplete="off">
    <input type="text" value="allergies" name="contentType" style="display: none;">
    <?php
    if($user['permissionLevel'] >= $levelToEdit){
        echo '
        <div class="formRow">
        <div class="inputRow">
        <label for="dietName">Name</label>
        <input value="'.$diet['name'].'" type="text" name="dietName" id="dietName">
        </div>
        </div>
        <div class="formRow">
        <div class="inputRow">
        <label for="dietAbbre">Abbreviation</label>
        <input value="'.$diet['abbr'].'" type="text" name="dietAbbre" id="dietAbbre">
        </div>
        </div>
        <div class="formRow">
        <div class="inputRow">
        <label for="descr">Description</label>
        <input value="'.$diet['description'].'" type="descr" name="descr" id="descr">
        </div>
        </div>';

    }

    ?>
    
    <button type="submit" name="addContentBtn">Update</button>
    <button type="button" class="deleteBtn" onclick="deleteContent('allergies',<?php echo $diet['id']?>)">Delete</button>
</form>
<script>
    $(document).ready(function() {
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        customMessage('Success', 1, false, true);
                        fillDietaryRows('');
                        $('#displayContentWrap').fadeOut(100, function() {
                            $('#displayContentWrap').html('');
                            $('#displayContentWrap').fadeIn(100)
                        })
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