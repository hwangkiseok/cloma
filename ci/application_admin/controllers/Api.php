<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends A_Controller {

    public function __construct() {
        parent::__construct();

        //API 허용 IP 체크
        if( $this->config->item('api_allow_ip') ) {
            if( in_array($this->input->ip_address(), $this->config->item('api_allow_ip')) === false ) {
                exit;
            }
        }

        $this->load->model('snsform_model');

    }//end of __construct()

    public function index() {
        show_404();
    }//end of index()


    //snsform 상품등록
    public function setProductInfo(){

        $data = $this->input->post('data');
        unset($_POST);

        if(empty($data) == true){
            result_echo_json(get_status_code('error') , "필수 입력정보가 없습니다 [data]" , true , "" , array() );
        }

        $arr = json_decode($data,true);

        foreach ($arr as $k => $v) {
            if($k == 'rtn_shop_name') continue;
            if($k == 'option_info') $v = json_encode($v,JSON_UNESCAPED_UNICODE);

//            log_message('A',$k . ' ==> ' .$v);

            $_POST[$k] = $v;
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "item_no"           => array("field" => "item_no", "label" => "상품번호", "rules" => "required|".$this->default_set_rules )
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

        $this->load->helper('string');


        if( $this->config->item('isFormTest') == 'Y' ){
            $img_prefix_path = 'https://pontos.snsform.co.kr:14047';
        }else{
            $img_prefix_path = 'https://snsform.co.kr';
        }

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $aSnsformProductInfo = array();
//            log_message('a','---------------------------------------------------------------------------------------------------------------------------------------------------');
            foreach ($_POST as $key => $v) {

//                log_message('a',$key . ' ==> ' . $v);
                $aSnsformProductInfo[$key] = trim($this->input->post($key, true));
            }

            {//데이터 변환

                $start_date= '';
                $end_date= '';

                if($aSnsformProductInfo['start_date']){
                    $date       = new DateTime($aSnsformProductInfo['start_date']);
                    $start_date = $date->format('YmdHis');
                }
                if($aSnsformProductInfo['end_date']){
                    $date       = new DateTime($aSnsformProductInfo['end_date']);
                    $end_date   = $date->format('YmdHis');
                }

                if(strpos($aSnsformProductInfo['img_url'],'http') !== FALSE) {
                    $img_url = $aSnsformProductInfo['img_url'];
                }else{
                    $img_url = $img_prefix_path.$aSnsformProductInfo['img_url'];
                }

                $chk_str = array();
                $chk_str[] = strtolower('<center><p><a href="http://pf.kakao.com/_KbbpT" target="_blank"><img alt="" src="/web/upload/NNEditor/20200221/채널추가 싸이다_shop1_161835.jpg"></a></p><p><br></p><p><a href="http://pf.kakao.com/_KbbpT/chat" target="_blank"><img alt="" src="/web/upload/NNEditor/20200221/카톡상담원연결_shop1_161955.jpg"></a></p><p><a href="http://pf.kakao.com/_KbbpT/chat" target="_blank"> </a><span xss=removed> </span></p><p></p><p></p><p></p></center>');
                $chk_str[] = strtolower('<div><p></p></div><div><b><span xss=removed><span xss=removed>※[싸이다]채널 추가후 주문하면 [스카프 증정!!]※</span></span></b></div><div><b><span xss=removed><span xss=removed><br></span></span></b></div><div><b><span xss=removed><span xss=removed>-구매후 상담원연결후 이야기 해주시면 누락없이 발송 되세요.-</span></span></b></div>');
                $chk_str[] = strtolower('<p><a href="http://pf.kakao.com/_kbbpt" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/채널추가 싸이다_shop1_161835.jpg"></a></p>');
                $chk_str[] = strtolower('<p><a href="http://pf.kakao.com/_kbbpt/chat" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/카톡상담원연결_shop1_161955.jpg"></a></p>');
                $chk_str[] = strtolower('<p style="text-align: center;"><a href="http://pf.kakao.com/_kbbpt" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/채널추가 싸이다_shop1_161835.jpg" /></a></p>

<p style="text-align: center;">&nbsp;</p>

<p style="text-align: center;"><a href="http://pf.kakao.com/_kbbpt/chat" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/카톡상담원연결_shop1_161955.jpg" /></a></p>

<p style="text-align: center;">&nbsp;</p>');
                $chk_str[] = strtolower('<p style="text-align: center;"><a href="http://pf.kakao.com/_kbbpt" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/채널추가 싸이다_shop1_161835.jpg" /></a></p>

<p style="text-align: center;">&nbsp;</p>

<p style="text-align: center;"><a href="http://pf.kakao.com/_kbbpt/chat" target="_blank"><img alt="" src="/web/upload/nneditor/20200221/카톡상담원연결_shop1_161955.jpg" /></a>');

                $aSnsformProductInfo['contents'] = str_replace($chk_str,'',strtolower($aSnsformProductInfo['contents']));

                $aSnsformProductInfo['item_name'] = str_replace("&#40;",'(',strtolower($aSnsformProductInfo['item_name']));
                $aSnsformProductInfo['item_name'] = str_replace("&#41;",')',strtolower($aSnsformProductInfo['item_name']));

            }

            $item_no = $aSnsformProductInfo['item_no'];
            if( $this->snsform_model->bOverlapProduct($item_no) == true ){ //중복 -> UPDATE

                unset($aSnsformProductInfo['item_no']);

                $bResult = $this->snsform_model->publicUpdate('snsform_product_tb',$aSnsformProductInfo , array('item_no' , $item_no ));

                if($bResult  == false) {
//                    log_message('P',"SNSFORM 상품수정 ERR :: PARAMS :: item_no => {$item_no} :: ".json_encode_no_slashes($aSnsformProductInfo));
                    result_echo_json(get_status_code('error') , "수정 실패" , true , "" , array());
                } else {

                    { //default 상품 등록

                        $aInput = array(
                            'p_name'                => $aSnsformProductInfo['item_name']
                        ,   'p_cate1'               => $aSnsformProductInfo['category_val']
                        ,   'p_detail'              => $aSnsformProductInfo['contents']
                        ,   'p_display_info'        => '{"":"Y"}'
                        ,   'p_termlimit_yn'        => empty($start_date) == false && empty($start_date) == false ? 'Y' : 'N'
                        ,   'p_termlimit_datetime1' => $start_date
                        ,   'p_termlimit_datetime2' => $end_date
                        ,   'p_taxation'            => 1
                        ,   'p_stock_state'         => $aSnsformProductInfo['item_count'] > 0 ? 'Y':'N'
                        ,   'p_today_image'         => $img_url
                        ,   'p_sale_price'          => $aSnsformProductInfo['item_price']
                        ,   'p_supply_price'        => $aSnsformProductInfo['supply_price']
                        ,   'p_margin_price'        => $aSnsformProductInfo['item_price'] - $aSnsformProductInfo['supply_price']
                        ,   'p_margin_rate'         => ( ( $aSnsformProductInfo['item_price'] - $aSnsformProductInfo['supply_price'] ) / $aSnsformProductInfo['item_price'] ) * 100
                        ,   'p_display_state'       => $aSnsformProductInfo['status_cd'] == '01' ? 'Y' : 'N'
                        ,   'p_sale_state'          => $aSnsformProductInfo['other_show_yn']
                        ,   'p_mod_id'              => 999999

                        );

                        if(empty($aSnsformProductInfo['org_price']) == true){

                            $ratio = (int)rand(30,70);
                            $org_price = round($aSnsformProductInfo['item_price'] / ( 1 - ($ratio / 100)));
                            $org_price = substr($org_price,0,-2).'00';
                            $ratio = number_format((($org_price - $aSnsformProductInfo['item_price']) / $org_price) * 100, 2);

                            $aInput['p_original_price'] = $org_price;
                            $aInput['p_discount_rate'] = $ratio;

                        }else{

                            $ratio = number_format((($aSnsformProductInfo['org_price'] - $aSnsformProductInfo['item_price']) / $aSnsformProductInfo['org_price']) * 100, 2);
                            $org_price = $aSnsformProductInfo['org_price'];

                            $aInput['p_original_price'] = $org_price;
                            $aInput['p_discount_rate'] = $ratio;

                        }

                        $this->snsform_model->publicUpdate('product_tb',$aInput , array('p_order_code' , $item_no ));

                    }

                    result_echo_json(get_status_code('success') , "" , true , "" , array() , $aSnsformProductInfo);
                } ;

            }else{ //중복X -> INSERT

                $bResult = $this->snsform_model->publicInsert('snsform_product_tb',$aSnsformProductInfo);

                if($bResult  == false) {
//                    log_message('P',"SNSFORM 상품등록 ERR :: PARAMS :: ".json_encode_no_slashes($aSnsformProductInfo));
                    result_echo_json(get_status_code('error') , "등록 실패" , true , "" , array());
                } else {

                    { //default 상품 등록

                        $aInput = array(
                            'p_order_code'          => $aSnsformProductInfo['item_no']
                        ,   'p_name'                => $aSnsformProductInfo['item_name']
                        ,   'p_cate1'               => $aSnsformProductInfo['category_val']
                        ,   'p_detail'              => $aSnsformProductInfo['contents']
                        ,   'p_display_info'        => '{"":"Y"}'
                        ,   'p_termlimit_datetime1' => $start_date
                        ,   'p_termlimit_datetime2' => $end_date
                        ,   'p_display_state'       => $aSnsformProductInfo['status_cd'] == '01' ? 'Y' : 'N'
                        ,   'p_sale_state'          => $aSnsformProductInfo['other_show_yn']
                        ,   'p_taxation'            => 1
                        ,   'p_stock_state'         => $aSnsformProductInfo['item_count'] > 0 ? 'Y':'N'
                        ,   'p_today_image'         => $img_prefix_path.$aSnsformProductInfo['img_url']
                        ,   'p_sale_price'          => $aSnsformProductInfo['item_price']
                        ,   'p_regdatetime'         => current_datetime()
                        ,   'p_supply_price'        => $aSnsformProductInfo['supply_price']
                        ,   'p_margin_rate'         => ( ( $aSnsformProductInfo['item_price'] - $aSnsformProductInfo['supply_price'] ) / $aSnsformProductInfo['item_price'] ) * 100
                        );

                        if(empty($aSnsformProductInfo['org_price']) == true){

                            $ratio = (int)rand(30,70);
                            $org_price = round($aSnsformProductInfo['item_price'] / ( 1 - ($ratio / 100)));
                            $org_price = substr($org_price,0,-2).'00';
                            $ratio = number_format((($org_price - $aSnsformProductInfo['item_price']) / $org_price) * 100, 2);

                            $aInput['p_original_price'] = $org_price;
                            $aInput['p_discount_rate'] = $ratio;

                        }else{

                            $ratio = number_format((($aSnsformProductInfo['org_price'] - $aSnsformProductInfo['item_price']) / $aSnsformProductInfo['org_price']) * 100, 2);
                            $org_price = $aSnsformProductInfo['org_price'];

                            $aInput['p_original_price'] = $org_price;
                            $aInput['p_discount_rate'] = $ratio;

                        }

                        $this->snsform_model->publicInsert('product_tb',$aInput);

                    }

                    result_echo_json(get_status_code('success') , "" , true , "" , array() , $aSnsformProductInfo);

                } ;

            };

        } else {

//            log_message('A',strip_tags($this->form_validation->error_string()));
            //뷰 출력용 폼 검증 오류메시지 설정
            result_echo_json(get_status_code('error'), strip_tags($this->form_validation->error_string()) , true, "", array());

        }

    } //end of setProductInfo()


    //snsform 주문등록
    public function setOrderInfo(){

        foreach ($_REQUEST as $k => $v) {
            log_message('a',$k. ' ==> ' . $v);
        }

        $data = $this->input->post('data');

        if(empty($data) == true){
            result_echo_json(get_status_code('error') , "필수 입력정보가 없습니다 [data]" , true , "" , array() );
        }

        $aData = json_decode($data,true);
        $this->load->library('form_validation');

        foreach ($aData as $k => $r) {

            unset($_POST);

            foreach ($r as $kk => $v ) {
                if($kk == 'option_list') $v = json_encode($v,JSON_UNESCAPED_UNICODE);

                $_POST[$kk] = $v;
            }

            //폼검증 룰 설정
            $set_rules_array = array(
                "item_no"               => array("field" => "item_no", "label" => "상품번호", "rules" => $this->default_set_rules )
            ,   "item_name"             => array("field" => "item_name", "label" => "상품명", "rules" => $this->default_set_rules )
            ,   "trade_no"              => array("field" => "trade_no", "label" => "주문번호", "rules" => $this->default_set_rules )
            ,   "payway_cd"             => array("field" => "payway_cd", "label" => "결제수단", "rules" => $this->default_set_rules )
            ,   "status_date"           => array("field" => "status_date", "label" => "상태변경일", "rules" => $this->default_set_rules )
            ,   "register_date"         => array("field" => "register_date", "label" => "주문일", "rules" => $this->default_set_rules )
            ,   "buy_amt"               => array("field" => "buy_amt", "label" => "주문금액", "rules" => $this->default_set_rules )
            ,   "status_cd"             => array("field" => "status_cd", "label" => "주문상태", "rules" => $this->default_set_rules )
            ,   "delivery_amt"          => array("field" => "delivery_amt", "label" => "배송비", "rules" => $this->default_set_rules )

            ,   "partner_buyer_id"      => array("field" => "partner_buyer_id", "label" => "", "rules" => $this->default_set_rules )
            ,   "campaign"              => array("field" => "campaign", "label" => "", "rules" => $this->default_set_rules )
            ,   "referer"               => array("field" => "referer", "label" => "", "rules" => $this->default_set_rules )
            ,   "option_list"           => array("field" => "option_list", "label" => "", "rules" => $this->default_set_rules )

                //명세 필요
            ,   "del_comp_cd"           => array("field" => "del_comp_cd", "label" => "", "rules" => $this->default_set_rules )
            ,   "invoice_no"            => array("field" => "invoice_no", "label" => "", "rules" => $this->default_set_rules )
            ,   "opt_cnt"               => array("field" => "opt_cnt", "label" => "", "rules" => $this->default_set_rules )
            ,   "partner_order_no"      => array("field" => "partner_order_no", "label" => "", "rules" => $this->default_set_rules )

            );

            $this->form_validation->set_rules($set_rules_array);

            //폼 검증 성공시
            if( $this->form_validation->run() === true ) {

                $aSnsformOrderInfo = array();

                foreach ($_POST as $key => $v) {
                    $aSnsformOrderInfo[$key] = trim($this->input->post($key, true));
                }

                if(empty($aSnsformOrderInfo['campaign']) == true) $aSnsformOrderInfo['campaign'] = $aSnsformOrderInfo['campaign']=='null'?'':$aSnsformOrderInfo['campaign'];
                if(empty($aSnsformOrderInfo['referer']) == true) $aSnsformOrderInfo['referer'] = $aSnsformOrderInfo['referer']=='null'?'':$aSnsformOrderInfo['referer'];

                $trade_no = $aSnsformOrderInfo['trade_no'];

                if(empty($aSnsformOrderInfo['receiver_tel']) == false) $aSnsformOrderInfo['receiver_tel'] = number_only($aSnsformOrderInfo['receiver_tel']);

                if( $this->snsform_model->bOverlapOrder($trade_no) == true ) { //중복있음

                    //상태 변경에 대한 데이터 insert 추가
                    if(empty($aSnsformOrderInfo['status_cd']) == false) {

                        setOrderStatusLog($trade_no,$aSnsformOrderInfo['status_cd']);

                        if($aSnsformOrderInfo['status_cd'] == 66 || $aSnsformOrderInfo['status_cd'] == 67 || $aSnsformOrderInfo['status_cd'] == 68 ){

                            // - 판매수량 상품정보에 저장
                            self::calc_stock($aSnsformOrderInfo,'p');
                            // - 판매수량 상품정보에 저장

                            //취소경우 local db upsert
                            $aCancelInput = array(
                                    'cancel_gubun'  => 'Z'
                                ,   'proc_flag' => 'Y'
                                ,   'proc_date' => current_datetime()
                                ,   'm_num'     => $aSnsformOrderInfo['partner_buyer_id']
                            );

                            //snsform_order_cancel_tb 등록처리
                            if($aSnsformOrderInfo['status_cd'] == 66){ //주문취소
                                $aCancelInput['after_status_cd'] = 166;
                            }else if($aSnsformOrderInfo['status_cd'] == 67) { //교환
                                $aCancelInput['after_status_cd'] = 167;
                            }else if($aSnsformOrderInfo['status_cd'] == 68) { //반품
                                $aCancelInput['after_status_cd'] = 168;
                            }

                            $sql = "SELECT * FROM snsform_order_cancel_tb WHERE trade_no = '{$trade_no}'; ";
                            $oResult = $this->db->query($sql);
                            $aDbCancelInfo = $oResult->row_array();

                            if(empty($aDbCancelInfo) == false){

                                unset($aCancelInput['cancel_gubun']);
                                $aCancelInput['mod_date'] = current_datetime();
                                $this->snsform_model->publicUpdate('snsform_order_cancel_tb' , $aCancelInput , array('trade_no' , $trade_no) );

                            }else{

                                $aCancelInput['trade_no'] = $trade_no;
                                $aCancelInput['reg_date'] = current_datetime();
                                $this->snsform_model->publicInsert('snsform_order_cancel_tb' , $aCancelInput );
                            }

                        }

                    }

                    unset($aSnsformOrderInfo['trade_no']);
                    unset($aSnsformOrderInfo['register_date']);
                    $bResult = $this->snsform_model->publicUpdate('snsform_order_tb' , $aSnsformOrderInfo , array('trade_no' , $trade_no )  ) ;

                }else{ //중복없음

                    $option_list = json_decode($aSnsformOrderInfo['option_list'],true);

                    // - 판매수량 상품정보에 저장
                    self::calc_stock($aSnsformOrderInfo,'m');
                    // - 판매수량 상품정보에 저장

                    // - 장바구니정보 삭제
                    if( $aSnsformOrderInfo['cart_yn'] == 'Y'){

                        foreach ($option_list as $rrr) {

                            $sql = "SELECT cart_id , set_campaign , set_referer
                                    FROM cart_tb 
                                    WHERE m_num = '{$aSnsformOrderInfo['partner_buyer_id']}' 
                                    AND p_order_code = '{$aSnsformOrderInfo['item_no']}'
                                    AND option_name = '{$rrr['option_name']}' ;
                            ";
                            $oResult = $this->db->query($sql);
                            $aResult = $oResult->row_array();

                            if(empty($aResult) == false){

                                $aSnsformOrderInfo['campaign'] = $aResult['set_campaign'];
                                $aSnsformOrderInfo['referer'] = $aResult['set_referer'];

                                $del_sql = "DELETE FROM cart_tb WHERE cart_id = '{$aResult['cart_id']}' ; ";
                                $this->db->query($del_sql);
                            }else{ //err
                                log_message('A',$sql);
                            }

                        }

                    };
                    // - 장바구니정보 삭제



                    //--------------------------------------------------------------------------- 판매수량 갱신

                    $this->load->model('product_model');
                    $aProductInfo = $this->product_model->get_product_row_code($aSnsformOrderInfo['item_no']);

                    if(empty($aProductInfo) == false){

                        $aInput = array(
                                'p_order_count_twoday'  => (int)$aProductInfo['p_order_count_twoday']+1
                            ,   'p_order_count_week'    => (int)$aProductInfo['p_order_count_week']+1
                            ,   'p_order_count'         => (int)$aProductInfo['p_order_count']+1
                        );

                        $this->snsform_model->publicUpdate('product_tb',$aInput, array('p_num' , $aProductInfo['p_num'])) ;

                    }

                    //--------------------------------------------------------------------------- 판매수량 갱신

                    //--------------------------------------------------------------------------- 주문정보에 있는 이름/연락처 갱신
                    $aInput = array(
                        'm_order_phone'  => $aSnsformOrderInfo['buyer_hhp']
                    ,   'm_order_name'   => $aSnsformOrderInfo['buyer_name']
                    );
                    $this->snsform_model->publicUpdate('member_tb',$aInput, array('m_num' , $aSnsformOrderInfo['partner_buyer_id'])) ;
                    //---------------------------------------------------------------------------주문정보에 있는 이름/연락처 갱신


                    //--------------------------------------------------------------------------- 회원 구매수 ++

                    $sql = "UPDATE member_tb SET m_order_count = m_order_count + 1 WHERE m_num = '{$aSnsformOrderInfo['partner_buyer_id']}' ; ";
                    $this->db->query($sql);

                    //--------------------------------------------------------------------------- 회원 구매수 ++

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

    private function calc_stock($aSnsformOrderInfo , $type){

        $option_list = json_decode($aSnsformOrderInfo['option_list'],true);

        $sql = "SELECT seq , option_info FROM snsform_product_tb WHERE item_no = '{$aSnsformOrderInfo['item_no']}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();
        $product_option_arr = json_decode($aResult['option_info'],true);

        foreach ($option_list as $kk =>$rr) {

            $name_arr       = explode(' | ',$rr['option_name']);
            $item_depth     = count($name_arr);

            foreach ($product_option_arr as $kkk => $rrr) {

                if($type == 'm'){

                    if($item_depth == 1){

                        if ( $rrr['option_depth1'] == $name_arr[0] ){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']-(int)$rr['option_count'];
                        };

                    } else if($item_depth == 2){

                        if ( $rrr['option_depth1'] == $name_arr[0] &&  $rrr['option_depth2'] == $name_arr[1] ){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']-(int)$rr['option_count'];
                        };

                    } else if($item_depth == 3){

                        if ( $rrr['option_depth1'] == $name_arr[0] &&  $rrr['option_depth2'] == $name_arr[1] && $rrr['option_depth3'] == $name_arr[2]){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']-(int)$rr['option_count'];
                        };

                    }

                }else{ // p

                    if($item_depth == 1){

                        if ( $rrr['option_depth1'] == $name_arr[0] ){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']+(int)$rr['option_count'];
                        };

                    } else if($item_depth == 2){

                        if ( $rrr['option_depth1'] == $name_arr[0] &&  $rrr['option_depth2'] == $name_arr[1] ){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']+(int)$rr['option_count'];
                        };

                    } else if($item_depth == 3){

                        if ( $rrr['option_depth1'] == $name_arr[0] &&  $rrr['option_depth2'] == $name_arr[1] && $rrr['option_depth3'] == $name_arr[2]){
                            $product_option_arr[$kkk]['option_count'] = (int)$product_option_arr[$kkk]['option_count']+(int)$rr['option_count'];
                        };

                    }

                }

            }

        }

        $product_option_json = json_encode($product_option_arr,JSON_UNESCAPED_UNICODE);
        $sql = "UPDATE snsform_product_tb SET option_info = '{$product_option_json}' WHERE seq = '{$aResult['seq']}' ";
        $bRet = $this->db->query($sql);

        if($bRet == false){

        }

    }

}//end of class Api