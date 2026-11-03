<style>
    .insertContentBody {
        display: flex;
        height: 80vh;
        max-height: 80vh;
    }

    .contentListWrap {
        flex-grow: 1;
        max-width: 350px;
        width: 100%;
        border-right: 1px solid;
        padding-right: 20px;
        padding-left: 3px;
        height: 100%;
        overflow-y: scroll;
    }

    .contentHead {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .searchWrap {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        height: 100%;
        overflow-y: auto;
    }

    .contentHead button {
        margin-top: 0px;
    }

    #contentList {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #displayContentWrap {
        flex-grow: 1;
        max-height: 100%;
        overflow-y: auto;
        padding-left: 20px;
        padding-right: 3px;
    }

    .contentRow {
        border: 1px solid #eee;
        padding: 10px 20px;
        display: flex;
        gap: 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 200ms ease-in-out;
        justify-content: space-between;
        align-items: center;
        transition: all 200ms ease-in-out;
    }

    .contentRow.active {
        background-color: var(--blue);
        color: white;
    }

    .contentRow.active:hover {
        background-color: var(--blue);
        color: #c6c6c6;
    }

    .contentRow:hover,
    .contentRow:focus {
        background-color: #eee;
        border-color: #cecece;
    }

    #newContentForm {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .recipFistName {
        width: 150px;
    }

    .recipLastName {
        width: 150px;
    }

    .recipNickName {
        width: 100px;
    }

    .recipSearch {
        position: relative;
        width: 100%;
    }

    #recipResults {
        position: absolute;
        background-color: white;
        box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.3);
        width: 232px;
    }

    .recipSearchRow {
        background-color: #eee;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .searchRecipResultWrap {
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .inputRow.contactRecipientLink {
        max-width: 950px;
    }

    #recipientBin {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .recipientItem {
        background-color: white;
        min-width: 160px;
        padding: 10px;
        border-radius: 5px;
        display: flex;
        width: 100%;
        align-items: center;
        gap: 20px;
        max-width: 920px;
    }

    .optionBin {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .optionBin span {
        width: 81px;
        display: block;
    }

    #recipName {
        width: 170px;
    }

    #relationship {
        width: 200px;
    }

    .recipDeleteBtn {
        font-size: 13px !important;
        color: #c63535 !important;
        border: none !important;
        padding: 0px !important;
    }

    .recipDeleteBtn:hover {
        background-color: transparent !important;
    }

    .systemSettingWrap {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .systemSettingRow {
        border: 1px solid #eee;
        padding: 20px;
    }

    .systemOptionFlex {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .systemOptionList {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 200px;
        overflow-y: scroll;
        padding: 10px;
    }

    .optionRow {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background-color: #eee;
        align-items: center;
        border-radius: 5px;
        gap: 10px;
        border: 1px solid transparent;
        cursor: pointer;
    }


    .optionRow.selected {
        border-color: red;
    }

    .optionDelete {
        color: #c13131 !important;
        border: none !important;
        padding: 0px !important;
        font-size: 16px !important;
    }

    .optionDelete:hover {
        background-color: transparent !important;
    }

    .recipTitle {
        position: sticky;
        background-color: white;
        padding: 10px;
        z-index: 100;
        top: 0px;
        box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.2);
    }

    .contentForm.edit {
        margin-bottom: 70px;
    }

    .sectionButton {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 5px;
    }

    .sectionButton h3 {
        margin: 0px;
    }

    .systemOptionFlexContent {
        display: flex;
        gap: 20px;
    }

    .editContentWrap {
        width: 350px;
        min-width: 350px;
        box-shadow: -10px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .clientStatusLog {
        width: 100%;
        background-color: white;
        border-radius: 5px;
        padding: 0px 20px 20px;
    }

    .clientStatusLogList {
        display: flex;
        flex-direction: column;
        gap: 5px;
        max-height: 130px;
        overflow-y: auto;
    }

    .recipClientLogRow {
        background-color: #eee;
        padding: 5px;
        display: flex;
        gap: 10px;
    }

    .recipClientLogRow span {
        min-width: 90px;
    }

    .recipUpdateBtn {
        position: sticky;
        bottom: 10px;
        width: 180px;
        z-index: 5;
        background-color: white !important;
    }

    .recipUpdateBtn:hover {
        background-color: var(--blue) !important;
        color: white !important;
    }

    .secBtnTogBtn {
        cursor: pointer;
        background-image: linear-gradient(to right, #536C7B, #536C7B40);
        padding: 10px;
        border-radius: 5px;
        color: white;
        user-select: none;
    }

    .secBtnTogBtn {
        position: sticky;
        top: 60px;
        z-index: 4;
    }

    .recipContactRow {
        border: 1px solid #c6c6c6;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 200ms ease-in-out;
    }

    .recipContactRow:hover {
        background-color: #c6c6c6;
    }

    .optionsEditForm {
        position: sticky;
        top: 10%;
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 90%;
        margin: 40px auto;
    }

    .noteLogWrap {
        background-color: white;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .noteLogItemWrap {
        padding: 10px;
        border: 1px solid #c6c6c6;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background-image: linear-gradient(to bottom, #ddc3c3, #ddc3c3a3);
    }

    .noteText {
        background-color: #fdeeee;
        padding: 10px;
    }

    .noteLogDetailWrap {
        display: flex;
        gap: 20px;
        justify-content: space-between;
        border: 1px solid #eee;
        padding: 5px;
    }

    .contactTraceWrap {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .contactTraceItem {
        display: flex;
        gap: 20px;
        background-color: #fdeeee;
        padding: 10px;
    }

    .contactTraceName {
        background-color: #ffdcae;
        padding: 0px 10px;
        cursor: pointer;
    }

    .noteLogEditSubmitBtn {
        position: sticky;
        bottom: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    }

    .contactDesc {
        max-width: 300px;
    }

    .newNoteWrap {
        display: flex;
        align-items: center;
        gap: 40px;
        flex-wrap: wrap;
        background-color: white;
        padding: 20px;
        width: 100%;
    }

    .newNoteWrap>div {
        display: flex;
        gap: 10px;
        flex-direction: column;
        align-items: baseline;
    }

    .noteLogActionWrap {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .noteLogActionWrap>span {
        background-color: white;
        padding: 5px 10px;
        border-radius: 50px;
    }

    .noteYearWrap {
        border: 1px solid #eee;
        padding: 5px;
    }

    .noteMonthWrap {
        border: 1px solid #eee;
        margin: 10px 0px;
        padding: 5px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .noteyearWrapHeader {
        background-color: #eee;
        padding: 5px 10px;
        cursor: pointer;
    }

    .noteMonthWrapHeader {
        background-color: #eee;
        padding: 0px 10px;
        cursor: pointer;
    }

    .routeColor {
        height: 10px;
        width: 20px;
        margin-right: auto;
    }

    #routePositionOrder {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .recipentPositionItem {
        background-color: white;
        padding: 10px;
        cursor: pointer;
    }

    #mealScheduleWrap {
        display: flex;
        gap: 10px;
        padding: 10px;
        background-color: white;
        max-height: 240px;
        overflow-y: auto;
        flex-direction: column;
    }

    .mealScheduleBin {
        border: 1px solid #c6c6c6;
        padding: 5px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        border-radius: 5px;
    }

    .recipMealSelect {
        background-color: #eee;
        padding: 5px 15px;
        cursor: pointer;
        transition: all 200ms ease-in-out;
        border: 1px solid transparent;
    }

    .recipMealSelect.active {
        background-color: var(--blue);
        color: white;
    }


    .recipMealSelect.activeSchedule {
        color: green;
    }

    .recipMealSelect.active.activeSchedule {
        background-color: green;
        color: white;
    }

    .schdeuleCalendar {
        width: 70%;
        height: 560px;
    }

    .schdeuleCalendar .empty {
        background-color: #cecece;
    }

    .schdeuleCalendar th {
        height: 10px;
        padding: 0px;
    }

    .schdeuleCalendar td {
        position: relative;
        height: 100px;
        width: 100px;
    }

    .dayNumber {
        position: absolute;
        top: 5px;
        left: 5px;
    }

    .onDay {
        background-color: var(--goodGreen);
        color: white;
        border-color: var(--grey);
    }

    .offDay {
        background-color: #d25555;
        color: white;
        border-color: var(--grey);
    }

    .inScheduleoffDay {
        background-color: var(--goodGreen);
        color: white;
        border: 2px solid #d25555;
    }

    .mealScheduleEditBtn {
        cursor: pointer;
        font-size: 16px;
        text-decoration: underline;
        margin-left: 10px;
    }

    .calendarDateSelectWrap {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .certArea {
        width: 100%;
    }

    .newCertWrap {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: end;
    }

    #certLogsWrap {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .certLogItem {
        background-color: white;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .dietFlexWrap {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .dietFlexWrap .inputRow+.inputRow {
        border-left: 1px solid #c6c6c6;
        padding-left: 20px;
    }

    .dietDesc {
        margin-left: 25px;
        font-size: 14px;
        color: grey;
        max-width: 180px;
    }

    #optionListWrap {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 10px;
        border: 1px solid #c6c6c6;
        border-radius: 5px;
        margin: 40px;
    }

    .answerOption,
    .questionListItem {
        cursor: n-resize;
    }

    .recipAssessmentWrap {
        width: 100%;
    }

    .recipNewAssessWrap,
    #recipPastAssessWrap {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    #recipPastAssessWrap {
        flex-direction: column;
    }

    .recipAssessRow {
        border: 1px solid #c6c6c6;
        padding: 5px 15px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        width: 33%;
    }

    .recipAssessRow.recipScore {
        display: flex;
        width: 100%;
    }

    .contentRow.assesshidden {
        background-color: #f7cfcf;
    }

    .contentRow.assesshidden.active {
        background-color: var(--blue);
    }

    #reportForm {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .recipientSelect {
	position: relative;
	width: 100%;
	display: flex;
	gap: 20px;
}

    .recipientSelect {
        position: relative;
        width: 100%;
    }

    #recipientSearchResults {
        padding: 10px;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
        width: 300px;
        position: absolute;
        z-index: 100;
        background-color: white;
        display: flex;
        flex-direction: column;
        gap: 5px;
        top: 100%;
        left: 0px;
    }

    #recipNameDisplay {
        background-color: var(--goodGreen);
        color: white;
        padding: 10px;
        border-radius: 5px;
    }
</style>