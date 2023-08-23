function editAppDoc() { 
    $("#appDocContents").empty();
    $.ajax({
        url: "/gw/ea2/form/form_" + $("#formId").val() + "_input.php", 
        success: function(result) {
            $("#appDocContents").append(result);
        },
        complete: function() {
            //작업모드
            $("#mode").val("EDIT");
            $("#divAppLine").empty();
            $.ajax({ 
                type: "POST", 
                url: "/gw/ea2/ea_form_list.php", 
                data: $("#mainForm").serialize(),
                dataType: "json",  
                success: function(result) {
                    //세션 만료일 경우
                    if (result["session_out"]) {
                        //로그인 화면으로 이동
                        onLogoutClick();
                    }

                    if ($("#ebMoveYn").val() == 1) {
                        var ebMenuInfo = result["ebMenuInfo"];
                        $("#ply").val(ebMenuInfo["ply"]);
                        $("#ebMenuId").val(ebMenuInfo["ebMenuId"]);
                        $("#ebMenuNm").val(ebMenuInfo["ebMenuNm"]);
                        $("#plyNm").val(ebMenuInfo["plyNm"]);
                        showEbMenuNmPly();
                    }

                    var optionList = result["optionList"];
                    $("#appAgrCnt").val(optionList["appAgrCnt"]);
                    $("#appLineType").val(optionList["appLineType"]);
                    $("#appUserYn").val(optionList["appUserYn"]);
//                    $("#reqApprYn").val(optionList["reqApprYn"]);
//                    $("#reqAgrYn").val(optionList["reqAgrYn"]);
//                    $("#reqRefYn").val(optionList["reqRefYn"]);
//                    $("#reqOprYn").val(optionList["reqOprYn"]);
//                    $("#rcpDeptYn").val(optionList["rcpDeptYn"]);
                    $("#reqRelYn").val(optionList["reqRelYn"]);

                    var html = "";
                    //부서
                    $(result["deptMainSubList"]).each(function(i, info) {
                        html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
                    });
                    $("#ddlDeptMainSub").append(html);

                    var appDocInfo = result["appDocInfo"];
                    $("#modalEditAppDoc .modal-title").text(appDocInfo["formNm"]);
                    $("#formNm").val(appDocInfo["formNm"]);
                    $("#formKind").val(appDocInfo["formKind"]);
                    $("#formAppKind").val(appDocInfo["formAppKind"]);
                    //중요도
                    $("#ddlImprotantKind").val(appDocInfo["kindImportant"]);
                    //품의번호
                    $("#DF01").text(appDocInfo["DF01"]);
                    //작성일자
                    $("#DF02").text(appDocInfo["DF02"]);
                    //기안부서
                    $("#DF03").val(appDocInfo["DF03"]);
                    //기안자
                    $("#DF04").text(appDocInfo["DF04"]);
                    //기안자 고유번호
                    $("#drafter").val(appDocInfo["drafter"]);
                    //제목
                    //등록 시
                    if ($("#actType").val() == "I") {
                        //제목이 고정되어 있을 경우 제외
                        if (!$("#subject").is('[readonly]')) { 
                            $("#subject").val(appDocInfo["subject"]);
                        }
                    }
                    else {
                        $("#subject").val(appDocInfo["subject"]);
                    }
                    $("#dateFrom").val(appDocInfo["dateFrom"]);
                    $("#dateTo").val(appDocInfo["dateTo"]);
                    $("#txtPjt_id_fr").val(appDocInfo["txtPjt_id_fr"]);
                    $("#txtPjt_cd_fr").val(appDocInfo["txtPjt_cd_fr"]);
                    $("#txtPjt_nm_fr").val(appDocInfo["txtPjt_nm_fr"]);
                    $("#DUMMY1").val(appDocInfo["DUMMY1"]);
                    $("#DUMMY2").val(appDocInfo["DUMMY2"]);
                    $("#DUMMY3").val(appDocInfo["DUMMY3"]);
                    $("#df_txtarea").val(appDocInfo["df_txtarea"]);
                    $("#df_txtarea1").val(appDocInfo["df_txtarea1"]);
                    $("#df_txtarea2").val(appDocInfo["df_txtarea2"]);
                    $("#df_txtarea3").val(appDocInfo["df_txtarea3"]);
                    // CKEDITOR.instances.textareaContent.setData(appDocInfo["contents"]);
                    // editor.insertHTML(appDocInfo["contents"]);
                    editor.openHTML(appDocInfo["contents"])
                    $("#DF20").val(appDocInfo["DF20"]);
                    $("#DF21").val(appDocInfo["DF21"]);
                    $("#DF22").val(appDocInfo["DF22"]);
                    $("#DF23").val(appDocInfo["DF23"]);
                    $("#DF24").val(appDocInfo["DF24"]);
                    $("#DF34").val(appDocInfo["DF34"]);

                    //근태신청서
                    if ($.inArray($("#formId").val(), ["10012", "10013", "10014", "10015", "10016", "10029", "10037"]) > -1) {
                        if (Object.keys(result["df30List"]).length > 0) {
                            html = "";
                            //구분
                            $(result["df30List"]).each(function(i, info) {
                                html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
                            });
                            $("#DF30").append(html);
                            $("#DF30").val(appDocInfo["DF30"]);
                        }
                        else {
                            $("#DF30").closest("div.row").hide();
                        }
                    }

                    //출장복명서
                    if ($.inArray($("#formId").val(), ["10024"]) > -1) {
                        if (Object.keys(result["df50ddlList"]).length > 0) {
                            html = "";
                            $(result["df50ddlList"]).each(function(i, info) {
                                html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
                            });
                            $("#DF50ddl").append(html);
                        }
                    }
                    $("#DF50ddl").val(appDocInfo["DF50ddl"]);
                    $("#DF51ddl").val(appDocInfo["DF51ddl"]);
                    $("#DF52ddl").val(appDocInfo["DF52ddl"]);
                    $("#DF53ddl").val(appDocInfo["DF53ddl"]);

                    //필수 결재라인
                    $("input[type='hidden'][name^='essential'").remove();
                    $.each(result["essentialUserList"], function(key, list) {
                        var name = "essential" + key + "[]";
                        $(list).each(function(i, info) {
                            $("<input>").attr({
                                type: "hidden",
                                name: name,
                                value : info["id"]
                            }).appendTo($("#mainForm"));
                        });
                    });

                    $("#appKindDisplay").val(result["appKindDisplay"]);
                    html = drawAppLine(result["appLine"]);
                    $("#divAppLine").append(html);
                    showAppLineMsg();

//                    if ($("#recipientIds").length) {
                    //수신참조
                    $("#recipientIds").val(result["recipientIds"]);
                    $("#recipientNms").val(result["recipientNms"]);
//                    }
//                    if ($("#operatorIds").length) {
                    //시행자
                    $("#operatorIds").val(result["operatorIds"]);
                    $("#operatorNms").val(result["operatorNms"]);
//                    }

                    //참조문서
                    html = "";
                    $(result["relatedDocList"]).each(function(i, info) {
                        html += '<tr>';
                        html += '<td style="width: 5%;text-align: center;">' + (i + 1) + '</td>';
                        html += '<td style="width: 35%;text-align: center;">';
                        //품의번호
                        html += info["docSeqCd"];
                        html += '</td>';
                        html += '<td style="width: 20%;text-align: center;">';
                        //문서분류
                        html += info["formNm"];
                        html += '</td>';
                        html += '<td style="width: 40%;">';
                        html += '<a href="javascript:void(0);" onclick="showEaAppDocDetail(' + info["docId"] + ',' + info["formId"] + ')">';
                        //제목
                        html += info["subject"];
                        html += '</a>';
                        html += '<input type="hidden" name="relatedDoc[]" value="' + info["docId"] + '" />';
                        html += '</td>';
                        html += '</tr>';
                    });
                    $("#tblRelatedDocList tbody").append(html);

                    //첨부파일
                    html = "";
                    $(result["attachFileList"]).each(function(i, info) {
                        html += '<div class="input-group mb-2">';
                        html += '<div class="form-control">';
                        html += info["oriFileNm"];
                        html += '<input type="hidden" name="attachFileId[]" value="' + info["attachId"] + '" />';
                        html += '<input type="hidden" name="attachFile[]" value="' + info["fileNm"] + '" />';
                        html += '</div>';
                        html += '<div class="input-group-append">';
                        html += '<button type="button" class="btn btn-secondary" onclick="javascript:delAttachedFile(this);">&times;</button>';
                        html += '</div>';
                        html += '</div>';
                    });
                    $("#divAttachedList").append(html);
                    addAttachedFile('new');

                    $("#modalEditAppDoc .imgLogo").attr("src", result["logo"]);

                    $("#modalEditAppDoc").modal("show");
                },
                complete:function() {
                    initFormInput();
                    $("#appDocContents").find("input").on("change", function() {
                        $("#detectEditAppDoc").val("Y");
                    });
                    $("#appDocContents").find("select").on("change", function() {
                        $("#detectEditAppDoc").val("Y");
                    });
                    $("#appDocContents").find("textarea").on("change", function() {
                        $("#detectEditAppDoc").val("Y");
                    });
                    $("#appDocContents .validateElement").each(function() {
                        var elem = $(this);

                        // Save current value of element
                        elem.data('oldVal', elem.val());

                        // Look for changes in the value
                        elem.on("propertychange change keyup input paste", function(event) {
                            // If value has changed...
                            if (elem.data('oldVal') != elem.val()) {
                                // Updated stored value
                                elem.data('oldVal', elem.val());

                                validateElement(elem.attr("id"));
                            }
                        });
                    });

                },
                error: function(request, status, error) {
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });
        }
    });
}

function showEbMenuNmPly() {
    var ebMenuNmPly = "[결재보관함]";
    ebMenuNmPly += $("#ebMenuNm").val();
    if ($("#plyNm").val() != "") {
        ebMenuNmPly += "-[" + $("#plyNm").val() + "]";
    }

    $("#ebMenuNmPly").text(ebMenuNmPly);
}

function onBtnEBChangeClick() {
    $("#modalEBMenu").modal("show");
}

//결재라인 표시
function drawAppLine(appLine) {
    $("input[type='hidden'][name='appAgrLine[]'").remove();
    $("input[type='hidden'][name='appAgrLineSign'").remove();

    var appAgrCnt = $("#appAgrCnt").val();
    var html = '';
    //결재라인
    $.each(appLine, function(type, obj) {
        var len = obj.length;
        html += '<span style="font-weight: bold;">';
        if (type == "app") {
            html += '결재';
        }
        else if (type == "agr") {
            html += '합의';
        }
        html += '</span>';
        html += '<table class="eaAppLine">';
        html += '<tr>';
        for(var i = 0; i < appAgrCnt; i++) {
            if (i < len) {
                var info = obj[i];
                html += '<th>' + info.appGradeDutyNm + '</th>';

                $("<input>").attr({
                    type: "hidden",
                    name: "appAgrLine[]",
                    value : info.appAgrValue
                }).appendTo($("#mainForm"));
                $("<input>").attr({
                    type: "hidden",
                    name: "appAgrLineSign[]",
                    value : info.docUserId + "|" + info.signKind + "|" + info.signYn
                }).appendTo($("#mainForm"));
            }
            else {
                html += '<td>&nbsp;</td>';
            }
        }
        html += '</tr>';
        html += '<tr style="height: 7rem;">';
        for(var i = 0; i < appAgrCnt; i++) {
            if (i < len) {
                var info = obj[i];
                html += '<td style="width: 5.25rem;">';
                html += '<div style="height: 4.25rem; width: 100%; position: relative; text-align: center;">';
                html += '<div style="height: 4.25rem; width: 100%; position: absolute; text-align: center; z-index: 3; left: 0; top: 0.625rem">';
                if (info.signKindNm != "") {
                    //html += '<img src="' + info.signKindNm + '" />';
                    html += '<span class="badge badge-' + info.signKindColor + '">' + info.signKindNm + '</span>';
                }
                html += '</div>';
                html += '<div style="height: 4.25rem; width: 100%; position: absolute; text-align: center; z-index: 1; left: 0; top: 0.4rem">';
                if (info.signImg != "") {
                    if (info.signImg == "approval") {
                        html += '<span class="fa-regular fa-circle fa-4x" style="color:#ffb3b3;"></span>';
                    }
                    else if (info.signImg == "appOrder") {
                        var nowLevel = (info.appLevel + "");
                        for(var j = 0; j < nowLevel.length; j++) {
                            var k = (nowLevel).substr(j, 1);
                            html += '<span class="fa-regular fa-' + k + ' fa-3x" style="color:#e6f5ff;"></span>';
                        }
                    }
                    else {
                        html += '<img src="' + info.signImg + '" width="49" height="49" />';
                    }
                }
                html += '</div>';
                html += '<div style="height: 4.25rem; width: 100%; vertical-align: middle; position: absolute; text-align: center; z-index: 2; left: 0; top: 2rem; font-size: 0.5rem;">';
                if (info.signUserNm != "") {
                    html += info.signUserNm;
                }
                html += '</div>';
                html += '</div>';
                html += info.signDetail;
                html += '</td>';
            }
            else {
                html += '<td style="width: 5.25rem;"></td>';
            }
        }
        html += '</tr>';
        html += '</table>';
        if (type == "agr" && $("#appKindDisplay").val() != "") {
            html += '<span>' + $("#appKindDisplay").val() + '</span>';
        }
//        html += '<br />';
    });

    return html;
}

//결재라인 표시
function drawAppLineShow(appLine, appAgrCnt, appKindDisplay, manageEaAppDoc = false) {
    if (manageEaAppDoc) {
        $("input[type='hidden'][name='appAgrLine[]'").remove();
        $("input[type='hidden'][name='appAgrLineSign'").remove();
    }

    var html = '';
    //결재라인
    $.each(appLine, function(type, obj) {
        var len = obj.length;
        html += '<span style="font-weight: bold;">';
        if (type == "app") {
            html += '결재';
        }
        else if (type == "agr") {
            html += '합의';
        }
        html += '</span>';
        html += '<table class="eaAppLine">';
        html += '<tr>';
        for(var i = 0; i < appAgrCnt; i++) {
            if (i < len) {
                var info = obj[i];
                html += '<th>' + info.appGradeDutyNm + '</th>';

                if (manageEaAppDoc) {
                    $("<input>").attr({
                        type: "hidden",
                        name: "appAgrLine[]",
                        value : info.appAgrValue
                    }).appendTo($("#mainForm"));
                    $("<input>").attr({
                        type: "hidden",
                        name: "appAgrLineSign[]",
                        value : info.docUserId + "|" + info.signKind + "|" + info.signYn
                    }).appendTo($("#mainForm"));
                }
            }
            else {
                html += '<td>&nbsp;</td>';
            }
        }
        html += '</tr>';
        html += '<tr style="height: 7rem;">';
        for(var i = 0; i < appAgrCnt; i++) {
            if (i < len) {
                var info = obj[i];
                html += '<td style="width: 5.25rem;">';
                html += '<div style="height: 4.25rem; width: 100%; position: relative; text-align: center;">';
                html += '<div style="height: 4.25rem; width: 100%; position: absolute; text-align: center; z-index: 3; left: 0; top: 0.625rem">';
                if (info.signKindNm != "") {
                    //html += '<img src="' + info.signKindNm + '" />';
                    html += '<span class="badge badge-' + info.signKindColor + '">' + info.signKindNm + '</span>';
                }
                html += '</div>';
                html += '<div style="height: 4.25rem; width: 100%; position: absolute; text-align: center; z-index: 1; left: 0; top: 0.4rem">';
                if (info.signImg != "") {
                    //html += '<img src="' + info.signImg + '" />';
                    if (info.signImg == "approval") {
                        html += '<span class="fa-regular fa-circle fa-4x" style="color:#ffb3b3;"></span>';
                    }
                    else if (info.signImg == "appOrder") {
                        var nowLevel = (info.appLevel + "");
                        for(var j = 0; j < nowLevel.length; j++) {
                            var k = (nowLevel).substr(j, 1);
                            html += '<span class="fa-regular fa-' + k + ' fa-3x" style="color:#e6f5ff;"></span>';
                        }
                    }
                    else {
                        html += '<img src="' + info.signImg + '" width="49" height="49" />';
                    }
                }
                html += '</div>';
                html += '<div style="height: 4.25rem; width: 100%; vertical-align: middle; position: absolute; text-align: center; z-index: 2; left: 0; top: 2rem; font-size: 0.5rem;">';
                if (info.signUserNm != "") {
                    html += info.signUserNm;
                }
                html += '</div>';
                html += '</div>';
                html += info.signDetail;
                html += '</td>';
            }
            else {
                html += '<td style="width: 5.25rem;"></td>';
            }
        }
        html += '</tr>';
        html += '</table>';
        if (type == "agr" && appKindDisplay != "") {
            html += '<span>' + appKindDisplay + '</span>';
        }
//        html += '<br />';
    });

    return html;
}

//첨부파일 추가
function addAttachedFile(target) {
    var html = '';
    html += '<div class="input-group mb-2">';
    //html += '<input type="file" class="form-control" name="newAttachFile[]" style="font-size:0.8rem;" onchange="onAttachFileChange()" />';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input" name="newAttachFile[]" onchange="onAttachFileChange(this)" />';
    html += '<label class="custom-file-label" for="customFile"><i class="fa-solid fa-cloud-arrow-up"></i> 파일을 선택하세요</label>';
    html += '</div>';
    html += '<div class="input-group-append">';
    html += '<button type="button" class="btn btn-secondary" onclick="javascript:delAttachedFile(this);">&times;</button>';
    html += '</div>';
    html += '</div>';

    if (target == 'new') {
        $('#divNewAttachedList').append(html);
    }
    else if (target == 'edit') {
        $('#divNewAttachedList_edit').append(html);
    }
}

//첨부파일 삭제
function delAttachedFile(obj) {
    $(obj).closest('div.input-group').remove();
    $("#detectEditAppDoc").val("Y");
}

//첨부파일 선택
function onAttachFileChange(obj) {
    var fileName = $(obj).val().split("\\").pop();
    $(obj).siblings(".custom-file-label").addClass("selected").html(fileName);

    $("#detectEditAppDoc").val("Y");
}

var delay = ms => new Promise(resolve => setTimeout(resolve, ms));

//임시저장 버튼 클릭
async function onBtnSaveTempAppDocClick() {
    $("#modalEditAppDoc").find("button:button").prop("disabled", true);

    //유효성 검사
    if(validateInputs()) {
        if (typeof rewriteContents === 'function') {
            rewriteContents();
            await delay(1000);
        }
        else {
//            $("#txtContent").val(CKEDITOR.instances.textareaContent.document.getBody().getText());
            var html = CKEDITOR.instances.textareaContent.getSnapshot();
            var dom = document.createElement("DIV");
            dom.innerHTML = html;
            var txt = (dom.textContent || dom.innerText);
            $("#txtContent").val(txt);
            $("#htmlContent").val(CKEDITOR.instances.textareaContent.getData());
        }

        //작업모드
        $("#mode").val("SAVE_TEMP");
        var formdata = new FormData($("#mainForm")[0]);
        $.ajax({ 
            type: "POST", 
            url: "/gw/ea2/ea_form_list.php", 
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

                $("#modalEditAppDoc").modal("hide");
                //$("#subMenu_" + result["moveToPage"]).trigger("click");
                location.href = "/gw/" + $("#topMenuCd").val() + "/" + result["moveToPage"] + "/";
            },
            error: function (request, status, error) {
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }
    else {
        $("#modalEditAppDoc").find("button:button").prop("disabled", false);
    }
}

//결재라인 유효성 검사
function validateAppLine() {
    var id = "setAppLine";
    var name = "결재라인지정";
    $("input[type='hidden'][name='appAgrLine[]']").each(function(i, obj) {
        var val = $(obj).val();
        var arr = val.split("|");
        //결재
        if (arr[arr.length - 2] == 1) {
            $("#" + id).val("Y");
            return false;
        }
    });
    var obj = document.getElementById(id);
    if (!checkRequired(obj.value)) {
        obj.setCustomValidity(name + "은(는) 필수 입력입니다.");
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

function showAppLineMsg() {
    $("#msgAppLine").empty();

    var msgList = []
    //결재선 기안자 포함
    if ($("#appUserYn").val() == "1") {
        var cntApp = 0;
        $("input[type='hidden'][name='appAgrLine[]']").each(function(i, obj) {
            var val = $(obj).val();
            var arr = val.split("|");
            //결재
            if (arr[arr.length - 2] == 1) {
                cntApp++;
            }
        });

        if (cntApp == 1) {
            msgList.push("결재라인에 기안자만 있으면 자동 결재되고 문서가 종료됩니다.");
        }
    }

    if (msgList.length > 0) {
        $("#msgAppLine").html(msgList.join("<br />"));
    }
}

//참조문서 유효성 검사
function validateRelatedDoc() {
    var id = "setRelatedDoc";
    var name = "참조문서";
    var obj = document.getElementById(id);

    //참조문서 필수
    if ($("#reqRelYn").val() == "1") {
        if ($("#tblRelatedDocList tr").length == 0) {
            obj.setCustomValidity(name + "은(는) 필수 입력입니다.");
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
    }
    else {
        obj.setCustomValidity("");
        $(obj).closest(".form-group").find(".invalid-feedback").html("");
        $(obj).removeClass('is-valid is-invalid');
    }

    return obj.validity.valid;
}

//유효성 검사
function validateInputs() {
    var valid = true;

    //결재라인지정
    valid = valid & validateAppLine();

    //제목
    valid = valid & validateElement("subject");

    //참조문서
    valid = valid & validateRelatedDoc();

    return valid;
}

//상신 버튼 클릭
async function onBtnSaveAppDocClick() {
    $("#modalEditAppDoc").find("button:button").prop("disabled", true);

    //유효성 검사
    if(validateInputs() && validateInputsDetail()) {
        if (typeof rewriteContents === 'function') {
            rewriteContents();
            await delay(1000);
        }
        else {
            var html = editor.getPublishingHtml();
            var dom = document.createElement("DIV");
            dom.innerHTML = html;
            var txt = (dom.textContent || dom.innerText);
            $("#txtContent").val(txt);
            $("#htmlContent").val(editor.getPublishingHtml());
        }
//         var sLang = getCookieValue("LangKind");
        
//         var APPUserYN = document.getElementById('hidAPPUser_YN').value;
//         var ReqApp    = document.getElementById('hidAPPInfo').value;
//         var ReqAgr    = document.getElementById('hidAGRInfo').value;
//         var ReqRef    = document.getElementById('hidRefInfo').value;
//         var ReqOpr    = document.getElementById('hidOperInfo').value;
//         var ReqRcv    = document.getElementById('hidRCVInfo').value;
//         var AppAgr = "";
//         var Ref    = "";
//         var Opr    = "";
//         var Rcv    = "";
//         var Rel    = "";             
//         var objInfo;
//         var ReqInfo;
//         var idx = -1;  
//         var nUserInfo = nUserID + "|" + nDeptID;

//         if(document.getElementById('txtAppLine') != null) {
//             AppAgr = document.getElementById('txtAppLine').value;
//         }
        
//         if(document.getElementById('txtManID') != null) {
//             Ref    = document.getElementById('txtManID').value;
//         }
//         /* 근태신청서 수신참조 */
//         if(document.getElementById('txtUserMultiID1') != null) {
//             Ref    = document.getElementById('txtUserMultiID1').value;
//         }
        
//         if(document.getElementById('txtAddID') != null) {
//             Opr    = document.getElementById('txtAddID').value;
//         }
//         /* 근태신청서 시행자 */
//         if(document.getElementById('txtUserMultiID2') != null) {
//             Opr = document.getElementById('txtUserMultiID2').value;
//         }
            
//         if(document.getElementById('txtReceiveID') != null) {
//             Rcv    = document.getElementById('txtReceiveID').value;
//         }
//         /* 근태신청서 수신처 */
//         if(document.getElementById('txtUserMultiID3') != null) {
//             Rcv    = document.getElementById('txtUserMultiID3').value;
//         }
        
//         if(document.getElementById('docidlist') != null) {
//             Rel    = document.getElementById('docidlist').value;
//         }
        
//         /* 상신자자동포함여부가 1일 경우 결재선의 첫번째이어야 한다. */
//         if(APPUserYN == "1") 
//         {
//             //alert(AppAgr.Array("APP"));
//             var firstAPP = AppAgr.Array("APP")[0];
//             var tmp = firstAPP.split('|');
//             if(nUserID != tmp[0]) 
//             {
//                 var msg = "";
//                 if(sLang == "JP")
//                  msg = "上申者が一番目決栽者と指定になっていなければなりません. 決栽ラインを修正してください.";
//              else if(sLang == "GB")
//                  msg = "提交人应该被指定为第一审批人，请修改审批流程。";
//              else
//                  msg = "상신자께서 첫 번째 결재자로 지정이 되어있어야 합니다. 결재선을 수정해주세요.";
                    
//                 alert(msg);
//                 return false;
//             }
            
//             //alert(firstAPP);
//             //alert(AppAgr.Array("APP").length);
//             if(AppAgr.Array("APP").length == 1 && firstAPP == nUserInfo)
//             {
                
//                 if(sLang == "JP")
//                  alert_Msg   = "決裁ラインにある上申者のみ自動決裁され、文書が終了されます。上申しますか?";
//              else if(sLang == "GB")
//                  alert_Msg   = "审批流程里只有提交人时，将自动审批后，结束文件。是否提交？";
//              else
//                  alert_Msg   = "결재라인에 상신자만 있으면 자동 결재 되고 문서가 종료 됩니다. 상신하시겠습니까?";
                    
//              if (!confirm(alert_Msg)){ return false;}
//             }
            
//         }
        
//         if(ReqApp != "") {
//             ReqInfo = ReqApp.Array();
//             objInfo = AppAgr.Array("APP");
//             idx = ReqInfo.ContainsKey(objInfo);
                            
//             if (idx >= 0) 
//             {
//                  // 결재 필수 필수 결재자에 기안자가 속해 있다면, 기안자가 합의라인에라도 있으면 통과
//                 var ReturnValue = -1; // 두번째 체크 리턴값
//                 for(var i = 0 ; i < ReqInfo.length  ; i++)
//                 {
//                     if(ReqInfo[i] == nUserInfo)  // 결재필수 사용자가 기안자이면 
//                     {
//                         var objInfo1 = AppAgr.Array("AGR");
//                         for(var j = 0 ; j < objInfo1.length  ; j++)
//                         {
//                             if(objInfo1[j] == nUserInfo)
//                             {
//                                 ReturnValue = ReturnValue + 1;
//                             }
//                         }
//                     }
//                     else
//                     {
//                         if(!objInfo.Contains(ReqInfo[i]))
//                         {
//                             ReturnValue = ReturnValue - 1;
//                             idx = i;
//                         }
//                     }
                   
//                 }
// //                if(ReturnValue < 0)
// //                {
// //                    var msg2 = "";
// //                    if(sLang == "JP")
// //                       msg2    = "決栽ラインに必須に指定された使用者情報を追加してください.";
// //                   else if(sLang == "GB")
// //                       msg2    = "请添加审批流程中必须指定的用户信息。";
// //                   else
// //                       msg2    = "결재선에 필수로 지정된 사용자정보를 추가해주세요.";
// //                   
// //                    alert(msg2+" \r\n - " + ReqApp.GetValue(idx));
// //                    return false;
// //                }
//             }
//         }
//         else {
//             if(document.getElementById("hidReqAPP_YN").value == "1"){
//                 objInfo = AppAgr.Array("APP");
//                 if(objInfo.length == 0)
//                 {
//                     var msg3 = "";
//                     if(sLang == "JP")
//                      msg3    = "決栽選定補は必須です.";
//                  else if(sLang == "GB")
//                      msg3    = "审批流程信息是必须";
//                  else
//                      msg3    = "결재선정보는 필수입니다.";
//                     // 결재 필수 필수 결재자에 기안자가 속해 있다면, 기안자가 합의라인에라도 있으면 통과
//                     alert( msg3 );
//                     return false;
//                 }
//             }
//         }
        
//         if(ReqAgr != "") {
//             ReqInfo = ReqAgr.Array();
//             objInfo = AppAgr.Array("AGR");
//             idx = ReqInfo.ContainsKey(objInfo);  
//             if (idx >= 0) 
//             {
//                 // 합의 필수 필수 합의자에 기안자 속해 있다면, 기안자가 결재라인에라도 있으면 통과
//                 var ReturnValue = -1; // 두번째 체크 리턴값
//                 for(var i = 0 ; i < ReqInfo.length  ; i++)
//                 {
//                     if(ReqInfo[i] == nUserInfo)  // 합의필수 사용자가 기안자이면 
//                     {
//                         var objInfo1 = AppAgr.Array("APP");
//                         for(var j = 0 ; j < objInfo1.length  ; j++)
//                         {
//                             if(objInfo1[j] == nUserInfo)
//                             {
//                                 ReturnValue = ReturnValue + 1;
//                             }
//                         }
//                     }
//                     else
//                     {
//                         if(!objInfo.Contains(ReqInfo[i]))
//                         {
//                             ReturnValue = ReturnValue - 1;
//                             idx = i;
//                         }
//                     }
                   
//                 }
// //                if(ReturnValue < 0)
// //                {
// //                    var msg4 = "";
// //                    if(sLang == "JP")
// //                       msg4    = "合意ラインに必須に指定された使用者情報を追加してください.";
// //                   else if(sLang == "GB")
// //                       msg4    = "请添加协议流程中必须指定的用户信息。";
// //                   else
// //                       msg4    = "합의선에 필수로 지정된 사용자정보를 추가해주세요.";
// //                       
// //                    alert(msg4 + " \r\n - " + ReqAgr.GetValue(idx));
// //                    return false;
// //                }
//             }
//         }
//         else {
//             if(document.getElementById("hidReqAGR_YN").value == "1"){
//                 objInfo = AppAgr.Array("AGR");
//                 if(objInfo.length == 0)
//                 {
//                     var msg5 = "";
//                     if(sLang == "JP")
//                      msg5    = "合意ライン情報は必須です.";
//                  else if(sLang == "GB")
//                      msg5    = "协议流程信息是必须";
//                  else
//                      msg5    = "합의선정보는 필수입니다.";
                        
//                      // 합의 필수 필수 합의자에 기안자 속해 있다면, 기안자가 결재라인에라도 있으면 통과
//                      // 위와 동일 조건인데 여기가 탈수가 없는데,,, 
//                     alert( msg5 );
//                     return false;
//                 }
//             }
//         }
        
//         if(ReqRef != "") {  
//             ReqInfo = ReqRef.Array();
//             objInfo = Ref.Array();    
//             if(typeof arryRefDept != 'undefined')
//                 idx = ReqInfo.ContainsKey(objInfo, arryRefDept);
//             else
//                 idx = ReqInfo.ContainsKey(objInfo);
                
//             if (idx >= 0) {
            
//                 var msg6 = "";
//                 if(sLang == "JP")
//                  msg6    = "受信参照に必須に指定された情報を追加してください.";
//              else if(sLang == "GB")
//                  msg6    = "请添加抄送中必须指定的信息。";
//              else
//                  msg6    = "수신참조에 필수로 지정된 정보를 추가해주세요.";
                    
//                 alert(msg6 + " \r\n - " + ReqRef.GetValue(idx));
//                 return false;
//             }
//         }
//         else {
//             if(document.getElementById("hidReqREF_YN").value == "1"){
//                 if(Ref == ""){
                    
//                     var msg7 = "";
//                     if(sLang == "JP")
//                      msg7    = "受信参照情報は必須です.";
//                  else if(sLang == "GB")
//                      msg7    = "抄送信息是必须";
//                  else
//                      msg7    = "수신참조정보는 필수입니다.";
                        
//                     alert( msg7 );
//                     return false;
//                 }
//             }
//         }
        
//         if(ReqOpr != "") {       
//             ReqInfo = ReqOpr.Array();
//             objInfo = Opr.Array();
//             idx = ReqInfo.ContainsKey(objInfo);  
//             if (idx >= 0) {
                
//                 var msg8 = "";
//                 if(sLang == "JP")
//                     msg8 = "施行に必須と指定された情報を追加してください.";
//                 else if(sLang == "GB")
//                     msg8 = "请添加执行所必须的指定内容";
//                 else
//                     msg8 = "시행에 필수로 지정된 정보를 추가해주세요.";
                    
//                  alert(msg8 + " \r\n - " + ReqOpr.GetValue(idx));
//                 return false;
//             }
//         }
//         else {
//             if(document.getElementById("hidReqOPR_YN").value == "1"){
//                 if(Opr == ""){
                    
//                     var msg9 = "";
//                     if(sLang == "JP")
//                         msg9 = "施行情報は必須です.";
//                     else if(sLang == "GB")
//                         msg9 = "执行信息必须输入";
//                     else
//                         msg9 = "시행정보는 필수입니다.";
                     
//                     alert( msg9 );
//                     return false;
//                 }
//             }
//         }
            
//         if(document.getElementById("hidReqREL_YN").value == "1"){
//             if(Rel == ""){
                
//                 var msg10 = "";
//                 if(sLang == "JP")
//                     msg10    = "参照文書は必須です.";
//                 else if(sLang == "GB")
//                     msg10    = "参考文件必须输入";
//                 else
//                     msg10    = "참조문서는 필수입니다.";
                 
//                 alert( msg10 );
//                 return false;
//             }
//         }
        
//         if (nAppLineType == "4" || nAppLineType == "5" || nAppLineType == "6") {
//            if(ReqRcv != "") {
//                 ReqInfo = ReqRcv.Array();
//                 objInfo = Rcv.Array();
                
//                 if(typeof arryRcvDept != 'undefined')
//                     idx = ReqInfo.ContainsKey(objInfo, arryRcvDept);
//                 else
//                     idx = ReqInfo.ContainsKey(objInfo);  
              
//                 if (idx >= 0) {
                
//                     var msg11 = "";
//                     if(sLang == "JP")
//                         msg11    = "宛先に必須に指定された情報を追加してください.";
//                     else if(sLang == "GB")
//                         msg11    = "请添加收信人所必须指定的内容";
//                     else
//                         msg11    = "수신처에 필수로 지정된 정보를 추가해주세요.";
                     
//                      alert(msg11 + " \r\n - " + ReqRcv.GetValue(idx));
//                     return false;
//                 }
//             }
//         }

        //작업모드
        $("#mode").val("SAVE");
        var formdata = new FormData($("#mainForm")[0]);
        $.ajax({ 
            type: "POST", 
            url: "/gw/ea2/ea_form_list.php", 
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

                //유효성 검사 실패
                if (!result["valid"]) {
                    $.each(result["resultValidation"], function(id, msg) {
                        var obj = document.getElementById(id);
                        $(obj).removeClass("is-valid").addClass("is-invalid");
                        obj.setCustomValidity(msg);
                        $(obj).closest(".form-group").find(".invalid-feedback").html(obj.validationMessage);
                        //if ($(obj).parent().hasClass('form-inline')) {
                            $(obj).closest(".form-group").find(".invalid-feedback").show();
                        //}
                    });
                    $("#mainForm").addClass('was-validated');
                    $("#modalEditAppDoc").find("button:button").prop("disabled", false);
                }
                else {
                    if (result["proceed"]) {
                        $("#modalEditAppDoc").modal("hide");
                        //$("#subMenu_" + result["moveToPage"]).trigger("click");
                        location.href = "/gw/" + $("#topMenuCd").val() + "/" + result["moveToPage"] + "/";
                    }
                    else {
                        $("#modalEditAppDoc").find("button:button").prop("disabled", false);
                        $("#modalAlertMsg .modal-body").html(result["msg"]);
                        $("#modalAlertMsg").modal("show");
                        $("#modalEditAppDoc").modal("hide");
                        if (result["moveToPage"]) {
                            $("#moveToPage").val(result["moveToPage"]);
                        }
                        else {
                            $("#moveToPage").val("");
                            onPageNoClick($("#pageNo").val(), "", true);
                        }
                    }
                }
            },
            error: function (request, status, error) {
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

    }
    else {
        $("#modalEditAppDoc").find("button:button").prop("disabled", false);
    }
}

//결재 특이사항 목록
function showReplyList(list) {
    $("#divReplyList").empty();

    var html = "";
    $(list).each(function(i, info) {
        html += '<div id="divReply_' + info["replyId"] + '">';
        html += info["userNm"];
        if (info["canEdit"] == "Y") {
            html += '    <button type="button" class="btn btn-sm btn-outline-info" onclick="inputReply(\'' + info["replyId"] + '\')">편집</button>';
        }
        if (info["canDel"] == "Y") {
            html += '    <button type="button" class="btn btn-sm btn-outline-danger" onclick="delReply(\'' + info["replyId"] + '\')">삭제</button>';
        }
        html += '<br />';
        html += '<p>';
        html += info["txtNote"];
        html += '</p>';
        html += '<div class="replyNote" style="display: none;">' + info["note"] + '</div>';
        html += '<div id="divReplyNote_' + info["replyId"] + '"></div>';
        html += '</div>';
    });
    $("#divReplyList").append(html);

    //결재의견 출력여부
    if ($("#appCommentPrint").val() == "1") {
        //특이사항 출력 옵션 - 본문 다음장 출력
        if ($("#eaPrintReplyPosition").val() == "0") {
            $("#printReplyList").empty();

            html = "";
            if (Object.keys(list).length > 0) {
                $("#divPrintReplyList").removeClass("notPrint");
                $(list).each(function(i, info) {
                    html += '<div>';
                    html += info["userNm"];
                    html += '<br />';
                    html += '<p>';
                    html += info["txtNote"];
                    html += '</p>';
                    html += '</div>';
                });
                $("#printReplyList").append(html);
            }
            else {
                $("#divPrintReplyList").addClass("notPrint");
            }
        }
        //본문 아래 출력
        else {
            if (Object.keys(list).length > 0) {
                $("#titleDivReplyList").removeClass("notPrint").addClass("includePrint");
            }
            else {
                $("#titleDivReplyList").removeClass("includePrint").addClass("notPrint");
            }
        }
    }
}

//결재 특이사항 입력
function inputReply(id) {
    var preReplyId = $("#replyId").val();
    if (preReplyId != "") {
        $("#divReplyNote_" + preReplyId).empty();
    }
    $("#divReplyNote_" + id).empty();

    $("#replyId").val(id);
    var note = "";
    if (id != "new") {
        note = $("#divReply_" + id).find(".replyNote").text();
        $("#divReply_" + id).find("p").hide();
    }
    var html = '';
    html += '<div class="row form-group">';
    html += '    <div class="col-10">';
    html += '        <textarea class="form-control" id="note" name="note" rows="2">' + note + '</textarea>';
    html += '    </div>';
    html += '    <div class="col-2">';
    html += '        <button type="button" class="btn btn-sm btn-info" id="btnSaveReply" name="btnSaveReply" onclick="onBtnSaveReplyClick()">저장</button>';
    html += '        <button type="button" class="btn btn-sm btn-warning ml-2" id="btnCancelReply" name="btnCancelReply" onclick="cancelReply(\'' + id + '\')">취소</button>';
    html += '    </div>';
    html += '</div>';

    $("#divReplyNote_" + id).append(html);
}

//결재 특이사항 입력 취소
function cancelReply(id) {
    $("#divReplyNote_" + id).empty();
    if (id != "new") {
        $("#divReply_" + id).find("p").show();
    }
}

//결재 특이사항 저장 클릭
function onBtnSaveReplyClick() {
    $("#note").val($("#note").val().trim());
    if ($("#note").val() == "") {
        return;
    }

    //작업모드
    $("#mode").val("SAVE_REPLY");
    $.ajax({ 
        type: "POST", 
        url: "/gw/ea2/ea_appdoc_list_2.php", 
        data: $("#mainForm").serialize(),
        dataType: "json",
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            cancelReply($("#replyId").val());

            showReplyList(result["replyList"]);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//결재 특이사항 삭제 클릭
function delReply(id) {
    $("#replyId").val(id);

    $("#modalConfirmDelReply").modal("show");
}

//삭제
function onBtnDeleteReplyClick() {
    $("#modalConfirmDelReply").modal("hide");

    //작업모드
    $("#mode").val("DEL_REPLY");
    $.ajax({ 
        type: "POST", 
        url: "/gw/ea2/ea_appdoc_list_2.php", 
        data: $("#mainForm").serialize(),
        dataType: "json", 
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            showReplyList(result["replyList"]);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}
