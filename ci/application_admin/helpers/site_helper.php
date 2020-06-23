<?php
/**
 * 사이트 공통 헬퍼
 */

//=========================================================== admin
/**
 * 로그인 상태
 */
function adminuser_login_status() {
    $CI =& get_instance();

    if( $_SESSION['session_au_num'] ) {
        return true;
    }
    else {
        return false;
    }
}//end of adminuser_login_status()

/**
 * 로그인 체크 (페이지 이동)
 */
function adminuser_login_check() {
    $CI =& get_instance();

    if( !adminuser_login_status() ) {
        if( $CI->input->is_ajax_request() ) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }
        else {
            redirect('/auth/login');
        }
    }//end of if()
}//end of adminuser_login_check()

/**
 * 관리자 계정 전체 권한 확인
 */
function is_adminuser_high_auth() {
    $CI =& get_instance();

    adminuser_login_check();

    $adminuser_level = array_search("전체", $CI->config->item('adminuser_level'));

    if( $_SESSION['session_au_level'] >= $adminuser_level ) {
        return true;
    }
    else {
        return false;
    }
}//end of is_adminuser_high_auth()
//=========================================================== /admin

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
    //$sess_id = md5($mic_arr[1] . $mic . $rand_v);
    $sess_id = hash('sha256', $mic_arr[1] . $mic . $rand_v, false);

    return $sess_id;
}//end of create_session_id()


/**
 * 경고메세지 출력후 앱액티비티를 닫음
 */
function alert_app_winclose($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); appWinClose('Y'); </script>";
    exit;
}//end of alert_close()

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
        echo "history.go(-1);";
    }

    echo "</script>";
    exit;
}//end of alert()

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

//
/**
 * 경고메세지만 출력
 * @param string $msg	: 출력할 경고메시지
 * @return string
 */
function alert_only($msg) {
    $CI =& get_instance();

    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$CI->config->item('charset')."\">";
    echo "<script type='text/javascript'> alert('".$msg."'); </script>";
    exit;
}//end of alert_only()
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
 * @param       $status			: 상태코드 (000=성공, 100=로그인페이지로이동, 200=오류)
 * @param       $message		: 메시지
 * @param bool  $exit			: echo 후 종료 여부 (true=종료, false)
 * @param       $message_type	: 메시지 출력 타입(alert=alert창으로 출력함)
 * @param array $error_data		: 오류 데이터 (배열) (키:값)
 * @param array $data			: 리턴 데이터 (json형태)
 */
function result_echo_json($status, $message="", $exit=false, $message_type="", $error_data=array(), $data=array()) {
    if( empty($status) ) {
        $status = "200";
    }

    $result_array = array();
    $result_array['status'] = $status;
    $result_array['message'] = $message;
    $result_array['message_type'] = $message_type;
    $result_array['error_data'] = $error_data;
    $result_array['data'] = $data;

    echo json_encode($result_array);

    if( $exit ) {
        exit;
    }
}//end of result_echo_json()


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
 * Ymd(His)에서 시간(H) 추출
 * @param $date
 * @return string
 */
function get_hour_format($date) {
    if ($date == "") {
        return "";
    }

    $date = number_only($date);

    if( strlen($date) < 10 ) {
        return "";
    }

    return substr($date, 8, 2);
}//end of get_hour_format()

/**
 * Ymd(His)에서 분(i) 추출
 * @param $date
 * @return string
 */
function get_min_format($date) {
    if ($date == "") {
        return "";
    }

    $date = number_only($date);

    if( strlen($date) < 12 ) {
        return "";
    }

    return substr($date, 10, 2);
}//end of get_min_format()

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
        if( $CI->config->item($item_name . '_text_color') ) {
            $text = '<span style="color:' . $CI->config->item($value, $item_name . '_text_color') . ';">' . $text . '</span>';
        }
    }

    return $text;
}//end of get_config_item_text()


//====================================================== HTML 관련
/**
 * 배열을 SELECT OPTION 태그로 변환
 * @param $blank_text           : 빈값 텍스트
 * @param $option_array         : option 으로 변환될 배열 (키=>값 형태)
 * @param $selected_value       : 선택값
 * @param array $exclude_array  : $option_array 에서 제외할 키값들 (배열)
 * @return string
 */
function get_select_option($blank_text, $option_array, $selected_value, $exclude_array=array()) {
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
 * <- $name                     : input name
 * <- $radio_array              : radio 로 변활될 배열 (키=>값 형태)
 * <- $checked_value            : 선택값
 * <- $radio_text_color_array   : label 텍스트 색상값 배열 (키=>색상코드 형태)
 * <- $exclude_array            : $radio_array 에서 제외할 키값
 * <- $begin_tag                : $bengin_tag <input ...><label></label> $end_tag
 * <- $end_tag                  : $bengin_tag <input ...><label></label> $end_tag
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
    echo "<script type='text/javascript'>document.location.replace('" . $href . "');</script>";
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
 * $dir		=> 디렉터리명(절대경로)
 * $mode	=> 권한(예:0775(rwx))(8진수여야 함)
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

function get_select_option_ym($blank_text, $selected_value="", $start_ym="", $end_ym="", $order_type="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    if( empty($start_ym) ) {
        if( $order_type == "desc" ) $start_ym = date("Ym", strtotime("first day of +1 months"));
        else                        $start_ym = date("Ym", strtotime("first day of -1 months"));
    }
    if( empty($end_ym) ) {
        if( $order_type == "desc" ) $end_ym = date("Ym", strtotime("first day of -1 months"));
        else                        $end_ym = date("Ym", strtotime("first day of +1 months"));
    }

    //var_dump($start_ym, $end_ym);
    //exit;

    //내림차순
    if( $order_type == 'desc' ) {
        for( $i=$start_ym; $i >= $end_ym; ) {
            $option_text .= '<option value="' . $i . '"';
            if( $i == trim($selected_value) ) {
                $option_text .= ' selected="selected"';
            }
            $option_text .= '>' . substr($i, 0, 4) . '년 ' . substr($i, 4, 2) . '월</option>';

            $i = date("Ym", strtotime("first day of -1 months", strtotime($i . "01")));
        }//end of for()
    }
    //오름차순
    else {
        for( $i=$start_ym; $i <= $end_ym; ) {
            $option_text .= '<option value="' . $i . '"';
            if( $i == trim($selected_value) ) {
                $option_text .= ' selected="selected"';
            }
            $option_text .= '>' . substr($i, 0, 4) . '년 ' . substr($i, 4, 2) . '월</option>';

            $i = date("Ym", strtotime("first day of +1 months", strtotime($i . "01")));
        }//end of for()
    }

    return $option_text;
}//end of get_select_option_ym()

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
 * @param $blank_text
 * @param string $selected_value
 * @return string
 */
function get_select_option_day($blank_text, $selected_value="", $add_zero=true) {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    for( $i=1; $i <= 31; $i++ ) {
        if( $add_zero === true ) {
            $day = sprintf("%02d", $i);
        }
        else {
            $day = $i;
        }

        $option_text .= '<option value="' . $day . '"';
        if( $day == trim($selected_value) ) {
            $option_text .= ' selected="selected"';
        }
        $option_text .= '>' . $day . '</option>';
    }

    return $option_text;
}//end of get_select_option_day()

/**
 * 시 option 값 출력
 * @param $blank_text
 * @param string $selected_value
 * @return string
 */
function get_select_option_hour($blank_text, $selected_value="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    for( $i=0; $i <= 23; $i++ ) {
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
 * 분 option 값 출력
 * @param $blank_text
 * @param string $selected_value
 * @return string
 */
function get_select_option_min($blank_text, $selected_value="") {
    $option_text = "";

    if( $blank_text != "" ) {
        $option_text .= '<option value="">' . $blank_text . '</option>';
    }

    for( $i=0; $i <= 59; $i++ ) {
        $day = sprintf("%02d", $i);

        $option_text .= '<option value="' . $day . '"';
        if( $day == trim($selected_value) ) {
            $option_text .= ' selected="selected"';
        }
        $option_text .= '>' . $day . '</option>';
    }

    return $option_text;
}//end of get_select_option_min()

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
 * img 출력
 * @param $data : 경로 | 경로배열
 * @param int $key : 출력할 이미지의 키값
 * @param string $width : 이미지 width
 * @param string $height : 이미지 height
 * @param string $etc_attr
 * @return string
 */
function create_img_tag($data, $key=0, $width="", $height="", $etc_attr="", $base64_output=false) {
    if( empty($data) ) {
        return "";
    }

    if( is_array($data) ) {
        $src = $data[$key];
        $org_src = $data[0];
    }
    else {
        $src = $data;
        $org_src = $src;
    }

    if( $base64_output === true ) {
        $src_path = HOMEPATH . $src;

        if( file_exists($src_path) ) {
            $img_data = file_get_contents($src_path);
            $src = "data:image/jpeg;base64," . base64_encode($img_data);
        }
    }

    $html = '';
    $html .= '<a href="#none" onclick="new_win_open(\'' . $org_src . '\', \'img_pop\', 800, 600);"><img src="' . $src . '" ';
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
}//end of create_img_tag()


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
 * 파일 쓰기
 * @param $file_path    : 파일경로(절대경로)
 * @param $file_data    : 파일 데이터
 * @param int $chmod    : 권한(기본:0775)
 * @return bool
 */
function file_write($file_path, $file_data, $chmod=0775) {
    if( empty($file_path) ) {
        return false;
    }

    $path_parts = pathinfo($file_path);
    create_directory($path_parts['dirname'], $chmod);

    $fh = fopen($file_path, "w");

    if( @fwrite($fh, $file_data) ) {
        @chmod($file_path, $chmod);
        @fclose($fh);

        return true;
    }
    else {
        @fclose($fh);
        return false;
    }
}//end of file_write()


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
 * SEED 암호화된 회원 데이터 추출
 * @param $m_key
 * @return mixed|string
 */
function get_seed_member_data($m_key) {
    $str = current_mstime() . "|".$m_key."|09SNS";
    return seed_encrypt($str, true);
}//end of get_seed_member_data()


/**
 * 주문배송조회 링크
 * @param $m_key
 * @return string
 */
function get_order_list_link($m_key) {
    $CI =& get_instance();
    return $CI->config->item('order_list_url') . "&mdata=" . get_seed_member_data($m_key);
}//end ofget_order_list_link()

/**
 * 주문서 링크
 * @param $pcode
 * @return string
 */
function get_order_link($pcode) {
    $CI =& get_instance();
    $url = $CI->config->item('order_link_head') . $pcode;
    return $url;
}//end of get_order_link()

///**
// * 매일응모 종료일시 구하기
// * @param $p_termlimit_datetime2 : 상품판매종료일시(YmdHis)
// * @param bool $getTime
// * @return bool|string
// */
//function get_everyday_enddate($p_termlimit_datetime2, $getTime=false) {
//    if( empty($p_termlimit_datetime2) ) {
//        return false;
//    }
//
//    $CI =& get_instance();
//
//    if( $getTime === true ) {
//        return date("YmdHis", strtotime("-" . $CI->config->item('everyday_winner_day') . " days", strtotime($p_termlimit_datetime2)));
//    }
//    else {
//        return date("Ymd", strtotime("-" . $CI->config->item('everyday_winner_day') . " days", strtotime($p_termlimit_datetime2)));
//    }
//}//end of get_everyday_enddate()

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

    //model
    $CI->load->model('total_stat_model');

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



function get_bitly_shorturl_info($url){

    $CI =& get_instance();

    $url    = str_replace('https://','',$url);
    $param  = array( 'bitlink_id' => $url );

    $param_j = json_encode_no_slashes($param);

    $req_url = "https://api-ssl.bitly.com/v4/expand";
    $ACCESS_TOKEN = $CI->config->item('bitly_token');

    $ch = curl_init();
    $arr = array();
    array_push($arr, "Content-Type: application/json; charset=utf-8");
    array_push($arr, "Authorization: Bearer {$ACCESS_TOKEN}");
    array_push($arr, "Accept: application/json");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
    curl_setopt($ch, CURLOPT_URL,$req_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param_j);

    $output = curl_exec($ch);
    $output = json_decode($output,true);

    curl_close($ch);

    return $output;

}



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
 * 단축URL 에서 긴URL 추출 (bitly)
 * @param $short_url
 * @return string
 */
function get_long_url_bitly($short_url) {
    if( empty($short_url) ) {
        return "";
    }

    $short_url = urlencode($short_url);

    $CI =& get_instance();

    $referer = $CI->config->item('site_http');
    $apiKey = $CI->config->item('bitly_key');
    $apiLogin = $CI->config->item('bitly_login');
    $format = 'json';

    $ch = curl_init();
    $arr = array();
    array_push($arr, "Content-Type: application/json; charset=utf-8");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
    curl_setopt($ch, CURLOPT_URL, "http://api.bit.ly/v3/expand?login=" . $apiLogin . '&apiKey=' . $apiKey . '&shortUrl=' . $short_url . '&format=' . $format);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    if( curl_errno($ch) ) {
        return "";
    }

    $result = json_decode($output, true);
    curl_close($ch);

    if( $result['status_code'] == 200 ) {
        return $result['data']['expand'][0]['long_url'];
    }
    else {
        return '';
    }
}//end of get_long_url_bitly()

/**
 * 단축 URL 에서 긴 URL 추출 (yourls : murl.kr : 자체서버)
 * @param $short_url
 * @return string
 */
function get_long_url_yourls($short_url) {
    if( empty($short_url) ) {
        return "";
    }

    $CI =& get_instance();

    $token = $CI->config->item("yourls_token");
    $timestamp = time();
    $signature = md5($timestamp . $token);
    $api_url =  $CI->config->item("yourls_api_http");

    // Init the CURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
    curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
        'shorturl'  => $short_url,
        'format'    => 'json',
        'action'    => 'expand',
        'timestamp' => $timestamp,
        'signature' => $signature
    ));

    // Fetch and return content
    $data = curl_exec($ch);
    curl_close($ch);

    // Do something with the result. Here, we echo the long URL
    $data = json_decode( $data );
    //var_dump($data);
    return $data->longurl;
}//end get_long_url_yourls;

function send_app_push_log($m_num, $push_data, $isCompl_send = false){

    if(empty($m_num) == true) return false;
    if(empty($push_data['title']) == true ||  empty($push_data['page']) == true ) return false;

    $CI =& get_instance();

    $sql     = "SELECT m_regid FROM member_tb WHERE m_num = '{$m_num}';";
    $oResult = $CI->db->query($sql);
    $aResult = $oResult->row_array();

    //푸시발송
    $resp = send_app_push($aResult['m_regid'],$push_data );

    if($resp['success'] == true || $isCompl_send == true){
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
 * 푸시 발송 (IOS용)
 * @param $regid
 * @param array $push_data : array([title]=>, [msg]=>, [smr]=>, [icon]=>, [img]=>, [tarUrl]=>, [notiType]=>, [badge]=>) 형식
 * @return mixed
 */
function send_app_push_1($regid, $push_data=array()) {
    if( empty($regid) || empty($push_data) ) {
        return false;
    }

    //필수값 체크
    if( empty($push_data['title']) || empty($push_data['msg']) ) {
        return false;
    }

    $CI =& get_instance();

    // 헤더 부분
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $CI->config->item('google_server_key')
    );

    //기본값 설정
    if( !isset($push_data['num']) || empty($push_data['num']) ) {
        $push_data['num'] = current_mstime();
    }
    if( !isset($push_data['smr']) || empty($push_data['smr']) ) {
        $push_data['smr'] = "";
    }
    if( !isset($push_data['icon']) || empty($push_data['icon']) ) {
        $push_data['icon'] = "";
    }
    if( !isset($push_data['img']) || empty($push_data['img']) ) {
        $push_data['img'] = "";
    }
    if( !isset($push_data['notiType']) || empty($push_data['notiType']) ) {
        $push_data['notiType'] = "1";
    }
    //if( !isset($push_data['badge']) || empty($push_data['badge']) ) {
    //    $push_data['badge'] = "1";
    //}
    if( !isset($push_data['style']) || empty($push_data['style']) ) {
        $push_data['style'] = "";
    }

    if( isset($push_data['badge']) && !empty($push_data['badge']) && $push_data['badge'] == "Y" ) {
        $badge = "1";
    }
    else {
        $badge = "0";
    }

    //- registration_ids : array. 1~1000 개의 아이디가 들어갈 수 있다.
    //- collapse_key : message type 을 grouping 하는 녀석으로, 해당 단말이 offline 일 경우 가장 최신 메세지만 전달되는 형태다.
    //- data : key-value pair.
    //- delay_while_idle : message 가 바로 전송되는 것이 아니라, phone 이 active 되었을 때 collapse_key 의 가장 마지막 녀석만 전송되도록. : false => 꺼져(잠겨)있어도 전송, true => 켜지면 전송
    //- time_to_live : 단말이 offline 일 때 GCM storage 에서 얼마나 있어야 하는지를 설정함. collapse_key 와 반드시 함께 설정되야 한다.
    //- dry_run : true=테스트, false=실제발송

    // 푸시 내용, data 부분을 자유롭게 사용해 클라이언트에서 분기할 수 있음.
    $arr = array();
    $arr['notification'] = array();
    $arr['notification']['num'] = $push_data['num'];            //고유번호(기기의 마지막 받은 푸시고유번호과 겹치지 알리지 않음)
    $arr['notification']['title'] = $push_data['title'];        //제목
    $arr['notification']['body'] = $push_data['msg'];            //내용
    $arr['notification']['smr'] = $push_data['smr'];            //요약
    $arr['notification']['icon'] = $push_data['icon'];          //아이콘이미지
    $arr['notification']['img'] = $push_data['img'];            //이미지
    $arr['notification']['style']  = $push_data['style'];
    $arr['notification']['tarUrl'] = $push_data['tarUrl'];      //이동URL
    $arr['notification']['notiType'] = $push_data['notiType'];  //알림타입(1=소리, 2=무음)
    $arr['notification']['badge'] = $badge;                     //뱃지올리기여부(1~|0)
    $arr['collapse_key'] = time() . substr(microtime(), 2, 3);
    $arr['priority'] = "high";
    $arr['content_available'] = true;
    $arr['registration_ids'] = is_array($regid) ? $regid : array($regid);
    //$arr['registration_ids'] = array($regid);
    $arr['dry_run'] = false;                 //true=테스트용, false=서비스용

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
    $response = curl_exec($ch);
    curl_close($ch);

    //log_message('ZS','Push I - '.$response);

    // 푸시 전송 결과 반환.
    $result = json_decode($response, true);

    return $result;
}//end of send_app_push()

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
 * 구글 단축URL 클릭수 가져오기
 */
function get_google_shorturl_click_count($url) {
    if( empty($url) ) {
        return false;
    }
    if( preg_match("/\/goo.gl\//i", $url) === false) {
        return false;
    }

    $CI =& get_instance();

    $tar_url = "https://www.googleapis.com/urlshortener/v1/url?key=" . $CI->config->item('google_web_key') . "&shortUrl=" . $url . "&projection=ANALYTICS_CLICKS";

    //zsView($tar_url);

    $result = @file_get_contents($tar_url);

    //log_message('ZS',$result);

    $result_json = json_decode($result, true);

    if( isset($result_json['analytics']['allTime']['shortUrlClicks']) ) {
        return $result_json['analytics']['allTime']['shortUrlClicks'];
    }

    return "";
}//end of get_google_shorturl_click_count()


/**
 * 구글 단축URL 정보
 */
function get_google_shorturl_info($url) {
    if( empty($url) ) {
        return false;
    }
    if( preg_match("/\/goo.gl\//i", $url) === false) {
        return false;
    }

    $CI =& get_instance();

    $tar_url = "https://www.googleapis.com/urlshortener/v1/url?key=" . $CI->config->item('google_web_key') . "&shortUrl=" . $url . "&projection=ANALYTICS_CLICKS";

    $result = @file_get_contents($tar_url);

    $result_json = json_decode($result, true);

    return $result_json;
}//end of get_google_shorturl_info()


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

function mb_basename($path) {
    $pathinfo_parts = pathinfo($path);
    return $pathinfo_parts['basename'];
}//end of mb_basename()

function utf2euc($str) {
    return iconv("UTF-8","cp949//IGNORE", $str);
}//end of utf2euc()

function is_ie() {
    if( !isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }
    // IE8
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        return true;
    }
    // IE11
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) {
        return true;
    }
    return false;
}//end of is_ie()

/**
 * 이미지 파일인지 아닌지
 * @param $path
 * @return bool
 */
function is_image($path) {
    $a = getimagesize($path);
    $image_type = $a[2];

    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
        return true;
    }
    return false;
}//end of is_image()

/**
 * 테이블 컬럼 존재 여부 체크
 * @param $tb
 * @param $column
 * @return bool
 */
function table_column_check($tb, $column) {
    if( empty($tb) || empty($column) ) {
        return false;
    }

    $CI =& get_instance();

    $query = "show columns from `" . $tb . "` where field = '" . $column . "'";
    $row = $CI->db->query($query)->row();

    if( !empty($row) ) {
        return true;
    }
    else {
        return false;
    }
}//end of table_column_check()

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

/**
 * 회원 프로필 이미지 썸네일 생성 (200 x 200 기준, /files/profile_img 에 저장)
 * @param $url
 * @param $m_num
 * @return string
 */
function create_profile_image_thumb($url, $m_num) {
    $CI =& get_instance();

    if( empty($url) ) {
        return false;
    }

    $file_data = file_get_contents($url);
    if(empty($file_data)){
        return 'null';
    }
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
    else if( $mime_type == "image/x-ms-bmp" ) {
        return 'null-bmp';
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
    //exit;

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

        $dst_w = ceil($img_width * $rule);
        $dst_h = ceil($img_height * $rule);

        $pos_x = (int)(($dst_w - $thumb_w) / 2);
        $pos_y = (int)(($dst_h - $thumb_h) / 2);
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


function getCurrency(){

    $url = 'http://finance.daum.net/exchange/exchangeDetail.daum?code=USD';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $currency = curl_exec($ch);
    curl_close ($ch);

    $rex="/\<dd id=\"hyenCost\"\>\<b\>(.*)\<\/b\>\<\/dd\>/";

    preg_match_all($rex, $currency, $o);
    $temp = substr($o[0][0], 21, 6);

    if (strlen($temp) > 0){
        return $temp;
    }else{
        return -1;
    }

}

/**
 * 디버그 확인
 * @return boolean
 */
function zsDebug(){//서울내
    $aChkIp = array('121.131.27.155','183.96.170.17','221.146.194.182','118.33.75.76','211.217.209.180');
    if(in_array($_SERVER['REMOTE_ADDR'],$aChkIp)){ return true; }else{ return false; }
}
function zsDebug_a(){ //창원&서울공유
    $aChkIp = array('121.131.27.155','220.84.224.106');
    if(in_array($_SERVER['REMOTE_ADDR'],$aChkIp)){ return true; }else{ return false; }
}
function zsView($params,$chkExit = false){
    echo '<xmp>';
    print_r($params);
    echo '</xmp>';
    if($chkExit == true){ exit; }
}
function ph_slice($ph_no){

    $ph_no = str_replace('-','',$ph_no);

    if(strlen($ph_no) <= 10){ // 011-205-2355
        $str = mb_substr($ph_no, 0, 3) . "-" . mb_substr($ph_no, 3, 3) . "-" . mb_substr($ph_no, 6, 4) ;
    }else{ // 011-2055-2355
        $str = mb_substr($ph_no, 0, 3) . "-" . mb_substr($ph_no, 3, 4) . "-" . mb_substr($ph_no, 7, 4) ;
    }
    return $str;
};

function dayDiff($frDt, $toDt){
    $tm1 = strtotime($frDt);
    $tm2 = strtotime($toDt);
    return round(($tm2-$tm1)/(60*60*24));
}

function dayDiff_second($frDt, $toDt){

    $date1 = new DateTime($frDt);
    $date2 = new DateTime($toDt);
    $diff = $date1->diff($date2);

    $ret = '';
    if( $diff->y > 0) $ret .= sprintf("%04d", $diff->y) .'년';
    if( $diff->m > 0) $ret .= sprintf("%02d", $diff->m) .'월';
    if( $diff->d > 0) $ret .= sprintf("%02d", $diff->d) .'일 ';
    $ret .= sprintf("%02d", $diff->h) .':';
    $ret .= sprintf("%02d", $diff->i) .':';
    $ret .= sprintf("%02d", $diff->s);

    return $ret;

}


function view_date_format( $sourceString, $type = 4 ) {

    if($sourceString == '') return '';

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


// 콤마를 제거한다.
function delComma($val) {
    return str_replace(",","",$val);
}

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
 * CDN 이미지 Purge(새로고침)
 * @param $img_url : 이미지 URL(도메인없이)(예: /upload/product/2018/0501/이미지명.jpg)
 * @return bool
 */
function cdn_purge($img_url, $tid="", $domain="") {
    return true;
}//end of cdn_purge()


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
 * 움직이는 GIF인지
 * @param $filename : 절대경로
 * @return bool
 */
function is_ani_pic($filename) {
    if(!($fh = @fopen($filename, 'rb')))
        return false;
    $count = 0;
    //an animated gif contains multiple "frames", with each frame having a
    //header made up of:
    // * a static 4-byte sequence (\x00\x21\xF9\x04)
    // * 4 variable bytes
    // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

    // We read through the file til we reach the end of the file, or we've found
    // at least 2 frame headers
    while(!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024 * 100); //read 100kb at a time
        $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
    }

    fclose($fh);
    return $count > 1;
}//end is_ani_pic;

function strip_encode($val, $strip_chk) {
    if( $strip_chk ) {
        $return_val = rawurldecode($val);
        $return_val = strip_tags($return_val);
    }
    else {
        $return_val = rawurldecode($val);
    }

    return $return_val;
}

/**
 * 연관배열 정렬
 * @param $array : 배열
 * @param $field : 정렬할 키
 * @param string $sort_type : asc=오른차순, desc=내림차순
 * @return array
 */
function array_key_sort($array, $field, $sort_type="desc",$data_data = 'array') {

    $array = (array)$array;

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



    if($data_data == 'object'){
        $return_array = new stdClass();
        foreach ($temp_array as $key => $item) {
            $return_array->$key = $array[$key];
        }
    }else{
        $return_array = array();
        foreach ($temp_array as $key => $item) {
            $return_array[$key] = $array[$key];
        }
    }

    return $return_array;
}//end of array_key_sort()



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

function insert_09Point_arr($arrayParams,$pm_point_id){

    $CI         =& get_instance();
    $_09sns     = $CI->load->database("09sns", true); //conn 09sns
    $cpid       = $CI->config->item('order_cpid');
    $query      = "SELECT * FROM point_master WHERE pt_uid = '{$pm_point_id}' AND pt_inids LIKE '%{$cpid}%' AND pt_use_yn = 'Y' ; ";
    $oRet       = $_09sns->query($query);
    $aPointInfo = $oRet->row_array();
    $oRet->free_result();

    if(empty($aPointInfo) == true){
        return array('success' => false , 'msg' => '적립금정보가 없습니다.[empty MasterData]');
    }

    $curr_date      = date('Y-m-d', time());
    $curr_datetime  = date('Y-m-d H:i:s', time());
    $aInsertValue   = array();

    foreach ($arrayParams as $arrayParam) {

        $arrayParam['pm_inid']             = $arrayParam['pm_inid']?$arrayParam['pm_inid']:$cpid;
        $arrayParam['pm_member_authno']    = $arrayParam['pm_member_authno']?$arrayParam['pm_member_authno']:'';
        $arrayParam['pm_startdate']        = $arrayParam['pm_startdate']?$arrayParam['pm_startdate']:'';
        $arrayParam['pm_enddate']          = $arrayParam['pm_enddate']?$arrayParam['pm_enddate']:'';
        $arrayParam['pm_active_yn']        = $arrayParam['pm_active_yn']?$arrayParam['pm_active_yn']:'Y';
        $arrayParam['pm_use_yn']           = $arrayParam['pm_use_yn']?$arrayParam['pm_use_yn']:'N';
        $arrayParam['pm_rel_key']          = $arrayParam['pm_rel_key']?$arrayParam['pm_rel_key']:'';
        $arrayParam['pm_expire_yn']        = $arrayParam['pm_expire_yn']?$arrayParam['pm_expire_yn']:'N';
        $arrayParam['pm_writer']           = $arrayParam['pm_writer']?$arrayParam['pm_writer']:'';
        $arrayParam['pm_last_type']        = $arrayParam['pm_last_type']?$arrayParam['pm_last_type']:'';

        $aInsertValue[] = " 
            ('{$arrayParam['pm_inid']}'
            ,'{$arrayParam['pm_member_key']}'
            ,'{$arrayParam['pm_member_authno']}'
            ,'{$arrayParam['pm_point_id']}'
            ,'{$arrayParam['pm_points']}'
            ,'{$arrayParam['pm_org_points']}'
            ,'{$arrayParam['pm_startdate']}'
            ,'{$arrayParam['pm_enddate']}'
            ,'{$arrayParam['pm_active_yn']}'
            ,'{$arrayParam['pm_use_yn']}'
            ,'{$arrayParam['pm_rest_points']}'
            ,'{$arrayParam['pm_rel_key']}'
            ,'{$arrayParam['pm_expire_yn']}'
            ,'{$arrayParam['pm_writer']}'
            ,'{$arrayParam['pm_last_type']}'
            ,'{$curr_datetime}'
            ,'{$curr_datetime}'
            ,'{$curr_date}'
            ) 
        ";

    }

    $sInsertValue = implode(',',$aInsertValue);

    $_09sns->trans_begin();

    $sql = "INSERT INTO point_member
            ( pm_inid , pm_member_key, pm_member_authno, pm_point_id , pm_points, pm_org_points , pm_startdate, pm_enddat, pm_active_yn, pm_use_yn , pm_rest_points, pm_rel_key , pm_expire_yn, pm_writer , pm_last_type, pm_regdate, pm_moddate , pm_date )
            VALUES
            {$sInsertValue} ;
    ";
    log_message('zs',$sql);
    $_09sns->query($sql);

    if ($_09sns->trans_status() === FALSE){
        $_09sns->trans_rollback();
        return array('success' => false , 'msg' => '쿠폰등록 중 문제가 발생하였습니다.[DB]');
    } else {
        $_09sns->trans_commit();
        return array('success' => true , 'msg' => '');
    }

}

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

/**
 * utf 8형 글자수 체크 후 잘라주는 함수
 * dhkim 20190710
 */
function strcut_utf8($str, $len, $checkmb=false, $tail='...') {

    preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);

    $m    = $match[0];
    $slen = strlen($str);  // length of source string
    $tlen = strlen($tail); // length of tail string
    $mlen = count($m); // length of matched characters

    if ($slen <= $len) return $str;
    if (!$checkmb && $mlen <= $len) return $str;

    $ret   = array();
    $count = 0;

    for ($i=0; $i < $len; $i++) {
        $count += ($checkmb && strlen($m[$i]) > 1)?2:1;

        if ($count + $tlen > $len) break;
        $ret[] = $m[$i];
    }

    return join('', $ret).$tail;
}

/**
 * 휴대폰번호에 하이픈 붙이기
 * @param $hp
 */
function get_hp_hyphen($hp) {
    $hp = number_only($hp);
    if( empty($hp) ) {
        return "";
    }

    $n1 = substr($hp, 0, 3);
    $n2 = substr($hp, 3, strlen($hp) - 7);
    $n3 = substr($hp, -4);

    return $n1 . "-" . $n2 . "-" . $n3;
}//end get_hp_hyphen;


/**
 * @date 191226
 * @modify 황기석
 * @desc snsform 상태변경 log
 */
function setOrderStatusLog($trade_no , $status_cd){

    if(empty($trade_no) == true || empty($status_cd) == true) return false;

    $CI =& get_instance();

    $sql     = "SELECT * FROM snsform_order_status_log_tb WHERE trade_no = '{$trade_no}' ORDER BY reg_date DESC LIMIT 1 ; ";
    $oResult = $CI->db->query($sql);
    $aResult = $oResult->row_array();

    if($aResult['curr_status'] != $status_cd){

        $sql = "    INSERT snsform_order_status_log_tb
                    SET
                        prev_status = '{$aResult['curr_status']}'
                    ,   curr_status = '{$status_cd}'
                    ,   trade_no = '{$trade_no}'
                    ,   reg_date = DATE_FORMAT(NOW() , '%Y%m%d%H%i%s');   
         ";
        $CI->db->query($sql);

    }

}

