<?php
// require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/lib/include.php";
require_once "../../lib/include.php";
require_once "official_document_ini.php";

$mode = $_POST["mode"];
$docNo = $_POST["docNo"];

//폐기
if ("DEL" == $mode) {
    //문서 상태를 폐기로 설정
    $SQL  = "UPDATE DOCUMENT_INFO ";
    $SQL .= "SET DOC_STATE = :docState, ";
    $SQL .= " MOD_USER = :modUser, ";
    $SQL .= " MOD_DATE = SYSTIMESTAMP ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        ":docState" => "3",
        ":modUser" => $user->uno,
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);

    //공문서 이력 추가
    $SQL  = "INSERT INTO DOCUMENT_INFO_HIS (DOC_HIS_NO, DOC_NO, TYPE, DOC_CD, DOC_STATE, CC, BCC, TITLE, CONTENT, JNO, ";
//     $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
    $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
    $SQL .= " MOD_USER, MOD_DATE) ";
    $SQL .= "SELECT SEQ_DOCUMENT_HIS.NEXTVAL, DOC_NO, TYPE, DOC_CD, DOC_STATE, CC, BCC, TITLE, CONTENT, JNO, ";
//     $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
    $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
    $SQL .= " MOD_USER, MOD_DATE ";
    $SQL .= "FROM DOCUMENT_INFO ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        //문서 고유 번호
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);

    //공문서 붙임 파일 이력 추가
    $SQL  = "INSERT INTO DOCUMENT_ATTACHED_FILE_HIS (DOC_HIS_NO, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER) ";
    $SQL .= "SELECT SEQ_DOCUMENT_HIS.CURRVAL, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER ";
    $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        //문서 고유 번호
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);

    $db->disconnect();

    $result = array(
        'msg' => $msg
    );

    echo json_encode($result);
}
//저장
else if ("SAVE" == $mode) {
    //공문서 종류
    $type = $_POST["type"];
    //JOB 고유번호
    $jno = $_POST["jno"];
    //수신
    $cc = $_POST["cc"];
    //참조
    $bcc = $_POST["bcc"];
    //시행일
    $enforcementDate = $_POST["enforcementDate"];
    $enforcementDate= new DateTime($enforcementDate);
    //제목
    $title = $_POST["title"];
    //내용
    $content = $_POST["content"];
    // $content = str_replace ("&nbsp;", " ", $content);
    //직인
    $seal = $_POST["seal"];
    //직인명
    $sealName = $_POST["sealName"];
    //작성자 고유번호
    $unoInCharge = $_POST["unoInCharge"];
    //작성자 명
    $userName = $_POST["userName"];
    //작성자 직위
    $gradeName = $_POST["gradeName"];
    //전화번호
    $tel = $_POST["tel"];
    //팩스번호
    $fax = $_POST["fax"];
    //이메일
    $email = $_POST["email"];
    //심의
//     $headType = $_POST["headType"];
    //심의 직원 고유번호
    $teamLeader = $_POST["teamLeader"];
    //심의 직원 명
    $teamLeaderName = $_POST["teamLeaderName"];
    //심의 직원 직위
    $teamLeaderGradeName = $_POST["teamLeaderGradeName"];
    //승인 타입
    $director = $_POST["director"];
    //승인 명
    $directorName = $_POST["directorName"];
    //결과 메시지
    $msg = "";

    //새 공문서
    if (empty($docNo)) {
        //공문서 상세 저장
        $params = array();
        $SQL  = "INSERT INTO DOCUMENT_INFO (DOC_NO, TYPE, DOC_STATE, CC, BCC, TITLE, CONTENT, ";
        if("ESTABLISHMENT" == $type) {
            $SQL .= " JNO, ";
        }
//         $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
        $SQL .= " SEAL, SEAL_NAME, UNO_IN_CHARGE, USER_NAME, GRADE_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, TEAM_LEADER_GRADE_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, ";
        $SQL .= " REG_USER, REG_DATE, MOD_USER, MOD_DATE) ";
        $SQL .= "VALUES (SEQ_DOCUMENT_NO.NEXTVAL, :type, :docState, :cc, :bcc, :title, :content, ";
        if("ESTABLISHMENT" == $type) {
            $SQL .= " :jno, ";
        }
//         $SQL .= ":seal, :sealName, :unoInCharge, :userName, :tel, :fax, :email, :headType, :teamLeader, :teamLeaderName, :director, :directorName, TO_DATE(:enforcementDate, 'YYYY-MM-DD'), ";
        $SQL .= ":seal, :sealName, :unoInCharge, :userName, :gradeName, :tel, :fax, :email, :teamLeader, :teamLeaderName, :teamLeaderGradeName, :director, :directorName, TO_DATE(:enforcementDate, 'YYYY-MM-DD'), ";
        $SQL .= ":regUser, SYSTIMESTAMP, :modUser, SYSTIMESTAMP) ";
        $params[] = array(":type", $type, false);
        $params[] = array(":docState", 1, false);
        $params[] = array(":cc", $cc, false);
        $params[] = array(":bcc", $bcc, false);
        $params[] = array(":title", $title, false);
        $params[] = array(":content", $content, true);
        $params[] = array(":seal", $seal, false);
        $params[] = array(":sealName", $sealName, false);
        $params[] = array(":unoInCharge", $unoInCharge, false);
        $params[] = array(":userName", $userName, false);
        $params[] = array(":gradeName", $gradeName, false);
        $params[] = array(":tel", $tel, false);
        $params[] = array(":fax", $fax, false);
        $params[] = array(":email", $email, false);
//         $params[] = array(":headType", $headType, false);
        $params[] = array(":teamLeader", $teamLeader, false);
        $params[] = array(":teamLeaderName", $teamLeaderName, false);
        $params[] = array(":teamLeaderGradeName", $teamLeaderGradeName, false);
        $params[] = array(":director", $director, false);
        $params[] = array(":directorName", $directorName, false);
        $params[] = array(":enforcementDate", $enforcementDate->format("Y-m-d"), false);
        $params[] = array(":regUser", $user->uno, false);
        $params[] = array(":modUser", $user->uno, false);
        if("ESTABLISHMENT" == $type) {
            $params[] = array(":jno", $jno, false);
        }
        $db->query_lob($SQL, $params);

        //문서 고유 번호 취득
        $SQL  = "SELECT SEQ_DOCUMENT_NO.CURRVAL AS DOC_NO ";
        $SQL .= "FROM DUAL ";
        $db->query($SQL);
        $db->next_record();
        $row = $db->Record;
        $docNo= $row["doc_no"];
    }
    //공문서 수정
    else {
        $SQL  = "UPDATE DOCUMENT_INFO ";
        $SQL .= "SET CC = :cc, ";
        $SQL .= " BCC = :bcc, ";
        $SQL .= " ENFORCEMENT_DATE = TO_DATE(:enforcementDate, 'YYYY-MM-DD'), ";
        $SQL .= " TITLE = :title, ";
        $SQL .= " CONTENT = :content, ";
        $SQL .= " SEAL = :seal, ";
        $SQL .= " SEAL_NAME = :sealName, ";
        $SQL .= " UNO_IN_CHARGE = :unoInCharge, ";
        $SQL .= " USER_NAME = :userName, ";
        $SQL .= " GRADE_NAME = :gradeName, ";
        $SQL .= " TEL = :tel, ";
        $SQL .= " FAX = :fax, ";
        $SQL .= " EMAIL = :email, ";
//         $SQL .= " HEAD_TYPE = :headType, ";
        $SQL .= " TEAM_LEADER = :teamLeader, ";
        $SQL .= " TEAM_LEADER_NAME = :teamLeaderName, ";
        $SQL .= " TEAM_LEADER_GRADE_NAME = :teamLeaderGradeName, ";
        $SQL .= " DIRECTOR = :director, ";
        $SQL .= " DIRECTOR_NAME = :directorName, ";
        $SQL .= " MOD_USER = :modUser, ";
        $SQL .= " MOD_DATE = SYSTIMESTAMP ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params[] = array(":cc", $cc, false);
        $params[] = array(":bcc", $bcc, false);
        $params[] = array(":title", $title, false);
        $params[] = array(":content", $content, true);
        $params[] = array(":seal", $seal, false);
        $params[] = array(":sealName", $sealName, false);
        $params[] = array(":unoInCharge", $unoInCharge, false);
        $params[] = array(":userName", $userName, false);
        $params[] = array(":gradeName", $gradeName, false);
        $params[] = array(":tel", $tel, false);
        $params[] = array(":fax", $fax, false);
        $params[] = array(":email", $email, false);
//         $params[] = array(":headType", $headType, false);
        $params[] = array(":teamLeader", $teamLeader, false);
        $params[] = array(":teamLeaderName", $teamLeaderName, false);
        $params[] = array(":teamLeaderGradeName", $teamLeaderGradeName, false);
        $params[] = array(":director", $director, false);
        $params[] = array(":directorName", $directorName, false);
        $params[] = array(":enforcementDate", $enforcementDate->format("Y-m-d"), false);
        $params[] = array(":modUser", $user->uno, false);
        $params[] = array(":docNo", $docNo, false);
        $db->query_lob($SQL, $params);
    }

    //파일 경로
    $fileDir = $baseFileDir;
//     if (empty($docCd)) {
        $fileDir .= $docNo . "\\";
//     }
//     else {
//         $fileDir .= $docCd . "\\";
//     }

    $attachedFile = $_POST["attachedFile"];
    //삭제할 붙임 파일
    $delFileList = array();
    $SQL  = "SELECT FILE_NO, REPLACE(FILE_LOCATION,'\', '\\') AS FILE_LOCATION ";
    $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        //문서 고유 번호
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);
    if ($db->nf() > 0) {
        while($db->next_record()) {
            $row = $db->Record;

            //기존 목록에서 삭제된 붙임 파일
            if (!empty($attachedFile)) {
                if (!in_array($row["file_no"], $attachedFile)) {
                    //삭제할 붙임 파일 목록 취득
                    $delFileList[$row["file_no"]] = $row["file_location"];
                }
            }
            else {
                $delFileList[$row["file_no"]] = $row["file_location"];
            }
        }
    }

//     $client = new SoapClient('http://wcfservice.hi-techeng.co.kr/WCF TEST/Service1.svc?singleWsdl');
    $client = new SoapClient('http://file.hi-techeng.co.kr/transferweb/Service1.svc?singleWsdl');
    //추가된 붙임 파일
    if (isset($_FILES["newAttachedFile"]['name'])) {
        //붙임 파일 저장 폴더
        $parameter = array(
            'strCreateDirectory' => $fileDir
        );
        $client->CreateDirectoryWeb($parameter);

        //추가된 붙임 파일 업로드
        $uploadFiles = array();
        for ($i=0; $i<count($_FILES['newAttachedFile']['name']); $i++) {
            if (!empty($_FILES['newAttachedFile']['name'][$i])) {
                $info = pathinfo($_FILES['newAttachedFile']['name'][$i]);
//                 $baseName = iconv("UTF-8", "EUC-KR", $info['basename']);
                $baseName = $info['basename'];
                $uploadFile = file_get_contents($_FILES['newAttachedFile']['tmp_name'][$i]);
                $parameter = array(
//                     'strFileBinary' => base64_encode($uploadFile),
                    'strFileBinary' => $uploadFile,
                    'strSaveFileName' => $fileDir . $baseName
                );
                $resultUpload = $client->UploadFileWeb($parameter);
                if ($resultUpload->UploadFileWebResult->ErrorMessage) {
                    $msg .= $resultUpload->UploadFileWebResult->ErrorMessage;
                }
                //붙임 파일 업로드 성공 시
                else {
                    $newFilePath = $resultUpload->UploadFileWebResult->Result;
                    $newFilePaths = explode("\\", $newFilePath);
                    $uploadFiles[] = array(
                        //붙임 파일 명
//                         "fileName" => iconv("EUC-KR", "UTF-8", end($newFilePaths)),
                        "fileName" => end($newFilePaths),
                        //붙임 파일 경로
//                         "fileLocation" => iconv("EUC-KR", "UTF-8", $newFilePath)
                        "fileLocation" => $newFilePath
                    );
                }
            }
        }
    }

//     //삭제할 붙임 파일 물리적 삭제
//     if (count($delFileList) > 0) {
//         foreach ($delFileList as $delFile) {
//             $parameter = array(
//                 'strFileName' => $delFile
//             );
//             $client->DeleteFileWeb($parameter);
//         }
//     }

    //삭제할 붙임 파일 정보 삭제
    if (count($delFileList) > 0) {
        $SQL  = "DELETE FROM DOCUMENT_ATTACHED_FILE ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $SQL .= " AND FILE_NO IN (" . implode(", ", array_keys($delFileList)) . ") ";
        $params = array(
            //문서 고유 번호
            ":docNo" => $docNo
        );
        $db->query($SQL, $params);
    }

    //추가된 붙임 파일 상세 저장
    if (count($uploadFiles) > 0) {
        foreach($uploadFiles as $fileInfo) {
            $SQL  = "INSERT INTO DOCUMENT_ATTACHED_FILE (FILE_NO, DOC_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER) ";
//             $SQL .= "SELECT NVL(MAX(FILE_NO), 0) + 1, :docNo, :fileName, :fileLocation ";
//             $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
            $SQL .= "VALUES (SEQ_DOCUMENT_FILE_NO.NEXTVAL, :docNo, :fileName, :fileLocation, (SELECT NVL(MAX(VIEW_ORDER), 0) + 1 FROM DOCUMENT_ATTACHED_FILE WHERE DOC_NO = :docNo)) ";

            $params = array(
                //문서 고유 번호
                ":docNo" => $docNo,
                //붙임 파일 명
                ":fileName" => $fileInfo["fileName"],
                //붙임 파일 경로
                ":fileLocation" => $fileInfo["fileLocation"]
            );
            $db->query($SQL, $params);
        }
    }

    $SQL  = "SELECT DOC_CD ";
    $SQL .= "FROM DOCUMENT_INFO ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);
    $db->next_record();
    $row = $db->Record;
    $docCd = $row["doc_cd"];
    //발행된 공문서일 경우 최신 내용으로 갱신
    if (!empty($docCd)) {
        //공문서 대체 문서가 존재할 경우
        if (!empty($_FILES["officialDocFile"]['name'])) {
            $info = pathinfo($_FILES['officialDocFile']['name']);
            $baseName = $info['basename'];
            $uploadFile = file_get_contents($_FILES['officialDocFile']['tmp_name']);
            $parameter = array(
//                 'strFileBinary' => base64_encode($uploadFile),
                'strFileBinary' => $uploadFile,
                'strSaveFileName' => $fileDir . $baseName
            );
            $resultUpload = $client->UploadFileWeb($parameter);
        }
        else {
            require_once 'official_document_list_download_pdf.php';
            $docInfo = getDocInfo("UPLOAD", $docNo);

            //공문서 PDF로 작성
            $resultUpload = drawPdf($docInfo, "UPLOAD");
        }

        //최신 파일 업로드 실패 시
        if ($resultUpload->UploadFileWebResult->ErrorMessage) {
            $msg .= $resultUpload->UploadFileWebResult->ErrorMessage;
        }
        //최신 파일 업로드 성공 시
        else {
            $newFilePath = $resultUpload->UploadFileWebResult->Result;
            $newFilePaths = explode("\\", $newFilePath);
            $SQL  = "UPDATE DOCUMENT_INFO ";
            $SQL .= "SET FILE_NAME = :fileName ";
            $SQL .= "WHERE DOC_NO = :docNo ";
            $params = array(
                ":fileName" => end($newFilePaths),
                ":docNo" => $docNo
            );
            $db->query($SQL, $params);
        }
    }

    //공문서 이력 추가
    $SQL  = "INSERT INTO DOCUMENT_INFO_HIS (DOC_HIS_NO, DOC_NO, TYPE, DOC_STATE, JNO, DOC_CD, CC, BCC, TITLE, CONTENT, SEAL, SEAL_NAME, ";
//     $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
    $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
    $SQL .= " MOD_USER, MOD_DATE) ";
    $SQL .= "SELECT SEQ_DOCUMENT_HIS.NEXTVAL, DOC_NO, TYPE, DOC_STATE, JNO, DOC_CD, CC, BCC, TITLE, CONTENT, SEAL, SEAL_NAME, ";
//     $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
    $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
    $SQL .= " MOD_USER, MOD_DATE ";
    $SQL .= "FROM DOCUMENT_INFO ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        //문서 고유 번호
        ":docNo" => $docNo,
    );
    $db->query($SQL, $params);

    //공문서 붙임 파일 이력 추가
    $SQL  = "INSERT INTO DOCUMENT_ATTACHED_FILE_HIS (DOC_HIS_NO, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER) ";
    $SQL .= "SELECT SEQ_DOCUMENT_HIS.CURRVAL, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER ";
    $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        //문서 고유 번호
        ":docNo" => $docNo,
    );
    $db->query($SQL, $params);

    $db->disconnect();

    $result = array(
        'msg' => $msg . "저장되었습니다.",
        'docNo' => $docNo
    );

    echo json_encode($result);
}
//발행
else if ("SEND" == $mode) {
    $SQL  = "SELECT DOC_STATE ";
    $SQL .= "FROM DOCUMENT_INFO ";
    $SQL .= "WHERE DOC_NO = :docNo ";
    $params = array(
        ":docNo" => $docNo
    );
    $db->query($SQL, $params);
    $db->next_record();
    $row = $db->Record;
    $docState = $row["doc_state"];

    //임시저장 일 경우
    if ($docState == 1) {
        //공문서 종류
        $type = $_POST["type"];
        $today = new DateTime;
        $docCdBase = "HT" . $today->format("y");
        //일반공문
        if ($type == "GENERAL") {
            $docCd = $docCdBase;
        }
        //기성청구용 공문
        else if ($type == "ESTABLISHMENT") {
            $docCd = $docCdBase . $today->format("m");
            //JOB의 client 회사 코드
            $docCd_comp = $_POST["docCd_comp"];
            $docCd .= "-" . $docCd_comp;
        }

        //문서번호 발행
        $SQL  = "UPDATE DOCUMENT_INFO ";
        $SQL .= "SET (DOC_CD, SEQ, DOC_STATE, MOD_USER, MOD_DATE) ";
        $SQL .= "    = (SELECT :docCd||'-'||LPAD((NVL(MAX(SEQ), 0) + 1), 3, '0'), NVL(MAX(SEQ), 0) + 1, :docState, :modUser, SYSTIMESTAMP ";
        $SQL .= "       FROM DOCUMENT_INFO ";
        $SQL .= "       WHERE TYPE = :type AND DOC_CD LIKE :docCdBase) ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params = array(
            ":docCd" => $docCd,
            ":year" => $today->format("Y"),
            ":docState" => "2",
            ":modUser" => $user->uno,
            ":type" => $type,
            ":docCdBase" => $docCdBase . "%",
            ":docNo" => $docNo
        );
        $db->query($SQL, $params);

//         //파일 폴더 발행된 문서번호로 변경
//         $SQL  = "SELECT DOC_CD ";
//         $SQL .= "FROM DOCUMENT_INFO ";
//         $SQL .= "WHERE DOC_NO = :docNo ";
//         $params = array(
//             ":docNo" => $docNo
//         );
//         $db->query($SQL, $params);
//         $db->next_record();
//         $row = $db->Record;
//         $docCd = $row["doc_cd"];

//         $parameter = array(
//             'strOrgDir' => $baseFileDir . $docNo,
//             'strNewDir' => $baseFileDir . $docCd
//         );
// //         $client = new SoapClient('http://wcfservice.hi-techeng.co.kr/WCF TEST/Service1.svc?singleWsdl');
//         $client = new SoapClient('http://wcfservice.hi-techeng.co.kr/FILE TRANSFER WCF FOR WEB/Service1.svc?singleWsdl');
//         $result = $client->RenameDirectoryWeb($parameter);

        //공문서 문서 작성
        require_once 'official_document_list_download_pdf.php';
        $docInfo = getDocInfo("UPLOAD", $docNo);

        //공문서 PDF로 작성
        drawPdf($docInfo, "UPLOAD");

        $SQL  = "UPDATE DOCUMENT_INFO ";
        $SQL .= "SET FILE_NAME = :fileName ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params = array(
            ":fileName" => $docInfo["fileName"],
            ":docNo" => $docNo
        );
        $db->query($SQL, $params);

//         //공문서 붙임 파일 경로 수정
//         $SQL  = "UPDATE DOCUMENT_ATTACHED_FILE ";
//         $SQL .= "SET FILE_LOCATION = REPLACE(FILE_LOCATION, :oriFileLoc, :newFileLoc) ";
//         $SQL .= "WHERE DOC_NO = :docNo ";
//         $params = array(
//             //문서 고유 번호
//             ":docNo" => $docNo,
//             ":oriFileLoc" => $baseFileDir . $docNo . "\\",
//             ":newFileLoc" => $baseFileDir . $docCd . "\\"
//         );
//         $db->query($SQL, $params);

//         //공문서 붙임 파일 경로 수정
//         $SQL  = "UPDATE DOCUMENT_ATTACHED_FILE_HIS ";
//         $SQL .= "SET FILE_LOCATION = REPLACE(FILE_LOCATION, :oriFileLoc, :newFileLoc) ";
//         $SQL .= "WHERE DOC_NO = :docNo ";
//         $params = array(
//             //문서 고유 번호
//             ":docNo" => $docNo,
//             ":oriFileLoc" => $baseFileDir . $docNo . "\\",
//             ":newFileLoc" => $baseFileDir . $docCd . "\\"
//         );
//         $db->query($SQL, $params);

        //공문서 이력 추가
        $SQL  = "INSERT INTO DOCUMENT_INFO_HIS (DOC_HIS_NO, DOC_NO, TYPE, DOC_STATE, JNO, DOC_CD, CC, BCC, TITLE, CONTENT, SEAL, SEAL_NAME, ";
//     $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
        $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
        $SQL .= " MOD_USER, MOD_DATE) ";
        $SQL .= "SELECT SEQ_DOCUMENT_HIS.NEXTVAL, DOC_NO, TYPE, DOC_STATE, JNO, DOC_CD, CC, BCC, TITLE, CONTENT, SEAL, SEAL_NAME, ";
//     $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, HEAD_TYPE, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
        $SQL .= " UNO_IN_CHARGE, USER_NAME, TEL, FAX, EMAIL, TEAM_LEADER, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME, ENFORCEMENT_DATE, FILE_NAME, ";
        $SQL .= " MOD_USER, MOD_DATE ";
        $SQL .= "FROM DOCUMENT_INFO ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params = array(
            //문서 고유 번호
            ":docNo" => $docNo,
        );
        $db->query($SQL, $params);

        //공문서 붙임 파일 이력 추가
        $SQL  = "INSERT INTO DOCUMENT_ATTACHED_FILE_HIS (DOC_HIS_NO, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER) ";
        $SQL .= "SELECT SEQ_DOCUMENT_HIS.CURRVAL, FILE_NO, FILE_NAME, FILE_LOCATION, VIEW_ORDER ";
        $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params = array(
            //문서 고유 번호
            ":docNo" => $docNo,
        );
        $db->query($SQL, $params);
        $msg = "발행되었습니다.";
    }
    else {
        $msg = "이미 발행된 문서입니다.";
    }

    $db->disconnect();

    $result = array(
        'msg' => $msg,
        'docNo' => $docNo
    );

    echo json_encode($result);
}
//공문서 편집 이력
else if ("SHOW_HIS" == $mode) {
    $docHisNo = $_POST["docHisNo"];

    //공문서 편집 이력 상세 취득
//     $SQL  = "WITH HEAD_TYPE AS ( ";
//     $SQL .= "SELECT MINOR_CD, CD_NM ";
//     $SQL .= "FROM SYS_CODE_SET ";
//     $SQL .= "WHERE MAJOR_CD = 'DOC_HEAD_TYPE' ";
//     $SQL .= ") ";
//     $SQL .= "SELECT D.DOC_STATE, D.TYPE, D.CC, D.BCC, D.DOC_CD, TO_CHAR(D.ENFORCEMENT_DATE, 'YYYY-MM-DD') AS ENFORCEMENT_DATE, D.TITLE, D.CONTENT, ";
//     $SQL .= " D.USER_NAME, D.TEL, D.FAX, D.EMAIL, D.SEAL_NAME, D.HEAD_TYPE, H.CD_NM AS HEAD_TYPE_NAME, D.TEAM_LEADER_NAME, D.DIRECTOR, D.DIRECTOR_NAME ";
//     $SQL .= "FROM DOCUMENT_INFO_HIS D ";
//     $SQL .= " LEFT OUTER JOIN HEAD_TYPE H ON D.HEAD_TYPE = H.MINOR_CD ";
//     $SQL .= "WHERE D.DOC_NO = :docNo AND D.DOC_HIS_NO = :docHisNo ";
    $SQL  = "SELECT DOC_STATE, TYPE, CC, BCC, DOC_CD, TO_CHAR(ENFORCEMENT_DATE, 'YYYY-MM-DD') AS ENFORCEMENT_DATE, TITLE, CONTENT, ";
    $SQL .= " USER_NAME, TEL, FAX, EMAIL, SEAL_NAME, TEAM_LEADER_NAME, DIRECTOR, DIRECTOR_NAME ";
    $SQL .= "FROM DOCUMENT_INFO_HIS ";
    $SQL .= "WHERE DOC_NO = :docNo AND DOC_HIS_NO = :docHisNo ";
    $params = array(
        ":docNo" => $docNo,
        ":docHisNo" => $docHisNo
    );
    $db->query($SQL, $params);
    $db->next_record();
    $row = $db->Record;

    $docCd = $row["doc_cd"];
    //임시 저장
    if ($row["doc_state"] == "1") {
        $type = $row["type"];
        //문서번호는 임시로 표시
        if ($type == "GENERAL") {
            $docCd = "HTYY - XXX";
        }
        else if ($type == "ESTABLISHMENT") {
            $docCd = "HTYYMM - CLIENT - XXX";
        }
    }

    //공문서 편집 이력 상세
    $docInfo = array(
        "docState" => $row["doc_state"],
        "cc" => $row["cc"],
        "bcc" => $row["bcc"],
        "docCd" => $docCd,
        "enforcementDate" => $row["enforcement_date"],
        "title" => $row["title"],
        "content" => $row["content"],
        "userName" => $row["user_name"],
        "tel" => $row["tel"],
        "fax" => $row["fax"],
        "email" => $row["email"],
        "sealName" => $row["seal_name"],
//         "headType" => $row["head_type"],
        "teamLeaderName" => $row["team_leader_name"],
        "director" => $row["director"],
        "directorName" => $row["director_name"]
    );

    //해당 뭍임 파일 정보 취득
    $fileList = array();
    $SQL  = "SELECT FILE_NAME ";
    $SQL .= "FROM DOCUMENT_ATTACHED_FILE_HIS ";
    $SQL .= "WHERE DOC_HIS_NO = :docHisNo ";
    $SQL .= "ORDER BY VIEW_ORDER ";
    $params = array(
        ":docHisNo" => $docHisNo
    );
    $db->query($SQL, $params);
    if ($db->nf() > 0) {
        while($db->next_record()) {
            $row = $db->Record;

            $temp = array();
            //붙임 파일 명
            $temp["fileName"] = substr($row["file_name"], 0, strrpos($row["file_name"], "."));

            $fileList[] = $temp;
        }
    }

    $result = array(
        'docInfo' => $docInfo,
        'fileList' => $fileList
    );

    echo json_encode($result);
}
//공문서 상세
else if ("SHOW" == $mode) {
    $type = $_POST["type"];
    $docInfo = array(
        "jno" => "",
        "docCd" => "",
        "docCd_ym" => "",
        "docCd_comp" => "",
        "docCd_seq" => "",
        "cc" => "",
        "bcc" => "",
        "title" => "",
        "content" => "",
        "seal" => "",
        "sealName" => "",
        "unoInCharge" => "",
        "gradeName" => "",
        "name" => "",
        "tel" => "",
        "fax" => "",
        "email" => "",
//         "headType" => "",
//         "headTypeName" => "",
        "teamLeader" => "",
        "teamLeaderName" => "",
        "teamLeaderGradeName" => "",
        "director" => "",
        "directorName" => "",
        "enforcementDate" => "",
        "docState" => ""
    );
    $fileList = array();

    //새 공문서
    if (empty($docNo)) {
        //내용
        $SQL  = "SELECT HEADER_CONTENT ";
        $SQL .= "FROM DOCUMENT_BASIC ";
        $db->query($SQL);
        if ($db->nf() > 0) {
            $db->next_record();
            $row = $db->Record;

            $docInfo["content"] = $row["header_content"];
        }
        $today = new DateTime;
        //문서번호는 임시로 표시
        if ($type == "GENERAL") {
            $docInfo["docCd_ym"] = "HTYY";
            $docInfo["docCd_comp"] = "";
            $docInfo["docCd_seq"] = "XXX";
            $docInfo["docCd"] = $docInfo["docCd_ym"] . " - " . $docInfo["docCd_seq"];
        }
        else if ($type == "ESTABLISHMENT") {
            $docInfo["docCd_ym"] = "HTYYMM";
            $docInfo["docCd_comp"] = "CLIENT";
            $docInfo["docCd_seq"] = "XXX";
            $docInfo["docCd"] = $docInfo["docCd_ym"] . " - " . $docInfo["docCd_comp"] . " - " . $docInfo["docCd_seq"];
        }
        //직인
        $docInfo["seal"] = "1";
        
        $SQL  = "SELECT U.user_id, U.user_nm, D.dept_id, dbo.FCMV_GetDeptFullNM(UD.dept_id) AS DEPT_NM2, UD.grade, GRA.cd_nm AS GRADE_NM, UD.tel1, UD.tel2, UD.tel3, UD.tel4, ";
        $SQL .= " UD.fax1, UD.fax2, UD.fax3, U.email_id, CASE WHEN UD.hold_office IN ('1', '2') THEN 'Y' ELSE 'N' END AS HOLD_OFFICE ";
        $SQL .= "FROM dbo.TCMG_USERDEPT AS UD ";
        $SQL .= " INNER JOIN dbo.TCMG_USER AS U ON UD.user_id = U.user_id ";
        $SQL .= " INNER JOIN dbo.TCMG_DEPT AS D ON UD.dept_id = D.dept_id ";
        $SQL .= " LEFT OUTER JOIN dbo.FCMT_CD(1, {$coId}, 'GRADE') AS GRA ON GRA.cd_val = UD.grade ";
        $SQL .= "WHERE D.co_id = {$coId} ";
        $SQL .= " AND U.user_id = {$user->uno} ";
        $userDB->query($SQL);
        $userDB->next_record();
        $row = $userDB->Record;

        $gradeNm = $row["grade_nm"];
        if ($row["grade_nm"] == "사장") {
            $gradeNm = "대표이사";
        }
        //전화번호
        $tel = $row["tel1"];
        if (!empty($row["tel2"])) {
            $tel .= "-" . $row["tel2"];
            if (!empty($row["tel3"])) {
                $tel .= "-" . $row["tel3"];
                if (!empty($row["tel4"])) {
                    $tel .= "-" . $row["tel4"];
                }
            }
        }
        //팩스 번호
        $fax = $row["fax1"];
        if (!empty($row["fax2"])) {
            $fax .= "-" . $row["fax2"];
            if (!empty($row["fax3"])) {
                $fax .= "-" . $row["fax3"];
            }
        }
        //이메일
        $email = "";
        if (!empty($row["email_id"])) {
            $email = $row["email_id"] . $domain;
        }
        //작성자
        $docInfo["unoInCharge"] = $user->uno;
        $docInfo["userName"] = $row["user_nm"];
        $docInfo["gradeName"] = $gradeNm;
        $docInfo["tel"] = $tel;
        $docInfo["fax"] = $fax;
        $docInfo["email"] = $email;

        //시행일 - 작성일
        $docInfo["enforcementDate"] = $today->format("Y-m-d");
        //승인 - 전결
        $docInfo["director"] = "1";
        //편집 가능 여부
        $docInfo["editable"] = "Y";
    }
    //기존 공문서
    else {
//         $SQL  = "WITH HEAD_TYPE AS ( ";
//         $SQL .= "SELECT MINOR_CD, CD_NM ";
//         $SQL .= "FROM SYS_CODE_SET ";
//         $SQL .= "WHERE MAJOR_CD = 'DOC_HEAD_TYPE' ";
//         $SQL .= ") ";
//         $SQL .= "SELECT D.TYPE, D.JNO, D.DOC_CD, D.BCC, D.CC, D.TITLE, D.CONTENT, D.UNO_IN_CHARGE, D.USER_NAME, D.TEL, D.FAX, D.EMAIL, D.SEAL, D.SEAL_NAME, ";
//         $SQL .= " D.HEAD_TYPE, H.CD_NM AS HEAD_TYPE_NAME, D.TEAM_LEADER, D.TEAM_LEADER_NAME, D.DIRECTOR, D.DIRECTOR_NAME, TO_CHAR(D.ENFORCEMENT_DATE, 'YYYY-MM-DD') AS ENFORCEMENT_DATE, D.DOC_STATE, D.REG_USER ";
//         $SQL .= "FROM DOCUMENT_INFO D ";
//         $SQL .= " LEFT OUTER JOIN HEAD_TYPE H ON D.HEAD_TYPE = H.MINOR_CD ";
//         $SQL .= "WHERE D.DOC_NO = :docNo ";
        $SQL  = "SELECT TYPE, JNO, BCC, CC, DOC_CD, TITLE, CONTENT, UNO_IN_CHARGE, USER_NAME, GRADE_NAME, TEL, FAX, EMAIL, SEAL, SEAL_NAME, ";
        $SQL .= " TEAM_LEADER, TEAM_LEADER_NAME, TEAM_LEADER_GRADE_NAME, DIRECTOR, DIRECTOR_NAME, TO_CHAR(ENFORCEMENT_DATE, 'YYYY-MM-DD') AS ENFORCEMENT_DATE, DOC_STATE, REG_USER ";
        $SQL .= "FROM DOCUMENT_INFO ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $params = array(
            ":docNo" => $docNo
        );
        $db->query($SQL, $params);
        $db->next_record();
        $row = $db->Record;

        $type = $row["type"];
        $docCd_ym = "";
        $docCd_comp = "";
        $docCd_seq = "";
        $docCd = $row["doc_cd"];
        //임시 저장
        if ($row["doc_state"] == "1") {
            //일반공문
            if ($type == "GENERAL") {
                $docCd_ym = "HTYY";
                $docCd_comp = "";
                $docCd_seq = "XXX";
                $docCd = $docCd_ym . " - " . $docCd_seq;
            }
            //기성청구용 공문
            else if ($type == "ESTABLISHMENT") {
                $docCd_ym = "HTYYMM";
                $docCd_comp = "CLIENT";
                $docCd_seq = "XXX";
                $docCd = $docCd_ym . " - " . $docCd_comp . " - " . $docCd_seq;
            }
        }
        else {
            $docCds = explode("-", $docCd);
            $docCd_ym = $docCds[0];
            //일반공문
            if ($type == "GENERAL") {
                $docCd_comp = "";
                $docCd_seq = $docCds[1];
                $docCd = $docCd_ym . " - " . $docCd_seq;
            }
            //기성청구용 공문
            else if ($type == "ESTABLISHMENT") {
                $docCd_comp = $docCds[1];
                $docCd_seq = $docCds[2];
                $docCd = $docCd_ym . " - " . $docCd_comp . " - " . $docCd_seq;
            }
        }

        //편집 가능 여부
        $editable = "N";
        //공문 관리자 또는 작성자일 경우
        if ($page->isDM() || $_SESSION["user"]["uno"] == $row["reg_user"]) {
            //편집 가능
            $editable = "Y";
        }

        //공문서 상세
        $docInfo = array(
            "jno" => $row["jno"],
            "cc" => $row["cc"],
            "bcc" => $row["bcc"],
            "docCd" => $docCd,
            "docCd_ym" => $docCd_ym,
            "docCd_comp" => $docCd_comp,
            "docCd_seq" => $docCd_seq,
            "enforcementDate" => $row["enforcement_date"],
            "title" => $row["title"],
            "content" => $row["content"],
            "seal" => $row["seal"],
            "sealName" => $row["seal_name"],
            "unoInCharge" => $row["uno_in_charge"],
            "userName" => $row["user_name"],
            "gradeName" => $row["grade_name"],
            "tel" => $row["tel"],
            "fax" => $row["fax"],
            "email" => $row["email"],
//             "headType" => $row["head_type"],
//             "headTypeName" => $row["head_type_name"],
            "teamLeader" => $row["team_leader"],
            "teamLeaderName" => $row["team_leader_name"],
            "teamLeaderGradeName" => $row["team_leader_grade_name"],
            "director" => $row["director"],
            "directorName" => $row["director_name"],
            "docState" => $row["doc_state"],
            "editable" => $editable
        );

        //뭍임 파일 정보 취득
        $SQL  = "SELECT FILE_NO, FILE_NAME, FILE_LOCATION ";
        $SQL .= "FROM DOCUMENT_ATTACHED_FILE ";
        $SQL .= "WHERE DOC_NO = :docNo ";
        $SQL .= "ORDER BY VIEW_ORDER ";
        $params = array(
            //문서 고유 번호
            ":docNo" => $docNo
        );
        $db->query($SQL, $params);
        if ($db->nf() > 0) {
            while($db->next_record()) {
                $row = $db->Record;

                $temp = array();
                //붙임 파일 고유 번호
                $temp["fileNo"] = $row["file_no"];
                //붙임 파일 명 : 확장자 없이 표시
                $temp["fileName"] = substr($row["file_name"], 0, strrpos($row["file_name"], "."));
                //붙임 파일 경로
                $temp["fileLocation"] = $row["file_location"];

                $fileList[] = $temp;
            }
        }
    }

    $result = array(
        'docInfo' => $docInfo, 
        'fileList' => $fileList
    );

    echo json_encode($result);
}
//초기화면 표시
else if ("INIT" == $mode) {

    $companyInfo = array();
    //회사 정보 취득
    $SQL  = "SELECT co_nm, zip_cd, zip_addr, detail_addr, homepage ";
    $SQL .= "FROM TCMG_CO ";
    $SQL .= "WHERE co_id = {$coId} ";
    $userDB->query($SQL);
    $userDB->next_record();
    $row = $userDB->Record;

    $companyInfo = array(
        //회사명
        "coNm" => $row["co_nm"],
        //회사주소
        "addr" => "우 " . $row["zip_cd"] . " " . $row["zip_addr"] . $row["detail_addr"],
        //홈페이지
        "homepage" => $row["homepage"]
    );

    //대표이사 정보 취득
    $faxList = array();
    $SQL  = "SELECT user_nm, GRADE_NM ";
    $SQL .= "FROM VCMG_GW_USERDEPT ";
    $SQL .= "WHERE grade = 'A1' ";
    $userDB->query($SQL);
    $userDB->next_record();
    $row = $userDB->Record;
//     $companyInfo["ceo"] = $row["grade_nm"] . $row["user_nm"];
    $companyInfo["ceo"] = "대표이사" . " " . $row["user_nm"];

    //직인 목록 취득
    $sealTypeList = array();
    $SQL  = "SELECT MINOR_CD, CD_NM ";
    $SQL .= "FROM SYS_CODE_SET ";
    $SQL .= "WHERE MAJOR_CD = 'DOC_SEAL_TYPE' ";
    $SQL .= "ORDER BY VAL5 ";
    $db->query($SQL);
    while($db->next_record()) {
        $row = $db->Record;

        $sealTypeList[$row["minor_cd"]] = $row["cd_nm"];
    }

//     //상급자 목록 취득
//     $headTypeList = array();
//     $directorTypeList = array();
//     $SQL  = "SELECT MINOR_CD, CD_NM, VAL1 ";
//     $SQL .= "FROM SYS_CODE_SET ";
//     $SQL .= "WHERE MAJOR_CD = 'DOC_HEAD_TYPE' ";
//     $SQL .= "ORDER BY VAL5 ";
//     $db->query($SQL);
//     while($db->next_record()) {
//         $row = $db->Record;

//         $headTypeList[$row["minor_cd"]] = $row["cd_nm"];
//         $directorTypeList[$row["minor_cd"]] = $row["val1"];
//     }

    //연도
    $yearList = array();
    $today = new DateTime();
    for($i = $today->format("Y"); $i >= $startYear; $i--) {
        $yearList["HT" . substr($i, 2)] = $i;
    }

    $result = array(
        'yearList' => $yearList,
        'sealTypeList' => $sealTypeList,
//         'headTypeList' => $headTypeList,
        'directorTypeList' => $directorTypeList,
        'companyInfo' => $companyInfo
    );

    echo json_encode($result);
}
?>
