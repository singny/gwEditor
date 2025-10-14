<script type="text/javascript" src="/vendor/ckeditor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/js/nb.js"></script>
<script>
$(document).ready(function(){
    //작업모드
    $("#mode").val("INIT");
    $("#menuId").val('<?php echo $_POST["menuId"]; ?>');
    $("#parMenuId").val('<?php echo $_POST["parMenuId"]; ?>');
    $("#articleMenuId").val('<?php echo $_POST["menuId"]; ?>');
    $.ajax({ 
        type: "POST", 
        url: "/gw/nb/nb0200100.php", 
        data: $("#mainForm").serialize(), 
        dataType: "json", 
        success: function(result) {
            //새게시글 표시기간
            $("#termNewArticle").val(result["termNewArticle"]);
            //공지, 게시 알림설정 체크박스 자동 체크여부
            $("#initialChkAll").val(result["initialChkAll"]);
            //공지함에서 작성글 삭제 시 첨부파일 완전삭제 여부
            $("#deleteAttach").val(result["deleteAttach"]);

            //첨부파일 최대 크기
            $("#maxFileSize").val(result["maxFileSize"]);

            //검색
            var html = "";
            $(result["searchKindList"]).each(function(i, info) {
                html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
            });
            $("#ddlSearchKind").append(html);

            $("#txtSearchValue").data('oldVal', "");

            var authList = result["authList"];
            if ($.inArray('Save', authList) > -1 || $.inArray('Man', authList) > -1) {
                $("#btnAddBoard").closest("div").show();
            }
            if ($.inArray('Man', authList) > -1) {
                $("#btnShowMoveTray").closest("div").show();
                $("#btnBatchDelArticle").closest("div").show();
                $("#isManager").val("Y");
                $('#tblBoardList thead th').first().show();
            }
            else {
                $('#tblBoardList thead th').first().remove();
            }

            //게시함에 열람자 확인페이지를 볼 수 있도록 할 것인지 여부
            $("#chkReadBoard").val(result["chkReadBoard"]);

            //게시함 목록
            $("#boardTrayList").append(result["boardTrayList"]);
            $("#boardTrayList > ul").treed();
            $("#boardTrayList li").trigger('click');

            //상시공지여부
            var html = "";
            $.each(result["ynList"], function(i, info) {
                html += '<div class="form-check-inline">';
                html += '<label class="form-check-label">';
                html += '<input type="radio" class="form-check-input" id="alwaysTopYn_' + info["key"] + '" name="alwaysTopYn" value="' + info["key"] + '" />';
                html += info["val"];
                html += '</label>';
                html += '</div>';
            });
            $("#divAlwaysTop").append(html);

            //알림
            html = "";
            var hidHtml = "";
            $.each(result["alarmList"], function(i, info) {
                html += '<div class="form-check-inline">';
                html += '<label class="form-check-label">';
                html += '<input type="checkbox" class="form-check-input" id="chkAlarm_' + info["key"] + '" name="alarmType[]" value="' + info["key"] + '" >';
                html += info["val"];
                html += '</label>';
                html += '</div>';

                hidHtml += '<input type="hidden" id="alarmTypeList_' + info["key"] + '" name="alarmTypeList[]" value="' + info["key"] + '" >';
            });
            $("#divAlarm").append(html);
            $("#mainForm").append(hidHtml);

            //댓글등록제한
            html = "";
            $.each(result["ynList"], function(i, info) {
                html += '<div class="form-check-inline">';
                html += '<label class="form-check-label">';
                html += '<input type="radio" class="form-check-input" id="commentYn_' + info["key"] + '" name="commentYn" value="' + info["key"] + '" />';
                html += info["val"];
                html += '</label>';
                html += '</div>';
            });
            $("#divComment").append(html);

            //댓글알림
            html = "";
            hidHtml = "";
            $.each(result["commentAlarmList"], function(i, info) {
                html += '<div class="form-check-inline">';
                html += '<label class="form-check-label">';
                html += '<input type="checkbox" class="form-check-input" id="chkCommentAlarm_' + info["key"] + '" name="commentAlarmType[]" value="' + info["key"] + '" >';
                html += info["val"];
                html += '</label>';
                html += '</div>';

                hidHtml += '<input type="hidden" id="commentAlarmTypeList_' + info["key"] + '" name="commentAlarmTypeList[]" value="' + info["key"] + '" >';
            });
            $("#divCommentAlarm").append(html);
            $("#mainForm").append(hidHtml);

            //인쇄가능여부
            html = "";
            $.each(result["ynList"], function(i, info) {
                html += '<div class="form-check-inline">';
                html += '<label class="form-check-label">';
                html += '<input type="radio" class="form-check-input"  id="printYn_' + info["key"] + '" name="printYn" value="' + info["key"] + '" />';
                html += info["val"];
                html += '</label>';
                html += '</div>';
            });
            $("#divPrint").append(html);

            addAttachedFile();

            //상시공지여부
            if (result["authAlwaysTop"] == "Y") {
                $("input:radio[name='alwaysTopYn']").prop("disabled", false);
            }
            else {
                //사용
                $("#alwaysTopYn_1").prop("disabled", true);
                //미사용
                $("#alwaysTopYn_0").prop("checked", true);
            }
            //공개설정
            if (result["eachInqYn"] == "Y") {
                $("#publicScopeNms").closest("div.row").show();
            }
            //댓글조회권한
            if (result["commentInqYn"] == "Y") {
                $("#roleCommentNms").closest("div.row").show();
            }
            //댓글 사용여부
            if (result["commentYn"] == "Y") {
                $("#rowEditComment").show();
            }
            //메뉴타이틀
            $("#menuTitle").val(result["title"]);
        },
        complete: function() {
            getSubMenuCnt();
            onConditionChange();

            addValidateElementToInputs("modalEditBoard");
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });

    //이동 창 닫을 경우
    $("#modalMoveBoard").on('hide.bs.modal', function() {
        if ($("#moveMenuId").val() != "") {
            $("#subMenu_" + $("#moveMenuId").val()).find("a").removeClass("active");
            $("#moveMenuId").val("");
        }
    });
    //편집창 열 경우
    $("#modalEditBoard").on('shown.bs.modal', function () {
        $("#editingArticle").val("Y");
    });
    //편집창 닫을 경우
    $("#modalEditBoard").on('hide.bs.modal', function () {
        $("#editingArticle").val("N");
        clearEditBoard();

        //유효성 검사 지우기
        clearValidateElementOfInputs("modalEditBoard");
        $("#txtContent").removeClass('is-valid is-invalid');
        $("#txtContent").closest(".form-group").find(".invalid-feedback").html("");
        $("#mainForm").removeClass('was-validated');
    });
    //상세 닫으면 새로고침
    $("#modalDetailBoard").on('hide.bs.modal', function () {
        onPageNoClick($("#pageNo").val(), "", true);
    });

    //IE
    if (!!navigator.userAgent.match(/Trident\/7\./)) {
        $("#modalMoveBoard").removeClass("fade");
        $("#modalEditBoard").removeClass("fade");
        $("#modalConfirmBatchDelBoard").removeClass("fade");
        $("#modalConfirmDelBoard").removeClass("fade");
    }

    var $th = $('#tblBoardList').find('thead th');
    $('#tblBoardList').closest("div.tableFixHead").on('scroll', function() {
        $th.css('transform', 'translateY('+ this.scrollTop +'px)');
    });

    var maxWidth = 1100;
    CKEDITOR.replace('textareaContent', {
        width: '100%',
        height: "23rem",
//         resize_maxHeight: "395px",
        autoParagraph: false,
        allowedContent: true,
        enterMode : CKEDITOR.ENTER_BR, 
        removeButtons: '',
        removePlugins: 'elementspath',
        resize_enabled: false,
        extraPlugins : 'font,colorbutton,justify,tableresize,specialchar',
        specialChars : CKEDITOR.config.specialChars.concat( [ [ '&#8361;', '원화 기호' ] ] ),
        toolbar: [
//             { name: 'document', items: [ 'Source' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            { name: 'tools', items: [ 'Maximize' ] },
            '/',
            { name: 'styles', items: [ 'Font', 'FontSize' ] },
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] }
        ],
        filebrowserImageUploadUrl: '/gw/cm/cm_file_upload.php?moduleName=NB&type=Images',
//         forcePasteAsPlainText : true,
        contentsCss: 'body{font-family: Arial,"Malgun Gothic",sans-serif;font-size: 14px;margin: 20px;}table{border-collapse: collapse;}',
        on: {
            instanceReady: function(evt) {
                CKEDITOR.instances.textareaContent.widgets.registered.uploadimage.onUploaded = function(e) {
                    var img = this.parts.img.$;
                    var width = e.responseData.width||img.naturalWidth;
                    var height = e.responseData.height||img.naturalHeight;
                    if (width > maxWidth) {
                        height = Math.round(maxWidth * (height / width));
                        width = maxWidth; 
                    }
                    this.replaceWith( '<img src="' + e.url + '" ' + 'width="' + width + '" ' + 'height="' + height + '">' );
                }
            }
        }
    });
    CKEDITOR.on('dialogDefinition', function(evt) {
        // Take the dialog name and its definition from the event data.
        var dialogName = evt.data.name;
        var dialog = evt.data.definition.dialog;

        dialog.on('show', function () {
            //이미지 정보 탭
            if (dialogName == 'image') {
                //너비
                var ele = this.getContentElement('info', 'txtWidth');
                //유효성 검사
                ele.validate = function(e) {
                    var y=/(^\s*(\d+)((px)|\%)?\s*$)|^$/i;
                    var a=this.getValue().match(y);
                    a=!(!a||0===parseInt(a[1],10));
                    if (a) {
                        if(ele.getValue() > maxWidth) {
                            alert("이미지 너비는 " + maxWidth + "px 이하로 지정해주세요.");
                            a = !a;
                        }
                    }
                    else {
                        alert(CKEDITOR.instances.textareaContent.lang.common.invalidLength.replace("%1",CKEDITOR.instances.textareaContent.lang.common.width).replace("%2","px, %"));
                    }
                    return a;
                }
            }
        });
    });
    CKEDITOR.instances.textareaContent.on('change', function() {
        if ($("#editingArticle").val() == "Y") {
//         $("#detectEditArticle").val("Y");
            validateContent();
        }
    });
    CKEDITOR.instances.textareaContent.on('paste', function (evt) {
        evt.data.dataValue = evt.data.dataValue.replace(/<span[^>]*?>/g, '');
        evt.data.dataValue = evt.data.dataValue.replace(/<font[^>]*?>/g, '');
    });

    //이동 버튼
    $("#btnMoveArticle").on("click", onBtnMoveArticleClick);
    //일괄결재 버튼
    $("#btnConfirmBatchDeleteBoard").on("click", onBtnBatchDelBoardClick);
    //검색조건 - 분류
    $("#ddlSearchKind").on("change", onConditionChange);
    //검색조건 - 입력란
    $("#txtSearchValue").on("keyup", function(e) {
        var cd = e.which || e.keyCode;
        //Enter 키
        if (cd == 13) {
            onBtnSearchBoardClick();
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    //검색 버튼
    $("#btnSearchBoard").on("click", onBtnSearchBoardClick);
    //신규 버튼
    $("#btnAddBoard").on("click", onBtnAddBoardClick);
    //저장 버튼
    $("#btnSaveBoard").on("click", onBtnSaveBoardClick);
    //작성취소 버튼
    $("#btnCancelBoard").on("click", onBtnCancelBoardClick);
    //작성취소 버튼
    $("button[name='btnCloseEditBoard']").on("click", onBtnCloseEditBoardClick);

    //날짜 min, max값 넣기
    dateMinMaxAppend();
});

//검색 버튼 클릭
function onBtnSearchBoardClick() {
    var elem = $("#txtSearchValue");
    elem.val(elem.val().trim());
    if (elem.data("oldVal") != elem.val()) {

        onConditionChange();
    }
}

//조건 변경 시 검색
function onConditionChange() {
    var elem = $("#txtSearchValue");
    elem.val(elem.val().trim());
    elem.data('oldVal', elem.val());

    onPageNoClick(1, "", false);
}

function showInfoList(list) {
    $("#chkAll").prop("checked", false);
    $('#tblBoardList').closest('div.tableFixHead').scrollTop(0);
    $("#tblBoardList tbody").empty();
    var articleMenuId = $("#articleMenuId").val();
    var html = "";
    $(list).each(function(i, info) {
        html += '<tr class="row"';
//         if (info["delYn"] != "Y" && (info["alwaysTopYn"] == "1" || info["readYn"] == "N")) {
        if (info["delYn"] != "Y" && info["readYn"] == "N") {
            html += ' style="font-weight: bold;" ';
        }
        html += '>';
        if ($("#isManager").val() == "Y") {
            html += '<td class="col-md-1 col-1 col-w-chk">';
            html += '<div class="h-100 d-flex align-items-center">';
            if (info["depth"] == 0) {
                html += '<input type="checkbox" name="chkArticleId[]" value="' + info["articleId"] + '" onchange="onChkArticleIdClick()" />';
            }
            html += '</div>';
            html += '</td>';
        }
        html += '<td class="col-md-1 col-2">';
        html += '<div class="h-100 d-flex align-items-center">';
        if (info["alwaysTopYn"] == "1") {
            html += '<span class="badge badge-danger">상시공지</span>';
        }
        else {
            html += info["boardNo"];
        }
        html += '</div>';
        html += '</td>';
        // if ($("#isManager").val() == "Y") {
        //     html += '<td class="col-md-5 col-4">';
        // }
        // else {
        //     html += '<td class="col-md-6 col-5">';
        // }
        html += '<td class="col-md d-none d-md-block notAlign text-ellipsis">';
        html += '<div class="h-100 d-flex align-items-center notAlign">';
        html += '<div class="ellipsisLongTxt">';
        for (var j = 0; j < info["depth"]; ++j) {
            html += '<i class="fa-solid fa-turn-up fa-rotate-90"></i>&nbsp;';
        }
        if (info["delYn"] == "Y") {
            html += "삭제된 게시글입니다.";
        }
        else {
            if (info["isNew"] == "Y") {
                html += '<span class="badge badge-info">new</span> ';
            }
            html += '<a href="javascript:void(0);" onclick="onBtnDetailBoardClick(\'' + info["articleId"] + '\', \'' + articleMenuId + '\')">';
            html += info["subject"];
            if (Number(info["isAttach"]) > 0) {
                html += '&nbsp;&nbsp;<i class="fa-solid fa-floppy-disk"></i>';
            }
            html += '</a>';
        }
        html += '</div>';
        html += '</div>';
        html += '</td>';
        html += '<td class="col-md-block d-md-none col notAlign text-ellipsis">';
        for (var j = 0; j < info["depth"]; ++j) {
            html += '<i class="fa-solid fa-turn-up fa-rotate-90"></i>&nbsp;';
        }
        if (info["delYn"] == "Y") {
            html += "삭제된 게시글입니다.";
        }
        else {
            if (info["isNew"] == "Y") {
                html += '<span class="badge badge-info">new</span> ';
            }
            html += '<a href="javascript:void(0);" onclick="onBtnDetailBoardClick(\'' + info["articleId"] + '\', \'' + articleMenuId + '\')">';
            html += info["subject"];
            if (Number(info["isAttach"]) > 0) {
                html += '&nbsp;&nbsp;<i class="fa-solid fa-floppy-disk"></i>';
            }
            html += '</a>';
            html += '<div class="userDtMobile">' + info["writer"] + ' | ' + info["regDate"] +'</div>'
        }
        html += '</td>';
        html += '<td class="col-md-3 d-none d-md-block">';
        if (info["delYn"] == "N") {
            html += '<div class="h-100 d-flex align-items-center">';
            html += info["writer"] + ' (' + info["regDate"] + ')';
            html += '</div>';
        }
        html += '</td>';
        html += '<td class="col-md-1 d-none d-md-block">';
        if (info["delYn"] == "N") {
            html += '<div class="h-100 d-flex align-items-center">';
            if ($("#chkReadBoard").val() == "1") {
                html += '<a href="javascript:void(0);" onclick="onReadCntClick(' + info["articleId"] + ');">';
            }
            html += info["readCnt"];
            if ($("#chkReadBoard").val() == "1") {
                html += "</a>";
            }
            html += '</div>';
        }
        html += '</td>';
        html += '<td class="col-md-1 col-2 col-w-btn">';
        html += '<div class="h-100 d-flex align-items-center">';
        html += '<button type="button" class="btn btn-primary" onclick="onBtnDetailBoardClick(\'' + info["articleId"] + '\', \'' + articleMenuId + '\')"> 상세</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
    });

    $("#tblBoardList tbody").append(html);
    onAfterChkArticleClick();
}

//열람자 확인
function onReadCntClick(articleId) {
    $("#articleId").val(articleId);
    $("#articleType").val("board");

    readArticleList();
}

//공지함 선택
function selectSubmenu(menuId, leafYn, menuKind, step) {
    $("#moveMenuId").val(menuId);
}

//문서 이동
function onBtnMoveArticleClick() {
    $("#mode").val("MOVE");
    $.ajax({ 
        type: "POST", 
        url: "/gw/nb/nb0200100.php", 
        data: $("#mainForm").serialize(),
        dataType: "json",  
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            $("#modalMoveBoard").modal("hide");

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        complete: function() {
            getSubMenuCnt();
            onPageNoClick($("#pageNo").val(), "", true);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//체크박스 전체 선택/해제
function onChkAllArticleClick(obj) {
    onChkAllClick(obj, "chkArticleId");

    onAfterChkArticleClick();
}

//문서 별 체크박스 선택 변경 시
function onChkArticleIdClick() {
    whenChkClick_chkAll("chkArticleId", "chkAll");

    onAfterChkArticleClick();
}

function onAfterChkArticleClick() {
    if ($("input[type='checkbox'][name='chkArticleId[]']:checked").length > 0) {
        //문서이동 버튼
        $("#btnShowMoveTray").prop("disabled", false);
        //삭제 버튼
        $("#btnBatchDelArticle").prop("disabled", false);
    }
    else {
        //문서이동 버튼
        $("#btnShowMoveTray").prop("disabled", true);
        //삭제 버튼
        $("#btnBatchDelArticle").prop("disabled", true);
    }
}

//일괄 삭제 버튼 클릭
function onBtnBatchDelBoardClick() {
    //게시글 삭제
    $("#deleteType").val("M");
//     //답글 포함 게시글 삭제
//     $("#deleteType").val("R");
    $("#modalConfirmBatchDelBoard").modal("hide");

    $("#mode").val("BATCH_DEL");
    $.ajax({ 
        type: "POST", 
        url: "/gw/nb/nb0200100.php", 
        data: $("#mainForm").serialize(),
        dataType: "json",  
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        complete: function() {
            getSubMenuCnt();
            onPageNoClick($("#pageNo").val(), "", true);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//첨부파일 추가
function addAttachedFile() {
    var html = '';
    html += '<div class="input-group mb-2">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input" name="newAttachFile[]" onchange="onAttachFileChange(this)" />';
    html += '<label class="custom-file-label" for="customFile"><i class="fa-solid fa-cloud-arrow-up"></i> 파일을 선택하세요</label>';
    html += '</div>';
    html += '<div class="input-group-append">';
    html += '<button type="button" class="btn btn-secondary" onclick="javascript:delAttachedFile(this);">&times;</button>';
    html += '</div>';
    html += '</div>';

    $('#divNewAttachedList').append(html);
}

//첨부파일 삭제
function delAttachedFile(obj) {
    $(obj).closest('div.input-group').remove();
}

//첨부파일 선택 시
function onAttachFileChange(obj) {
    if ($("#maxFileSize").val() != "" && $("#maxFileSize").val() != "0") {
        var max = Number($("#maxFileSize").val()) * 1024 * 1024;
        var totalSize = 0;
        $("input[type='file'][name='attachFileSize[]']").each(function() {
            totalSize += Number($(this).val());
        });
        $("input[type='file'][name='newAttachFile[]']").each(function () {
            totalSize += $(this)[0].files[0].size;
        });
        if (totalSize > max) {
            $("#modalAlertMsg .modal-body").html("첨부 파일 최대 용량은 " + $("#maxFileSize").val() + "MByte 입니다");
            $("#modalAlertMsg").modal("show");
            $(obj).val("");
            return;
        }
    }
    var fileName = $(obj).val().split("\\").pop();
    $(obj).siblings(".custom-file-label").addClass("selected").html(fileName);
}

//게시 편집 초기화
function clearEditBoard() {
    $("#modalEditBoard").find("input[type='text']").val("");
    $("#modalEditBoard").find("input[type='date']").val("");
    $("#modalEditBoard").find("input[type='hidden']").val("");
    $("#modalEditBoard").find("input[type='checkbox']").prop("checked", false);
    CKEDITOR.instances.textareaContent.setData("");

    //첨부파일 지우기
    $("#divAttachedList").empty();
    $("#divNewAttachedList").empty();
    addAttachedFile();
}

//신규 버튼 클릭
function onBtnAddBoardClick() {
    $("#articleId").val("");
    $("#depth").val("0");
    $("#parArticleId").val("");
    //상시공지여부
    $("#alwaysTopYn_0").prop("checked", true);
    var d = new Date();
    d.setMonth(d.getMonth() + 1);
    var date = d.getFullYear();
    date += "-";
    if ((d.getMonth() + 1) < 10) {
        date += "0" + (d.getMonth() + 1);
    }
    else {
        date += (d.getMonth() + 1);
    }
    date += "-";
    if (d.getDate() < 10) {
        date += "0" + d.getDate();
    }
    else {
        date += d.getDate();
    }
    $("#alwaysTopDate").val(date);
    //공지, 게시 알림설정 체크박스 자동 체크
    if ($("#initialChkAll").val() == "Y") {
        $("input:checkbox[name='alarmType[]']").prop("checked", true);
        $("input:checkbox[name='commentAlarmType[]']").prop("checked", true);
    }
    //댓글등록제한
    $("#commentYn_1").prop('checked', true);
    //인쇄가능여부
    $("#printYn_1").prop("checked", true);

    $("#btnDelBoard").hide();
    $("#btnCancelBoard").hide();

    //모달 헤더
    $("#modalEditBoard .modal-title").text($("#menuTitle").val() + " 편집");

    $("#modalEditBoard").modal("show");
}

//수정 버튼 클릭
function onBtnEditBoardClick() {
    $("#modalDetailBoard").modal("hide");
    $("#btnCancelBoard").show();

    //작업모드
    $("#mode").val("EDIT");
    $.ajax({ 
        type: "POST", 
        url: "/gw/nb/nb0200100.php", 
        data: $("#mainForm").serialize(),
        dataType: "json",  
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            $("#modalEditBoard .modal-title").text(result["title"] + " 편집");

            var boardInfo = result["boardInfo"];

            var authList = result["authList"];
            if ($.inArray('Man', authList) > -1) {
                $("#btnDelBoard").show();
            }
            else if ($.inArray('Save', authList) > -1) {
                if (boardInfo["isWriter"]) {
                    $("#btnDelBoard").show();
                }
            }

            $("#depth").val(boardInfo["depth"]);
            //제목
            $("#subject").val(boardInfo["subject"]);
            //상시공지여부
            $("#alwaysTopYn_" + boardInfo["alwaysTopYn"]).prop('checked', true);
            //상시게시기간설정
            if (boardInfo["alwaysTopDate"] != "") {
                $("#alwaysTopDate").val(boardInfo["alwaysTopDate"]);
            }
            if (boardInfo["depth"] == "0") {
                $("#divAlwaysTop").closest("div.row").show();
            }
            else {
                $("#divAlwaysTop").closest("div.row").hide();
                //공개범위 숨기기
            }
            //댓글등록제한
            $("#commentYn_" + boardInfo["commentYn"]).prop('checked', true);
            //댓글알림
            $(boardInfo["commentAlarmType"]).each(function(i, val) {
                $("#chkCommentAlarm_" + val).prop('checked', true);
            });
            //인쇄가능여부
            $("#printYn_" + boardInfo["printYn"]).prop('checked', true);
            $("#preAlarmType").val(boardInfo["preAlarmType"]);
            //알림
            $(boardInfo["alarmType"]).each(function(i, val) {
                $("#chkAlarm_" + val).prop('checked', true);
            });
            //공개설정
            $("#publicScopeIds").val(boardInfo["publicScopeIds"]);
            $("#publicScopeNms").val(boardInfo["publicScopeNms"]);
            //댓글조회권한
            $("#roleCommentIds").val(boardInfo["roleCommentIds"]);
            $("#roleCommentNms").val(boardInfo["roleCommentNms"]);
            //내용
            CKEDITOR.instances.textareaContent.setData(boardInfo["content"]);

            //첨부파일
            if (Object.keys(result["attachFileList"]).length > 0) {
                html = "";
                $(result["attachFileList"]).each(function(i, info) {
                    html += '<div class="input-group mb-2">';
                    html += '<div class="form-control">';
                    html += info["fileName"];
                    html += '<input type="hidden" name="attachFileId[]" value="' + info["attachId"] + '" />';
                    html += '<input type="hidden" name="attachFile[]" value="' + info["realFileName"] + '" />';
                    html += '</div>';
                    html += '<div class="input-group-append">';
                    html += '<button type="button" class="btn btn-secondary" onclick="javascript:delAttachedFile(this);">&times;</button>';
                    html += '</div>';
                    html += '</div>';
                });
                $("#divAttachedList").append(html);
            }

            //답글 수
            if (boardInfo["replyCnt"] > 1) {
                $("#btnConfirmDeleteBoardAndReply").show();
            }
            else {
                $("#btnConfirmDeleteBoardAndReply").hide();
            }

            $("#modalEditBoard").modal("show");
        },
        complete: function() {
//             $("input:button[name='btnDetailAppDoc']").prop("disabled", false);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//저장
function onBtnSaveBoardClick() {
    if(validateInputs()) {
        $("#mode").val("SAVE");
        var formdata = new FormData($("#mainForm")[0]);
        var proceed = false; 
        $.ajax({ 
            type: "POST", 
            url: "/gw/nb/nb0200100.php", 
            data: formdata,
            dataType: "json", 
            contentType: false,
            processData: false,
            success: function(result) {
                //세션 만료일 경우
                if (result["session_out"]) {
                    //로그인 화면으로 이동
                    onLogoutClick();
                }

                proceed = result["proceed"];
                if (proceed) {
                    $("#modalEditBoard").modal("hide");

//                     onBtnDetailBoardClick(result["articleId"], $("#articleMenuId").val());
                }
                else {
//                     $("#resultSign").empty().html(result["msg"]).fadeIn();
//                     $("#resultSign").delay(5000).fadeOut();
                }
            },
            complete: function() {
                if (proceed) {
                    getSubMenuCnt();
                    onPageNoClick($("#pageNo").val(), "", true);
                }
            },
            error: function (request, status, error) {
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }
}

//유효성 검사
function validateInputs() {
    var valid = true;

    //제목
    valid = valid & validateElement("subject");

    //내용
    valid = valid & validateContent();

    return valid;
}

//내용 유효성 검사
function validateContent() {
    var html = CKEDITOR.instances.textareaContent.getSnapshot();
    var existImgs = false; 
    var dom = document.createElement("div");
    dom.innerHTML = html;
    for (var i=0; i < dom.childNodes.length; i++) {
        var node = dom.childNodes[i];
        if (node.tagName == "TITLE" || node.tagName == "STYLE" || node.tagName == "SCRIPT") {
            node.parentNode.removeChild(node);
        }
        if (node.tagName == "IMG") {
            existImgs = true;
        }
    }
    var txt = (dom.textContent || dom.innerText);
    if (txt != "" || existImgs) {
        $("#txtContent").val(txt);
        $("#htmlContent").val(CKEDITOR.instances.textareaContent.getData());
    }
    else {
        $("#txtContent").val("");
        $("#htmlContent").val("");
    }

    var id = "txtContent";
    var name = "내용";
    var obj = document.getElementById(id);
    if (!checkRequired(obj.value)) {
        if (!existImgs) {
            obj.setCustomValidity(name + "은(는) 필수 입력입니다.");
        }
        else {
            obj.setCustomValidity("");
        }
    }
    else {
        obj.setCustomValidity("");
    }
    //유효한 경우
    if (obj.validity.valid) {
        $(obj).closest(".form-group").find(".invalid-feedback").html("");
        $(obj).removeClass('is-valid is-invalid');
    }
    //유효하지 않을 경우
    else {
        //에러 메시지 표시
        $(obj).closest(".form-group").find(".invalid-feedback").html(obj.validationMessage);
        $(obj).addClass("is-invalid");
    }

    return obj.validity.valid;
}

//작성 취소
function onBtnCancelBoardClick() {
    $("#modalEditBoard").modal("hide");

    onBtnDetailBoardClick($("#articleId").val(), $("#articleMenuId").val());
}

//편집 창 닫기 버튼 클릭
function onBtnCloseEditBoardClick() {
//     if ($("#detectEditArticle").val() == "Y") {
//         $("#modalConfirmCloseEditAppDoc").modal("show");
//     }
//     else {
        $("#modalEditBoard").modal("hide");
//     }
}

//작성취소 버튼 클릭
function onBtnConfirmCloseEditArticleClick() {
    $("#modalConfirmCloseEditArticle").modal("hide");
    $("#modalEditNotice").modal("hide");
}

</script>
<form id="mainForm" name="mainForm" method="post" action="/gw/nb/nb0200100.php" enctype="multipart/form-data">
<div id="divSearch">
<div class="btnList">
    <div style="display: none;">
        <button type="button" class="btn btn-primary" id="btnAddBoard">신규</button>
    </div>
    <div style="display: none;">
        <button type="button" class="btn btn-primary ml-2" id="btnShowMoveTray" data-toggle="modal" data-target="#modalMoveBoard" disabled>문서이동</button>
    </div>
    <div style="display: none;">
        <button type="button" class="btn btn-primary ml-2" id="btnBatchDelArticle" data-toggle="modal" data-target="#modalConfirmBatchDelBoard" disabled>삭제</button>
    </div>
</div>
<div class="row">
    <div class="col mb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <select class="form-control" id="ddlSearchKind" name="ddlSearchKind">
                </select>
            </div>
            <input type="search" class="form-control" id="txtSearchValue" name="txtSearchValue" maxlength="50"/>
            <div class="input-group-append">
                <button type="button" id="btnSearchBoard" name="btnSearchBoard" class="btn btn-info">
                    <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    <span class="fas fa-magnifying-glass"></span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<div id="resultMsg" class="alert alert-primary py-1 mb-2" style="display: none;"></div>

<div class="tableFixHead">
<table class="table table-hover" id="tblBoardList" style="table-layout: fixed;">
    <thead class="thead-light">
        <tr class="row">
            <th class="col-md-1 col-1 col-w-chk" style="display: none;"><input type="checkbox" id="chkAll" onclick="onChkAllArticleClick(this);" /></th>
            <th class="col-md-1 col-2">No.</th>
            <th class="col-md d-none d-md-block">제목</th>
            <th class="col-md-block d-md-none col">문서</th>
            <th class="col-md-3 d-none d-md-block">등록자 (등록일)</th>
            <th class="col-md-1 d-none d-md-block">조회수</th>
            <th class="col-md-1 col-2 col-w-btn">상세</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>

<ul class="pagination justify-content-center" id="pageList">
</ul>

<!-- 결재함 이동 -->
<div class="modal fade" id="modalMoveBoard" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">문서 이동</h4>
                <button type="button" class="close btn-close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div id="boardTrayList" class="blockList"></div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" id="btnMoveArticle" class="btn btn-primary">이동</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade modalMain" id="modalEditBoard" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close btn-close" name="btnCloseEditBoard">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body mainContents">
                <div class="row form-group">
                    <div class="col-md-2 colHeader">
                        <label for="subject">제목</label><span class="necessaryInput"> *</span>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control validateElement" id="subject" name="subject" maxlength="255" required />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 colHeader">상시공지여부</div>
                    <div class="col-md-4" id="divAlwaysTop">
                    </div>
                    <div class="col-md-2 colHeader">상시게시기간설정</div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="alwaysTopDate" name="alwaysTopDate" />
                    </div>
                </div>
                <div id="rowEditComment" class="row" style="display: none;">
                    <div class="col-md-2 colHeader">댓글등록제한</div>
                    <div class="col-md-4" id="divComment">
                    </div>
                    <div class="col-md-2 colHeader">댓글알림</div>
                    <div class="col-md-4" id="divCommentAlarm">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 colHeader">알림</div>
                    <div class="col-md-4" id="divAlarm">
                    </div>
                    <input type="hidden" id="preAlarmType" name="preAlarmType" />
                    <div class="col-md-2 colHeader">인쇄가능여부</div>
                    <div class="col-md-4" id="divPrint">
                    </div>
                </div>
                <div class="row form-group" style="display: none;">
                    <div class="col-md-2">
                        <label for="publicScopeNms">공개설정</label>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="publicScopeNms" name="publicScopeNms" readonly />
                            <input type="hidden" id="publicScopeIds" name="publicScopeIds" />
                            <div class="input-group-append">
                                <button class="btn btn-success" type="button" onclick="onBtnSelectMultiDeptUserClick('NB', 'publicScope', 'Y')">선택</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group" style="display: none;">
                    <div class="col-md-2">
                        <label for="commentInqNms">댓글조회권한</label>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="text" class="form-control" id="roleCommentNms" name="roleCommentNms" readonly />
                            <input type="hidden" id="roleCommentIds" name="roleCommentIds" />
                            <div class="input-group-append">
                                <button class="btn btn-success" type="button" onclick="onBtnSelectMultiDeptUserClick('NB', 'roleComment', 'Y')">선택</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-2 mb-2">
                    <!-- <label for="textareaContent" style="font-weight: bold;">내용</label>-->
                    <textarea id="textareaContent"></textarea>
                    <div>
                        <input type="text" id="txtContent" name="txtContent" style="display:none;" />
                        <div class="invalid-feedback"></div>
                    </div>
                    <input type="hidden" id="htmlContent" name="htmlContent" />
                </div>
                <div>
                    <label for="divAttachList" style="font-weight: bold; margin-bottom: 0px;">첨부파일</label><button type="button" class="btn btn-outline-info btn-sm py-0 ml-2" onclick="javascript:addAttachedFile('new');"><i class="fas fa-plus"></i></button>
                    <div id="divAttachedList" class="mt-2">
                    </div>
                    <div id="divNewAttachedList">
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" class="btn btn-primary" id="btnSaveBoard">저장</button>
                        <button type="button" class="btn btn-primary" id="btnCancelBoard">작성취소</button>
                        <button type="button" class="btn btn-secondary" name="btnCloseEditBoard">닫기</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 작성 취소 확인창 -->
<div class="modal fade" id="modalConfirmCloseEditArticle" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <p>작성을 취소하시겠습니까?<br />취소 시 작성된 내용은 저장되지 않습니다.</p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">계속작성</button>
                        <button type="button" id="btnConfirmCloseEditArticle" class="btn btn-primary" onclick="onBtnConfirmCloseEditArticleClick()">작성취소</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 삭제 확인창 -->
<div class="modal fade" id="modalConfirmBatchDelBoard" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <p>선택 게시글을 삭제하시겠습니까?</p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" id="btnConfirmBatchDeleteBoard" class="btn btn-primary">네</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">아니오</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$multiGroupMode = "G";
require_once '../cm/cm_select_multi_dept_user_view.php';
require_once 'nb0200100_detail_view.php';
require_once 'nb_read_list_view.php';
?>

<input type="hidden" id="mode" name="mode" /> 
<input type="hidden" id="menuId" name="menuId" />
<input type="hidden" id="parMenuId" name="parMenuId" />
<input type="hidden" id="pageNo" name="pageNo" value="1" />
<input type="hidden" id="isManager" name="isManager" value="N" />
<input type="hidden" id="maxFileSize" name="maxFileSize" />
<input type="hidden" id="termNewArticle" name="termNewArticle" />
<input type="hidden" id="chkReadBoard" name="chkReadBoard" />
<input type="hidden" id="initialChkAll" name="initialChkAll" />
<input type="hidden" id="depth" name="depth" />
<input type="hidden" id="parArticleId" name="parArticleId" />
<input type="hidden" id="deleteAttach" name="deleteAttach" />
<input type="hidden" id="moveMenuId" name="moveMenuId" />
<input type="hidden" id="menuTitle" name="menuTitle" />
<input type="hidden" id="editingArticle" name="editingArticle" value="N" />
<input type="hidden" id="detectEditArticle" name="detectEditArticle" value="N" />
</form>
