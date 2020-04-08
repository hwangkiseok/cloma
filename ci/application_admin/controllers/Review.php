<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 댓글 관련 컨트롤러
 */
class Review extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('review_model');
        $this->load->model('point_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->review_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['ym']              = trim($this->input->post_get('ym', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['view_type']       = trim($this->input->post_get('view_type', true));
        $req['tb_num']          = trim($this->input->post_get('tb_num', true));
        $req['tb']              = trim($this->input->post_get('tb', true));
        $req['page']            = trim($this->input->post_get('page', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['ym']              = trim($this->input->post_get('ym', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['blind']           = trim($this->input->post_get('blind', true));
        $req['admin']           = trim($this->input->post_get('admin', true));
        $req['state']           = trim($this->input->post_get('state', true));
        $req['img_yn']          = trim($this->input->post_get('img_yn', true));

        $req['grade']           = trim($this->input->post_get('grade', true));
        $req['main_view']       = trim($this->input->post_get('main_view', true));
        $req['reward_yn']       = trim($this->input->post_get('reward_yn', true));
        $req['reward_type']     = trim($this->input->post_get('reward_type', true));
        $req['winner_mode']     = trim($this->input->post_get('winner_mode', true));
        $req['dateType']        = trim($this->input->post_get('dateType', true));
        $req['memo_yn']         = trim($this->input->post_get('memo_yn', true));
        $req['cs_help_yn']      = trim($this->input->post_get('cs_help_yn', true));
        
        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 댓글 목록
     */
    public function review_list() {
        //request
        $req = $this->_list_req();
        $req['pop'] = trim($this->input->post_get("pop", true));    //팝업여부(팝업일때 header, footer 필요)

        if( !empty($req['pop']) ) {
            $this->_header(true);
        }
        else {
            $this->_header();
        }

        $viewFile = "/review/review_list_v2";
        $this->load->view($viewFile, array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        if( !empty($req['pop']) ) {
            $this->_footer(true);
        }
        else {
            $this->_footer();
        }
    }//end of comment_list()

    /**
     * 댓글 목록 (Ajax)
     */
    public function review_list_ajax()
    {

        //request
        $req = $this->_list_req();

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array = array();
        $query_array['where'] = $req;
        if (!empty($req['sort_field']) && !empty($req['sort_type'])) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        if ($this->input->get_post('pop', true)) {
            $query_array['best_review'] = 'N';
        }

        //전체갯수
        $list_count = $this->review_model->get_review_list($query_array, "", "", true);

        $base_url = '/review/list_ajax/';

        //페이징
        $page_result = $this->_paging(array(
            "total_rows" => $list_count,
            //"base_url"      => "/review/list_ajax/" . $PGV,
            "base_url" => $base_url,
            "per_page" => $req['list_per_page'],
            "page" => $req['page'],
            "ajax" => true,
            //"sort"          => "reverse"
        ));

        //목록
        $review_list = $this->review_model->get_review_list($query_array, $page_result['start'], $page_result['limit']);

        if ($this->input->get_post('pop', true)) {
            // 베스트글 데이터
            $query_array['best_review'] = 'Y';
            $best_review_list_cnt = $this->review_model->get_review_list($query_array, "", "", true);
            $best_review_list = $this->review_model->get_review_list($query_array, "", "", false);
        } else {
            $best_review_list_cnt = 0;
        }

        //정렬
        $sort_array = array();
        $sort_array['table_num_name'] = array("asc", "sorting");
        $sort_array['re_name'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");
        $sort_array['re_member_num'] = array("asc", "sorting");
        $sort_array['re_blind'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");
        $sort_array['re_best_order'] = array("asc", "sorting");
        $sort_array['re_display_state'] = array("asc", "sorting");
        $sort_array['re_regdatetime'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $view_file = "/review/review_list_ajax_v2";


        $this->load->view($view_file, array(
            "req"           => $req,
            "GV"            => $GV,
            "PGV"           => $PGV,
            "sort_array"    => $sort_array,
            "list_count"    => $list_count,
            "list_per_page" => $req['list_per_page'],
            "page"          => $req['page'],
            "review_list"  => $review_list,
            "pagination"    => $page_result['pagination'],
            'best_review_list' => $best_review_list,
            'best_review_list_cnt' => $best_review_list_cnt,
        ));
    }//end of comment_list_ajax()

    /**
     * 댓글 등록
     */
    public function review_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/review/review_insert_pop", array(
            'req'           => $req,
            'list_url'      => $this->_get_list_url()
        ));
    }//end of comment_insert_pop()


    public function upload_review_img(){

        //이미지 경로arr
        $review_img_arr = array();

        if(count($_FILES['review_file']['name']) > 0) {


            foreach ($_FILES['review_file'] as $key => $row) {
                foreach ($row as $kkey => $val) {
                    $_FILES['review_file' . $kkey][$key] = $val;
                }
            }
            unset($_FILES['review_file']);

            $re_image_path_web = $this->config->item('review_img_path_web') . "/" . date("Y") . "/" . date("md");
            $re_image_path = $this->config->item('review_img_path') . "/" . date("Y") . "/" . date("md");
            create_directory($re_image_path);

            foreach ($_FILES as $key => $FILE) {

                $rotation_angle = '';

                $config = array();
                $config['upload_path'] = $re_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('image_lib');
                $this->load->library('upload');

                $this->upload->initialize($config);

                if ($this->upload->do_upload($key)) {

                    //업로드 이미지정보
                    $review_image_data_array = $this->upload->data();
                    $aFileNM = explode('.',$review_image_data_array['file_name']);


                    //zsView($review_image_data_array);

                    if($aFileNM[1] == 'jpg' || $aFileNM[1] == 'jpeg' || $aFileNM[1] == 'tiff'){
                        $exif = @exif_read_data($review_image_data_array['full_path']);

                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 8:
                                    $rotation_angle = '90';
                                    break;
                                case 3:
                                    $rotation_angle = '180';
                                    break;
                                case 6:
                                    $rotation_angle = '-90';
                                    break;
                            }
                        }

                    }

                    if ($rotation_angle != '') {

                        $rotate_config = array();
                        $rotate_config['source_image'] = $review_image_data_array['full_path'];
                        $rotate_config['rotation_angle'] = $rotation_angle;

                        $this->image_lib->initialize($rotate_config);

                        if (!$this->image_lib->rotate()) {
                            $rResult = array('success' => false, 'msg' => '사진 회전 에러 :: ' . strip_tags($this->image_lib->display_errors()));
                            return $rResult;
                        }

                    }

                    if($aFileNM[1] == 'jpg' || $aFileNM[1] == 'jpeg' || $aFileNM[1] == 'tiff'){
                        $exif2 = @exif_read_data($review_image_data_array['full_path']);
                    }
                    if ($exif2['COMPUTED']['Width'] > 720) { //720px 이상인경우 리사이즈

                        $resize_config = array();
                        $resize_config['image_library'] = 'gd2';
                        $resize_config['source_image'] = $review_image_data_array['full_path'];
                        $resize_config['maintain_ratio'] = TRUE;
                        $resize_config['width'] = 720;

                        $this->image_lib->initialize($resize_config);

                        if (!$this->image_lib->resize()) {
                            $rResult = array('success' => false, 'msg' => '사진 리사이즈 에러 :: ' . strip_tags($this->image_lib->display_errors()));
                            return $rResult;
                        }

                    }

                    $review_img_arr[] = $re_image_path_web . '/' . $review_image_data_array['file_name'];

                } else {

                    $rResult = array('success' => false, 'msg' => '사진 업로드 에러 :: ' . strip_tags($this->upload->display_errors()));
                    return $rResult;
                } //upload end

            } // foreach $_FILES End
        }

        $rResult = array('success' => true, 'msg' => '' , 'data' => $review_img_arr);
        return $rResult;

    }

    /**
     * 댓글 등록 처리 (Ajax)
     */
    public function review_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "re_table"         => array("field" => "re_table", "label" => "구분", "rules" => "required|in_list[review]|".$this->default_set_rules),
            "re_table_num"     => array("field" => "re_table_num", "label" => "댓글 대상", "rules" => "required|is_natural|".$this->default_set_rules),
            "re_name"          => array("field" => "re_name", "label" => "작성자명", "rules" => "required|".$this->default_set_rules),
            "re_display_state" => array("field" => "re_display_state", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string('comment_display_state') ."]|".$this->default_set_rules),
            "re_recommend"      => array("field" => "re_recommend", "label" => "상품상세 노출여부", "rules" => "required|in_list[Y,N]|".$this->default_set_rules),
            "re_content"       => array("field" => "re_content", "label" => "내용", "rules" => "required|".$this->default_set_rules),
            "re_grade"          => array("field" => "re_grade", "label" => "상품만족도", "rules" => "required|in_list[A,B,C]|".$this->default_set_rules)
        );

        $aImg = $this->upload_review_img();

        if($aImg['success'] == false){
            result_echo_json(get_status_code('error'), $aImg['msg'], true, 'alert');
            exit;
        }

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $re_table          = trim($this->input->post("re_table", true));
            $re_table_num      = trim($this->input->post("re_table_num", true));
            $re_name           = trim($this->input->post("re_name", true));
            $re_profile_img    = trim($this->input->post("re_profile_img", true));
            $re_display_state  = trim($this->input->post("re_display_state", true));
            $re_recommend      = trim($this->input->post("re_recommend", true));
            $re_content        = trim($this->input->post("re_content", true));
            $re_grade          = trim($this->input->post("re_grade", true));

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['re_table'] = $re_table;
                $query_data['re_table_num'] = $re_table_num;
                $query_data['re_admin'] = "Y";
                $query_data['re_name'] = $re_name;
                $query_data['re_profile_img'] = $re_profile_img;
                $query_data['re_display_state'] = $re_display_state;
                $query_data['re_recommend'] = $re_recommend;
                $query_data['re_grade'] = $re_grade;
                $query_data['re_img'] = count($aImg['data']) > 0 ? json_encode_no_slashes($aImg['data']):'';

                $query_data['re_content'] = $re_content;

                if( $this->review_model->insert_review($query_data) ) {
                    //댓글 갯수 업데이트
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
    }//end of comment_insert_proc()
    

    /**
     * 댓글 수정
     */
    public function review_update_pop() {
        //request
        $req = $this->_list_req();
        $req['re_num'] = trim($this->input->post_get("re_num", true));
        $req['re_admin'] = trim($this->input->post_get("re_admin", true));

        //댓글 정보
        $review_row = $this->review_model->get_review_row($req['re_num']);
        if( empty($review_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        $this->load->view("/review/review_update_pop", array(
            'req'           => $req,
            'list_url'      => $this->_get_list_url(),
            'review_row'   => $review_row
        ));
    }//end of comment_insert_pop()

    /**
     * 댓글 수정 처리 (Ajax)
     */
    public function review_update_proc() {
        ajax_request_check();

        //request
        $req['re_num'] = trim($this->input->post_get("re_num", true));

        //댓글 정보
        $review_row = $this->review_model->get_review_row($req['re_num']);
        if( empty($review_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        $this->load->library('form_validation');

        $re_name_set_rules = $this->default_set_rules;
        $re_content_set_rules = $this->default_set_rules;
        if( $review_row->re_admin == "Y" ) {
            $re_name_set_rules .= "|required";
            $re_content_set_rules .= "|required";
        }

        $aImg = $this->upload_review_img();
        if($aImg['success'] == false){
            result_echo_json(get_status_code('error'), $aImg['msg'], true, 'alert');
            exit;
        }else{

            $tmp_re_img = $review_row->re_img == '' ? array() : json_decode($review_row->re_img);
            if(count($tmp_re_img) > 0 ) $aImg['data'] = array_merge($tmp_re_img,$aImg['data']);

        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "re_num"           => array("field" => "re_num", "label" => "리뷰번호", "rules" => "required|is_natural|" . $this->default_set_rules),
            "re_name"          => array("field" => "re_name", "label" => "작성자명", "rules" => $re_name_set_rules),
            "re_blind"         => array("field" => "re_blind", "label" => "참고", "rules" => "required|in_list[" . get_config_item_keys_string('comment_blind') ."]|".$this->default_set_rules),/*블라인드*/
            "re_blind_memo"    => array("field" => "re_blind_memo", "label" => "참고 메모", "rules" => "max_length[200]" . $this->default_set_rules),/*블라인드*/
            "re_display_state" => array("field" => "re_display_state", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string('comment_display_state') ."]|".$this->default_set_rules),
            "re_content"       => array("field" => "re_content", "label" => "내용", "rules" => $re_content_set_rules),
            "re_recommend"      => array("field" => "re_display_state", "label" => "노출여부", "rules" => "required|in_list[Y,N]|".$this->default_set_rules),
            "re_grade"          => array("field" => "re_grade", "label" => "상품만족도", "rules" => "in_list[A,B,C]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $re_num            = trim($this->input->post("re_num", true));
            $re_name           = trim($this->input->post("re_name", true));
            $re_blind          = trim($this->input->post("re_blind", true));
            $re_blind_memo     = trim($this->input->post("re_blind_memo", true));
            $re_display_state  = trim($this->input->post("re_display_state", true));
            $re_content        = trim($this->input->post("re_content", true));
            $re_recommend        = trim($this->input->post("re_recommend", true));
            $re_grade           = trim($this->input->post("re_grade", true));

            if( empty($form_error_array) ) {
                $query_data = array();
                if( $review_row->re_admin == "Y" ) {
                    $query_data['re_name'] = $re_name;
                }
                $query_data['re_blind'] = $re_blind;
                $query_data['re_blind_memo'] = $re_blind_memo;
                $query_data['re_display_state'] = $re_display_state;
                $query_data['re_recommend'] = $re_recommend;
                $query_data['re_img'] = count($aImg['data']) > 0 ? json_encode_no_slashes($aImg['data']):'';

                if( $review_row->re_admin == "Y" ) {
                    $query_data['re_content'] = $re_content;
                    $query_data['re_grade'] = $re_grade;
                }


                if( $re_blind == "Y" ) {
                    $query_data['re_blind_regdatetime'] = current_datetime();
                }
                else {
                    $query_data['re_blind_regdatetime'] = "";
                }


                if( $this->review_model->update_review($re_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of comment_update_proc()


    public function review_img_delete(){

        ajax_request_check();

        $aInput = array( 're_num'   => $this->input->post('re_num')
                        ,'img_seq'  => $this->input->post('img_seq')
        );

        $oResult = $this->review_model->get_review_row($aInput['re_num']);

        $tmp_re_img = json_decode($oResult->re_img);
        $delete_img_path_web = $tmp_re_img[$aInput['img_seq']-1];
        $delete_img_path =  DOCROOT.$delete_img_path_web;

        unset($tmp_re_img[$aInput['img_seq']-1]);

        $re_img= array();
        foreach ($tmp_re_img as $item) {
            $re_img[] = $item;
        }

        $aSubInput = array('re_img' => json_encode_no_slashes($re_img) ) ;
        $bResult = $this->review_model->update_review($aInput['re_num'],$aSubInput);

        if($bResult){
            @unlink($delete_img_path);
            $rResult = array('success' => true , 'msg' => '');
        } else {
            $rResult = array('success' => false , 'msg' => '삭제 실패');
        }

        echo json_encode($rResult);
        exit;

    }

    public function setReviewFlag(){

        $review_event_num = 28;
        $set_winner_cnt = 10; //한달에 정해진 당첨자 수

        $aInput = array(    're_num'   => $this->input->post('re_num')
                        ,   'setFlag'  => $this->input->post('setFlag')
                        ,   'fd'       => $this->input->post('fd')
        );

        $arrayParams = array($aInput['fd'] => $aInput['setFlag']);

        if(empty($arrayParams['re_winner_yn']) == false){

            {//기프티콘 관리

                $sql = "SELECT * 
                        FROM event_gift_tb 
                        WHERE eg_event_num = {$review_event_num}
                        AND eg_state = 1 
                        ORDER BY eg_num DESC 
                        LIMIT 1 ; ";
                $oResult = $this->db->query($sql);
                $aGiftInfo = $oResult->row_array();

                if(empty($aGiftInfo) == true){ // 당첨 처리시 지급할 기프티콘이 없는경우
                    $rResult = array('success' => false , 'msg' => '당첨 처리시에는 발급가능한 기프티콘이 존재해야합니다.');
                    echo json_encode($rResult);
                    exit;
                }

            }

            {//당첨자 연락처 확인
                $o_re_info  = $this->review_model->get_review_row($aInput['re_num']);
                $winner_ph = $o_re_info->m_authno?$o_re_info->m_authno:$o_re_info->m_order_phone;
                if(empty($winner_ph) == true){
                    $rResult = array('success' => false , 'msg' => '선택하신 리뷰어의 연락처가 없습니다.');
                    echo json_encode($rResult);
                    exit;
                }

            }

            {//당첨 년월 셋팅

                if($aInput['setFlag'] == 'Y'){ //당첨일

                    {//인원수 제한
                        $arrayParams['re_winner_date'] = $o_re_info->winner_date;
                        //php 에서는 윤달에 버그가 있어 review_row 가져올시 sql에서 처리
                        $sql        = "SELECT COUNT(*) AS cnt FROM review_tb WHERE re_winner_date = '{$arrayParams['re_winner_date']}' ; ";
                        $oResult    = $this->db->query($sql);
                        $curr_month_winner_cnt = $oResult->row_array();

                        if($curr_month_winner_cnt['cnt'] > $set_winner_cnt){
                            $rResult = array('success' => false , 'msg' => "포토리뷰 이벤트 당첨자는 {$set_winner_cnt}명이상 지정할 수 없습니다.");
                            echo json_encode($rResult);
                            exit;
                        }

                    }

                    {//같은 회원번호를 가진 대상이 있는 경우

                        $sql        = "SELECT COUNT(*) AS cnt FROM review_tb WHERE re_member_num = '{$o_re_info->re_member_num}' AND re_winner_date = '{$arrayParams['re_winner_date']}' ; ";
                        $oResult    = $this->db->query($sql);
                        $curr_month_winner_member_cnt = $oResult->row_array();

                        if($curr_month_winner_member_cnt['cnt'] > 0){
                            $rResult = array('success' => false , 'msg' => "해당 참여자는 이미 같은달에 당첨내역이 있습니다.");
                            echo json_encode($rResult);
                            exit;
                        }

                    }

                }else{//비당첨
                    $arrayParams['re_winner_date'] = '';
                }

            }

        }

        $bResult = $this->review_model->update_review($aInput['re_num'],$arrayParams);

        if ($aInput['fd'] == 're_recommend') {
            $this->review_model->updateReviewSort($aInput);
        }
        //블라인드 수정일때
        else if ($aInput['fd'] == 're_blind') {
            $re_row  = $this->review_model->get_review_row($aInput['re_num']);

            //상품 리뷰 갯수 업데이트
            $this->load->model('product_model');
            $this->product_model->update_prdouct_review_count($re_row->re_table_num);
        }

        if($bResult){

            if(empty($arrayParams['re_recommend']) == false){ //추천 리뷰인경우 리뷰이미지 순서 재정립

                $o_re_info = $this->review_model->get_review_row($aInput['re_num']);
                //$this->review_model->setReArrange($o_re_info->re_table_num);

            }else if(empty($arrayParams['re_winner_yn']) == false){ //리뷰이벤트 당첨처리

                $this->load->model('event_model');
                $this->load->model('event_active_model');

                $aInputSub1 = array(
                    'cel_tel'   => ph_slice($winner_ph)
                ,   'm_num'     => $o_re_info->m_num
                ,   'set_winner_date' => $o_re_info->winner_date
                );

                $o_event_info   = $this->event_model->get_event_row($review_event_num);
                $aInputSub2     = array(
                  'e_num'                   => $o_event_info->e_num
                , 'reg_date'                => date('YmdHis')
                , 'regist_winner_item'      => $aGiftInfo['eg_event_gift']
                , 'chk_review'              => 'Y'
                );

                $aInput2 = array(
                    'setData'   => $aInputSub1
                ,   'initInfo'  => $aInputSub2
                );

                $bOverlapChk = $this->event_active_model->overlapWinner($aInput2);

                $aInput2 = array(
                    'setData'   => array($aInputSub1)
                ,   'initInfo'  => $aInputSub2
                );

                if($aInput['setFlag'] == 'Y' && $bOverlapChk == false){ //당첨flag 및 당첨정보가 없음
                    $this->event_active_model->setWinner($aInput2);
                    $msg = '';
                }else if($aInput['setFlag'] == 'N' && $bOverlapChk == true){//당첨정보가 있음
                    $this->event_active_model->setCancelWinner($aInput2);
                    $msg = '처리완료';
                }

            }

            $rResult = array('success' => true , 'msg' => $msg);

        } else {
            $rResult = array('success' => false , 'msg' => '변경 실패');
        }

        echo json_encode($rResult);
        exit;


    }

    public function delete_proc(){

        ajax_request_check();

        //request
        $re_num     = $this->input->post('re_num', true);
        $review_row = $this->review_model->get_review_row($re_num);

        if(empty($review_row)){
            $rResult = array('success' => false , 'msg' => '삭제할 리뷰가 없습니다.');
            echo json_encode($rResult);
            exit;
        }

        $tmp_re_img = json_decode($review_row->re_img);

        //삭제
        if( $this->review_model->delete($re_num,$review_row->re_table_num) ) {

            if(isset($tmp_re_img)) {
                foreach ($tmp_re_img as $val) {
                    $delete_img_path = DOCROOT . $val;
                    @unlink($delete_img_path);
                }
            }

            $rResult = array('success' => true , 'msg' => '삭제완료');
            echo json_encode($rResult);
            exit;
        }
        else {
            $rResult = array('success' => false , 'msg' => '삭제 실패[DB]');
            echo json_encode($rResult);
            exit;
        }

    }

    public function review_setBestReviewSort()
    {
        $_id = $this->input->post_get('re_num');
        $_table_id = $this->input->post_get('re_table_num');
        $_index = $this->input->post_get('now_index');
        $_type = $this->input->post_get('type');

        $param = array(
            'id' => $_id,
            'table_id' => $_table_id,
            'index' => $_index,
            'type' => $_type
        );

        $result = $this->review_model->setBestReviewSort($param);

        if($result) {
            $rResult = array('success' => true , 'msg' => '수정완료');
        } else {
            $rResult = array('success' => false , 'msg' => '수정 실패[DB]');
        }
        echo json_encode($rResult);
        
    }

}//end of class Comment