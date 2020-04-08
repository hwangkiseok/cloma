<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 이벤트 참여 관련 컨트롤러
 */
class Event_active extends A_Controller {

    public function __construct() {
        parent::__construct();
        //model
        $this->load->model('event_active_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->event_active_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['div']             = trim($this->input->post_get('div', true));
        $req['pro_state']       = trim($this->input->post_get('pro_state', true));
        $req['dis_state']       = trim($this->input->post_get('dis_state', true));
        $req['m_id']            = trim($this->input->post_get('m_id', true));
        $req['ew_type']         = trim($this->input->post_get('ew_type', true));        //달성종류
        $req['grp_yn']          = trim($this->input->post_get('grp_yn', true));         //회원별보기(Y|null)
        $req['ym']              = trim($this->input->post_get('ym', true));             //년월
        $req['select_gift']     = trim($this->input->post_get('select_gift', true));    //당첨상품
        //$req['year']            = trim($this->input->post_get('year', true));           //년도
        //$req['month']           = trim($this->input->post_get('month', true));          //월
        $req['dateType']        = trim($this->input->post_get('dateType', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['m_num']           = trim($this->input->post_get('m_num', true));          //회원번호
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        //if( $req['grp_yn'] == "" ) {
        //    $req['grp_yn'] = "Y";
        //}
        if( empty($req['div']) ) {
            $req['div'] = 1;
        }
        if( empty($req['ym']) ) {
            $req['ym'] = date("Ym", time());
        }
        //if( empty($req['year']) ) {
        //    $req['year'] = date("Y", time());
        //}
        //if( empty($req['month']) ) {
        //    $req['month'] = date("m", time());
        //}
        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        //var_dump($req);

        return $req;
    }//end of _list_req()

    /**
     * 이벤트 참여 목록
     */
    public function event_active_list() {

        //request
        $req = $this->_list_req();

        //달성검색 radio 배열 목록 추출
        $query = "select ew_type from event_winner_tb ";
        $query .= "group by ew_type ";
        $query .= "order by ew_type ";
        $ew_type_result = $this->db->query($query)->result();

        $ew_type_array = array();
        foreach ( $ew_type_result as $row ) {
            $ew_type_array[$row->ew_type] = get_event_winner_type_name($row->ew_type);
        }

        $this->_header();

        $this->load->view("/event_active/event_active_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page,
            'ew_type_array' => $ew_type_array
        ));

        $this->_footer();
    }//end of event_active_list()

    /**
     * 이벤트 참여 목록 (Ajax)
     */
    public function event_active_list_ajax() {
        ajax_request_check(true);

        //request
        $req = $this->_list_req();

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //전체갯수
        $list_count = $this->event_active_model->get_event_active_list($query_array, "", "", true);


        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/event_active/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $stat_data = array();

        //목록
        $event_active_list = $this->event_active_model->get_event_active_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['e_division'] = array("asc", "sorting");
        $sort_array['e_subject'] = array("asc", "sorting");
        $sort_array['e_termlimit_yn'] = array("asc", "sorting");
        $sort_array['e_termlimit_datetime1'] = array("asc", "sorting");
        $sort_array['e_termlimit_datetime2'] = array("asc", "sorting");
        $sort_array['e_regdatetime'] = array("asc", "sorting");
        $sort_array['e_usestate'] = array("asc", "sorting");
        $sort_array['ea_regdatetime'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");
        $sort_array['ea_month_count'] = array("asc", "sorting");
        $sort_array['ea_accrue_count'] = array("asc", "sorting");
        $sort_array['ew_type'] = array("asc", "sorting");
        $sort_array['ew_contact'] = array("asc", "sorting");
        $sort_array['win_overlap'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $view_file = "/event_active/event_active_list_ajax";

        $this->load->view($view_file, array(
            'req'               => $req,
            'GV'                => $GV,
            'PGV'               => $PGV,
            'sort_array'        => $sort_array,
            'list_count'        => $list_count,
            'list_per_page'     => $req['list_per_page'],
            'page'              => $req['page'],
            'event_active_list' => $event_active_list,
            'pagination'        => $page_result['pagination'],
            'stat_data'         => $stat_data

        ));
    }//end of event_active_list_ajax()

    /**
     * 이벤트 참여 목록 엑셀 다운로드
     */
    public function event_active_list_excel() {

        $req = $this->_list_req();


        if($req['div'] == '1'){ //출석이벤트 관련

            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $file_name = iconv("utf-8", "euc-kr", "이벤트참여목록_" . current_datetime() . ".xls");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$file_name");
            header("Content-Description: PHP5 Generated Data");
            header("Cache-Control: max-age=0");

            //request
            $req = $this->_list_req();

            //lib
            require_once(APPPATH . "third_party/PHPExcel.php");

            $phpExcel = new PHPExcel();

            $phpExcel->getProperties()->setTitle("이벤트참여목록");

            $excelRow = $phpExcel->setActiveSheetIndex(0);
            $excelRow->setCellValue("A1", "No.");
            $excelRow->setCellValue("B1", "이벤트종류");
            $excelRow->setCellValue("C1", "이벤트제목");
            $excelRow->setCellValue("D1", "기간");
            $excelRow->setCellValue("E1", "참여회원");
            $excelRow->setCellValue("F1", "월총출석수");
            $excelRow->setCellValue("G1", "월연속출석수");
            $excelRow->setCellValue("H1", "참여일시");
            $excelRow->setCellValue("I1", "회원번호");

            //
            if( !empty($req['ew_type']) ) {
                $excelRow->setCellValue("J1", "달성종류");
                $excelRow->setCellValue("K1", "연락처");
            }

            //쿼리 배열
            $query_array =  array();
            $query_array['where'] = $req;
            if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
                $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
            }

            //전체갯수
            $list_count = $this->event_active_model->get_event_active_list($query_array, "", "", true);

            $list_number = $list_count - ($req['list_per_page'] * ($req['page']-1));

            //목록
            $event_active_list = $this->event_active_model->get_event_active_list($query_array);

            $i = 2;
            foreach ($event_active_list as $key => $row) {
                if($row->e_termlimit_yn == 'Y') {
                    $row->termlimit_date = get_date_format($row->e_termlimit_datetime1) . " ~ " . get_date_format($row->e_termlimit_datetime2);
                }
                else {
                    $row->termlimit_date = $this->config->item($row->e_termlimit_yn, 'event_termlimit_yn');
                }

                $excelRow = $phpExcel->setActiveSheetIndex(0);
                $excelRow->setCellValue("A$i", $list_number);
                $excelRow->setCellValue("B$i", $this->config->item($row->e_division, 'event_division'));
                $excelRow->setCellValue("C$i", $row->e_subject);
                $excelRow->setCellValue("D$i", $row->termlimit_date);
                $excelRow->setCellValue("E$i", $row->m_loginid);
                $excelRow->setCellValue("F$i", number_format($row->ea_month_count));
                $excelRow->setCellValue("G$i", number_format($row->ea_accrue_count));
                $excelRow->setCellValue("H$i", get_datetime_format($row->ea_regdatetime));
                $excelRow->setCellValue("I$i", $row->ew_member_num);
                if( !empty($req['ew_type']) ) {
                    $excelRow->setCellValue("J$i", get_event_winner_type_name($row->ew_type));
                    $excelRow->setCellValue("K$i", $row->ew_contact);
                }

                $i++;
                $list_number--;
            }//end of foreach()

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;


        }else{

            $req['fExcelDownLoad'] = true;

            //쿼리 배열
            $query_array =  array();
            $query_array['where'] = $req;
            if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
                $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
            }

            //목록
            if( $req['div'] == "naver_search_20170922" ) {
                $excelFileNM = '네이버검색이벤트';
                $event_active_list = $this->event_active_model->get_event_naver_search_list($query_array);
            }
            else if( $req['div'] == "event_20171027" ) {
                $excelFileNM = '추석이벤트_';
                $event_active_list = $this->event_active_model->get_event_chuseok_search_list($query_array);
            }else if( $req['div'] == "event_20171205" ) {
                $excelFileNM = '안마의자드립니다_이벤트_';
                $event_active_list = $this->event_active_model->get_event_thanks_list($query_array);
            }else if( $req['div'] == "event_20171206" ) {
                $excelFileNM = '안마의자드립니다_이벤트_';
                $event_active_list = $this->event_active_model->get_event_thanks2_list($query_array);
            }else if( $req['div'] == "event_20171219" ) {
                $excelFileNM = 'VIP고객_이벤트_';
                $event_active_list = $this->event_active_model->get_event_vip_list($query_array);
            }else if( $req['div'] == "event_20171221" ) {
                $excelFileNM = 'VIP고객2_이벤트_';
                $event_active_list = $this->event_active_model->get_event_vip2_list($query_array);
            }else if( $req['div'] == "event_20180109" ) {
                $excelFileNM = '선착순_이벤트_';
                $event_active_list = $this->event_active_model->get_event_first_come_list($query_array);
            }else if( $req['div'] == "event_20180117" ) {
                $excelFileNM = '구매고객_감사이벤트_';
                $event_active_list = $this->event_active_model->get_event_buyer_gift_list($query_array);
            }else if( $req['div'] == "event_20180122" ) {
                $excelFileNM = '선착순_이벤트2_';
                $event_active_list = $this->event_active_model->get_event_first_come2_list($query_array);
            }else if( $req['div'] == "event_20180201" ) {
                $excelFileNM = '선착순_이벤트3_';
                $event_active_list = $this->event_active_model->get_event_first_come3_list($query_array);
            }else if( $req['div'] == "event_20180310" ) {
                $excelFileNM = '메가쇼_룰렛이벤트_';
                $event_active_list = $this->event_active_model->get_event_roulette_list($query_array);
            }else if( $req['div'] == "event_20180312" ) {
                $excelFileNM = '룰렛이벤트2_';
                $event_active_list = $this->event_active_model->get_event_roulette2_list($query_array);
            }else if( $req['div'] == "event_20180328" ) {
                $excelFileNM = '룰렛이벤트3_';
                $event_active_list = $this->event_active_model->get_event_roulette3_list($query_array);
            }else if( $req['div'] == "event_20180508" ) {
                $excelFileNM = '럭키박스_';
                $event_active_list = $this->event_active_model->get_lucky_box_list($query_array);
            }else if( $req['div'] == "event_20180516" ) {
                $excelFileNM = '럭키박스2_';
                $event_active_list = $this->event_active_model->get_lucky_box2_list($query_array);
            }else if( $req['div'] == "event_20180620" ) {
                $excelFileNM = '럭키박스3_';
                $event_active_list = $this->event_active_model->get_lucky_box3_list($query_array);
            }else if( $req['div'] == "event_20180711" ) {
                $excelFileNM = '뉴_럭키박스_';
                $event_active_list = $this->event_active_model->get_new_lucky_box_list($query_array);
            }else if( $req['div'] == "event_20180717" ) {
                $excelFileNM = '카카오_럭키박스_';
                $event_active_list = $this->event_active_model->get_kakao_lucky_box_list($query_array);
            }else if( $req['div'] == "event_20180823" ) {
                $excelFileNM = '깜짝이벤트_';
                $event_active_list = $this->event_active_model->get_surprise_list($query_array);
            }else if( $req['div'] == "event_20180911" ) {
                $excelFileNM = '매일룰렛이벤트_';
                $event_active_list = $this->event_active_model->get_daily_roulette_list($query_array);
            }else if( $req['div'] == "event_20190527" ) {
                $excelFileNM = '쇼핑적립금_이벤트_';
                $event_active_list = $this->event_active_model->get_active_list($query_array);
            }else{
                exit;
            }

            $this->load->library('/MY_Excel');
            $aField	=  array(
            	'ens_num'			    => 'SEQ'
            ,   'e_subject'		        => '이벤트명'
            ,	'ens_ph_str'			=> '휴대폰번호'
            ,	'ens_regdatetime_str'	=> '참여일시'
            //,	'ens_reg_info_str'	    => '브라우저'
            ,   'order_cnt'             => '주문건수'
            ,   'win_overlap'           => '당첨여부'
            ,   'view_chk'              => '당첨여부확인'
            );

            $this->my_excel->ExcelDown($event_active_list , $excelFileNM.date('YmdHi') , $aField);

        }

    }//end of event_active_list_excel()

    /**
     * 이벤트 참여 삭제 처리 (Ajax)
     */
    public function event_active_delete_proc() {
        ajax_request_check();

        //request
        $req['ea_num'] = $this->input->post_get('ea_num', true);

        //이벤트 참여 정보
        $event_active_row = $this->event_active_model->get_event_active_row($req['ea_num']);

        if( empty($event_active_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //이벤트 참여 삭제
        if( $this->event_active_model->delete_event_active($req['ea_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of event_active_delete_proc()

    /**
     */
    public function getWinnerItem() {
        ajax_request_check();

        $aInput = array( 'e_code' => $this->input->get('div_val'));
        $aRet = $this->config->item($aInput['e_code']);

        if(count($aRet) > 0){
            result_echo_json(get_status_code('success'), '', true, '','',$aRet);
        }else{

            $this->load->model('event_model');
            $aGiftList = $this->event_model->getEventGiftCodeLists($aInput['e_code']);

            if(count($aGiftList) < 1){
                result_echo_json(get_status_code('error'), "해당 이벤트에 등록된 당첨상품이 없습니다.\n개발자에게 문의하세요", true );
            }else{
                $aRet = array();
                foreach ($aGiftList as $r) { $aRet[$r['gift_code']] = $r['gift_name']; }

                result_echo_json(get_status_code('success'), '', true, '','',$aRet);
            }


        }
    }//end of getWinnerItem()

    public function setWinner(){

        $aInput = array(    'e_code'                => $this->input->post('e_code')
                        ,   'regist_winner_item'    => $this->input->post('regist_winner_item')
                        ,   'overlap_chk'           => $this->input->post('overlap_chk')
                        ,   'join_info_chk'         => $this->input->post('join_info_chk')
                        ,   'reg_date'              => date('YmdHis')
                        ,   'filePath'              => $_FILES['csvData']['tmp_name']
        );

        $this->load->model('event_model');

        $bindParam['where']             = array();
        $bindParam['where']['e_code']   = $aInput['e_code'];
        $aEventInfo                     = $this->event_model->get_event_list($bindParam);

        if(count($aEventInfo) < 1){ //validChk
            result_echo_json(get_status_code('error'), '이벤트정보가 없습니다.', true );
            exit;
        }

        $aInput['e_num'] = $aEventInfo[0]->e_num;

        $this->load->library("/MY_Excel",$aInput);
        $aResult = $this->my_excel->getData();

        if(count($aResult) < 2){
            result_echo_json(get_status_code('error'), '엑셀내용이 없습니다.', true );
            exit;
        }

        array_shift($aResult);
        $last_data      = array();
        $_overlap_cnt   = 0;

        foreach ($aResult as $k => $v) {

            /**
             * @date 190312
             * @modify 황기석
             * @desc 참여정보가 없는 경우 엑셀파일에서 회원번호 및 연락처를 받아서 처리한다
             */
            if($aInput['join_info_chk'] == 'N'){
                $aTmpData = array(
                    'cel_tel'   => $v['B']
                ,   'm_num'     => $v['A']
                );

            }else{
                /**
                 * 기존 처리방법
                 */
                $aTmpData = array(
                    'seq'       => $v['A']
                ,   'cel_tel'   => ph_slice($v['D'])
                );
            }

            /**
             * @date 190312
             * @modify 황기석
             * @desc 중복당첨이 불가능한 경우
             */
            if($aInput['overlap_chk'] == 'N'){

                $arrayParams = array(   'initInfo'  => $aInput
                                    ,   'setData'   => $aTmpData
                );

                $bRet = $this->event_active_model->overlapWinner($arrayParams);

                if($bRet == true) {
                    log_message('ZS', 'ADMIN - Event_active CLASS - setWinner FUNC - overlapData :: '.serialize($arrayParams));
                    $_overlap_cnt++;
                }

                $aTmpData['is_overlap'] = $bRet;

            }else{

                $aTmpData['is_overlap'] = false;

            }

            $last_data[]            = $aTmpData;

        }

        // 당첨자 정보 INSERT PROC

        $arrayParams = array(   'initInfo'  => $aInput
                            ,   'setData'   => $last_data
        );

        //zsView($arrayParams,true);

        $aResult                = $this->event_active_model->setWinner($arrayParams);
        $aResult['overlap_cnt'] = $_overlap_cnt;

        echo json_encode($aResult);


        exit;

    }//end of setWinner()

    public function auto_attend_view(){

        ajax_request_check();

        $aInput = array( 'seq' => $this->input->get('seq') );

        $aAttendLists = $this->event_active_model->get_auto_attend_detail_list($aInput);





        $this->load->view("/event_active/auto_attend_view", array(
            'aAttendLists' => $aAttendLists
        ));




    }


}//end of class Event_active