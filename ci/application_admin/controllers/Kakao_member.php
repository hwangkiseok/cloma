<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 회원 관련 컨트롤러
 */
class Kakao_member extends A_Controller {

    var $list_per_page = 20;
    var $default_set_rules = "trim|xss_clean|prep_for_form|strip_tags";

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('kakao_member_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->kakao_member_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['state']           = trim($this->input->post_get('state', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['excel']           = trim($this->input->post_get('excel', true));  //엑셀다운로드 여부


        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 회원 목록
     */
    public function kakao_member_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view('/kakao_member/kakao_member_list', array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of kakao_member_list()

    /**
     * 회원 목록 (Ajax)
     */
    public function kakao_member_list_ajax() {
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

        //전체수
        $list_count = $this->kakao_member_model->get_kakao_member_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/kakao_member/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }

        //목록
        $kakao_member_list = $this->kakao_member_model->get_kakao_member_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['m_division'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");
        $sort_array['m_sns_site'] = array("asc", "sorting");
        $sort_array['m_join_path'] = array("asc", "sorting");
        $sort_array['m_logindatetime'] = array("asc", "sorting");
        $sort_array['m_regdatetime'] = array("asc", "sorting");
        $sort_array['m_state'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");
        $sort_array['m_email'] = array("asc", "sorting");
        $sort_array['m_nickname'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view('/kakao_member/kakao_member_list_ajax', array(
            'req'                   => $req,
            'GV'                    => $GV,
            'PGV'                   => $PGV,
            'sort_array'            => $sort_array,
            'list_count'            => $list_count,
            'list_per_page'         => $req['list_per_page'],
            'page'                  => $req['page'],
            'kakao_member_list'     => $kakao_member_list,
            'pagination'            => $page_result['pagination']
        ));
    }//end of kakao_member_list_ajax()


    /**
     * @date 20200504
     * @modify 황기석
     * @desc 엑셀다운로드 csv
     */
    public function kakao_member_list_excel() {

        error_reporting(-1);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);

        set_time_limit(0);
        ini_set("memory_limit", "1024M");


        $file_name = iconv("utf-8", "euc-kr", "채널_회원리스트_" . current_datetime() . ".csv");
        header("Content-Type:application/octet-stream");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Description: PHP5 Generated Data");
        header("Cache-Control: max-age=0");

        //request
        $req = $this->_list_req();

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;

        $kakao_member_list  = $this->kakao_member_model->get_kakao_member_list($query_array);

        if(1){

            $data_line = "전화번호,이름,생년월일,지역,성별,연령" ;

            foreach ($kakao_member_list as $key => $row) {

                if(empty($row['phone_number']) == false) $phone_number = ph_slice($row['phone_number']) ;
                else $phone_number = '';

                if(empty($row['gender']) == false)  $gender = $row['gender'] == 'male' ? '남' : '여';
                else $gender = '';

                if(empty($row['birthyear']) == false) $age = date('Y') - $row['birthyear'] + 1;
                else if(empty($row['age_range']) == false) $age = explode('~',$row['age_range'])[0];
                else $age = '';

                if(     empty($row['birthyear']) == false
                    &&  empty($row['birthday']) == false
                ) $birth = $row['birthyear'].'.'.(int)substr($row['birthday'],0,2).'.'.(int)substr($row['birthday'],2,2);
                else $birth = '';

                $data_line     .= "\n{$phone_number},{$row['nickname']},{$birth},,{$gender},{$age}";

            }

            echo iconv('utf-8','euc-kr',$data_line);

        }else{ //한글깨짐 처리가 안됨

            //lib
            require_once(APPPATH . "third_party/PHPExcel.php");

            $phpExcel = new PHPExcel();
            $phpExcel->getProperties()->setTitle("채널_회원리스트_".date('YmdHi'));


            $excelRow = $phpExcel->setActiveSheetIndex(0);
    //        $excelRow->setCellValue("A1", "No.");
    //        $excelRow->setCellValue("A1", "앱유저아이디");
            $excelRow->setCellValue("A1",  iconv('utf-8','euc-kr','가나다') );
            $excelRow->setCellValue("B1", "이름");
            $excelRow->setCellValue("C1", "생년월일");
            $excelRow->setCellValue("D1", "지역");
            $excelRow->setCellValue("E1", "성별");
            $excelRow->setCellValue("F1", "연령");



            $list_number = count($kakao_member_list);

            $i = 2;
            foreach ($kakao_member_list as $key => $row) {



                $excelRow = $phpExcel->setActiveSheetIndex(0);
    //            $excelRow->setCellValue("A$i", $row['sns_id']);
                $excelRow->setCellValue("A$i", ph_slice($row['phone_number']));
                $excelRow->setCellValue("B$i", $row['nickname']);
                $excelRow->setCellValue("C$i", '');
                $excelRow->setCellValue("D$i", '');
                $excelRow->setCellValue("E$i", $row['gender']=='male'?'남':'여');
                $excelRow->setCellValue("F$i", explode('~',$row['age_range'])[0]);
    //            $excelRow->setCellValue("I$i", ph_slice($row['phone_number']));


    //$excelRow->setCellValue("F$i", substr($row['birthday'],0,2).'월 '.substr($row['birthday'],2,2).'일');
    //$excelRow->setCellValue("D$i", $row['friend_flag']=='added'?'채널추가':'차단');
                $i++;
                $list_number--;
            }//end of foreach()
            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'CSV');
            $objWriter->setUseBOM(true);
            ob_end_clean();

            $objWriter->save('php://output');
        }
        exit;

    }

}//end of class kakao_member