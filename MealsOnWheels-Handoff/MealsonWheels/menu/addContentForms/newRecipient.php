<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm" autocomplete="off">
    <h2>New Recipient</h2>
    <input type="text" value="recipient" name="contentType" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="fistName" required autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="middleName">Middle Name</label>
            <input type="text" name="middleName" id="middleName" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" required autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="suffix">Suffix</label>
            <input style="width: 100px;" type="text" name="suffix" id="suffix" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="nickName">Nick Name</label>
            <input type="text" name="nickName" id="nickName" autocomplete="off">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="gender">Gender</label>
            <select name="gender" id="gender">
                <?php
                $genders = $db->select('SELECT * FROM genders');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($genders as $gender) {
                    echo '<option value="' . $gender['id'] . '">' . $gender['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="dob">Date of Birth</label>
            <input type="text" name="dob" class="customDate" required>
        </div>
        <div class="inputRow">
            <label for="maritalStatus">Marital Status</label>
            <select name="maritalStatus" id="maritalStatus">
                <?php
                $statuses = $db->select('SELECT * FROM maritalStatus');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($statuses as $status) {
                    echo '<option value="' . $status['id'] . '">' . $status['name'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="homePhone">Home Phone</label>
            <input type="tel" name="homePhone" id="homePhone" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="cellPhone">Cell Phone</label>
            <input type="tel" name="cellPhone" id="cellPhone" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" autocomplete="off">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="addressLine1">Address Line 1</label>
            <input type="text" name="addressLine1" id="addressLine1" placeholder="House/Building, Street Name" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="addressLine2">Address Line 2</label>
            <input type="text" name="addressLine2" id="addressLine2" placeholder="Apt, Suite, Room, Ect" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="city">City</label>
            <input type="text" name="city" id="city" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="state">State</label>
            <select name="state" id="state" class="stateSelector"></select>
        </div>
        <script>
            alterCC()
        </script>
        <div class="inputRow">
            <label for="zip">Zip</label>
            <input type="text" name="zip" id="zip" style="width: 100px;" autocomplete="off">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="ethnicGroup">Ethnic Group</label>
            <select name="ethnicGroup" id="ethnicGroup">
                <?php
                $groups = $db->select('SELECT * FROM ethnicGroups');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($groups as $group) {
                    echo '<option value="' . $group['id'] . '">' . $group['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="clientStatus">Current Client Status</label>
            <select name="clientStatus" id="clientStatus">
                <?php
                $clientStatuses = $db->select('SELECT * FROM clientStatus');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($clientStatuses as $clientStatus) {
                    echo '<option value="' . $clientStatus['id'] . '">' . $clientStatus['name'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="physician_id">Physician</label>
            <select name="physician_id" id="physician_id">
                <?php
                $physicians = $db->select('SELECT * FROM physicians');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($physicians as $physician) {
                    echo '<option value="' . $physician['id'] . '">' . $physician['firstName'] . ' ' . $physician['lastName'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="clinic_id">Physician Clinic</label>
            <select name="clinic_id" id="clinic_id">
                <?php
                $clinics = $db->select('SELECT * FROM clinics');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($clinics as $clinic) {
                    echo '<option value="' . $clinic['id'] . '">' . $clinic['name'] .  '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="certStartDate">Cert Start Date</label>
            <input type="text" name="certStartDate" id="certStartDate" autocomplete="off" class="customDate">
        </div>
        <div class="inputRow">
            <label for="certEndDate">Cert End Date</label>
            <input type="text" name="certEndDate" id="certEndDate" autocomplete="off" class="customDate">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="notes">Quick Notes</label>
            <textarea name="notes" id="notes"></textarea>
            <script>
                var globalEditor;
                ClassicEditor
                    .create(document.querySelector('#notes'), {
                        mediaEmbed: {
                            previewsInData: true
                        }
                    })
                    .then(editor => {
                        globalEditor2 = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            </script>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="referredBy">Referred By</label>
            <input type="text" name="referredBy" id="referredBy" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="referralReason">Referral Reason</label>
            <input type="text" name="referralReason" id="referralReason" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="referralDate">Referral Date</label>
            <input type="text" name="referralDate" id="referralDate" class="customDate">
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="route">Route</label>
            <select name="route" id="route">
                <?php
                $routes = $db->select('SELECT * FROM routes');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($routes as $route) {
                    echo '<option value="' . $route['id'] . '">' . $route['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <!-- <div class="inputRow">
            <label for="routePosition">Route Position</label>
            <input type="number" name="routePosition" id="routePosition" style="width: 100px;" min="0">
        </div> -->
        <div class="inputRow" style="width:100%">
            <label for="routeNote">Route Note</label>
            <textarea name="routeNote" id="routeNote"></textarea>
            <script>
                var globalEditor2;
                ClassicEditor
                    .create(document.querySelector('#routeNote'), {
                        mediaEmbed: {
                            previewsInData: true
                        }
                    })
                    .then(editor => {
                        globalEditor2 = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            </script>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="relationship">Spouse/Partner</label>
            <select name="relationship" id="relationship">
                <?php
                $recipients = $db->select('SELECT id,firstName,lastName FROM recipients');
                echo '<option value="" selected hidden>Select</option>';
                echo '<option value="">None</option>';
                foreach ($recipients as $recipient) {
                    echo '<option value="' . $recipient['id'] . '">' . $recipient['lastName'] . ', ' . $recipient['firstName'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="">Contacts (Add to continue)</label>
        </div>
    </div>
    <button type="submit" name="addContentBtn">Add</button>
</form>
<script>
    $(document).ready(function() {
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        customMessage('Success', 1, false, true);
                        fillRecipientData();
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