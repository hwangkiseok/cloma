<?php
/**
 * Cron용 함수들
 */

//타임존 설정
date_default_timezone_set('Asia/Seoul');

define("HOMEPATH", "/data/shop1");
define("CRONHOME", HOMEPATH . "/cron");

//FCM
define("GOOGLE_SERVER_KEY", "AAAANUo0amk:APA91bFF0qu2gymOWaj3wimBnE8ZzFaoTpfu_DygJSg191dM3Zi96lkMYmyCmWUK5WPWrLLuOogZOHWM1OsptCxvAI2yb9wIrpLxwkGkj6tg0VUQKGHY-m4PSKzY89Ll9dFQWHuoT8fb");
$req_url = 'https://fcm.googleapis.com/fcm/send';

//DB 커넥션
require_once 'db_conn.php';

//동시 실행 갯수
$MOD = 2;

//FCM 사용 앱버전코드
$FCM_APP_VERSIONCODE = 5;
$SPLASH_APP_VERSIONCODE = 5;
$STATUSBAR_APP_VERSIONCODE = 5;


/**
 * json_encode 시 JSON_UNESCAPED_SLASHES 가 안 될때 (PHP 5.4.0 미만일때)
 * @param $str
 * @return mixed
 */
function json_encode_no_slashes($str) {
    return str_replace("\/", "/", json_encode($str));
}//end of json_encode_no_slashes()

/**
 * 현재 YmdHis
 * @return bool|string
 */
function current_datetime() {
    return date("YmdHis", time());
}//end of current_datetime()

/**
 * 현재 ms 타임스탬프
 * @return string
 */
function current_mstime() {
    return time() . substr(microtime(), 2, 3);
}//end of current_mstime()

/**
 * 숫자만 남김
 */
function number_only($str) {
    $str = preg_replace("/[^0-9]/", "", $str);
    return $str;
}//end of number_only()

//보내기------------------
//array(4) {
//    [0]=>
//  string(140) "APA91bHrxBEiZO8y0DrIol9sayybVkgTZ66BD3dCsz3XY5TeRI3eJX-JHLNtq9JhWxwUijxQYMADNhgQKsqComuvJkuT0InZUaI5aOPmy5chxRedoKkPP_K0Wz3Ii1U-djye5HgUlZmj"
//    [1]=>
//  string(140) "APA91bEvDMu17RV55S38lhzNfUoDCQGSzqqbWmDwEMHDHeMPjtUZlj9ItsQjiBDKIQ8I7OmaqG5h_Jr5BAKsc5jR8-VMuqaxylL49p_KG8KxnGh2NGcEaKflOSIQoxVsTF0WHrFr7awG"
//    [2]=>
//  string(140) "APA91bEIXW_Hg5U_NmEKGMreWHVMDVCB5Ya7pj6bEYGNJjsATEadCP3WC0-1AYpPhyOBI1_SfkArEKGFgxiAYK1Xiv9emiXyIwfEvFepQ0iQMVswoBSZAF3XBYZ6soarQHf9OdScZqb6"
//    [3]=>
//  string(140) "APA91bGakdfGkz9Wq6roirZFavfFiiFYVkgn8J6RdfOOU5GGiS8-n6hoQggTImpSKJiz9cqeWoORW3BMZ28awgFsZZpjGA9mJu9NrR_5sEfbwkoKS0HSTdLoTt9HFXor_NhXHqS7anON"
//}
//결과--------------------
//array(5) {
//    ["multicast_id"]=>
//  int(-1)
//  ["success"]=>
//  int(4)
//  ["failure"]=>
//  int(0)
//  ["canonical_ids"]=>
//  int(3)
//  ["results"]=>
//  array(4) {
//        [0]=>
//    array(2) {
//            ["registration_id"]=>
//      string(140) "APA91bGakdfGkz9Wq6roirZFavfFiiFYVkgn8J6RdfOOU5GGiS8-n6hoQggTImpSKJiz9cqeWoORW3BMZ28awgFsZZpjGA9mJu9NrR_5sEfbwkoKS0HSTdLoTt9HFXor_NhXHqS7anON"
//            ["message_id"]=>
//      string(15) "fake_message_id"
//    }
//    [1]=>
//    array(2) {
//            ["registration_id"]=>
//      string(140) "APA91bGakdfGkz9Wq6roirZFavfFiiFYVkgn8J6RdfOOU5GGiS8-n6hoQggTImpSKJiz9cqeWoORW3BMZ28awgFsZZpjGA9mJu9NrR_5sEfbwkoKS0HSTdLoTt9HFXor_NhXHqS7anON"
//            ["message_id"]=>
//      string(15) "fake_message_id"
//    }
//    [2]=>
//    array(2) {
//            ["registration_id"]=>
//      string(140) "APA91bGakdfGkz9Wq6roirZFavfFiiFYVkgn8J6RdfOOU5GGiS8-n6hoQggTImpSKJiz9cqeWoORW3BMZ28awgFsZZpjGA9mJu9NrR_5sEfbwkoKS0HSTdLoTt9HFXor_NhXHqS7anON"
//            ["message_id"]=>
//      string(15) "fake_message_id"
//    }
//    [3]=>
//    array(1) {
//            ["message_id"]=>
//      string(15) "fake_message_id"
//    }
//  }
//}
/**
 * 푸시 발송 (멀티)
 * @param $regid_array
 * @param $push_R
 * @return bool|mixed
 */
function send_push_multi($regid_array, $push_R) {
    global $SERVER_KEY, $SERVER_HTTP;

    if( empty($regid_array) ) {
        return false;
    }

    // 헤더 부분
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $SERVER_KEY
    );

    //- registration_ids : array. 1~1000 개의 아이디가 들어갈 수 있다.
    //- collapse_key : message type 을 grouping 하는 녀석으로, 해당 단말이 offline 일 경우 가장 최신 메세지만 전달되는 형태다.
    //- data : key-value pair.
    //- delay_while_idle : message 가 바로 전송되는 것이 아니라, phone 이 active 되었을 때 collapse_key 의 가장 마지막 녀석만 전송되도록. : false => 꺼져(잠겨)있어도 전송, true => 켜지면 전송
    //- time_to_live : 단말이 offline 일 때 GCM storage 에서 얼마나 있어야 하는지를 설정함. collapse_key 와 반드시 함께 설정되야 한다.
    //- dry_run : true=테스트, false=실제발송

    // 푸시 내용, data 부분을 자유롭게 사용해 클라이언트에서 분기할 수 있음.
    $arr = array();
    $arr['data'] = array();
    $arr['data']['num'] = $push_R['ap_num'];            //일련번호
    $arr['data']['title'] = $push_R['ap_subject'];      //제목
    $arr['data']['msg'] = $push_R['ap_message'];        //내용
    $arr['data']['smr'] = $push_R['ap_summary'];        //요약
    $arr['data']['icon'] = $push_R['ap_icon'];          //아이콘이미지
    $arr['data']['img'] = $push_R['ap_image'];          //이미지
    $arr['data']['style'] = $push_R['ap_style'];        //스타일(json형식)(배경색,제목색,메시지색 등)
    $arr['data']['tarUrl'] = $push_R['ap_target_url'];  //이동URL
    $arr['data']['notiType'] = $push_R['ap_noti_type']; //알림타입(1=소리, 2=무음)
    $arr['data']['badge'] = $push_R['ap_badge'];        //뱃지올리기여부(Y/N)
    $arr['collapse_key'] = time() . substr(microtime(), 2, 3);
    $arr['priority'] = "high";                          //메시지의 우선순위(normal | high)
    $arr['content_available'] = true;                   //비활성 클라이언트 앱이 활성 상태로 전환됩니다. Android에서는 기본적으로 데이터 메시지가 앱을 활성 상태로 전환합니다.
    //$arr['time_to_live'] = 2419200;                   //메시지 수명 (단말기가 오프라인이면 서버에 저장하는데, 저장기간을 설정함, 최대값인 4주(2419200)가 기본값)
    //$arr['delay_while_idle'] = false;                 //true => GCM 서버에 저장되었다가 스마트폰이 켜질때(awake) 수신 / false => 화면이 꺼진 후 스마트폰이 idle 상태가 된 상태에서도 GCM 은 바로 수신(2016년 11월 15일자로 지원 중단됨)
    $arr['registration_ids'] = $regid_array;
    $arr['dry_run'] = false;                            //true=테스트용, false=서비스용

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $SERVER_HTTP);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
    $response = curl_exec($ch);
    curl_close($ch);

    // 푸시 전송 결과 반환.
    $result = json_decode($response, true);

    return $result;
}//end of send_push_multi()

function send_push_multi_1($regid_array, $push_R) {
    global $SERVER_KEY, $SERVER_HTTP;

    if( empty($regid_array) ) {
        return false;
    }

    // 헤더 부분
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $SERVER_KEY
    );

    //- registration_ids : array. 1~1000 개의 아이디가 들어갈 수 있다.
    //- collapse_key : message type 을 grouping 하는 녀석으로, 해당 단말이 offline 일 경우 가장 최신 메세지만 전달되는 형태다.
    //- data : key-value pair.
    //- delay_while_idle : message 가 바로 전송되는 것이 아니라, phone 이 active 되었을 때 collapse_key 의 가장 마지막 녀석만 전송되도록. : false => 꺼져(잠겨)있어도 전송, true => 켜지면 전송
    //- time_to_live : 단말이 offline 일 때 GCM storage 에서 얼마나 있어야 하는지를 설정함. collapse_key 와 반드시 함께 설정되야 한다.
    //- dry_run : true=테스트, false=실제발송

    // 푸시 내용, data 부분을 자유롭게 사용해 클라이언트에서 분기할 수 있음.
    if($push_R['ap_badge'] =="Y"){
        $edit_chk = "1";
    }else{
        $edit_chk = "";
    }

    $arr = array();
    $arr['notification'] = array();
    $arr['notification']['num'] = $push_R['ap_num'];            //일련번호
    $arr['notification']['title'] = $push_R['ap_subject'];      //제목
    $arr['notification']['body'] = $push_R['ap_message'];        //내용
    $arr['notification']['smr'] = $push_R['ap_summary'];        //요약
    $arr['notification']['icon'] = $push_R['ap_icon'];          //아이콘이미지
    $arr['notification']['img'] = $push_R['ap_image'];          //이미지
    $arr['notification']['style'] = $push_R['ap_style'];        //스타일(json형식)(배경색,제목색,메시지색 등)
    $arr['notification']['tarUrl'] = $push_R['ap_target_url'];  //이동URL
    $arr['notification']['notiType'] = $push_R['ap_noti_type']; //알림타입(1=소리, 2=무음)
    $arr['notification']['badge'] = $edit_chk;        //뱃지올리기여부(Y/N)
    $arr['collapse_key'] = time() . substr(microtime(), 2, 3);
    $arr['priority'] = "high";                          //메시지의 우선순위(normal | high)
    $arr['content_available'] = true;                   //비활성 클라이언트 앱이 활성 상태로 전환됩니다. Android에서는 기본적으로 데이터 메시지가 앱을 활성 상태로 전환합니다.
    //$arr['time_to_live'] = 2419200;                   //메시지 수명 (단말기가 오프라인이면 서버에 저장하는데, 저장기간을 설정함, 최대값인 4주(2419200)가 기본값)
    //$arr['delay_while_idle'] = false;                 //true => GCM 서버에 저장되었다가 스마트폰이 켜질때(awake) 수신 / false => 화면이 꺼진 후 스마트폰이 idle 상태가 된 상태에서도 GCM 은 바로 수신(2016년 11월 15일자로 지원 중단됨)
    $arr['registration_ids'] = $regid_array;
    $arr['dry_run'] = false;                            //true=테스트용, false=서비스용

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $SERVER_HTTP);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
    $response = curl_exec($ch);
    curl_close($ch);




    // 푸시 전송 결과 반환.
    $result = json_decode($response, true);

    return $result;
}//end of send_push_multi()

/**
 * 발송 결과 처리
 * @param $regid_array
 * @param $result_array
 * @param $push_R
 * @return bool
 */
function push_result_proc($regid_array, $result_array, $push_R) {
    global $DB, $SEQ, $MOD, $MOD_VALUE, $FCM_APP_VERSIONCODE;

    //if( empty($regid_array) || empty($result_array) ) {
    //    return false;
    //}

    $succ_cnt = 0;
    $succ_cnt_fcm = 0;
    $fail_cnt = 0;

    foreach( $regid_array as $key => $regid ) {
        $result = ( isset($result_array['results'][$key]) ) ? $result_array['results'][$key] : "";

        if( empty($result) ) {
            continue;
        }

        $cur_regid = $regid;
        $push_check_datetime = ( !empty($push_R['ap_reserve_datetime']) ) ? $push_R['ap_reserve_datetime'] : $push_R['ap_regdatetime'];

        //발송성공일때
        if( isset($result['message_id']) && !empty($result['message_id']) ) {
            //등록 ID가 변경되었으면
            if( isset($result['registration_id']) && !empty($result['registration_id']) ) {
                $new_regid = $result['registration_id'];

                $regid_row = mysqli_fetch_array(mysqli_query($DB, "select * from app_device_tb where dv_regid='" . $new_regid . "'"));
                //변경된 등록ID가 있으면 => 기존 삭제
                if( !empty($regid_row) ) {
                    mysqli_query($DB, "delete from app_device_tb where dv_regid='" . $regid . "'");
                }
                //변경된 등록ID가 없으면 => 기존 변경
                else {
                    mysqli_query($DB, "update app_device_tb set dv_regid='" . $new_regid . "' where dv_regid='" . $regid . "'");
                }

                $cur_regid = $new_regid;
            }//end of if()

            $datetime = date("YmdHis", time());

            //발송 업데이트
            $query = "update app_device_tb set ";
            $query .= "dv_last_push_num = '" . $push_R['ap_num'] . "', ";
            $query .= "dv_last_push_check_datetime = '" . $push_check_datetime . "', ";
            $query .= "dv_last_push_status = 'success', ";
            $query .= "dv_last_push_result = '" . json_encode_no_slashes($result) . "', ";
            $query .= "dv_last_push_datetime = '" . $datetime . "' ";
            $query .= "where dv_regid = '" . $cur_regid . "'";
            mysqli_query($DB, $query);

            $succ_cnt++;

            $dv_row = mysqli_fetch_array(mysqli_query($DB, "select * from app_device_tb where dv_regid='" . $cur_regid . "'"));

            //FCM 적용된 앱버전일때
            if( !empty($dv_row) && $dv_row['dv_app_version_code'] >= $FCM_APP_VERSIONCODE ) {
                $succ_cnt_fcm++;
            }
        }
        //발송실패일때
        else {
            //APP이 삭제되거나 잘못된 등록ID일때 삭제함.
            if( isset($result['error']) && ($result['error'] == "NotRegistered" || $result['error'] == "InvalidRegistration") ) {
                mysqli_query($DB, "delete from app_device_tb where dv_regid='" . $regid . "'");
            }
            //그외 오류
            else {
                //발송 업데이트
                $query = "update app_device_tb set ";
                $query .= "dv_last_push_num = '" . $push_R['ap_num'] . "', ";
                $query .= "dv_last_push_check_datetime = '" . $push_check_datetime . "', ";
                $query .= "dv_last_push_status = 'fail', ";
                $query .= "dv_last_push_result = '" . json_encode_no_slashes($result) . "', ";
                $query .= "dv_last_push_datetime = '" . date("YmdHis", time()) . "' ";
                $query .= "where dv_regid = '" . $cur_regid . "'";
                mysqli_query($DB, $query);
            }

            $fail_cnt++;
        }
    }//end of foreach()

    //남은 갯수 확인
    $remain_device_cnt = mysqli_num_rows(mysqli_query($DB, "select * from app_device_tb where MOD(dv_num, " . $MOD . ") = " . $MOD_VALUE . " and dv_push_yn = 'Y' and dv_last_push_num != '" . $push_R['ap_num'] . "'"));

    //다중 발송상태 업데이트
    if( $remain_device_cnt > 0 ) {
        mysqli_query($DB, "update app_push_tb set ap_state_" . $SEQ . " = '2' where ap_num = '" . $push_R['ap_num'] . "'");
    }
    else {
        mysqli_query($DB, "update app_push_tb set ap_state_" . $SEQ . " = '3' where ap_num = '" . $push_R['ap_num'] . "'");
    }

    //푸시 정보
    $push_R = mysqli_fetch_array(mysqli_query($DB, "select * from app_push_tb where ap_num = '" . $push_R['ap_num'] ."'"), MYSQLI_ASSOC);

    //최종 발송상태
    $ap_state = "";
    //if( $push_R['ap_state_1'] == 3 && $push_R['ap_state_2'] == 3 && $push_R['ap_state_3'] == 3 ) {
    if( $push_R['ap_state_1'] == 3 && $push_R['ap_state_2'] == 3) {
        $ap_state = 3;
    }

    //푸시 발송완료 변경
    $query = "update app_push_tb set ";
    if( !empty($ap_state) ) {
        $query .= "ap_state = '3', ";
    }
    $query .= "ap_success_cnt = ap_success_cnt + " . $succ_cnt . ", ";
    $query .= "ap_success_cnt_fcm = ap_success_cnt_fcm + " . $succ_cnt_fcm . ", ";
    $query .= "ap_fail_cnt = ap_fail_cnt + " . $fail_cnt . ", ";
    $query .= "ap_proc_datetime = '" . date("YmdHis", time()) . "' ";
    $query .= "where ap_num = '" . $push_R['ap_num'] . "'";
    mysqli_query($DB, $query);
}//end of push_result_proc()


function push_result_proc_1($regid_array, $result_array, $push_R) {
    global $DB, $SEQ, $MOD, $MOD_VALUE, $FCM_APP_VERSIONCODE;

    //if( empty($regid_array) || empty($result_array) ) {
    //    return false;
    //}

    $succ_cnt = 0;
    $succ_cnt_fcm = 0;
    $fail_cnt = 0;

    foreach( $regid_array as $key => $regid ) {
        $result = ( isset($result_array['results'][$key]) ) ? $result_array['results'][$key] : "";

        if( empty($result) ) {
            continue;
        }

        $cur_regid = $regid;
        $push_check_datetime = ( !empty($push_R['ap_reserve_datetime']) ) ? $push_R['ap_reserve_datetime'] : $push_R['ap_regdatetime'];

        //발송성공일때
        if( isset($result['message_id']) && !empty($result['message_id']) ) {
            //등록 ID가 변경되었으면
            if( isset($result['registration_id']) && !empty($result['registration_id']) ) {
                $new_regid = $result['registration_id'];

                $regid_row = mysqli_fetch_array(mysqli_query($DB, "select * from app_device_tb where dv_regid='" . $new_regid . "'"));
                //변경된 등록ID가 있으면 => 기존 삭제
                if( !empty($regid_row) ) {
                    mysqli_query($DB, "delete from app_device_tb where dv_regid='" . $regid . "'");
                }
                //변경된 등록ID가 없으면 => 기존 변경
                else {
                    mysqli_query($DB, "update app_device_tb set dv_regid='" . $new_regid . "' where dv_regid='" . $regid . "'");
                }

                $cur_regid = $new_regid;
            }//end of if()

            $datetime = date("YmdHis", time());

            //발송 업데이트
            $query = "update app_device_tb set ";
            $query .= "dv_last_push_num = '" . $push_R['ap_num'] . "', ";
            $query .= "dv_last_push_check_datetime = '" . $push_check_datetime . "', ";
            $query .= "dv_last_push_status = 'success', ";
            $query .= "dv_last_push_result = '" . json_encode_no_slashes($result) . "', ";
            $query .= "dv_last_push_datetime = '" . $datetime . "' ";
            $query .= "where dv_regid = '" . $cur_regid . "'";
            mysqli_query($DB, $query);

            $succ_cnt++;

            $succ_cnt_fcm++;
            /*
            $dv_row = mysqli_fetch_array(mysqli_query($DB, "select * from app_device_tb where dv_regid='" . $cur_regid . "'"));

            //FCM 적용된 앱버전일때
            if( !empty($dv_row) && $dv_row['dv_app_version_code'] >= $FCM_APP_VERSIONCODE ) {
                $succ_cnt_fcm++;
            }
            */
        }
        //발송실패일때
        else {
            //APP이 삭제되거나 잘못된 등록ID일때 삭제함.
            if( isset($result['error']) && ($result['error'] == "NotRegistered" || $result['error'] == "InvalidRegistration") ) {
                mysqli_query($DB, "delete from app_device_tb where dv_regid='" . $regid . "'");
            }
            //그외 오류
            else {
                //발송 업데이트
                $query = "update app_device_tb set ";
                $query .= "dv_last_push_num = '" . $push_R['ap_num'] . "', ";
                $query .= "dv_last_push_check_datetime = '" . $push_check_datetime . "', ";
                $query .= "dv_last_push_status = 'fail', ";
                $query .= "dv_last_push_result = '" . json_encode_no_slashes($result) . "', ";
                $query .= "dv_last_push_datetime = '" . date("YmdHis", time()) . "' ";
                $query .= "where dv_regid = '" . $cur_regid . "'";
                mysqli_query($DB, $query);
            }

            $fail_cnt++;
        }
    }//end of foreach()

    //남은 갯수 확인
    $remain_device_cnt = mysqli_num_rows(mysqli_query($DB, "select * from app_device_tb where MOD(dv_num, " . $MOD . ") = " . $MOD_VALUE . " and dv_push_yn = 'Y' and dv_last_push_num != '" . $push_R['ap_num'] . "'"));

    //다중 발송상태 업데이트
    if( $remain_device_cnt > 0 ) {
        mysqli_query($DB, "update app_push_tb set ap_state_" . $SEQ . " = '2' where ap_num = '" . $push_R['ap_num'] . "'");
    }
    else {
        mysqli_query($DB, "update app_push_tb set ap_state_" . $SEQ . " = '3' where ap_num = '" . $push_R['ap_num'] . "'");
    }

    //푸시 정보
    $push_R = mysqli_fetch_array(mysqli_query($DB, "select * from app_push_tb where ap_num = '" . $push_R['ap_num'] ."'"), MYSQLI_ASSOC);

    //최종 발송상태
    $ap_state = "";
    //if( $push_R['ap_state_1'] == 3 && $push_R['ap_state_2'] == 3 && $push_R['ap_state_3'] == 3 ) {
    if( $push_R['ap_state_1'] == 3 && $push_R['ap_state_2'] == 3) {
        $ap_state = 3;
    }

    //푸시 발송완료 변경
    $query = "update app_push_tb set ";
    if( !empty($ap_state) ) {
        $query .= "ap_state = '3', ";
    }
    $query .= "ap_success_cnt = ap_success_cnt + " . $succ_cnt . ", ";
    $query .= "ap_success_cnt_fcm = ap_success_cnt_fcm + " . $succ_cnt_fcm . ", ";
    $query .= "ap_fail_cnt = ap_fail_cnt + " . $fail_cnt . ", ";
    $query .= "ap_proc_datetime = '" . date("YmdHis", time()) . "' ";
    $query .= "where ap_num = '" . $push_R['ap_num'] . "'";
    mysqli_query($DB, $query);
}//end of push_result_proc()

