<div class="systemSettingPageWrap">
    <h1 style="margin-top:0px">System Data</h1>
    <div class="systemOptionFlexContent">
        <div class="systemSettingWrap">

            <div class="systemSettingRow">
                <h3>Relationship Options</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="relationship" placeholder="Add Relationship">
                        <button type="button" onclick="addOption('relationship')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM relationships ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'relationship\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'relationship\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Gender Options</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="gender" placeholder="Add Gender">
                        <button type="button" onclick="addOption('gender')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM genders ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'gender\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'gender\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Ethnic Options</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="ethnicGroup" placeholder="Add Ethnic Groups">
                        <button type="button" onclick="addOption('ethnicGroup')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM ethnicGroups ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'ethnicGroup\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'ethnicGroup\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Marital Status Options</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="maritalStatus" placeholder="Add Status">
                        <button type="button" onclick="addOption('maritalStatus')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM maritalStatus ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'maritalStatus\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'maritalStatus\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Client Status Options</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="clientStatus" placeholder="Add Status">
                        <button type="button" onclick="addOption('clientStatus')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM clientStatus ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'clientStatus\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'clientStatus\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Physicians</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox" style="display: flex;flex-direction: column;gap: 5px;">
                        <input type="text" id="physicianFirstName" placeholder="First Name">
                        <input type="text" id="physicianLastName" placeholder="Last Name">
                        <button type="button" onclick="addOption('physician')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM physicians ORDER BY firstName,lastName ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'physician\',this)">' . $option['firstName'] . ' ' . $option['lastName'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'physician\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="systemSettingRow">
                <h3>Clinics</h3>
                <div class="systemOptionFlex">
                    <div class="systemOptionBox">
                        <input type="text" id="clinic" placeholder="Add Clinic">
                        <button type="button" onclick="addOption('clinic')">Add</button>
                    </div>
                    <div class="systemOptionList">
                        <?php
                        $options = $db->select('SELECT * FROM clinics ORDER BY name ASC');
                        foreach ($options as $option) {
                            echo '<div class="optionRow"><span onclick="editOption(' . $option['id'] . ',\'clinic\',this)">' . $option['name'] . '</span><button class="optionDelete" type="button" onclick="deleteOption(\'clinic\',this,' . $option['id'] . ')">Delete</button></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="editContentWrap"></div>
    </div>

</div>

<script>
    function cancelSelection(btn){
        $(btn).parent().remove();
        var optionBtns = $('.optionRow');
        optionBtns.each(function(index, element) {
            $(element).removeClass('selected');
        })
    }
    function deleteOption(type,btn,id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    id: id,
                    contentType: type
                },
                success: function(response) {
                    if (response == 'ok') {
                        $(btn).parent().remove();
                    } else {
                        customMessage(response, 3, false, true);
                    }

                },
                error: function(xhr, status, error) {
                    customMessage(error, 3, false, true)
                }
            })
        }
    }

    function addOption(type) {
        var firstName;
        var lastName;
        var value;
        if (type == 'physician') {
            firstName = $('#physicianFirstName').val();
            lastName = $('#physicianLastName').val();

        } else {
            value = $('#' + type).val();
        }

        $.ajax({
            type: 'POST',
            url: 'menu/handlers/addContentHandler.php',
            data: {
                firstName: firstName,
                lastName: lastName,
                value: value,
                contentType: type
            },
            success: function(response) {
                if (response == 'ok') {
                    location.reload();
                } else {
                    customMessage(response, 3, false, false);
                }

            },
            error: function(xhr, status, error) {

            }
        })
    }

    function sendEditContent(form) {
        var formData = $(form).serialize();


        $.ajax({
            type: 'POST',
            url: 'menu/handlers/editContentHandler.php',
            data: formData,
            success: function(response) {
                if (response == 'ok') {
                    location.reload();
                } else {
                    customMessage(response, 3, false, false);
                }

            },
            error: function(xhr, status, error) {

            }
        });

        return false;
    }

    function editOption(id, type, btn) {
        var optionBtns = $('.optionRow');
        optionBtns.each(function(index, element) {
            $(element).removeClass('selected');
        })
        $(btn).parent().addClass('selected');
        $.ajax({
            type: 'POST',
            url: 'menu/getData/getOptionDisplay.php',
            data: {
                type: type,
                id: id
            },
            success: function(response) {
                if (response == 'Error') {
                    customMessage(response, 3, false, true);
                } else {
                    $('.editContentWrap').html(response)
                }
            },
            error: function(error) {
                customMessage(error, 3, false, true)
            }
        })
    }
</script>