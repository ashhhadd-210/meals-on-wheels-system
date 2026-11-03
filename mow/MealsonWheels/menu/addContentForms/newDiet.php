<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm" autocomplete="off">
    <h2>New Diet</h2>
    <input type="text" value="diet" name="contentType" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="dietName">Name</label>
            <input type="text" name="dietName" id="dietName" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="dietAbbre">Abbreviation</label>
            <input type="text" name="dietAbbre" id="dietAbbre" required>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="descr">Description</label>
            <input type="text" name="descr" id="descr" required>
        </div>
    </div>
    <button type="submit">Add</button>
</form>
<script>
    $(document).ready(function() {
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way;
            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
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