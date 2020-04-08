<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 이벤트 기프티콘 관련 컨트롤러
 */
class Event_gift extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('event_gift_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->event_gift_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['state']           = trim($this->input->post_get('state', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['eg_event_num']    = trim($this->input->post_get('div', true));


        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 이벤트 기프티콘 목록
     */
    public function event_gift_list() {
        //request
        $req = $this->_list_req();

        //이벤트 목록
        $this->load->model("event_model");
        $event_list = $this->event_model->get_event_list();
        $event_option_array = result_to_option_array($event_list, "e_num", "e_subject");

        $this->_header();

        $this->load->view("/event_gift/event_gift_list", array(
            'req'                   => $req,
            'list_per_page'         => $this->list_per_page,
            'event_option_array'    => $event_option_array
        ));

        $this->_footer();
    }//end of event_gift_list()

    /**
     * 이벤트 기프티콘 목록 (Ajax)
     */
    public function event_gift_list_ajax() {
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
        //$query_array['groupby'] = "eg_event_num";   //이벤트로 그룹핑

        //전체갯수
        $list_count = $this->event_gift_model->get_event_gift_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/event_gift/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $event_gift_list = $this->event_gift_model->get_event_gift_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['eg_event_num'] = array("asc", "sorting");
        $sort_array['eg_event_ym'] = array("asc", "sorting");
        $sort_array['eg_gift'] = array("asc", "sorting");
        $sort_array['eg_member_num'] = array("asc", "sorting");
        $sort_array['eg_regdatetime'] = array("asc", "sorting");
        $sort_array['eg_state'] = array("asc", "sorting");
        $sort_array['e_subject'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/event_gift/event_gift_list_ajax", array(
            'req'               => $req,
            'GV'                => $GV,
            'PGV'               => $PGV,
            'sort_array'        => $sort_array,
            'list_count'        => $list_count,
            'list_per_page'     => $req['list_per_page'],
            'page'              => $req['page'],
            'event_gift_list'   => $event_gift_list,
            'pagination'        => $page_result['pagination']
        ));
    }//end of event_gift_list_ajax()

    public function event_gift_code_insert() {

        //request
        $req = $this->_list_req();
//특가전이벤트 예외처리 추가
        $sql            = "SELECT * FROM event_tb WHERE e_code <> '' AND ( e_proc_state = 'Y' OR e_num = '37') ORDER BY e_num DESC ; ";
        $oResult        = $this->db->query($sql);
        $aEventLists    = $oResult->result_array();

        $view_file = '/event_gift/code_upsert_pop';

        $this->load->view($view_file, array(
            'req'         => $req,
            'list_url'    => $this->_get_list_url(),
            'aEventLists' => $aEventLists
        ));

    }




    public function event_gift_code_upsert(){
        ajax_request_check();


        $aInput = array(    'event_code'    => $this->input->post('event_code')
                        ,   'tmp_code_name' => $this->input->post('code_name')
                        ,   'tmp_code'      => $this->input->post('code')
                        ,   'tmp_use_flag'  => $this->input->post('code_use_flag')
                        ,   'tmp_type'      => $this->input->post('code_type')
        );



        $bRet = true;
        $tmp_code_arr = array();

        foreach ($aInput['tmp_code'] as $code => $val) {

            $tmp_code_arr[$val]++;

            if($tmp_code_arr[$val] > 1 ) {
                $bRet = false;
                $err_arr2[] = $val;
            }
        }

        if($bRet == false){
            $aRet = array('success' => false , 'msg' => "이미 등록된 코드가 있습니다.\n중복 코드 : ".implode(',',$err_arr2) );
            echo json_encode_no_slashes($aRet);
            exit;
        }

        $insert_code_str = '\''.implode('\',\'',$aInput['tmp_code']).'\'';

        $sql        = "SELECT *,COUNT(*) AS cnt FROM event_gift_code_tb  WHERE gift_code IN  ({$insert_code_str}) AND event_code <> '{$aInput['event_code']}' GROUP BY gift_code";
        $oResult    = $this->db->query($sql);
        $aResult    = $oResult->result_array();

        $err_arr = $err_arr2 = array();
        foreach ($aResult as $row) {
            if($row['cnt'] > 0 ) {
                $err_arr[] = $row['gift_code'];
            }
        }

        if(count($err_arr) > 0){
            $aRet = array('success' => false , 'msg' => "이미 등록된 코드가 있습니다.\n중복 코드 : ".implode(',',$err_arr) );
            echo json_encode_no_slashes($aRet);
            exit;
        }

        foreach ($aInput['tmp_code'] as $seq => $code_val) {

            $code_name_val      = $aInput['tmp_code_name'][$seq];
            $code_use_flag_val  = $aInput['tmp_use_flag'][$seq];
            $code_type_val      = $aInput['tmp_type'][$seq];

            $sql            = " UPDATE event_gift_code_tb 
                                SET 
                                  gift_code   = '{$code_val}' 
                                , gift_name   = '{$code_name_val}'
                                , use_flag    = '{$code_use_flag_val}'
                                , `type`      = '{$code_type_val}'
                                , mod_date    = DATE_FORMAT(NOW(),'%Y%m%d%i%s')
                                WHERE event_code = '{$aInput['event_code']}' AND seq = '{$seq}' ;
                                 
            ";

            $this->db->query($sql);

        }

        $aRet = array('success' => true , 'msg' => '수정완료' );
        echo json_encode_no_slashes($aRet);

        exit;

    }




    public function copyGiftCode() {

        //request
        ajax_request_check();

        $aInput = array(
            'event_code'    => $this->input->post('e_code')
        ,   'reg_date'      => current_datetime()
        ,   'curr_m'        => date('Ym')
        ,   'next_m'        => date('Ym',strtotime("+1 months"))
        );

        $sql = "INSERT INTO event_gift_code_tb
                (
                      event_code            
                    , gift_code                                  
                    , gift_name                                      
                    , use_flag                                                 
                    , state                                     
                    , `TYPE`                          
                    , daliy_issue_cnt  
                    , gift_weighted                          
                    , `sort`                                            
                    , gift_ym                                          
                    , reg_date
                )
                SELECT
                  event_code
                , CONCAT(gift_code,'_{$aInput['next_m']}') AS gift_code
                , gift_name
                , use_flag
                , state
                , `type`
                , daliy_issue_cnt
                , gift_weighted
                , `sort`
                , '{$aInput['next_m']}' AS gift_ym
                , '{$aInput['reg_date']}' AS reg_date
                FROM event_gift_code_tb
                WHERE event_code = '{$aInput['event_code']}' AND gift_ym = '{$aInput['curr_m']}' ;   
        ";

        $bRet = $this->db->query($sql);
        $aRet = array('success' => $bRet , 'msg' => '' );
        echo json_encode_no_slashes($aRet);
        exit;

    }

    public function addGiftCode() {

        //request
        ajax_request_check();

        $aInput = array(    'event_code'    => $this->input->post('e_code')
                        ,   'reg_date'      => current_datetime()
        );

        $bRet = $this->db->insert("event_gift_code_tb", $aInput);
        $o_last_id = $this->db->query('SELECT last_insert_id() as last_id');
        $a_last_id = $o_last_id->row_array();

        $aRet = array('success' => $bRet , 'msg' => '' , 'last_id' => $a_last_id['last_id'] );
        echo json_encode_no_slashes($aRet);

        exit;

    }


    public function delGiftCode() {

        //request
        ajax_request_check();

        $aInput = array( 'seq' => $this->input->post('seq') );

        $sql = "DELETE FROM event_gift_code_tb WHERE seq = '{$aInput['seq']}'; ";
        $bRet= $this->db->query($sql);

        $aRet = array('success' => $bRet , 'msg' => '');
        echo json_encode_no_slashes($aRet);

        exit;

    }


    public function getGiftCode() {
        ajax_request_check();

        //request
        $aInput = array( 'e_code' => $this->input->post('e_code') );

        $sql        = "SELECT * FROM event_tb WHERE e_code = '{$aInput['e_code']}'; ";
        $oResult    = $this->db->query($sql);
        $aEventInfo = $oResult->row_array();


        $addQueryString = '';
        if($aEventInfo['e_content_type'] == 2) $addQueryString .= " AND gift_ym >= DATE_FORMAT(NOW(),'%Y%m') ";

        $sql = "SELECT A.* FROM event_gift_code_tb A
                WHERE A.event_code = '{$aInput['e_code']}'
                AND A.use_flag = 'Y'
                {$addQueryString}
                ORDER BY A.sort,A.seq; ";

        $oResult = $this->db->query($sql);
        $aGiftCode = $oResult->result_array();

        $curr_m = date('Ym');
        $next_m = date('Ym',strtotime("+1 months"));

        if(count($aGiftCode) > 0){ //등록된 기프티콘이 있는 경우

            if( $aEventInfo['e_content_type'] == '2' ){

                $tmp_result = $aGiftCode;
                unset($aGiftCode);
                $aGiftCode = array($curr_m => array() , $next_m => array());
                foreach ($tmp_result as $row) {
                    $aGiftCode[$row['gift_ym']][] = $row;
                }

            }

        }

        $ret = array('success' => true , 'msg' => '' , 'info' => $aEventInfo , 'data' => $aGiftCode);
        echo json_encode_no_slashes($ret);

        exit;

    }



    /**
     * 이벤트 기프티콘 추가
     */
    public function event_gift_insert_pop() {
        //request
        $req = $this->_list_req();

        //이벤트 목록
        $this->load->model("event_model");
        $event_list = $this->event_model->get_event_list();
        $event_option_array = result_to_option_array($event_list, "e_num", "e_subject");

        $view_file = '/event_gift/event_gift_insert_pop';

        $this->load->view($view_file, array(
            'req'                   => $req,
            'list_url'              => $this->_get_list_url(),
            'event_option_array'    => $event_option_array
        ));
    }//end of event_gift_insert_pop()

    /**
     * 이벤트 기프티콘 추가 처리 (Ajax)
     */
    public function event_gift_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        $eg_gift_set_rules = $this->default_set_rules;
        if( empty($_FILES['eg_gift_file']) ) {
            $eg_gift_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "eg_event_num" => array("field" => "eg_event_num", "label" => "이벤트", "rules" => "required|" . $this->default_set_rules),
            "eg_event_ym" => array("field" => "eg_event_ym", "label" => "이벤트년월", "rules" => $this->default_set_rules),
            "eg_gift_file" => array("field" => "eg_gift_file", "label" => "기프티콘 파일", "rules" => $eg_gift_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $eg_event_num = $this->input->post('eg_event_num', true);
            $eg_event_ym = $this->input->post('eg_event_ym', true);
            $eg_event_gift = $this->input->post('eg_event_gift', true);

            if($eg_event_num == '2' && $eg_event_gift == ''){ //네이버검색이벤트 인 경우 상품을 필수 입력정보로 한다.
                result_echo_json(get_status_code('success'), '미스할인 네이버검색 이벤트는 상품을 선택해야합니다.', true, 'alert');
                exit;
            }

            if( empty($form_error_array) ) {
                $event_gift_path_db = $this->config->item("event_gift_head") . "/" . date("Y", time()) . "/" . date("md", time());
                $event_gift_path = $this->config->item("event_gift_path") . "/" . date("Y", time()) . "/" . date("md", time());
                create_directory($event_gift_path);

                $config = array();
                $config['upload_path'] = $event_gift_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']	= $this->config->item('upload_total_max_size');
                $config['encrypt_name'] = false;
                $config['overwrite'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                $succ = 0;
                $fail = 0;

                foreach ($_FILES['eg_gift_file']['name'] as $key => $name) {
                    $name_parts = pathinfo($name);
                    $eg_gift = $name_parts['filename'];

                    //중복체크
                    $eg_over_row = $this->event_gift_model->get_event_gift_row(array('eg_gift' => $eg_gift));
                    if( !empty($eg_over_row) ) {
                        $fail++;
                        continue;
                    }

                    //기본 파일 배열 설정 (CI에서 사용)
                    $_FILES['userfile']['name'] = $_FILES['eg_gift_file']['name'][$key];
                    $_FILES['userfile']['type'] = $_FILES['eg_gift_file']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['eg_gift_file']['tmp_name'][$key];
                    $_FILES['userfile']['error'] = $_FILES['eg_gift_file']['error'][$key];
                    $_FILES['userfile']['size'] = $_FILES['eg_gift_file']['size'][$key];

                    if( $this->upload->do_upload() ){
                        $upload_data_array = $this->upload->data();
                        $eg_gift_file = $event_gift_path_db . "/" . $upload_data_array['file_name'];
                        @chmod($upload_data_array['full_path'], 0775);
                        @chown($upload_data_array['full_path'], get_current_user() . ".apache");
                    }
                    else {
                        $form_error_array['eg_gift_file'] = strip_tags($this->upload->display_errors());
                    }//end of if()

                    $query_data = array();
                    $query_data['eg_event_num'] = db_val($eg_event_num);
                    $query_data['eg_event_ym'] = db_val($eg_event_ym);
                    $query_data['eg_gift'] = db_val($eg_gift);
                    $query_data['eg_gift_file'] = db_val($eg_gift_file);
                    if( !empty($eg_event_gift) ) {
                        $query_data['eg_event_gift'] = db_val($eg_event_gift);
                    }

                    if( $this->event_gift_model->insert_event_gift($query_data) ) {
                        $succ++;
                    }
                    else {
                        $fail++;
                    }
                }//endforeach;

                $msg = lang('site_insert_success') . " [" . number_format($succ) . "건 성공 / " . number_format($fail) . "건 실패]";
                result_echo_json(get_status_code('success'), $msg, true, 'alert');
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of event_gift_insert_proc()

    /**
     * 이벤트 기프티콘 수정
     */
    public function event_gift_update_pop() {
        //request
        $req = $this->_list_req();
        $req['eg_num'] = $this->input->post_get('eg_num', true);

        //row
        $eg_row = $this->event_gift_model->get_event_gift_row(array('eg_num' => $req['eg_num']));

        if( empty($eg_row) ) {
            alert(lang('site_error_empty_data'));
        }

        //이벤트 목록
        $this->load->model("event_model");
        $event_list = $this->event_model->get_event_list();
        $event_option_array = result_to_option_array($event_list, "e_num", "e_subject");

        $this->load->view("/event_gift/event_gift_update_pop", array(
            'req'                   => $req,
            'list_url'              => $this->_get_list_url(),
            'eg_row'                => $eg_row,
            'event_option_array'    => $event_option_array,
        ));
    }//end of event_gift_update_pop()

    /**
     * 이벤트 기프티콘 수정 처리 (Ajax)
     */
    public function event_gift_update_proc() {
        ajax_request_check();

        //request
        $req['eg_num'] = $this->input->post_get('eg_num', true);

        //row
        $eg_row = $this->event_gift_model->get_event_gift_row(array('eg_num' => $req['eg_num']));

        if( empty($eg_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //set rules
        $eg_gift_file_set_rules = $this->default_set_rules;
        if( empty($eg_row->eg_gift_file) && empty($_FILES['eg_gift_file']) ) {
            $eg_gift_file_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "eg_num" => array("field" => "eg_num", "label" => "일련번호", "rules" => "required|is_natural|" . $this->default_set_rules),
            "eg_event_num" => array("field" => "eg_event_num", "label" => "이벤트", "rules" => "required|" . $this->default_set_rules),
            "eg_event_ym" => array("field" => "eg_event_ym", "label" => "이벤트년월", "rules" => $this->default_set_rules),
            "eg_gift_file" => array("field" => "eg_gift_file", "label" => "기프티콘 파일", "rules" => $eg_gift_file_set_rules),
            "eg_state" => array("field" => "eg_state", "label" => "상태", "rules" => "required|in_list[" . get_config_item_keys_string("event_gift_state") . "]|" . $eg_gift_file_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $eg_num = $this->input->post('eg_num', true);
            $eg_event_num = $this->input->post('eg_event_num', true);
            $eg_event_ym = $this->input->post('eg_event_ym', true);
            $eg_state = $this->input->post('eg_state', true);
            $eg_event_gift = $this->input->post('eg_event_gift', true);
            $eg_gift = "";
            $eg_gift_file = "";

            if( isset($_FILES['eg_gift_file']['name']) && !empty($_FILES['eg_gift_file']['name']) ) {
                $name_parts = pathinfo($_FILES['eg_gift_file']['name']);
                $eg_gift = $name_parts['filename'];

                //중복체크
                $eg_check_row = $this->event_gift_model->get_event_gift_row(array('eg_gift' => $eg_gift, 'not_eg_num' => $eg_row->eg_num));
                if( !empty($eg_check_row) ) {
                    $form_error_array['eg_gift_file'] = "이미 등록된 기프티콘 입니다.";
                }

                if( empty($form_error_array) ) {
                    $event_gift_path_db = $this->config->item("event_gift_head") . "/" . date("Y", time()) . "/" . date("md", time());
                    $event_gift_path = $this->config->item("event_gift_path") . "/" . date("Y", time()) . "/" . date("md", time());
                    create_directory($event_gift_path);

                    $config = array();
                    $config['upload_path'] = $event_gift_path;
                    $config['allowed_types'] = 'gif|jpg|jpeg|png';
                    $config['max_size']	= $this->config->item('upload_total_max_size');
                    $config['encrypt_name'] = false;
                    $config['overwrite'] = true;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if( $this->upload->do_upload('eg_gift_file') ) {
                        $upload_data_array = $this->upload->data();
                        $eg_gift_file = $event_gift_path_db . "/" . $upload_data_array['file_name'];
                        @chmod($upload_data_array['full_path'], 0775);
                        @chown($upload_data_array['full_path'], get_current_user() . ".apache");

                        if( $eg_gift != $eg_row->eg_gift ) {
                            //기존 파일 삭제
                            file_delete(1, $eg_row->eg_gift_file, HOMEPATH);
                        }
                    }
                    else {
                        $form_error_array['eg_gift_file'] = strip_tags($this->upload->display_errors());
                    }//end of if()
                }
            }//end of if( eg_gift_file )

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['eg_event_num'] = db_val($eg_event_num);
                $query_data['eg_event_ym'] = db_val($eg_event_ym);

                if( !empty($eg_event_gift) ) {
                    $query_data['eg_event_gift'] = db_val($eg_event_gift);
                }
                if( !empty($eg_gift) ) {
                    $query_data['eg_gift'] = db_val($eg_gift);
                }
                if( !empty($eg_gift_file) ) {
                    $query_data['eg_gift_file'] = db_val($eg_gift_file);
                }
                if( $eg_state == "1" ) {
                    $query_data['eg_member_num'] = "0";
                }
                $query_data['eg_state'] = db_val($eg_state);

                if( $this->event_gift_model->update_event_gift($eg_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_upadte_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of event_gift_update_proc()

    /**
     * 이벤트 기프티콘 삭제 처리 (Ajax)
     */
    public function event_gift_delete_proc() {
        ajax_request_check();

        //request
        $req['eg_num'] = $this->input->post_get('eg_num', true);

        //이벤트 기프티콘 정보
        $eg_row = $this->event_gift_model->get_event_gift_row(array('eg_num' => $req['eg_num']));

        if( empty($eg_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //이벤트 기프티콘 삭제
        if( $this->event_gift_model->delete_event_gift($req['eg_num']) ) {
            //기프티콘 파일 삭제
            file_delete("1", HOMEPATH . $eg_row->eg_gift_file);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of event_gift_delete_proc()

    /**
     * 이벤트 기프티콘 발급 팝업
     */
    public function event_gift_issue_pop() {
        //request
        $req = $this->_list_req();

        //기프티콘 목록
        $query = "
SELECT
A.*
, e_division
, e_subject
, e_member_join_flag
, e_code
, COUNT(eg_gift) AS gift_cnt
, C.gift_name

FROM event_gift_tb A
LEFT JOIN event_tb B ON e_num = eg_event_num
LEFT JOIN event_gift_code_tb C ON C.gift_code = A.eg_event_gift AND C.event_code = B.e_code 
WHERE A.eg_state = '1'
GROUP BY eg_event_num, eg_event_ym,eg_event_gift
        ";



        $event_gift_list = $this->db->query($query)->result();

        foreach ($event_gift_list as $key => $row) {

            $add_where = "";
            if(  $row->e_division == "1" && !empty($row->eg_event_ym) ) {
                $add_where = "and left(ew_regdatetime, 6) = '" . $row->eg_event_ym . "' ";
            }
            $addFromString = ' join member_tb on m_num = ew_member_num ';
            if($row->e_member_join_flag == 'Y'){
                $addFromString = '';
            }

            $query = "
                select count(*) as cnt
                from event_winner_tb
                    {$addFromString}
                where
                    ew_event_num = '" . $row->eg_event_num . "'
                    and ew_state = '2'
                    and ew_event_gift = '" . $row->eg_event_gift . "'
                    " . $add_where . "
                group by ew_event_gift
            ";

            //zsView($query);
            $winner_cnt = $this->db->query($query)->row("cnt");


            $row->winner_cnt = $winner_cnt;
        }//endforeach;

        $this->load->view("/event_gift/event_gift_issue_pop", array(
            'req'               => $req,
            'list_url'          => $this->_get_list_url(),
            'event_gift_list'   => $event_gift_list
        ));
    }//end of event_gift_issue_pop()

    /**
     * 이벤트 기프티콘 발급 처리
     */
    public function event_gift_issue_proc() {
        ajax_request_check();

        //request
        $req['eg_event_num'] = trim($this->input->post("eg_event_num", true));
        $req['eg_event_ym'] = trim($this->input->post("eg_event_ym", true));
        $req['eg_event_gift'] = trim($this->input->post("eg_event_gift", true));

        if( empty($req['eg_event_num']) ) {
            page_request_return(get_status_code("error"), lang("site_error_empty_id"), true, "alert");
        }

        //기프티콘 확인
        $query = "
            select eg_num
            from event_gift_tb
            where
                eg_event_num = '" . $this->db->escape_str($req['eg_event_num']) . "'
                and eg_event_ym = '" . $this->db->escape_str($req['eg_event_ym']) . "'
                and eg_event_gift = '" . $this->db->escape_str($req['eg_event_gift']) . "'
                and eg_state = '1'
        ";


        $eg_count = $this->db->query($query)->num_rows();
        if( empty($eg_count) ) {
            page_request_return(get_status_code("error"), "발급할 기프티콘이 없습니다.", true, "alert");
        }

        //model
        $this->load->model("event_model");

        $event_row = $this->event_model->get_event_row($req['eg_event_num']);
        if( empty($event_row) ) {
            page_request_return(get_status_code("error"), lang("site_error_empty_data") . " (No Event)", true, "alert");
        }

        //고정출석체크 이벤트일때
        if( $event_row->e_division == "1" ) {
            if( empty($req['eg_event_ym']) ) {
                page_request_return(get_status_code("error"), lang("site_error_empty_id") . " (No Ym)", true, "alert");
            }

            //기프티콘 목록
            $query = "
                select *
                from event_gift_tb
                where
                    eg_event_num = '" . $this->db->escape_str($req['eg_event_num']) . "'
                    and eg_event_ym = '" . $this->db->escape_str($req['eg_event_ym']) . "'
                    and eg_state = '1'
            ";
            $gift_list = $this->db->query($query)->result();

            foreach ($gift_list as $key => $gift_row) {
                //기프티콘 발급
                $this->_gift_issue($event_row, $gift_row);
            }//endforeach;
        }
        //일반이벤트 일때
        else {
            //기프티콘 목록
            $query = "
                select *
                from event_gift_tb
                where
                    eg_event_num = '" . $this->db->escape_str($req['eg_event_num']) . "'
                    and eg_event_gift = '" . $this->db->escape_str($req['eg_event_gift']) . "'
                    and eg_state = '1'
            ";
            $gift_list = $this->db->query($query)->result();

            foreach ($gift_list as $key => $gift_row) {
                //기프티콘 발급
                $this->_gift_issue($event_row, $gift_row);
            }//endforeach;
        }//endif; (출석, 일반)

        page_request_return(get_status_code("success"), "기프티콘 발급완료", true, "alert");
    }//end of event_gift_issue_proc()

    /**
     * 기프티콘 발급 처리 (parts)
     */
    private function _gift_issue($event_row, $gift_row) {
        //등록상태인 기프티콘만
        if( $gift_row->eg_state != "1" ) {
            return false;
        }

        //해당 이벤트 당첨상태인 회원
        $ew_where_query = "where ew_event_num = '" . $gift_row->eg_event_num . "' ";
        $ew_where_query .= "and ew_state = '2' ";
        $ew_where_query .= "and ew_event_gift = '".$gift_row->eg_event_gift."' ";
        //출석이벤트일때 년월 체크
        if( $event_row->e_division == "1" ) {
            if( empty($gift_row->eg_event_ym) ) {
                return false;
            }

            $ew_where_query .= "and left(ew_regdatetime, 6) = '" . $gift_row->eg_event_ym . "' ";
        }

        $addFormString = ' join member_tb on m_num = ew_member_num ';
        if($event_row->e_member_join_flag == 'Y'){
            $addFormString = '';
        }

        //당첨회원 추출
        $query = "
            select event_winner_tb.*
            from event_winner_tb
                {$addFormString}
            " . $ew_where_query . "
            order by ew_num asc
            limit 1
        ";

        $winner_row = $this->db->query($query)->row();
        if( empty($winner_row) ) {
            return false;
        }

        //당첨회원에게 발급
        $query = "
            update event_winner_tb
            set
                ew_gift = '" . $gift_row->eg_gift . "'
                , ew_state = '3'
                , ew_view_yn = 'Y'
            where
                ew_num = '" . $winner_row->ew_num . "'
        ";

        $this->db->query($query);

        //기프티콘 상태 변경
        $query = "
            update event_gift_tb
            set
                eg_member_num = '" . $winner_row->ew_member_num . "'
                , eg_issuedatetime = '" . current_datetime() . "'
                , eg_state = '2'
                , eg_event_ph = '" . $winner_row->ew_contact . "'
            where
                eg_num = '" . $gift_row->eg_num . "'
        ";
        $this->db->query($query);

        return true;
    }//end of _gift_issue()



    public function getEventInfo(){

        ajax_request_check();

        $aInput = array(    'e_num'     => $this->input->post('e_num')
                        ,   'e_code'    => $this->input->post('e_code')
                        ,   'call_type' => $this->input->post('call_type')
        );

        if($aInput['e_num']){
            $this->load->model("event_model");
            $event_row = $this->event_model->get_event_row($aInput['e_num']);
            $aInput['e_code'] = $event_row->e_code;
        }

        $addQueryString = '';
        if($aInput['call_type'] == 'event_active_ajax'){ //이벤트참여목록 에서 call 인경우 지난 기프티콘은 제외
            //$addQueryString .= " AND gift_ym >= DATE_FORMAT(NOW(), '%Y%m') ";
        }


        $sql =" SELECT * FROM event_gift_code_tb WHERE event_code = ? AND use_flag = 'Y' {$addQueryString} ";
        $oResult = $this->db->query($sql,array($aInput['e_code']));
        $aResult = $oResult->result_array();
        if(count($aResult) > 0){
            foreach ($aResult as $r) $aItem[$r['gift_code']] = $r['gift_name'];
        }else{
            $aItem = $this->config->item($event_row->e_code);
        }

        if(count($aItem) < 1) $aItem = array();

        result_echo_json(get_status_code('success'), '', true, '' , '' , json_encode_no_slashes($aItem));

    }


}//end of class Event_gift