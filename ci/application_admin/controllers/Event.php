<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 이벤트 관련 컨트롤러
 */
class Event extends A_Controller {

    public function __construct() {
        parent::__construct();

        $this->cur_ym = date("Ym", time());
        $this->cur_m = date("m", time());
        $this->next_ym = date("Ym", strtotime("first day of +1 months"));
        $this->next_m = date("m", strtotime("first day of +1 months"));

        //model
        $this->load->model('event_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->event_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['div']             = trim($this->input->post_get('div', true));
        $req['pro_state']       = trim($this->input->post_get('pro_state', true));
        $req['dis_state']       = trim($this->input->post_get('dis_state', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 이벤트 목록
     */
    public function event_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/event/event_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of event_list()

    /**
     * 이벤트 목록 (Ajax)
     */
    public function event_list_ajax() {
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
        $list_count = $this->event_model->get_event_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/event/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $event_list = $this->event_model->get_event_list($query_array, $page_result['start'], $page_result['limit']);

        foreach ($event_list as $key => $row) {

            if($row->e_num == 16){

                $oResult = $this->db->query('SELECT COUNT(*) AS CNT FROM event_megashow_tk_tb');
                $aResult = $oResult->row_array('CNT');
                $row->join_cnt = $aResult['CNT'];

            }else{
                $row->join_cnt = 0;
            }


            if( $row->e_rep_image_type == 1 ) {
                $e_rep_image_array = json_decode($row->e_rep_image, true);
                $row->e_rep_image = $e_rep_image_array[1];
            }
            else if( $row->e_rep_image_type == 2 ) {
                $e_rep_image_ym_array = json_decode($row->e_rep_image_ym, true);
                $row->e_rep_image = $e_rep_image_ym_array[$this->cur_ym];
            }
        }//end of foreach()

        //정렬
        $sort_array = array();
        $sort_array['e_division'] = array("asc", "sorting");
        $sort_array['e_subject'] = array("asc", "sorting");
        $sort_array['e_termlimit_yn'] = array("asc", "sorting");
        $sort_array['e_termlimit_datetime1'] = array("asc", "sorting");
        $sort_array['e_termlimit_datetime2'] = array("asc", "sorting");
        $sort_array['e_regdatetime'] = array("asc", "sorting");
        $sort_array['e_proc_state'] = array("asc", "sorting");
        $sort_array['e_display_state'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/event/event_list_ajax", array(
            'req'           => $req,
            'GV'            => $GV,
            'PGV'           => $PGV,
            'sort_array'    => $sort_array,
            'list_count'    => $list_count,
            'list_per_page' => $req['list_per_page'],
            'page'          => $req['page'],
            'event_list'    => $event_list,
            'pagination'    => $page_result['pagination']
        ));
    }//end of event_list_ajax()

    /**
     * 이벤트 추가 (이벤트)
     */
    public function event_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/event/event_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url(),
            'cur_ym'    => $this->cur_ym,
            'cur_m'     => $this->cur_m,
            'next_ym'   => $this->next_ym,
            'next_m'    => $this->next_m
        ));
    }//end of event_insert_pop()

    /**
     * 이벤트 추가 처리 (Ajax)
     */
    public function event_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //set rules
        $e_termlimit_datetime1_set_rules = $this->default_set_rules;
        if( $this->input->post('e_termlimit_yn', true) == 'Y' ) {
            $e_termlimit_datetime1_set_rules .= "|required";
        }
        $e_termlimit_datetime2_set_rules = $this->default_set_rules;
        if( $this->input->post('e_termlimit_yn', true) == 'Y' ) {
            $e_termlimit_datetime2_set_rules .= "|required";
        }

        $e_rep_image_set_rules = $this->default_set_rules;
        $e_rep_image_ym_1_set_rules = $this->default_set_rules;
        $e_rep_image_ym_2_set_rules = $this->default_set_rules;

        //대표이미지
        if( $this->input->post('e_rep_image_type', true) == 1 ) {
            if( !isset($_FILES['e_rep_image']['name']) || empty($_FILES['e_rep_image']['name']) ) {
                $e_rep_image_set_rules .= "|required";
            }
        }
        //월별대표이미지
        else if( $this->input->post('e_rep_image_type', true) == 2 ) {
            if( !isset($_FILES['e_rep_image_ym_1']['name']) || empty($_FILES['e_rep_image_ym_1']['name']) ) {
                $e_rep_image_ym_1_set_rules .= "|required";
            }
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "e_division" => array("field" => "e_division", "label" => "이벤트종류", "rules" => "required|in_list[".get_config_item_keys_string("event_division")."]|".$this->default_set_rules),
            "e_code" => array("field" => "e_code", "label" => "이벤트코드", "rules" => "max_length[30]|".$this->default_set_rules),
            "e_subject" => array("field" => "e_subject", "label" => "이벤트제목", "rules" => "required|".$this->default_set_rules),
            "e_content" => array("field" => "e_content", "label" => "이벤트내용", "rules" => "required"),
            "e_termlimit_yn" => array("field" => "e_termlimit_yn", "label" => "노출기간사용여부", "rules" => "required|in_list[".get_config_item_keys_string("event_termlimit_yn")."]|".$this->default_set_rules),
            "e_termlimit_datetime1" => array("field" => "e_termlimit_datetime1", "label" => "이벤트시작일", "rules" => $e_termlimit_datetime1_set_rules),
            "e_termlimit_datetime2" => array("field" => "e_termlimit_datetime2", "label" => "이벤트종료일", "rules" => $e_termlimit_datetime2_set_rules),
            //"e_attend_day" => array("field" => "e_attend_day", "label" => "연속출석일", "rules" => "is_natural|".$this->default_set_rules),
            "e_rep_image_type" => array("field" => "e_rep_image_type", "label" => "대표이미지타입", "rules" => "required|in_list[".get_config_item_keys_string("event_rep_image_type")."]|".$this->default_set_rules),
            "e_rep_image" => array("field" => "e_rep_image", "label" => "대표이미지", "rules" => $e_rep_image_set_rules),
            "e_rep_image_ym_1" => array("field" => "e_rep_image_ym_1", "label" => "월별대표이미지(" . $this->cur_m . "월)", "rules" => $e_rep_image_ym_1_set_rules),
            "e_rep_image_ym_2" => array("field" => "e_rep_image_ym_2", "label" => "월별대표이미지(" . $this->next_m . "월)", "rules" => $e_rep_image_ym_2_set_rules),
            "e_after_type" => array("field" => "e_after_type", "label" => "참여후이동타입", "rules" => "in_list[".get_config_item_keys_string("event_after_type")."]|".$this->default_set_rules),
            "e_proc_state" => array("field" => "e_proc_state", "label" => "진행여부", "rules" => "required|in_list[".get_config_item_keys_string("event_proc_state")."]|".$this->default_set_rules),
            "e_display_state" => array("field" => "e_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("event_display_state")."]|".$this->default_set_rules),
            "e_alert_message" => array("field" => "e_alert_message", "label" => "Alert메시지", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $e_division = $this->input->post('e_division', true);
            $e_code = $this->input->post('e_code', true);
            $e_subject = $this->input->post('e_subject', true);
            $e_content = $this->input->post('e_content');
            $e_termlimit_yn = $this->input->post('e_termlimit_yn', true);
            $e_termlimit_datetime1 = $this->input->post('e_termlimit_datetime1', true);
            //$e_attend_day = $this->input->post('e_attend_day', true);
            $e_termlimit_datetime2 = $this->input->post('e_termlimit_datetime2', true);
            $e_rep_image_type = $this->input->post('e_rep_image_type', true);
            $e_rep_image = $this->input->post('e_rep_image', true);
            $e_after_type = $this->input->post('e_after_type', true);
            $e_proc_state = $this->input->post('e_proc_state', true);
            $e_display_state = $this->input->post('e_display_state', true);
            $e_alert_message = $this->input->post('e_alert_message', true);


            //이미지 업로드 경로 설정
            $image_path_web = $this->config->item('event_image_path_web') . "/" . date("Y") . "/" . date("md");
            $image_path = $this->config->item('event_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($image_path);

            $config = array();
            $config['upload_path'] = $image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = '5000';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            //대표이미지 업로드 (썸네일 생성)
            if( isset($_FILES['e_rep_image']['name']) && !empty($_FILES['e_rep_image']['name']) ) {
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('e_rep_image') ) {
                    $upload_data_array = $this->upload->data();
                    $image_array = create_thumb_image($upload_data_array, $image_path_web, $this->config->item('event_rep_image_size'), true);

                    if ( empty($image_array) || $image_array === false ) {
                        $form_error_array['e_rep_image'] = "대표이미지 썸네일 생성을 실패했습니다.";
                    }
                    else {
                        //$image_array[0] = $image_path_web . "/" . $upload_data_array['file_name'];    //원본유지일때
                        $image_array[0] = $image_array[1];  //원본삭제일때
                        $e_rep_image = json_encode_no_slashes($image_array);

                        //cdn purge
                        cdn_purge($image_array[0]);
                    }
                }
                else {
                    $form_error_array['e_rep_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            //월별대표이미지 업로드
            $e_rep_image_ym_array = array($this->cur_ym => "", $this->next_ym => "");

            //이번달
            if( isset($_FILES['e_rep_image_ym_1']['name']) && !empty($_FILES['e_rep_image_ym_1']['name']) ) {
                $this->upload->initialize($config);

                if( $this->upload->do_upload('e_rep_image_ym_1') ){
                    $upload_data_array = $this->upload->data();
                    $e_rep_image_ym_array[$this->cur_ym] = $image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0775);

                    //cdn purge
                    cdn_purge($e_rep_image_ym_array[$this->cur_ym]);
                }
                else {
                    $form_error_array['e_rep_image_ym_1'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }
            //다음달
            if( isset($_FILES['e_rep_image_ym_2']['name']) && !empty($_FILES['e_rep_image_ym_2']['name']) ) {
                $this->upload->initialize($config);

                if( $this->upload->do_upload('e_rep_image_ym_2') ){
                    $upload_data_array = $this->upload->data();
                    $e_rep_image_ym_array[$this->next_ym] = $image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0775);

                    //cdn purge
                    cdn_purge($e_rep_image_ym_array[$this->next_ym]);
                }
                else {
                    $form_error_array['e_rep_image_ym_2'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }



            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['e_division'] = $e_division;
                $query_data['e_code'] = $e_code;
                $query_data['e_rep_image_type'] = $e_rep_image_type;
                if( $e_rep_image_type == 1 ) {
                    $query_data['e_rep_image'] = $e_rep_image;
                }
                else if( $e_rep_image_type == 2 ) {
                    $query_data['e_rep_image_ym'] = json_encode_no_slashes($e_rep_image_ym_array);
                }
                $query_data['e_subject'] = $e_subject;
                $query_data['e_content'] = $e_content;
                $query_data['e_termlimit_yn'] = $e_termlimit_yn;
                $query_data['e_termlimit_datetime1'] = number_only($e_termlimit_datetime1);
                $query_data['e_termlimit_datetime2'] = number_only($e_termlimit_datetime2);
                //$query_data['e_attend_day'] = number_only($e_attend_day);
                $query_data['e_after_type'] = $e_after_type;
                $query_data['e_proc_state'] = $e_proc_state;
                $query_data['e_display_state'] = $e_display_state;
                $query_data['e_alert_message'] = $e_alert_message;

                if( $this->event_model->insert_event($query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of event_insert_proc()

    /**
     * 이벤트 수정
     */
    public function event_update_pop() {
        //request
        $req = $this->_list_req();
        $req['e_num'] = $this->input->post_get('e_num', true);

        //row
        $event_row = $this->event_model->get_event_row($req['e_num']);
        $event_row->e_attend_day_info_array = json_decode($event_row->e_attend_day_info, true);

        if( empty($event_row) ) {
            alert(lang('site_error_empty_data'));
        }

        //월별 대표이미지 배열
        $event_row->e_rep_image_ym_array = array();
        if( !empty($event_row->e_rep_image_ym) ) {
            $event_row->e_rep_image_ym_array = json_decode($event_row->e_rep_image_ym, true);
        }

        //상세이미지 배열
        $event_row->e_content_image_array = array();
        if( !empty($event_row->e_content_image) ) {
            $event_row->e_content_image_array = json_decode($event_row->e_content_image, true);
        }

        $addQueryString = '';
        if($event_row->e_content_type == 2) $addQueryString .= " AND gift_ym >= DATE_FORMAT(NOW(),'%Y%m') ";

        $sql = "SELECT * FROM event_gift_code_tb WHERE use_flag = 'Y' AND event_code = '".$event_row->e_code."' {$addQueryString} ORDER BY gift_ym , `sort`  ; ";
        $oResult = $this->db->query($sql);
        $aGiftCode = $oResult->result_array();

        //금월, 내월
        $cur_ym = date("Ym");                           //예:201604
        $cur_m = date("m");                             //예:04
        $next_ym = date("Ym", strtotime("first day of +1 months"));  //예:201605
        $next_m = date("m", strtotime("first day of +1 months"));    //예:05

        $this->load->view("/event/event_update_pop", array(
            'req'       => $req,
            'cur_ym'    => $this->cur_ym,
            'cur_m'     => $this->cur_m,
            'next_ym'   => $this->next_ym,
            'next_m'    => $this->next_m,
            'event_row' => $event_row,
            'aGiftCode' => $aGiftCode,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of event_update_pop()

    /**
     * 이벤트 수정 처리 (Ajax)
     */
    public function event_update_proc() {
        ajax_request_check();

        //request
        $req['e_num'] = $this->input->post_get('e_num', true);
        $req['e_rep_image_type'] = $this->input->post_get('e_rep_image_type', true);
        $req['e_content_type'] = $this->input->post_get('e_content_type', true);

        //금월, 내월
        $cur_ym = date("Ym");                           //예:201604
        $cur_m = date("m");                             //예:04
        $next_ym = date("Ym", strtotime("first day of +1 months"));  //예:201605
        $next_m = date("m", strtotime("first day of +1 months"));    //예:05

        //row
        $event_row = $this->event_model->get_event_row($req['e_num']);

        if( empty($event_row) ) {
            alert(lang('site_error_empty_data'));
        }
        $event_row->e_rep_image_ym_array = json_decode($event_row->e_rep_image_ym, true);
        $event_row->e_content_image_array = json_decode($event_row->e_content_image, true);

        $cur_ym_rep_image = (isset($event_row->e_rep_image_ym_array[$this->cur_ym]) && !empty($event_row->e_rep_image_ym_array[$this->cur_ym])) ? $event_row->e_rep_image_ym_array[$this->cur_ym] : "";
        $next_ym_rep_image = (isset($event_row->e_rep_image_ym_array[$this->next_ym]) && !empty($event_row->e_rep_image_ym_array[$this->next_ym])) ? $event_row->e_rep_image_ym_array[$this->next_ym] : "";

        $cur_ym_image = (isset($event_row->e_content_image_array[$cur_ym]) && !empty($event_row->e_content_image_array[$cur_ym])) ? $event_row->e_content_image_array[$cur_ym] : "";
        $next_ym_image = (isset($event_row->e_content_image_array[$next_ym]) && !empty($event_row->e_content_image_array[$next_ym])) ? $event_row->e_content_image_array[$next_ym] : "";


        //var_dump($event_row->e_content_image_array);
        //var_dump($event_row->e_content_image_array[$cur_ym]);
        //exit;

        $this->load->library('form_validation');

        //set rules
        $e_termlimit_datetime1 = $this->default_set_rules;
        if( $this->input->post('e_termlimit_yn', true) == 'Y' ) {
            $e_termlimit_datetime1 .= "|required";
        }
        $e_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('e_termlimit_yn', true) == 'Y' ) {
            $e_termlimit_datetime2 .= "|required";
        }

        $e_content_set_rules = "";
        $e_content_image_1_set_rules = $this->default_set_rules;
        $e_content_image_2_set_rules = $this->default_set_rules;

        //HTML
        if( $req['e_content_type'] == "1" ) {
            $e_content_set_rules = "required";
        }
        //이미지
        else if( $req['e_content_type'] == "2" ) {
            //금월은 필수
            if(
                (!isset($event_row->e_content_image_array[$cur_ym]) || empty($event_row->e_content_image_array[$cur_ym])) &&
                (!isset($_FILES['e_content_image_1']['name']) || empty($_FILES['e_content_image_1']['name']))
            ){
                $e_content_image_1_set_rules .= "|required";
            }
            //if( !isset($_FILES['e_content_image_2']['name']) || empty($_FILES['e_content_image_2']['name']) ) {
            //    $e_content_image_2_set_rules .= "|required";
            //}
        }

        $e_rep_image_set_rules = $this->default_set_rules;
        $e_rep_image_ym_1_set_rules = $this->default_set_rules;
        $e_rep_image_ym_2_set_rules = $this->default_set_rules;
        //대표이미지
        if( $req['e_rep_image_type'] == 1 ) {
            if(
                (!isset($event_row->e_rep_image) || empty($event_row->e_rep_image)) &&
                (!isset($_FILES['e_rep_image']['name']) || empty($_FILES['e_rep_image']['name']))
            ) {
                $e_rep_image_set_rules .= "|required";
            }
        }
        //월별대표이미지
        else if( $req['e_rep_image_type'] == 2 ) {
            $e_rep_image_ym_array = json_decode($event_row->e_rep_image_ym, true);

            if(
                ( !isset($e_rep_image_ym_array[$this->cur_ym]) || empty($e_rep_image_ym_array[$this->cur_ym]) ) &&
                ( !isset($_FILES['e_rep_image_ym_1']['name']) || empty($_FILES['e_rep_image_ym_1']['name']) )
            ) {
                $e_rep_image_ym_1_set_rules .= "|required";
            }
        }

        $e_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('e_termlimit_yn', true) == 'Y' ) {
            $e_termlimit_datetime2 .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "e_num" => array("field" => "e_num", "label" => "이벤트번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "e_division" => array("field" => "e_division", "label" => "이벤트종류", "rules" => "required|in_list[".get_config_item_keys_string("event_division")."]|".$this->default_set_rules),
            "e_code" => array("field" => "e_code", "label" => "이벤트코드", "rules" => "max_length[30]|".$this->default_set_rules),
            "e_subject" => array("field" => "e_subject", "label" => "이벤트제목", "rules" => "required|".$this->default_set_rules),
            "e_content" => array("field" => "e_content", "label" => "이벤트내용", "rules" => $e_content_set_rules),
            "e_termlimit_yn" => array("field" => "e_termlimit_yn", "label" => "노출기간사용여부", "rules" => "required|in_list[".get_config_item_keys_string("event_termlimit_yn")."]|".$this->default_set_rules),
            "e_termlimit_datetime1" => array("field" => "e_termlimit_datetime1", "label" => "이벤트시작일", "rules" => $e_termlimit_datetime1),
            "e_termlimit_datetime2" => array("field" => "e_termlimit_datetime2", "label" => "이벤트종료일", "rules" => $e_termlimit_datetime2),
            //"e_attend_day" => array("field" => "e_attend_day", "label" => "연속출석일", "rules" => "is_natural|".$this->default_set_rules),
            "e_rep_image_type" => array("field" => "e_rep_image_type", "label" => "대표이미지타입", "rules" => "required|in_list[".get_config_item_keys_string("event_rep_image_type")."]|".$this->default_set_rules),
            "e_rep_image" => array("field" => "e_rep_image", "label" => "대표이미지", "rules" => $e_rep_image_set_rules),
            "e_rep_image_ym_1" => array("field" => "e_rep_image_ym_1", "label" => "월별대표이미지(" . $this->cur_m . "월)", "rules" => $e_rep_image_ym_1_set_rules),
            "e_rep_image_ym_2" => array("field" => "e_rep_image_ym_2", "label" => "월별대표이미지(" . $this->next_m . "월)", "rules" => $e_rep_image_ym_2_set_rules),

            "e_content_type" => array("field" => "e_content_type", "label" => "상세내용타입", "rules" => "required|in_list[".get_config_item_keys_string("event_content_type")."]|".$this->default_set_rules),
            "e_content_image_1" => array("field" => "e_content_image_1", "label" => $cur_m . "월 상세이미지", "rules" => $e_content_image_1_set_rules),
            "e_content_image_2" => array("field" => "e_content_image_2", "label" => $next_m . "월 상세이미지", "rules" => $e_content_image_2_set_rules),
            "e_after_type" => array("field" => "e_after_type", "label" => "참여후이동타입", "rules" => "in_list[".get_config_item_keys_string("event_after_type")."]|".$this->default_set_rules),
            "e_proc_state" => array("field" => "e_proc_state", "label" => "진행여부", "rules" => "required|in_list[".get_config_item_keys_string("event_proc_state")."]|".$this->default_set_rules),
            "e_display_state" => array("field" => "e_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("event_display_state")."]|".$this->default_set_rules),
            "e_alert_message" => array("field" => "e_alert_message", "label" => "Alert메시지", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $e_num = $this->input->post('e_num', true);
            $e_division = $this->input->post('e_division', true);
            $e_code = $this->input->post('e_code', true);
            $e_subject = $this->input->post('e_subject', true);
            $e_content_type = $this->input->post('e_content_type', true);
            $e_content = $this->input->post('e_content');
            $e_termlimit_yn = $this->input->post('e_termlimit_yn', true);
            $e_termlimit_datetime1 = $this->input->post('e_termlimit_datetime1', true);
            $e_termlimit_datetime2 = $this->input->post('e_termlimit_datetime2', true);


            if($e_division == 3){ //매일룰렛이벤트 인경우 gift_code 도 업데이트

                $daliy_issue_cnt_arr = $this->input->post('daliy_issue_cnt');
                $bRet = false;
                foreach ($daliy_issue_cnt_arr as $key => $item) {
                    $val = $item ? $item : "0" ;
                    if($val == "0") $bRet = true;
                }

                if($bRet == false) result_echo_json(get_status_code('error'), '최소한 1개의 경품은 무제한(0)으로 셋팅을 해야합니다.', true, 'alert');

            }



            //$e_attend_day = $this->input->post('e_attend_day', true);

            //중복삭제, 오름차순정렬
            //array(3) {
            // [0]=> string(2) "20"
            // [1]=> string(2) "25"
            // [2]=> string(3) "all"
            //}
            //array(3) {
            // [0]=> string(2) "당첨"
            // [1]=> string(2) "응모"
            // [2]=> string(3) "응모"
            //}
            // array(3) {
            // [0]=> string(186) "축하합니다!! 누적 연속출석을 달성하셨습니다. 해당 이벤트 상품을 받으시려면, 달력에서 해당 날짜를 클릭하여 추가정보를 입력해주세요."
            // [1]=> string(189) "축하합니다!! 누적 연속출석을 달성하셨습니다. 해당 이벤트 상품에 응모하시려면, 달력에서 해당 날짜를 클릭하여 추가정보를 입력해주세요."
            // [2]=> string(189) "축하합니다!! 누적 연속출석을 달성하셨습니다. 해당 이벤트 상품에 응모하시려면, 달력에서 해당 날짜를 클릭하여 추가정보를 입력해주세요."
            //}

            $attend_day_array = $this->input->post('attend_day', true);
            $attend_day_btn_array = $this->input->post('attend_day_btn', true);
            $attend_day_msg_array = $this->input->post('attend_day_msg', true);

            //중복제거, 오름차순정렬
            $attend_day_array = array_unique($attend_day_array);
            asort($attend_day_array);

            $attend_day_info = array();
            $attend_day_info_json = "";
            $idx = 0;
            foreach ( $attend_day_array as $key => $day ) {
                if( empty($day) ) {
                    continue;
                }

                $info_array = array("day" => $day, "btn" => $attend_day_btn_array[$key], "msg" => $attend_day_msg_array[$key]);;

                if( $day == 99 ) {
                    $attend_day_info[99] = $info_array;
                }
                else {
                    $idx++;
                    $attend_day_info[$idx] = $info_array;
                }
            }//end of foreach()

            if( !empty($attend_day_info) ) {
                $attend_day_info_json = json_encode_no_slashes($attend_day_info);
            }

            $e_after_type = $this->input->post('e_after_type', true);
            $e_proc_state = $this->input->post('e_proc_state', true);
            $e_display_state = $this->input->post('e_display_state', true);
            $e_alert_message = $this->input->post('e_alert_message', true);

            $e_rep_image_type = $this->input->post('e_rep_image_type', true);
            $e_rep_image = $this->input->post('e_rep_image', true);

            //이미지 업로드 경로 설정
            $image_path_web = $this->config->item('event_image_path_web') . "/" . date("Y") . "/" . date("md");
            $image_path = $this->config->item('event_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($image_path);

            $config = array();
            $config['upload_path'] = $image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = '5000';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            //대표이미지 업로드 (썸네일 생성)
            if( isset($_FILES['e_rep_image']['name']) && !empty($_FILES['e_rep_image']['name']) ) {
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('e_rep_image') ) {
                    $upload_data_array = $this->upload->data();
                    $image_array = create_thumb_image($upload_data_array, $image_path_web, $this->config->item('event_rep_image_size'), true);

                    if ( empty($image_array) || $image_array === false ) {
                        $form_error_array['e_rep_image'] = "대표이미지 썸네일 생성을 실패했습니다.";
                    }
                    else {
                        //$image_array[0] = $image_path_web . "/" . $upload_data_array['file_name'];    //원본유지일때
                        $image_array[0] = $image_array[1];  //원본삭제일때
                        $e_rep_image = json_encode_no_slashes($image_array);

                        //기존 파일 삭제
                        file_delete(3, $event_row->e_rep_image, DOCROOT);

                        //cdn purge
                        cdn_purge($image_array[0]);
                    }
                }
                else {
                    $form_error_array['e_rep_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            //월별대표이미지 업로드
            $e_rep_image_ym_array = array($this->cur_ym => $cur_ym_rep_image, $this->next_ym => $next_ym_rep_image);

            //이번달
            if( isset($_FILES['e_rep_image_ym_1']['name']) && !empty($_FILES['e_rep_image_ym_1']['name']) ) {
                $this->upload->initialize($config);

                if( $this->upload->do_upload('e_rep_image_ym_1') ){
                    $upload_data_array = $this->upload->data();
                    $e_rep_image_ym_array[$this->cur_ym] = $image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0775);

                    //기존 이미지 삭제
                    file_delete(1, $cur_ym_rep_image, DOCROOT);

                    //cdn purge
                    cdn_purge($e_rep_image_ym_array[$this->cur_ym]);
                }
                else {
                    $form_error_array['e_rep_image_ym_1'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }
            //다음달
            if( isset($_FILES['e_rep_image_ym_2']['name']) && !empty($_FILES['e_rep_image_ym_2']['name']) ) {
                $this->upload->initialize($config);

                if( $this->upload->do_upload('e_rep_image_ym_2') ){
                    $upload_data_array = $this->upload->data();
                    $e_rep_image_ym_array[$this->next_ym] = $image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0775);

                    //기존 이미지 삭제
                    file_delete(1, $next_ym_rep_image, DOCROOT);

                    //cdn purge
                    cdn_purge($e_rep_image_ym_array[$this->next_ym]);
                }
                else {
                    $form_error_array['e_rep_image_ym_2'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            //상세 이미지
            $e_content_image_array = array($cur_ym => $cur_ym_image, $next_ym => $next_ym_image);

            $e_image_path_web = $this->config->item('event_image_path_web') . "/" . date("Y") . "/" . date("md");
            $e_image_path = $this->config->item('event_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($e_image_path);

            $config = array();
            $config['upload_path'] = $e_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']	= '5000';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //금월 상세 이미지
            if( isset($_FILES['e_content_image_1']['name']) && !empty($_FILES['e_content_image_1']['name']) ) {
                if( $this->upload->do_upload('e_content_image_1') ){
                    $upload_data_array = $this->upload->data();
                    $e_content_image_array[$cur_ym] = $e_image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0664);

                    //기존 파일 삭제
                    file_delete(1, $cur_ym_image, DOCROOT);

                    //cdn purge
                    cdn_purge($e_content_image_array[$this->cur_ym]);
                }
                else {
                    $form_error_array['e_content_image_1'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }
            //내월 상세 이미지
            if( isset($_FILES['e_content_image_2']['name']) && !empty($_FILES['e_content_image_2']['name']) ) {
                if( $this->upload->do_upload('e_content_image_2') ){
                    $upload_data_array = $this->upload->data();
                    $e_content_image_array[$next_ym] = $e_image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0664);

                    //기존 파일 삭제
                    file_delete(1, $next_ym_image, DOCROOT);

                    //cdn purge
                    cdn_purge($e_content_image_array[$this->next_ym]);
                }
                else {
                    $form_error_array['e_content_image_2'] = strip_tags($this->upload->display_errors());
                }//end of if()

                //기존 파일 삭제
                file_delete(1, $event_row->e_content_image_array[$next_ym], DOCROOT);
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['e_division'] = $e_division;
                $query_data['e_code'] = $e_code;
                $query_data['e_rep_image_type'] = $e_rep_image_type;
                if( $e_rep_image_type == "1" ) {
                    if ( !empty($e_rep_image) ) {
                        $query_data['e_rep_image'] = $e_rep_image;
                    }
                }
                else if( $e_rep_image_type == "2" ) {
                    $query_data['e_rep_image_ym'] = json_encode_no_slashes($e_rep_image_ym_array);
                }
                $query_data['e_subject'] = $e_subject;
                $query_data['e_content_type'] = $e_content_type;
                if( $req['e_content_type'] == "1" ) {
                    $query_data['e_content'] = $e_content;
                }
                else if( $req['e_content_type'] == "2" ) {
                    $query_data['e_content_image'] = json_encode_no_slashes($e_content_image_array);
                }
                $query_data['e_termlimit_yn'] = $e_termlimit_yn;
                $query_data['e_termlimit_datetime1'] = number_only($e_termlimit_datetime1);
                $query_data['e_termlimit_datetime2'] = number_only($e_termlimit_datetime2);
                //$query_data['e_attend_day'] = number_only($e_attend_day);
                $query_data['e_attend_day_info'] = $attend_day_info_json;
                $query_data['e_after_type'] = $e_after_type;
                $query_data['e_proc_state'] = $e_proc_state;
                $query_data['e_display_state'] = $e_display_state;
                $query_data['e_alert_message'] = $e_alert_message;


                if( $this->event_model->update_event($e_num, $query_data) ) {

                    if($e_division == 3){ //매일룰렛이벤트 인경우 gift_code 도 업데이트

                        $daliy_issue_cnt_arr = $this->input->post('daliy_issue_cnt');
                        $gift_weighted_arr = $this->input->post('gift_weighted_arr');


                        foreach ($daliy_issue_cnt_arr as $key => $item) { $item2 = $gift_weighted_arr[$key];
                            $val  = $item  ? $item : 0 ;
                            $val2 = $item2 ? $item2 : 0 ;

                            $sql = "UPDATE event_gift_code_tb SET daliy_issue_cnt = '{$val}' , gift_weighted = '{$val2}' WHERE seq = '{$key}' ; " ;
                            $this->db->query($sql);
                        }

                    }

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
    }//end of event_update_proc()

    /**
     * 상품 수정 토글
     */
    function event_update_toggle() {
        ajax_request_check();

        //request
        $req['e_num'] = trim($this->input->post_get('e_num', true));
        $req['fd'] = trim($this->input->post_get('fd', true));          //e_display_state, e_proc_state


        //수정 가능 필드
        $allow_fds = array('e_proc_state', 'e_display_state');

        if( !in_array($req['fd'], $allow_fds) ) {
            result_echo_json(get_status_code('error'), "", true);
        }

        $event_row = $this->event_model->get_event_row($req['e_num']);

        if( empty($event_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true);
        }

        $query_data = array();
        if( $event_row->{$req['fd']} == "Y" ) {
            $query_data[$req['fd']] = "N";
        }
        else {
            $query_data[$req['fd']] = "Y";
        }

        if( $this->event_model->update_event($event_row->e_num, $query_data, false) ) {
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_db'), true, 'alert');
        }
    }//end of event_update_toggle()

    /**
     * 이벤트 삭제 처리 (Ajax)
     */
    public function event_delete_proc() {
        ajax_request_check();

        //request
        $req['e_num'] = $this->input->post_get('e_num', true);

        //이벤트 정보
        $event_row = $this->event_model->get_event_row($req['e_num']);

        if( empty($event_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }
        //고정출석이벤트 삭제 불가
        if( $event_row->e_division == '1' ) {
            result_echo_json(get_status_code('error'), lang('site_error_default'), true, 'alert');
        }


        //이벤트 삭제
        if( $this->event_model->delete_event($req['e_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of event_delete_proc()

}//end of class event