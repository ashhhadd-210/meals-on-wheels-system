<div class="insertContentBody">
    <div class="contentListWrap">
        <h2>Assessments</h2>
        <div class="contentHead">
            <button type="button" onclick="newAssessment()" class="addConentBtn">Add</button>
        </div>
        <div class="assessmentList" id="assessmentList">

        </div>
    </div>
    <div id="displayContentWrap">

    </div>
</div>
<script>
    function newAssessment(){
        $.ajax({
            type: 'POST',
            url: 'menu/addContentForms/newAssessment.php',
            success: (re)=>{
                $('#displayContentWrap').html(re)
            }
        })
    }
    $(document).ready(()=>{
        fillAssessments();
    })
    function fillAssessments(){
        $.ajax({
            type: 'POST',
            url: 'menu/getData/fillAssessments.php',
            success: (re)=>{
                $('#assessmentList').html(re)
            }
        })
    }
    function editAssessment(id,btn){
        $.ajax({
            type: 'POST',
            url: 'menu/editContent.php',
            data: {
                type: 'assessment',
                id: id
            },
            success: (re)=>{
                $('.contentRow').each((x,el)=>[
                    $(el).removeClass('active')
                ])
                if(btn != null){
                    $(btn).addClass('active');
                }
                $('#displayContentWrap').html(re)
            }
        })
    }
</script>
<style>
    .assessmentList {
        margin-top: 40px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
</style>