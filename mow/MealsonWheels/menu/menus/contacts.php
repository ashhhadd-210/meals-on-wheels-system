<div class="insertContentBody">
    <div class="contentListWrap">
        <h2>Contacts</h2>
        <div class="contentHead">
            <div class="searchWrap">
                <input oninput="searchRecipients(this)" type="text" id="searchRecipient" placeholder="Search By Name">
                <button type="button" id="clearRecipSearch" onclick="clearRecipSearch()">Clear</button>
            </div>

            <button type="button" class="addConentBtn" onclick="addContent('contact')">New</button>
        </div>
        <div class="contentList" id="contentList">
        </div>
    </div>
    <div id="displayContentWrap">

    </div>
</div>

<script>
    
        <?php
        if(isset($_GET['l']) && $_GET['l'] == 'contacts'){
            echo "$('document').ready(function(){";
            echo "addContent('contact'); ";
            echo 'alert()';
            echo '})';
            unset($_GET['l']);
        }
        
        ?>
    
    function fillContactData() {
        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillContactData.php',
            success: function(response) {
                $('#contentList').html(response);
            },
            error: function(error) {
                $('#contentList').html(error);
            }
        })
    }

    function searchRecipients(input) {
        value = input.value.toLowerCase().trim();
        var rows = $('.contentRow');
        if (value != '') {
            rows.each(function(index, element) {
                if ($(element).text().toLowerCase().includes(value)) {
                    $(element).show();
                } else {
                    $(element).hide();
                }
            })
        } else {
            $('.contentRow').show();
        }
    }

    function clearRecipSearch(){
        $('#searchRecipient').val('')
        $('.contentRow').show();
    }

    fillContactData()
</script>