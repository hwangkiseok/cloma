<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 메인 컨트롤러
 */
class Main extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['ctgr_code']       = trim($this->input->post_get('ctgr_code', true)); //패션 카테고리 코드
        $req['best_code']       = trim($this->input->post_get('best_code', true)); //베스트 2depth 코드
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['list_type']       = trim($this->input->post_get('list_type', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }
        if( empty($req['ctgr_code']) ) {
            $req['ctgr_code'] = 2;
        }
        if( empty($req['best_code']) ) {
            $req['best_code'] = 'today';
        }

        return $req;
    }//end of _list_req()

    /**
     * 메인
     */
    public function index()
    {

        $this->load->model('product_model');

        {//rolling banner
            $this->load->model('exhibition_model');
            $aRollingLists = $this->exhibition_model->get_exhibition_product_list();
        }

        $notin = array();

        { // top30_top 상품은 최대 15개

            $fix_cnt = 15;

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '1';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' pmd_order ASC ';
            $addProductList = $this->product_model->get_product_list($aInput) ;

            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            $e_limit    = $fix_cnt - count($addProductList);
            $aInput     = array('not_pnum'  => $notin);

            $aTop10Lists = $this->product_model->get_main_product($aInput, 0 , $e_limit);
            $aTopTheme  = array_merge($addProductList,$aTop10Lists);

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }

//        $aTop10Lists     = parent::clearProductField($aTop10Lists);

        $this->_header();

        $this->load->view('/main/main', array(
            'aRollingLists'     => $aRollingLists
        ,   'aTop10Lists'       => $aTop10Lists
        ) );

        $this->_footer();

    }//end of index()


    public function get_btm_thema(){

        $this->load->model('product_model');

        $notin = array();

        { // top30_top 상품은 최대 15개

            $fix_cnt = 15;

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '1';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' pmd_order ASC ';
            $addProductList = $this->product_model->get_product_list($aInput) ;

            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            $e_limit    = $fix_cnt - count($addProductList);
            $aInput     = array('not_pnum'  => $notin);

            $aTop10Lists = $this->product_model->get_main_product($aInput, 0 , $e_limit);
            $aTopTheme  = array_merge($addProductList,$aTop10Lists);

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }

        { // top30_bottom 상품은 최대 15개

            //정책에 의한 노출
            $aInput = array('not_pnum'  => $notin);
            $aTopTheme2 = $this->product_model->get_main_product($aInput,0,$fix_cnt);
            foreach ($aTopTheme2 as $r)  $notin[] = $r['p_num'];

            if( count($aTopTheme2) < $fix_cnt ) { //모자르는 경우 채워넣기

                $addCnt =  (int)$fix_cnt - count($aTopTheme2);

                $aInput = array(
                    'main_best' => 'Y'
                ,   'where'     => array('not_pnum'  => $notin)
                );

                $aAddTopTheme2 = $this->product_model->get_product_list($aInput, 0 , $addCnt);

                foreach ($aAddTopTheme2 as $r)  $notin[] = $r['p_num'];

                $aTopTheme2 = array_merge($aTopTheme2,$aAddTopTheme2);

            }


        }

        { // 메인 페이지 테마

            $aMainThemaResult   = $this->product_model->get_main_thema(array('not_in' => $notin));
            $notin              = $aMainThemaResult['not_in'];
            $aMainThemaList     = $aMainThemaResult['list'];

        }

        { // 테마 - 카테고리 별 신상품 ( 카테고리별 20개 씩 )

            $this->load->model('category_md_model');
            $aInput = array();
            $aInput['where']['division'] = 4;
            $aInput['where']['state'] = 'Y';
            $aCategory = $this->category_md_model->get_category_md_list($aInput);

            $aTmpTheme3 = array();

            foreach ($aCategory as $r) {

                $aInput = array();
                $aInput['where']['sale_state']  =  'Y';
                $aInput['where']['stock_state'] =  'Y';
                $aInput['where']['ctgr']        =  $r['cmd_name'];
                $aInput['where']['not_pnum']    =  $notin;
                $aInput['orderby']              = ' p_termlimit_datetime1 DESC ';
                $tmp_result = $this->product_model->get_product_list($aInput, 0 , 20 ) ;

                $aTmpTheme3 = array_merge($tmp_result,$aTmpTheme3);

            }

        }

        if(empty($aTopTheme) == false) $aTopTheme = self::clearProductField($aTopTheme , array('campaign' => 'top30_t'));
        else $aTopTheme = array();
        if(empty($aTopTheme2) == false) $aTopTheme2 = self::clearProductField($aTopTheme2 , array('campaign' => 'top30_b'));
        else $aTopTheme2 = array();
        if(empty($aTmpTheme3) == false) $aTmpTheme3 = self::clearProductField($aTmpTheme3 , array('campaign' => 'thema_new_ctgr'));
        else $aTmpTheme3 = array();

        $aTheme2 = array(
            'title' => ''
        ,   'view_type' => 'A'
        ,   'aLists' => $aTopTheme2
        );

        $aTheme3 = array(
            'title' => '카테고리별 신상품'
        ,   'view_type' => 'C'
        ,   'aLists' => $aTmpTheme3
        );


        $aTheme = array();

        if(count($aMainThemaList) > 1){

            foreach ($aMainThemaList as $k => $r) {

                if($k == 1) $aTheme[] = $aTheme2;

                $aRowList = self::clearProductField($r['main_thema_product_lists'], array());

                $aTheme[] = array(
                    'title'     => $r['main_thema_row']['thema_name']
                ,   'view_type' => $r['main_thema_row']['display_type']
                ,   'aLists'    => $aRowList
                );

            }

        }else {

            if(count($aMainThemaList) == 1){
                foreach ($aMainThemaList as $k => $r) {

                    $aRowList = self::clearProductField($r['main_thema_product_lists'], array());

                    $aTheme[] = array(
                        'title'     => $r['main_thema_row']['thema_name']
                    ,   'view_type' => $r['main_thema_row']['display_type']
                    ,   'aLists'    => $aRowList
                    );

                }
            }

            $aTheme[] = $aTheme2;

        }

        $aTheme[] = $aTheme3;




        $this->load->view('/main/main_btm_thema', array(
            'aTheme' => $aTheme //테마 리스트
        ) );

    }

    /**
     * 패션
     */
    public function Fashion()
    {

        $req = $this->_list_req();

        $this->load->model('product_model');
        $this->load->model('category_md_model');
        $aCategoryInfo = $this->category_md_model->get_category_md_row( array('cmd_num' => $req['ctgr_code']) );

        if(empty($aCategoryInfo) == true){
            exit;
        }

        $query_data             =  array();
        $query_data['where']    = $req;
        $query_data['where']['sale_state']  =  'Y';
        $query_data['where']['stock_state'] =  'Y';
        $query_data['where']['ctgr'] = $aCategoryInfo['cmd_product_cate'];

        //전체갯수
        $list_count = $this->product_model->get_product_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $query_data['orderby'] = ' p_termlimit_datetime1 DESC '; //신상품부터

        $product_list = $this->product_model->get_product_list($query_data, $page_result['start'], $page_result['limit']);

        $this->_header();

        $this->load->view('/main/fashion', array(
            'req'           => $req,
            'list_count'    => $list_count,
            'total_page'    => $page_result['total_page'],
            'aProductLists'      => $product_list
        ));

        $this->_footer();

    }//end of Fashion()

    /**
     * 베스트
     */
    public function Best()
    {

        $req = $this->_list_req();

        $query_data             = array();
        $query_data['where']    = $req;
        $query_data['where']['sale_state']  =  'Y';
        $query_data['where']['stock_state'] =  'Y';

        $this->load->model('product_model');

        if($req['best_code'] == 'today' ) $query_data['orderby'] = ' p_order_count_twoday DESC , p_date DESC , p_num DESC ';
        else if($req['best_code'] == 'week' ) $query_data['orderby'] = ' p_order_count_week DESC , p_date DESC , p_num DESC ';
        else if($req['best_code'] == 'month' ) $query_data['orderby'] = ' p_order_count_month DESC , p_date DESC , p_num DESC ';

        $product_list = $this->product_model->get_product_list($query_data, 0, 50);

        $this->_header();

        $this->load->view('/main/best', array(
            'req'           => $req,
            'aProductLists'      => $product_list
        ));

        $this->_footer();

    }//end of Best()

    /**
     * 기획전
     */
//    public function exhibition()
//    {
//
//        $this->load->model('exhibition_model');
//        $aExhibition = $this->exhibition_model->get_exhibition_list();
//
//        $this->_header();
//
//        $this->load->view('/main/exhibition', array(
//            'aExhibition'     => $aExhibition
//        ) );
//
//        $this->_footer();
//
//    }//end of exhibition()

}//end of class Main