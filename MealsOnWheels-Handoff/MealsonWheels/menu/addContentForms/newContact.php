<form action="index.php" method="post" enctype="multipart/form-data" id="newContentForm" class="contentForm">
    <h2>New Contact</h2>
    <input type="text" value="contact" name="contentType" style="display: none;">
    <div class="formRow">
        <div class="inputRow">
            <label for="recipient_id">Add Recipients</label>
            <div class="recipSearch">
                <input id="searchRecip" type="text" oninput="searchRecipForContact(this)" placeholder="Search" style="width:232px">
                <div id="recipResults"></div>
            </div>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="">Recipients</label>
            <input type="text" name="inputArray" id="inputArray" style="display: none;">
            <div id="recipientBin">
                <?php
                if (isset($_SESSION['redirect_data'])) {
                    $relationships = $db->select('SELECT * FROM relationships');
                    echo '<div class="recipientItem">';
                    echo '<input value="' . $row['id'] . '" type="text" class="linkID" readonly style="display:none">';
                    echo '<button type="button" class="recipDeleteBtn" onclick="deleteRecip(this)">Delete</button>';
                    $recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_SESSION['redirect_data']])[0];
                    echo '<p id="recipName">' . $recip['firstName'] . ' ' . $recip['lastName'] . '</p>';
                    echo '<select name="relationship" id="relationship">';
                    echo '<option value="">Relationship</option>';
                    foreach ($relationships as $re) {
                        echo '<option value="' . $re['id'] . '"' . (($re['id'] == $row['relationship_id']) ? ' selected' : '') . '>' . $re['name'] . '</option>';
                    }
                    echo '</select>';
                    echo '<div class="optionBin"><span>Emergency</span><input type="checkbox" class="emergencyCheck"' . (($row['emergencyContact'] == 1) ? ' checked' : '') . '></div>';
                    echo '<div class="optionBin"><span>POA</span><input type="checkbox" class="poaCheck"' . (($row['poa'] == 1) ? ' checked' : '') . '></div>';
                    echo '<div class="optionBin"><span>Financial</span><input type="checkbox" class="financialCheck"' . (($row['financial'] == 1) ? ' checked' : '') . '></div>';
                    echo '</div>';
                    unset($_SESSION['redirect_mod']);
                    unset($_SESSION['redirect_data']);
                    unset($_SESSION['redirect']);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="formRow">
        <div class="inputRow">
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="fistName" required autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" required autocomplete="off">
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
            <label for="workPhone">Work Phone</label>
            <input type="tel" name="workPhone" id="workPhone" autocomplete="off">
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
            <label for="zip">Zip</label>
            <input type="text" name="zip" id="zip" style="width: 100px;" autocomplete="off">
        </div>
        <div class="inputRow">
            <label for="state">State</label>
            <select name="state" style="max-width:250px" id="state" class="stateSelector"></select>
            <script>
                alterCC()
            </script>
        </div>
    </div>
    <button type="submit" name="addContentBtn">Add</button>
</form>
<script>
    function collectRecipientData() {
        var recips = $('.recipientItem');
        var str = '';
        recips.each(function(index, element) {
            if (index != 0) {
                str += '|';
            }
            var id = $(element).find('.recipId').first();
            var relationship = $(element).find('#relationship').first();
            var emergency = ($(element).find('.emergencyCheck').first().prop('checked')) ? '1' : '0';
            var poa = ($(element).find('.poaCheck').first().prop('checked')) ? '1' : '0';
            var financial = ($(element).find('.financialCheck').first().prop('checked')) ? '1' : '0';
            str += id.val() + ',' + relationship.val() + ',' + emergency + ',' + poa + ',' + financial;
        })

        $('#inputArray').val(str)
    }

    function addRecipient(id, name) {
        $('#recipResults').html('');
        $('#searchRecip').val('');
        var recipientItem = $('<div>', {
            class: 'recipientItem',
        })
        var deleteBtn = $('<button>', {
            type: 'button',
            class: 'recipDeleteBtn',
            onclick: 'deleteRecip(this)',
            text: 'Delete'
        })

        var name = $('<p>', {
            id: 'recipName',
            text: name
        })
        var recipId = $('<input>', {
            type: 'text',
            class: 'recipId',
            value: id
        })
        recipId.css({
            'display': 'none'
        })
        // //
        var relationships = $('<select>', {
            name: 'relationship',
            id: 'relationship',
        })
        var relationship = $('<option>', {
            value: '',
            text: 'Relationship'
        })
        relationships.append(relationship);
        <?php
        $relatioships = $db->select('SELECT * FROM relationships ORDER BY name ASC');
        $count = 0;
        foreach ($relatioships as $re) {
            echo 'var relationship' . $count . ' = $("<option>",{
                value: "' . $re['id'] . '",
                text: "' . $re['name'] . '"
            });';
            echo 'relationships.append(relationship' . $count . ');';
            $count++;
        }
        ?>

        // //
        var emergency = $('<div>', {
            class: 'optionBin'
        })
        var emergencyLabel = $('<span>', {
            text: 'Emergency'
        })
        var emergencyCheck = $('<input>', {
            type: 'checkbox',
            class: 'emergencyCheck'
        })
        emergency.append(emergencyLabel)
        emergency.append(emergencyCheck)

        var poa = $('<div>', {
            class: 'optionBin'
        })
        var poaLabel = $('<span>', {
            text: 'POA'
        })
        var poaCheck = $('<input>', {
            type: 'checkbox',
            class: 'poaCheck'
        })
        poa.append(poaLabel)
        poa.append(poaCheck)

        var financial = $('<div>', {
            class: 'optionBin'
        })
        var financialLabel = $('<span>', {
            text: 'Financial'
        })
        var financialCheck = $('<input>', {
            type: 'checkbox',
            class: 'financialCheck'
        })
        financial.append(financialLabel)
        financial.append(financialCheck)


        recipientItem.append(deleteBtn)
        recipientItem.append(recipId);
        recipientItem.append(name);
        recipientItem.append(relationships);
        recipientItem.append(emergency);
        recipientItem.append(poa);
        recipientItem.append(financial);
        $('#recipientBin').append(recipientItem)
    }

    function deleteRecip(btn) {
        $(btn).parent().remove();
    }

    function searchRecipForContact(input) {
        var $value = input.value.toLowerCase().trim();
        if ($value != '') {
            $.ajax({
                type: 'POST',
                url: 'menu/getData/searchRecipientsByName.php',
                data: {
                    value: $value
                },
                success: function(response) {
                    if (response == 'none') {
                        $('#recipResults').html('<p style="padding:0px 10px">No Results</p>');
                    } else {
                        $('#recipResults').html(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(erro);
                }
            });
        } else {
            $('#recipResults').html('');
        }
    }
    $(document).ready(function() {
        $('#newContentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way
            collectRecipientData();
            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                data: formData,
                success: function(response) {
                    if (response == 'ok') {
                        customMessage('Success', 1, false, true);
                        fillContactData();
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