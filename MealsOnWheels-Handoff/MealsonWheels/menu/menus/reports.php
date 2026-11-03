<div class="insertContentBody">
    <div class="contentListWrap">
        <h2>Reports</h2>
        <div class="contentHead">
            
        </div>
        <div class="contentList" id="contentList">
            
        </div>
    </div>
    <div id="displayContentWrap">

    </div>
</div>

<script>
    $(document).ready(()=>{
        fillReports()
    })

    function fillReports(){
        $.ajax({
            type:'POST',
            url:'menu/getData/fillReports.php',
            success: (re)=>{
                $('#contentList').html(re)
            }
        })
    }

    function openReport(btn,id){
        $('.contentRow').each((index,elem)=>{
            $(elem).removeClass('active');
        })
        $(btn).addClass('active')
        $.ajax({
            type:'POST',
            url:'menu/reports/getReport.php',
            data: {
                id:id
            },
            success: (re)=>{
                $('#displayContentWrap').html(re)
            }
        })
    }
</script>