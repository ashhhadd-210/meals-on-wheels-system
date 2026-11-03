<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}
?>
<h1>Individual Recipient</h1>
<form action="" id="reportForm">
    <input type="text" name="reportType" value="individual-recipient" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="recipient_id">Recipient</label>
            <div class="recipientSelect">
                <input type="text" name="recipient_id" id="recipient_id" style="display: none;">
                <input type="text" id="recipenetSearchInput" placeholder="Search Recipient">
                <div id="recipientSearchResults" style="display: none;"></div>
                <div id="recipNameDisplay" style="display: none;"></div>
            </div>
        </div>
    </div>
    <h3>Includes</h3>
    <div class="formRow">
        <label for="activeStatus"><input type="checkbox" name="activeStatus" id="activeStatus"> Current Active Status w/ date</label>
        <label for="address"><input type="checkbox" name="address" id="address"> Address</label>
        <label for="contactInfo"><input type="checkbox" name="contactInfo" id="contactInfo"> Contact Information</label>
        <label for="extra"><input type="checkbox" name="extra" id="extra"> Gender/DOB/Ethnicity/Marital Status</label>

    </div>
    <div class="formRow">
        <label for="contacts"><input type="checkbox" name="contacts" id="contacts"> Contacts</label>
        <label for="diets"><input type="checkbox" name="diets" id="diets"> Dietary Info</label>
        <label for="billing"><input type="checkbox" name="billing" id="billing"> Billing Info</label>
        <label for="quickNotes"><input type="checkbox" name="quickNotes" id="quickNotes"> Quick Notes</label>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <button type="submit">Generate</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(() => {

        $('#reportForm').on('submit', (e) => {
            e.preventDefault();
            if ($('#recipient_id').val().trim() == '') {
                customMessage('Select a recipient', 3)
                return false;
            }
            var action = '/viewReport';
            var data = $('#reportForm').serialize();
            window.open(action + '?' + data, '_blank');
        })

        $('#recipenetSearchInput').on('input', (e) => {
            if ($('#recipenetSearchInput').val().trim() == '') {
                $('#recipientSearchResults').hide()
                $('#recipientSearchResults').html('')
            } else {
                $('#recipientSearchResults').show()
            }
            sendSearch()
        })
        $('#recipenetSearchInput').on('focus', (e) => {
            if ($('#recipenetSearchInput').val().trim() != '') {
                sendSearch()
                $('#recipientSearchResults').show()
            }
        })
        // $('#recipenetSearchInput').on('blur', () => {
        //     $('#recipientSearchResults').html('');
        //     $('#recipientSearchResults').hide()
        // })

        function sendSearch() {
            $.ajax({
                type: 'POST',
                url: 'menu/getData/fillRecipientSearch.php',
                data: {
                    value: $('#recipenetSearchInput').val().trim()
                },
                success: (re) => {
                    $('#recipientSearchResults').html(re)
                }
            })
        }
    })

    function selectRecipSearch(id, name) {
        $('#recipient_id').val(id);
        $('#recipNameDisplay').text(name);
        $('#recipNameDisplay').show();
        $('#recipientSearchResults').html('');
        $('#recipientSearchResults').hide()
        $('#recipenetSearchInput').val('');
    }
</script>