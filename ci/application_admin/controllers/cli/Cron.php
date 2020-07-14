<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @date 20180207
 * @modify 황기석
 * @desc 매시 5분마다 크론 실행
 */
class Cron extends A_Controller {

    public $debug = true;
    public $curr_time;
    public $curr_time_i;
    public $curr_time_h;

    public function __construct() {
        parent::__construct();

        if( !$this->input->is_cli_request() ) {
            exit;
        }

        $this->curr_time    = date('Hi');
        $this->curr_time_i  = date('i');
        $this->curr_time_h  = date('H');

        $this->load->model('cron_model');

    }

    public function index(){

        $debug = $this->debug;
        $aResult = $this->cron_model->get_cron();

        if($debug) log_message('A',"------------------------ CRON START");

        if(count($aResult) > 0){

            for ($i = 0; $i < count($aResult) ; $i++) { $row = $aResult[$i];
                if($debug) log_message('A',"## CRON START -- fnc name {$row['cron_fnc']} :: {$row['bigo']} ");
                $this->{$row['cron_fnc']}($debug);
                if($debug) log_message('A',"## CRON END -- fnc name {$row['cron_fnc']} ");
            }

        }else{
            if($debug) log_message('A',"## CRON -- 실행 크론 없음 ");
        }
        if($debug) log_message('A',"------------------------ CRON END");

    }

    //메인페이지 상품노출 proc
    public function main_product($debug){
        if($this->curr_time_i % 30 == 0) { //30분마다 처리
            $this->cron_model->proc_main_product($debug);
        }
    }

    //메인페이지 상품노출 proc
    public function main_product_v2($debug){
        if($this->curr_time_i % 15 == 0) { //15분마다 처리
            $this->cron_model->proc_main_product_v2($debug);
        }
    }

    //메인페이지 상품노출 proc
    public function main_product_v3($debug){
        if($this->curr_time_i % 15 == 0) { //15분마다 처리
            $this->cron_model->proc_main_product_v3($debug);
        }
    }

    //메인페이지 상품노출 proc
    public function main_product_v4($debug){
        if($this->curr_time_i % 15 == 0) { //15분마다 처리
            $this->cron_model->proc_main_product_v4($debug);
        }
    }



    //최근 30일 판매수량
    public function product_static_month_order($debug){
        $this->cron_model->product_static_month_order($debug);
    }

    //이번주/저번주 판매수량
    public function product_static_week_order($debug){
        if(date('w') == 1){ //월요일
            $this->cron_model->product_static_week_order($debug);
        }
    }

    //어제 판매수량
    public function product_static_yesterday_order($debug){
        $this->cron_model->product_static_yesterday_order($debug);
    }

    public function product_static_view($debug){
        $this->cron_model->product_static_view($debug);
    }

    //배송중 푸시발송
    public function sending_push($debug){

        $aResult = $this->cron_model->get_sending_push_target($debug);

        $target_arr1 = array();
        $target_arr2 = array();
        foreach ($aResult as $k => $r) {

            if(empty($r['m_trade_no']) == false) $r['isCart'] = true;
            else $r['isCart'] = false;

            $target_arr1[$r['invoice_no']][] = $r;

        }

        foreach ($target_arr1 as $k => $r) {

            $nOrderInfo     = (int)count($r) - 1 ;
            $isCompl_send   = $r['req_push_cnt'] >= 3 ? true : false;

            if(mb_strlen($r[0]['p_name']) > 5) $p_name = mb_substr($r[0]['p_name'], 0, 5, 'utf-8')."...";
            else $p_name = $r[0]['p_name'];

            $push_data  = array();
            if($r[0]['isCart'] == true && $nOrderInfo > 0) $push_data['title'] = "[{$p_name}] 외 {$nOrderInfo}건의 배송이 시작되었습니다.";
            else $push_data['title'] = "[{$p_name}]의 배송이 시작되었습니다.";;
            $push_data['body']  = "";
            $push_data['page']  = "delivery";

            $resp = send_app_push_log($r[0]['partner_buyer_id'], $push_data, $isCompl_send);
            log_message('A','sending_push >>>>>> '.json_encode($resp));

            //푸시발송 cnt
            $sql = "UPDATE snsform_order_tb SET req_push_cnt = req_push_cnt + 1 WHERE invoice_no = '{$k}'; ";
            $this->db->query($sql);

            if( $resp['success'] == true || $isCompl_send = true ){
                $sql = "UPDATE snsform_order_tb SET delivery_push_yn = 'Y' WHERE invoice_no = '{$k}'; ";
                $this->db->query($sql);
            }

        }

    }

    /**
     * 상품 판매상태 업데이트
     */
    public function product_salestate_update() {
        $this->load->model('product_model');

        //노출안함상품 노출시작
        $query_data = array();
        $query_data['where']['term_yn'] = 'Y';
        $query_data['where']['termdate1'] = current_datetime();
        $query_data['where']['termdate2'] = current_datetime();
        $query_data['where']['display_state'] = 'N';
        $product_list = $this->product_model->get_product_list($query_data);

        foreach ( $product_list as $key => $row ) {
            $query_data = array();
            $query_data['p_display_state'] = 'Y';
            $this->product_model->update_product($row['p_num'], $query_data);
        }//end of foreach()

        //판매중인 상품 판매종료
        $query_data = array();
        $query_data['where']['term_yn'] = 'Y';
        $query_data['where']['termdate2_end'] = current_datetime();
        $query_data['where']['sale_state'] = 'Y';
        $query_data['where']['display_state'] = 'Y';
        $product_list = $this->product_model->get_product_list($query_data);

        foreach ( $product_list as $key => $row ) {
            $query_data = array();
            $query_data['p_sale_state'] = 'N';
            $this->product_model->update_product($row['p_num'], $query_data);
        }//end of foreach()

    }//end of product_salestate_update()

    /**
     * 오래된 데이터 삭제
     * 장바구니 : 3개월 complete
     * 알림메시지 noti : 3개월 complete
     * log 파일 : 기간체크 complete
     * 홈30 상품 : 3개월 complete
     **/
    public function clear_data() {

        //별도 cron
        //log 파일삭제 : 0 5 * * * sudo find /data/shop1/log/ -name '*.php' -mtime +60

        { //홈30 상품
            $sql = "SELECT * FROM `main_product_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
            $count = $this->db->query($sql)->num_rows();

            if($count > 0){
                $sql = "DELETE FROM `main_product_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
                $this->db->query($sql);
            }
        }

        { //알림메시지 noti
            $sql = "SELECT * FROM `noti_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
            $count = $this->db->query($sql)->num_rows();

            if($count > 0){
                $sql = "DELETE FROM `noti_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
                $this->db->query($sql);
            }
        }

        { //장바구니
            $sql = "SELECT * FROM `cart_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
            $count = $this->db->query($sql)->num_rows();

            if($count > 0){
                $sql = "DELETE FROM `cart_tb` WHERE reg_date < DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) , '%Y%m%d%H%i%s');";
                $this->db->query($sql);
            }
        }

    }

}//end of class Cron