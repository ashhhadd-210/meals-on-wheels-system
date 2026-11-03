<h1>New Assessment</h1>
<form action="" id="newContentForm">
    <div class="formRow">
        <div class="inputRow">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>
        </div>
    </div>
    <div class="formRow">
        <button type="submit">Save & Continue</button>
    </div>
</form>
<script>
    $(document).ready(()=>{
        $('#newContentForm').on('submit',(e)=>{
            e.preventDefault();
            var form = $('#newContentForm')[0];
            var formData = new FormData(form);
            formData.append('contentType','assessment-init');
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/addContentHandler.php',
                processData: false,
                contentType: false,
                data: formData,
                success: (re)=>{
                    console.log(re)
                    if(re.msg == 'ok'){
                        fillAssessments();
                        editAssessment(re.id);
                    } else {
                        customMessage(re.msg,3)
                    }
                }
            })
        })
    })
</script>