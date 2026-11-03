<?php
$recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$security->sanitizeInput($_POST['id'])])[0];
// exit('Access Error');
$_SESSION['last_recip'] = $recip['id'];
$_SESSION['editing_recip_id'] = $recip['id'];
?>
<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm edit">
    <input type="text" value="recipient" name="contentType" style="display: none;">

    <?php
    $titleStr = $recip['firstName'] . ' ' . $recip['lastName'];
    if (!empty($recip['homePhone'])) {
        $titleStr .= ' - ' . formatPhone($recip['homePhone']);
    } else {
        if (!empty($recip['cellPhone'])) {
            $titleStr .= ' - ' . formatPhone($recip['cellPhone']);
        }
    }
    if (!empty($recip['addressLine1'])) {
        $titleStr .= ' - ' . $recip['addressLine1'];
    }
    if (!empty($recip['addressLine2'])) {
        $titleStr .=  ' ' . $recip['addressLine2'];
    }
    $titleStr .= ' ' . $recip['city'];
    ?>
    <h2 class="recipTitle"><?php echo $titleStr ?></h2>
    <div class="secBtnTogBtn" onclick="toggleSection('scheduling',this)">Scheduling</div>
    <div class="formRow" id="scheduling" style="display: none; flex-direction: column">
        <div class="inputRow"><button type="button" class="addConentBtn " onclick="editOffDays()">Edit Off Days</button></div>
        <div class="inputRow">
            <div id="offDaySchedule">

            </div>
            <script>
                $(document).ready(() => {
                    fillOffDaySchedule()
                })

                function saveUpdateOffDays() {
                    updateOffDayCalendar();
                    customModalClear()
                }

                function fillOffDaySchedule() {
                    $.ajax({
                        type: 'POST',
                        url: 'menu/getData/offDaySchedule.php',
                        data: {
                            recip_id: <?php echo $recip['id'] ?>
                        },
                        success: (re) => {
                            $('#offDaySchedule').html(re)
                        }
                    })
                }

                function updateOffDayCalendar() {
                    $.ajax({
                        type: 'POST',
                        url: 'menu/getData/offDaySchedule.php',
                        data: {
                            recip_id: <?php echo $recip['id'] ?>,
                            mY: $('#offDayYearSelect').val() + '-' + $('#offDayMonthSelect').val() + '-01'
                        },
                        success: (re) => {
                            $('#offDaySchedule').html(re)
                        }
                    })
                }
            </script>
        </div>
    </div>
    <div class="secBtnTogBtn" onclick="toggleSection('noteInfo',this)">Notes</div>
    <div class="formRow" id="noteInfo" style="display: none;">
        <div class="inputRow" style="width: 100%;">
            <label for="notes">Quick Notes</label>
            <textarea name="notes" id="notes" style="height: 200px;width:100%"><?php echo $recip['notes'] ?></textarea>
            <script>
                var globalEditor2;
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
        <hr>
        <label for="">New Note</label>
        <div class="newNoteWrap">

            <input type="text" id="newNoteText" placeholder="Description">
            <div><label for="">Follow Up</label><input type="checkbox" id="newNoteFollowUp"></div>
            <div id="followUpDateWrap" style="display: none;"><label for="">Follow Up Date</label><input type="date" id="newNoteFollowUpDate"></div>
            <div><label for="">Emergency Contacts called</label><input type="checkbox" id="newNoteECC"></div>
            <div><label for="">EMS called</label><input type="checkbox" id="newNoteEMS"></div>
            <div><label for="">Referral Made</label><input type="checkbox" id="newNoteReferral"></div>
            <button type="button" onclick="addNewNoteLog()" class="addConentBtn">Add</button>
        </div>
        <div class="inputRow" style="width: 100%;">
            <label for="">Note Log</label>
            <div class="noteLogWrap" id="noteLogWrap">
                <?php
                $noteLogs = $db->select('SELECT * FROM noteLogs WHERE recipient_id = ? ORDER BY dateStamp DESC', [$recip['id']]);
                include($root . '/menu/includes/noteLogAreaTemplate.php');

                ?>
            </div>
        </div>
    </div>
    <script>
        function fillnoteLog() {
            $.ajax({
                type: 'POST',
                url: 'menu/includes/noteLogAreaTemplate.php',
                data: {
                    noteLogDataNeeded: 'true'
                },
                success: function(response) {
                    $('#noteLogWrap').html(response)
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }
    </script>

    <div class="secBtnTogBtn" onclick="toggleSection('contactInfoInsert',this)">Contacts</div>
    <div class="formRow" id="contactInfoInsert" style="display: none;">
        <div class="inputRow">
            <label for="">Contacts (Link through contacts)</label>
            <?php
            $contacts = $db->select('SELECT * FROM contact_link WHERE recipient_id = ?', [$recip['id']]);
            if ($contacts != false) {

                foreach ($contacts as $contact) {
                    $contactQuery = $db->select('SELECT * FROM contacts WHERE id = ?', [$contact['contact_id']])[0];
                    $relationship = $db->select('SELECT * FROM relationships WHERE id = ?', [$contact['relationship_id']])[0];
                    $phone = (empty($contactQuery['homePhone'])) ? $contactQuery['cellPhone'] : $contactQuery['homePhone'];
                    echo '<div onclick="viewContact(' . $contact['contact_id'] . ')" class="recipContactRow" style="display:flex;gap:20px;align-items:center">';
                    echo '<span>' . $contactQuery['firstName'] . ' ' . $contactQuery['lastName'] . ' - ' . $relationship['name'] . ' - ' . formatPhone($phone) . '</span>';
                    $emergency = ($contact['emergencyContact'] == 1) ? 'Emergency' : '';
                    echo '<span>' . $emergency . '</span>';
                    $poa = ($contact['poa'] == 1) ? 'POA' : '';
                    echo '<span>' . $poa . '</span>';
                    $financial = ($contact['financial'] == 1) ? 'Financial Statment Reciever' : '';
                    echo '<span>' . $financial . '</span>';
                    echo '</div>';
                }
                // echo '<a class="addConentBtn" style="cursor:pointer" onclick="goToContact(' . $recip['id'] . ')">Add</a>';
            } else {
                echo '<div style="display:flex;gap:20px;align-items:center">';
                echo '<span>No Contacts</span>';
                // echo '<a class="addConentBtn" style="cursor:pointer" onclick="goToContact(' . $recip['id'] . ')">Add</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="secBtnTogBtn" onclick="toggleSection('diet',this)">Diet</div>
    <div class="formRow" id="diet" style="display: none; flex-direction: column">
        <div class="dietFlexWrap">
            <div class="inputRow">
                <label for="recip_diets[]">Diets</label>
                <?php
                $dietMatrix = explode(',', $recip['diets']);
                $dietOptions = $db->select('SELECT * FROM diets ORDER BY name');
                foreach ($dietOptions as $option) {
                    echo '<label>';
                    echo '<input type="checkbox" name="recip_diets[]" value="' . $option['id'] . '"' . ((in_array($option['id'], $dietMatrix)) ? ' checked' : '') . '>';
                    echo $option['name'] . '<br><div class="dietDesc">' . $option['description'] . '</div>';
                    echo '</label>';
                }
                ?>

            </div>


            <div class="inputRow">
                <label for="recip_allergies[]">Allergies</label>
                <?php
                $allergyMatrix = explode(',', $recip['allergies']);
                $allergyOptions = $db->select('SELECT * FROM allergies ORDER BY name');
                foreach ($allergyOptions as $option) {
                    echo '<label>';
                    echo '<input type="checkbox" name="recip_allergies[]" value="' . $option['id'] . '"' . ((in_array($option['id'], $allergyMatrix)) ? ' checked' : '') . '>';
                    echo $option['name'] . '<br><div class="dietDesc">' . $option['description'] . '</div>';
                    echo '</label>';
                }
                ?>

            </div>

            <div class="inputRow">
                <label for="recip_accommodations[]">Accommodations</label>
                <?php
                $accommodationsMatrix = explode(',', $recip['accommodations']);
                $accommodationsOptions = $db->select('SELECT * FROM accommodations ORDER BY name');
                foreach ($accommodationsOptions as $option) {
                    echo '<label>';
                    echo '<input type="checkbox" name="recip_accommodations[]" value="' . $option['id'] . '"' . ((in_array($option['id'], $accommodationsMatrix)) ? ' checked' : '') . '>';
                    echo $option['name'] . '<br><div class="dietDesc">' . $option['description'] . '</div>';
                    echo '</label>';
                }
                ?>

            </div>
            <div class="inputRow">
                <label for="recip_hot_milk_id">Hot Meal Milk Choice</label>
                <select name="recip_hot_milk_id" id="recip_hot_milk_id">
                    <option value="">None</option>
                    <?php
                    $milkOptions = $db->select('SELECT * FROM milkChoices ORDER BY name');
                    foreach ($milkOptions as $option) {
                        echo '<option value="' . $option['id'] . '"' . (($recip['hotMilk_id'] == $option['id']) ? ' selected' : '') . '>' . $option['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="inputRow">
                <label for="recip_frozen_milk_id">Frozen Meal Milk Choice</label>
                <select name="recip_frozen_milk_id" id="recip_frozen_milk_id">
                    <option value="">None</option>
                    <?php
                    $milkOptions = $db->select('SELECT * FROM milkChoices ORDER BY name');
                    foreach ($milkOptions as $option) {
                        echo '<option value="' . $option['id'] . '"' . (($recip['frozenMilk_id'] == $option['id']) ? ' selected' : '') . '>' . $option['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>


    <div class="secBtnTogBtn" onclick="toggleSection('basicInfo',this)">Info</div>
    <div class="formRow" id="basicInfo" style="display: none;">
        <div class="inputRow">
            <label for="firstName">First Name</label>
            <input value="<?php echo $recip['firstName'] ?>" type="text" name="firstName" id="fistName" required autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="middleName">Middle Name</label>
            <input value="<?php echo $recip['middleName'] ?>" type="text" name="middleName" id="middleName" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="lastName">Last Name</label>
            <input value="<?php echo $recip['lastName'] ?>" type="text" name="lastName" id="lastName" required autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="suffix">Suffix</label>
            <input value="<?php echo $recip['suffix'] ?>" style="width: 100px;" type="text" name="suffix" id="suffix" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="nickName">Nick Name</label>
            <input value="<?php echo $recip['nickName'] ?>" type="text" name="nickName" id="nickName" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="gender">Gender</label>
            <select name="gender" id="gender">
                <?php
                $genders = $db->select('SELECT * FROM genders');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($genders as $gender) {
                    $select = ($recip['gender_id'] == $gender['id']) ? ' selected' : '';
                    echo '<option value="' . $gender['id'] . '"' . $select . '>' . $gender['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="dob">Date of Birth</label>
            <input value="<?php echo ((empty($recip['dob'])) ? '' : date('m/d/Y', strtotime($recip['dob']))) ?>" style="width:120px" type="text" name="dob" placeholder="MM/DD/YYYY">
        </div>
        <div class="inputRow">
            <label for="maritalStatus">Marital Status</label>
            <select name="maritalStatus" id="maritalStatus">
                <?php
                $statuses = $db->select('SELECT * FROM maritalStatus');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($statuses as $status) {
                    $select = ($recip['maritalStatus_id'] == $status['id']) ? ' selected' : '';
                    echo '<option value="' . $status['id'] . '"' . $select . '>' . $status['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="ethnicGroup">Ethnic Group</label>
            <select name="ethnicGroup" id="ethnicGroup">
                <?php
                $groups = $db->select('SELECT * FROM ethnicGroups');
                echo '<option value="" selected hidden>Select</option>';
                echo '<option value="">None</option>';
                foreach ($groups as $group) {
                    $select = ($recip['ethnicGroup_id'] == $group['id']) ? ' selected' : '';
                    echo '<option value="' . $group['id'] . '"' . $select . '>' . $group['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="relationship">Spouse/Partner</label>
            <select name="relationship" id="relationship">
                <?php
                $recipients = $db->select('SELECT id,firstName,lastName FROM recipients');
                echo '<option value="" selected hidden>Select</option>';
                echo '<option value="">None</option>';
                foreach ($recipients as $recipient) {
                    if ($recipient['id'] != $recip['id']) {
                        $select = ($recip['link'] == $recipient['id']) ? ' selected' : '';
                        echo '<option value="' . $recipient['id'] . '"' . $select . '>' . $recipient['lastName'] . ', ' . $recipient['firstName'] . '</option>';
                    }
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
                    $select = ($recip['clientStatus_id'] == $clientStatus['id']) ? ' selected' : '';
                    echo '<option value="' . $clientStatus['id'] . '"' . $select . '>' . $clientStatus['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="clientStatusLog">
            <p>Client Status Log</p>
            <div class="clientStatusLogList">
                <?php
                $clientLogs = $db->select('SELECT * FROM clientStatusLog WHERE recipient_id = ?', [$recip['id']]);
                foreach ($clientLogs as $log) {
                    echo '<div class="recipClientLogRow"><span>' . date('m/d/y', strtotime($log['logDate'])) . '</span><span>' . $log['currentStatus'] . '</span></div>';
                }
                ?>
            </div>
        </div>
        <div class="inputRow">
            <label for="homePhone">Home Phone</label>
            <input value="<?php echo $recip['homePhone'] ?>" type="tel" name="homePhone" id="homePhone" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="cellPhone">Cell Phone</label>
            <input value="<?php echo $recip['cellPhone'] ?>" type="tel" name="cellPhone" id="cellPhone" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="email">Email</label>
            <input value="<?php echo $recip['email'] ?>" type="email" name="email" id="email" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="addressLine1">Address Line 1</label>
            <input value="<?php echo $recip['addressLine1'] ?>" type="text" name="addressLine1" id="addressLine1" placeholder="House/Building, Street Name" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="addressLine2">Address Line 2</label>
            <input value="<?php echo $recip['addressLine2'] ?>" type="text" name="addressLine2" id="addressLine2" placeholder="Apt, Suite, Room, Ect" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="city">City</label>
            <input value="<?php echo $recip['city'] ?>" type="text" name="city" id="city" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="state">State</label>
            <select data-initial-state="<?php echo $recip['state'] ?>" name="state" id="state" class="stateSelector"></select>
        </div>
        <script>
            alterCC()
        </script>
        <div class="inputRow">
            <label for="zip">Zip</label>
            <input value="<?php echo $recip['zip'] ?>" type="text" name="zip" id="zip" style="width: 100px;" autocomplete="off">
        </div>
    </div>

    <div class="secBtnTogBtn" onclick="toggleSection('billing',this)">Billing</div>
    <div class="formRow" id="billing" style="display: none;">
        <div id="billingRow">
            <button type="button" class="addConentBtn" onclick="openBillingManager()">Manage Billing</button>
        </div>
        <div class="formRow" style="width: 100%;">
            <div class="inputRow">
                <label for="">Current Billing Method:</label>
                <?php
                $recentBilling = $db->select('SELECT * FROM billings WHERE recipient_id = ? ORDER BY id DESC',[$recip['id']])[0];
                if($recentBilling['billType_id'] == 1) {
                    echo '<div>Self</div>';
                } else if ($recentBilling['billType_id'] == 2){
                    echo '<div>Insurance: '.date('m/d/y',strtotime($recentBilling['startDate'])).' - '.date('m/d/y',strtotime($recentBilling['endDate'])).'</div>';
                } else if ($recentBilling['billType_id'] == 3) {
                    echo '<div>Contacts:<br></div>';
                    $billContactLinks = $db->select('SELECT * FROM contact_link WHERE recipient_id = ? AND financial = ?',[$recip['id'],1]);
                    if($billContactLinks == false){
                        echo '<div>No Financial Contacts</div>';
                    } else {
                        foreach($billContactLinks as $link){
                            $billContact = $db->select('SELECT * FROM contacts WHERE id = ?',[$link['contact_id']])[0];
                            echo '<div style="cursor:pointer" onclick="viewContact('.$billContact['id'].')"><b>'.$billContact['firstName'].' '.$billContact['lastName'].'</b></div>';
                        }
                    }
                }
                
                ?>
            </div>
        </div>
    </div>
    <script>
        function openBillingManager() {
            $.ajax({
                type: 'POST',
                url: 'menu/editContentForms/billingManager.php',
                data: {
                    recip_id: <?php echo $recip['id'] ?>,
                },
                success: (re) => {
                    newModal(re)
                }
            })
        }
    </script>
    <div class="secBtnTogBtn" onclick="toggleSection('assessments',this)">Assessments</div>
    <div class="formRow" id="assessments" style="display: none;">
        <div class="recipAssessmentWrap">
            <h3>Choose a new Assessment</h3>
            <div class="recipNewAssessWrap">
                <?php
                $assessments = $db->select('SELECT * FROM assessments WHERE hide = ? ORDER BY name ASC', [0]);
                if ($assessments != false) {
                    foreach ($assessments as $assess) {
                        echo '<div class="recipAssessRow" onclick="startNewAsessment(' . $assess['id'] . ')">' . $assess['name'] . '</div>';
                    }
                } else {
                    echo 'No assessments';
                }
                ?>
            </div>
            <h3>Completed Assessments</h3>
            <div id="recipPastAssessWrap"></div>
        </div>
    </div>
    <script>
        $(document).ready(() => {
            fillRecipAssessments()
        })

        function fillRecipAssessments() {
            $.ajax({
                type: 'POST',
                url: '/menu/getData/fillRecipAssessments.php',
                data: {
                    recip_id: <?php echo $recip['id'] ?>,
                },
                success: (re) => {
                    $('#recipPastAssessWrap').html(re)
                }
            })
        }

        function editRecipAssessment(id) {
            $.ajax({
                type: 'POST',
                url: '/menu/editContentForms/editRecipAssessment.php',
                data: {
                    link_id: id,
                },
                success: (re) => {
                    newModal(re)
                }
            })
        }

        function startNewAsessment(id) {
            $.ajax({
                type: 'POST',
                url: '/menu/addContentForms/newRecipAssessment.php',
                data: {
                    recip_id: <?php echo $recip['id'] ?>,
                    assess_id: id
                },
                success: (re) => {
                    newModal(re)
                }
            })
        }
    </script>
    <div class="secBtnTogBtn" onclick="toggleSection('healthInfo',this)">Health Info</div>
    <div class="formRow" id="healthInfo" style="display:none">
        <div class="inputRow">
            <label for="physician_id">Recipient Physician</label>
            <select name="physician_id" id="physician_id">
                <?php
                $physicians = $db->select('SELECT * FROM physicians ORDER BY firstName');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($physicians as $physician) {
                    $select = ($recip['physician_id'] == $physician['id']) ? ' selected' : '';
                    echo '<option value="' . $physician['id'] . '"' . $select . '>' . $physician['firstName'] . ' ' . $physician['lastName'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="clinic_id">Recipient Clinic</label>
            <select name="clinic_id" id="clinic_id">
                <?php
                $clinics = $db->select('SELECT * FROM clinics ORDER BY name');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($clinics as $clinic) {
                    $select = ($recip['clinic_id'] == $clinic['id']) ? ' selected' : '';
                    echo '<option value="' . $clinic['id'] . '"' . $select . '>' . $clinic['name'] .  '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="referralReason">Referral Reason</label>
            <input type="text" name="referralReason" autocomplete="off" value="<?php echo $recip['referralReason'] ?>">
        </div>
        <hr>
        <div class="certArea">
            <p>New Medical Certification</p>
            <div class="newCertWrap">
                <div class="inputRow">
                    <label for="certStartDate">Cert Start Date *</label>
                    <input type="date" id="certStartDate" autocomplete="off" placeholder="MM/DD/YYYY">
                </div>
                <div class="inputRow">
                    <label for="certEndDate">Cert End Date *</label>
                    <input type="date" id="certEndDate" autocomplete="off" placeholder="MM/DD/YYYY">
                </div>
                <div class="inputRow">
                    <label for="physician_id">Cert Physician *</label>
                    <select id="cert_physician_id">
                        <?php
                        $physicians = $db->select('SELECT * FROM physicians ORDER BY firstName');
                        echo '<option value="" selected hidden>Select</option>';
                        foreach ($physicians as $physician) {
                            echo '<option value="' . $physician['id'] . '">' . $physician['firstName'] . ' ' . $physician['lastName'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="inputRow">
                    <label for="cert_clinic_id">Cert Clinic</label>
                    <select id="cert_clinic_id">
                        <?php
                        $clinics = $db->select('SELECT * FROM clinics ORDER BY name');
                        echo '<option value="" selected hidden>Select</option>';
                        foreach ($clinics as $clinic) {
                            echo '<option value="' . $clinic['id'] . '">' . $clinic['name'] .  '</option>';
                        }
                        ?>
                    </select>
                </div>
                <!-- <div class="inputRow">
                    <label for="referredBy">Referred By</label>
                    <input type="text" id="referredBy" autocomplete="off">
                </div> -->

                <!-- <div class="inputRow">
                    <label for="referralDate">Referral Date</label>
                    <input type="date" id="referralDate" placeholder="MM/DD/YYYY">
                </div> -->
                <button type="button" class="addConentBtn" onclick="addCert(<?php echo $recip['id'] ?>)">Add</button>
            </div>
            <hr style="margin-top: 40px;">
            <p style="margin-bottom: 0px;">Certification Log</p>
            <div id="certLogsWrap"></div>
        </div>
        <script>
            function editCert(id) {
                $.ajax({
                    type: 'POST',
                    url: 'menu/editContentForms/editCert.php',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        newModal();
                        $('#modalBody').html($('#modalBody').html() + response);
                    }
                })
            }

            function addCert(id) {
                if ($('#certStartDate').val().trim() == '') {
                    customMessage('Enter a start date', 3)
                    return false;
                }
                if ($('#certEndDate').val().trim() == '') {
                    customMessage('Enter a end date', 3)
                    return false;
                }
                if ($('#cert_physician_id').val() == '') {
                    customMessage('Select a physician', 3)
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: 'menu/handlers/addContentHandler.php',
                    data: {
                        recipient_id: id,
                        contentType: 'cert',
                        startDate: $('#certStartDate').val(),
                        endDate: $('#certEndDate').val(),
                        physician_id: $('#cert_physician_id').val(),
                        clinic_id: $('#cert_clinic_id').val()
                    },
                    success: function(response) {
                        if (response == 'ok') {
                            fillCertData()
                            $('#certStartDate').val('')
                            $('#certEndDate').val('')
                            $('#cert_physician_id').val('')
                            $('#cert_clinic_id').val('')
                        } else {
                            customMessage(response, 3)
                        }
                    }
                })
            }

            function fillCertData() {
                $.ajax({
                    type: 'POST',
                    url: 'menu/getData/fillCertLogs.php',
                    data: {
                        recipient_id: <?php echo $recip['id'] ?>
                    },
                    success: function(response) {

                        $('#certLogsWrap').html(response)
                    }
                })
            }
            $(document).ready(function() {
                fillCertData()
            })
        </script>
    </div>

    <div class="secBtnTogBtn" onclick="toggleSection('routeInfo',this)">Route Info</div>
    <div class="formRow" id="routeInfo" style="display: none;">

        <div class="inputRow" style="width:100%">
            <div class="scheduleActionWrap" style="display: flex; flex-wrap: wrap; gap: 10px;">
                <button type="button" class="addConentBtn " onclick="addMeal()">Add Schedule</button>
                <!-- <button type="button" class="addConentBtn" onclick="editMeal('main')">Edit Default Schedules</button> -->
                <!-- <button type="button" class="addConentBtn " onclick="addMeal('indiv')">Add Individual Schedule</button> -->

            </div>
            <div id="mealScheduleWrap">

            </div>
            <script>
                $(document).ready(() => {
                    updateMealSchedule();
                })
            </script>
            <div id="mealScheduleCalendarWrap"></div>
        </div>

        <div class="inputRow">
            <label for="route">Route</label>
            <select name="route" id="route">
                <?php
                $routes = $db->select('SELECT * FROM routes');
                echo '<option value="" selected hidden>Select</option>';
                foreach ($routes as $route) {
                    $select = ($recip['route_id'] == $route['id']) ? ' selected' : '';
                    echo '<option value="' . $route['id'] . '"' . $select . '>' . $route['name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="inputRow">
            <label for="routePosition">Route Position</label>
            <input value="<?php echo $recip['routePosition'] ?>" type="number" name="routePosition" id="routePosition" style="width: 100px;" disabled>
        </div>

        <div class="inputRow" style="justify-content: end;">
            <button type="button" class="addConentBtn " onclick="scheduleRouteChange()">Schedule Route Change</button>
        </div>

        <div class="inputRow" style="width:100%">
            <label for="routeNote">Route Note</label>
            <?php
            $breaks = array("<br />", "<br>", "<br/>");
            $text = str_ireplace($breaks, "\n", $recip['routeNote']);
            ?>
            <textarea name="routeNote" id="routeNote"><?php echo $recip['routeNote'] ?></textarea>
            <script>
                var globalEditor;
                ClassicEditor
                    .create(document.querySelector('#routeNote'), {
                        mediaEmbed: {
                            previewsInData: true
                        }
                    })
                    .then(editor => {
                        globalEditor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            </script>
        </div>
    </div>

    <button type="submit" name="addContentBtn" class="recipUpdateBtn">Update</button>
</form>
<script>
    function scheduleRouteChange() {
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/routeChange.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>,
            },
            success: (re) => {
                newModal(re)
            }
        })
    }

    function editMealSchedule(id) {
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/editSchedule.php',
            data: {
                id: id
            },
            success: function(response) {
                newModal(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function updateCalendar(btn, id) {
        var value = $('#yearSelect').val() + '-' + $('#monthSelect').val() + '-01';
        $.ajax({
            type: 'POST',
            url: 'menu/getData/mealSchedule.php',
            data: {
                id: id,
                mY: value,
                recip_id: <?php echo $recip['id'] ?>
            },
            success: function(response) {
                $('#mealScheduleCalendarWrap').html(response)
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function openSchdeule(id) {
        $('.recipMealSelect').each(function(index, element) {
            $(element).removeClass('active');
        })
        $('#openScheduleBtn' + id).addClass('active')

        $.ajax({
            type: 'POST',
            url: 'menu/getData/mealSchedule.php',
            data: {
                id: id,
                recip_id: <?php echo $recip['id'] ?>
            },
            success: function(response) {
                $('#mealScheduleCalendarWrap').html(response)
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function addMeal() {
        $.ajax({
            type: 'POST',
            url: 'menu/addContentForms/newSchedule.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>
            },
            success: function(response) {
                newModal(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function editOffDays() {
        $.ajax({
            type: 'POST',
            url: 'menu/editContentForms/editOffDays.php',
            data: {
                recip_id: <?php echo $recip['id'] ?>
            },
            success: function(response) {
                newModal(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function updateMealSchedule(selected) {
        $.ajax({
            type: 'POST',
            url: 'menu/getData/mealData.php',
            data: {
                recipient_id: <?php echo $recip['id'] ?>,
                selected: selected,
            },
            success: function(response) {
                $('#mealScheduleWrap').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        })
    }

    function toggleNoteYear(btn) {
        var sections = $(btn).parent().find('.noteMonthWrap');
        var inter = 150;
        var descOffset = sections.length * inter
        var ascOffset = 0;
        if ($(btn).hasClass('open')) {
            $(btn).removeClass('open')
            sections.each(function(index, element) {
                $(element).hide(descOffset);
                descOffset = descOffset - inter;
            })
        } else {
            $(btn).addClass('open')
            sections.each(function(index, element) {
                $(element).show(ascOffset);
                ascOffset += inter
            })
        }
    }

    function toggleNoteMonth(btn) {
        var sections = $(btn).parent().find('.noteLogItemWrap');
        var inter = 150;
        var descOffset = sections.length * inter
        var ascOffset = 0;
        if ($(btn).hasClass('open')) {
            $(btn).removeClass('open')
            sections.each(function(index, element) {
                $(element).hide(descOffset);
                descOffset = descOffset - inter;
            })
        } else {
            $(btn).addClass('open')
            sections.each(function(index, element) {
                $(element).show(ascOffset);
                ascOffset += inter
            })
        }
    }

    function addNewNoteLog() {
        var textDesc = $('#newNoteText').val().trim();
        var followUp = ($('#newNoteFollowUp').prop('checked')) ? 1 : 0;
        var followUpDate = $('#newNoteFollowUpDate').val();
        var newNoteECC = ($('#newNoteECC').prop('checked')) ? 1 : 0;
        var newNoteEMS = ($('#newNoteEMS').prop('checked')) ? 1 : 0;
        var newNoteReferral = ($('#newNoteReferral').prop('checked')) ? 1 : 0;


        if (textDesc == '') {
            customMessage('Enter a description', 2, false, false)
            return false;
        }

        if (followUp == 1 && followUpDate == '') {
            customMessage('Select a follow up date', 2, false, false)
            return false;
        }

        $.ajax({
            type: 'POST',
            url: 'menu/handlers/addContentHandler.php',
            data: {
                contentType: 'noteLog',
                note: textDesc,
                followUp: followUp,
                followUpDate: followUpDate,
                emergContactCalled: newNoteECC,
                emsCalled: newNoteEMS,
                referralMade: newNoteReferral
            },
            success: function(response) {
                if (response == 'ok') {
                    fillnoteLog();
                    $('#newNoteText').val('')
                    $('#newNoteFollowUp').prop('checked', false)
                    $('#newNoteFollowUpDate').val('')
                    $('#newNoteECC').prop('checked', false)
                    $('#newNoteEMS').prop('checked', false)
                    $('#newNoteReferral').prop('checked', false)
                }

            },
            error: function(error) {
                console.log(error);
            }
        })
    }
    $(document).ready(function() {
        $('#newNoteFollowUp').on('change', function() {
            if ($('#newNoteFollowUp').prop('checked')) {
                $('#followUpDateWrap').show(200)
            } else {
                $('#followUpDateWrap').hide(200)
            }
        })
    })

    function editNoteLog(id) {
        $.ajax({
            type: 'POST',
            url: 'menu/getData/getNoteLogs.php',
            data: {
                id: id
            },
            success: function(response) {
                newModal();
                $('#modalBody').ready(function() {
                    $('#modalBody').html($('#modalBody').html() + response);
                })
            },
            error: function(error) {
                console.log(error);
            }
        })

    }
    var redirect = false;
    var vewContact = false;

    function viewContact(id) {
        if (confirm('Save and Continue?')) {
            vewContact = id;
            $('#newContentForm').submit()

        }
    }

    function goToContact(id) {
        if (confirm('Save and Continue?')) {
            $('#newContentForm').submit()
            redirect = id;
        }

    }

    function toggleSection(section, btn) {
        if ($(btn).hasClass('checked')) {
            $('#' + section).hide(200)
            $(btn).removeClass('checked')
        } else {
            $('#' + section).show(200)
            $(btn).addClass('checked')
        }
    }
    $(document).ready(function() {
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way
            $('#notes').val(globalEditor2.getData());
            var form = $('#newContentForm')[0];
            var formData = new FormData(form) // Serialize the form data

            formData.append('notes', globalEditor2.getData());

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/editContentHandler.php',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        if (redirect != false) {
                            window.location.href = 'menu/redirects.php/?l=contacts&mod=new&data=' + redirect;
                            redirect = false;
                        } else if (vewContact != false) {
                            window.location.href = 'menu/redirects.php/?l=contacts&mod=view&data=' + vewContact;
                            redirect = false;
                        } else {
                            customMessage('Success', 1, false, true);
                            fillRecipientData();
                            $('#displayContentWrap').fadeOut(100, function() {
                                $('#displayContentWrap').html('');
                                $('#displayContentWrap').fadeIn(100)
                            })
                        }

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