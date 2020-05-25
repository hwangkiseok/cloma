<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 테스트용
 */
class Test extends M_Controller
{

    public $debug = true;
    public $curr_time;
    public $curr_time_i;
    public $curr_time_h;

    public function __construct()
    {
        parent::__construct();

        error_reporting(-1);
        ini_set('display_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);

        $allow_ip_array = array(
            "112.146.73.238" //사무실
        ,   "106.243.140.135"   //마포
        );

        if( array_search($this->input->ip_address(), $allow_ip_array) === false ) {
            redirect('/');
        }

    }//end of __construct()

    public function login()
    {

        $m_num = $this->uri->segment(3);

        if( empty($m_num) ) {
            exit;
        }

        $query = "select * from member_tb where m_num = '" . $this->db->escape_str($m_num) . "'";
        $member_row = $this->db->query($query)->row_array();

        if( empty($member_row) ) {
            exit;
        }

        set_login_session($member_row);

        alert("OK", "/");

    }//end of login()


//    public function excel(){
//        $this->_header();
//        $this->load->view('/test/excel' );
//        $this->_footer();
//    }
//
//    public function excel_proc(){
//
//        $arrayParams['filePath'] = $_FILES['excel']['tmp_name'];
//        $this->load->library("/MY_Excel",$arrayParams);
//        $aResult = $this->my_excel->getData();
//
//        foreach ($aResult as $k => $r) {
//
//
//            if($k > 1){
//
//                $sql = "UPDATE product_tb SET p_summary = '{$r['C']}' WHERE p_num = '{$r['A']}'; ";
//                $this->db->query($sql);
//
//            }
//
//        }
//
//        zsView('complete');
//
//        exit;
//
//    }

    public function update_detail(){



//        $sql = "
//SELECT *
//FROM product_tb
//WHERE p_detail REGEXP \"^[\\r\\n]\";
//
//        ";
//
//        zsView('a');
//        $oResult = $this->db->query($sql);
//        $aResult = $oResult->result_array();


        exit;
        foreach ($aResult as $r) {
            //$text = preg_replace('/\r\n|\r|\n/','',$text);


//            $patterns       = "/<center><center><br>/";
//            $replacements   = '<center><center>';
//            $r['p_detail'] =  preg_replace($patterns, $replacements, $r['p_detail'],1);

            //$r['p_detail'] = preg_replace('/\r\n|\r|\n/','','',$r['p_detail'],1);

            $sql = "UPDATE product_tb SET p_detail = '{$r['p_detail']}' WHERE p_num = '{$r['p_num']}' ; ";
//            $this->db->query($sql);
            zsView($sql);

        }

        zsView('c : '.count($aResult));

//        $dir = "/data/shop1/www/uploads/suvin_n";
//
//        // 핸들 획득
//        $handle  = opendir($dir);
//
//        $files = array();
//
//        // 디렉터리에 포함된 파일을 저장한다.
//        while (false !== ($filename = readdir($handle))) {
//            if($filename == "." || $filename == ".."){
//                continue;
//            }
//
//            // 파일인 경우만 목록에 추가한다.
//            if(is_file($dir . "/" . $filename)){
//                $files[] = str_replace('.jpg','',$filename);
//            }
//        }
//
//        // 핸들 해제
//        closedir($handle);
//
//        // 정렬, 역순으로 정렬하려면 rsort 사용
//        sort($files);
//
//
//        $aProductLists = array();
//        $aProductLists_F = array();
//
//        foreach ($files as $v) {
//            $sql = "SELECT * FROM product_tb WHERE p_easy_admin_code = '{$v}';  ";
//            $aResult = $this->db->query($sql)->row_array();
//
//            $rep_image = array();
//
//            if(empty($aResult) == true){
//                $aProductLists_F[] = $v;
//            }else{
//                $aProductLists[] = $v;
//
//
////                $rep_image[1] = $rep_image[0] ='/uploads/suvin_n/'.$v.'.jpg';
////                $rep_image_j    = json_encode_no_slashes($rep_image);
////
////                $sql = "UPDATE product_tb SET p_rep_image = '{$rep_image_j}' WHERE p_easy_admin_code = '{$v}';  ";
////                $this->db->query($sql);
//
//            }
//
//        }
//
//        zsView($aProductLists_F);
//        zsView('-------------------------------');
//        zsView($aProductLists);

//        $sql = "SELECT * FROM product_tb WHERE p_rep_image LIKE '%www.cloma%'; ";
//        $sql = "SELECT * FROM product_tb WHERE p_name IN ('줄무늬블리롱원피스','더끌려유넥가오리티셔츠','편하다후드롱원피스','코튼셔링롱원피스','남녀공용기본면티셔츠','매일편하게밴딩팬츠','레터링꼬임롱원피스','소매핀턱코튼티셔츠','잘록라인밴딩원피스','스타일자수밴딩팬츠','두줄레터링롱티셔츠','린넨브이롱원피스','더편하게상하의세트','플라밍고파우치3종세트','편하고예쁜체크팬츠1+1','누구나편하게밴딩팬츠','알록이원피스앞치마','예쁨만점나염원피스','봄처럼레이스블라우스','촉촉주름상하의세트','컬러풀밴딩팬츠','남성쿨링터치티셔츠','여성에어터치티셔츠','주름나염끈원피스','쉬폰러플롱원피스','시원하게입는나염팬츠','밴딩민소매점프수트','살랑와이드밴딩팬츠','두줄라인롱원피스','라이프투톤바람막이','언발포인트티셔츠','크로스언발스커트','골드시보리밴딩팬츠','플레어밴딩롱스커트','간편레이어드나시티셔츠','바삭코튼베이직남방','허리단시보리원피스','군살커버와이드팬츠','화사레이스티셔츠','반팔후드상하의세트');";
//        $oResult = $this->db->query($sql);
//        $aResult = $oResult->result_array();
//
//        foreach ($aResult as $r) {
//
//            $rep_image = array();
//            $rep_image[] = $rep_image[] = '/uploads/suvin_n/'.$r['p_easy_admin_code'].'.jpg';//str_replace('/suvin','/suvin_n',$rep_image[1]);
//
//            $rep_image_j = json_encode_no_slashes($rep_image);
//
//            $sql = "UPDATE product_tb SET p_rep_image = '{$rep_image_j}' WHERE p_num = '{$r['p_num']}'; ";
//            $this->db->query($sql);
//            zsView($sql);
//
//        }

    }


/*
    public function excel_proc(){


        exit;

//
//        $allow_id = array('41129','43226','47230','47725','51105','51561','56926','57567','57594','57604','57617','57655','57904','57919','57946','57953','57974','58030','58037','58125','58211','58263','58266','58281','58310','58315','58336','58343','58348','58417','58424','58470','58522','58525','58534','58546','58554','58566','58572','58594','58597','58606','58612','58620','58642','58653','58666','58671','58681','58692','58703','58721','58766','58770','58775','58780','58787','58806','58811','58822','58834','58849','58871','58875','58878','58882','58885','58903','58908','58912','58918','58958','58964','58968','58981','58988','59004','59037','59075','59081','59085','59102','59130','59136','59167');
        $arrayParams['filePath'] = $_FILES['excel']['tmp_name'];
        $this->load->library("/MY_Excel",$arrayParams);
        $aResult = $this->my_excel->getData();


        foreach ($aResult as $k => $r) {


            if($k > 1){

                $sql = "UPDATE product_tb SET p_summary = '{$r['F']}' , p_sale_price = '{$r['C']}' WHERE p_order_code = '{$r['A']}'; ";
                $this->db->query($sql);

            }

        }

zsView('complete');

        exit;


//        $aInput = array();
//        $i = 0;
//
//        $cate1_arr[42]='패션';
//        $cate1_arr[48]='패션잡화';
//        $cate1_arr[49]='아우터';
//        $cate1_arr[50]='상의';
//        $cate1_arr[51]='하의';
//        $cate1_arr[52]='원피스';
//        $cate1_arr[53]='홈웨어';
//
//        foreach ($aResult as $k => $r) {
//
//
//
//            $sub_data1 = array();
//            $item_cnt = 0;
//
//            if($k > 1 ){
//
//
//
//                $tmp_arr = explode('/',$r['D']) ;
//                $tmp_arr[0]; //순서
//                $tmp_arr[1]; //옵션1
//                $tmp_arr[2]; //옵션2
//                $tmp_arr[3]; //옵션3
//                $tmp_arr[4]; //옵션금액
//                $tmp_arr[5]; //재고량
//
//                $sub_data1['option_supply']    = 0;
//                $sub_data1['option_count']     = $tmp_arr[5];
//                $sub_data1['option_depth1']    = $tmp_arr[1];
//                if(empty($tmp_arr[2]) == false) $sub_data1['option_depth2']    = $tmp_arr[2];
//                if(empty($tmp_arr[3]) == false) $sub_data1['option_depth3']    = $tmp_arr[3];
//                $sub_data1['option_price']     = $tmp_arr[4];
//                $sub_data1['use_yn']           = 'Y';
//                $sub_data1['item_no']          = $r['A'];
//                $sub_data1['option_order']     = $tmp_arr[0];
//
//                $item_cnt   += $sub_data1['option_count'];
//                $sub_data[]  = $sub_data1; //json_encode_no_slashes($sub_data);
//
//                if($aResult[$k+1]['A'] != $r['A'] ){ // insert & init
//
//                    $aInput[$i] = array(
//                        'item_no'           => $r['A']
//                    ,   'local_no'          => $r['B']
//                    ,   'item_name'         => $r['C']
//                    ,   'start_date'        => "2020-02-03 17:00:00"
//                    ,   'end_date'          => "2021-02-02 17:00:00"
//                    ,   'sel_payway_cd'     => '13572'
//                    ,   'item_price'        => $r['E']
//                    ,   'supply_name'       => $r['I']
//                    ,   'org_area'          => $r['N']
//                    ,   'contents'          => $r['J']
//                    ,   'img_url'           => $r['L']
//                    ,   'ctgry_seq'         => $cate1_arr[$r['H']]
//                    ,   'item_count'        => $item_cnt
//                    ,   'option_yn'         => 'Y'
//                    ,   'option_info'       => json_encode($sub_data,JSON_UNESCAPED_UNICODE)
//                    );
//
//                    $i++;
//
//                    $item_cnt = 0;
//                    $sub_data = array();
//
//                }
//
//            }
//
//        }
//
//        $this->load->model('product_model');
//        $this->load->helper('string');
//
//        foreach ($aInput as $k => $r) {
//
//            $img_arr = array(
//                '1'=> 'http://www.cloma.co.kr/uploads/suvin/'.$r['local_no'].'.jpg'
//            ,  '0'=> 'http://www.cloma.co.kr/uploads/suvin/'.$r['local_no'].'.jpg'
//            );
//
//            $rep_img_json = json_encode($img_arr);
//
//            unset($r['local_no']);
//
//            $bResult = $this->product_model->publicInsert('snsform_product_tb',$r);
//            if($bResult) $r_str = 'Y';
//            else $r_str = 'N';
//
//            zsView('insert SNSFORM TABLE :: '.$r['item_no'].' :: '.$r_str);
//
//            if($bResult == true){
//
//                $aInput = array(
//                    'p_order_code'          => $r['item_no']
//                ,   'p_name'                => $r['item_name']
//                ,   'p_cate1'               => $r['ctgry_seq']
//                ,   'p_detail'              => strip_quotes($r['contents'])
//                ,   'p_display_info'        => '{"":"Y"}'
//                ,   'p_termlimit_datetime1' => '20200203170000'
//                ,   'p_termlimit_datetime2' => '20210203170000'
//                ,   'p_display_state'       => 'N'
//                ,   'p_sale_state'          => 'N'
//                ,   'p_taxation'            => 1
//                ,   'p_stock_state'         => $r['item_count'] > 0 ? 'Y':'N'
//                ,   'p_today_image'         => $r['img_url']
//                ,   'p_rep_image'           => $rep_img_json
//                ,   'p_sale_price'          => $r['item_price']
//
//                    // 필수 입력 정보 확인 및 추가
//
//                    //snsform 상품등록일자필드 ?
//
//                );
//
//                $bResult = $this->product_model->publicInsert('product_tb',$aInput);
//                if($bResult) $r_str = 'Y';
//                else $r_str = 'N';
//
//                zsView('insert LOCAL TABLE :: '.$r['item_no'].' :: '.$r_str);
//
//            }
//
//        }

        zsView('complete');

    }

*/

}//end of class Test