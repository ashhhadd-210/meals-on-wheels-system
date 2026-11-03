<div class="insertContentBody">
    <div class="contentListWrap">
        <h2>Recipients</h2>
        <div class="contentHead">
            <div class="searchWrap">
                <input type="text" id="searchRecipient" placeholder="Search By Name">
                <button type="button" id="clearRecipSearch" onclick="clearRecipSearch()">Clear</button>
            </div>

            
            <select id="statusSelect">
                <?php
                if(!isset($_SESSION['statusFilter'])){
                    $_SESSION['statusFilter'] = 1;
                }
                $statuses = $db->select('SELECT * FROM clientStatus');
                foreach($statuses as $stat){
                    $selected = ($_SESSION['statusFilter'] == $stat['id']) ? ' selected' : '';
                    echo '<option value="'.$stat['id'].'"'.$selected.'>'.$stat['name'].'</option>';
                }
                echo '<option value="all"'.(($_SESSION['statusFilter'] == 'all') ? ' selected' : '').'>All</option>';
                ?>
            </select>
            <select id="recipAmount">
                <?php
                if(!isset($_SESSION['recipAmount'])){
                    $_SESSION['recipAmount'] = 100;
                }
                $amounts = [25,50,100,150,200,0];
                foreach($amounts as $amts){
                    $selected = ($amts == $_SESSION['recipAmount']) ? ' selected' : '';
                    $amtText  = ($amts == 0) ? 'All' : (string)$amts;
                    echo '<option value="'.$amts.'"'.$selected.'>'.$amtText.'</option>';
                }
                ?>
            </select>
            <button type="button" class="addConentBtn" onclick="addContent('recipient')">New</button>
        </div>
        <div class="contentList" id="contentList">
        </div>
    </div>
    <div id="displayContentWrap">

    </div>
</div>

<script>
    $(document).ready(()=>{
        $('#statusSelect, #recipAmount').on('change',(e)=>{
            fillRecipientData();
        })

        $('#searchRecipient').on('input',()=>{
            fillRecipientData();
        })
        fillRecipientData();

        <?php
        // if(isset($_SESSION['redirect'])){
        //     echo 'editContent(null, recipient, '.$_SESSION['redirect'].')';
        //     unset($_SESSION['redirect']);
        // }
        ?>
    })
    var request = null;
    function fillRecipientData() {
        if(request != null){
            request.abort();
        }
        request = $.ajax({
            type: 'POST',
            url: 'menu/getData/fillRecipientData.php',
            data: {
                status: $('#statusSelect').val(),
                amount: $('#recipAmount').val(),
                textFilter: $('#searchRecipient').val().trim()
            },
            success: function(response) {
                request = null;
                $('#contentList').html(response);
            },
            error: function(error) {
                request = null;
                $('#contentList').html(error);
            }
        })
    }

    // function searchRecipients(input) {
    //     value = input.value.toLowerCase().trim();
    //     var rows = $('.contentRow');
    //     if (value != '') {
    //         rows.each(function(index, element) {
    //             if ($(element).text().toLowerCase().includes(value)) {
    //                 $(element).show();
    //             } else {
    //                 $(element).hide();
    //             }
    //         })
    //     } else {
    //         $('.contentRow').show();
    //     }
    // }

    function clearRecipSearch(){
        $('#searchRecipient').val('');
        fillRecipientData();
    }

    
</script>