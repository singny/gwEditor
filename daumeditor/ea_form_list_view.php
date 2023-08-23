<!-- <script type="text/javascript" src="/gw/vendor/ckeditor/ckeditor/ckeditor.js"></script> -->
<script type="text/javascript" src="/gw/js/ea.js?random=<?php echo uniqid(); ?>"></script>
<script>
$(document).ready(function(){
    //작업모드
    $("#mode").val("INIT");
    $("#menuId").val('<?php echo $_POST["menuId"]; ?>');
    $.ajax({ 
        type: "POST", 
        url: "/gw/ea2/ea_form_list.php", 
        data: $("#mainForm").serialize(),
        dataType: "json",  
        success: function(result) {
            $("#divNote").val(result["note"]);

            $("#loginUserDeptId").val(result["loginUserDeptId"]);

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

            //직급/직책 표시
            $("#appType").val(result["appType"]);
        },
        complete:function(){
            getSubMenuCnt();
            onConditionChange();
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
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
//         extraPlugins : 'font,colorbutton,justify,tableresize,specialchar',
//         specialChars : CKEDITOR.config.specialChars.concat( [ [ '&#8361;', '원화 기호' ] ] ),
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

    //편집창 닫을 경우
    $("#modalEditAppDoc").on('hide.bs.modal', function () {
        //결재라인 지우기
        $("input[type='hidden'][name='appAgrLine[]'").remove();
        $("input[type='hidden'][name^='essential'").remove();
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

//     //검색 - 검색어
//     $("#btnSearchName").on("click", onConditionChange);

    //저장 버튼
    $("#btnSaveAppDoc").on("click", onBtnSaveAppDocClick);
    //임시 저장 버튼
    $("#btnSaveTempAppDoc").on("click", onBtnSaveTempAppDocClick);
    //작성취소 버튼
    $("button[name='btnCloseEditAppDoc']").on("click", onBtnCloseEditAppDocClick);
});

//검색 조건 변경 시
function onConditionChange() {
    //작업모드
    $("#mode").val("LIST");
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

            showInfoList(result["formList"]);
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

function showInfoList(list) {
    $("#divFormList").empty();

    var html = "";
    $(list).each(function(i, info) {
        if (i % 4 == 0) {
            html += '<div class="row">';
        }
        html += '<div class="col-sm-3 mb-3">';
        html += '<button type="button" class="btn btn-primary btn-block py-4" style="font-size: 1.15rem;" onclick="onBtnFormInputClick(' + info["formId"] + ')">';
        //양식명
        html += info["formNm"];
        html += '</button>';
        html += '</div>';
        if (i % 4 == 3) {
            html += '</div>';
        }
    });

    $("#divFormList").append(html);
}

//등록
function onBtnFormInputClick(formId) {
    if ($('#modalEditAppDoc').hasClass('show')) {
        return false;
    }
    $("#formId").val(formId);
    $("#actType").val("I");

    editAppDoc();
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
</script>
<form id="mainForm" name="mainForm" method="post" enctype="multipart/form-data">
<!-- 
<div class="form-group">
    <div class="row">
        <label class="col-sm-2 control-label" for="searchName">검색</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="text" class="form-control" id="searchName" name="searchName">
                <div class="input-group-append">
                    <button type="button" id="btnSearchName" name="btnSearchName" class="btn btn-info">검색</button>
                </div>
            </div>
        </div>
    </div>
</div>
-->
<div class="row mb-3">
    <div class="col" id="divNote"></div>
</div>
<div id="divFormList" class="container-fluid">
</div>

<!-- The Modal -->
<div class="modal fade modalMain modalEaDocApp" id="modalEditAppDoc" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close btn-close" name="btnCloseEditAppDoc">&times;</button>
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
                            <div class="ml-auto text-primary" id="msgAppLine" style="font-size: 80%;">
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
                <div class="mb-2">
                    <label for="textareaContent" style="font-weight: bold;">내용</label>
                    <!-- <textarea id="textareaContent"></textarea> -->
                    <iframe src="/gw/daumeditor/editor.html" frameborder="0" style="width:100%; height:21.5rem"></iframe>
                    <input type="hidden" id="txtContent" name="txtContent" />
                    <input type="hidden" id="htmlContent" name="htmlContent" />
                </div>
                <div class="form-group mb-2">
                    <label for="tblRelatedDocList" class="colHeader mb-0">참조문서</label><button type="button" class="btn btn-outline-info btn-sm py-0 ml-2" id="btnSelectRelatedDoc" name="btnSelectRelatedDoc" onclick="onBtnSelectRelatedDocClick()">선택</button>
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
                <div class="d-flex justify-content-center mb-2">
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
<!--                         <div>
                            <button type="button" class="btn btn-danger" id="btnDelAppDoc" name="btnDelAppDoc" data-toggle="modal" data-target="#modalConfirmDel">삭제</button>
                        </div> -->
                        <button type="button" class="btn btn-primary" id="btnSaveTempAppDoc" name="btnSaveTempAppDoc">임시저장</button>
                        <button type="button" class="btn btn-primary" id="btnSaveAppDoc" name="btnSaveAppDoc">상신</button>
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
require_once 'ea_appdoc_detail_view.php';
require_once 'ea_appdoc_sign_info_view.php';
require_once '../so/so600200_view.php';
?>

<input type="hidden" id="mode" name="mode" />
<input type="hidden" id="menuId" name="menuId" />
<input type="hidden" id="loginUserDeptId" name="loginUserDeptId" />
<input type="hidden" id="formId" name="formId" />
<input type="hidden" id="formNm" name="formNm" />
<input type="hidden" id="formKind" name="formKind" />
<input type="hidden" id="formAppKind" name="formAppKind" />
<input type="hidden" id="ebMoveYn" name="ebMoveYn" />
<input type="hidden" id="ebKind2" name="ebKind2" />
<input type="hidden" id="appType" name ="appType" />
<input type="hidden" id="actType" name ="actType" />
<input type="hidden" id="docId" name="docId" />
<input type="hidden" id="appLineType" name="appLineType" />
<input type="hidden" id="appUserYn" name="appUserYn" />
<input type="hidden" id="reqRelYn" name="reqRelYn" />
<input type="hidden" id="eaAppDtEdit" name="eaAppDtEdit" />
<input type="hidden" id="appKindDisplay" name="appKindDisplay" />
<input type="hidden" id="recipientIds" name="recipientIds" />
<input type="hidden" id="operatorIds" name="operatorIds" />
<input type="hidden" id="detectEditAppDoc" name="detectEditAppDoc" value="N" />
<input type="hidden" id="moveToPage" name="moveToPage" value="" />
</form>
