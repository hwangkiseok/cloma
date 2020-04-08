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

}//end of class Cron