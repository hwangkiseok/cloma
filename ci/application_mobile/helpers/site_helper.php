<?php
/**
 * 사이트 공통 헬퍼
 */

//=========================================================== front
/**
 * 로그인 상태
 * @param bool $row_check
 * @return bool
 */
function member_login_status($row_check=false) {
    $CI =& get_instance();

    if( $_SESSION['session_m_num'] ) {
        if( $row_check === true ) {
            $member_row = $CI->_get_member_info();
            if( !empty($member_row) ) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return true;
        }
    }
    else {
        return false;
    }
}//end of member_login_status()

/**
 * 로그인 체크
 */
function member_login_check($backUrl="") {
    $CI =& get_instance();

    if( !member_login_status() ) {
        if( $CI->input->is_ajax_request() ) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }
        else {

            if( empty($backUrl) ) {
                $backUrl = get_current_url();
            }

            redirect('/member?r_url='.urlencode($backUrl));
            exit;
        }
    }//end of if()
}//end of member_login_check()

/**
 * 회원 로그인 세션 생성
 * @param $member_row
 * @return bool
 */
function set_login_session($member_row) {
    if( empty($member_row) ) {
        return false;
    }

    //세션
    $_SESSION['session_m_num']      = $member_row['m_num'];
    $_SESSION['session_m_key']      = $member_row['m_key'];
    $_SESSION['session_m_division'] = $member_row['m_division'];
    $_SESSION['session_m_sns_site'] = $member_row['m_sns_site'];
    $_SESSION['session_m_sns_id']   = $member_row['m_sns_id'];
    $_SESSION['session_m_authno']   = $member_row['m_authno'];
    $_SESSION['session_m_state']    = $member_row['m_state'];
    $_SESSION['m_app_version_code'] = $member_row['m_app_version_code'];
    $_SESSION['session_m_logindatetime']    = $member_row['m_logindatetime'];
    $_SESSION['session_m_device_model']     = $member_row['m_device_model'];
    $_SESSION['session_m_nickname']         = $member_row['m_nickname'];

}//end of set_login_session()

/**
 * 헤더의 회원정보로 로그인 시킴
 */
function set_login_session_from_header() {
    $CI =& get_instance();

    $headers = apache_request_headers();

    foreach ($headers as $header => $value) {
        if ($header == 'm_num') {
            $m_num = $value;
        }

        if ($header == 'm_key') {
            $m_key = $value;
        }

        if ($header == 'key') { // API KEY
            $key = $value;
        }

    }//endforach;

    //네이티브 세션제어
    if( $m_num && $m_key ) {

        $CI->load->model('member_model');
        $member_row = $CI->member_model->get_login_app($m_num, $m_key);

        if( $member_row->m_num ) {
            set_login_session($member_row);
        }

    }

}//end set_login_session_from_header;

//=========================================================== /front

//==================================================== SEED 암호화 관련
function hex2str( $hex ) {
    return pack('H*', str_replace(",", "", $hex));
}
function str2hex($str) {
    return @array_shift(unpack('H*', $str));
}
function addHexComma($hex) {
    $hex_v = "";
    for($i=0; $i <= strlen($hex); $i+=2) {
        if( $i != 0 ) {
            $hex_v .= ",";
        }
        $hex_v .= substr($hex, $i, 2);
    }

    $hex_v = substr($hex_v, 0, -1);

    return $hex_v;
}
function str2hexComma($str) {
    $str = addHexComma(str2hex($str));
    //var_dump($str);
    return $str;
}

/**
 * SEED 128 암호화
 * @param $str              : 평문
 * @param bool $no_comma    : 콤마제거형태로 출력여부
 * @return mixed|string
 */
function seed_encrypt($str, $no_comma=false) {
    require_once(APPPATH."third_party/KISA_SEED_CBC.php");

    $CI =& get_instance();
    //$CI->load->library('KISA_SEED_CBC');

    $planBytes = explode(",", str2hexComma($str));
    $keyBytes = explode(",", str2hexComma($CI->config->item('seed_key')));
    $IVBytes = explode(",", str2hexComma($CI->config->item('seed_iv')));

    for ( $i = 0; $i < 16; $i++ ) {
        $keyBytes[$i] = @hexdec($keyBytes[$i]);
        $IVBytes[$i] = @hexdec($IVBytes[$i]);
    }
    for ( $i = 0; $i < count($planBytes); $i++ ) {
        $planBytes[$i] = @hexdec($planBytes[$i]);
    }

    if ( count($planBytes) == 0 ) {
        return $str;
    }
    $ret = null;
    $bszChiperText = null;
    $pdwRoundKey = array_pad(array(), 32, 0);

    //방법 1
    $bszChiperText = KISA_SEED_CBC::SEED_CBC_Encrypt($keyBytes, $IVBytes, $planBytes, 0, count($planBytes));

    $r = count($bszChiperText);

    for ( $i = 0; $i < $r; $i++ ) {
        $ret .= sprintf("%02X", $bszChiperText[$i]) . ",";
    }

    $str_enc = substr($ret, 0, strlen($ret) - 1);
    if( $no_comma === true ) {
        return str_replace(",", "", $str_enc);
    }
    else {
        return $str_enc;
    }
}//end of seed_encrypt()

/**
 * SEED 128 복호화
 * @param $hex      : 암호화된 HEX 코드(콤마 있거나 없거나)
 * @return string
 */
function seed_decrypt($hex) {
    require_once(APPPATH."third_party/KISA_SEED_CBC.php");

    $CI =& get_instance();

    if( substr($hex, 2, 1) != "," ) {
        $hex = addHexComma($hex);
    }

    $planBytes = explode(",", $hex);
    $keyBytes = explode(",", str2hexComma($CI->config->item('seed_key')));
    $IVBytes = explode(",", str2hexComma($CI->config->item('seed_iv')));

    for ( $i = 0; $i < 16; $i++ ) {
        $keyBytes[$i] = @hexdec($keyBytes[$i]);
        $IVBytes[$i] = @hexdec($IVBytes[$i]);
    }

    for ( $i = 0; $i < count($planBytes); $i++ ) {
        $planBytes[$i] = @hexdec($planBytes[$i]);
    }

    if ( count($planBytes) == 0 ) {
        return $hex;
    }

    $pdwRoundKey = array_pad(array(), 32, 0);

    $bszPlainText = null;

    // 방법 1
    $planBytresMessage = "";
    $bszPlainText = KISA_SEED_CBC::SEED_CBC_Decrypt($keyBytes, $IVBytes, $planBytes, 0, count($planBytes));
    for ( $i = 0; $i < sizeof($bszPlainText); $i++ ) {
        $planBytresMessage .= sprintf("%02X", $bszPlainText[$i]) . ",";
    }

    return hex2str(substr($planBytresMessage, 0, strlen($planBytresMessage) - 1));
}//end of seed_decrypt()
//==================================================== / SEED 암호화 관련

/**
 * SEED 암호화된 회원 데이터 추출
 * @return mixed|string
 */
function get_seed_member_data() {
    $CI =& get_instance();

    $is_app = "N";
    if( is_app() ) {
        $is_app = "Y";
    }

    $str = current_mstime() . "|".$_SESSION['session_m_key'] . $is_app . "|" . get_cookie("cookie_cart_id"). "|" .$_SESSION['session_m_authno'] ;
    return seed_encrypt($str, true);
}//end of get_seed_member_data()

/**
 * 구매하기 링크
 * @param $order_code
 * @param string $p_num
 * @param string $add_param
 * @return string
 */
function get_order_link($order_code, $p_num="", $add_param="") {
    if( empty($order_code) ) {
        return "/";
    }

    $CI =& get_instance();

    //판매상태 확인
    $salestate= get_product_salestate($order_code);

    if($add_param == 'new') {
        $link = $CI->config->item('order_link_head_new') . $order_code . "&p_num=" . $p_num;
    } else {
        $link = $CI->config->item('order_link_head') . $order_code . "&p_num=" . $p_num;
    }

    if(0){
        $link .= "&referer=" . urlencode($_COOKIE['sess_ref']);
    }else{
        $link .= "&referer=" . urlencode($_SESSION['sess_ref']);
    }

    //캠패인
    $link .= "&arrival_source=" . urlencode($_SESSION['sess_arrival_source']);

    if(1){
        //3차가격
        $link .= "&nv_shop=".$_SESSION['nv_shop'];
    }
    if($_SESSION['session_m_num'] == "792007"){
        $salestate = 'Y';
    }
    if( $salestate == 'Y' ) {
        if( member_login_status() ) {
            $link .= "&mdata=" . get_seed_member_data();
        }
        return $link;
    }
    else {
        return "javascript:void(alert('판매종료된 상품입니다..'));";
    }
}//end of get_order_link()

/**
 * 주문배송조회 링크
 * @return string
 */
function get_order_list_link() {
    $CI =& get_instance();

    $link = $CI->config->item('order_list_url');

    if( member_login_status() ) {
        $link .= "&mdata=" . get_seed_member_data();
        $link .= "&vwonly=1";
        $link .= "&webIconType=2";
    }

    return $link;
}//end ofget_order_list_link()

function get_order_list_link_1() {
    $CI =& get_instance();

    $link = $CI->config->item('order_list_url');

    if( member_login_status() ) {
        $link .= "&mdata=" . get_seed_member_data();
        $link .= "&vwonly=1";
        $link .= "&webIconType=2";
    }

    return $link;
}//end ofget_order_list_link()

/**
 * 상품코드로 판매상태 확인
 * @param $order_code
 * @return mixed
 */
function get_product_salestate($order_code) {
    $CI =& get_instance();

    $where_array = array();
    $where_array['p_order_code'] = $order_code;
    $where_array['p_display_state'] = "Y";
    $where_array['p_sale_state'] = "Y";

    return $CI->db->where($where_array)->get('product_tb')->row('p_sale_state');
}//end of get_product_salestate()


/**
 * 세션 아이디 생성
 * @return string
 */
function create_session_id(){
    $mic_arr = explode(" ", microtime());
    $mic = substr($mic_arr[0],2,7);
    $rand_v = rand(1, 100000);
    /**
     * @date 190212
     * @modify 황기석
     * @desc md5 -> sha256으로 변경
     */
    //$sess_id = md5(SITE_DOMAIN . $mic_arr[1] . $mic . $rand_v);
    $sess_id = hash('sha256', SITE_DOMAIN . $mic_arr[1] . $mic . $rand_v, false);
    return $sess_id;
}//end of create_session_id()

//====================================================== alert
/**
 * 경고메세지를 경고창으로 출력 후 URL로 이동
 * @param string $msg	: 출력할 경고메시지
 * @param string $url	: 이동할 URL
 * @return string
 */
function alert($msg='', $url='') {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'>";
    if( $msg ) {
        echo "alert('".$msg."');";
    }

    if( $url ) {
        echo "location.replace('".$url."');";
    }
    else {

        if(is_app()){
            echo " if(appIsNewWin()){ appWinClose(); }";
        }else{
            echo "history.go(-1);";
        }

    }

    echo "</script>";
    exit;
}//end of alert()

function alert_1($msg='', $url='') {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'>";
    if( $msg ) {
        echo "alert('".$msg."');";
    }

    echo "</script>";
    //exit;

}//end of alert()

function start_l() {
    $CI =& get_instance();

    echo "
            <script src='/js/jquery-2.2.0.min.js' type='text/javascript'></script>
            <script>
            
                 
               
                appLogout();
             
            </script>
            ";
    exit;
}//end of alert()

/**
 * 경고메세지 출력후 앱액티비티를 닫음
 */
function alert_app_winclose($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); appWinClose('Y'); </script>";
    exit;
}//end of alert_close()


/**
 * 경고메세지 출력후 창을 닫음
 * @param string $msg	: 출력할 경고메시지
 * @return string
 */
function alert_close($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); window.close(); </script>";
    exit;
}//end of alert_close()

function alert_reload_close($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."');opener.location.reload();window.close();</script>";
    exit;
}//end of alert_close()

/**
 * 경고메세지만 출력
 * @param string $msg : 출력할 경고메시지
 * @param bool $no_exit
 * @return string
 */
function alert_only($msg, $no_exit=false) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); </script>";

    if( $no_exit === false ) {
        exit;
    }
}//end of alert_only()

/**
 * 뒤로가기
 */
function back() {
    $CI =& get_instance();

    if( $CI->input->is_ajax_request() ) {
        $CI->output->set_status_header("403");
        exit;
    }
    else {
        echo "<script>history.back();</script>";
        exit;
    }
}//end of back()
//====================================================== /alert

/**
 * 결과 코드 반환
 * <- $name : 코드명(예:success|noauth|error)
 * -> 해당 코드 리턴
 */
function get_status_code($name) {
    $CI =& get_instance();
    return $CI->config->item($name, 'status_code');
}//end of get_status_code()

/**
 * 처리 결과를 JSON 형태로 출력
 * @param $status               : 상태코드 (000=성공, 100=로그인페이지로이동, 200=오류)
 * @param string $message       : 메시지
 * @param bool $exit            : echo 후 종료 여부 (true=종료, false)
 * @param string $message_type  : 메시지 출력 타입(alert=alert창으로 출력함)
 * @param array $error_data     : 오류 데이터 (배열) (키:값)
 * @param array $data           : 리턴 데이터 (json형태)
 * @param string $goUrl         : 이동 URL
 */
function result_echo_json($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="") {

    if( empty($status) ) {
        $status = get_status_code("error");
    }
    if( empty($error_data) == true ) $error_data = new stdClass();
    if( empty($data) == true ) $data = new stdClass();

    $result_array = get_result_array($status, $message, $exit, $message_type, $error_data, $data, $goUrl);

    echo json_encode($result_array,JSON_UNESCAPED_UNICODE);

    if( $exit ) {
        exit;
    }
}//end of result_echo_json()

function result_echo_rest_json($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="") {
    if( empty($status) ) {
        $status = get_status_code("error");
    }

    $result_array = get_result_array($status, $message, $exit, $message_type, $error_data, $data, $goUrl);

    return $result_array;

    if( $exit ) {
        exit;
    }
}//end of result_echo_rest_json()

/**
 * 결과리턴 파라미터를 배열로 변환해 줌
 * @param $status
 * @param string $message
 * @param bool $exit
 * @param string $message_type
 * @param array $error_data
 * @param array $data
 * @param string $goUrl
 * @return array
 */
function get_result_array($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="") {
    if( empty($status) ) {
        $status = get_status_code("error");
    }

    $result_array = array();
    $result_array['status'] = $status;
    $result_array['message'] = $message;
    $result_array['message_type'] = $message_type;
    $result_array['error_data'] = $error_data;
    $result_array['data'] = $data;
    $result_array['goUrl'] = $goUrl;
    $result_array['exit'] = $exit;

    return $result_array;
}//end of get_result_array()


function result_echo_json_1($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="",$etc_data=array()) {
    if( empty($status) ) {
        $status = get_status_code("error");
    }



    $result_array = get_result_array_1($status, $message, $exit, $message_type, $error_data, $data, $goUrl, $etc_data);



    echo json_encode($result_array);

    if( $exit ) {
        exit;
    }
}//end of result_echo_json()

function get_result_array_1($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="",$etc_data=array()) {
    if( empty($status) ) {
        $status = get_status_code("error");
    }

    $result_array = array();
    $result_array['status'] = $status;
    $result_array['message'] = $message;
    $result_array['message_type'] = $message_type;
    $result_array['error_data'] = $error_data;
    $result_array['data'] = $data;
    $result_array['etc_data'] = $etc_data;
    $result_array['goUrl'] = $goUrl;
    $result_array['exit'] = $exit;

    return $result_array;
}//end of get_result_array()


/**
 * 페이지 요청 타입에 따른 리턴 (json형태 | script형태)
 * @param $status
 * @param string $message
 * @param bool $exit
 * @param string $message_type
 * @param array $error_data
 * @param array $data
 * @param string $goUrl
 */
function page_request_return($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array(), $goUrl="") {
    $CI =& get_instance();

    $result_array = get_result_array($status, $message, $exit, $message_type, $error_data, $data, $goUrl);

    //json 리턴 요청일때
    if( is_json_request() ) {
        echo json_encode($result_array);
    }
    //html 리턴 요청일때
    else {
        if( $status == get_status_code('error') ) {
            //ajax 요청일때
            if( $CI->input->is_ajax_request() ) {
                header("HTTP/1.1 500 " . rawurlencode($message));
                exit;
            }
            else {
                alert($message, $goUrl);
            }
        }
    }

    if( $exit ) {
        exit;
    }
}//end of page_request_return()

// 날짜시간
// 현재 날짜시간 관련
function current_ym($mark="") {
    return date("Y" . $mark . "m", time());
}//end of current_ym()

function current_date($mark="") {
    return date("Y" . $mark . "m" . $mark . "d", time());
}//end of current_date()

function current_datetime() {
    return date("YmdHis", time());
}

function current_mstime() {
    return time() . substr(microtime(), 2, 3);
}

function get_date_format($date, $mark = ".") {
    if ($date == "") {
        return "";
    }
    else {
        return substr($date, 0, 4) . $mark . substr($date, 4, 2) . $mark . substr($date, 6, 2);
    }
}//end of get_date_format()

function get_date_format_kr($date, $mark = " ", $type="md") {
    if ($date == "") {
        return "";
    }

    if( $type == "md" ) {
        return (int)substr($date, 4, 2) . "월" . $mark . (int)substr($date, 6, 2) . "일";
    }
    else if( $type == "ymd" ) {
        return (int)substr($date, 0, 4) . "년" . $mark . (int)substr($date, 4, 2) . "월" . $mark . (int)substr($date, 6, 2) . "일";
    }
}//end of get_date_format()

function get_datetime_format($time, $mark = ".", $mark2 = ":", $len = 14) {
    if ($time == "") {
        return "";
    }
    else {
        $text = substr($time, 0, 4) . $mark . substr($time, 4, 2) . $mark . substr($time, 6, 2);
        if( $len >= 10 ) {
            $text .= " " . substr($time, 8, 2);
        }
        if( $len >= 12 ) {
            $text .= $mark2 . substr($time, 10, 2);
        }
        if( $len >= 14 ) {
            $text .= $mark2 . substr($time, 12, 2);
        }
        return $text;
    }
}//end of get_datetime_format()

/**
 * 전역설정된 텍스트 출력 (텍스트 컬러가 설정돼 있으면 적용)
 * @param $value
 * @param $item_name
 * @param bool $color
 * @return bool|string
 */
function get_config_item_text($value, $item_name, $color=true) {
    if( !isset($value) || empty($value) ) {
        return false;
    }

    $CI =& get_instance();

    $text = $CI->config->item($value, $item_name);
    if( $color === true ) {
        if( $CI->config->item($item_name . '_text_color') !== false ) {
            $text = '<span style="color:' . $CI->config->item($value, $item_name . '_text_color') . ';">' . $text . '</span>';
        }
    }

    return $text;
}//end of get_config_item_text()


//====================================================== HTML 관련
/**
 * 배열을 SELECT OPTION 태그로 변환
 * <- $blank_text       : 빈값 텍스트
 * <- $option_array     : option 으로 변환될 배열 (키=>값 형태)
 * <- $selected_value   : 선택값
 * <- $exclude_array    : $option_array 에서 제외할 키값들 (배열)
 * @param $blank_text
 * @param $option_array
 * @param $selected_value
 * @param array $exclude_array
 * @return string
 */
function get_select_option($blank_text, $option_array, $selected_value="", $exclude_array=array()) {
    $option_text = "";

    if ($blank_text != "") {
        $option_text .= "<option value=\"\">" . $blank_text . "</option>";
    }

    if ( !empty($option_array) && count($option_array) > 0) {
        foreach($option_array as $value => $text) {
            if( !$text ) {
                continue;
            }
            if( !empty($exclude_array) && in_array($value, $exclude_array) ) {
                continue;
            }

            $option_text .= "<option value=\"" . $value . "\"";
            if (trim($value) == trim($selected_value)) {
                $option_text .= " selected=\"selected\"";
            }
            $option_text .= ">" . $text . "</option>";
        }
    }

    return $option_text;
}//end of get_select_option()

/**
 * 배열을 <input type="radio"><label></lable> 태그로 변환
 * @param $name : input name
 * @param $radio_array : radio 로 변활될 배열 (키=>값 형태)
 * @param $checked_value : 선택값
 * @param array $radio_text_color_array : label 텍스트 색상값 배열 (키=>색상코드 형태)
 * @param array $exclude_array : $radio_array 에서 제외할 키값
 * @param string $begin_tag : $bengin_tag <input ...><label></label> $end_tag
 * @param string $end_tag : $bengin_tag <input ...><label></label> $end_tag
 * @return string
 */
function get_input_radio($name, $radio_array, $checked_value, $radio_text_color_array=array(), $exclude_array=array(), $begin_tag="", $end_tag="") {
    $radio_text = "";

    if (count($radio_array) > 0) {
        foreach($radio_array as $value => $text) {
            if( !$text ) {
                continue;
            }

            if( !empty($exclude_array) ) {
                if( in_array($value, $exclude_array) ) {
                    continue;
                }
            }

            if( !empty($begin_tag) ) {
                $radio_text .= $begin_tag;
            }

            $radio_text .= "<input type='radio' id='" . $name. "_" . $value . "' name='" . $name . "' value='" . $value . "'";
            if (trim($value) == trim($checked_value)) {
                $radio_text .= " checked";
            }
            $radio_text .= "><label for='" . $name. "_" . $value . "'>";

            if( isset($radio_text_color_array[$value]) ) {
                $radio_text .= "<span style='color:" . $radio_text_color_array[$value] . "'>" . $text . "</span>";
            }else{
                $radio_text .= $text;
            }
            $radio_text .= "</label>";

            if( !empty($end_tag) ) {
                $radio_text .= $end_tag;
            } else {
                $radio_text .= "&nbsp;&nbsp;";
            }
        }
    }

    return $radio_text;
}//end of get_input_radio()

/**
 * 페이지 이동 (location.replace())
 * @param $href
 */
function location_replace($href) {
    $CI =& get_instance();
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "
    <script>
        function location_replace(url) {
            if( typeof(url) == 'undefined' || url == null || url == '' ) {
                url = '/';
            }
            
            if( history.replaceState ){
                history.replaceState(null, document.title, url);
                history.go(0);
            }
            else{
                location.replace(url);
            }
            
            return false;
        }//end of location_replace()
        
        location_replace('" . $href . "');
    </script>
    ";

    exit;
}//end of location_replace()

/**
 * 메뉴 아이콘 추출
 */
function get_menu_icon($menu_name) {
    $CI =& get_instance();

    $menu_icon = $CI->config->item('기본', 'usermenu_icon');

    foreach( $CI->config->item('usermenu_icon') as $key => $item ) {
        if( preg_match("/" . $key . "/", $menu_name) ) {
            $menu_icon = $item;
            break;
        }
    }//end of foreach()

    return $menu_icon;
}//end of get_menu_icon()

/**
 * 현재 URL 추출
 * @return string   : (예)/user/user_list
 */
function get_current_url() {
    $CI =& get_instance();
    $uri_string = $CI->uri->uri_string();
    if( substr($uri_string, 0, 1) != "/" ) {
        $uri_string = "/" . $uri_string;
    }
    return $uri_string;
}//end of get_current_url()

/**
 * 외부 URL 결과 요청
 * @param $url	: 주소(URL)
 * @return string|bool
 */
function get_url_content($url) {
    if( empty($url) ) {
        return false;
    }

    $url_arr = parse_url($url);
    $host = $url_arr['host'];
    $req_url = $url_arr['path'];
    if( isset($url_arr['query']) && !empty($url_arr['query']) ) {
        $req_url .= "?" . $url_arr['query'];
    }
    if( isset($url_arr['fragment']) && !empty($url_arr['fragment']) ) {
        $req_url .= "#" . $url_arr['fragment'];
    }


    //log_message('zs',$host.'::::'.$req_url);
    $socket = fsockopen($host, 80);
    if($socket) {
        $header = "GET " . $req_url. " HTTP/1.0\r\n";
        $header .= "Host: " . $host . "\r\n";
        $header .= "Connection: Close\r\n\r\n";
        fwrite($socket, $header);

        $data = '';
        while(!feof($socket)) { $data .= fgets($socket); }
        fclose($socket);

        $data = explode("\r\n\r\n", $data, 2);
        return $data[1];
    }
    else {
        return false;
    }
}//end of get_url_content()

/**
 * 숫자만 남김
 * @param $str
 * @param bool|string $default
 * @return mixed|string
 */
function number_only($str, $default=false) {
    $str = preg_replace("/[^0-9]/", "", $str);

    if( empty($str) && $default === true ) {
        return 0;
    }

    return $str;
}//end of number_only()

/**
 * 디렉터리 생성
 * @param $dir => 디렉터리명(절대경로)
 * @param int $mode => 권한(예:0775(rwx))(8진수여야 함)
 * @return bool
 */
function create_directory($dir, $mode=0775){
    //있으면 바로 리턴
    if( $dh = @opendir($dir) ){
        @closedir($dh);
        return true;
    }

    $dir_arr = explode("/", $dir);
    $ck_dir = "";

    //디렉터리 존재 여부 체크해서 없으면 생성함
    for( $i=0; $i < sizeof($dir_arr); $i++ ){
        if( empty($dir_arr[$i]) ) {
            continue;
        }

        $ck_dir .= "/" . $dir_arr[$i];

        if( $dh = @opendir($ck_dir) ){
            @closedir($dh);
        }
        else{
            @mkdir($ck_dir, $mode, true);
            @chmod($ck_dir, $mode);
        }
    }//end of for()

    return true;
}//end of create_directory()

/**
 * 년도 option 값 출력
 * @param $blank_text
 * @param string $selected_value
 * @param string $start_year
 * @param string $end_year
 * @param string $order_type
 * @return string
 */
function get_select_option_year($blank_text, $selected_value="", $start_year="", $end_year="", $order_type="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    if( empty($start_year) ) {
        if( $order_type == "desc" ) $start_year = date("Y", strtotime("+1 years"));
        else                        $start_year = date("Y", strtotime("-1 years"));
    }
    if( empty($end_year) ) {
        if( $order_type == "desc" ) $end_year = date("Y", strtotime("-1 years"));
        else                        $end_year = date("Y", strtotime("+1 years"));
    }

    //내림차순
    if( $order_type == 'desc' ) {
        for( $i=$start_year; $i >= $end_year; $i-- ) {
            $option_text .= '<option value="' . $i . '"';
            if( $i == trim($selected_value) ) {
                $option_text .= ' selected="selected"';
            }
            $option_text .= '>' . $i . '</option>';
        }//end of for()
    }
    //오름차순
    else {
        for( $i=$start_year; $i <= $end_year; $i++ ) {
            $option_text .= '<option value="' . $i . '"';
            if( $i == trim($selected_value) ) {
                $option_text .= ' selected="selected"';
            }
            $option_text .= '>' . $i . '</option>';
        }//end of for()
    }

    return $option_text;
}//end of get_select_option_year()


/**
 * 월 option 값 출력
 * @param $blank_text
 * @param string $selected_value
 * @return string
 */
function get_select_option_month($blank_text, $selected_value="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    for( $i=1; $i <= 12; $i++ ) {
        $month = sprintf("%02d", $i);

        $option_text .= '<option value="' . $month . '"';
        if( $month == trim($selected_value) ) {
            $option_text .= ' selected="selected"';
        }
        $option_text .= '>' . $month . '</option>';
    }

    return $option_text;
}//end of get_select_option_month()

/**
 * 일 option 값 출력
 */
function get_select_option_day($blank_text, $selected_value="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    for( $i=1; $i <= 31; $i++ ) {
        $day = sprintf("%02d", $i);

        $option_text .= '<option value="' . $day . '"';
        if( $day == trim($selected_value) ) {
            $option_text .= ' selected="selected"';
        }
        $option_text .= '>' . $day . '</option>';
    }

    return $option_text;
}//end of get_select_option_day()

/**
 * $this->db->query()->result() ==> 1차원 배열로 컨버팅
 * $result      => $this->db->query()->result() 객체
 * $key_field   => key값으로 사용할 필드명
 * $value_field => value값으로 사용할 필드명
 */
function result_to_option_array($result, $key_field, $value_field) {

    if( empty($result) || !isset($key_field) || !isset($value_field) ) {
        return array();
    }

    $return_array = array();

    foreach( $result as $item ) {
        $return_array[$item->$key_field] = $item->$value_field;
    }//end of foreach()

    return $return_array;

}//end of result_to_option_array()

/**
 * 두날짜간 일수 계산
 * @param $date1
 * @param $date2
 * @return bool|float
 */
function date_diff_day($date1, $date2) {
    if( empty($date1) || empty($date2) ) {
        return false;
    }

    $date1 = number_only($date1);
    $date2 = number_only($date2);

    if( (strlen($date1) != 8) || (strlen($date2) != 8) ) {
        return false;
    }

    $date1_time = strtotime($date1);
    $date2_time = strtotime($date2);

    $diff_second = $date2_time - $date1_time;

    $diff_day = ceil($diff_second / 86400);

    return $diff_day;
}//end of date_diff_day()

/**
 * 목록결과배열(db->query()->result())에서 원하는 row 추출
 * @param $list         : 목록결과배열(db->query()->result())
 * @param $search_key   : 검색키
 * @param $search_value : 검색값
 * @return array|bool
 */
function find_row_from_result($list, $search_key, $search_value) {
    if( empty($list) || !is_array($list) ) {
        return false;
    }

    $return_array = array();

    foreach( $list as $key => $item ) {
        if( $item->{$search_key} == $search_value ) {
            $return_array = $item;
            break;
        }
    }//end of foreach()

    return $return_array;
}//end of find_row_from_result()

/**
 * Ajax 요청인지 체크
 * @param bool $exit
 */
function ajax_request_check($exit=false) {
    $CI =& get_instance();

    if( !$CI->input->is_ajax_request() ) {
        if( $exit === true ) {
            exit;
        }
        else {
            alert(lang('site_error_bad_request'));
        }
    }
}//end of ajax_request_check()

/**
 * json 요청인지
 * @return bool
 */
function is_json_request() {
    if( strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false ) {
        return true;
    }
    else {
        return false;
    }
}//end of is_json_request()

/**
 * config->item($name) 배열의 키만 문자열화
 */
function get_config_item_keys_string($name, $div=",") {
    $CI =& get_instance();
    return implode($div, array_keys($CI->config->item($name)));
}//end of get_config_item_values_string()

/**
 * 이미지 업로드 & 썸네일 생성
 * @param $data : 업로드 파일 정보(upload 라이브러리 데이터)
 * @param $path_web : 웹이미지 경로
 * @param $thumb_arr : 썸네일 사이즈 배열
 * @param bool $org_delete
 * @return array : 생성한 썸네일 이미지명 배열 리턴
 */
//$data 스펙
//[file_name] => c184bc477a28284e2a8a9c6ad7b65155.jpg
//[file_type] => image/jpeg
//[file_path] => /home/castingbox/www/img_data/photo/2014/06/17/
//[full_path] => /home/castingbox/www/img_data/photo/2014/06/17/c184bc477a28284e2a8a9c6ad7b65155.jpg
//[raw_name] => c184bc477a28284e2a8a9c6ad7b65155
//[orig_name] => 바탕화면이미지6.jpg
//[client_name] => 바탕화면이미지6.jpg
//[file_ext] => .jpg
//[file_size] => 669.47
//[is_image] => 1
//[image_width] => 1024
//[image_height] => 768
//[image_type] => jpeg
//[image_size_str] => width="1024" height="768"
function create_thumb_image($data, $path_web, $thumb_arr, $org_delete=false){
    if( !isset($data) || empty($data) || !isset($thumb_arr) || empty($thumb_arr) ) {
        return false;
    }

    $CI =& get_instance();

    $img_arr = array();

    //썸네일 생성
    foreach( $thumb_arr as $key => $arr ){
        $img_arr[$key] = "";

        $thumb_w = $arr[0];     //가로 사이즈
        $thumb_h = $arr[1];     //세로 사이즈
        $crop_yn = $arr[2];     //자르기 여부(Y|N)

        $thumb_img_path = $data['file_path'] . $data['raw_name'] . "_" . $key . $data['file_ext'];

        //  //생성할 썸네일보다 크기가 작으면 continue
        //  if( $data['image_width'] <= $thumb_w && $data['image_height'] <= $thumb_h ){
        //      continue;
        //  }

        //비율을 구함
        $w_rule = ($thumb_w / $data['image_width']);
        $h_rule = ($thumb_h / $data['image_height']);

        //자르기
        if( $crop_yn == 'Y' ) {
            //큰쪽 비율 (자르기용)
            $rule = ($w_rule >= $h_rule) ? $w_rule : $h_rule;

            $dst_w = ceil($data['image_width'] * $rule);
            $dst_h = ceil($data['image_height'] * $rule);

            $pos_x = (int)(($dst_w - $thumb_w) / 2);
            $pos_y = (int)(($dst_h - $thumb_h) / 2);
        }
        else {
            //작은쪽 비율 (안자르기용)
            $rule = ($w_rule <= $h_rule) ? $w_rule : $h_rule;

            //가로기준
            if( $w_rule <= $h_rule ) {
                $dst_w = $thumb_w;
                $dst_h = $thumb_h = ceil($data['image_height'] * $rule);
            }
            //세로기준
            else {
                $dst_w = $thumb_w = ceil($data['image_width'] * $rule);
                $dst_h = $thumb_h;
            }//end of if()

            $pos_x = 0;
            $pos_y = 0;
        }//end of if()

        if( $data['image_type'] == 'jpeg' ) {
            $org_img = imagecreatefromjpeg($data['full_path']);
        }
        else if( $data['image_type'] == 'gif' ) {
            $org_img = imagecreatefromgif($data['full_path']);
        }
        else if( $data['image_type'] == 'png' ) {
            $org_img = imagecreatefrompng($data['full_path']);
        }
        else {
            continue;
        }

        $new_img = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresampled($new_img, $org_img, -$pos_x, -$pos_y, 0, 0, $dst_w, $dst_h, $data['image_width'], $data['image_height']);

        if( $data['image_type'] == 'jpeg' ) {
            imagejpeg($new_img, $thumb_img_path, 90);
        }
        else if( $data['image_type'] == 'gif' ) {
            imagegif($new_img, $thumb_img_path);
        }
        else if( $data['image_type'] == 'png' ) {
            imagepng($new_img, $thumb_img_path, 9);
        }

        $img_arr[$key] = $path_web . "/" . $data['raw_name'] . "_" . $key . $data['file_ext'];
        @chmod(DOCROOT . $img_arr[$key], 0775);
    }//end of foreach()

    //원본 삭제
    if( !empty($img_arr) && $org_delete === true ) {
        @unlink($data['full_path']);
    }

    return $img_arr;
}//end of create_thumb_image()


/**
 * Y/N 값 설정 (없으면 N를 설정하기 위함)
 * @param $value
 * @return string
 */
function get_yn_value ($value) {
    if( !empty($value) && $value == 'Y' ) {
        return 'Y';
    }
    else {
        return 'N';
    }
}//end of get_yn_value()

/**
 * json_encode 시 JSON_UNESCAPED_SLASHES 가 안 될때 (PHP 5.4.0 미만일때)
 * @param $str
 * @return mixed
 */
function json_encode_no_slashes($str) {
    if( phpversion() >= "5.4.0" ) {
        return json_encode($str, JSON_UNESCAPED_SLASHES);
    }
    else {
        return str_replace("\/", "/", json_encode($str));
    }
}//end of json_encode_no_slashes()

/**
 * json 데이터의 특정키를 <img> 로 출력
 * @param $json : json 데이터
 * @param int $key : 출력할 이미지의 키값
 * @param string $width : 이미지 width
 * @param string $height : 이미지 height
 * @param string $etc_attr
 * @param string $add_url : 이미지경로 앞쪽에 붙이는 경로
 * @return string
 */
function create_img_tag_from_json($json, $key=1, $width="", $height="", $etc_attr="", $add_url="") {
    $json_array = json_decode($json, true);

    if( !is_array($json_array) || empty($json_array) ) {
        return '';
    }

    $html = '';
    $html .= '<a href="#none" onclick="new_win_open(\'' . $add_url . $json_array[0].'\', \'img_pop\', 800, 600);"><img src="' . $add_url . $json_array[$key] . '" ';
    if( !empty($width) ) {
        $html .= ' width="'.$width.'" ';
    }
    if( !empty($height) ) {
        $html .= ' height="'.$height.'" ';
    }
    if( !empty($etc_attr) ) {
        $html .= ' ' . $etc_attr . ' ';
    }
    $html .= ' alt="" /></a>';

    return $html;
}//end of create_img_tag_from_json()

/**
 * 파일 삭제
 * @param $input_type       : 입력형태(1=문자열, 2=1차원 배열, 3=1차원 json)
 * @param $value            : 삭제대상 값(문자열, 배열, json)
 * @param string $add_path  : 삭제대상 값 앞에 추가할 경로(예: DOCUMENT_ROOT 경로 추가시 입력함)
 * @return bool
 */
function file_delete($input_type, $value, $add_path="") {
    if( !isset($input_type) || empty($input_type) || !isset($value) || empty($value) ) {
        return false;
    }

    //문자열일때
    if( $input_type == "1" ) {
        if( !empty($add_path) ) {
            @unlink($add_path . $value);
        }
        else {
            @unlink($value);
        }
    }
    //1차원 배열일때
    else if( $input_type == "2" ) {
        foreach( $value as $item ) {
            if( !empty($item) ) {
                if( !empty($add_path) ) {
                    @unlink($add_path . $item);
                }
                else {
                    @unlink($item);
                }
            }
        }
    }
    //1차원 json일때
    else if( $input_type == "3" ) {
        $value_array = json_decode($value);

        if( !empty($value_array) ) {
            foreach( $value_array as $item ){
                if( !empty($add_path) ) {
                    @unlink($add_path . $item);
                }
                else {
                    @unlink($item);
                }
            }
        }
    }

    return true;
}//end of file_delete()

/**
 * form validation error 를 폼 에러 배열에 적용함
 * @param $set_rules_array
 * @param $form_error_array
 * @return mixed
 */
function set_form_error_from_rules ($set_rules_array, $form_error_array){
    //뷰 출력용 폼 검증 오류메시지 설정
    foreach( array_keys($set_rules_array) as $item ) {
        if( form_error($item) ) {
            if( preg_match("/(\[|\])/", $item) ) {
                $key_array = explode("[", $item);
                $key = $key_array[0];
            }
            else {
                $key = $item;
            }
            $form_error_array[$key] = strip_tags(form_error($item));
        }
    }//end of foreach()

    return $form_error_array;
}//end of set_form_error_from_rules()

/**
 * 새창
 * @param $url
 * @param string $name
 * @param string $w
 * @param string $h
 * @param string $t
 * @param string $l
 * @param string $s
 * @param string $r
 */
function new_win_open($url, $name="", $w="", $h="", $t="", $l="", $s="", $r=""){
    echo "<script>new_win_open('{$url}', '{$name}', '{$w}', '{$h}', '{$t}', '{$l}', '{$s}', '{$r}');</script>";
}//end of new_win_open()

/**
 * 앱 접근인지 확인
 * @return bool
 */
function is_app() {
    $CI =& get_instance();

    //에이전트에서 문자열 검색
    if( preg_match("/(android|kr.co.cloma.app)/", $CI->input->user_agent()) ) {
        return true;
    }else{

        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {
            if ($header == 'is_app') $is_app = $value;
        }

        if($is_app == 'Y'){
            return true;
        }else{
            return false;
        }

    }

}//end of is_app()

/**
 * 앱(IOS)인지 아닌지
 * @return bool
 */
function is_app_1() {
    global $_SESSION;

    $CI =& get_instance();
    /*
     Agent=Mozilla/5.0 (iPhone; CPU iPhone OS 11_1 like Mac OS X) AppleWebKit/604.3.5 (KHTML, like Gecko) Mobile/15B87 MysdisAPP/ios MysdisAPPVersion/1.0.0 MysdisAPPVersionCode/1
    mysterydiscount/2.0.8 (kr.co.mysdis.mysterydiscount; build:69; iOS 12.1.4) Alamofire/4.8.1
     */

    $member_row = array();

    //로그인상태일때
    if( member_login_status() ) {
        $CI->load->model("member_model");

        //회원정보
        $query_data = array();
        $query_data['m_num'] = $_SESSION['session_m_num'];
        $member_row = $CI->member_model->get_member_row($query_data, true);
    }

    //회원정보 > 모델명에서
    if( is_app() && !empty($member_row) ) {
        if( preg_match('/(iphone|ios|ipad|ipad touch)/i', $member_row->m_device_model) ) {
            return true;
        }
    }

    //user_agent 에서
    if( is_app() && preg_match('/(iPhone|ios|IPHONE|iphone)/i', $CI->input->user_agent()) ) {
        return true;
    }

    return false;
}//end of is_app_1()

/**
 * 앱(Android)인지 아닌지
 * @return bool
 */
function is_app_2() {
    global $_SESSION;

    $CI =& get_instance();

    //$member_row = array();
    //if( member_login_status() ) {
    //    $CI->load->model("member_model");
    //
    //    //회원정보
    //    $query_data = array();
    //    $query_data['m_num'] = $_SESSION['session_m_num'];
    //    $member_row = $CI->member_model->get_member_row($query_data, true);
    //}
    //
    ////회원정보 > 모델명에서
    //if( is_app() && !empty($member_row) ) {
    //    if( !preg_match('/(iphone|ios|ipad|ipad touch)/i', $member_row->m_device_model) ) {
    //        return true;
    //    }
    //}
    //
    ////user_agent 에서
    //if( is_app() && !preg_match('/(iphone|ios|ipad|ipad touch)/i', $CI->input->user_agent()) ) {
    //    return true;
    //}
    if( is_app() && !is_app_1() ) {
        return true;
    }

    return false;
}//end of is_app_2()



/**
 * 네이버앱 접근인지
 * @return bool
 */
function is_naver_app() {
    $CI =& get_instance();

    if( preg_match("/NAVER/", $CI->input->user_agent()) && $CI->agent->is_mobile() ) {
        return true;
    }
    else {
        return false;
    }
}//end of is_naver_app()

/**
 * APP OS 타입 추출 (android | ios)
 */
function get_app_os () {
    $CI =& get_instance();

    if( is_app() ) {
        //ios
        if( is_app_1() ) {
            return "ios";
        }
        //android
        else if( is_app_2() ) {
            return "android";
        }
        else {
            $agent_explode1 = explode($CI->config->item('app_main_string') . "/", $CI->input->user_agent());
            $agent_explode2 = explode(" ", $agent_explode1[1]);
            return $agent_explode2[0];
        }
    }

    return '';
}//end of get_app_os()

/**
 * APP 버전 추출 (웹뷰에서만 동작함)
 */
function get_app_version () {
    $CI =& get_instance();

    if( is_app() ) {
        $agent_explode1 = explode($CI->config->item('app_version_string') . "/", $CI->input->user_agent());
        $agent_explode2 = explode(" ", $agent_explode1[1]);
        return $agent_explode2[0];
    }

    return "";
}//end of get_app_version()

/**
 * APP 버전코드 추출 (웹뷰에서만 동작함)
 */
function get_app_version_code ($ua="") {
    $CI =& get_instance();

    if( is_app() && empty($ua) ) {
        $ua = $CI->input->user_agent();
    }
    if( empty($ua) ) {
        return "";
    }

    $agent_explode1 = explode($CI->config->item('app_version_code_string') . "/", $ua);
    $agent_explode2 = explode(" ", $agent_explode1[1]);
    return $agent_explode2[0];

    return "";
}//end of get_app_version_code()

/**
 * 배너 이미지 출력
 * @param $banner_row
 * @return string
 */
function banner_img_html($banner_row) {
    $html = "";

    if( empty($banner_row) ) {
        return '';
    }

    //노출여부 확인
    if( $banner_row->bn_usestate == 'N' ) {
        return '';
    }
    //노출 기간 확인
    if( $banner_row->bn_termlimit_yn == 'Y' ) {
        if( $banner_row->bn_termlimit_datetime1 > current_datetime() || $banner_row->bn_termlimit_datetime2 < current_datetime() ) {
            return '';
        }
    }
    //이미지 확인
    if( empty($banner_row->bn_image) ) {
        return '';
    }

    $bn_image_array = json_decode($banner_row->bn_image, true);

    //썸네일 이미지 확인
    if( empty($bn_image_array[1]) ) {
        return '';
    }

    $img_html = '<img src="' . $bn_image_array[1] . '" alt="" />';


    $html .= '<div class="banner">';
    if( !empty($banner_row->bn_target_url) ) {
        $html .= '<a href="' . $banner_row->bn_target_url . '">' . $img_html . '</a>';
    }
    else {
        $html .= $img_html;
    }
    $html .= '</div>';

    return $html;
}//end of banner_img_html()

/**
 * 전체통계
 * @param $date
 * @param $fd
 * @return bool
 */
function total_stat($fd, $date="") {
    if( empty($fd) ) {
        return false;
    }
    if( empty($date) ) {
        $date = current_date();
    }

    $CI =& get_instance();

    if($fd == 'product_view_app'){
        //log_message('zs','fetch_class :: '.$CI->router->fetch_class() .' /// fetch_method :: '.$CI->router->fetch_method());
    }


    //model
    $CI->load->model("total_stat_model");

    //통계 업데이트
    $CI->total_stat_model->update_total_stat($date, $fd);
}//end of total_stat()

function total_stat_ios($fd, $date="") {
    if( empty($fd) ) {
        return false;
    }
    if( empty($date) ) {
        $date = current_date();
    }

    $CI =& get_instance();

    $CI->load->model("total_stat_model");

    //통계 업데이트
    $CI->total_stat_model->update_total_stat($date, $fd);
}//end of total_stat()

/**
 * 설정된 날짜와 차이 추출 (초단위) : 쿠키 만료시간에 사용함
 * @param $str
 * @return int
 */
function get_strtotime_diff($str) {
    if( empty($str) ) {
        return 0;
    }

    return strtotime($str) - time();
}//end of get_strtotime_diff()

/**
 * 단축 URL (bit.ly)
 * @param $long_url
 * @return bool|mixed|string
 */
function get_short_url($long_url) {

    if( empty($long_url) ) {
        return "";
    }

    $long_url = urlencode($long_url);

    $CI =& get_instance();

    $referer = $CI->config->item('site_http');
    $apiLogin = $CI->config->item('bitly_login');
    $apiToken = $CI->config->item('bitly_token');

    $format = 'json';

    $ch = curl_init();
    $arr = array();
    array_push($arr, "Content-Type: application/json; charset=utf-8");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
    curl_setopt($ch, CURLOPT_URL, "https://api-ssl.bitly.com/v3/shorten?login=" . $apiLogin . '&access_token=' . $apiToken . '&uri=' . $long_url . '&format=' . $format);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    if( curl_errno($ch) ) {
        return "";
    }

    $short_url = json_decode($output, true);
    curl_close($ch);

    if( $short_url['status_code'] == 200 ) {
        return $short_url['data']['url'];
    }
    else {
        return '';
    }

}//end of get_short_url()

/**
 * 단축 URL (네이버)
 * @param $long_url
 * @return string
 */
function get_short_url_naver($long_url) {
    $client_id = "RIWJoF76xqeL2m15I0PB";
    $client_secret = "TnJxAa7qVJ";
    $long_url = urlencode($long_url);
    $postvars = "url={$long_url}";
    $url = "https://openapi.naver.com/v1/util/shorturl";
    $is_post = true;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    $headers = array();
    $headers[] = "X-Naver-Client-Id: " . $client_id;
    $headers[] = "X-Naver-Client-Secret: " . $client_secret;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if( $status_code == 200 ) {
        $result = json_decode($response, true);
        return $result['result']['url'];
    }
    else {
        return "";
    }
}//end of get_short_url_naver()

/**
 * 출석 달성 타입별 이름 추출
 * @param $ew_type
 * @return string
 */
function get_event_winner_type_name($ew_type) {
    if( $ew_type == 99 ) {
        return "한 달 만근";
    }
    else {
        return $ew_type . "차 출석 달성";
    }
}//end of get_event_winner_type_name()

/**
 * AES 256 CBC 암호화 (openssl사용)
 * @param $plain_text
 * @return string
 */
function AES_Encode($plain_text) {
    $CI =& get_instance();

    $key = $CI->config->item("aes256_key");

    return base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));
}//end of AES_Encode()

/**
 * AES 256 CBC 복호화 (openssl사용)
 * @param $base64_text
 * @return string
 */
function AES_Decode($base64_text) {
    $CI =& get_instance();

    $key = $CI->config->item("aes256_key");

    return openssl_decrypt(base64_decode($base64_text), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
}//end of AES_Decode()


/**
 * 랜던 문자열
 * @param int $len
 * @param array $except_array
 * @return string
 */
function get_random_string($len=7, $except_array=array()) {
    $char_arr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");

    //제외할 문자 처리
    if( !empty($except_array) ) {
        foreach ($except_array as $item) {
            $del_key = array_search($item, $char_arr);
            if( $del_key !== false ) {
                unset($char_arr[$del_key]);
            }
        }
    }

    shuffle($char_arr);
    return implode("", array_slice($char_arr, 0, $len));
}//end of get_random_string()

/**
 * 안드로이드 버전 추출 (6.0.1, 5.1.1, ...) (웹뷰에서만 동작함)
 * @param string $pos   : 1|2|3|null (
 * @return mixed|string
 */
function get_android_version($pos="") {
    preg_match_all("/Android (\d+(?:\.\d+)+);/", $_SERVER['HTTP_USER_AGENT'], $result);

    if( isset($result[0][0]) && !empty($result[0][0]) ) {
        $result_arr = explode(" ", $result[0][0]);

        $ver_full = str_replace(";", "", trim($result_arr[1]));      //5.0.1, ...

        if( !empty($pos) ) {
            $ver_full_arr = explode(".", $ver_full);

            if( $pos == 1 ) {
                return $ver_full_arr[0];
            }
            else if( $pos == 2 ) {
                return $ver_full_arr[0] . "." . $ver_full_arr[1];
            }
            else if( $pos == 3 ) {
                return $ver_full_arr[0] . "." . $ver_full_arr[1] . "." . $ver_full_arr[2];
            }
        }

        return ``;
    }

    return '';
}//end of get_android_version()

/**
 * 안드로이드 인지
 * @return bool
 */
function is_android() {
    //$version = get_android_version();
    //
    //if( !empty($version) ) {
    //    return true;
    //}
    //else {
    //    return false;
    //}
    if( stristr($_SERVER['HTTP_USER_AGENT'], 'android') ) {
        return true;
    }
    else {
        return false;
    }
}//end of is_android()

/**
 * IOS인지
 * @return bool
 */
function is_ios() {
    $iPod    = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
    $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
    $iPad    = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
    $iOS    = stripos($_SERVER['HTTP_USER_AGENT'], "iOS");

    if( $iPod !== false || $iPhone !== false || $iPad !== false || $iOS !== false ) {
        return true;
    }

    return false;
}//end of is_ios()

/**
 * 판매 남은일수
 * @param $date : YmdHis, Y-m-d H:i:s 형식
 * @return int
 */
function get_sale_remain_day($date) {
    if( empty($date) ) {
        return 0;
    }

    $remain_day = ceil((strtotime($date) - time()) / 86400);
    if( $remain_day < 0 ) {
        $remain_day = 0;
    }

    return $remain_day;
}//end of get_sale_remain_day()

/**
 * 댓글 날짜 형식
 * @param $datetime
 * @param string $div
 * @return bool
 */
function get_comment_date($datetime, $div="/") {
    if( empty($datetime) ) {
        return false;
    }

    $year = substr(number_only($datetime), 0, 4);
    $month = substr(number_only($datetime), 4, 2);
    $day = substr(number_only($datetime), 6, 2);
    $hour = substr(number_only($datetime), 8, 2);
    $min = substr(number_only($datetime), 10, 2);

    $ymd = substr(number_only($datetime), 0, 8);

    $today_day = date("Ymd", time());
    $yester_day = date("Ymd", strtotime("-1 days"));

    //지난 시간
    $past_hour = (int)((time() - strtotime($datetime)) / 60 / 60);     //시
    $past_min = ceil((time() - strtotime($datetime)) / 60);           //분

    //어제
    if( $yester_day == $ymd ) {
        return "어제";
    }
    //오늘중
    else if( $today_day == $ymd ) {
        if( $past_hour > 0 ) {
            return $past_hour . "시간 전";
        }
        else {
            return $past_min . "분 전";
        }
    }

    else {
        //오전, 오후
        $am_pm = "오전";
        $hour_str = $hour;
        if( $hour >= 12 ) {
            $am_pm = "오후";

            if( $hour > 12 ) {
                $hour_str = $hour - 12;
            }
        }
        else {
            if( $hour == 0 ) {
                $hour_str = 12;
            }
        }

        //년도가 다르면 : Y/m/d 오전 00:00
        if( date("Y", time()) != $year ) {
            return get_date_format($datetime, $div) . " " . $am_pm . " " . number_format($hour_str) . ":" . $min;
        }
        //같은 년도이면 : m월 d일 오전 00:00
        else {
            return  number_format($month) . "월 " . number_format($day) . "일" . " " . $am_pm . " " . number_format($hour_str) . ":" . $min;
        }
    }
}//end of get_comment_date()

/**
 * site_lang 메시지를 html 단에 출력시 사용함
 * @param $name
 * @return mixed
 */
function echo_lang($name) {
    $CI =& get_instance();
    $msg = $CI->lang->line($name, FALSE);
    return str_replace("\n", "\\n", $msg);
}//end of echo_lang()

/**
 * 한글,영문,숫자,데쉬,언더바만 가능하게 한다. (UTF-8 기준)
 * @param $str
 * @return bool
 */
function korean_alpha_dash($str) {
    return ( preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z_-]/u',$str)) ? FALSE : TRUE;
}//end of korean_alpha_dash()

/**
 * 앱 인텐트
 * @param string $param
 * @return mixed
 */
function get_app_intent($param="") {
    $CI =& get_instance();

    if( !empty($param) ) {
        return str_replace("#PARAM#", $param, $CI->config->item('app_intent'));
    }
    else {
        return str_replace("#PARAM#", "", $CI->config->item('app_intent'));
    }
}//end of get_app_intent()

//======================================================================= 09sns

// 상품정보를 추출한다.
// 인자 : 상품코드
// 리턴 : 상품정보
function get_product_info_09($pcode){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns

    $query = "
    select * 
    from
        smart_product as p
        left join smart_individual as ind on (ind.in_id=p.p_inid)
        left join smart_individual_setup as sis on (sis.sis_inid=ind.in_id)
    where
        p.p_code = '$pcode'
    ";
    $oRet           = $_09sns->query($query);
    $rProductInfo   = $oRet->row_array();
    $oRet->free_result();

    return $rProductInfo;

} // end of get_product_info_09

/**
 * 상품 카테고리 정보 추출 (name1, name2, name3 설정)
 * @param $c_uid
 * @return array|bool
 */
function get_product_category_row_09($c_uid) {

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns

    if( empty($c_uid) ) {
        return false;
    }

    $query = "select *, c_name name1 from smart_category where c_uid = '" . $c_uid . "'";
    $oRet = $_09sns->query($query);
    $row  = $oRet->row_array();
    $oRet->free_result();

    $depth = $row['c_depth'];

    //1단계
    if( $depth == "1" ) {
        return $row;
    }
    //2단계
    else if( $depth == "2" ) {
        $query = "
            select
                a.*
                , a.c_name name2
                , b.c_name name1
            from smart_category a
                left join smart_category b on b.c_uid = a.c_parent
            where a.c_uid = '" . $c_uid . "'
        ";

        $oRet = $_09sns->query($query);
        $row  = $oRet->row_array();
        $oRet->free_result();

        return $row;
    }
    //3단계
    else if( $depth == "3" ) {
        $query = "
            select
                a.*
                , a.c_name name3
                , b.c_name name2
                , c.c_name name1
            from smart_category a
                left join smart_category b on b.c_uid = substring_index(a.c_parent, ',', -1)
                left join smart_category c on c.c_uid = substring_index(a.c_parent, ',', 1)
            where a.c_uid = '" . $c_uid . "'
        ";
        $oRet = $_09sns->query($query);
        $row  = $oRet->row_array();
        $oRet->free_result();

        return $row;
    }//endif;

    return $row;
}//end of get_product_category_row_09()





function get_product_option_v2($p_code){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns

    //상품정보
    $pro_info = get_product_info_09($p_code);

    //카테고리정보
    $pro_info['cate1'] = "";
    $pro_info['cate2'] = "";
    $pro_info['cate3'] = "";
    if( !empty($pro_info['p_name_uid']) ) {
        $query = "
            select *
            from smart_product_represent
            where pr_uid = '" . $pro_info['p_name_uid'] . "'
        ";

        $oRet = $_09sns->query($query);
        $pr_row  = $oRet->row_array();
        $oRet->free_result();

        if( !empty($pr_row['pr_category_uid']) ) {
            $cate_row = get_product_category_row_09($pr_row['pr_category_uid']);

            $pro_info['cate1'] = (!empty($cate_row['name1'])) ? $cate_row['name1'] : "";
            $pro_info['cate2'] = (!empty($cate_row['name2'])) ? $cate_row['name2'] : "";
            $pro_info['cate3'] = (!empty($cate_row['name3'])) ? $cate_row['name3'] : "";
        }

    }

    $data['info'] = array(
        'code' => $pro_info['p_code'],
        'name' => $pro_info['p_name'],
        'price' => $pro_info['p_price'],
        'price_app_yn' => $pro_info['p_price_app_yn'],
        'price_app' => $pro_info['p_price_app'],

        'price_second_yn' => $pro_info['p_price_second_yn'],
        'price_second' => $pro_info['p_price_second'],
        'price_third_yn' => $pro_info['p_price_third_yn'],
        'price_third' => $pro_info['p_price_third'],

        'offered_info_use' => $pro_info['p_offered_info_use'],
        'offered_info' => $pro_info['p_offered_info'],
        'stock' => $pro_info['p_stock'],
        'shopping_pay_use' => $pro_info['p_shoppingPay_use'],
        'shopping_pay' => $pro_info['p_shoppingPay'],
        'shopping_pay_free' => $pro_info['p_shoppingPayFree'],
        'option_type' => $pro_info['p_option_type_chk'],
        'cate1' => $pro_info['cate1'],
        'cate2' => $pro_info['cate2'],
        'cate3' => $pro_info['cate3'],
        'sendcompany' => $pro_info['p_sendcompany'],
        'shipping_info' => $pro_info['p_shipping_info'],

        'sum_delivery_yn' => $pro_info['p_sum_delivery_yn'],
        'sum_delivery' => $pro_info['p_sum_delivery'],
        'add_option_type' => $pro_info['p_add_option_type']

    );


    $depth = onlynumber(substr($pro_info['p_option_type_chk'], 0, 1));



    if($_SESSION['session_m_num'] == "792007"){

        $option_table_name = 'smart_product_option';
        if( $pro_info['p_add_option_type'] == '2' || $pro_info['p_add_option_type'] == '3' ){
            $option_table_name = 'smart_product_option_add';

            $sql            = "SELECT * FROM smart_product_option WHERE po_pcode = '{$pro_info['p_code']}' ; ";
            $oRet           = $_09sns->query($sql);
            $real_result    = $oRet->result_array();
            $oRet->free_result();
            $tmp_array = array();

            foreach ($real_result as $row) {
                $row['po_add_parent_rename'] = str_replace(',','||',$row['po_add_parent']);
                $tmp_array[$row['po_add_parent_rename']] = $row;
            }

            $data['option']['real_result'] = $tmp_array;
        }
        // 옵션정보 불러오기
        $query = "
          select *
          from {$option_table_name} 
          where po_pcode = '" . $pro_info['p_code'] . "'
              and po_depth = '1'
              and (po_other = 'N' or po_other = '')
          order by po_uid asc
        ";

        $oRet = $_09sns->query($query);
        $option_result  = $oRet->result_array();
        $oRet->free_result();

        foreach( $option_result as $key => $row ) {
            $data['option'][1][] = array(
                'uid' => $row['po_uid'],
                'name' => $row['po_poptionname'],
                'price' => $row['po_poptionprice'],
                'second_price' => $row['po_second_poptionprice'],
                'third_price' => $row['po_third_poptionprice']?$row['po_third_poptionprice']:0,
                'stock' => $row['po_cnt'],
                'img' => $row['po_img']
            );

            if( $depth >= 2 ) {
                $query = "
                select *
                from {$option_table_name}
                where po_pcode = '" . $pro_info['p_code'] . "'
                    and po_depth = '2'
                    and po_parent = '" . $row['po_uid'] . "'
                    and (po_other = 'N' or po_other = '')
                order by po_uid asc
            ";

                $oRet = $_09sns->query($query);
                $option2_result  = $oRet->result_array();
                $oRet->free_result();

                foreach( $option2_result as $key2 => $row2 ) {
                    $data['option'][2][$row['po_uid']][] = array(
                        'uid' => $row2['po_uid'],
                        'name' => $row2['po_poptionname'],
                        'price' => $row2['po_poptionprice'],
                        'second_price' => $row2['po_second_poptionprice'],
                        'third_price' => $row2['po_third_poptionprice']?$row2['po_third_poptionprice']:0,
                        'stock' => $row2['po_cnt'],
                        'img' => $row2['po_img'],
                    );

                    if( $depth >= 3 ) {
                        $query = "
                        select *
                        from {$option_table_name}
                        where po_pcode = '" . $pro_info['p_code'] . "'
                            and po_depth = '3'
                            and po_parent = '" . $row['po_uid']  . "," . $row2['po_uid'] . "'
                            and (po_other = 'N' or po_other = '')
                        order by po_uid asc
                    ";

                        $oRet = $_09sns->query($query);
                        $option3_result  = $oRet->result_array();
                        $oRet->free_result();

                        foreach( $option3_result as $key3 => $row3 ) {
                            $data['option'][3][$row2['po_uid']][] = array(
                                'uid' => $row3['po_uid'],
                                'name' => $row3['po_poptionname'],
                                'price' => $row3['po_poptionprice'],
                                'second_price' => $row3['po_second_poptionprice'],
                                'third_price' => $row3['po_third_poptionprice']?$row3['po_third_poptionprice']:0,
                                'stock' => $row3['po_cnt'],
                                'img' => $row3['po_img'],
                                'sub_option' => ''
                            );
                        }//end of foreach(3)
                    }//end of if(3)
                }//end of foreach(2)
            }//end of if(2)
        }//end of foreach()

        // 추가옵션정보 불러오기
        $query = "
      select *
      from {$option_table_name} 
      where po_pcode = '" . $pro_info['p_code'] . "'
          and po_depth = '1'
          and po_other = 'Y'
      order by po_uid asc
    ";

        $oRet = $_09sns->query($query);
        $other_result  = $oRet->result_array();
        $oRet->free_result();

        foreach( $other_result as $key => $row ) {
            $data['other'][] = array(
                'uid' => $row['po_uid'],
                'name' => $row['po_poptionname'],
                'price' => $row['po_poptionprice'],
                'second_price' => $row['po_second_poptionprice'],
                'third_price' => $row['po_third_poptionprice']?$row['po_third_poptionprice']:0,
                'stock' => $row['po_cnt'],
                'img' => $row['po_img']
            );
        }//end of foreach()





    }else{


        // 옵션정보 불러오기
        $query = "
          select *
          from smart_product_option 
          where po_pcode = '" . $pro_info['p_code'] . "'
              and po_depth = '1'
              and (po_other = 'N' or po_other = '')
          order by po_uid asc
        ";

        $oRet = $_09sns->query($query);
        $option_result  = $oRet->result_array();
        $oRet->free_result();

        foreach( $option_result as $key => $row ) {
            $data['option'][1][] = array(
                'uid' => $row['po_uid'],
                'name' => $row['po_poptionname'],
                'price' => $row['po_poptionprice'],
                'second_price' => $row['po_second_poptionprice'],
                'third_price' => $row['po_third_poptionprice']?$row['po_third_poptionprice']:0,
                'stock' => $row['po_cnt'],
                'img' => $row['po_img']
            );

            if( $depth >= 2 ) {
                $query = "
                select *
                from smart_product_option
                where po_pcode = '" . $pro_info['p_code'] . "'
                    and po_depth = '2'
                    and po_parent = '" . $row['po_uid'] . "'
                    and (po_other = 'N' or po_other = '')
                order by po_uid asc
            ";

                $oRet = $_09sns->query($query);
                $option2_result  = $oRet->result_array();
                $oRet->free_result();

                foreach( $option2_result as $key2 => $row2 ) {
                    $data['option'][2][$row['po_uid']][] = array(
                        'uid' => $row2['po_uid'],
                        'name' => $row2['po_poptionname'],
                        'price' => $row2['po_poptionprice'],
                        'second_price' => $row2['po_second_poptionprice'],
                        'third_price' => $row2['po_third_poptionprice']?$row2['po_third_poptionprice']:0,
                        'stock' => $row2['po_cnt'],
                        'img' => $row2['po_img'],
                    );

                    if( $depth >= 3 ) {
                        $query = "
                        select *
                        from smart_product_option
                        where po_pcode = '" . $pro_info['p_code'] . "'
                            and po_depth = '3'
                            and po_parent = '" . $row['po_uid']  . "," . $row2['po_uid'] . "'
                            and (po_other = 'N' or po_other = '')
                        order by po_uid asc
                    ";

                        $oRet = $_09sns->query($query);
                        $option3_result  = $oRet->result_array();
                        $oRet->free_result();

                        foreach( $option3_result as $key3 => $row3 ) {
                            $data['option'][3][$row2['po_uid']][] = array(
                                'uid' => $row3['po_uid'],
                                'name' => $row3['po_poptionname'],
                                'price' => $row3['po_poptionprice'],
                                'second_price' => $row3['po_second_poptionprice'],
                                'third_price' => $row3['po_third_poptionprice']?$row3['po_third_poptionprice']:0,
                                'stock' => $row3['po_cnt'],
                                'img' => $row3['po_img'],
                                'sub_option' => ''
                            );
                        }//end of foreach(3)
                    }//end of if(3)
                }//end of foreach(2)
            }//end of if(2)
        }//end of foreach()

        // 추가옵션정보 불러오기
        $query = "
      select *
      from smart_product_option 
      where po_pcode = '" . $pro_info['p_code'] . "'
          and po_depth = '1'
          and po_other = 'Y'
      order by po_uid asc
    ";

        $oRet = $_09sns->query($query);
        $other_result  = $oRet->result_array();
        $oRet->free_result();

        foreach( $other_result as $key => $row ) {
            $data['other'][] = array(
                'uid' => $row['po_uid'],
                'name' => $row['po_poptionname'],
                'price' => $row['po_poptionprice'],
                'second_price' => $row['po_second_poptionprice'],
                'third_price' => $row['po_third_poptionprice']?$row['po_third_poptionprice']:0,
                'stock' => $row['po_cnt'],
                'img' => $row['po_img']
            );
        }//end of foreach()

    }





    if( empty($data) ) {
        return '';
    }

    return json_encode_no_slashes($data);

}


/**
 * 09sns에서 상품옵션 정보를 가져옴(json)
 * @param $p_code
 * @return bool|string
 */
function get_product_option($p_code) {
    /**
     * == 출력형식 ==
     * [info] =>
     *      [code]
     *      [name]
     *      [price]
     *      [price_app_yn]
     *      [price_app]
     *      [offered_info_use]
     *      [offered_info]
     *      [stock]
     *      [shopping_pay_use]  //배송비정책 사용여부
     *      [shopping_pay]      //배송비
     *      [shopping_pay_free] //무료배송가
     *      [option_type] = nooption|1depth|2depth|3depth|img_option|manual_option
     * [option][1] =>
     *      [uid]
     *      [name]
     *      [price]
     *      [stock]
     *      [img]
     *      [sub_option] => array()
     * [option][2][1차uid] => array()
     * [option][3][2차uid] => array()
     * [other] =>
     *      [uid]
     *      [name]
     *      [price]
     *      [stock]
     *      [img]
     */
    $CI =& get_instance();
    return get_url_content($CI->config->item("order_site_http") . "/api/product_info.php?code=" . $p_code);
}//end of get_product_option()


/**
 * 09sns에서 상품정보 가져옴(json) ($fd가 없으면 get_product_option()와 리턴값이 동일함)
 * @param $p_code
 * @param string $fd : 필드명(aaa:aaa 형식)
 */
function get_product_info_v2($p_code, $fd) {

    if(empty($fd) == true || empty($p_code) == true){
        return array();
    }

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns

    //상품정보
    $pro_info = get_product_info_09($p_code);



    //카테고리정보
    $pro_info['cate1'] = "";
    $pro_info['cate2'] = "";
    $pro_info['cate3'] = "";
    if( !empty($pro_info['p_name_uid']) ) {
        $query = "
            select *
            from smart_product_represent
            where pr_uid = '" . $pro_info['p_name_uid'] . "'
        ";

        $oRet = $_09sns->query($query);
        $pr_row  = $oRet->row_array();
        $oRet->free_result();

        if( !empty($pr_row['pr_category_uid']) ) {
            $cate_row = get_product_category_row_09($pr_row['pr_category_uid']);

            $pro_info['cate1'] = (!empty($cate_row['name1'])) ? $cate_row['name1'] : "";
            $pro_info['cate2'] = (!empty($cate_row['name2'])) ? $cate_row['name2'] : "";
            $pro_info['cate3'] = (!empty($cate_row['name3'])) ? $cate_row['name3'] : "";
        }

    }

    //원하는 필드만 추출일때
    $fd_array = explode(":", $fd);

    if( !empty($fd_array) ) {
        foreach ($fd_array as $item) {
            $tmpdata[$item] = $pro_info[$item];
        }//end of foreach()
    }

    if( empty($tmpdata) ) {
        return array();
    }

    return json_encode_no_slashes($tmpdata);

}
/**
 * 09sns에서 상품정보 가져옴(json) ($fd가 없으면 get_product_option()와 리턴값이 동일함)
 * @param $p_code
 * @param string $fd : 필드명(aaa:aaa 형식)
 */
function get_product_info($p_code, $fd="") {
    $CI =& get_instance();
    return get_url_content($CI->config->item("order_site_http") . "/api/product_info.php?code=" . $p_code . "&fd=" . $fd);
}//end of get_product_info()

/**
 * 09sns에서 상품옵션 정보를 가져옴(json)
 * @param $p_code
 * @return bool|string
 */
function get_product_desc($p_code) {
    /**
     * == 출력형식 ==
     * [smart_product_desc.*]
     * [top_url] : 상단고정이미지
     * [btm_url] : 하단고정이미지
     */
    $CI =& get_instance();
    return get_url_content($CI->config->item("order_site_http") . "/api/product_desc_info.php?code=" . $p_code);
}//end of get_product_desc()


/**
 * 09sns에서 상품옵션 정보를 가져옴(json)
 * @param $p_code
 * @return bool|string
 */
function get_product_desc_v2($p_code) {

    if(empty($p_code) == true){
        return false;
    }

    /**
     * == 출력형식 ==
     * [smart_product_desc.*]
     * [top_url] : 상단고정이미지
     * [btm_url] : 하단고정이미지
     */
    $CI =& get_instance();
    $_09sns = $CI->load->database("09sns", true); //conn 09sns


    $req['code'] = $p_code;

    $data = array();
    $data['top_url'] = "";

//상품정보
    $sql = "
        select
            p.p_sendcompany
            , p.p_name_uid
            , pr.pr_distributor_uid
        from smart_product p
            left join smart_product_represent pr on pr.pr_uid = p.p_name_uid
        where p.p_code = '" . $req['code'] . "'
        limit 1
    ";
    $oResult = $_09sns->query($sql);
    $p_row = $oResult->row_array();
    $oResult->free_result();


    //그룹별 첨부이미지 출력 (180727)
    $sql = "
    select pd.url as top_url
    from smart_product_desc_group pdg
        join smart_product_desc pd on pd.p_desc = pdg.pdg_desc_uid
    where
        pdg_display_state = 'Y'
        and (
            (pdg_onday_yn = 'Y' and pdg_onday_datetime1 <= '" . current_datetime() . "' and pdg_onday_datetime2 >= '" . current_datetime() . "')
            or pdg_onday_yn = 'N'
        )
        and (
            pdg_grp = 'ALL'
            or ( pdg_grp = 'PROD' and find_in_set('" . $p_row['p_name_uid'] . "', pdg_grp_uid) )
            or ( pdg_grp = 'DIST' and find_in_set('" . $p_row['pr_distributor_uid'] . "', pdg_grp_uid) )
            or ( pdg_grp = 'DELV' and find_in_set('" . $p_row['p_sendcompany'] . "', pdg_grp_uid) )
        )
    order by pdg_order asc, pdg_uid desc
    limit 1
    ";
    $oResult = $_09sns->query($sql);
    $row = $oResult->row_array();
    $oResult->free_result();

//그룹별 있으면
    if( isset($row['top_url']) && !empty($row['top_url']) ) {
        $data['top_url'] = $row['top_url'];
    }
//그룹별 없으면 -> 상품별
    else {
        //상품정보에서 첨부이미지 정보 가져오기
        $sql = "
        select B.url top_url
        from smart_product A
            inner join smart_product_desc B on A.p_top_desc = B.p_desc
        where A.p_code = '" . $req['code'] . "'
    ";
        $oResult = $_09sns->query($sql);
        $row = $oResult->row_array();
        $oResult->free_result();

        if( isset($row['top_url']) && !empty($row['top_url']) ) {
            $data['top_url'] = $row['top_url'];
        }
    }//endif;

//기본 첨부이미지 출력
    $sql = "
    select
        (select url from smart_product_desc where gubun = 2 and open_flag = 'Y' limit 1) as btm_url
        , (select url from smart_product_desc where gubun = 3 and open_flag = 'Y' limit 1) as top_fixed_url
        , (select view_flag from smart_product_desc_conf where gubun = 1 limit 1) as top_view_flag
        , (select view_flag from smart_product_desc_conf where gubun = 2 limit 1) as btm_view_flag
        , (select view_flag from smart_product_desc_conf where gubun = 3 limit 1) as top_fixed_view_flag
";
    $oResult = $_09sns->query($sql);
    $row = $oResult->row_array();
    $oResult->free_result();

    if( is_array($row) ) {
        $data = array_merge($data, $row);
    }

    return json_encode_no_slashes($data);

}//end of get_product_desc()



/**
 * 09sns에서 상품 옵션정보 가져옴(json)
 * @param $p_code : 상품코드
 * @param string $uid : 옵션일련번호
 */
function get_product_option_info_v2($p_code, $uid) {
    /**
     * == 출력형식 ==
     * [smart_product_option.*]
     * [name1] : 1차옵션명
     * [name2] : 2차옵션명
     * [name3] : 3차옵션명
     */
    $CI =& get_instance();
    $_09sns = $CI->load->database("09sns", true); //conn 09sns

    $req['code'] = $p_code;
    $req['uid'] = $uid;
    $pro_info = get_product_info_09($req['code']);

    if( empty($pro_info) ) {
        return false;
    }

    $selet_query = "
        select
            a.*
            , a.po_poptionname name1
            , '' name2
            , '' name3
        from smart_product_option a
    ";



    if( $pro_info['p_option_type_chk'] == "2depth" ) {
        $selet_query = "
            select
                a.*
                , IFNULL(b.po_poptionname,'') name1
                , IFNULL(a.po_poptionname,'') name2
                , '' name3
            from smart_product_option a
                left join smart_product_option b on b.po_uid = a.po_parent
        ";
    }
    else if( $pro_info['p_option_type_chk'] == "3depth" ) {
        $selet_query = "
            select
                a.*
                , IFNULL(a.po_poptionname,'') name3
                , IFNULL(b.po_poptionname,'') name2
                , IFNULL(c.po_poptionname,'') name1
            from smart_product_option a
                left join smart_product_option b on b.po_uid = SUBSTRING_INDEX(a.po_parent, ',', -1)
                left join smart_product_option c on c.po_uid = SUBSTRING_INDEX(a.po_parent, ',', 1)    
        ";
    }

    $query = $selet_query . "
        where
            a.po_pcode = '" . $req['code'] . "'
            and a.po_uid = '" . $req['uid'] . "';
    ";

    unset($pro_info['p_offered_info']);

    $oResult    = $_09sns->query($query);
    $option_row = $oResult->row_array();
    $oResult->free_result();
    $option_row = array_merge($option_row,$pro_info);

    return json_encode_no_slashes($option_row);

}//end of get_product_info()

/**
 * 09sns에서 상품 옵션정보 가져옴(json)
 * @param $p_code : 상품코드
 * @param string $uid : 옵션일련번호
 */
function get_product_option_info($p_code, $uid) {
    /**
     * == 출력형식 ==
     * [smart_product_option.*]
     * [name1] : 1차옵션명
     * [name2] : 2차옵션명
     * [name3] : 3차옵션명
     */
    $CI =& get_instance();
    return ($CI->config->item("order_site_http") . "/api/product_option_info.php?code=" . $p_code . "&uid=" . $uid);
}//end of get_product_info()

/**
 * 09sns에서 기획전 정보가져옴(json)
 * @param $in_id : 판매자아이디
 * @param JSON $list : 이미지 리스트 여부
 */
function get_exhibition($in_id,$seq = '',$list = 'N'){
    /**
     * == 출력형식 ==
     * [smart_exhibition.*] // exception_mall 제외되지 않은 모든 기획전 리스트
     * [pdt_list] : array() 상품상품
     */
    $CI =& get_instance();
    return get_url_content($CI->config->item("order_site_http") . "/api/special_exhibition.php?in_id={$in_id}&list={$list}&seq={$seq}");
}//end of get_exhibition()

/**
 * 09sns에 SMS 인증번호 요청(json)
 * @param $inid
 * @param $mkey
 * @param $ph
 * @param string $retry
 * @return bool|string
 */
function sms_auth_req($inid, $mkey, $ph, $retry=false) {
    $CI =& get_instance();

    return false;
}//end of sms_auth_req()

/**
 * 09sns에 인증 확인(json)
 * @param $inid
 * @param $mkey
 * @param $no
 * @return bool|string
 */
function sms_auth_cert($inid, $mkey, $no) {
    $CI =& get_instance();

    $param = "dummy=" . time() . "&mode=cert&inid=" . $inid . "&mkey=" . $mkey . "&no=" . $no;
    $param_enc = seed_encrypt($param, true);

    $url = $CI->config->item("order_site_http") . "/api/sms_auth.php?data=" . $param_enc;
    return get_url_content($url);
}//end of sms_auth_cert()

/**
 * 알림톡 발송 요청
 * @param $inid : 상점아이디
 * @param $type : 알림톡 발송문구유형(user1, user2, ...) (등록 후 발송해야함)
 * @param $ph : 휴대폰번호
 * @param $replace_text : 교체할 문자열({EVENT_NAME}:오픈이벤트|{REG_DATE}:2017-01-01})
 */
function req_alimtalk_send($ph, $type, $replace_text) {
    $CI =& get_instance();

    $url = $CI->config->item("order_site_http") . "/api/alimtalk_send.php";
    $param = "dummy=" . time() . "&inid=" . $CI->config->item("order_cpid") . "&ph=" . $ph . "&type=" . $type . "&replace_text=" . urlencode($replace_text);
    $param = "data=" . seed_encrypt($param, true);

    return http_post_request($url, $param);
}//end of req_alimtalk_send()

/**
 * 레퍼러 카운터
 * @return mixed
 */
function req_counter_referer($ref="", $ref_site="", $ref_kwd="") {
    if( empty($ref) && empty($ref_site) && empty($ref_kwd) ) {
        return false;
    }

    $CI =& get_instance();

    $url = $CI->config->item("order_site_http") . "/api/counter_referer.php";
    $param = "inid=" . $CI->config->item("order_cpid") . "&ref=" . urlencode($ref) . "&ref_site=" . urlencode($ref_site) . "&ref_kwd=" . urlencode($ref_kwd);

    return http_post_request($url, $param);
}//end of req_counter_referer()
//======================================================================= /09sns

/**
 * CSS, JS 파일 링크 html
 * @param $url
 * @param string $type : js|css|''
 */
function link_src_html($url, $type="") {
    $filetime = @filemtime(DOCROOT . $url);

    if( $type == "css" ) {
        echo '<link href="' . $url . '?v=' . $filetime . '" rel="stylesheet" />';
    }
    else if( $type == "js" ) {
        echo '<script src="' . $url . '?v=' . $filetime . '" type="text/javascript"></script>';
    }
    else {
        echo $url . '?v=' . $filetime;
    }
}//end of link_src_html()

/**
 * POST 로 보내기 (cURL사용)
 * @param $url
 * @param $params
 * @return mixed
 */
function http_post_request($url, $params="") {
    $postData = '';

    if( is_array($params) ) {
        foreach($params as $k => $v) {
            $postData .= $k . '=' . $v . '&';
        }
        $postData = rtrim($postData, '&');
    }
    else {
        $postData = $params;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $output = curl_exec($ch);

    curl_close($ch);
    return $output;
}//end of http_post_request()

/**
 * 쿠키에서 최근본상품 추출
 * @param string $limit
 * @param string $banner
 * @return array
 */
function get_recently_product($limit="", $banner="",$bMainRolling=false) {
    $CI =& get_instance();

    if( is_app() == true ) {
        $recently_cookie = $CI->getRctly();
    }else{
        $recently_cookie = get_cookie('rctlyViewPdt');
    }

    $recently_list = array();

    if( !empty($recently_cookie) ) {
        $CI->load->model('product_model');

        $recently_array = json_decode($recently_cookie, true);

        foreach($recently_array as $key => $p_num) {

            $product_row = $CI->product_model->get_product_row(array('p_num' => $p_num));   //판매종료 제외

            //상품정보 없으면 제외
            if( empty($product_row) ) {
                unset($recently_array[$key]);
                continue;
            }
            //판매종료, 품절 제외
            if( $product_row['p_sale_state'] != "Y" || $product_row['p_display_state'] != "Y" || $product_row['p_stock_state'] != "Y" ) {
                unset($recently_array[$key]);
                continue;
            }

            //배너이미지 확인시
//            if( !empty($banner) && empty($product_row['p_banner_image']) ) {
//                continue;
//            }

//            $product_row['p_rep_image_array'] = json_decode($product_row['p_rep_image'], true);
//            $product_row['p_display_info_array'] = json_decode($product_row['p_display_info'], true);
//            $product_row['p_display_info_1_text'] = "";

//            foreach($CI->config->item('product_display_info1') as $iKey => $iItem) {
//                if( $product_row['p_display_info_array'][$iKey] == 'Y' ) {
//                    $product_row['p_display_info_1_text'] = $iItem;
//                }
//            }

//            $product_row['p_review_count_str']    = number_format($product_row['p_review_count']);
//            $product_row['p_tot_order_count_str'] = product_count($product_row['p_tot_order_count']);

            $recently_list[] = $product_row;
        }//end of foreach()

        if( is_app() == true ) {
            $CI->setRctly(json_encode_no_slashes($recently_array));
        }
        else {
            saveCookie('rctlyViewPdt', json_encode_no_slashes($recently_array), strtotime("+7 days"), "/");
        }

        if( !empty($limit) && count($recently_list) > $limit ) {
            $recently_list = array_slice($recently_list, 0, $limit);
        }
    }//end of if()

    return $recently_list;

}//end of get_recently_product()

/**
 * 카카오스토리 웹뷰인지
 * @param platform : android|ios
 * @returns {boolean}
 */
function is_kakastory_agent($platform="") {
    $CI =& get_instance();

    $ks = false;

    if( preg_match("/kakaostory/i", $CI->input->user_agent()) !== false ) {
        $ks = true;
    }

    if( !empty($platform) ) {
        //안드로이드 KS인지
        if( $platform == 'android' ) {
            if( $ks && is_android() ) {
                $ks = true;
            }
            else {
                $ks = false;
            }
        }
        //IOS KS인지
        else if( $platform == 'ios' ) {
            if( $ks && is_ios() ) {
                $ks = true;
            }
            else {
                $ks = false;
            }
        }
        else {
            $ks = false;
        }
    }

    return $ks;
}//end of is_kakastory_agent()

/**
 * DB에 입력할 값을 필터링함
 * @param $val
 * @return string
 */
function db_val($val) {
    if( is_null($val) ) {
        $val = "";
    }

    return addslashes($val);
}//end of db_val()

/**
 * 디렉터내 파일 갯수 체크
 * @param $dir
 * @return bool|int
 */
function dir_file_count($dir) {
    $cnt = 0;

    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if( $entry == "." || $entry == ".." ) {
                continue;
            }

            if( is_dir($dir . $entry) ) {
                $cnt += dir_file_count($dir . "/" . $entry);
            }
            else {
                $cnt++;
            }
        }//endwhile()

        @closedir($handle);

        return $cnt;
    }
    else {
        return false;
    }
}//end of dir_file_count()


function send_app_push_log($m_num, $push_data){

    if(empty($m_num) == true) return false;
    if(empty($push_data['title']) == true || empty($push_data['page']) == true ) return false;

    $CI =& get_instance();

    $sql     = "SELECT m_regid FROM member_tb WHERE m_num = '{$m_num}';";
    $oResult = $CI->db->query($sql);
    $aResult = $oResult->row_array();

    //푸시발송
    $resp = send_app_push($aResult['m_regid'],$push_data );

    if($resp['success'] == true){
        $sql = "INSERT INTO noti_tb
                    SET m_num           = '{$m_num}'
                    ,   noti_subject    = '{$push_data['title']}'
                    ,   noti_content    = '{$push_data['body']}'
                    ,   loc_type        = '{$push_data['page']}'
                    ,   reg_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
             ";

        $CI->db->query($sql);
    };

    return $resp;

}

/**
 * 푸시 발송
 * @param $regid
 * @param array $push_data : array([title]=>, [msg]=>, [smr]=>, [icon]=>, [img]=>, [tarUrl]=>, [notiType]=>, [badge]=>) 형식
 * @return mixed
 */
function send_app_push($regid, $push_data=array()) {
    if( empty($regid) || empty($push_data) ) {
        return false;
    }

    //필수값 체크
    if( empty($push_data['title'])  ) {
        return false;
    }

    $CI =& get_instance();

    // 헤더 부분
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $CI->config->item('google_server_key')
    );

    if( !isset($push_data['badge']) || empty($push_data['badge']) ) {
        $push_data['badge'] = "Y";
    }

    //- registration_ids : array. 1~1000 개의 아이디가 들어갈 수 있다.
    //- collapse_key : message type 을 grouping 하는 녀석으로, 해당 단말이 offline 일 경우 가장 최신 메세지만 전달되는 형태다.
    //- data : key-value pair.
    //- delay_while_idle : message 가 바로 전송되는 것이 아니라, phone 이 active 되었을 때 collapse_key 의 가장 마지막 녀석만 전송되도록. : false => 꺼져(잠겨)있어도 전송, true => 켜지면 전송
    //- time_to_live : 단말이 offline 일 때 GCM storage 에서 얼마나 있어야 하는지를 설정함. collapse_key 와 반드시 함께 설정되야 한다.
    //- dry_run : true=테스트, false=실제발송

    // 푸시 내용, data 부분을 자유롭게 사용해 클라이언트에서 분기할 수 있음.
    $arr = array();
    $arr['data'] = array();
    $arr['data']['title']   = $push_data['title'];  //제목
    $arr['data']['body']    = $push_data['body'];    //내용
    $arr['data']['page']    = $push_data['page'];   //내용
    if(empty($push_data['seq']) == false) $arr['data']['seq']    = $push_data['seq'];   //내용
    $arr['data']['badge']   = 'Y';        //뱃지올리기여부(Y/N)
    $arr['priority']        = "high";     //메시지의 우선순위(normal | high)
    $arr['registration_ids'] = is_array($regid) ? $regid : array($regid);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
    $response = curl_exec($ch);
    curl_close($ch);

    //log_message('ZS','Push A - '.$response);

    // 푸시 전송 결과 반환.
    $result = json_decode($response, true);

    return $result;
}//end of send_app_push()


/**
 * 디버그 확인211.217.209.180
 * @return boolean
 */
function zsDebug(){//서울내
    $aChkIp = array( "106.243.140.135" );
    if(in_array($_SERVER['REMOTE_ADDR'],$aChkIp)){ return true; }else{ return false; }
}

/**
 * 배열 / stdClass 잘보이도록
 * @return mixed
 */
function zsView($params,$chkExit = false){
    echo '<xmp>';
    print_r($params);
    echo '</xmp>';
    if($chkExit == true){ exit; }
}
function ph_slice($ph_no){

    $ph_no = onlynumber($ph_no);

    if(strlen($ph_no) <= 10){ // 011-205-2355
        $str = mb_substr($ph_no, 0, 3) . "-" . mb_substr($ph_no, 3, 3) . "-" . mb_substr($ph_no, 6, 4) ;
    }else{ // 011-2055-2355
        $str = mb_substr($ph_no, 0, 3) . "-" . mb_substr($ph_no, 3, 4) . "-" . mb_substr($ph_no, 7, 4) ;
    }
    return $str;
};
/**
 * 날짜비교
 * @param 시작날짜($frDt), 종료날짜($toDt)
 * @return intiger
 */
function dayDiff($frDt, $toDt){
    $tm1 = strtotime($frDt);
    $tm2 = strtotime($toDt);
    return round(($tm2-$tm1)/(60*60*24));
}
// 콤마를 제거한다.
function delComma($val) {
    return str_replace(",","",$val);
}
function view_date_format( $sourceString, $type = 4 ) {
    switch ($type) {
        case 1: // YYYY년 MM월 DD일
            $str = mb_substr($sourceString, 0, 4) . "년 " . mb_substr($sourceString, 4, 2) . "월 " . mb_substr($sourceString, 6, 2) . "일";
            break;
        case 2: // YYYY.MM.DD HH:II
            $str = mb_substr($sourceString, 0, 4) . "." . mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2) . " " . mb_substr($sourceString, 8, 2) . ":" . mb_substr($sourceString, 10, 2);
            break;
        case 3: // YYYY.MM.DD HH:II:SS
            $str = mb_substr($sourceString, 0, 4) . "." . mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2) . " " . mb_substr($sourceString, 8, 2) . ":" . mb_substr($sourceString, 10, 2) . ":" . mb_substr($sourceString, 12, 2);
            break;
        case 4: // YYYY.MM.DD
            $str = mb_substr($sourceString, 0, 4) . "." . mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2);
            break;
        case 5: // YYYY-MM-DD
            $str = mb_substr($sourceString, 0, 4) . "-" . mb_substr($sourceString, 4, 2) . "-" . mb_substr($sourceString, 6, 2);
            break;
        case 6: // YYYY년 MM월 DD일 HH시 II분
            $str = mb_substr($sourceString, 0, 4) . "년 " . mb_substr($sourceString, 4, 2) . "월 " . mb_substr($sourceString, 6, 2) . "일" . " " . mb_substr($sourceString, 8, 2) . "시 " . mb_substr($sourceString, 10, 2). "분";
            break;
        case 7: // YYYY-MM-DD HH:II:SS
            $str = mb_substr($sourceString, 0, 4) . "-" . mb_substr($sourceString, 4, 2) . "-" . mb_substr($sourceString, 6, 2) . " " . mb_substr($sourceString, 8, 2) . ":" . mb_substr($sourceString, 10, 2) . ":" . mb_substr($sourceString, 12, 2);
            break;
        case 8: // MM.DD
            $str = mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2);
            break;

        case 9: // YYYY/MM/DD
            $str = mb_substr($sourceString, 0, 4) . "/" . mb_substr($sourceString, 4, 2) . "/" . mb_substr($sourceString, 6, 2);
            break;

        case 10: // YY.MM.DD
            $str = mb_substr($sourceString, 2, 2) . "." . mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2);
            break;
        case 11: // YYYY-MM
            $str = mb_substr($sourceString, 0, 4) . "-" . mb_substr($sourceString, 4, 2);
            break;
        case 12: // DD:HH
            $str = mb_substr($sourceString, 8, 2) . ":" . mb_substr($sourceString, 10, 2);
            break;
        case 50: // YYYY.MM.DD(요일) AM 00:00
            $sDate 		= strtotime($sourceString);
            $aWeekArray = array('일', '월', '화', '수', '목', '금', '토');

            $nWeekNo = date('w', $sDate );

            $sWeekName = $aWeekArray[$nWeekNo];

            $str = date('Y.m.d', $sDate);
            $str.= sprintf('(%s)', $sWeekName);
            $str.= date(' A H:i', $sDate);
            break;

        case 51	://YYYY.MM.DD ({요일 한글명})	/ 2010/12/24, 이강수 / 쪽지
            $aWeekArray = array('일', '월', '화', '수', '목', '금', '토');

            $nWeekNo = date('w', strtotime( view_date_format($sourceString, 5) ) );

            $sWeekName = $aWeekArray[$nWeekNo];

            $str = mb_substr($sourceString, 0, 4) . "." . mb_substr($sourceString, 4, 2) . "." . mb_substr($sourceString, 6, 2);
            $str.= sprintf(' (%s)', $sWeekName);
            break;

        case 52: // YYYY.MM.DD(요일) 밤 00:00

            $sDate 		= strtotime($sourceString);
            $aWeekArray = array('일', '월', '화', '수', '목', '금', '토');
            $nWeekNo = date('w', $sDate );

            if(date('A', $sDate ) == 'AM') {
                $sAmPm = '낮';
            } else {
                $sAmPm = '밤';
            }

            $sWeekName = $aWeekArray[$nWeekNo];

            $str = date('Y.m.d', $sDate);
            $str.= sprintf('(%s) ', $sWeekName);
            $str.= $sAmPm;

            $str.= date(' H:i', $sDate);

            break;

    }
    return $str;
}


/**
 * @date 180326
 * @modify 황기석
 * @desc 공유하기 상품 가져오기
 */

function getKakaoStoryShareProduct(){

    $CI =& get_instance();

    /**
     * @date 180321
     * @modify 황기석
     * @desc 대표님요청 :: 노출 상품수가 적어 진열은 되어있지만, 판매중이 아닌 상품도 포함하여 상품을 출력
     */
    //$sql = "SELECT * FROM product_tb WHERE p_display_state = 'Y' AND p_sale_state = 'Y' AND p_stock_state = 'Y' ORDER BY RAND() LIMIT 1 ; ";
    $sql = "SELECT * FROM product_tb WHERE p_display_state = 'Y' AND p_outside_display_able = 'Y' AND p_hash <> '' ORDER BY RAND() LIMIT 1 ; ";
    $oResult        = $CI->db->query($sql);
    $product_row    = $oResult->row_array();

    $product_row['p_rep_image_array'] = json_decode($product_row['p_rep_image'], true);

    $link        = $CI->config->item("site_domain_http") . "/product/detail/" . $product_row['p_num'];
    $link       .= "/?ref_site=hashtag";
    $utm_param   = "&utm_source=hashtag&utm_campaign=hashtag";
    $ios_add     = "&isi=".$CI->config->item("ios_link_key")."&ibi=".$CI->config->item("app_id");
    $long_url    = $CI->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&apn=" . $CI->config->item("app_id") . "&afl=" . $utm_param.$ios_add;
    $url         = get_short_url($long_url);

    if($product_row['p_hash']){
        $sHash       = str_replace(',',' #',$product_row['p_hash']);
        $sHash       = '#'.$sHash;
    }else{
        $sHash       = '#옷쟁이들';
    }
    $p_detail    = strip_tags(str_replace("\n\n","\n",str_replace(array("><","&nbsp;"),array(">\n<","") ,($product_row['p_detail']))));

    $share_product_info = array(
        'nick'              => $_SESSION['session_m_nickname']?$_SESSION['session_m_nickname']:$CI->config->item('site_name_kr')
    ,   'share_short_url'   => $url
    ,   'share_url'         => $CI->config->item('share_url'). "/?url=" . urlencode($CI->config->item('site_domain_http') . "/product/detail/?p_num=" . $product_row['p_num'])
    ,   'share_text'        => "{$product_row['p_name']}\n\n{$url}\n{$url}\n{$p_detail}\n{$sHash}"
        //,   'share_text'        => "{$product_row['p_name']}\n\n{$url}\n{$url}\n{$sHash}"
    ,   'share_img'         => $CI->config->item('site_domain_http').$product_row['p_rep_image_array'][1]
    ,   'share_title'       => $CI->config->item('site_name_kr')."[{$product_row['p_name']}]"
    ,   'share_desc'        => $CI->config->item('site_description')
    ,   'share_name'        => $CI->config->item('site_name_kr')
    );

    return $share_product_info;

}

/**
 * @date 180503
 * @modify 황기석
 * @params $url , $ref_site : 리퍼러 , $campaign : 캠패인(arrival_source) , $is_web : true > 웹 ; false > 마켓
 * @desc dynamic url 생성
 * @usage create_dynamic_url('/product/detail/?p_num=3483','','',false);
 */
function create_dynamic_url($url,$ref_site='',$campaign='',$is_web = false){

    if($url == ''){
        return false;
    }

    $CI =& get_instance();

    $Query_String   = explode("&", explode("?", $url)[1] );
    $utm_param      = '';
    $link           = '';

    if($Query_String[0] == ''){ //파라메터없음
        $link       .= $url;
        $link       .= $ref_site?"?ref_site={$ref_site}":'';
    }else{
        $link       .= $url;
        $link       .= $ref_site?"&ref_site={$ref_site}":'';
    }

    if($is_web == true){ //앱x > 웹
        $utm_param  .= urlencode($link); //안드로이드
        $ios_add     = "&ibi=".$CI->config->item("app_id"); //아이폰
    }else{ // 무조건 마켓
        $utm_param  .= ''; //안드로이드
        $ios_add     = "&isi=".$CI->config->item("ios_link_key")."&ibi=".$CI->config->item("app_id"); //아이폰
    }

    $utm_param  .= "&utm_source={$ref_site}&utm_campaign={$campaign}";
    $long_url    = $CI->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&apn=" . $CI->config->item("app_id") . "&afl=" . $utm_param.$ios_add;

    return $long_url;

}// end of create_dynamic_url

/**
 * 회원 프로필 이미지 썸네일 생성 (200 x 200 기준, /files/profile_img 에 저장)
 * @param $url
 * @param $m_num
 * @return string
 */
function create_profile_image_thumb($url, $m_num) {
    $CI =& get_instance();

//    error_reporting(-1);
//    ini_set('display_errors', 1);
//    error_reporting(E_ALL & ~E_NOTICE);

    if( empty($url) ) {
        return false;
    }

    $file_data = file_get_contents($url);
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->buffer($file_data);

    $ext = "";
    if( $mime_type == "image/jpeg" ) {
        $ext = "jpg";
    }
    else if( $mime_type == "image/png" ) {
        $ext = "png";
    }
    else if( $mime_type == "image/gif" ) {
        $ext = "gif";
    }

    //썸네일 생성
    $thumb_w = 200;     //가로 사이즈
    $thumb_h = 200;     //세로 사이즈
    $crop_yn = "Y";     //자르기 여부(Y|N)

    $file_root_dir = $CI->config->item("member_profile_dir") . "/";
    $file_cnt = dir_file_count(HOMEPATH . $file_root_dir);

    //2000개씩 구분해서 저장
    $file_cnt_div = (int)($file_cnt / 2000);
    $file_root_dir .= ($file_cnt_div + 1) * 2000 . "/";
    $file_root_path = HOMEPATH . $file_root_dir;
    create_directory($file_root_path);

    $org_file = $file_root_path . $m_num . "." . $ext;
    file_put_contents($org_file, $file_data);

    $thumb_file = $file_root_path . $m_num . "_thumb." . $ext;
    $thumb_file_web = $file_root_dir . $m_num . "_thumb." . $ext;

    $img_info = getimagesize($org_file);
    $img_width = $img_info[0];
    $img_height = $img_info[1];
    $img_mime = $img_info['mime'];

    //  //생성할 썸네일보다 크기가 작으면 continue
    //  if( $data['image_width'] <= $thumb_w && $data['image_height'] <= $thumb_h ){
    //      continue;
    //  }





    //비율을 구함
    $w_rule = ($thumb_w / $img_width);
    $h_rule = ($thumb_h / $img_height);

    //자르기
    if( $crop_yn == 'Y' ) {
        //큰쪽 비율 (자르기용)
        $rule = ($w_rule >= $h_rule) ? $w_rule : $h_rule;
        $chk_size = ($w_rule >= $h_rule) ? 'A' : 'B';

        $dst_w = ceil($img_width * $rule);
        $dst_h = ceil($img_height * $rule);

        if($chk_size == 'B'){
            $pos_x = 0;
            $pos_y = (int)(($dst_h - $thumb_h) / 2);
        }else{
            $pos_x = (int)(($dst_w - $thumb_w) / 2);
            $pos_y = (int)(($dst_h - $thumb_h) / 2);
        }


    }
    else {
        //작은쪽 비율 (안자르기용)
        $rule = ($w_rule <= $h_rule) ? $w_rule : $h_rule;

        //가로기준
        if( $w_rule <= $h_rule ) {
            $dst_w = $thumb_w;
            $dst_h = $thumb_h = ceil($img_height * $rule);
        }
        //세로기준
        else {
            $dst_w = $thumb_w = ceil($img_width * $rule);
            $dst_h = $thumb_h;
        }//end of if()

        $pos_x = 0;
        $pos_y = 0;
    }//end of if()


    if( $img_mime == "image/jpeg" ) {
        $org_img = imagecreatefromjpeg($org_file);
    }
    else if( $img_mime == "image/gif" ) {
        $org_img = imagecreatefromgif($org_file);
    }
    else if( $img_mime == "image/png" ) {
        $org_img = imagecreatefrompng($org_file);
    }
    else {
        return false;
    }

    $exif = exif_read_data($org_file);

    if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
            case 8:
                $org_img = imagerotate($org_img,90,0);
                break;
            case 3:
                $org_img = imagerotate($org_img,180,0);
                break;
            case 6:
                $org_img = imagerotate($org_img,-90,0);
                break;
        }
    }

    $new_img = imagecreatetruecolor($thumb_w, $thumb_h);
    imagecopyresampled($new_img, $org_img, -$pos_x, -$pos_y, 0, 0, $dst_w, $dst_h, $img_width, $img_height);

    if( $img_mime == "image/jpeg" ) {
        imagejpeg($new_img, $thumb_file, 90);
    }
    else if( $img_mime == "image/gif" ) {
        imagegif($new_img, $thumb_file);
    }
    else if( $img_mime == "image/png" ) {
        imagepng($new_img, $thumb_file, 9);
    }

    @chmod($thumb_file, 0775);

    //원본 삭제
    @unlink($org_file);

    return $thumb_file_web;
}//end of create_profile_image_thumb()

/**
 * CDN 이미지 Purge(새로고침)
 * @param $img_url : 이미지 URL(도메인없이)(예: /upload/product/2018/0501/이미지명.jpg)
 * @return bool
 */
function cdn_purge($img_url) {

}//end of cdn_purge()

/**
 * HTML 소스에 img src 에 CDN 주소 적용하기
 */
function cdn_html_img_src_convert($html) {
    if( empty($html) ) {
        return '';
    }

    $CI =& get_instance();

    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $html, $matches);

    $replace_arr = array();
    foreach($matches[1] as $item) {
        $src = str_replace(array($CI->config->item('site_domain_http'), $CI->config->item('site_img_http')), "", $item);
        $src = $CI->config->item('site_img_http') . $src;
        $replace_arr[] = $src;
    }
    $html = str_replace($matches[1], $replace_arr, $html);

    //중복 입력 오류 제거
    $err_arr = array(
        $CI->config->item('site_domain_http') . $CI->config->item('site_img_http'),
        $CI->config->item('site_img_http') . $CI->config->item('site_domain_http'),
        $CI->config->item('site_domain_http') . $CI->config->item('site_domain_http'),
        $CI->config->item('site_img_http') . $CI->config->item('site_img_http'),
    );
    return str_replace($err_arr, $CI->config->item('site_img_http'), $html);
}//end of cdn_html_img_src_convert()


/**
 * @date 180607
 * @date 190305 : m_num 으로 체크하도록 변경함. (김홍주)
 * @modify 레이어팝업 정보여부 확인
 * @desc 정보가 있으면(true) 레이어팝업 x / 정보가 없으면(false) 레이어팝업 open
 * @return boolean
 */
function chk_layer_pop($fd) {
    if( empty($fd) ) {
        return true;
    }

    $CI =& get_instance();

    $sql = "
        SELECT * 
        FROM layer_pop_chk_tb 
        WHERE
            {$fd} > DATE_FORMAT(NOW(),'%Y%m%d')  
            AND m_num = '{$_SESSION['session_m_num']}'
    ";
    $oResult = $CI->db->query($sql);
    $aResult = $oResult->result_array();

    if( empty($aResult) ) { //레이어팝업 정보
        return false;
    }
    else {
        return true;
    }
}//end chk_layer_pop;

function app_n_chk(){

    //if( preg_match("/\/product_n\/detail/", $_SERVER['REQUEST_URI']) ) {



    if (preg_match('/(\/main_n\?|\/product_n\/detail|\/product_n\/list_n|\/product_n\/cate_list_n|\/main_n\/lnb_app)/i', $_SERVER['REQUEST_URI'])) {
        //log_message('sh', "1_".$_SERVER['REQUEST_URI']);
        return true;
    }else{
        //log_message('sh', "1_N".$_SERVER['REQUEST_URI']);
        return false;
    }

}

//가중치관련 func
function weighted_random($weights) {
    $r = rand(1, array_sum($weights));
    for($i=0; $i<count($weights); $i++) {
        $r -= $weights[$i];
        if($r < 1) return $i;
    }
    return false;
}

/**
 * 핫딜 카운터 정리
1~99 : 100+
100~199 : 200+
200~299 : 300+
300~399 : 400+
....
900~999 : 1천+
1,000~1,999 : 2천+
....
9,000~9,999 : 1만+
10,000~19,999 : 2만+
 *
 * 1~49 : 50+
 * 50~99 : 100+
 * 100~199 : 200+
 * 200~299 : 300+
 * 300~399 : 400+
 * 900~999 : 1000+....
 *
 * 1,000~1,999 : 2000+
 * 2,000~2,999 : 3000+
 * 3,000~3,999 : 4000+
 * 9,000~9,999 : 10000+...
 *
 * 10,000~10,999 : 11000+
 * 11,000~11,999 : 12000+
 * 12,000~12,999 : 13000+
 * 13,000~13,999 : 14000+
 * 19,000~19,999 : 20000+
 *
 * 20,000~20,999 : 21000+
 **/

function product_count($n, $type=1){

    if(is_numeric($n) == false) return '';

    if( $n <= 0 ) {
        return "0";
    }

    //1~50 => 50+
    if( $n >= 1 && $n < 50 ) {
        return "50+";
    }
    //50 ~
    else {
        $n_cnt = strlen($n);
        $right = ($n_cnt > 3) ? "000" : "00";

        $left = substr($n, 0, -strlen($right)) + 1;
        $v = number_format($left . $right) . "+";
        return $v;

        //$n_cnt = strlen($n); //자리수
        //$num1 = 1; // + num
        //$v = 0; // 비교값
        //
        //for ($i = 0; $i < $n_cnt-1 ; $i++) {
        //    $num1 = $num1 * 10;
        //}
        //
        //if($n_cnt <= 2) $num1 = 100;
        //
        //while ($v <= $n) {
        //    if( strlen($v) > $n_cnt ) $num1 = $num1 * 10;
        //    $v = $v + $num1;
        //}
        //
        ////숫자그대로 출력
        //if( $type == 2 ) {
        //    $type_arr = array(4 => ',000', 5 => '0,000', 6 => '00,000', 7 => ',000,000', 8 => '0,000,000');
        //}
        ////한글로 출력
        //else {
        //    $type_arr = array(4 => '천', 5 => '만', 6 => '십만', 7 => '백만', 8 => '천만');
        //}
        //
        //if(strlen($v) >= 4){
        //    $v = mb_substr($v, 0, 1).$type_arr[strlen($v)].'+';
        //}else{
        //    $v = number_format($v).'+';
        //}
        //
        //return $v;
    }//endif;
}//end product_count;


function get_today_hotdeal($hotdeal_list_1,$hotdeal_list_2){




    $i = $condi_cnt_1 = $condi_cnt_2 = 0;

    $hotdeal_list_1_dummy = $aResult = $chk_arr = array();

    foreach ($hotdeal_list_2 as $row) {
        if($i < 12){
            $chk_arr[] = $row->p_num;
        }
        $i++;
    }

    $i = 0;

    foreach ($hotdeal_list_1 as $row) {
        if(in_array($row->p_num , $chk_arr) == false){
            $hotdeal_list_1_dummy[] = $row;
        }
    }

    unset($hotdeal_list_1);
    $hotdeal_list_1 = $hotdeal_list_1_dummy;

    do{

        if($i < 5){

            if($i < 3){
                $aResult[$i] = $hotdeal_list_2[$condi_cnt_1];
                $condi_cnt_1++;
            }else{
                $aResult[$i] = $hotdeal_list_1[$condi_cnt_2];
                $condi_cnt_2++;
            }

        }else{

            if($i%5 == 3 || $i%5 == 4){
                $aResult[$i] = $hotdeal_list_1[$condi_cnt_2];
                $condi_cnt_2++;
            }else{
                $aResult[$i] = $hotdeal_list_2[$condi_cnt_1];
                $condi_cnt_1++;
            }

        }
        $i++;

    }while(count($aResult) < 20);

    return $aResult;



}

/**
 * 쿠키에 저장
 * @param $name
 * @param $value
 * @param int $expire
 */
function saveCookie($name, $value, $expire=0) {
    global $_COOKIE;

    setcookie($name, $value, $expire, "/", COOKIE_DOMAIN);
    $_COOKIE[$name] = $value;
}//end saveCookie;

/**
 * 연관배열 정렬
 * @param $array : 배열
 * @param $field : 정렬할 키
 * @param string $sort_type : asc=오른차순, desc=내림차순
 * @return array
 */
function array_key_sort($array, $field, $sort_type="desc") {
    if( empty($array) ) {
        return array();
    }

    $temp_array = array();

    foreach ($array as $key => $item) {
        $temp_array[$key] = $item[$field];
    }

    if( $sort_type == "asc" ) {
        asort($temp_array);
    }
    else {
        arsort($temp_array);
    }

    $return_array = array();

    foreach ($temp_array as $key => $item) {
        $return_array[$key] = $array[$key];
    }

    return $return_array;
}//end of array_key_sort()

/**
 * 일기준 시작일 가져오기
 */
function getStartdate() {
    $date = date('Ymd' . '000000');
    return $date;
}

/**
 * 일기준 마지막일 가져오기
 */
function getEnddate($period) {
    $date = date('Ymd', strtotime("+" . $period . " days")) . "235959";
    return $date;
}

###  숫자만 반환
function onlynumber($str){
    //$str = ereg_replace("[^0-9]", "",$str);
    $str = preg_replace("/[^0-9]*/s","",$str);
    return $str;
}

/**
 * URL에서 tag 추출
 * @param $backUrl
 */
function get_join_tag($backUrl="", $device_model="") {
    //backURL이 없으면 기본 태그
    if( empty($backUrl) ) {
        if( is_app_1() || (!empty($device_model) && in_array($device_model, array("iPad", "iPhone", "iPod touch"))) ) {
            return "appstore";
        }
        else {
            return "googleplay";
        }
    }//endif;

    //푸쉬 단축 url 디코딩이 안되기때문에 예외 처리
    if( (strpos($backUrl, "//goo.gl") !== false || strpos($backUrl, "//bit.ly") !== false) ) {
        return "app_push";
    }
    //나머지 리퍼러 추출
    if( strpos($backUrl, "ref_site") !== false ) {
        if( strpos($backUrl, "&") !== false ) {
            preg_match_all("@ref_site=(.+?)&@", $backUrl, $chk_url, PREG_SET_ORDER);
        }
        else {
            preg_match_all("@ref_site=(.*)@", $backUrl, $chk_url, PREG_SET_ORDER);
        }

        if($chk_url['0']['1']){
            return $chk_url['0']['1'];
        }
    }
}//end get_join_tag;


function get09PointInfo($arrayParams){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $cpid       = $CI->config->item('order_cpid');

    $whereQueryString = '';

    if(empty($arrayParams['pt_uid']) == false){
        $whereQueryString .= " AND pt_uid = '{$arrayParams['pt_uid']}' ";
    }

    if(empty($arrayParams['pt_code']) == false){
        $whereQueryString .= " AND pt_code = '{$arrayParams['pt_code']}' ";
    }

    if(empty($whereQueryString) == true){
        return false;
    }

    $query      = "SELECT * FROM point_master WHERE 1 AND pt_inids LIKE '%{$cpid}%' {$whereQueryString} ; ";
    $oRet       = $_09sns->query($query);
    $aPointInfo = $oRet->row_array();
    $oRet->free_result();

    return $aPointInfo;

}

function insert_09Point($arrayParams){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $cpid       = $CI->config->item('order_cpid');
    $query      = "SELECT * FROM point_master WHERE pt_uid = '{$arrayParams['pm_point_id']}' AND pt_inids LIKE '%{$cpid}%' AND pt_use_yn = 'Y' ; ";
    $oRet       = $_09sns->query($query);
    $aPointInfo = $oRet->row_array();
    $oRet->free_result();

    if(empty($aPointInfo) == true){
        return array('success' => false , 'msg' => '적립금정보가 없습니다.[empty MasterData]');
    }
    if(empty($arrayParams['pm_member_key']) == true){
        return array('success' => false , 'msg' => '회원정보가 없습니다.[empty MemberKey]');
    }

    $arrayParams['pm_inid']             = $arrayParams['pm_inid']?$arrayParams['pm_inid']:$cpid;
    $arrayParams['pm_member_authno']    = $arrayParams['pm_member_authno']?$arrayParams['pm_member_authno']:'';
    $arrayParams['pm_startdate']        = $arrayParams['pm_startdate']?$arrayParams['pm_startdate']:'';
    $arrayParams['pm_enddate']          = $arrayParams['pm_enddate']?$arrayParams['pm_enddate']:'';
    $arrayParams['pm_active_yn']        = $arrayParams['pm_active_yn']?$arrayParams['pm_active_yn']:'Y';
    $arrayParams['pm_use_yn']           = $arrayParams['pm_use_yn']?$arrayParams['pm_use_yn']:'N';
    $arrayParams['pm_rel_key']          = $arrayParams['pm_rel_key']?$arrayParams['pm_rel_key']:'';
    $arrayParams['pm_expire_yn']        = $arrayParams['pm_expire_yn']?$arrayParams['pm_expire_yn']:'N';
    $arrayParams['pm_writer']           = $arrayParams['pm_writer']?$arrayParams['pm_writer']:'';
    $arrayParams['pm_last_type']        = $arrayParams['pm_last_type']?$arrayParams['pm_last_type']:'';

    $sql = "
        INSERT INTO point_member
        SET
          pm_inid           = '{$arrayParams['pm_inid']}'
        , pm_member_key     = '{$arrayParams['pm_member_key']}'
        , pm_member_authno  = '{$arrayParams['pm_member_authno']}'
        , pm_point_id       = '{$arrayParams['pm_point_id']}'
        , pm_points         = '{$arrayParams['pm_points']}'
        , pm_org_points     = '{$arrayParams['pm_org_points']}'
        , pm_startdate      = '{$arrayParams['pm_startdate']}'
        , pm_enddate        = '{$arrayParams['pm_enddate']}'
        , pm_active_yn      = '{$arrayParams['pm_active_yn']}'
        , pm_use_yn         = '{$arrayParams['pm_use_yn']}'
        , pm_rest_points    = '{$arrayParams['pm_rest_points']}'
        , pm_rel_key        = '{$arrayParams['pm_rel_key']}'
        , pm_expire_yn      = '{$arrayParams['pm_expire_yn']}'
        , pm_writer         = '{$arrayParams['pm_writer']}'
        , pm_last_type      = '{$arrayParams['pm_last_type']}'
        
        , pm_regdate        = NOW()
        , pm_moddate        = NOW()
        , pm_date           = DATE_FORMAT(NOW(),'%Y-%m-%d')
    ";

    $bRet = $_09sns->query($sql);

    if($bRet == false){
        return array('success' => false , 'msg' => '쿠폰등록 중 문제가 발생하였습니다.[DB]');
    }else{
        return array('success' => true , 'msg' => '');
    }

}

function update_09Point($arrayParams,$seq){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $query      = "SELECT * FROM point_member WHERE pm_uid = '{$seq}' ; ";
    $oRet       = $_09sns->query($query);
    $aPointInfo = $oRet->row_array();
    $oRet->free_result();

    if(empty($aPointInfo) == true){
        return array('success' => false , 'msg' => '변경할 적립금 정보가 없습니다.[empty PointMemberData]');
    }

    $arrayParams['pm_moddate'] = date('Y-m-d H:i:s');
    $arrayParams['pm_date'] = date('Y-m-d');

    $_09sns->where('pm_uid', $seq);
    $bRet= $_09sns->update('point_member', $arrayParams);

    if($bRet == false){
        return array('success' => false , 'msg' => '쿠폰수정 중 문제가 발생하였습니다.[DB]');
    }else{
        return array('success' => true , 'msg' => '');
    }
}

function delete_09Point($seq){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $query      = "SELECT * FROM point_member WHERE pm_uid = '{$seq}' ; ";
    $oRet       = $_09sns->query($query);
    $aPointInfo = $oRet->row_array();
    $oRet->free_result();

    if(empty($aPointInfo) == true){
        return array('success' => false , 'msg' => '삭제할 적립금 정보가 없습니다.[empty PointMemberData]');
    }

    $_09sns->where('pm_uid', $seq);

    $bRet= $_09sns->delete('point_member');

    if($bRet == false){
        return array('success' => false , 'msg' => '쿠폰삭제 중 문제가 발생하였습니다.[DB]');
    }else{
        return array('success' => true , 'msg' => '');
    }

}

/**
 * 특정 태그 제거 정규식 dhkim
 * $str : 대상문자열
 * $re_tags : 제거 대상 태그 array타입
 */
function remove_tag($str, $re_tags) {

    foreach ($re_tags as $key => $val) {
        $str = preg_replace("/<{$val}[^>]*>/i", '', $str);
        $str = preg_replace("/<\/{$val}>/i", '', $str);
    }

    return $str;
}


/**
 * @date 190411
 * @modify 황기석
 * @desc 푸시를 통한 자동적립금 처리 함수
 */
function save_point_push(){

    $CI          =& get_instance();
    $aPointInput = array(
        'point_authkey' => $CI->input->get_post('point_authkey')
    ,   'ap_num'        => $CI->input->get_post('ap_num')
    ,   'm_num'         => $_SESSION['session_m_num']
    );

    $aInsertPointInfo   = array();
    $addQueryString     = '';
    $curr_date          = current_date();

    if(empty($aPointInput['point_authkey']) == true) return $aInsertPointInfo;
    //if($CI->input->get_post('test_push') != 'Y') $addQueryString .= " AND LEFT(ap_reserve_datetime,8) = '{$curr_date}' ";
    if( zsDebug() == false ) $addQueryString .= " AND LEFT(ap_reserve_datetime,8) = '{$curr_date}' ";

    $sql     = " SELECT * FROM app_push_tb WHERE ap_point_authkey = '{$aPointInput['point_authkey']}' AND ap_push_type = 'point' {$addQueryString}; ";
    $oResult = $CI->db->query($sql);
    $aResult = $oResult->row_array();

    $aPointInput['pt_uid'] = $aResult['ap_ptuid'];



    if(     empty($aPointInput['pt_uid']) == false
        &&  empty($aPointInput['ap_num']) == false
        &&  empty($aPointInput['m_num']) == false
    ){

        $sql = "SELECT COUNT(*) AS cnt 
                FROM point_save_log_tb 
                WHERE sl_apnum = '{$aPointInput['ap_num']}' 
                AND sl_mnum = '{$aPointInput['m_num']}'
                AND sl_code = 'push' ; 
        ";


        $oResult = $CI->db->query($sql);
        $aResult = $oResult->row_array();
        $oResult->free_result();

        if($aResult['cnt'] < 1){ //발급전

            //적립log
            $sql = "INSERT INTO point_save_log_tb 
                    SET
                      sl_apnum    = '{$aPointInput['ap_num']}'
                    , sl_code     = 'push'
                    , sl_authkey  = '{$aPointInput['point_authkey']}'
                    , sl_mnum     = '{$aPointInput['m_num']}'
                    , sl_ptuid    = '{$aPointInput['pt_uid']}'
                    , reg_date    = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
            ";
            $this->db->query($sql);
            //적립log End

            //--적립 proc
            $aPointInfo = get09PointInfo(array('pt_uid' => $aPointInput['pt_uid']));

            $setPointInput = array(
                'pm_member_key'     => $_SESSION['session_m_key']
            ,   'pm_point_id'       => $aPointInfo['pt_uid']
            ,   'pm_points'         => $aPointInfo['pt_issue_value']
            ,   'pm_org_points'     => $aPointInfo['pt_issue_value']
            ,   'pm_rest_points'    => $aPointInfo['pt_issue_value']
            ,   'pm_last_type'      => 'E'
            ,   'pm_startdate'      => getStartdate()
            ,   'pm_enddate'        => getEnddate($aPointInfo['pt_period'])
            );
            $resp = insert_09Point($setPointInput);
            //--적립 proc End

            if($resp['success'] == false){
                log_message('zs','save_point_push :: insert_09Point err :: '.json_encode_no_slashes($setPointInput));
            }else{
                $add_day                = $aPointInfo['pt_period']+1;
                $aPointInfo['set_date'] = date('m/d/Y ', strtotime("+{$add_day} days")) . "00:00 AM";
                $aInsertPointInfo       = $aPointInfo;
            }

        }

    }else{
        unset($aPointInput);
    }

    return $aInsertPointInfo;

}

/**
 * 웹/앱 조건별 상품 가격정보
 * dhkim 20190429
 * @param $row => product_tb
 * @return integer
 */

function get_product_price($row)
{
    // 표시될 가격
    if( is_app() && $row->p_app_price_yn == "Y" && !empty($row->p_app_price) ) { //앱혜택가
        $sale_price = $row->p_app_price;
    }else if( $_SESSION['nv_shop'] == 'Y' && is_app() == false && $row->p_price_third_yn == "Y" && !empty($row->p_price_third) ) { //3차판매가
        $sale_price = $row->p_price_third;
    }else if( is_app() == false && $row->p_price_second_yn == "Y" && !empty($row->p_price_second) ) { //2차판매가
        $sale_price = $row->p_price_second;
    } else {
        $sale_price = $row->p_sale_price;
    }

    return $sale_price;
}

/**
 * 프로필 이미지 가져오기
 * dhkim 20190516
 * @param $row => member_tb
 * @return string
 */
function get_profile_img($row)
{
    if ($row['m_profile_img']) {
        $member_img_path = HOMEPATH . $row['m_profile_img'];
        $img_data = file_get_contents($member_img_path);
        $member_img_data = "data:image/jpeg;base64," . base64_encode($img_data);

        return $member_img_data;
    }
}

/**
 * Snoopy Library, Web Scrapping
 * dhkim 20190527
 * @param $url => web url
 * @return object
 */
function get_scrap($url)
{
    $CI =& get_instance();

    $CI->load->library('Snoopy');
    $CI->snoopy->fetch($url);

    return $CI->snoopy->results;

}

/**
 * 회원 이름 구하기
 * @param $member_row
 * @return mixed|string
 */
function get_member_name($name_arr) {
    $name = "none name";

    if( isset($name_arr['re_name']) && !empty($name_arr['re_name']) ) {
        $name = $name_arr['re_name'];
    }
    else if( isset($name_arr['cmt_name']) && !empty($name_arr['cmt_name']) ) {
        $name = $name_arr['cmt_name'];
    }
    else if( isset($name_arr['m_nickname']) && !empty($name_arr['m_nickname']) ) {
        $name = $name_arr['m_nickname'];
    }
    else if( isset($name_arr['m_loginid']) && !empty($name_arr['m_loginid']) ) {
        $name = $name_arr['m_loginid'];
    }
    else if( isset($name_arr['m_email']) && !empty($name_arr['m_email']) ) {
        $email_arr = explode("@", $name_arr['m_email']);
        $name = substr($email_arr[0], 0, -2) . "**";
    }

    return $name;
}//end get_member_name;


/**
 * 품절 레이어 html
 * @returns {string}
 */
function soldout_html($__boolean){

    $in_class = ' ';
    if($__boolean == true){
        $in_class = ' circle';
    }


    $html  = "";
    $html .= "<div class='laySoldOut {$in_class}'>";
    $html .= "  <div class='tb'><div class='tb-cell middle'>";
    $html .= "  <img class='imgSoldOut' src='".IMG_HTTP."/images/img_sold_out.png' alt='' />";
    $html .= '</div></div>';
    $html .= '</div>';

    return $html;

}

/**
 * PG사에서 리턴받은 카드번호 별표처리
 */
function get_cardno_encrypt($str) {

    $card_no1 = substr($str, 0, 4);
    $card_no2 = str_replace(substr($str, 4, 4), '****', substr($str, 4, 4));
    $card_no3 = substr($str, 8, 4);
    $card_no4 = str_replace(substr($str, 12, 2), '**', substr($str, 12, 4));

    $re_card_no = $card_no1 . ' ' . $card_no2 . ' ' . $card_no3 . ' ' . $card_no4;
    return $re_card_no;
}

/**
 * iconv euckr => utf8
 */
function iconv_euc_utf($str) {

    $conv_str = iconv('euc-kr', 'utf-8', $str);
    return $conv_str;
}

/**
 * iconv utf8 => euckr
 */
function iconv_utf_euc($str) {

    $conv_str = iconv('utf-8', 'euc-kr', $str);
    return $conv_str;
}

/**
 * 앱팝업을 보여줄지 말지 (기기정보, 앱버전 기준) (Y|N)
 * @param $member_row : 회원정보(배열)
 * @return string
 */
function get_app_popup_chk($member_row) {
    $m_os_version_exp = explode(".", $member_row->m_os_version);    //기기 OS 버전
    $m_app_version_code = $member_row->m_app_version_code;          //앱버전코드

    //팝업사용여부(Y/N)
    if( zsDebug() ) {
        $is_pop_chk = 'Y';
    }
    else {
        $is_pop_chk = 'Y';
    }

    //예전버전에서는 android 5미만에서 팝업 오류있음
    //if ($m_os_version_exp[0] < 5 && $m_app_version_code < 72) {
    //    $is_pop_chk = 'N';
    //}
    //android 5 미만에서는 팝업 출력안함
    if ($m_os_version_exp[0] < 5) {
        $is_pop_chk = 'N';
    }
    ////적립금 오픈 팝업은 73 버전이 아닐때 안 보여줌
    //if( $m_app_version_code < 73 ) {
    //    $is_pop_chk = "N";
    //}

    //안드로이드 앱버전코드 77 미만 일때만 팝업 숨김. (그 이하에서는 팝업 웹뷰 버전이라 오류있음)
    if( is_app_2() && $m_app_version_code < 77 ) {
        $is_pop_chk = "N";
    }
    //IOS 앱버전코드 70 미만일때 팝업 숨김
    //if( stristr($_SERVER['HTTP_USER_AGENT'], 'iOS') !== false ) {
    //    $is_pop_chk = "N";
    //}
    if( is_app_1() && $m_app_version_code < 70 ) {
        $is_pop_chk = "N";
    }

    return $is_pop_chk;
}//end get_app_popup_chk;



function click_counter_09($arrayParams){

    if( empty($arrayParams['kwd']) == true ) return false;

    $CI                  =& get_instance();
    $arrayParams['inid'] = $CI->config->item("order_cpid");
    $_09sns              = $CI->load->database("09sns", true); //conn 09sns

    $sql = "
        INSERT INTO smart_click
        SET {$arrayParams['kwd']} = 1
        , 	inid = '{$arrayParams['inid']}'
        , 	c_date = DATE_FORMAT(NOW(),'%Y%m%d')
        ON DUPLICATE KEY 
        UPDATE {$arrayParams['kwd']} = {$arrayParams['kwd']} + 1
    ";
    $_09sns->query($sql);

}

/**
 * HTTP 비동기 요청 (결과값 리턴 없음)
 * @param $url : URL
 * @param $params : 파라미터(배열 또는 변수명=값&변수명=값) (GET방식도 $params에 넣어야 함)
 * @param string $type : GET|POST
 */
function http_request_async($url, $params="", $type='POST') {
    $post_string = "";
    if( !empty($params) ) {
        if( is_array($params) ) {
            $post_params = array();
            foreach( $params as $key => &$val ) {
                if( is_array($val) ) {
                    $val = implode(',', $val);
                }
                $post_params[] = $key . '=' . urlencode($val);
            }
            $post_string = implode('&', $post_params);
        }
        else {
            $post_string = $params;
        }
    }//endif;

    $parts = parse_url($url);

    if ($parts['scheme'] == 'http') {
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
    }
    else if ($parts['scheme'] == 'https') {
        $fp = fsockopen("ssl://" . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
    }

    if('GET' == $type) {
        $parts['path'] .= '?' . $post_string;
    }

    $out = $type . " " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    if( 'POST' == $type && isset($post_string) && !empty($post_string) ) {
        $out .= $post_string;
    }

    fwrite($fp, $out);
    fclose($fp);
}//end http_request_async;


function product_view_plus($p_code){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $field      = 'cnt_'.$CI->config->item("order_cpid");

    $query = "
        INSERT INTO date_product_view_tb
        SET {$field} = 1
        ,   p_code = '{$p_code}' 
        , 	`date` = DATE_FORMAT(NOW(),'%Y%m%d')
        ON DUPLICATE KEY 
        UPDATE {$field} = {$field} + 1
        
    ";

    $_09sns->query($query);

}


/**
 * @date 190909
 * @modify 황기석
 * @desc 적립금 임의발급 관련 proc
 *
 * @rel_table
 *  point_save_log_tb 발급로그
 *  point_save_tb 발급정보
 *
 * @retrun err_code
 *  000 ==> 정상발급
 *  100 ==> 적립금발급정보없음(옷쟁이들)
 *  200 ==> 적립금중복발급
 *  300 ==> 적립금정보없음(09sns)
 *  400 ==> 앱로그인 여부실패
 */

function issue_event_point(){

    $CI         =& get_instance();

    if( $_SESSION['new_log_chk'] == 'N' ){
        return array('success' => false , 'msg' => '' , 'err_code' => '400');
    }

    $aInput = array(
        'key'       => $CI->input->get_post('point_authkey')
    ,   'm_num'     => $_SESSION['session_m_num']
    ,   'm_key'     => $_SESSION['session_m_key']
    ,   'date'      => current_date()
    );

    $sql = " SELECT * FROM point_save_tb WHERE issue_key = '{$aInput['key']}' ;  ";

    $oResult = $CI->db->query($sql);
    $aResult = $oResult->row_array();
    $oResult->free_result();

    if(empty($aResult) == true){

        return array('success' => false , 'msg' => '' , 'err_code' => '100');

    }else{

        $aInput['type'] = $aResult['issue_type'];
        $aInput['code'] = $aResult['issue_code'];
        $cpid           = $CI->config->item('order_cpid');

        $addWhereQueryString = '';

        //code가 없거나 only_one인경우는 한번만 발급
        if($aInput['type'] == 'everyday'){
            $addWhereQueryString .= " AND reg_date >= '{$aInput['date']}000000' AND reg_date <= '{$aInput['date']}235959' ";
        }

        $sql     = " SELECT COUNT(*) AS cnt FROM point_save_log_tb WHERE sl_mnum = '{$aInput['m_num']}' AND sl_code = '{$aInput['code']}' AND sl_authkey = '{$aInput['key']}' {$addWhereQueryString} ; ";
        $oResult = $CI->db->query($sql);
        $aResult = $oResult->row_array();
        $oResult->free_result();

        if($aResult['cnt'] < 1){

            {//적립금 select

                $_09sns     = $CI->load->database("09sns", true); //conn 09sns
                $sql        = " SELECT * FROM point_master WHERE pt_inids LIKE '%{$cpid}%' AND pt_code LIKE '%{$aInput['code']}%' ";
                $oPointInfo = $_09sns->query($sql);
                $aPointInfo = $oPointInfo->result_array();
                $oPointInfo->free_result();

                $pt_uid_arr = array();
                foreach ($aPointInfo as $r) {
                    $pt_uid_arr[] = $r['pt_uid'];
                }
                shuffle($pt_uid_arr);

                $aInput['pt_uid'] = $pt_uid_arr[0];

            }

            if(empty($aInput['pt_uid']) == true){

                return array('success' => false , 'msg' => '발급 포인트정보가 없습니다.' , 'err_code' => '300');

            }else{

                //적립log
                $sql = "INSERT INTO point_save_log_tb 
                            SET
                              sl_code     = '{$aInput['code']}'
                            , sl_authkey  = '{$aInput['key']}'
                            , sl_mnum     = '{$aInput['m_num']}'
                            , sl_ptuid    = '{$aInput['pt_uid']}'
                            , reg_date    = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                    ";
                $this->db->query($sql);
                //적립log End

                //--적립 proc
                $aPointInfo = get09PointInfo(array('pt_uid' => $aInput['pt_uid']));

                $setPointInput = array(
                    'pm_member_key'     => $aInput['m_key']
                ,   'pm_point_id'       => $aPointInfo['pt_uid']
                ,   'pm_points'         => $aPointInfo['pt_issue_value']
                ,   'pm_org_points'     => $aPointInfo['pt_issue_value']
                ,   'pm_rest_points'    => $aPointInfo['pt_issue_value']
                ,   'pm_last_type'      => 'E'
                ,   'pm_startdate'      => getStartdate()
                ,   'pm_enddate'        => getEnddate($aPointInfo['pt_period'])
                );

                $resp = insert_09Point($setPointInput);
                //--적립 proc End

                if($resp['success'] == false){
                    log_message('zs','save_point_push :: insert_09Point err :: '.json_encode_no_slashes($setPointInput));
                }

                return array('success' => true , 'msg' => '발급완료' , 'err_code' => '000');

            }

        }

        return array('success' => false , 'msg' => '' , 'err_code' => '200');

    }

}

function getSnsformDeliveryLists($arrayParams){

    $CI =& get_instance();

    $api_key = $CI->config->item('form_api_key');
    $req_url = $CI->config->item('form_order_api_url');
    $headers[] = "api_token: {$api_key}";
    $headers[] = "Content-type: application/x-www-form-urlencoded";
    $headers[] = "Cache-Control: no-cache";
    $is_post   = true;

    $postvars = array( 'api_id' => $CI->config->item('form_api_id') );
    $postvars = array_merge($postvars,$arrayParams);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $req_url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postvars));

    $response = curl_exec ($ch);
    curl_close ($ch);
    $delivery_list = json_decode($response,true);

    return $delivery_list['data'];

}

function getSnsformDeliveryInfo($tn){

    $CI =& get_instance();

    $api_key = $CI->config->item('form_api_key');
    $req_url = $CI->config->item('form_order_detail_api_url');
    $headers[] = "api_token: {$api_key}";
    $headers[] = "Content-type: application/x-www-form-urlencoded";
    $headers[] = "Cache-Control: no-cache";
    $is_post   = true;

    $postvars = array( 'api_id' => $CI->config->item('form_api_id') , 'trade_no' => $tn );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $req_url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postvars));

    $response = curl_exec ($ch);
    curl_close ($ch);
    $delivery_row = json_decode($response,true);

    //zsView($delivery_row);

    return $delivery_row['data'];

}
/**
 * @date 20200529
 * @modify 황기석
 * @desc snsform 주문취소 API
 */
function getSnsformOrderCancel($arrayParams){

    $CI =& get_instance();

    $api_key = $CI->config->item('form_api_key');
    $req_url = $CI->config->item('form_order_cancel_api_url');

    $headers[] = "api_token: {$api_key}";
    $headers[] = "Content-type: application/x-www-form-urlencoded";
    $headers[] = "Cache-Control: no-cache";
    $is_post   = true;

    $postvars = array( 'api_id' => $CI->config->item('form_api_id') );
    $postvars = array_merge($arrayParams,$postvars);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $req_url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postvars));

    $response = curl_exec ($ch);

    curl_close ($ch);
    $resp = json_decode($response,true);

    return $resp;

}

/**
 * @date 20200429
 * @modify 황기석
 * @desc 카카오 연결끊기 api
 */
function unlink_kakao($sns_id){

    $CI =& get_instance();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization : KakaoAK ".$CI->config->item('kakao_app_key')['admin']));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, "https://kapi.kakao.com/v1/user/unlink");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "target_id_type=user_id&target_id={$sns_id}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    $resp = json_decode($output, true);
    curl_close($ch);

    return $resp;

}

function chk_soldout_layer($b){

    if($b == false){
        $html  = "";
        $html .= "<div class=\"cart_soldout_wrap\">";
        $html .= "    <span class=\"cart_soldout\">해당 상품은 품절되었습니다.<br><a class=\"zs-cp\">삭제하기</a></span>";
        $html .= "</div>";
        echo $html;
    }
}
