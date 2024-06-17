<style>
#divDoc {
    font-family:"맑은 고딕", "Malgun Gothic", serif;
}
#divDoc ol {
    margin:0; 
    padding: 0;
    list-style-position: inside;
} 
#divDoc ol li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDoc ol ol {
    counter-reset: custom;
    list-style-type: none;
    padding-left: 40px;
} 
#divDoc ol li li:before {
  content: counter(custom)') ';
  counter-increment: custom;
}
#divDoc ol li li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDoc ol ol ol {
    counter-reset: custom;
    list-style-type: none;
    padding-left: 40px;
} 
#divDoc ol li li li:before {
  content: '('counter(custom)') ';
  counter-increment: custom;
}
#divDoc ol li li li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDoc table {
    text-indent: 0;
    border-collapse: collapse;
}

#divDocHistory {
    font-family:"맑은 고딕", "Malgun Gothic", serif;
    font-size: 12px;
}
#divDocHistory ol {
    margin:0; 
    padding: 0;
    list-style-position: inside;
} 
#divDocHistory ol li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDocHistory ol ol {
    counter-reset: custom;
    list-style-type: none;
    padding-left: 40px;
} 
#divDocHistory ol li li:before {
  content: counter(custom)') ';
  counter-increment: custom;
}
#divDocHistory ol li li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDocHistory ol ol ol {
    counter-reset: custom;
    list-style-type: none;
    padding-left: 40px;
} 
#divDocHistory ol li li li:before {
  content: '('counter(custom)') ';
  counter-increment: custom;
}
#divDocHistory ol li li li {
    text-align: justify;
    text-indent: 40px;
    padding: 5px;
}
#divDocHistory table {
    text-indent: 0;
    border-collapse: collapse;
}
#txtContent {
    height : 600px !important;
}
</style>
<script type="text/javascript" src="jqwidgets-ver9.1.6-src/jqxinput.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript">
let editor = {};
$(document).ready(function() {
    //공문서 목록
    var cellsrenderer = function(row, columnfield, value, defaulthtml, columnSettings, rowData) {
        //폐기일 경우 취소선 표시
        if (rowData.docState == "3") {
            return '<span style="margin: 4px; float: ' + columnSettings.cellsalign + '"><del>' + $.jqx.dataFormat.formatdate(value, columnSettings.cellsformat) + '</del></span>';
        }
    }
    $("#gridOfficialDocList").jqxGrid({
        width: 520,
        sortable: true,
        pageable: true,
        pagermode: 'simple',
        autorowheight: true,
        showfilterrow: true,
        filterable: true,
        autoheight: true,
        columnsresize: true,
        pagesize: 20,
        selectionmode: 'singlerow',
        columns: [
            { text: '문서번호', dataField: 'docCd', width: 150, cellsrenderer: cellsrenderer },
            { text: '제목', dataField: 'title', cellsrenderer: cellsrenderer },
            { text: '시행일', dataField: 'enforcementDate', width: 95, cellsrenderer: cellsrenderer }
        ]
    });
    //공문서 선택 시
    $("#gridOfficialDocList").on('rowselect', function(event) {
        var data = $('#gridOfficialDocList').jqxGrid('getrowdata', event.args.rowindex);
        //공문서 고유번호
        $("#docNo").val(data.docNo);

        //공문서 상세 표시
        showOfficialDocDetail();
    });

    //초기 화면 표시
    $("#mode").val("INIT");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(),
        dataType: "json", 
        success: function(result) {
            //회사 정보
            var companyInfo = result["companyInfo"];
            //회사 명
            $("#coNm").html( companyInfo["coNm"] );
            //회사 주소
            $("#addr").html( companyInfo["addr"] );
            //회사 홈페이지
            $("#homepage").html( companyInfo["homepage"] );
            //회사 대표자 명
            $("#ceo").val( companyInfo["ceo"] );

            //연도
            var html = "";
            $.each(result["yearList"], function(cd, val) {
                html += "<option value='" + cd + "'>" + val + "</option>";
            });
            $("#year").append(html);
            $("#year").val($("#year option:eq(1)").val());

            //직인 목록
            html = "";
            $.each(result["sealTypeList"], function(cd, val) {
                html += "<option value='" + cd + "'>" + val + "</option>";
            });
            $("#seal").append(html);
            onSealChanged();

//             html = "";
//             $.each(result["headTypeList"], function(cd, val) {
//                 html += "<option value='" + cd + "'>" + val + "</option>";
//             });
//             $("#headType").append(html);
//             html = "";
//             $.each(result["directorTypeList"], function(cd, val) {
//                 html += "<span id='directorType_" + cd + "' style='display:none;' >" + val + "</span>";
//             });
//             $("body").append(html);
//             onHeadTypeChanged();

            //회사 명
            $("#his_coNm").html( companyInfo["coNm"] );
            //회사 주소
            $("#his_addr").html( companyInfo["addr"] );
            //회사 홈페이지
            $("#his_homepage").html( companyInfo["homepage"] );
        },
        complete: function() {
            //공문 종류 선택
            onDocConditionsChanged();
        },
        error: function(request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });

    //공문서 편집 이력
    $("#gridOfficialDocHistoryList").jqxGrid(
    {
        width: "310px",
        pageable: true,
        pagermode: 'simple',
        pagesize: 15,
        pagerheight: 60,
        autoheight: true,
        columnsresize: true,
        selectionmode: 'singlerow',
        columns: [
            { text: '편집일', dataField: 'modDate' },
            { text: '진행상태', dataField: 'docStateName', width: 80 }
        ]
    });
    //공문서 편집 이력 선택 시
    $("#gridOfficialDocHistoryList").on('rowselect', function(event) {
        var data = $('#gridOfficialDocHistoryList').jqxGrid('getrowdata', event.args.rowindex);

        //공문서 편집 이력 고유번호
        $("#docHisNo").val(data.docHisNo);

        //공문서 편집 이력 상세 표시
        showOfficialDocHistoryDetail();
    });

    //공문서 편집 이력 보기 창
    $('#historyDialog').jqxWindow({
        width: 1100,
        maxWidth: 1100, 
        height: 870, 
        maxHeight: 870, 
        resizable: false,
        autoOpen: false,
        isModal: true
    });
    //공문서 편집 이력 보기 창 닫기 시
    $('#historyDialog').on('close', function(event) {
        //공문서 편집 이력 선택 없애기
        $("#gridOfficialDocHistoryList").jqxGrid('clearselection');
        $('#gridOfficialDocHistoryList').jqxGrid('clear');
        //공문서 편집 이력 상세 숨기기
        $("#divDocHistory").hide();
    });

    //내용 편집기
//     CKEDITOR.replace('txtContent', {
//         height: "500px",
//         enterMode : CKEDITOR.ENTER_BR, 
//         removeButtons: 'Styles,Format,Anchor',
//         extraPlugins : 'tableresize',
//         toolbar: [
// //             { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
// //             { name: 'document', items: [ 'Source' ] },
// //             { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
//             { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ] },
// //             { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
//             { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
// //             { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
// //             { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
//             { name: 'basicstyles', items: [ 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
// //             { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
// //             '/',
//             { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
// //             { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
// //             { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
//             { name: 'insert', items: [ 'Table', 'HorizontalRule', 'PageBreak' ] },
// //             { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
//             { name: 'styles', items: [ 'FontSize' ] },
// //             { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
// //             { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
//             { name: 'tools', items: [ 'Maximize' ] }//,
// //             { name: 'about', items: [ 'About' ] }
//         ],
//         forcePasteAsPlainText : true,
//         indentOffset: 80,
// //         contentsCss: 'body{font-family:"맑은 고딕", "Malgun Gothic", serif;} ol, ul {margin:0; padding: 0;list-style-position: inside;} li {text-indent: 60px;}'
//         contentsCss: 'body{font-family:"맑은 고딕", "Malgun Gothic", serif; font-size: 12px;margin:0; padding: 0;} ol {margin:0; padding: 0;list-style-position: inside;} ol li {text-align: justify;text-indent: 40px; padding: 5px;}'
//             + 'ol ol {counter-reset: custom;list-style-type: none;padding-left: 40px;} ol li li:before {content: counter(custom)") ";counter-increment: custom;} ol li li {text-align: justify;text-indent: 40px;padding: 5px;}'
//             + 'ol ol ol {counter-reset: custom;list-style-type: none;padding-left: 40px;}  ol li li li:before {content: "("counter(custom)") ";counter-increment: custom;} ol li li li {text-align: justify;text-indent: 40px;padding: 5px;}'
//             + 'table {text-indent: 0;border-collapse: collapse;}'
//     });
//     CKEDITOR.on('dialogDefinition', function(ev) {
//         var diagName = ev.data.name;
//         var diagDefn = ev.data.definition;

//         //표 그리기
//         if(diagName === 'table') {
//             var infoTab = diagDefn.getContents('info');
//             var width = infoTab.get('txtWidth');
//             //표 너비 기본 값 설정
//             width['default'] = "300px";
//         }
//     });

    //직원 선택 다이알로그
    $('#dialogUser').jqxWindow({
        width: 800,
        maxWidth: 800, 
        height: 600, 
        resizable: false,
        autoOpen: false,
        isModal: true,
        cancelButton: $('#btnCloseUser'),
        initContent: function() {
            
        }
    });
    $('#dialogUser').appendTo($("#mainForm"));
    $('#dialogUser').on('close', function(event) { 
        clearUserList();
    });
    $('#dialogUser').on('open', function(event) {
        getUserGrid();
    });
        
    var pmClass = function(row, columnfield, value) {
        var data = $('#gridJobList').jqxGrid('getrowdata', row);
        if (data['jobPmHoldOffice'] == "3") {
            return 'txtStrikeOut';
        }
    }
    //JOB 목록
    var urlJob = "document/official_document_job_list_data.php";
    // prepare the data
    var sourceJob =
    {
        datatype: "json",
        datafields: [
            { name: 'jno', type: 'int' },
            { name: 'jobNo', type: 'string' },
            { name: 'compName', type: 'string' },
            { name: 'jobName', type: 'string' },
            { name: 'jobPm', type: 'string' },
            { name: 'jobPmHoldOffice', type: 'string' },
            { name: 'jobSd', type: 'string' },
            { name: 'jobEd', type: 'string' },
            { name: 'jobState', type: 'string' },
            { name: 'locName', type: 'string' },
            { name: 'jobCode', type: 'string' },
            { name: 'jobType', type: 'string' },
            { name: 'orderCompNick', type: 'string' },
            { name: 'orderCompName', type: 'string' }
        ],
        id: 'jno',
        url: urlJob
    };
    var dataAdapterJob = new $.jqx.dataAdapter(sourceJob);
    $("#ddlJobList").jqxDropDownButton({ width: "480px", height: "20px"});
    $("#gridJobList").jqxGrid({
        width: "880px",
        source: dataAdapterJob,
        sortable: true,
        pageable: true,
        pagermode: 'simple',
        autorowheight: true,
        showfilterrow: true,
        filterable: true,
        autoheight: true,
        columnsresize: true,
        selectionmode: 'singlerow',
        columns: [
            { text: 'JOB No.', dataField: 'jobNo', width: 150 },
            { text: 'End-User', dataField: 'compName', width: 120 },
            { text: 'Client', dataField: 'orderCompName', width: 120 },
            { text: 'JOB 명', dataField: 'jobName', width: 250 },
            { text: 'PM', dataField: 'jobPm', cellclassname: pmClass, width: 60 },
            { text: '시작일', dataField: 'jobSd', width: 90 },
            { text: '종료일', dataField: 'jobEd', width: 90 },
            { text: '진행현황', dataField: 'jobState', width: 80 }//,
//             { text: '사업소', dataField: 'locName', width: 80 },
//             { text: '업무 코드', dataField: 'jobCode', width: 80 },
//             { text: 'JOB 유형', dataField: 'jobType', width: 100 }
        ]
    });
    //JOB 선택 시
    $("#gridJobList").on('rowselect', function(event) {
        var args = event.args;
        var row = $("#gridJobList").jqxGrid('getrowdata', args.rowindex);
        //JOB 고유 번호
        $("#jno").val(row["jno"]);
        //문서번호 Client 코드 추가
        $("#docCd_comp").val(row["orderCompNick"]);
        //선택된 JOB
        var dropDownContent = '<div style="position: relative; margin-left: 3px; margin-top: 2px;">' + row['jobNo'] + ' ' + row['jobName'] + '</div>';
        $("#ddlJobList").jqxDropDownButton('setContent', dropDownContent);
        $('#ddlJobList').jqxDropDownButton('close');
    });

    //시행일
    $("#div_enforcementDate").jqxDateTimeInput({ 
        width: 150, 
        height: 20, 
        readonly: true, 
        formatString: "yyyy-MM-dd", 
        culture: 'ko-KR' 
    });

    // initialize validator.
    $('#mainForm').jqxValidator({
        rules: [
            { input: '#ddlJobList', message: 'JOB은 필수 입력입니다.', action: 'close'
                , rule: function(input, commit) {
                    if ($("input[type='radio'][name='type']:checked").val() == "ESTABLISHMENT") {
                        var jno = $("#jno").val();
                        if (jno == "") {
                            return false;
                        }
                        return true;
                    }
                    return true;
               } 
           },
           { input: '#cc', message: '수신은 필수 입력입니다.', action: 'keyup, blur', rule: 'required' },
           { input: '#cc', message: '수신은 1자 이상 500자 이하로 입력해 주세요.', action: 'blur'
               , rule: function(input, commit) {
                    return checkLengthOfValue(input.val(), 1, 1000);
               } 
           },
           { input: '#bcc', message: '참조는 1자 이상 500자 이하로 입력해 주세요.', action: 'blur'
                , rule: function(input, commit) {
                    return checkLengthOfValue(input.val(), 1, 1000);
               } 
           },
           { input: '#title', message: '제목은 필수 입력입니다.', action: 'keyup, blur', rule: 'required' },
           { input: '#title', message: '제목은 1자 이상 250자 이하로 입력해 주세요.', action: 'blur'
                , rule: function(input, commit) {
                    return checkLengthOfValue(input.val(), 1, 500);
               } 
           },
//            { input: '#divContent', message: '내용은 1자 이상 2000자 이하로 입력해 주세요.', action: 'blur'
//                , rule: function (input, commit) {
//                     $("#content").val(CKEDITOR.instances.txtContent.getData());
//                     return checkLengthOfValue( $("#content").val(), 0, 4000 );
//                } 
//            },
//MODIFY 20190208 표기 수정
//            { input: '#userName', message: '담당자는 필수 입력입니다.', action: 'change', rule: 'required' },
           { input: '#userName', message: '작성자는 필수 입력입니다.', action: 'change', rule: 'required' },
//            { input: '#headType', message: '팀장/PM 을 입력해 주세요.', action: 'change'
//                , rule: function (input, commit) {
//                    var valid = true;
//                    if ($("#headType").val() == null || $("#headType").val() == "") {
//                        valid = false;
//                    }
//                    return valid;
//                }
//            }, 
//            { input: '#teamLeaderName', message: '팀장/PM 은 필수 입력입니다.', action: 'change', rule: 'required' },
           { input: '#teamLeaderName', message: '심의는 필수 입력입니다.', action: 'change', rule: 'required' },
//            { input: '#directorName', message: '본부장 명을 입력해 주세요.', action: 'blur'
           { input: '#directorName', message: '승인 명을 입력해 주세요.', action: 'keyup, blur'
               , rule: function(input, commit) {
                    var valid = true;
                    //직접입력 or 직원선택일 경우
                    if ($("#director").val() == "2" || $("#director").val() == "3") {
                        if (input.val() == "") {
                            valid = false;
                        }
                    }
                    return valid;
               } 
           },
           { input: '#tel', message: '전화번호 형식이 올바르지 않습니다.', action: 'blur'
               , rule: function(input, commit) {
                   return checkPhoneNumberBlank(input.val());
               } 
           },
           { input: '#fax', message: '팩스번호 형식이 올바르지 않습니다.', action: 'blur'
               , rule: function(input, commit) {
                   return checkPhoneNumberBlank(input.val());
               } 
           },
           { input: '#email', message: '이메일 형식이 올바르지 않습니다.', action: 'blur'
               , rule: function(input, commit) {
                   return checkEmail(input.val());
               } 
           }
        ]
    });

    //연도 선택
    $("#year").on("change", onDocConditionsChanged);
    //공문 종류 선택
    $("input[type='radio'][name='type']").on("change", onDocConditionsChanged);
//     $("#headType").on("change", onHeadTypeChanged);
    //직인 선택
    $("#seal").on("change", onSealChanged);
    //직인 이름란 클릭
    $("#sealName").on("click", function() {
        //대표이사는 고정
        if ($("#seal").val() == "1" || $("#seal").val() == "4") {
            return;
        }
        $("#targetUser").val("seal");
        // show the popup window.
        $("#dialogUser").jqxWindow('open');
    });
    //작성자 클릭
    $("#userName").on("click", function() {
        $("#targetUser").val("charge");
        // show the popup window.
        $("#dialogUser").jqxWindow('open');
    });
    //심의 클릭
    $("#teamLeaderName").on("click", function() {
        $("#targetUser").val("leader");
        // show the popup window.
        $("#dialogUser").jqxWindow('open');
    });
    //승인 이름란 클릭
    $("#directorName").on("click", function() {
        $("#targetUser").val("director");
        //대표이사는 고정
        if ($("#director").val() == "1" || $("#director").val() == "2") {
            return;
        }
        // show the popup window.
        $("#dialogUser").jqxWindow('open');
    });

    //공문서 일괄 다운로드
    $("#btnDownloadOfficialDocList").on("click", onBtnDownloadOfficialDocListClick);
    //공문서 다운로드 버튼
    $("#btnDownloadOfficialDoc").on("click", onBtnDownloadOfficialDocClick);
    //새 공문서 작성 버튼
    $("#btnAddOfficialDoc").on("click", onBtnAddOfficialDocClick);
    //공문서 편집 이력 보기 버튼
    $("#btnShowOfficialDocHistory").on("click", onBtnShowOfficialDocHistoryClick);
    //폐기 버튼
    $("#btnDelOfficialDoc").on("click", onBtnDelOfficialDocClick);
    //편집 버튼
    $("#btnEditOfficialDoc").on("click", onBtnEditOfficialDocClick);
    //미리보기 버튼
    $("#btnPreviewOfficialDoc").on("click", function() {
        onOfficialDocPdfClick("PREVIEW");
    });
    //CLEAR 버튼
    $("#btnClearOfficialDoc").on("click", editOfficialDocDetail);
    //저장 버튼
    $("#btnSaveOfficialDoc").on("click", onBtnSaveOfficialDocClick);
    //발행 버튼
    $("#btnSendOfficialDoc").on("click", function(event) {
        event.preventDefault();
        $(this).prop('disabled', true);
        onBtnSendOfficialDocClick();
    });
    //취소 버튼
    $("#btnShowOfficialDoc").on("click", showOfficialDocDetail);
    //붙임 파일 추가 버튼
    $("#btnAddFile").on("click", onAddFileClick);
    //과거 공문서 다운로드
    $("#btnDownloadOfficialDocHis").on("click", onBtnDownloadOfficialDocHisClick);
    //직접기입 값변경
    $("#directSeal").on('keyup', onDirectSealChange);
});

//공문 종류 선택
function onDocConditionsChanged() {
    $("#gridOfficialDocList").jqxGrid('clearselection');

    //공문 종류
    var type = $("input[type='radio'][name='type']:checked").val();
    var url = "document/official_document_list_data.php";
    // prepare the data
    var source =
    {
        datatype: "json",
        datafields: [
            { name: 'docNo', type: 'int' },
            { name: 'docCd', type: 'string' },
            { name: 'title', type: 'string' },
            { name: 'enforcementDate', type: 'string' },
            { name: 'docState', type: 'string' }
        ],
        id: 'docNo',
        url: url,
        data: {
            type: type, 
            year: $("#year").val()
        },
        async: false
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    $("#gridOfficialDocList").jqxGrid({source: dataAdapter});
    //일반 공문일 경우
    if (type == "GENERAL") {
        //JOB 선택 불가
        $('#ddlJobList').jqxDropDownButton({disabled: true});
        if ($("#mode").val() != "INIT") {
            clearJobList();
        }
        //문서번호 Client 숨기기
        $("#docCd_comp").hide();
        $("#span_docCd_comp").hide();
    }
    //기성청구용 공문
    else if (type == "ESTABLISHMENT") {
        //JOB 선택 가능
        $('#ddlJobList').jqxDropDownButton({disabled: false});
        //문서번호 Client 표시
        $("#docCd_comp").show();
        $("#span_docCd_comp").show();
    }
    //유효성 검사 숨기기
    $('#mainForm').jqxValidator('hide');
    //공문서 상세 숨기기
    $("#divDoc").hide();
}

//JOB 초기화
function clearJobList() {
    $("#ddlJobList").jqxDropDownButton('setContent', "");
    $("#gridJobList").jqxGrid('clearSelection');
    $('#gridJobList').jqxGrid('gotopage', 0);
    var filterGroups = $('#gridJobList').jqxGrid('getfilterinformation');
    if (filterGroups.length > 0) {
        $('#gridJobList').jqxGrid('clearfilters');
    }
    var sortinformation = $('#gridJobList').jqxGrid('getsortinformation');
    if (sortinformation != null) {
        $('#gridJobList').jqxGrid('removesort');
    }
    $("#jno").val("");
}

//공문서 상세 보기
function showOfficialDocDetail() {
    //유효성 검사 숨기기
    $('#mainForm').jqxValidator('hide');
    //공문서 상세 숨기기
    $("#divDoc").hide();

    $("#mode").val("SHOW");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(),
        dataType: "json", 
        success: function(result) {
            var docInfo = result["docInfo"];

            //공문 종류
            var type = $("input[type='radio'][name='type']:checked").val();
            //일반공문
            if (type == "GENERAL") {
                //JOB 선택 불가
                $('#ddlJobList').jqxDropDownButton({disabled: true});
                clearJobList();
            }
            //기성청구용 공문
            else if (type == "ESTABLISHMENT") {
                //JOB 선택
                var index = $('#gridJobList').jqxGrid('getrowboundindexbyid', docInfo["jno"]);
                $('#gridJobList').jqxGrid('selectrow', index);
                //임시저장일 경우
                if (docInfo["docState"] == "1") {
                    //JOB 수정 가능
                    $('#ddlJobList').jqxDropDownButton({disabled: false});
                  //문서번호 Client 코드 수정 가능
                    $('#docCd_comp').prop("readonly", false);
                }
                else {
                    //JOB 수정 불가
                    $('#ddlJobList').jqxDropDownButton({disabled: true});
                    //문서번호 Client 코드 수정 불가
                    $('#docCd_comp').prop("readonly", true);
                }
            }
            //수신
            $("#txt_cc").html( docInfo["cc"] );
            //참조
            $("#txt_bcc").html( docInfo["bcc"] );
            //문서번호
            $("#txt_docCd").html( docInfo["docCd"] );
            //시행일 
            $("#txt_enforcementDate").html( docInfo["enforcementDate"] );
            //시행일
            $("#enforcementDate").val( docInfo["enforcementDate"] );
            //제목
            $("#txt_title").html( docInfo["title"] );
            //내용
            $("#txt_content").html( docInfo["content"] );
            //붙임 파일 목록
            $("#txt_attachedFile").empty();
            //붙임 파일이 존재할 경우
            if (result["fileList"].length > 0) {
                $(result["fileList"]).each(function(i) {
                    var fileInfo = $(this)[0];

                    //파일 다운로드
                    $("#txt_attachedFile").append(
                        (i + 1) + '. ' +
                        '<a href="document/official_document_list_file_download.php?mode=attached&docNo=' + $("#docNo").val() + '&fileNo=' + fileInfo["fileNo"] + '" target="_blank">' + 
                        fileInfo["fileName"] + 
                        '</a>' + 
                        '<br />'
                    );
                });
            }
            //직인
            $("#txt_sealName").html( docInfo["sealName"] );
            //작성자 명
            $("#txt_userName").html( docInfo["userName"] );
            //전화번호
            $("#txt_tel").html( docInfo["tel"] );
            //팩스번호
            $("#txt_fax").html( docInfo["fax"] );
            //이메일
            $("#txt_email").html( docInfo["email"] );
            //심의
//             $("#txt_headType").html($("#headType option[value='" + docInfo["headType"] + "']").text());
            //심의 사원명
            $("#txt_teamLeaderName").html( docInfo["teamLeaderName"] );
            //승인
            $("#txt_directorType").html($("#directorType_" + $("#headType").val()).text());
            //전결일 경우
            if (docInfo["director"] == "1") {
                $("#txt_director").html("전결");
            }
            //직접 입력일 경우
            else if (docInfo["director"] == "2" || docInfo["director"] == "3") {
                //승인 명
                $("#txt_director").html(docInfo["directorName"]);
            }

            //임시 저장
            if (docInfo["docState"] == "1") {
                $("#btnDelOfficialDoc").hide();
                //공문 관리자 또는 작성자
                if (docInfo["editable"] == "Y") {
                    //발행 버튼 표시
                    $("#btnSendOfficialDoc").show();
                    $("#btnSendOfficialDoc").prop('disabled', false);
                    //편집 버튼 표시
                    $("#btnEditOfficialDoc").show();
                }
                //작성 불가
                else if (docInfo["editable"] == "N") {
                    //발행 버튼 숨기기
                    $("#btnSendOfficialDoc").hide();
                    //편집 버튼 숨기기
                    $("#btnEditOfficialDoc").hide();
                }
                //공문서 다운로드 버튼 숨기기
                $("#btnDownloadOfficialDoc").hide();
            }
            //발행
            else if (docInfo["docState"] == "2") {
                //공문 관리자 또는 작성자
                if (docInfo["editable"] == "Y") {
                    //폐기 버튼 표시
	                $("#btnDelOfficialDoc").show();
	                //편집 버튼 표시
                    $("#btnEditOfficialDoc").show();
                }
                //작성 불가
                else if (docInfo["editable"] == "N") {
                    //폐기 버튼 숨기기
                    $("#btnDelOfficialDoc").hide();
                    //편집 버튼 숨기기
                    $("#btnEditOfficialDoc").hide();
                }
                //발행 버튼 숨기기
                $("#btnSendOfficialDoc").hide();
                //공문서 다운로드 버튼 표시
                $("#btnDownloadOfficialDoc").show();
            }
            //폐기
            else if (docInfo["docState"] == "3") {
                //폐기 버튼 숨기기
                $("#btnDelOfficialDoc").hide();
                //발행 버튼 숨기기
                $("#btnSendOfficialDoc").hide();
                //편집 버튼 숨기기
                $("#btnEditOfficialDoc").hide();
                //공문서 다운로드 버튼 표시
                $("#btnDownloadOfficialDoc").show();
            }

            //공문서 상세 보기 란 보이기
            $("#divDoc table .show").show();
            //붙임 파일이 존재할 경우
            if (result["fileList"].length > 0) {
                //붙임 파일 표시
                $(".showFile").show();
            }
            //붙임 파일이 존재하지 않을 경우
            else {
                //붙임 파일 숨기기
                $(".showFile").hide();
            }
            //공문서 대체 문서 업로드 숨기기
            $("#tblOfficialDocFile").hide();

            //공문서 편집 란 숨기기
            $("#divDoc table .edit").hide();
            //공문서 상세 표시
            $("#divDoc").show();
            //공문서 편집 이력 보기 버튼 표시
            $("#btnShowOfficialDocHistory").show();
        },
        error: function(request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//공문서 편집 이력 보기 버튼 클릭
function onBtnShowOfficialDocHistoryClick() {
    var url = "document/official_document_list_history_data.php";
    // prepare the data
    var source =
    {
        datatype: "json",
        datafields: [
            { name: 'docHisNo', type: 'int' },
            { name: 'docStateName', type: 'string' },
            { name: 'modDate', type: 'string' }
        ],
        id: 'docHisNo',
        url: url,
        data: {
            docNo: $("#docNo").val()
        },
        async: false
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    $("#gridOfficialDocHistoryList").jqxGrid({source: dataAdapter});

    // show the popup window.
    $("#historyDialog").jqxWindow('open');
}

//공문서 편집 이력 보기
function showOfficialDocHistoryDetail() {
    $("#divDocHistory").hide();

    $("#mode").val("SHOW_HIS");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(),
        dataType: "json", 
        success: function(result) {
            var docInfo = result["docInfo"];

            //수신
            $("#his_cc").html(docInfo["cc"]);
            //참조
            $("#his_bcc").html(docInfo["bcc"]);
            //문서번호
            $("#his_docCd").html(docInfo["docCd"]);
            //시행일 
            $("#his_enforcementDate").html(docInfo["enforcementDate"]);
            //제목
            $("#his_title").html(docInfo["title"]);
            //내용
            $("#his_content").html(docInfo["content"]);
            //붙임 파일 목록
            $("#his_attachedFile").empty();
            if (result["fileList"].length > 0) {
                $(result["fileList"]).each(function(i) {
                    var fileInfo = $(this)[0];

                    $("#his_attachedFile").append(
                        (i + 1) + '. ' + fileInfo["fileName"] + '<br />'
                    );
                });
                $(".his_file").show();
            }
            else {
                $(".his_file").hide();
            }
            //직인
            $("#his_sealName").html(docInfo["sealName"]);
            //작성자 명
            $("#his_userName").html(docInfo["userName"]);
            //심의
//             $("#his_headType").html($("#headType option[value='" + docInfo["headType"] + "']").text());
            //심의 사원명
            $("#his_teamLeaderName").html(docInfo["teamLeaderName"]);
            //승인
//             $("#his_directorType").html($("#directorType_" + $("#headType").val()).text());
            //전결일 경우
            if (docInfo["director"] == "1") {
                $("#his_directorName").html("전결");
            }
            //직접 입력일 경우
            else if (docInfo["director"] == "2" || docInfo["director"] == "3") {
                //승인 명
                $("#his_directorName").html(docInfo["directorName"]);
            }
            //전화번호
            $("#his_tel").html(docInfo["tel"]);
            //팩스번호
            $("#his_fax").html(docInfo["fax"]);
            //이메일
            $("#his_email").html(docInfo["email"]);

            //공문서 편집 이력 상세 표시
            $("#divDocHistory").show();
            //발행
            if (docInfo["docState"] == 2) {
                //해당 공문서 다운로드 버튼 표시
                $("#btnDownloadOfficialDocHis").show();
            }
            else {
                //해당 공문서 다운로드 버튼 숨기기
                $("#btnDownloadOfficialDocHis").hide();
            }
        },
        error: function(request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//새 공문서 작성 버튼 클릭
function onBtnAddOfficialDocClick() {
    //정보 초기화
    $("#docNo").val("");
    $("#pre_unoInCharge").val("");
    $("#pre_gradeName").val("");
    $("#pre_tel").val("");
    $("#pre_fax").val("");
    $("#pre_email").val("");
    $("#directSeal").hide();
    $("#directSeal").val("");

    //공문서 목록 선택 없애기
    $("#gridOfficialDocList").jqxGrid('clearselection');

    editOfficialDocDetail();
}

//편집 버튼 클릭
function onBtnEditOfficialDocClick() {
    editOfficialDocDetail();
}

//공문서 편집
function editOfficialDocDetail() {
    //공문서 편집 이력 보기 버튼 숨기기
    $("#btnShowOfficialDocHistory").hide();

    $('#mainForm').jqxValidator('hide');
    $("#divDoc").hide();

    editor = new SynapEditor("txtContent", synapEditorConfig);

    clearJobList();
    //내용 초기화
    // CKEDITOR.instances.txtContent.setData("");
    //붙임 파일 초기화
    $('#divAttachedFileList').empty();

    $("#mode").val("SHOW");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(),
        dataType: "json", 
        success: function(result) {
            var docInfo = result["docInfo"];

            //공문 종류
            var type = $("input[type='radio'][name='type']:checked").val();
            //일반공문
            if (type == "GENERAL") {
                $('#ddlJobList').jqxDropDownButton({disabled: true});
            }
            //기성청구용 공문
            else if (type == "ESTABLISHMENT") {
                //새 작성이 아닌경우
                if (docInfo["docState"] != "") {
                    var index = $('#gridJobList').jqxGrid('getrowboundindexbyid', docInfo["jno"]);
                    $('#gridJobList').jqxGrid('selectrow', index);
                }
                //새 작성, 임시저장일 경우
                if (docInfo["docState"] == "" || docInfo["docState"] == "1") {
                    //JOB 선택 가능
                    $('#ddlJobList').jqxDropDownButton({disabled: false});
                    //문서번호 Client 코드 편집 가능
                    $('#docCd_comp').prop("readonly", false);
                }
                else {
                    //JOB 선택 불가
                    $('#ddlJobList').jqxDropDownButton({disabled: true});
                    //문서번호 Client 코드 편집 불가
                    $('#docCd_comp').prop("readonly", true);
                }
            }
            //수신
            $("#cc").val(docInfo["cc"]);
            //참조
            $("#bcc").val(docInfo["bcc"]);
            //문서번호
            $("#docCd_ym").val(docInfo["docCd_ym"]);
            $("#docCd_comp").val(docInfo["docCd_comp"]);
            $("#docCd_seq").val(docInfo["docCd_seq"]);
            //시행일
            $("#div_enforcementDate").jqxDateTimeInput("setDate", docInfo["enforcementDate"]);
            $("#enforcementDate").val(docInfo["enforcementDate"]);
            //제목
            $("#title").val(docInfo["title"]);
            //내용
            $("#content").val(docInfo["content"]);
            //직인
            $("#seal").val(docInfo["seal"]);
            //대표이사 또는 초기값
            if (docInfo["seal"] == "1" && docInfo["sealName"] == "") {
                //대표이사
                $("#sealName").val($("#ceo").val());
                $("#directSeal").hide();
            } 
            // 직접기입
            else if(docInfo["seal"] == "9") {
                var arrSeal = docInfo["sealName"].split(" ");
                var name = arrSeal[arrSeal.length - 1];

                directSeal = docInfo["sealName"].replace(" " + name, '');

                $("#sealName").val(docInfo["sealName"]);
                $("#directSeal").val(directSeal);
                $("#directSeal").show();
            }
            else {
                $("#sealName").val(docInfo["sealName"]);
                $("#directSeal").hide();
            }
//             //새 작성
//             if (docInfo["docState"] == "") {
//                 //로그인 유저를 작성자로 선택
//                 var index = $('#gridUserList').jqxGrid('getrowboundindexbyid', docInfo["unoInCharge"]);
//                 //작성자
//                 $("#targetUser").val("charge");
//                 selectUser(index);
//             }
//             //편집
//             else {
            if (docInfo["docState"] != "") {
                //저장된 작성자 정보(비교용)
                $("#pre_unoInCharge").val(docInfo["unoInCharge"]);
                $("#pre_gradeName").val(docInfo["gradeName"])
                $("#pre_tel").val(docInfo["tel"]);
                $("#pre_fax").val(docInfo["fax"]);
                $("#pre_email").val(docInfo["email"]);
            }

            //작성자 고유번호
            $("#unoInCharge").val(docInfo["unoInCharge"]);
            //작성자
            $("#userName").val(docInfo["userName"]);
            //작성자 직위
            $("#gradeName").val(docInfo["gradeName"]);
            //전화번호
            $("#tel").val(docInfo["tel"]);
            //팩스번호
            $("#fax").val(docInfo["fax"]);
            //이메일
            $("#email").val(docInfo["email"]);
//             }
//             $("#headType").val( docInfo["headType"] );
//             onHeadTypeChanged();
            //심의
            $("#teamLeader").val(docInfo["teamLeader"]);
            $("#teamLeaderName").val(docInfo["teamLeaderName"]);
            //심의 직위
            $("#teamLeaderGradeName").val(docInfo["teamLeaderGradeName"]);
            //승인
            var director = docInfo["director"];
            //전결
            // if (director == 3) {
            //     director = 1;
            // }
            $("#director").val(director).prop("selected", true);
            onDirectorChange();
            //승인 명
            $("#directorName").val(docInfo["directorName"]);
            //내용
            // CKEDITOR.instances.txtContent.setData(docInfo["content"]);
            editor.openHTML(docInfo["content"]);

            //붙임 파일 목록
            $("#divAttachedFileList").empty();
            if (result["fileList"].length > 0) {
                $(result["fileList"]).each(function(i) {
                    var fileInfo = $(this)[0];
                    $("#divAttachedFileList").append(
                        '<p id="pAttachedFile_' + fileInfo["fileNo"] + '">' + 
                        '<span>' + fileInfo["fileName"] + '</span>' +
                        '<input type="hidden" name="attachedFile[]" value="' + fileInfo["fileNo"] + '" /> ' + 
                        '<img onclick="javascript:delAddFile(\'\', ' + fileInfo["fileNo"] + ');" src="images/em_cross.png" title="파일 삭제" style="verical-align: center; padding: 0 4px; cursor: pointer;" />' + 
                        '</p>'
                    );
                });
            }

            //새 작성, 임시저장일 경우
            if (docInfo["docState"] == "" || docInfo["docState"] == "1") {
                //공문서 대체 문서 업로드 숨기기
                $("#tblOfficialDocFile").hide();
            }
            else {
                //공문서 대체 문서 업로드 표시
                $("#tblOfficialDocFile").show();
            }

            $("#btnSaveOfficialDoc").prop('disabled', false);
            //새작성일 경우
            if (docInfo["docState"] == "") {
                //취소 버튼 숨기기
                $("#btnShowOfficialDoc").hide();
            }
            else {
                //취소 버튼 보이기
                $("#btnShowOfficialDoc").show();
            }

            //공문서 상세 보기 란 숨기기
            $("#divDoc table .show").hide();
            $(".showFile").hide();
            //공문서 편집 란 표시
            $("#divDoc table .edit").show();
            //공문서 상세 표시
            $("#divDoc").show();
        },
        error: function(request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//User 초기화
function clearUserList() {
    $('#gridUserList').jqxGrid('clear');
    // $("#gridUserList").jqxGrid('clearSelection');
    // $('#gridUserList').jqxGrid('gotopage', 0);
    // var filterGroups = $('#gridUserList').jqxGrid('getfilterinformation');
    // if (filterGroups && filterGroups.length > 0) {
    //     $('#gridUserList').jqxGrid('clearfilters');
    // }
    // var sortinformation = $('#gridUserList').jqxGrid('getsortinformation');
    // if (sortinformation != null) {
    //     $('#gridUserList').jqxGrid('removesort');
    // }
}

//직원 선택
function selectUser(row) {
    var dataRecord = $("#gridUserList").jqxGrid('getrowdata', row);

    var target = $("#targetUser").val();
    //직인
    if (target == "seal") {
        var name = "";
        if ($("#seal option:selected").text() == "PM") {
            name += "프로젝트매니저  ";
        }
        else if ($("#seal option:selected").text() == "영업팀장") {
            name += "영업팀장 ";
        } else if ($("#seal option:selected").text() == "직접기입") {
            name += $("#directSeal").val() + " ";
        }
        name += dataRecord.userName;
        $("#sealName").val(name);
    }
    //작성자
    else if (target == "charge") {
        $("#unoInCharge").val(dataRecord.uno);
        $("#userName").val(dataRecord.userName);

         //지정된 작성자와 일치할 경우, 기존 작성자 정보로 설정
         if ($("#unoInCharge").val() == $("#pre_unoInCharge").val()) {
             //직위
             $("#gradeName").val( $("#pre_gradeName").val() );
             //전화번호
             $("#tel").val( $("#pre_tel").val() );
             //팩스번호
             $("#fax").val( $("#pre_fax").val() );
             //이메일
             $("#email").val( $("#pre_email").val() );
         }
         else {
             //직위
             $("#gradeName").val( dataRecord.gradeName );
             //전화번호
             $("#tel").val( dataRecord.tel );
             //팩스번호
             $("#fax").val( dataRecord.fax );
             //이메일
             $("#email").val( dataRecord.email );
         }
    }
    //심의
    else if (target == "leader") {
        $("#teamLeader").val(dataRecord.uno);
        $("#teamLeaderName").val(dataRecord.userName);
        $("#teamLeaderGradeName").val(dataRecord.gradeName);
    }
    //승인
    else if (target == "director") {
        $("#directorName").val(dataRecord.userName);
    }

    $('#mainForm').jqxValidator('hideHint', '#teamLeaderName');

    //직원 선택 창이 띄워져 있을 경우 닫기
    if ($('#dialogUser').jqxWindow('isOpen')) {
        $("#dialogUser").jqxWindow('close');
    }
}

//직인 변경
function onSealChanged() {
    var sealName = "";

    //직접기입
    if ($("#seal").val() == "9") {
        $("#directSeal").show();
    } else {
        $("#directSeal").hide();
        $("#directSeal").val('');
    }

    //대표이사
    if ($("#seal").val() == "1") {
        sealName = $("#ceo").val();
    }
    //회사명의
    else if ($("#seal").val() == "4") {
        sealName = "(주)하이테크엔지니어링";
    }
    //대표이사 외
//     else if ($("#seal").val() == "2") {
    else {
        sealName = "";
    }
    $("#sealName").val(sealName);
}

// function onHeadTypeChanged() {
//     var type = $("#headType").val();
//     var html = "";
//     if (type == "1") {
// //         type = "본부장";

//         html += "<option value='1'>전결</option>";
//         html += "<option value='2'>직접입력</option>";
//     }
//     else if (type == "2") {
// //         type = "사업팀장";

//         html += "<option value='3'>전결</option>";
//     }
//     $("#headTypeName").val($("#headType option:selected").text());
//     $("#spanDirectorType").html($("#directorType_" + type).text());
//     $("#directorType").val($("#directorType_" + type).text());
//     $("#director option").remove();
//     $("#director").append(html);
//     onDirectorChange();
// }

//승인 선택 시
function onDirectorChange() {
    var text = $( "#directorName" );
    //전결
    if ( $("#director option:selected").text() == "전결" ) {
        //승인 입력란 쓰기 불가
        text.css( "background-color", "#DDDDDD" );
        text.attr( "readonly", "readonly" );
        text.val( "" );
        $('#mainForm').jqxValidator('hideHint', '#directorName');
    }
    //직접입력
    else if ( $("#director option:selected").text() == "직접입력" ) {
        //승인 입력란 쓰기 가능
        $("#directorName").val('');
        text.css( "background-color", "#FFFFFF" );
        text.removeAttr( "readonly" );
    }
    //직원선택
    else if ( $("#director option:selected").text() == "직원선택" ) {
        //승인 입력란 쓰기 가능
        $("#directorName").val('');
        text.css( "background-color", "#FFFFFF" );
        text.removeAttr( "readonly" );
    }
}

//폐기
function onBtnDelOfficialDocClick() {
    $("#mode").val("DEL");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(), 
        dataType: "json", 
        success: function(result) {
            //공문서 목록 새로 고침
            $("#gridOfficialDocList").jqxGrid('updatebounddata', 'data');

            //공문서 상세 표시
            showOfficialDocDetail();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//붙임 파일 추가
var fId = 1;
function onAddFileClick() {
    $('#divAttachedFileList').append(
        '<p id="pAttachedFile_new_' + fId + '">' + 
        '<input type="file" name="newAttachedFile[]" style="width: 500px;" /> ' + 
        '<img onclick="javascript:delAddFile(\'new\', ' + fId + ');" src="images/em_cross.png" title="파일 삭제" style="verical-align: center; padding: 0 4px; cursor: pointer;" />' + 
        '</p>'
    );
    fId++;
}

//붙임 파일 삭제
function delAddFile(fileType, fId) {
    //추가된 파일 란 삭제
    if (fileType == "new") {
        $("#pAttachedFile_new_" + fId).remove();
    }
    //붙임 파일 삭제
    else {
        $("#pAttachedFile_" + fId).remove();
    }
}

//저장 버튼 클릭
function onBtnSaveOfficialDocClick() {
    //유효성 검사
    var valid = $('#mainForm').jqxValidator('validate');
    if (valid) {
        $("#btnSaveOfficialDoc").prop('disabled', true);

        $("#enforcementDate").val($("#div_enforcementDate").jqxDateTimeInput("getText"));
        // $("#content").val(CKEDITOR.instances.txtContent.getData());
        $("#content").val(editor.getPublishingHtml());
//         $("#uploadIframe").remove();

        var form = document.getElementById("mainForm");

        //첨부파일 전송용 iframe 생성
        var iframe = document.createElement("iframe");
        iframe.setAttribute("id", "uploadIframe");
        iframe.setAttribute("name", "uploadIframe");
        iframe.setAttribute("style", "width: 0; height: 0; border: none;");
        form.parentNode.appendChild(iframe);
        if(iframe.addEventListener) {
            iframe.addEventListener("load", onBtnGoDetailClick, true);
        }
        else if(iframe.attachEvent) {
            iframe.attachEvent("onload", onBtnGoDetailClick);
        }

        $("#mode").val("SAVE");
        form.action = "document/official_document_list.php";
        form.method = "POST";
        form.target = "uploadIframe";
        form.submit();
    }
}

//상세 보기로 이동
function onBtnGoDetailClick() {
    var iframe = document.getElementById("uploadIframe");
    var doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document;
    //저장 후 결과 취득
    var innerHTML = doc.body.innerHTML;
    if (innerHTML != null && innerHTML != "") {
        try {
            var result = JSON.parse(innerHTML);

            //공문서 고유번호
            $("#docNo").val(result.docNo);

            $("#gridOfficialDocList").jqxGrid('clearselection');
            //공문서 목록 새로 고침
            $("#gridOfficialDocList").jqxGrid('updatebounddata', 'data');
            //공문서 상세보기
            var index = $('#gridOfficialDocList').jqxGrid('getrowboundindexbyid', $("#docNo").val());
            $('#gridOfficialDocList').jqxGrid('selectrow', index);

            $("#officialDocFile").val("");
        }
        catch(err) {
            alert("에러가 발생했습니다. 관리자에게 문의 바랍니다.");
            return;
        }
    }
}

//발행 버튼 클릭
function onBtnSendOfficialDocClick() {
    $("#mode").val("SEND");
    $.ajax({ 
        type: "POST", 
        url: "document/official_document_list.php", 
        data: $("#mainForm").serialize(), 
        dataType: "json", 
        success: function(result) {
            //공문서 목록 새로 고침
            $("#gridOfficialDocList").jqxGrid('updatebounddata', 'data');

            //공문서 상세 표시
            showOfficialDocDetail();
        },
        error: function(request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//공문서 PDF 파일 다운로드
function onOfficialDocPdfClick(mode) {
    $("#mode").val(mode);
    // $("#content").val(CKEDITOR.instances.txtContent.getData());
    $("#content").val(editor.getPublishingHtml());
    
    var form = document.getElementById("mainForm");
    form.target = "documentPDF";
    form.method = "POST";
    form.action = "document/official_document_list_download_pdf.php";

    var map = window.open("", "documentPDF");

    if (map) {
        form.submit();

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) {
            setTimeout(function() {
                map.close();
            }, 5000);
        }
    } 
    else {
        alert('You must allow popups for this map to work.');
    }
//     window.open("../timesheet/document/official_document_list_download_pdf.php?mode=DOWNLOAD&docNo=" + $("#docNo").val());
}

//공문서 일괄 다운로드
function onBtnDownloadOfficialDocListClick() {
    window.open("document/official_document_list_excel.php?yearText=" + $("#year option:selected").text() + "&year=" + $("#year").val() + "&type=" + $("input[type='radio'][name='type']:checked").val());
}

//최신 공문서 다운로드
function onBtnDownloadOfficialDocClick() {
    window.open("document/official_document_list_file_download.php?mode=official&docNo=" + $("#docNo").val());
}

//공문서 이력 다운로드
function onBtnDownloadOfficialDocHisClick() {
    window.open("document/official_document_list_file_download.php?mode=history&docHisNo=" + $("#docHisNo").val());
}

//직접기입 값 변경시
function onDirectSealChange() {
    var sealName = $("#sealName").val();
    var arrSeal = sealName.split(" ");
    var name = arrSeal[arrSeal.length - 1];

    var directSeal = $("#directSeal").val();

    sealName = directSeal + " " + name;

    $("#sealName").val(sealName);
}

// seal값에 따른 직원 정렬
function getUserGrid() {
    var cellsrenderer = function(row, columnfield, value, defaulthtml, columnSettings, rowData) {
        //퇴직일 경우 취소선 표시
        if (rowData.holdOffice == "N") {
            return '<span style="font-color: gray;margin: 4px; float: ' + columnSettings.cellsalign + '"><del>' + $.jqx.dataFormat.formatdate(value, columnSettings.cellsformat) + '</del></span>';
        }
    }

    //직원 목록
    var target = $("#targetUser").val();

    if(target == "seal") {
        var url = "document/official_document_list_user_data.php?seal=" + $("#seal").val();
    } else {
        var url = "document/official_document_list_user_data.php";
    }
    // prepare the data
    var source =
    {
        datatype: "json",
        datafields: [
            { name: 'uno', type: 'int' },
            { name: 'userName', type: 'string' },
            { name: 'deptId', type: 'int' },
            { name: 'deptFullName', type: 'string' },
            { name: 'gradeId', type: 'string' },
            { name: 'gradeName', type: 'string' },
            { name: 'tel', type: 'string' },
            { name: 'fax', type: 'string' },
            { name: 'email', type: 'string' },
            { name: 'holdOffice', type: 'string' }
        ],
        id: 'uno',
        url: url
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    // $("#gridUserList").jqxGrid({source: dataAdapter});

    $("#gridUserList").jqxGrid({
        width: 750,
        source: dataAdapter,
        sortable: true,
        pageable: true,
        pagermode: 'simple',
        showfilterrow: true,
        filterable: true,
        autoheight: true,
        columnsresize: true,
        selectionmode: 'singlerow',
        columns: [
            { text: '직원명', dataField: 'userName', width: 120, cellsrenderer: cellsrenderer },
            { text: '부서명', dataField: 'deptFullName', cellsrenderer: cellsrenderer },
            { text: '직위', dataField: 'gradeName', width: 100, cellsrenderer: cellsrenderer }
        ]
    });
    $('#gridUserList').on('rowdoubleclick', function(event) { 
        selectUser(event.args.rowindex);
    });
    $("#btnSelectUser").on("click", function() {
        var idx = $('#gridUserList').jqxGrid('selectedrowindex');
        if (idx < 0) {
            return;
        }
        selectUser(idx);
    });
}
</script>
<table style="width: 1200px;">
    <tbody>
        <tr>
            <td style="width:15px;vertical-align:middle;"><img src="images/common/a_title.gif" height="28px"></td>
            <td style="vertical-align:bottom;"><span class="HeadTitle">공문서 목록</span></td>
        </tr>
    </tbody>
</table>

<hr class="hrContentsBorder" />

<table>
    <tbody>
        <tr>
            <td style='height: 20px;'>
                <div id='resultMsg' class='ordinaryMsg'></div>
            </td>
        </tr>
    </tbody>
</table>

<form id="mainForm" name="mainForm" enctype="multipart/form-data" >
<div style="float: left;">
<table style="margin-left: 5px;">
    <colgroup>
        <col style="width: 100px" />
        <col style="width: 250px" />
        <col style="width: 150px" />
    </colgroup>
    <tbody>
        <tr>
            <td>
                <select id="year" name="year" style="width: 90px;">
                    <option value="">전체</option>
                </select>
            </td>
            <td>
                <!-- <select id="type" name="type" style="width: 280px;">
                    <option value="GENERAL">일반공문</option>
                    <option value="ESTABLISHMENT">기성청구용 공문</option>
                </select> -->
                <input type="radio" id="type_general" name="type" value="GENERAL" checked="checked" /><label for="type_general">일반공문</label>
                <input type="radio" id="type_establishment" name="type" value="ESTABLISHMENT" /><label for="type_establishment">기성청구용 공문</label>
            </td>
            <td>
                <button type="button" id="btnDownloadOfficialDocList" name="btnDownloadOfficialDocList" class="btnPlain">공문서 일괄 다운로드</button>
            </td>
        </tr>
    </tbody>
</table>
<br />
<div id="gridOfficialDocList"></div>
<br />
<table style="margin-left: 5px;">
    <colgroup>
        <col style="width: 250px" />
        <col style="width: 250px" />
    </colgroup>
    <tbody>
        <tr>
            <td>
                <button type="button" id="btnAddOfficialDoc" name="btnAddOfficialDoc" class="btnNormal" >새 공문서 작성</button>
            </td>
            <td style="text-align: right;">
                <button type="button" id="btnShowOfficialDocHistory" name="btnShowOfficialDocHistory" class="btnLogin" style="display: none;" >공문서 편집 이력 보기</button>
            </td>
        </tr>
    </tbody>
</table>
</div>
<div id="divDoc" style="float: left;margin-left: 12px;display: none;">
<table>
    <colgroup>
        <col style="width: 80px;" />
        <col style="width: 500px;" />
    </colgroup>
    <tbody>
        <tr>
            <th>JOB :</th>
            <td>
                <div id="ddlJobList">
                    <div id="gridJobList"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<br />
<div style="border: 1px solid #333333;padding:5px 20px;">
<table>
    <colgroup>
        <col style="width:  60px" />
        <col style="width: 245px" />
        <col style="width:  60px" />
        <col style="width: 245px" />
    </colgroup>
    <tbody>
        <tr>
            <td colspan="4" style="text-align: center;height: 60px;">
                <!-- <img src="images/logo/hi-tech.gif" width="30" height="26" style="vertical-align: middle;" /> -->
                <img src="images/logo/hi-tech_logo_2021.png" width="175" style="vertical-align: middle;" />
                <div style="font-family:'Verdana';color:#595959">More Sustainable Engineering Company</div>
            </td>
        </tr>
        <tr style="font-size: 14px;" class="show">
            <td>수신</td>
            <td colspan="3">
                <span id="txt_cc" style="display: inline-block; width: 550px;" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;" class="edit">
            <td>수신</td>
            <td colspan="3">
                <input type="text" id="cc" name="cc" maxlength="1000" style="width: 550px" />
            </td>
        </tr>
        <tr style="font-size: 14px;" class="show">
            <td>참조</td>
            <td colspan="3">
                <span id="txt_bcc" style="display: inline-block; width: 550px;" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;" class="edit">
            <td>참조</td>
            <td colspan="3">
                <input type="text" id="bcc" name="bcc" maxlength="1000" style="width: 550px" />
            </td>
        </tr>
        <tr style="font-size: 14px;" class="show">
            <td>문서번호</td>
            <td>
                <span id="txt_docCd" ></span>
            </td>
            <td>시행일</td>
            <td>
                <span id="txt_enforcementDate" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;" class="edit">
            <td>문서번호</td>
            <td>
                <input type="text" id="docCd_ym" name="docCd_ym" style="width: 50px;" readonly="readonly" /> -
                <input type="text" id="docCd_comp" name="docCd_comp" style="width: 50px;" /><span id="span_docCd_comp"> - </span>
                <input type="text" id="docCd_seq" name="docCd_seq" style="width: 50px;" readonly="readonly" />
            </td>
            <td>시행일</td>
            <td>
                <div id="div_enforcementDate"></div>
                <input type="hidden" id="enforcementDate" name="enforcementDate" />
            </td>
        </tr>
        <tr style="font-size: 14px;">
            <td>제목</td>
            <td colspan="3" class="show">
                <span id="txt_title" style="display: inline-block; width: 550px;" ></span>
            </td>
            <td colspan="3" class="edit">
                <input type="text" id="title" name="title" maxlength="500" style="width: 550px" />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr />
            </td>
        </tr>
        <tr class="show">
            <td colspan="4">
                <span id="txt_content" style="display: inline-block;width: 620px;magin:0;padding:0;" ></span>
            </td>
        </tr>
        <tr class="edit">
            <td colspan="4">
                <div style='width:620px;' id="divContent" >
                    <!-- <textarea id='txtContent' name='txtContent' rows='10'></textarea> -->
                    <div id="txtContent"></div>
                    <input type="hidden" id="content" name="content" />
                </div>
            </td>
        </tr>
        <tr class="showFile">
            <td style="vertical-align: top;">붙임</td>
            <td colspan="3">
                <span id="txt_attachedFile" style="display: inline-block; width: 550px;" ></span>
                <br /><br />
            </td>
        </tr>
        <tr class="edit">
            <td style="vertical-align: top;">붙임</td>
            <td colspan="3">
                <button type='button' id='btnAddFile' name='btnAddFile' class='btnPlain'>파일 추가</button>
                <br />
                <div id="divAttachedFileList"></div>
            </td>
        </tr>
        <tr class="show">
            <td colspan="4" style="text-align: center;">
                <span id="txt_sealName" style="font-size: 16px; font-weight: bold;" ></span>
            </td>
        </tr>
        <tr class="edit">
            <td colspan="4" style="text-align: center;">
                <select id="seal" name="seal"></select>
                <input type="text" id="directSeal" name="directSeal" style="display:none" />
                <input type="text" id="sealName" name="sealName" readonly="readonly" />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table class="footer">
                    <colgroup>
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                    </colgroup>
                    <tbody>
                        <tr class="show">
        <!--                     <td>담당자</td> -->
                            <td>작성자</td>
                            <td>
                                <span id="txt_userName" ></span>
                            </td>
        <!--                     <td>
                                <span id="txt_headType" ></span>
                            </td> -->
                            <td>심의</td>
                            <td>
                                <span id="txt_teamLeaderName" ></span>
                            </td>
        <!--                     <td>
                                <span id="txt_directorType"></span>
                            </td> -->
                            <td>승인</td>
                            <td>
                                <span id="txt_director" ></span>
                            </td>
                        </tr>
                        <tr class="edit">
        <!--                     <td>담당자</td> -->
                            <td>작성자</td>
                            <td>
                                <input type="text" id="userName" name="userName" style="width: 130px;" />
                                <input type="hidden" id="gradeName" name="gradeName" />
                                <input type="hidden" id="unoInCharge" name="unoInCharge" />
                            </td>
        <!--                     <td>
                                <select id="headType" name="headType"></select>
                                <input type="hidden" id="headTypeName" name="headTypeName" />
                            </td> -->
                            <td>심의</td>
                            <td>
                                <input type="hidden" id="teamLeader" name="teamLeader" />
                                <input type="text" id="teamLeaderName" name="teamLeaderName" style="width: 130px;" />
                                <input type="hidden" id="teamLeaderGradeName" name="teamLeaderGradeName" />
                            </td>
        <!--                     <td>
                                <span id="spanDirectorType"></span>
                                <input type="hidden" id="directorType" name="directorType" />
                            </td> -->
                            <td>승인</td>
                            <td>
                                <select id="director" name="director" style="width: 50px;" onchange="javascript:onDirectorChange();">
                                    <option value='1'>전결</option>
                                    <option value='3'>직원선택</option>
                                    <option value='2'>직접입력</option>
                                </select>
                                <input type="text" id="directorName" name="directorName" maxlength="100" style="width: 85px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><span id="addr" ></span></td>
                            <td colspan="2"><span id="homepage" ></span></td>
                        </tr>
                        <tr class="show">
                            <td>전화번호</td>
                            <td>
                                <span id="txt_tel" ></span>
                            </td>
                            <td>팩스번호</td>
                            <td>
                                <span id="txt_fax" ></span>
                            </td>
                            <td>이메일</td>
                            <td>
                                <span id="txt_email" ></span>
                            </td>
                        </tr>
                        <tr class="edit">
                            <td>전화번호</td>
                            <td>
                                <input type="text" id="tel" name="tel" maxlength="20" style="width: 130px" />
                            </td>
                            <td>팩스번호</td>
                            <td>
                                <input type="text" id="fax" name="fax" maxlength="20" style="width: 130px" />
                            </td>
                            <td>이메일</td>
                            <td>
                                <input type="text" id="email" name="email" maxlength="100" style="width: 130px" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</div>
<br />
<table id="tblOfficialDocFile" style="display: none;">
    <colgroup>
        <col style="width: 150px;" />
        <col style="width: 500px;" />
    </colgroup>
    <tbody>
        <tr>
            <th style="background-color:Violet">공문서 대체 문서 업로드</th>
            <td><input type="file" id="officialDocFile" name="officialDocFile" style="width: 480px;"/></td>
        </tr>
        <tr>
            <td colspan="2" class="errorMsg">* 부득이하게 별도의 파일로 공문서 작성 시 이력 관리를 위해 실제 제출한 공문서를 첨부해 주시기 바랍니다.</td>
        </tr>
    </tbody>
</table>
<table style="margin-left: 5px;">
    <colgroup>
        <col style='width: 153px;'/>
        <col style='width: 152px;'/>
        <col style='width: 152px;'/>
        <col style='width: 153px;'/>
    </colgroup>
    <tbody>
        <tr class="show">
            <td>
                <button type="button" id='btnDelOfficialDoc' name='btnDelOfficialDoc' class='btnSave' >폐기</button>
            </td>
            <td style="text-align: center;">
                <button type="button" id='btnSendOfficialDoc' name='btnSendOfficialDoc' class='btnSave' >발행</button>
            </td>
            <td style="text-align: center;">
                <button type="button" id='btnEditOfficialDoc' name='btnEditOfficialDoc' class='btnSave' >편집</button>
            </td>
            <td style="text-align: right;">
                <button type="button" id="btnDownloadOfficialDoc" name="btnDownloadOfficialDoc" class="btnSave" >공문서 다운로드</button>
            </td>
        </tr>
    </tbody>
</table>
<table>
    <colgroup>
        <col style='width: 153px;'/>
        <col style='width: 152px;'/>
        <col style='width: 152px;'/>
        <col style='width: 153px;'/>
    </colgroup>
    <tbody>
        <tr class="edit">
            <td>
                <button type="button" id='btnClearOfficialDoc' name='btnClearOfficialDoc' class='btnSave' >CLEAR</button>
            </td>
            <td style="text-align: center;">
                <button type="button" id="btnPreviewOfficialDoc" name="btnPreviewOfficialDoc" class="btnSave" >미리보기</button>
            </td>
            <td style="text-align: center;">
                <button type="button" id='btnSaveOfficialDoc' name='btnSaveOfficialDoc' class='btnSave' >저장</button>
            </td>
            <td style="text-align: right;">
                <button type="button" id='btnShowOfficialDoc' name='btnShowOfficialDoc' class='btnSave' style="display: none;" >취소</button>
            </td>
        </tr>
    </tbody>
</table>
</div>

<div id="historyDialog" style="clear: both">
<div id="historyDialogHeader">
<span>공문서 편집 이력</span>
</div>
<div id="historyDialogContents" style="padding: 20px;">
<div style="float: left;">
<div id="gridOfficialDocHistoryList"></div>
</div>
<div id="divDocHistory" style="float: left;margin-left: 20px;display: none;">
<table>
    <tbody>
        <tr style="height: 35px;">
            <td style="width: 610px; text-align: right;">
                <button type="button" id="btnDownloadOfficialDocHis" name="btnDownloadOfficialDocHis" class="btnLogin" >해당 공문서 다운로드</button>
            </td>
        </tr>
    </tbody>
</table>
<br />
<div style="border: 1px solid #333333;padding:5px 20px;">
<table>
    <colgroup>
        <col style="width:  60px" />
        <col style="width: 245px" />
        <col style="width:  60px" />
        <col style="width: 245px" />
    </colgroup>
    <tbody>
        <tr>
            <td colspan="4" style="text-align: center;height: 60px;">
                <img src="images/logo/hi-tech_logo_2021.png" width="154" height="22" style="vertical-align: middle;" />
            </td>
        </tr>
        <tr style="font-size: 14px;">
            <td>수신</td>
            <td colspan="3">
                <span id="his_cc" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;">
            <td>참조</td>
            <td colspan="3">
                <span id="his_bcc" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;">
            <td>문서번호</td>
            <td>
                <span id="his_docCd" ></span>
            </td>
            <td>시행일</td>
            <td>
                <span id="his_enforcementDate" ></span>
            </td>
        </tr>
        <tr style="font-size: 14px;">
            <td>제목</td>
            <td colspan="3">
                <span id="his_title" ></span>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <span id="his_content" style="display: inline-block;width: 620px;magin:0;padding:0;" ></span>
            </td>
        </tr>
        <tr class="his_file">
            <td style="vertical-align: top;">붙임</td>
            <td colspan="3">
                <span id="his_attachedFile" ></span>
                <br />
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">
                <span id="his_sealName" style="font-size: 16px;font-weight: bold;" ></span>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <hr />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table class="footer">
                    <colgroup>
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                        <col style="width:  55px" />
                        <col style="width: 146px" />
                    </colgroup>
                    <tbody>
                        <tr>
        <!--                     <td>담당자</td> -->
                            <td>작성자</td>
                            <td>
                                <span id="his_userName" ></span>
                            </td>
        <!--                     <td><span id="his_headType" ></span></td> -->
                            <td>심의</td>
                            <td>
                                <span id="his_teamLeaderName" ></span>
                            </td>
        <!--                     <td><span id="his_directorType" ></span></td> -->
                            <td>승인</td>
                            <td>
                                <span id="his_directorName" ></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><span id="his_addr" ></span></td>
                            <td colspan="2"><span id="his_homepage" ></span></td>
                        </tr>
                        <tr>
                            <td>전화번호</td>
                            <td>
                                <span id="his_tel" ></span>
                            </td>
                            <td>팩스번호</td>
                            <td>
                                <span id="his_fax" ></span>
                            </td>
                            <td>이메일</td>
                            <td>
                                <span id="his_email" ></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</div>
<br />
</div>
</div>
</div>

<div id="dialogUser">
<div id="dialogUserHeader">
직원 선택
</div>
<div id="dialogUserContents">
<br />
<div id="gridUserList"></div>
<br />
<table>
    <colgroup>
        <col style="width: 370px;" />
        <col style="width: 370px;" />
    </colgroup>
    <tbody>
        <tr>
            <td style="width: 10px;">
                <button type="button" id="btnSelectUser" name="btnSelectUser" class="btnSave" >반영</button>
            </td>
            <td style="text-align: right;">
                <button type="button" id="btnCloseUser" name="btnCloseUser" class="btnSave" >닫기</button>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>

<input type='hidden' id='mode' name='mode' />
<input type='hidden' id='docNo' name='docNo' />
<input type='hidden' id='docHisNo' name='docHisNo' />
<input type='hidden' id='jno' name='jno' />
<input type="hidden" id="pre_unoInCharge" name="pre_unoInCharge" />
<input type="hidden" id="pre_gradeName" name="pre_gradeName" />
<input type="hidden" id="pre_tel" name="pre_tel" />
<input type="hidden" id="pre_fax" name="pre_fax" />
<input type="hidden" id="pre_email" name="pre_email" />
<input type="hidden" id="ceo" name="ceo" />
<input type="hidden" id="targetUser" name="targetUser" />
<input type="hidden" id="editable" name="editable" />
</form>

