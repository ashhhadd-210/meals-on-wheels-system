<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm">
    <h2>New Contact</h2>
    <input type="text" value="route" name="contentType" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="routeName">Route Name</label>
            <input type="text" name="routeName" id="routeName">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="color">Color</label>
            <input type="color" name="color" id="color">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="vendor">Vendor</label>
            <input type="text" name="vendor" id="vendor">
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
                        fillRouteData();
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