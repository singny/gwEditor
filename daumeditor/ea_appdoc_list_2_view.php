<style>
/* #divDF11 {
    all: revert;
} */
#divDF11 p {
    margin-top: 0;
    margin-bottom: 0rem;
}
@media (max-width: 1400px) {
    .col-w-state {
        display: none !important;
    }
}
@media print {
    body.modal-open {
        visibility: hidden;
    }
    body.modal-open #modalDetailAppDoc {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        padding: 0;
        overflow: visible!important;
    }
    body.modal-open #modalDetailAppDoc .modal-dialog {
        width: 768px;
        transform-origin: top left;
        transform: scale(1.35, 1.35);
    }
    body.modal-open #divPrintReplyList:not(.notPrint) {
        visibility: visible!important;
        break-before: page;
    }
    body.modal-open #modalDetailAppDoc .modal-header {
        visibility: visible;
        padding-top: 1rem!important;
        padding-bottom: 1rem!important;
    }
    body.modal-open #modalDetailAppDoc #divBtnList {
        display: none;
    }
    body.modal-open #modalDetailAppDoc #divDetailAppDocTitle {
        display: flex!important;
        justify-content: center!important;
    }
    body.modal-open #modalDetailAppDoc .modal-body {
        visibility: visible;
    }
    body.modal-open #modalDetailAppDoc .includePrint {
        visibility: visible!important;
    }
    body.modal-open #modalDetailAppDoc .btn, 
    body.modal-open #modalDetailAppDoc .close {
        visibility: hidden;
    }
    body.modal-open #modalDetailAppDoc .notPrint {
        height: 0!important;
        visibility: hidden;
    }
    body.modal-open #modalDetailAppDoc a:link {
        text-decoration: none !important;
    }
    body.modal-open #modalDetailAppDoc .mainContents .row div[class*=col] {
        min-height: 2rem;
    }
    body.modal-open #modalDetailAppDoc #divDetailContent {
        display: block!important;
        overflow-x: visible;
        min-height: 35rem;
        margin-bottom: 0.5rem;
    }
}
</style>
<!-- <script type="text/javascript" src="/gw/vendor/ckeditor/ckeditor/ckeditor.js"></script> -->
<script type="text/javascript" src="/gw/js/ea.js?random=<?php echo uniqid(); ?>"></script>
<script>
$(document).ready(function() {
    //작업모드
    $("#mode").val("INIT");
    $("#menuId").val('<?php echo $_POST["menuId"]; ?>');
    $("#userMenuId").val('<?php echo $_POST["userMenuId"]; ?>');
    $("#appbox").val('<?php echo $_POST["appbox"]; ?>');
    $.ajax({ 
        type: "POST", 
        url: "/gw/ea2/ea_appdoc_list_2.php",
        data: $("#mainForm").serialize(), 
        dataType: "json", 
        success: function(result) {
            $("#loginUserDeptId").val(result["loginUserDeptId"]);

            //검색
            var html = "";
//             $(result["searchList"]).each(function(i, info) {
//                 html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
//             });
//             $("#ddlSearchKind").append(html);

            //검색일자
            html = "";
            $.each(result["searchDateList"], function(i, info) {
                html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
            });
            $("#ddlSearchDate").append(html);

            $("#searchFrom").val(result["searchFrom"]);
            $("#searchTo").val(result["searchTo"]);
            $("#viewOrderField").val(result["viewOrderField"]);
            $("#viewOrderDirect").val(result["viewOrderDirect"]);

            html = "";
//             if ($.inArray($("#appbox").val(), ["mytray", "temptray", "intray", "extray", "outtray", "holdtray", "mantray", "workmantray"]) > -1) {
                //기안구분
//                 html += '<div class="col-md mb-2" ';
//                 if ($.inArray($("#appbox").val(), ["mytray", "temptray", "outtray", "mantray", "workmantray"]) > -1) {
//                     html += 'style="display: none;"';
//                 }
//                 html += '>';
//                 html += '<div class="d-flex">';
//                 html += '<div class="p-2">기안구분</div>';
//                 html += '<div class="flex-grow-1">';
//                 html += '<select class="form-control" id="ddlAppKind" name="ddlAppKind" onchange="onConditionChange()">';
//                 $(result["appKindList"]).each(function(i, info) {
//                     html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
//                 });
//                 html += '</select>';
//                 html += '</div>';
//                 html += '</div>';
//                 html += '</div>';
//             }
            // html += '<div class="col-lg-3 mb-2">';
            // html += '<div class="search-inline">';

            // html += '</div>';
            // html += '</div>';

            //수신참조함
            if ($("#appbox").val() == "rcptray") {
                html += '<div class="col-lg-4 mb-2">';
                html += '<div class="search-inline">';
                //문서종류
                html += '<label>문서종류</label>';
                html += '<select class="form-control" id="ddlDocType" name="ddlDocType" onchange="onConditionChange()">';
                html += '<option value="0">전체</option>';
                $(result["formList"]).each(function(i, info) {
                    html += '<option value="'+ info["formId"] +'">'+ info["formNm"] +'</option>';
                });
                html += '</select>';
                //열람구분
                html += '<label>열람구분</label>';
                html += '<select class="form-control" id="ddlReadKind" name="ddlReadKind" onchange="onConditionChange()">';
                html += '<option value="-999">전체</option>';
                $(result["readKindList"]).each(function(i, info) {
                    html += '<option value="' + info["key"] + '" ';
                    //미열람
                    if (info["key"] == 0) {
                        html += 'selected';
                    }
                    html += '>' + info["val"] + '</option>';
                });
                html += '</select>';
                html += '</div>';
                html += '</div>';
                var divCol = $("#divSearch div[class^='col-lg-']"); 
                divCol.eq(0).removeClass("col-lg-9").addClass("col-lg-5");
//                 divCol.eq(1).removeClass("col-lg-5").addClass("col-lg-3");
            }
            else {
                $("#divSearch").append('<input type="hidden" id="ddlReadKind" name="ddlReadKind" value="-999" />');
            }

            //상신함, 반려함, 기결함, 전체문서함, 사업장전체문서함
            if ($.inArray($("#appbox").val(), ["mytray", "extray", "outtray", "mantray", "workmantray"]) > -1) {
                html += '<div class="col-lg-4 mb-2" ';
                if ($.inArray($("#appbox").val(), ["extray"]) > -1) {
                    html += 'style="display: none;"';
                }
                html += '>';
                html += '<div class="search-inline">';
                //문서종류
                html += '<label>문서종류</label>';
                html += '<select class="form-control" id="ddlDocType" name="ddlDocType" onchange="onConditionChange()">';
                html += '<option value="0">전체</option>';
                $(result["formList"]).each(function(i, info) {
                    html += '<option value="'+ info["formId"] +'">'+ info["formNm"] +'</option>';
                });
                html += '</select>';
                //결재상태
                html += '<label>결재상태</label>';
                html += '<select class="form-control" id="ddlDocStat" name="ddlDocStat" onchange="onConditionChange()">';
                html += '<option value="99">전체</option>';
                $(result["docStatList"]).each(function(i, info) {
                    html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
                });
                html += '</select>';
                html += '</div>';
                html += '</div>';
                if ($.inArray($("#appbox").val(), ["extray"]) === -1) {
                    var divCol = $("#divSearch div[class^='col-lg-']"); 
                    divCol.eq(0).removeClass("col-lg-9").addClass("col-lg-5");
                    // divCol.eq(1).removeClass("col-lg-5").addClass("col-lg-3");
                }
            }
            else {
                $("#divSearch").append('<input type="hidden" id="ddlDocStat" name="ddlDocStat" value="99" />');
            }

            //미결함
            if ($("#appbox").val() == "intray") {
                html += '<div class="col-lg-4 mb-2">';
                html += '<div class="search-inline">';
                //문서종류
                html += '<label>문서종류</label>';
                html += '<select class="form-control" id="ddlDocType" name="ddlDocType" onchange="onConditionChange()">';
                html += '<option value="0">전체</option>';
                $(result["formList"]).each(function(i, info) {
                    html += '<option value="'+ info["formId"] +'">'+ info["formNm"] +'</option>';
                });
                html += '</select>';
                //열람구분
                html += '<label>열람구분</label>';
                html += '<select class="form-control" id="ddlRead" name="ddlRead" onchange="onConditionChange()">';
                html += '<option value="0">전체</option>';
                $(result["readList"]).each(function(i, info) {
                    html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
                });
                html += '</select>';
                html += '</div>';
                html += '</div>';
                var divCol = $("#divSearch div[class^='col-lg-']"); 
                divCol.eq(0).removeClass("col-lg-9").addClass("col-lg-5");
            }
            else {
                $("#divSearch").append('<input type="hidden" id="ddlRead" name="ddlRead" value="0" />');
            }

            if($.inArray($("#appbox").val(), ["mytray", "outtray", "mantray", "workmantray", "rcptray", "intray"]) == -1) {
                html += '<div class="col-lg-4 mb-2">';
                html += '<div class="search-inline">';
                //문서종류
                html += '<label>문서종류</label>';
                html += '<select class="form-control" id="ddlDocType" name="ddlDocType" onchange="onConditionChange()">';
                html += '<option value="0">전체</option>';
                $(result["formList"]).each(function(i, info) {
                    html += '<option value="'+ info["formId"] +'">'+ info["formNm"] +'</option>';
                });
                html += '</select>';
                html += '</div>';
                html += '</div>';
                var divCol = $("#divSearch div[class^='col-lg-']"); 
                divCol.eq(0).removeClass("col-lg-9").addClass("col-lg-5");
            }

            //시행함
//             if ($("#appbox").val() == "proctray") {
//                 //시행구분
//                 html += '<div class="col-md mb-2">';
//                 html += '<div class="d-flex">';
//                 html += '<div class="p-2">시행구분</div>';
//                 html += '<div class="flex-grow-1">';
//                 html += '<select class="form-control" id="ddlOperKind" name="ddlOperKind" onchange="onConditionChange()">';
//                 html += '<option value="-999">전체</option>';
//                 $(result["operKindList"]).each(function(i, info) {
//                     html += '<option value="' + info["key"] + '" ';
//                     //미시행
//                     if (info["key"] == 0) {
//                         html += 'selected';
//                     }
//                     html += '>' + info["val"] + '</option>';
//                 });
//                 $("#ddlOperKind").append(html);
//                 html += '</select>';
//                 html += '</div>';
//                 html += '</div>';
//                 html += '</div>';
//             }

//             html = "";
//             $(result["ynList"]).each(function(i, info) {
//                 html += '<div class="form-check-inline">';
//                 html += '<label class="form-check-label" for="pwChkYn' + info["key"] + '">';
//                 html += '<input type="radio" class="form-check-input" id="pwChkYn' + info["key"] + '" name="pwChkYn" value="' + info["key"] + '"> ' + info["val"];
//                 html += '</label>';
//                 html += '</div>';
//             });
//             $("#divPwChkYn").append(html);

//             if ($.inArray($("#appbox").val(), ["intray", "rcptray"]) > -1) {
//                 $("#divSearch").append('<div class="row">' + html + '</div>');
//             }
//             else {
//                 $("#divSearch div.row").append(html);
//             }
            $("#divSearch div.row").prepend(html);

            if ($.inArray($("#appbox").val(), ["mytray", "intray", "outtray", "proctray", "rcptray"]) > -1) {
                $("#tblAppDocList thead th:eq(0)").find("input").show();

                //미결함
                if ($("#appbox").val() == "intray") {
                    //일괄결재 버튼 표시
                    $("#btnBatchSign").closest("div").show();
                }
                else {
                    //수신참조함
                    if ($("#appbox").val() == "rcptray") {
                        //일괄열람 버튼 표시
                        $("#btnBatchRead").closest("div").show();
                    }

                    //결재함 이동 버튼 표시
                    $("#btnShowMoveTray").closest("div").show();

                    if (Object.keys(result["userTrayList"]).length > 0) {
                        html = "";
                        $(result["userTrayList"]).each(function(i, info) {
                            html += '<option value="' + info["menuId"] + '">' + info["menuNm"] + '</option>';
                        });
                        $("#ddlMoveTray").append(html);
                    }
                }
            }
            else {
                $("#tblAppDocList thead th:eq(0)").find("input").hide();

                //보관함
                if ($("#appbox").val() == "temptray") {
                    $("#tblAppDocList thead th:eq(1)").text("문서분류");
                    $("#tblAppDocList thead th:eq(3)").text("작성자 (작성일)");
                    $("#tblAppDocList thead th").last().text("편집");

                    //보관함의 경우 편집에서 삭제 가능
                    $("#btnDeleteTempAppDoc").show();
                }
            }

            $.each(result["envOption"], function (key, val) {
                $("<input>").attr({
                    type: "hidden",
                    id: key,
                    name: key,
                    value : val
                }).appendTo($("#mainForm"));
            });

            //결재이관사용여부
            $("#ebMoveYn").val(result["ebMoveYn"]);
            if(result["ebMoveYn"] == 1) {
                $("#divEBMenu").show();
            }
            else {
                $("#divEBMenu").hide();
            }
            //상신시 이관문서함, 보존연한 수정여부
            if(result["dbEditMenuPlyYn"] == 1) {
                $("#btnEBChange").show();
            }
            else {
                $("#btnEBChange").hide();
            }
            //결재 완료후 이관시 문서함 선택 순서
            $("#ebKind2").val(result["ebKind2"]);

            var html = "";
            //중요도
            $(result["kindImportantList"]).each(function(i, info) {
                html += '<option value="' + info["key"] + '">' + info["val"] + '</option>';
            });
            $("#ddlImprotantKind").append(html);

            //결재라인 수정 여부
            $("#eaAppLineEdit").val(result["eaAppLineEdit"]);
            //최종결재자 예결 불가 여부
            $("#eaLastPreApp").val(result["eaLastPreApp"]);

            //전자결재 첨부파일 리스트 인쇄옵션 - 인쇄안함
            if (result["eaAttachPrint"] == "0") {
                $("#txtAttachedList").closest("div.row").addClass("notPrint");
            }
            //특이사항 출력 옵션
            $("#eaPrintReplyPosition").val(result["eaPrintReplyPosition"]);
            //결재의견 출력여부
            if ($("#appCommentPrint").val() == "0") {
                $("#titleDivReplyList").addClass("notPrint");
                $("#divReplyList").closest("div.row").addClass("notPrint");
                $("#divPrintReplyList").addClass("notPrint");
            }
            else {
                //본문 다음장 출력
                if (result["eaPrintReplyPosition"] == "0") {
                    $("#titleDivReplyList").addClass("notPrint");
                    $("#divReplyList").closest("div.row").addClass("notPrint");
                }
                //본문 아래 출력
                else {
                    $("#titleDivReplyList").addClass("includePrint");
                    $("#divPrintReplyList").addClass("notPrint");
                }
            }

            //반려시 반려팝업 사용 여부
            $("#eaReturnReason").val(result["eaReturnReason"]);
        },
        complete: function() {
            if ($("#parameters").val() != "") {
                var params = ($("#parameters").val()).split("|");
                var paramList = {};
                for (var i = 0; i < params.length; i++) {
                    var temp = params[i].split("=");
                    paramList[temp[0]] = temp[1];
                }
                if (paramList["formId"] === undefined || paramList["formId"] == "") {
                    $("#mode").val("INFO_FORM");
                    $("#docId").val(paramList["docId"]);
                    $.ajax({ 
                        type: "POST", 
                        url: "/gw/ea2/ea_appdoc_list_2.php",
                        data: $("#mainForm").serialize(), 
                        dataType: "json", 
                        success: function(result) {
                            onBtnDetailAppDocClick($("#docId").val(), result["formId"]);
                        }
                    });
                }
                else {
                    onBtnDetailAppDocClick(paramList["docId"], paramList["formId"]);
                }
                $("#parameters").val("");
            }
            getSubMenuCnt();
            onConditionChange();
//             $("#modalDetailAppDoc").data("processing", false);
            if ($("#appPwCheck").val() == "1") {
                $("#userPwd").prop("required", false);
                $("#userPwd").closest("div").hide();
            }
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    })

    //상세창 닫을 경우
    $("#modalDetailAppDoc").on('hide.bs.modal', function() {
        //미결함, 수신참조함
        if ($.inArray($("#appbox").val(), ["intray", "rcptray"]) > -1) {
            //읽음 확인
            onConditionChange();
        }

        clearDetailAppDoc();
        $("#modalDetailAppDoc").data("processing", false);
//         //첨부파일 지우기
//         $("#txtAttachedList").empty();
//         //참조문서 지우기
//         $("#txtRelatedDocList tbody").empty();
    });
    //편집창 닫을 경우
    $("#modalEditAppDoc").on('hide.bs.modal', function () {
        //결재라인 지우기
        $("input[type='hidden'][name='appAgrLine[]'").remove();
        $("input[type='hidden'][name^='essential'").remove();
        if($("#ebMoveYn").val() == 1) {
            $("#divEBMenu").closest("div.row").show();
        }
        else {
            $("#divEBMenu").closest("div.row").hide();
        }
        $("#textareaContent").closest("div").show();
        // CKEDITOR.instances.textareaContent.setData("");
        $("#txtContent").val("");
        $("#htmlContent").val("");
        //첨부파일 지우기
        $("#divAttachedList").empty();
        $("#divNewAttachedList").empty();
        //참조문서 지우기
        $("#tblRelatedDocList tbody").empty();
        //유효성 검사 지우기
        $("#mainForm").removeClass('was-validated');
        $("#setAppLine").closest(".form-group").find(".invalid-feedback").html("");
        $("#detectEditAppDoc").val("N");
    });

    //IE
    if (!!navigator.userAgent.match(/Trident\/7\./)) {
         $("#modalDetailAppDoc").removeClass("fade");
         $("#modalEditAppDoc").removeClass("fade");
         $("#modalConfirmDel").removeClass("fade");
    }

    var $th = $('#tblAppDocList').find('thead th');
    $('#tblAppDocList').closest("div.tableFixHead").on('scroll', function() {
        $th.css('transform', 'translateY('+ this.scrollTop +'px)');
    });

//     var maxWidth = 620;
//     CKEDITOR.replace('textareaContent', {
//         width: '100%',
//         height: "21.5rem",
// //         resize_maxHeight: "395px",
//         autoParagraph: false,
//         allowedContent: true,
//         enterMode : CKEDITOR.ENTER_BR, 
// //         removeButtons: 'Styles,Format,Anchor',
//         removeButtons: '',
//         removePlugins: 'elementspath',
//         resize_enabled: false,
//         specialChars : CKEDITOR.config.specialChars.concat( [ [ '&#8361;', '원화 기호' ] ] ),
//         extraPlugins : 'font,colorbutton,justify,tableresize,specialchar',
//         toolbar: [
//             { name: 'document', items: [ 'Source' ] },
//             { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
//             { name: 'insert', items: [ 'Image', 'Table', 'SpecialChar' ] },
//             { name: 'styles', items: [ 'FontSize' ] },
//             { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
//             { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
//             { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
//             { name: 'tools', items: [ 'Maximize' ] }
//         ],
//         filebrowserImageUploadUrl: '/gw/cm/cm_file_upload.php?moduleName=EA&type=Images',
// //         forcePasteAsPlainText : true,
//         contentsCss: 'body{font-family: Arial,"Malgun Gothic",sans-serif;font-size: 14px;margin: 20px;width: 630px;line-height:1.5;}table{border-collapse: collapse;}',
//         on: {
//             instanceReady: function(evt) {
//                 CKEDITOR.instances.textareaContent.widgets.registered.uploadimage.onUploaded = function(e) {
//                     var img = this.parts.img.$;
//                     var width = e.responseData.width||img.naturalWidth;
//                     var height = e.responseData.height||img.naturalHeight;
//                     if (width > maxWidth) {
//                         height = Math.round(maxWidth * (height / width));
//                         width = maxWidth; 
//                     }
//                     this.replaceWith( '<img src="' + e.url + '" ' + 'width="' + width + '" ' + 'height="' + height + '">' );
//                 }
//             }
//         }
//     });
//     CKEDITOR.instances.textareaContent.on('key', function() {
//         $("#detectEditAppDoc").val("Y");
//     });
//     CKEDITOR.instances.textareaContent.on('paste', function (evt) {
//         evt.data.dataValue = evt.data.dataValue.replace(/<span[^>]*?>/g, '');
//         evt.data.dataValue = evt.data.dataValue.replace(/<font[^>]*?>/g, '');
//     });

//     CKEDITOR.replace('textareaContent_edit', {
//         width: '100%',
//         height: "21.5rem",
// //         resize_maxHeight: "395px",
//         autoParagraph: false,
//         allowedContent: true,
//         enterMode : CKEDITOR.ENTER_BR, 
// //         removeButtons: 'Styles,Format,Anchor',
//         removeButtons: '',
//         removePlugins: 'elementspath',
//         resize_enabled: false,
//         extraPlugins : 'colorbutton,justify,tableresize,specialchar',
//         specialChars : CKEDITOR.config.specialChars.concat( [ [ '&#8361;', '원화 기호' ] ] ),
//         toolbar: [
//             { name: 'document', items: [ 'Source' ] },
//             { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
//             { name: 'insert', items: [ 'Image','Table', 'SpecialChar' ] },
//             { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
//             { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
//             { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
//             { name: 'tools', items: [ 'Maximize' ] }
//         ],
//         filebrowserImageUploadUrl: '/gw/cm/cm_file_upload.php?moduleName=EA&type=Images',
// //         forcePasteAsPlainText : true,
//         contentsCss: 'body{font-family: Arial,"Malgun Gothic",sans-serif;font-size: 14px;margin: 20px;width: 630px;line-height:1.5;}table{border-collapse: collapse;}',
//         on: {
//             instanceReady: function(evt) {
//                 CKEDITOR.instances.textareaContent_edit.widgets.registered.uploadimage.onUploaded = function(e) {
//                     var img = this.parts.img.$;
//                     var width = e.responseData.width||img.naturalWidth;
//                     var height = e.responseData.height||img.naturalHeight;
//                     if (width > maxWidth) {
//                         height = Math.round(maxWidth * (height / width));
//                         width = maxWidth; 
//                     }
//                     this.replaceWith( '<img src="' + e.url + '" ' + 'width="' + width + '" ' + 'height="' + height + '">' );
//                 }
//             }
//         }
//     });
//     CKEDITOR.instances.textareaContent_edit.on('paste', function (evt) {
//         evt.data.dataValue = evt.data.dataValue.replace(/<span[^>]*?>/g, '');
//         evt.data.dataValue = evt.data.dataValue.replace(/<font[^>]*?>/g, '');
//     });

//     CKEDITOR.on('dialogDefinition', function(evt) {
//         // Take the dialog name and its definition from the event data.
//         var dialogName = evt.data.name;
//         var dialog = evt.data.definition.dialog;

//         dialog.on('show', function () {
//             //이미지 정보 탭
//             if (dialogName == 'image') {
//                 //너비
//                 var ele = this.getContentElement('info', 'txtWidth');
//                 //유효성 검사
//                 ele.validate = function(e) {
//                     var y=/(^\s*(\d+)((px)|\%)?\s*$)|^$/i;
//                     var a=this.getValue().match(y);
//                     a=!(!a||0===parseInt(a[1],10));
//                     if (a) {
//                         if(ele.getValue() > maxWidth) {
//                             alert("이미지 너비는 " + maxWidth + "px 이하로 지정해주세요.");
//                             a = !a;
//                         }
//                     }
//                     else {
//                         alert(CKEDITOR.instances.textareaContent.lang.common.invalidLength.replace("%1",CKEDITOR.instances.textareaContent.lang.common.width).replace("%2","px, %"));
//                     }
//                     return a;
//                 }
//             }
//         });
//     });

    //결재함 이동 버튼
    $("#btnShowMoveTray").on("click", onBtnShowTrayClick);
    //이동 버튼 - 결재함 이동
    $("#btnMoveTray").on("click", onBtnMoveTrayClick);
    //일괄결재 버튼
    $("#btnBatchSign").on("click", onBtnBatchSignClick);
    //일괄열람 버튼
    $("#btnBatchRead").on("click", onBtnBatchReadClick);
    //검색조건 - 입력란
    $("#txtSearchValue").on("keyup", function(e) {
        var cd = e.which || e.keyCode;
        //Enter 키
        if (cd == 13) {
            onBtnSearchAppDocClick();
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    //검색 버튼
    $("#btnSearchAppDoc").on("click", onBtnSearchAppDocClick);
//     //검색조건 - 일자
//     $("#ddlSearchDate").on("change", onConditionChange);
    //검색조건 - 일자(시작)
    $("#searchFrom").on("blur", function(e) {
        onSearchDateChange('from');
    });
    $("#searchFrom").on("keyup", function(e) {
        var cd = e.which || e.keyCode;
        //Enter 키
        if (cd == 13) {
            if (onSearchDateChange('from')) {
                onConditionChange();
            }
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    //검색조건 - 일자(종료)
    $("#searchTo").on("blur", function(e) {
        onSearchDateChange('to');
    });
    $("#searchTo").on("keyup", function(e) {
        var cd = e.which || e.keyCode;
        //Enter 키
        if (cd == 13) {
            if (onSearchDateChange('to')) {
                onConditionChange();
            }
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    $("#btnSearchDate").on('click', onConditionChange);
    //수정 버튼
    $("#btnModifyAppDoc").on("click", onBtnModifyAppDocClick);
    //삭제 버튼
    $("#btnConfirmDelete").on("click", onBtnDeleteAppDocClick);
    //상신취소 버튼
    $("#btnCancelAppDoc").on("click", onBtnCancelAppDocClick);
    //결재이력 버튼
    $("#btnShowSignInfo").on("click", onBtnShowSignInfoClick);
    //보류 버튼
    $("#btnHoldAppDoc").on("click", onBtnHoldAppDocClick);
    //결재 취소 버튼
    $("#btnCancelAppAgr").on("click", onBtnCancelAppAgrClick);
    //재작성 버튼
    $("#btnResubmitAppDoc").on("click", onBtnResubmitAppDocClick);
    //저장 버튼
    $("#btnSaveAppDoc").on("click", onBtnSaveAppDocClick);
    $("#btnSaveTempAppDoc").on("click", onBtnSaveTempAppDocClick);
    //수정취소
    $("#btnCancelEditAppDoc").on("click", onBtnCancelEditAppDocClick);
    //닫기 버튼
    $("button[name='btnCloseEditAppDoc']").on("click", onBtnCloseEditAppDocClick);
    //결재특이사항 삭제 버튼
    $("#btnConfirmDelReply").on("click", onBtnDeleteReplyClick);

    //날짜 min, max값 넣기
    dateMinMaxAppend();
});

//검색 버튼 클릭
function onBtnSearchAppDocClick() {
    var elem = $("#txtSearchValue");
    elem.val(elem.val().trim());
    if (elem.data("oldVal") != elem.val()) {
        onConditionChange();
    }
}

//일자 변경
function onSearchDateChange(type) {
    var from = new Date($("#searchFrom").val());
    var to = new Date($("#searchTo").val());
    if (isNaN(from.getTime())) {
        $("#searchFrom").css("background-color", "pink");
        return false;
    }
    else {
        $("#searchFrom").removeAttr("style");
    }
    if (isNaN(to.getTime())) {
        $("#searchTo").css("background-color", "pink");
        return false;
    }
    else {
        $("#searchTo").removeAttr("style");
    }
    //시작일이 미래일 경우
    if (from > to) {
        if (type == "from") {
            //종료일을 시작일로 변경
            $("#searchTo").val($("#searchFrom").val());
        }
        else if (type == "to") {
            //시작일을 종료일로 변경
            $("#searchFrom").val($("#searchTo").val());
        }
    }
    return true;
}

//조건 변경 시 검색
function onConditionChange() {
    if (onSearchDateChange("")) {
        var elem = $("#ddlSearchDate");
        elem.data('oldVal', elem.val());

        elem = $("#searchFrom");
        elem.data('oldVal', elem.val());

        elem = $("#searchTo");
        elem.data('oldVal', elem.val());

        elem = $("#txtSearchValue");
        elem.val(elem.val().trim());
        elem.data('oldVal', elem.val());

        onPageNoClick(1, "", false);
    }
//     //작업모드
//     $("#mode").val("LIST");
//     $("#pageNo").val("1");
//     $.ajax({ 
//         type: "POST", 
//         url: "/gw/ea2/ea_appdoc_list_2.php",
//         data: $("#mainForm").serialize(),
//         dataType: "json",  
//         success: function(result) {
//             //세션 만료일 경우
//             if (result["session_out"]) {
//                 //로그인 화면으로 이동
//                 onLogoutClick();
//             }

//             showInfoList(result["infoList"]);
//             //현재 페이지
//             $("#pageNo").val(result["pageNo"]);
//             //페이지 목록
//             $("#pageList").empty().append(result["pageList"]);
//         },
//         beforeSend:function(){
//             $("#divSearch").find("input").prop("readonly", true);
//             $("#divSearch option").not(":selected").prop("disabled", true)
//             $("#btnSearchAppDoc").find("span.spinner-border").show();
//         },
//         complete: function() {
//             $("#divSearch").find("input").prop("readonly", false);
//             $("#divSearch option").not(":selected").prop("disabled", false)
//             $("#btnSearchAppDoc").find("span.spinner-border").hide();
//         },
//         error: function (request, status, error) {
//             alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
//         }
//     });
}

function showInfoList(list) {
    $("#chkAll").prop("checked", false);
    $('#tblAppDocList').closest('div.tableFixHead').scrollTop(0);
    $("#tblAppDocList tbody").empty();
    var html = "";
    $(list).each(function(i, info) {
        html += '<tr class="row" ';
        if (info["readYn"] == "N") {
            html += ' style="font-weight: bold;" ';
        }
        html += '>';
        html += '<td class="col-md-1 col-1 col-w-chk">';
        if ($.inArray($("#appbox").val(), ["mytray", "intray", "outtray", "proctray", "rcptray"]) > -1) {
            html += '<div class="h-100 d-flex align-items-center">';
            html += '<input type="checkbox" name="chkDocId[]" value="' + info["chkDocId"] + '" onclick="onChkDocIdClick()" />';
            html += '</div>';
        }
        html += '</td>';
        html += '<td class="col-md-3 d-none d-md-block">';
        html += '<div class="h-100 d-flex align-items-center">';
        html += info["docSeqCd"];
        html += '</div>';
        html += '</td>';
        html += '<td class="col-md d-none d-md-block notAlign text-ellipsis">';
        html += '<div class="h-100 d-flex align-items-center notAlign">';
        html += '<div class="ellipsisLongTxt">';
        //보관함
        if ($("#appbox").val() == "temptray") {
            html += '<a href="javascript:void(0);" onclick="onBtnEditAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
        }
        else {
            //열람권한 체크
            if (info["chkView"] == "Y") {
                html += '<a href="javascript:void(0);" onclick="onBtnDetailAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
            }
        }
        html += info["subject"];
        if (info["existAttachFile"]) {
//             html += '<i class="fa-solid fa-paperclip"></i>';
            html += '&nbsp;&nbsp;<i class="fa-solid fa-floppy-disk"></i>';
        }
        //보관함
        if ($("#appbox").val() == "temptray") {
            html += '<a href="javascript:void(0);" onclick="onBtnEditAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
        }
        else {
            if (info["chkView"] == "Y") {
                html += '</a>';
            }
        }
        html += '</div>';
        html += '</div>';
        html += '</td>';
//         html += '<td class="col-md-2 d-none d-md-block">';
//         html += '<div class="h-100 d-flex align-items-center">';
//         html += info["drafter"];
//         html += '</div>';
//         html += '</td>';
//         html += '<td class="col-md-2 d-none d-md-block">';
//         html += '<div class="h-100 d-flex align-items-center">';
//         html += info["reportDate"];
//         html += '</div>';
        html += '<td class="col-md-2 d-none d-md-block">';
        html += '<div class="h-100 d-flex align-items-center">';
        html += info["drafter"];
        html += '</div>';
        html += '</td>';
        html += '<td class="col-md-block d-md-none col-9 notAlign text-ellipsis">';
        //보관함
        if ($("#appbox").val() == "temptray") {
            html += '<a href="javascript:void(0);" onclick="onBtnEditAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
        }
        else {
            //열람권한 체크
            if (info["chkView"] == "Y") {
                html += '<a href="javascript:void(0);" onclick="onBtnDetailAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
            }
        }
        html += info["subject"];
        if (info["existAttachFile"]) {
            html += '&nbsp;&nbsp;<i class="fa-solid fa-floppy-disk"></i>';
        }
        //보관함
        if ($("#appbox").val() == "temptray") {
            html += '</a>';
        }
        else {
            if (info["chkView"] == "Y") {
                html += '</a>';
            }
        }
        html += '<br /><div style="font-size:0.75rem">' + info["drafterMobile"] + '</div>';
        html += '</td>';
        html += '<td class="col-md-2 d-none d-md-block col-w-state">';
        html += '<div class="h-100 d-flex align-items-center">';
        html += info["note"];
        html += '</div>';
        html += '</td>';
        //보관함
        if ($("#appbox").val() == "temptray") {
            html += '<td class="col-md-1 col-2 col-w-btn">';
            html += '<div class="h-100 d-flex align-items-center">';
            html += '<button type="button" class="btn btn-primary" onclick="onBtnEditAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">편집</button>';
            html += '</div>';
            html += '</td>';
        }
        else {
            html += '<td class="col-md-1 col-2 col-w-btn">';
            //열람권한 체크
            if (info["chkView"] == "Y") {
                html += '<div class="h-100 d-flex align-items-center">';
                html += '<button type="button" class="btn btn-primary" name="btnDetailAppDoc" onclick="onBtnDetailAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">상세</button>';
                html += '</div>';
            }
            html += '</td>';
            html += '</tr>';
        }
    });

    $("#tblAppDocList tbody").append(html);
    onAfterChkDocClick();
}

//결재함 이동 클릭
function onBtnShowTrayClick() {
    $("#ddlMoveTray").val("0");
    onDdlMoveTrayChange();

    $("#modalMoveTray").modal("show");
}

//결재함 이동에서 결재함 선택 시
function onDdlMoveTrayChange() {
    //결재함 미선택
    if ($("#ddlMoveTray").val() == "0") {
        $("#btnMoveTray").prop("disabled", true);
    }
    //결재함 선택
    else {
        $("#btnMoveTray").prop("disabled", false);
    }
}

//결재함 이동
function onBtnMoveTrayClick() {
    $("#mode").val("MOVE_TRAY");
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

            $("#modalMoveTray").modal("hide");

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        complete: function() {
            getSubMenuCnt();
            onConditionChange();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//체크박스 전체 선택/해제
function onChkAllDocClick(obj) {
    onChkAllClick(obj, "chkDocId");

    onAfterChkDocClick();
}

//문서 별 체크박스 선택 변경 시
function onChkDocIdClick() {
    whenChkClick_chkAll("chkDocId", "chkAll");

    onAfterChkDocClick();
}

function onAfterChkDocClick() {
    if ($("input[type='checkbox'][name='chkDocId[]']:checked").length > 0) {
        //일괄결재 버튼
        $("#btnBatchSign").prop("disabled", false);
        //일괄열람 버튼
        $("#btnBatchRead").prop("disabled", false);
        //결재함 이동 버튼
        $("#btnShowMoveTray").prop("disabled", false);
    }
    else {
        //일괄결재 버튼
        $("#btnBatchSign").prop("disabled", true);
        //일괄열람 버튼
        $("#btnBatchRead").prop("disabled", true);
        //결재함 이동 버튼
        $("#btnShowMoveTray").prop("disabled", true);
    }
}

//일괄결재 버튼 클릭
function onBtnBatchSignClick() {
    $("#txtSignKind").text("batch_sign");

    $("#modalConfirmSign .modal-title").text("일괄결재");
    $("#msgSign").text("일괄 결재 하시겠습니까?");
    $("#returnReason").closest("div").hide();

    $("#modalConfirmSign").modal("show");
}

//일괄열람 버튼 클릭
function onBtnBatchReadClick() {
    $("#mode").val("BATCH_READ");
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

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        complete: function() {
            onPageNoClick($("#pageNo").val(), "", true);
            getSubMenuCnt();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//편집 버튼 클릭
function onBtnEditAppDocClick(docId, formId) {
    $("#docId").val(docId);
    $("#formId").val(formId);
    $("#actType").val("U");

    //보관함
    if ($("#appbox").val() == "temptray") {
        $("#btnCancelEditAppDoc").hide();
    }
    else {
        $("#btnCancelEditAppDoc").show();
    }

    editAppDoc();
}

function clearDetailAppDoc() {
    $("#divBtnList").find("button:gt(1)").hide();
    $("#btnEditAppLine").hide();
    $("#txtDF10").closest("div").find("button").remove();
    $("#modalDetailAppDoc .modal-footer").find("button[id^='btn']").not("button[id='btnShowSignInfo']").hide();
    //첨부파일 지우기
    $("#txtAttachedList").empty();
    //참조문서 지우기
    $("#txtRelatedDocList").empty();
}

//상세 버튼 클릭
function onBtnDetailAppDocClick(docId, formId) {
    if ($("#modalDetailAppDoc").data("processing")) {
        return false;
    }
    $("#modalDetailAppDoc").data('processing', true);
    $("input:button[name='btnDetailAppDoc']").prop("disabled", true);
    clearDetailAppDoc();
    $("#docId").val(docId);
    $("#formId").val(formId);

    $("#appDocTxtContents").empty();
    $.ajax({
        url: "/gw/ea2/form/form_" + $("#formId").val() + "_detail.php", 
        success: function(result) {
            $("#appDocTxtContents").append(result);
        },
        complete: function() {
            showDetailAppDoc();
        }
    })
//     $("#appDocTxtContents").load("/gw/ea2/form/form_" + $("#formId").val() + "_detail.php");
}

function showDetailAppDoc() {
    //작업모드
    $("#mode").val("DETAIL");
    $("#divDetailAppLine").empty();
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

            if (result["authDoc"] == 0) {
                $("#modalAlertMsg .modal-body").html("이 문서를 볼 권한이 없습니다.");
                $("#modalAlertMsg").modal("show");
                if ($('#modalDetailAppDoc').hasClass('show')) {
                    $("#modalDetailAppDoc").modal("hide");
                }
            }
            else {
                var docInfo = result["appDocInfo"];
                $("#kindImportant").html(docInfo["txtKindImportant"]);
                $("#modalDetailAppDoc .modal-title").text(docInfo["formNm"]);
                $("#txtDF01").text(docInfo["DF01"]);
                $("#txtDF02").text(docInfo["DF02"]);
                $("#txtDF03").text(docInfo["DF03"]);
                $("#txtDF04").text(docInfo["userNm"] + " " + docInfo["gradeNm"]);
                $("#txtDF09").text(docInfo["DF09"]);
                $("#txtDF10").text(docInfo["subject"]);
                var contents = $("<div>" + docInfo["contents"] + "</div>");
                contents.find("title").remove();
                contents.find("meta").remove();
                contents.find("style").remove();
                contents.find("link").remove();
                $("#divDF11").empty().append(contents);
                $("#txtDFPJT").text(docInfo["DFPjtNm"]);
                $("#txtDF20").text(docInfo["DF20"]);
                $("#txtDF21").text(docInfo["DF21"]);
                $("#txtDF22").text(docInfo["DF22"]);
                $("#txtDF23").text(docInfo["DF23"]);
                $("#txtDF24").text(docInfo["DF24"]);
                $("#txtDF34").text(docInfo["DF34"]);

                $("#seq").val(docInfo["seq"]);
                $("#nowApp").val(docInfo["nowApp"]);
                $("#appDocUserId").val(docInfo["userId"]);
                $("#proKind").val(docInfo["proKind"]);
                $("#proId").val(docInfo["proId"]);
                $("#docCoId").val(docInfo["coId"]);
                $("ebMenuId").val(docInfo["menuId"]);

                $("#appAgrCnt").val(docInfo["appAgrCnt"]);

                $("#appKindDisplay").val(result["appKindDisplay"]);
                var html = drawAppLine(result["appLine"]);
                $("#divDetailAppLine").append(html);

                var canEditList = result["canEditList"];

                //수신참조
                $("#recipientIds").val(result["recipientIds"]);
                var nms = "";
                if (result["recipientIds"] != "") {
//                     html = '<button type="button" class="btn btn-success btn-sm ml-2" onclick="onBtnListRecipientClick()">수신참조리스트</button>';
//                     $("#txtRecipientNms").closest("div").append(html);
                    nms += '<a href="javascript:void(0);" onclick="onBtnListRecipientClick(' + $("#docId").val() + ')">';
                }
                nms += result["recipientNms"]
                if (result["recipientIds"] != "") {
                    nms += '</a>';
                }
                $("#txtRecipientNms").html(nms);
                var width = 0;
                if (canEditList["recipient"] == "Y") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" style="float: left;" onclick="onBtnSelectAppLineClick(\'save\', \'recipient\')">수정</button>';
                    $("#txtRecipientNms").closest("div[class^='col']").append(html);
                    width += 3.5;
                }
                if (canEditList["showRecipientHis"] == "Y") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" style="float: left;" onclick="onBtnListROHisClick(\'recipient\')">이력</button>';
                    $("#txtRecipientNms").closest("div[class^='col']").append(html);
                    width += 3.5;
                }
                if (width > 0) {
                    $("#txtRecipientNms").css({"float": "left", "max-width": "calc(100% - " + width + "rem)"});
                }
                else {
                    $("#txtRecipientNms").css({"max-width": "100%"});
                }
                $("#txtRecipientNms").addClass("ellipsisLongTxt");

                //로그인 유저가 시행자인 경우
                if (result["operator"]) {
                    $("#btnOperateDoc").show();
                }
                else {
                    $("#btnOperateDoc").hide();
                }
                //시행자
                $("#operatorIds").val(result["operatorIds"]);
                nms = "";
                if (result["operatorIds"] != "") {
//                     html = '<button type="button" class="btn btn-success btn-sm ml-2" onclick="onBtnOperatorListClick()">시행자리스트</button>';
//                     $("#txtOperatorNms").closest("div").append(html);
                    nms += '<a href="javascript:void(0);" onclick="onBtnOperatorListClick(' + $("#docId").val() + ')">';
                }
                nms += result["operatorNms"]
                if (result["operatorIds"] != "") {
                    nms += '</a>';
                }
                $("#txtOperatorNms").html(nms);
                width = 0;
                if (canEditList["operator"] == "Y") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" style="float: left;" onclick="onBtnSelectAppLineClick(\'save\', \'operator\')">수정</button>';
                    $("#txtOperatorNms").closest("div[class^='col']").append(html);
                    width += 3.5;
                }
                if (canEditList["showOperatorHis"] == "Y") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" style="float: left;" onclick="onBtnListROHisClick(\'operator\')">이력</button>';
                    $("#txtOperatorNms").closest("div[class^='col']").append(html);
                    width += 3.5;
                }
                if (width > 0) {
                    $("#txtOperatorNms").css({"float": "left", "max-width": "calc(100% - " + width + "rem)"});
                }
                else {
                    $("#txtOperatorNms").css({"max-width": "100%"});
                }
                $("#txtOperatorNms").addClass("ellipsisLongTxt");

                //본문 수정
                if (canEditList["contents"] != "N") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" onclick="onBtnEditContentsClick(\'' + result["canEditList"]["contents"] + '\')">수정</button>';
                    $("#txtDF10").append(html);
                }
                if (canEditList["showContentsHis"] == "Y") {
                    html = '<button type="button" class="btn btn-info btn-sm py-0 ml-2" onclick="onBtnListContentsHisClick(\'W\')">내역</button>';
                    $("#txtDF10").append(html);
                }

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

                //반려 문서 목록
                html = "";
                $(result["appDocReturnList"]).each(function(i, info) {
                    html += '<div class="row mb-2">';
                    html += '<div class="col bg-warning" style="cursor: pointer;" onclick="onBtnDetailAppDocClick(\'' + info["docId"] + '\', \'' + info["formId"] + '\')">';
                    html += (i + 1) + ' ' + info["docNm"];
                    html += '</div>';
                    html += '</div>';
                });
                if (html != "") {
                    $("#divAppDocReturnList").empty().append(html);
                    $("#divAppDocReturnList").show();
                }
                else {
                    $("#divAppDocReturnList").empty();
                    $("#divAppDocReturnList").hide();
                }

                if (Object.keys(result["relatedDocList"]).length > 0) {
                    //참조문서
                    html = "";
                    $(result["relatedDocList"]).each(function(i, info) {
//                         html += '<tr class="row">';
//                         html += '<td class="col-md-5 col-5">';
                        //품의번호
                        html += '<div class="ellipsisLongTxt">';
                        html += '<a href="javascript:void(0);" onclick="showEaAppDocDetail(' + info["docId"] + ',' + info["formId"] + ')">';
                        html += '<i class="fa-solid fa-thumbtack"></i> ' + " [" + info["docSeqCd"] + "] " + info["subject"];
                        html += '</a>';
                        html += '</div>';
//                         html += '</td>';
//                         html += '<td class="col-md-2 col-3">';
//                         //문서분류
//                         html += info["formNm"];
//                         html += '</td>';
//                         html += '<td class="col-md-5 col-4 notAlign">';
//                         //제목
//                         html += info["subject"];
//                         html += '</td>';
//                         html += '</tr>';
                    });
//                 $("#txtRelatedDocList tbody").append(html);
                    $("#txtRelatedDocList").append(html);
                    $("#txtRelatedDocList").closest("div.row").show();
                }
                else {
                    $("#txtRelatedDocList").closest("div.row").hide();
                }

                if (Object.keys(result["attachFileList"]).length > 0) {
                    //첨부파일
                    html = "";
                    $(result["attachFileList"]).each(function(i, info) {
                        html += '<div class="ellipsisLongTxt">';
                        html += '<a href="/gw/cm/cm_file_download.php?mKind=EA&fid=' + info["attachId"] + '" target="_blank">';
//                         html += '<a href="' + info["fileDownloadLink"] + '" target="_blank">';
                        html += '<i class="fa-regular fa-file-lines"></i> ' + info["oriFileNm"];
                        html += '</a>'; 
                        html += '<span style="display: none;" class="txtAttachFileId">' + info["attachId"] + '</span>';
                        html += '<span style="display: none;" class="txtAttachFile">' + info["fileNm"] + '</span>';
                        html += '<span style="display: none;" class="txtAttachOriFileNm">' + info["oriFileNm"] + '</span>';
                        html += '</div>';
                    });
                    $("#txtAttachedList").append(html);
                    $("#txtAttachedList").closest("div.row").show();
                }
                else {
                    $("#txtAttachedList").closest("div.row").hide();
                }

                $("#divReplyNote_new").empty();
                showReplyList(result["replyList"]);

                if (result["inputDocReply"] == "Y") {
                    $("#btnAddReply").show();
                }
                else if (result["inputDocReply"] == "N") {
                    $("#btnAddReply").hide();
                }

                $.each(result["showBtnList"], function(i, val) {
                    $("#" + val).show();
                });

                //휴가신청서
                if ($("#formId").val() == "10012") {
                    //연차 휴가 현황
                    if (result["isTblAnnvacShow"] == "Y") {
                        var html = '';
                        $(result["annualVacationList"]).each(function(i, info) {
                            html += '<tr class="row">';
                            html += '<td class="col-3" style="text-align:center">';
                            html += info["userNm"];
                            html += '</td>';
                            html += '<td class="col-3" style="text-align: right;">';
                            html += info["baseCnt"];
                            html += '</td>';
                            html += '<td class="col-3" style="text-align: right;">';
                            html += info["remCnt"];
                            html += '</td>';
                            html += '<td class="col-3" style="text-align: right;">';
                            html += info["useDay"];
                            html += '</td>';
                            html += '</tr>';
                        });
                        $("#txtTblAnnualVacation > tbody").append(html);
                        $("#txtTblAnnualVacation").show();
                    }
                    else {
                        $("#txtTblAnnualVacation").hide();
                    }
                }

                $("#alterSign").val(result["alterSign"]);
                var myAppAgr = result["myAppAgr"];
                $("#myAppAgrUser").val(myAppAgr["docUserId"]);
                $("#myAppKind").val(myAppAgr["appKind"]);

                $("#modalDetailAppDoc .imgLogo").attr("src", result["logo"]);

                if (!$('#modalDetailAppDoc').hasClass('show')) {
                    $("#modalDetailAppDoc").modal("show");
                }
            }
        },
        complete: function() {
            $("input:button[name='btnDetailAppDoc']").prop("disabled", false);
            $("#modalDetailAppDoc").data("processing", false);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//상신취소
function onBtnCancelAppDocClick() {
    $("#mode").val("CANCEL");
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

            $("#modalDetailAppDoc").modal("hide");

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        beforeSend: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", true);
        },
        complete: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", false);
            onPageNoClick($("#pageNo").val(), "", true);
            getSubMenuCnt();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//수정
function onBtnModifyAppDocClick() {
    $("#modalDetailAppDoc").modal("hide");

    $("#actType").val("U");

    editAppDoc();
}

//삭제
function onBtnDeleteAppDocClick() {
    $("#modalConfirmDel").modal("hide");

    $("#mode").val("DEL");
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

            if ($("#modalDetailAppDoc").hasClass('show')) {
                $("#modalDetailAppDoc").modal("hide");
            }
            if ($("#modalEditAppDoc").hasClass('show')) {
                $("#modalEditAppDoc").modal('hide')
            }

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        beforeSend: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", true);
        },
        complete: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", false);
            onPageNoClick($("#pageNo").val(), "", true);
            getSubMenuCnt();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//결재이력
function onBtnShowSignInfoClick() {
    showSignInfo($("#docId").val());
}

//보류
function onBtnHoldAppDocClick() {
    $("#mode").val("HOLD");
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

            $("#modalDetailAppDoc").modal("hide");

            $("#resultMsg").empty().html(result["msg"]).fadeIn();
            $("#resultMsg").delay(5000).fadeOut();
        },
        beforeSend: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", true);
        },
        complete: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", false);
            onPageNoClick($("#pageNo").val(), "", true);
            getSubMenuCnt();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//결재 취소
function onBtnCancelAppAgrClick() {
   $("#txtSignKind").text("cancel");

   $("#modalConfirmSign .modal-title").text("결재취소");
   $("#msgSign").text("결재 취소 하시겠습니까?");
   $("#returnReason").closest("div").hide();

   $("#modalConfirmSign").modal("show");
}

//결재
function appAgrSignAppDoc() {
    $("#mode").val("SIGN");
    var proceed = false; 
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

            proceed = result["proceed"];
            if (proceed) {
                $("#modalConfirmSign").modal("hide");

                $("#modalDetailAppDoc").modal("hide");

                $("#resultMsg").empty().html(result["msg"]).fadeIn();
                $("#resultMsg").delay(5000).fadeOut();
            }
            else {
                $("#resultSign").empty().html(result["msg"]).fadeIn();
                $("#resultSign").delay(5000).fadeOut();
            }
        },
        beforeSend: function() {
            $("#modalConfirmSign").find("input").prop("readonly", true);
            $("#modalConfirmSign").find("textarea").prop("readonly", true);
            $("#modalConfirmSign").find("button").prop("disabled", true);
        },
        complete: function() {
            $("#modalConfirmSign").find("input").prop("readonly", false);
            $("#modalConfirmSign").find("textarea").prop("readonly", false);
            $("#modalConfirmSign").find("button").prop("disabled", false);

            if (proceed) {
                onPageNoClick($("#pageNo").val(), "", true);
                getSubMenuCnt();
            }
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//일괄결재
function appAgrBatchSignAppDoc() {
    $("#mode").val("BATCH_SIGN");
    var proceed = false; 
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

            proceed = result["proceed"];
            if (proceed) {
                $("#modalConfirmSign").modal("hide");

                $("#resultMsg").empty().html(result["msg"]).fadeIn();
                $("#resultMsg").delay(5000).fadeOut();
            }
            else {
                $("#resultSign").empty().html(result["msg"]).fadeIn();
                $("#resultSign").delay(5000).fadeOut();
            }
        },
        beforeSend: function() {
            $("#modalConfirmSign").find("input").prop("readonly", true);
            $("#modalConfirmSign").find("textarea").prop("readonly", true);
            $("#modalConfirmSign").find("button").prop("disabled", true);
        },
        complete: function() {
            $("#modalConfirmSign").find("input").prop("readonly", false);
            $("#modalConfirmSign").find("textarea").prop("readonly", false);
            $("#modalConfirmSign").find("button").prop("disabled", false);

            if (proceed) {
                onPageNoClick($("#pageNo").val(), "", true);
                getSubMenuCnt();
            }
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//반려
function appAgrReturnAppDoc() {
    $("#mode").val("RETURN");
    var proceed = false; 
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

            proceed = result["proceed"];
            if (proceed) {
                $("#modalConfirmSign").modal("hide");

                $("#modalDetailAppDoc").modal("hide");

                $("#resultMsg").empty().html(result["msg"]).fadeIn();
                $("#resultMsg").delay(5000).fadeOut();
            }
            else {
                $("#resultSign").empty().html(result["msg"]).fadeIn();
                $("#resultSign").delay(5000).fadeOut();
            }
        },
        beforeSend: function() {
            $("#modalConfirmSign").find("input").prop("readonly", true);
            $("#modalConfirmSign").find("textarea").prop("readonly", true);
            $("#modalConfirmSign").find("button").prop("disabled", true);
        },
        complete: function() {
            $("#modalConfirmSign").find("input").prop("readonly", false);
            $("#modalConfirmSign").find("textarea").prop("readonly", false);
            $("#modalConfirmSign").find("button").prop("disabled", false);

            if (proceed) {
                onPageNoClick($("#pageNo").val(), "", true);
                getSubMenuCnt();
            }
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//결재 취소
function appAgrCancelAppDoc() {
    $("#mode").val("CANCEL_APPAGR");
    var proceed = false; 
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

            proceed = result["proceed"];
            if (proceed) {
                $("#modalConfirmSign").modal("hide");

                $("#modalDetailAppDoc").modal("hide");

                $("#resultMsg").empty().html(result["msg"]).fadeIn();
                $("#resultMsg").delay(5000).fadeOut();
            }
            else {
                $("#userPwd").prop("readonly", false);
                $("#returnReason").prop("readonly", false);
                $("#modalConfirmSign").find("button").prop("disabled", false);

                $("#resultSign").empty().html(result["msg"]).fadeIn();
                $("#resultSign").delay(5000).fadeOut();
            }
        },
        beforeSend: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", true);
        },
        complete: function() {
            $("#modalDetailAppDoc").find("button:button").prop("disabled", false);
            if (proceed) {
                onPageNoClick($("#pageNo").val(), "", true);
                getSubMenuCnt();
            }
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//재작성
function onBtnResubmitAppDocClick() {
    $("#modalDetailAppDoc").modal("hide");

    $("#actType").val("R");

    editAppDoc();
}

//수정취소
function onBtnCancelEditAppDocClick() {
    onBtnCloseEditAppDocClick();

    onBtnDetailAppDocClick($("#docId").val(), $("#formId").val());
}

//편집 창 닫기 버튼 클릭
function onBtnCloseEditAppDocClick() {
    if ($("#detectEditAppDoc").val() == "Y") {
        $("#modalConfirmCloseEditAppDoc").modal("show");
    }
    else {
        $("#modalEditAppDoc").modal("hide");
    }
}

//작성취소 버튼 클릭
function onBtnConfirmCloseEditAppDocClick() {
    $("#modalConfirmCloseEditAppDoc").modal("hide");
    $("#modalEditAppDoc").modal("hide");
}

function onBtnCloseModalAlertMsg() {
    if ($("#moveToPage").val() != "") {
        location.href = "/gw/" + $("#topMenuCd").val() + "/" + $("#moveToPage").val() + "/";
    }
}

function onBtnPrintClick() {
    window.open("/gw/ea2/ea_appdoc_list_print_view.php", "eaPrint", "width=1300,height=700");
}

function getAppDocPrint() {
    var appDoc = $("#modalDetailAppDoc").clone();
    appDoc.find("#divBtnList, #divAppDocReturnList, .modal-footer, .notPrint, .btn, .close").remove().end();

    var reply = "";
    if (!$("#divPrintReplyList").hasClass("notPrint")) {
        reply = $("#divPrintReplyList").clone().html();;
    }
    return appDoc.html() + reply;
}
</script>
<form id="mainForm" name="mainForm" method="post" enctype="multipart/form-data" action="/gw/ea2/ea_appdoc_list_2.php">
<div class="btnList">
    <div style="display: none;">
        <button type="button" class="btn btn-primary" id="btnBatchSign" disabled>일괄결재</button>
    </div>
    <div style="display: none;">
        <button type="button" class="btn btn-primary mr-2" id="btnBatchRead" disabled>일괄열람</button>
    </div>
    <div style="display: none;">
        <button type="button" class="btn btn-primary" id="btnShowMoveTray" disabled>결재함 이동</button>
    </div>
</div>
<div id="divSearch">
<div class="row">
    <div class="col-lg-9 search-inline mb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <select class="form-control prependDdlSearch" id="ddlSearchDate" name="ddlSearchDate">
                </select>
            </div>
            <input type="date" class="form-control mr-2" id="searchFrom" name="searchFrom" />
            -
            <input type="date" class="form-control ml-2" id="searchTo" name="searchTo" />
            <div class="input-group-append">
                <button type="button" id="btnSearchDate" name="btnSearchDate" class="btn btn-info">
                    <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    <span class="fas fa-magnifying-glass"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-3 search-inline mb-2">
        <!-- <div>
            <select class="form-control" id="ddlSearchKind" name="ddlSearchKind">
            </select>
        </div> -->
        <div class="input-group">
            <!-- <div class="input-group-prepend">
                <label>검색어</label>
            </div> -->
            <input type="search" class="form-control" id="txtSearchValue" name="txtSearchValue" maxlength="50" autocomplete="false"/>
            <div class="input-group-append">
                <button type="button" id="btnSearchAppDoc" name="btnSearchAppDoc" class="btn btn-info">
                    <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    <span class="fas fa-magnifying-glass"></span>
                </button>
            </div>
        </div>
        <!-- 자동완성 방지 -->
        <input type="text" style="width:0rem; height:0rem; border: 0;" aria-hidden="true">
        <input type="hidden" id="ddlSearchKind" name="ddlSearchKind" value="all" />
    </div>
</div>
<input type="hidden" id="ddlAppKind" name="ddlAppKind" value="10" />
<input type="hidden" id="ddlOperKind" name="ddlOperKind" value="-999" />
</div>
<div id="resultMsg" class="alert alert-primary py-1 mb-2" style="display: none;"></div>

<div class="tableFixHead">
<table class="table" id="tblAppDocList" style="table-layout: fixed;">
    <thead class="thead-light">
        <tr class="row">
            <th class="col-md-1 col-1 col-w-chk"><input type="checkbox" id="chkAll" onclick="onChkAllDocClick(this);" /></th>
            <th class="col-md-3 d-none d-md-block">품의번호</th>
            <th class="col-md d-none d-md-block">제목</th>
            <th class="col-md-2 d-none d-md-block">기안자/기안일</th>
            <th class="col-md-block d-md-none col-9">문서</th>
            <th class="col-md-2 d-none d-md-block col-w-state">상태</th>
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
<div class="modal fade" id="modalMoveTray" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">결재함 이동</h4>
                <button type="button" class="close btn-close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <select class="form-control" id="ddlMoveTray" name="ddlMoveTray" onchange="onDdlMoveTrayChange()">
                    <option value="0">결재함을 선택해주세요</option>
                </select>
                <br />
                <br />
                <br />
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container">
                    <div class="d-flex justify-content-around">
                        <button type="button" id="btnMoveTray" class="btn btn-primary">이동</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade modalMain modalEaDocApp" id="modalDetailAppDoc" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md" id="divDetailAppDocTitle">
                            <h4 class="modal-title text-nowrap"></h4>
                        </div>
                        <div id="divBtnList" class="col-md">
                            <div class="d-flex justify-content-end mr-3">
                                <button type="button" class="btn btn-primary mr-2" id="btnPrint" onclick="window.print();">인쇄</button>
                                <button type="button" class="btn btn-primary mr-2" id="btnPreview" JavaScript="fn_Print_Preview();" style="display: none;">미리보기</button>
                                <button type="button" class="btn btn-primary mr-2" id="btnPdfConvert" JavaScript="fn_MakePdfFile_Detail();" style="display: none;">PDF저장</button>
                                <button type="button" class="btn btn-primary mr-2" id="btnModifyAppDoc" style="display: none;">수정</button>
                                <button type="button" class="btn btn-danger mr-2" id="btnDeleteAppDoc" style="display: none;" data-toggle="modal" data-target="#modalConfirmDel">삭제</button>
                                <button type="button" class="btn btn-primary mr-2" id="btnCancelAppDoc" style="display: none;">상신취소</button>
                                <button type="button" class="btn btn-primary mr-2" id="btnReUse" JavaScript="true;" style="display: none;">기안작성</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="close btn-close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div id="divAppDocReturnList" style="display: none;"></div>
                <!-- <span id="kindImportant"></span> -->
                <div class="row row-direction-reverse">
                    <div class="col-md-5 mb-2">
                        <div class="d-flex">
                            <div class="ml-auto" style="z-index: 999;">
                                <button type="button" class="btn btn-info" id="btnEditAppLine" onclick="onBtnSelectAppLineClick('save', 'all')" style="display: none;">결재라인수정</button>
                            </div>
                        </div>
                        <div class="d-flex" style="margin-top: -1rem;">
                            <div id="divDetailAppLine" class="ml-auto"></div>
                        </div>
                    </div>
                    <div class="col-md-7 mb-2">
                        <div id="appDocTxtContents" class="mainContents"></div>
                    </div>
                </div>
                <div class="row m-0 mb-2">
                    <div id="divDetailContent" class="col d-flex justify-content-md-center p-2" style="border: 0.1rem solid #999;min-height: 28rem;overflow-x: auto;">
                        <div class="detailContent" class="py-2" style="min-width: 47rem;">
                            <div class="mb-4">
                                <h4 id="txtDF10" style="font-weight: bold;"></h4>
                            </div>
                            <div id="divDF11"></div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-2 colHeader">참조문서</div>
                    <div class="col-10">
                        <!-- <table id="txtRelatedDocList" class="table table-sm eaTable">
                            <tbody></tbody>
                        </table> -->
                        <div id="txtRelatedDocList" class="px-2 py-1" style="border: 0.1rem solid #999;">
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-2 colHeader">첨부파일</div>
                    <div class="col-10">
                        <div id="txtAttachedList" class="px-2 py-1" style="border: 0.1rem solid #999;">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-2">
                    <img class="imgLogo" src="" />
                </div>
                <hr class="notPrint" />
                <h6 id="titleDivReplyList" style="visibility: hidden;"><strong>결재 특이사항</strong></h6>
                <div class="row mb-2">
                    <div class="col" id="divReplyList"></div>
                </div>
                <h6 class="notPrint"><strong>결재 특이사항</strong> - 특이 사항이 있으신 경우 남겨주세요.<button type="button" class="btn btn-sm btn-outline-info ml-2" id="btnAddReply" name="btnAddReply" onclick="inputReply('new')">입력</button></h6>
                <div id="divReplyNote_new" class="notPrint"></div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" class="btn btn-primary" id="btnOperateDoc" name="btnOperateDoc">시행관리</button>
                        <button type="button" class="btn btn-success mr-2" id="btnShowSignInfo" name="btnShowSignInfo">결재이력</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnResubmitAppDoc" name="btnResubmitAppDoc" style="display: none;">재작성</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnCancelAppAgr" name="btnCancelAppAgr" style="display: none;">결재취소</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnSignApp" onclick="onBtnSignAppClick()" style="display: none;">결재</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnReturnApp" onclick="onBtnReturnAppClick()" style="display: none;">반려</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnSignAgr" onclick="onBtnSignAgrClick()" style="display: none;">합의</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnDisAgr" onclick="onBtnDisAgrClick()" style="display: none;">거부</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnReturnAgr" onclick="onBtnReturnAgrClick()" style="display: none;">반려</button>
                        <button type="button" class="btn btn-primary mr-2" id="btnHoldAppDoc" name="btnHoldAppDoc" style="display: none;">보류</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="divPrintReplyList" style="visibility: hidden;">
<strong>결재 특이사항</strong>
<div id="printReplyList"></div>
</div>

<!-- 삭제 확인창 -->
<div class="modal fade" id="modalConfirmDel" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <p>삭제하시겠습니까?</p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" id="btnConfirmDelete" class="btn btn-primary">네</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">아니오</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 결재 특이사항 삭제 확인창 -->
<div class="modal fade" id="modalConfirmDelReply" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <p>삭제하시겠습니까?</p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around">
                        <button type="button" id="btnConfirmDelReply" class="btn btn-primary">네</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">아니오</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade modalMain modalEaDocApp" id="modalEditAppDoc" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md">
                            <h4 class="modal-title text-nowrap"></h4>
                        </div>
                        <div class="col-md d-flex justify-content-end mr-3">
                            <button type="button" class="btn btn-danger ml-auto" id="btnDeleteTempAppDoc" style="display: none;" data-toggle="modal" data-target="#modalConfirmDel">삭제</button>
                        </div>
                        <button type="button" class="close btn-close" name="btnCloseEditAppDoc">&times;</button>
                    </div>
                </div>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row" style="display: none;">
                    <div class="col">
                        <div id="divEBMenu">
                            <span id="ebMenuNmPly"></span>
                            <button type="button" class="btn btn-info btn-sm ml-2" id="btnEBChange" name="btnEBChange" onclick="onBtnEBChangeClick()">선택</button>
                            <input type="hidden" id="ply" name="ply" />
                            <input type="hidden" id="plyNm" name="plyNm" />
                            <input type="hidden" id="ebMenuId" name="ebMenuId" />
                            <input type="hidden" id="ebMenuNm" name="ebMenuNm" />
                        </div>
                    </div>
                    <!-- 
                    <div class="col-md-4">
                        중요도: <select name="ddlImprotantKind" id="ddlImprotantKind"></select>
                    </div>
                     -->
                </div>
                <div class="row row-direction-reverse">
                    <div class="col-md-5 mb-2">
                        <div class="d-flex">
                            <div class="ml-auto" style="z-index: 999;">
                                <button type="button" class="btn btn-info" id="btnSelectAppLine" name="btnSelectAppLine" onclick="onBtnSelectAppLineClick('apply', '')">결재라인지정</button>
                            </div>
                        </div>
                        <div class="d-flex flex-column" style="margin-top: -1rem;">
                            <div class="ml-auto">
                                <div id="divAppLine"></div>
                            </div>
                            <div id="msgAppLine" class="ml-auto text-primary" style="font-size: 80%;">
                            </div>
                            <div class="form-group ml-auto">
                                <input type="text" id="setAppLine" name="setAppLine" style="display:none;" required />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 mb-2">
                        <div id="appDocContents" class="mainContents"></div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="textareaContent" class="colHeader">내용</label>
                    <!-- <textarea id="textareaContent"></textarea> -->
                    <iframe src="/gw/daumeditor/editor.html" frameborder="0" style="width:100%; height:21.5rem"></iframe>
                    <input type="hidden" id="txtContent" name="txtContent" />
                    <input type="hidden" id="htmlContent" name="htmlContent" />
                </div>
                <div class="form-group mb-2">
                    <label for="divRelatedDocList" class="colHeader mb-0">참조문서</label><button type="button" class="btn btn-outline-info btn-sm py-0 ml-2" id="btnSelectRelatedDoc" name="btnSelectRelatedDoc" onclick="onBtnSelectRelatedDocClick()">선택</button>
                    <table class="table table-sm table-bordered mt-2" id="tblRelatedDocList">
                        <tbody></tbody>
                    </table>
                    <input type="text" id="setRelatedDoc" name="setRelatedDoc" style="display:none;" />
                    <div class="invalid-feedback"></div>
                </div>
                <div>
                    <label for="divAttachList" class="colHeader mb-0">첨부파일</label><button type="button" class="btn btn-outline-info btn-sm py-0 ml-2" onclick="javascript:addAttachedFile('new');"><i class="fas fa-plus"></i></button>
                    <div id="divAttachedList" class="mt-2">
                    </div>
                    <div id="divNewAttachedList">
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <img class="imgLogo" src="" />
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-center">
                        <div>
                            <div id="resultMsgEdit" class="alert alert-primary py-1 mb-2" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around">
                        <button type="button" class="btn btn-primary" id="btnSaveTempAppDoc" name="btnSaveTempAppDoc">임시저장</button>
                        <button type="button" class="btn btn-primary" id="btnSaveAppDoc" name="btnSaveAppDoc">상신</button>
                        <button type="button" class="btn btn-primary" id="btnCancelEditAppDoc" name="btnCancelEditAppDoc">수정취소</button>
                        <button type="button" class="btn btn-secondary" name="btnCloseEditAppDoc">닫기</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 작성 취소 확인창 -->
<div class="modal fade" id="modalConfirmCloseEditAppDoc" data-backdrop="static" tabindex="-1">
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
                        <button type="button" id="btnConfirmCloseEditAppDoc" class="btn btn-primary" onclick="onBtnConfirmCloseEditAppDocClick()">작성취소</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once '../cm/cm_select_eb_menu_view.php'; 
require_once '../cm/cm_select_appline_view.php'; 
require_once 'ea_appdoc_relay_list_view.php'; 
require_once 'ea_appdoc_sign_info_view.php';
require_once 'ea_receip_oper_view.php';
require_once 'ea_appdoc_edit_contents_view.php';
require_once 'ea_appdoc_detail_view.php';
require_once '../so/so600200_view.php';
?>

<input type="hidden" id="mode" name="mode" /> 
<input type="hidden" id="menuId" name="menuId" />
<input type="hidden" id="loginUserDeptId" name="loginUserDeptId" />
<input type="hidden" id="userMenuId" name="userMenuId" />
<input type="hidden" id="appbox" name="appbox" />
<input type="hidden" id="viewOrderField" name="viewOrderField" />
<input type="hidden" id="viewOrderDirect" name="viewOrderDirect" />
<input type="hidden" id="pageNo" name="pageNo" value="1" />
<input type="hidden" id="formId" name="formId" />
<input type="hidden" id="formNm" name="formNm" />
<input type="hidden" id="formKind" name="formKind" />
<input type="hidden" id="formAppKind" name="formAppKind" />
<input type="hidden" id="ebMoveYn" name="ebMoveYn" />
<input type="hidden" id="ebKind2" name="ebKind2" />
<input type="hidden" id="eaAppLineEdit" name="eaAppLineEdit" />
<input type="hidden" id="eaLastPreApp" name="eaLastPreApp" />
<input type="hidden" id="eaPrintReplyPosition" name="eaPrintReplyPosition" />
<input type="hidden" id="eaReturnReason" name="eaReturnReason" />
<input type="hidden" id="docId" name="docId" />
<input type="hidden" id="nowApp" name="nowApp" />
<input type="hidden" id="seq" name="seq" />
<input type="hidden" id="actType" name ="actType" />
<input type="hidden" id="appLineType" name="appLineType" />
<input type="hidden" id="appUserYn" name="appUserYn" />
<input type="hidden" id="reqRelYn" name="reqRelYn" />
<input type="hidden" id="eaAppDtEdit" name="eaAppDtEdit" />
<input type="hidden" id="appKindDisplay" name="appKindDisplay" />
<input type="hidden" id="recipientIds" name="recipientIds" />
<input type="hidden" id="operatorIds" name="operatorIds" />
<input type="hidden" id="replyId" name="replyId" />
<input type="hidden" id="detectEditAppDoc" name="detectEditAppDoc" value="N" />
<input type="hidden" id="moveToPage" name="moveToPage" value="" />
</form>
