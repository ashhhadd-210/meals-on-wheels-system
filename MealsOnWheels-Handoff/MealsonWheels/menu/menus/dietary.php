<div class="insertContentBody">
    <div class="contentListWrap">
        <h2>Dietary Settings</h2>
        <div class="contentHead">
            <div class="inputRow">
                <select name="dietaryType" id="dietaryType">
                    <option value="accommodations">Accommodations</option>
                    <option value="allergies">Allergies</option>
                    <option value="diets">Diets</option>
                    <option value="milks">Milk Choices</option>
                </select>
            </div>
        </div>
        <div class="dietaryList" id="dietaryList">
        </div>
    </div>
    <div id="displayContentWrap">

    </div>
</div>
<script>
    var dietaryMenu = sessionStorage.getItem('dietaryMenu');

    function fillDietaryRows(type) {
        if(type == ''){
            type = sessionStorage.getItem('dietaryMenu');
        }
        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillDietaryData.php',
            data: {
                type: type
            },
            success: function(response) {
                $('#dietaryList').html(response);
            },
            error: function(error) {
                $('#dietaryList').html(error);
            }
        })
    }
    $(document).ready(function() {
        if (dietaryMenu == null) {
            dietaryMenu = 'diets'
            sessionStorage.setItem('dietaryMenu', 'diets');
        }
        $('#dietaryType').val(dietaryMenu)
        fillDietaryRows($('#dietaryType').val())
        $('#dietaryType').on('change', function() {
            fillDietaryRows($(this).val());
            sessionStorage.setItem('dietaryMenu', $(this).val());
        })
    })
</script>
<style>
    #dietaryList {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 30px;
    }
</style>