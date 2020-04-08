<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 카테고리 MD 관련 컨트롤러
 */
class Category_md extends A_Controller {

    var $category_md_ver;

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('category_md_model');

    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->category_md_list();
    }//end of index()

    /**
     * 목록 request 배열
     * @return array
     */
    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['division']        = trim($this->input->post_get('division', true));
        $req['state']           = trim($this->input->post_get('state', true));
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
     * 카테고리 MD 목록
     */
    public function category_md_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/category_md/category_md_list", array(
            'req'               => $req,
            'list_per_page'     => $this->list_per_page
        ));

        $this->_footer();
    }//end of product_list()

    /**
     * 카테고리 MD 목록 (Ajax)
     */
    public function category_md_list_ajax() {
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
        $list_count = $this->category_md_model->get_category_md_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/category_md/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $md_list = $this->category_md_model->get_category_md_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['cmd_division'] = array("asc", "sorting");
        $sort_array['cmd_product_cate'] = array("asc", "sorting");
        $sort_array['cmd_name'] = array("asc", "sorting");
        $sort_array['cmd_order'] = array("asc", "sorting");
        $sort_array['cmd_state'] = array("asc", "sorting");
        $sort_array['cmd_regdatetime'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/category_md/category_md_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "md_list"           => $md_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of category_md_list_ajax()

    /**
     * 카테고리 MD 등록 팝업
     */
    public function category_md_insert_pop() {

        $product_cate_array = $this->config->item('product_cate_array');

        $this->load->view("/category_md/category_md_insert_pop", array(
            'product_cate_array'    => $product_cate_array
        ));
    }//end of category_md_insert_pop()

    /**
     * 카테고리 MD 등록 처리 (Ajax)
     */
    public function category_md_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        $cmd_image_set_rules = $this->default_set_rules;
        if( !isset($_FILES['cmd_image']['name']) || empty($_FILES['cmd_image']['name']) ) {
            //$cmd_image_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
//            "cmd_division" => array("field" => "cmd_division", "label" => "구분", "rules" => "required|in_list[" . get_config_item_keys_string("category_md_division") . "]|" . $this->default_set_rules),
            //"cmd_product_cate" => array("field" => "cmd_product_cate", "label" => "상품카테고리", "rules" => $this->default_set_rules),
            "cmd_name" => array("field" => "cmd_name", "label" => "카테고리명", "rules" => "required|" . $this->default_set_rules),
            "cmd_image" => array("field" => "cmd_image", "label" => "목록이미지", "rules" => $cmd_image_set_rules),
            "cmd_state" => array("field" => "cmd_state", "label" => "활성여부", "rules" => "required|in_list[" . get_config_item_keys_string("category_md_state") . "]|" . $this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cmd_division = $this->input->post('cmd_division', true);
            $cmd_product_cate = $this->input->post('cmd_product_cate');   //배열
            $cmd_name = $this->input->post('cmd_name', true);
            $cmd_state = $this->input->post('cmd_state', true);
            $cmd_image = "";

            if( $cmd_division == "4" && empty($cmd_product_cate) ) {
                $form_error_array['cmd_product_cate'] = "상품카테고리를 선택해주세요.";
            }
            if( $cmd_division != "4" ) {
                $cmd_product_cate = "";
            }

            //이미지 업로드
            $cmd_image_path_web = $this->config->item('category_md_image_path_web') . "/" . date("Y") . "/" . date("md");
            $cmd_image_path = $this->config->item('category_md_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($cmd_image_path);

            $config = array();
            $config['upload_path'] = $cmd_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
            $config['max_size']	= $this->config->item('upload_max_size');
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //이미지
            if( isset($_FILES['cmd_image']['name']) && !empty($_FILES['cmd_image']['name']) ) {
                if( $this->upload->do_upload('cmd_image') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_image = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //cdn purge
                    cdn_purge($cmd_image);
                }
                else {
                    $form_error_array['cmd_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            /**
             * @date 171103
             * @author 황기석
             * @desc 아이콘 이미지 추가/수정
             */
            if( isset($_FILES['cmd_icon']['name']) && !empty($_FILES['cmd_icon']['name']) ) {
                if( $this->upload->do_upload('cmd_icon') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_icon = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //cdn purge
                    cdn_purge($cmd_icon);
                }
                else {
                    $form_error_array['cmd_icon'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            if( isset($_FILES['cmd_icon2']['name']) && !empty($_FILES['cmd_icon2']['name']) ) {
                if( $this->upload->do_upload('cmd_icon2') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_icon2 = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //cdn purge
                    cdn_purge($cmd_icon2);
                }
                else {
                    $form_error_array['cmd_icon2'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()


            /**
             * @date 181115
             * @author 황기석
             * @desc 띠배너관련 추가
             */
            $cmd_zone_banner = '';
            if( isset($_FILES['cmd_zone_banner']['name']) && !empty($_FILES['cmd_zone_banner']['name']) ) {
                if( $this->upload->do_upload('cmd_zone_banner') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_zone_banner = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //cdn purge
                    cdn_purge($cmd_zone_banner);
                }
                else {
                    $form_error_array['cmd_zone_banner'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()
            $cmd_zone_banner_url = $this->input->post('cmd_zone_banner_url', true);





            /**/

            if( empty($form_error_array) ) {
                //등록
                $query_data = array();
                $query_data['cmd_division'] = $cmd_division;
                $query_data['cmd_product_cate'] = (!empty($cmd_product_cate)) ? implode(",", $cmd_product_cate) : "";
                $query_data['cmd_name'] = $cmd_name;
                $query_data['cmd_image'] = $cmd_image;
                $query_data['cmd_icon'] = $cmd_icon;
                $query_data['cmd_icon2'] = $cmd_icon2;

                $query_data['cmd_state'] = $cmd_state;

                /**
                 * @date 181115
                 * @author 황기석
                 * @desc 띠배너관련 추가
                 */
                $query_data['cmd_zone_banner'] = $cmd_zone_banner;
                $query_data['cmd_zone_banner_url'] = $cmd_zone_banner_url;
                /**/


                if( $this->category_md_model->insert_category_md($query_data) ) {
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
    }//end of category_md_insert_proc()

    /**
     * 카테고리 MD 수정 팝업
     */
    public function category_md_update_pop() {
        //reqeust
        $cmd_num = $this->input->post_get("cmd_num", true);
        if( empty($cmd_num) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_id"), true, "alert");
        }

        $category_md_row = $this->category_md_model->get_category_md_row(array('cmd_num' => $cmd_num));
        if( empty($category_md_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        $product_cate_array = $this->config->item('product_cate_array');

        $this->load->view("/category_md/category_md_update_pop", array(
            'category_md_row'       => $category_md_row,
            'product_cate_array'    => $product_cate_array
        ));
    }//end of category_md_update_pop()

    /**
     * 카테고리 MD 수정 처리 (Ajax)
     */
    public function category_md_update_proc() {
        ajax_request_check();

        //reqeust
        $cmd_num = $this->input->post("cmd_num", true);
        if( empty($cmd_num) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_id"), true, "alert");
        }

        $category_md_row = $this->category_md_model->get_category_md_row(array('cmd_num' => $cmd_num));
        if( empty($category_md_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        $this->load->library('form_validation');

        $cmd_image_set_rules = $this->default_set_rules;
        if( empty($category_md_row->cmd_image) && (!isset($_FILES['cmd_image']['name']) || empty($_FILES['cmd_image']['name'])) ) {
            //$cmd_image_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "cmd_division" => array("field" => "cmd_division", "label" => "구분", "rules" => "required|in_list[" . get_config_item_keys_string("category_md_division") . "]|" . $this->default_set_rules),
            //"cmd_product_cate" => array("field" => "cmd_product_cate", "label" => "상품카테고리", "rules" => $this->default_set_rules),
            "cmd_name" => array("field" => "cmd_name", "label" => "카테고리명", "rules" => "required|" . $this->default_set_rules),
            "cmd_image" => array("field" => "cmd_image", "label" => "목록이미지", "rules" => $cmd_image_set_rules),
            "cmd_state" => array("field" => "cmd_state", "label" => "활성여부", "rules" => "required|in_list[" . get_config_item_keys_string("category_md_state") . "]|" . $this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cmd_division = $this->input->post('cmd_division', true);
            $cmd_product_cate = $this->input->post('cmd_product_cate', true);   //배열
            $cmd_name = $this->input->post('cmd_name', true);
            $cmd_state = $this->input->post('cmd_state', true);
            $cmd_image = "";

            if( $cmd_division == "4" && empty($cmd_product_cate) ) {
                $form_error_array['cmd_product_cate'] = "상품카테고리를 선택해주세요.";
            }
            if( $cmd_division != "4" ) {
                $cmd_product_cate = "";
            }

            //이미지 수정 업로드
            $cmd_image_path_web = $this->config->item('category_md_image_path_web') . "/" . date("Y") . "/" . date("md");
            $cmd_image_path = $this->config->item('category_md_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($cmd_image_path);

            $config = array();
            $config['upload_path'] = $cmd_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
            $config['max_size']	= $this->config->item('upload_max_size');
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //이미지 수정
            if( isset($_FILES['cmd_image']['name']) && !empty($_FILES['cmd_image']['name']) ) {
                if( $this->upload->do_upload('cmd_image') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_image = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //기존 파일 삭제
                    file_delete(1, $category_md_row->cmd_image, DOCROOT);

                    //cdn purge
                    cdn_purge($cmd_image);
                }
                else {
                    $form_error_array['cmd_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()



            /**
             * @date 171103
             * @author 황기석
             * @desc 아이콘 이미지 추가/수정
             */
            if( isset($_FILES['cmd_icon']['name']) && !empty($_FILES['cmd_icon']['name']) ) {
                if( $this->upload->do_upload('cmd_icon') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_icon = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //기존 파일 삭제
                    file_delete(1, $category_md_row->cmd_icon, DOCROOT);

                    //cdn purge
                    cdn_purge($cmd_icon);
                }
                else {
                    $form_error_array['cmd_icon'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            if( isset($_FILES['cmd_icon2']['name']) && !empty($_FILES['cmd_icon2']['name']) ) {
                if( $this->upload->do_upload('cmd_icon2') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_icon2 = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //기존 파일 삭제
                    file_delete(1, $category_md_row->cmd_icon2, DOCROOT);

                    //cdn purge
                    cdn_purge($cmd_icon2);
                }
                else {
                    $form_error_array['cmd_icon2'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            /**
             * @date 181115
             * @author 황기석
             * @desc 띠배너관련 추가
             */
            if( isset($_FILES['cmd_zone_banner']['name']) && !empty($_FILES['cmd_zone_banner']['name']) ) {
                if( $this->upload->do_upload('cmd_zone_banner') ){
                    $upload_data_array = $this->upload->data();
                    $cmd_zone_banner = $cmd_image_path_web . "/" . $upload_data_array['file_name'];

                    //기존 파일 삭제
                    file_delete(1, $category_md_row->cmd_zone_banner, DOCROOT);

                    //cdn purge
                    cdn_purge($cmd_zone_banner);
                }
                else {
                    $form_error_array['cmd_zone_banner'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()
            $cmd_zone_banner_url = $this->input->post('cmd_zone_banner_url', true);

            /**/

            if( empty($form_error_array) ) {
                //수정
                $query_data = array();
                $query_data['cmd_division'] = $cmd_division;
                $query_data['cmd_product_cate'] = (!empty($cmd_product_cate)) ? implode(",", $cmd_product_cate) : "";
                $query_data['cmd_name'] = $cmd_name;
                if( !empty($cmd_image) ) {
                    $query_data['cmd_image'] = $cmd_image;
                }
                if( !empty($cmd_icon) ) {
                    $query_data['cmd_icon'] = $cmd_icon;
                }
                if( !empty($cmd_icon2) ) {
                    $query_data['cmd_icon2'] = $cmd_icon2;
                }

                /**
                 * @date 181115
                 * @author 황기석
                 * @desc 띠배너관련 추가
                 */
                if( !empty($cmd_zone_banner) ) {
                    $query_data['cmd_zone_banner'] = $cmd_zone_banner;
                }
                $query_data['cmd_zone_banner_url'] = $cmd_zone_banner_url;
                /**/

                $query_data['cmd_state'] = $cmd_state;

                if( $this->category_md_model->update_category_md($cmd_num, $query_data) ) {
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
    }//end of category_md_update_proc()

    /**
     * 카테고리 MD 삭제 (Ajax)
     */
    public function category_md_delete_proc() {
        ajax_request_check();

        //request
        $cmd_num = $this->input->post_get('cmd_num', true);

        $category_md_row = $this->category_md_model->get_category_md_row(array('cmd_num' => $cmd_num));
        if( empty($category_md_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        //삭제
        if( $this->category_md_model->delete_category_md($cmd_num) ) {
            //이미지 삭제
            file_delete(1, $category_md_row->cmd_image, DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of category_md_delete_proc()

    /**
     * 카테고리 MD 순서 수정 (Ajax)
     */
    public function category_md_order_proc() {
        ajax_request_check();

        //reqeust
        $data = $this->input->post('data', true);       //배열 ([cmd_num] => order 형식)

        if( empty($data) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $data as $key => $value ) {
            $cmd_num = $key;
            $cmd_order = $value;

            $this->category_md_model->order_update_category_md($cmd_num, $cmd_order);
        }//end of foreach()

        result_echo_json(get_status_code('success'), '', true);
    }//end of category_md_order_proc()

    /**
     * 카테고리 MD 수정 토글
     */
    function category_md_update_toggle() {
        ajax_request_check();

        //request
        $req['cmd_num'] = $this->input->post_get('cmd_num', true);
        $req['fd'] = $this->input->post_get('fd', true);          //cmd_state


        //수정 가능 필드
        $allow_fds = array("cmd_state");

        if( !in_array($req['fd'], $allow_fds) ) {
            result_echo_json(get_status_code('error'), "", true);
        }

        $category_md_row = $this->category_md_model->get_category_md_row(array('cmd_num' => $req['cmd_num']));
        if( empty($category_md_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true);
        }

        $query_data = array();
        if( $category_md_row->{$req['fd']} == "Y" ) {
            $query_data[$req['fd']] = "N";
        }
        else {
            $query_data[$req['fd']] = "Y";
        }

        if( $this->category_md_model->update_category_md($category_md_row->cmd_num, $query_data) ) {
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_unknown'), true);
        }
    }//end of category_md_update_toggle()

}//end of class Category_md