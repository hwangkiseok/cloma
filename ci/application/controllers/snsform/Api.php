<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SNS FORM API 관련 컨트롤러
 */
class Api extends W_Controller
{

    public $allow_ip;

    public function __construct()
    {
        parent::__construct();

//        //주고받을 허용대상 ip
//        $this->allow_ip = $this->config->item('sns_allow_ip');
//        if(in_array($_SERVER['REMOTE_ADDR'],$this->allow_ip) == false){
//            show_404();
//            exit;
//        }

        $this->load->model('snsform_model');

    }//end of __construct()

    /**
     * 메인
     */
    public function index()
    {
        show_404();
    }//end of index()

    public function testProductInfo(){

        $aInput = array(
                "item_no" => "i3412"
            ,	"api_id" => "api_id11asd"
            ,	"api_token" => "api_token"
            ,	"div_code" => "code"
            ,	"api_item_no" => "api_item_no"
            ,	"item_name" => "item_name"
            ,	"sel_payway_cd" => "1"
            ,	"item_price" => "100"
            ,	"item_count" => "50"
        );

        $aInput     = array('data' => json_encode_no_slashes($aInput));

        //$url        = $this->config->item("default_http") . "/snsform/Api/setProductInfo/";
        $url        = "http://112.175.29.130/snsform/Api/setProductInfo/";

        $param      = http_build_query($aInput);

        $resp       = http_post_request($url, $param);

        echo $resp;

    }

    public function testOrderInfo(){

        $aInput1 = array(
            "item_no" => "item_no"
        ,	"trade_no" => "trade_no3"
        ,	"user_id" => "user_id"
        ,	"item_name" => "item_name"
        ,	"payway_cd" => "1"
        ,	"status_cd" => "2"
        ,	"buy_count" => 1
        ,	"buy_amt" => 2000
        ,	"register_date" => current_datetime()
        ,	"buyer_name" => "buyer_name"
        ,	"buyer_hhp" => "buyer_hhp"
        ,	"receiver_address" => "receiver_address"
        ,   "receiver_tel"  => "010-0000-0000"
        );

        $aInput2 = array(
            "item_no" => "item_no"
        ,	"trade_no" => "trade_no4"
        ,	"user_id" => "user_id"
        ,	"item_name" => "item_name"
        ,	"payway_cd" => "1"
        ,	"status_cd" => "3"
        ,	"buy_count" => 1
        ,	"buy_amt" => 3000
        ,	"register_date" => current_datetime()
        ,	"buyer_name" => "buyer_name"
        ,	"buyer_hhp" => "buyer_hhp"
        ,	"receiver_address" => "receiver_address"
        ,   "receiver_tel"  => "010-0000-0000"
        );

        $aInput     = json_encode_no_slashes(array($aInput1,$aInput2));
        $aInput     = array('data' => $aInput);

        //$url        = $this->config->item("default_http") . "/snsform/Api/setOrderInfo/";
        $url        = "http://112.175.29.130/snsform/Api/setOrderInfo/";
        $param      = http_build_query($aInput);

        $resp       = http_post_request($url, $param);

        echo $resp;

    }

    //snsform 상품등록
    public function setProductInfo(){

        $data = $this->input->post('data');
        $aData = json_decode($data,true);

        unset($_POST);

        foreach ($aData as $k => $v) {
            $_POST[$k] = $v;
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "item_no"           => array("field" => "item_no", "label" => "상품번호", "rules" => "required|".$this->default_set_rules )
        ,   "api_id"            => array("field" => "api_id", "label" => "회원아이디", "rules" => "required|".$this->default_set_rules )
        ,   "api_token"         => array("field" => "api_token", "label" => "APIToken", "rules" => "required|".$this->default_set_rules )
        ,   "div_code"          => array("field" => "div_code", "label" => "제휴사구분", "rules" => "required|".$this->default_set_rules )
        ,   "api_item_no"       => array("field" => "api_item_no", "label" => "제휴사상품번호", "rules" => "required|".$this->default_set_rules )
        ,   "item_name"         => array("field" => "item_name", "label" => "상품명", "rules" => "required|".$this->default_set_rules )
        ,   "start_date"        => array("field" => "start_date", "label" => "판매시작일", "rules" => $this->default_set_rules )
        ,   "end_date"          => array("field" => "end_date", "label" => "판매종료일", "rules" => $this->default_set_rules )
        ,   "sel_payway_cd"     => array("field" => "sel_payway_cd", "label" => "결제수단", "rules" => $this->default_set_rules )
        ,   "org_price"         => array("field" => "org_price", "label" => "소비자가", "rules" => $this->default_set_rules )
        ,   "supply_price"      => array("field" => "supply_price", "label" => "공급가", "rules" => $this->default_set_rules )
        ,   "item_price"        => array("field" => "item_price", "label" => "단가(판매가)", "rules" => "required|".$this->default_set_rules )
        ,   "item_count"        => array("field" => "item_count", "label" => "수량", "rules" => "required|".$this->default_set_rules )
        ,   "tax_opt_cd"        => array("field" => "tax_opt_cd", "label" => "과세구분", "rules" => $this->default_set_rules )
        ,   "order_req_yn"      => array("field" => "order_req_yn", "label" => "주문요청", "rules" => $this->default_set_rules )
        ,   "supply_name"       => array("field" => "supply_name", "label" => "공급자", "rules" => $this->default_set_rules )
        ,   "org_area"          => array("field" => "org_area", "label" => "원산지", "rules" => $this->default_set_rules )
        ,   "org_maker"         => array("field" => "org_maker", "label" => "제조사", "rules" => $this->default_set_rules )
        ,   "delivery_yn"       => array("field" => "delivery_yn", "label" => "배송비설정", "rules" => $this->default_set_rules )
        ,   "delivery_amt"      => array("field" => "delivery_amt", "label" => "배송비", "rules" => $this->default_set_rules )
        ,   "notice_use_yn"     => array("field" => "notice_use_yn", "label" => "구매전공지사항", "rules" => $this->default_set_rules )
        ,   "content"           => array("field" => "content", "label" => "공지사항내용", "rules" => $this->default_set_rules )
        ,   "notice_agree_yn"   => array("field" => "notice_agree_yn", "label" => "공지사항동의체크", "rules" => $this->default_set_rules )
        ,   "customs_no_yn"     => array("field" => "customs_no_yn", "label" => "개인통관번호사용여부", "rules" => $this->default_set_rules )
        ,   "receipt_yn"        => array("field" => "receipt_yn", "label" => "현금영수증발급", "rules" => $this->default_set_rules )
        ,   "del_memo_yn"       => array("field" => "del_memo_yn", "label" => "배송메모사용여부", "rules" => $this->default_set_rules )
        ,   "email_open_yn"     => array("field" => "email_open_yn", "label" => "메일주소사용여부", "rules" => $this->default_set_rules )
        ,   "hhp_open_yn"       => array("field" => "hhp_open_yn", "label" => "일반전화사용여부", "rules" => $this->default_set_rules )
        ,   "other_show_yn"     => array("field" => "other_show_yn", "label" => "상품목록노출여부", "rules" => $this->default_set_rules )
        ,   "file_name"         => array("field" => "file_name", "label" => "대표이미지", "rules" => $this->default_set_rules )
        ,   "content2"          => array("field" => "content2", "label" => "상품설명", "rules" => $this->default_set_rules )
        ,   "option_yn"         => array("field" => "option_yn", "label" => "옵션사용여부", "rules" => $this->default_set_rules )
        ,   "option_json"       => array("field" => "option_json", "label" => "옵션내용", "rules" => $this->default_set_rules )
        ,   "option_type"       => array("field" => "option_type", "label" => "옵션종류", "rules" => $this->default_set_rules )
        ,   "option_order"      => array("field" => "option_order", "label" => "정렬순서", "rules" => $this->default_set_rules )
        ,   "option_title1"     => array("field" => "option_title1", "label" => "1차옵션타이틀", "rules" => $this->default_set_rules )
        ,   "option_title2"     => array("field" => "option_title2", "label" => "2차옵션타이틀", "rules" => $this->default_set_rules )
        ,   "option_title3"     => array("field" => "option_title3", "label" => "3차옵션타이틀", "rules" => $this->default_set_rules )
        );

        $this->form_validation->set_rules($set_rules_array);

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $aSnsformProductInfo = array();
            foreach ($_POST as $key => $v) {
                $aSnsformProductInfo[$key] = trim($this->input->post($key, true));
            }

            $item_no = $aSnsformProductInfo['item_no'];

            if( $this->snsform_model->bOverlapProduct($item_no) == true ){ //중복 -> UPDATE

                unset($aSnsformProductInfo['item_no']);

                $bResult = $this->snsform_model->publicUpdate('snsform_product_tb',$aSnsformProductInfo , array('item_no' , $item_no ));

                if($bResult  == false) {
                    log_message('P',"SNSFORM 상품수정 ERR :: PARAMS :: item_no => {$item_no} :: ".json_encode_no_slashes($aSnsformProductInfo));
                    result_echo_json(get_status_code('error') , "수정 실패" , true , "" , array());
                } else {

                    { //default 상품 등록

                        $aInput = array(
                            'p_name'                => $aSnsformProductInfo['item_name']
                        ,   'p_detail'              => 'detail content'
                        ,   'p_display_info'        => 'p_display_info content'
                        ,   'p_termlimit_datetime1' => $aSnsformProductInfo['start_date'].'000000'
                        ,   'p_termlimit_datetime2' => $aSnsformProductInfo['end_date'].'235959'
                            // 필수 입력 정보 확인 및 추가

                            //snsform 상품수정일자필드 ?

                        );

                        $this->snsform_model->publicUpdate('product_tb',$aInput , array('p_order_code' , $item_no ));

                    }


                    result_echo_json(get_status_code('success') , "" , true , "" , array() , $aSnsformProductInfo);
                } ;

            }else{ //중복X -> INSERT

                $bResult = $this->snsform_model->publicInsert('snsform_product_tb',$aSnsformProductInfo);

                if($bResult  == false) {
                    log_message('P',"SNSFORM 상품등록 ERR :: PARAMS :: ".json_encode_no_slashes($aSnsformProductInfo));
                    result_echo_json(get_status_code('error') , "등록 실패" , true , "" , array());
                } else {

                    { //default 상품 등록

                        $aInput = array(
                            'p_order_code'          => $aSnsformProductInfo['item_no']
                        ,   'p_name'                => $aSnsformProductInfo['item_name']
                        ,   'p_detail'              => 'detail content'
                        ,   'p_display_info'        => 'p_display_info content'
                        ,   'p_termlimit_datetime1' => $aSnsformProductInfo['start_date'].'000000'
                        ,   'p_termlimit_datetime2' => $aSnsformProductInfo['end_date'].'235959'
                            // 필수 입력 정보 확인 및 추가

                            //snsform 상품등록일자필드 ?

                        );

                        $this->snsform_model->publicInsert('product_tb',$aInput);

                    }

                    result_echo_json(get_status_code('success') , "" , true , "" , array() , $aSnsformProductInfo);
                } ;

            };

        } else {

            //뷰 출력용 폼 검증 오류메시지 설정
            result_echo_json(get_status_code('error'), strip_tags($this->form_validation->error_string()) , true, "", array());

        }

    } //end of setProductInfo()


    //snsform 주문등록
    public function setOrderInfo(){

        $data = $this->input->post('data');
        $aData = json_decode($data,true);

        $this->load->library('form_validation');

        foreach ($aData as $k => $r) {

            unset($_POST);

            foreach ($r as $kk => $v ) {
                $_POST[$kk] = $v;
            }

            //폼검증 룰 설정
            $set_rules_array = array(
                    "item_no"               => array("field" => "item_no", "label" => "상품번호", "rules" => "required|".$this->default_set_rules )
                ,   "trade_no"              => array("field" => "trade_no", "label" => "주문번호", "rules" => "required|".$this->default_set_rules )
                ,   "user_id"               => array("field" => "user_id", "label" => "판매자id", "rules" => "required|".$this->default_set_rules )
                ,   "item_name"             => array("field" => "item_name", "label" => "주문상품명", "rules" => "required|".$this->default_set_rules )
                ,   "payway_cd"             => array("field" => "payway_cd", "label" => "결제수단", "rules" => "required|".$this->default_set_rules )
                ,   "status_cd"             => array("field" => "status_cd", "label" => "주문상태", "rules" => "required|".$this->default_set_rules )
                ,   "partner_order_no"      => array("field" => "partner_order_no", "label" => "제휴사주문번호", "rules" => $this->default_set_rules )
                ,   "partner_seller_id"     => array("field" => "partner_seller_id", "label" => "제휴사판매자ID", "rules" => $this->default_set_rules )
                ,   "buy_count"             => array("field" => "buy_count", "label" => "주문수량", "rules" => "required|".$this->default_set_rules )
                ,   "buy_amt"               => array("field" => "buy_amt", "label" => "주문금액", "rules" => "required|".$this->default_set_rules )
                ,   "delivery_amt"          => array("field" => "delivery_amt", "label" => "배송비", "rules" => $this->default_set_rules )
                ,   "island_amt"            => array("field" => "island_amt", "label" => "도서산간비", "rules" => $this->default_set_rules )
                ,   "register_date"         => array("field" => "register_date", "label" => "주문일", "rules" => "required|".$this->default_set_rules )
                ,   "status_date"           => array("field" => "status_date", "label" => "상태변경일", "rules" => $this->default_set_rules )
                ,   "check_date"            => array("field" => "check_date", "label" => "입금확인일", "rules" => $this->default_set_rules )
                ,   "cancel_date"           => array("field" => "cancel_date", "label" => "주문취소일", "rules" => $this->default_set_rules )
                ,   "buyer_name"            => array("field" => "buyer_name", "label" => "주문자명", "rules" => "required|".$this->default_set_rules )
                ,   "buyer_hhp"             => array("field" => "buyer_hhp", "label" => "주문자연락처", "rules" => "required|".$this->default_set_rules )
                ,   "receiver_tel"          => array("field" => "receiver_tel", "label" => "수령자연락처", "rules" => "required|".$this->default_set_rules )
                ,   "receiver_address"      => array("field" => "receiver_address", "label" => "수령주소", "rules" => "required|".$this->default_set_rules )
                ,   "receiver_memo"         => array("field" => "receiver_memo", "label" => "배송메모", "rules" => $this->default_set_rules )
                ,   "order_memo"            => array("field" => "order_memo", "label" => "주문시요청사항", "rules" => $this->default_set_rules )
                ,   "supply_name"           => array("field" => "supply_name", "label" => "공급자", "rules" => $this->default_set_rules )
                ,   "supply_amt"            => array("field" => "supply_amt", "label" => "공급가", "rules" => $this->default_set_rules )
                ,   "customs_no"            => array("field" => "customs_no", "label" => "개인통관번호", "rules" => $this->default_set_rules )
            );

            $this->form_validation->set_rules($set_rules_array);

            //폼 검증 성공시
            if( $this->form_validation->run() === true ) {

                $aSnsformOrderInfo = array();

                foreach ($_POST as $key => $v) {
                    $aSnsformOrderInfo[$key] = trim($this->input->post($key, true));
                }

                $trade_no = $aSnsformOrderInfo['trade_no'];

                if( $this->snsform_model->bOverlapOrder($trade_no) == true ) { //중복있음

                    //상태 변경에 대한 데이터 insert 추가
                    if(empty($aSnsformOrderInfo['status_cd']) == false) setOrderStatusLog($trade_no,$aSnsformOrderInfo['status_cd']);


                    unset($aSnsformOrderInfo['trade_no']);
                    unset($aSnsformOrderInfo['register_date']);
                    $bResult = $this->snsform_model->publicUpdate('snsform_order_tb' , $aSnsformOrderInfo , array('trade_no' , $trade_no )  ) ;

                }else{ //중복없음
                    //상태 변경에 대한 데이터 insert 추가
                    setOrderStatusLog($trade_no,$aSnsformOrderInfo['status_cd']);

                    $bResult = $this->snsform_model->publicInsert('snsform_order_tb',$aSnsformOrderInfo) ;

                }

                if( $bResult == false ) {
                    log_message('P',"SNSFORM 주문 UPSERT ERR :: PARAMS :: ".json_encode_no_slashes($aSnsformOrderInfo));
                    result_echo_json(get_status_code('error') , "실패" , true , "" , array());
                } ;

            } else {

                result_echo_json(get_status_code('error'), strip_tags($this->form_validation->error_string()) , true, "", array());

            }

        }

        result_echo_json(get_status_code('success') , "" , true , "" , array() );

    } //end of setProductInfo()

}//end of class Api


