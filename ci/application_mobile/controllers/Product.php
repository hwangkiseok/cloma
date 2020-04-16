<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품
 */
class Product extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->model('product_model');

    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['ctgr_code']       = trim($this->input->post_get('ctgr_code', true));
        $req['best_code']       = trim($this->input->post_get('best_code', true));
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

        return $req;
    }//end of _list_req()

    public function index()
    {
        show_404();
    }//end of index()


    public function product_list_ajax(){

        ajax_request_check();
        $req = $this->_list_req();


        $query_data             =  array();
        $query_data['where']    = $req;
        $query_data['where']['sale_state']  = 'Y';
        $query_data['where']['stock_state'] = 'Y';

        if( $req['list_type'] == 'fashion' ) { //패션상품

            $this->load->model('category_md_model');
            $aCategoryInfo = $this->category_md_model->get_category_md_row( array('cmd_num' => $req['ctgr_code']) );

            if(empty($aCategoryInfo) == true) exit;

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

        }else if( $req['list_type'] == 'best' ) { //베스트 상품

            if($req['best_code'] == 'today' ) $query_data['orderby'] = ' p_order_count_twoday DESC , p_date DESC , p_num DESC ';
            else if($req['best_code'] == 'week' ) $query_data['orderby'] = ' p_order_count_week DESC , p_date DESC , p_num DESC ';
            else if($req['best_code'] == 'month' ) $query_data['orderby'] = ' p_order_count_month DESC , p_date DESC , p_num DESC ';

            $page_result['start'] = 0;
            $page_result['limit'] = 50;

        }



        $product_list = $this->product_model->get_product_list($query_data, $page_result['start'], $page_result['limit']);

        $view_suffix = '';

        if($req['list_type'] == 'fashion' || $req['list_type'] == 'best'){
            $view_suffix = "_main";
        }

        $this->load->view('/product/ajax_list'.$view_suffix, array(
            'req'               => $req,
            'list_count'        => $list_count,
            'total_page'        => $page_result['total_page'],
            'aProductLists'     => $product_list,
            'campaign'          => $req['list_type']
        ));

    }

    public function product_detail()
    {

        $p_num = number_only($this->uri->segment(3));
        if(empty($p_num) == true ) $p_num = $this->input->post_get('p_num',true);

        if( empty($p_num) ) {
            alert(lang("site_product_sale_end").'.', "/");
        }

        $aInput         = array( 'p_num' => $p_num );
        $aProductInfo   = $this->product_model->get_product_row($aInput);

        if( empty($aProductInfo) ) {
            alert(lang("site_product_sale_end").'.', "/");
        }




        //상품상세페이지페이지 진입수 ++ (p_view_count) (새로고침 제외)
        if( get_cookie('pdt_view') != $aProductInfo['p_num'] ) {

            $query_data = array();
            $query_data['p_view_count'] = (int)($aProductInfo['p_view_count']) + 1;
            $query_data['p_view_today_count'] = (int)($aProductInfo['p_view_today_count']) + 1;
            $query_data['p_view_3day_count'] = (int)($aProductInfo['p_view_3day_count']) + 1;
            $query_data['p_click_count_week'] = (int)($aProductInfo['p_click_count_week']) + 1;

            $this->product_model->update_product($aProductInfo['p_num'], $query_data);
            set_cookie('pdt_view', $aProductInfo['p_num'], 0);
        }

        {//최근 본 상품에 저장
            $recently_view_product_array = array();
            $recently_view_product = get_cookie('rctlyViewPdt');

            if( !empty($recently_view_product) ) {
                $recently_view_product_array = json_decode($recently_view_product, true);
                $search_key = array_search($aProductInfo['p_num'], $recently_view_product_array);
                if( $search_key !== false ) {
                    unset($recently_view_product_array[$search_key]);
                }

                foreach($recently_view_product_array as $key => $val ) {
                    if( $val == $aProductInfo['p_num']) {
                        unset($recently_view_product_array[$key]);
                    }
                }
            }//end of if()

            array_unshift($recently_view_product_array, $aProductInfo['p_num']);

            if( count($recently_view_product_array) > $this->config->item('recently_view_product_max_count') ) {
                for($i=$this->config->item('recently_view_product_max_count'); $i <= count($recently_view_product_array); $i++ ) {
                    array_pop($recently_view_product_array);
                }
            }

            saveCookie('rctlyViewPdt', json_encode_no_slashes($recently_view_product_array), strtotime("+7 days"));

        }

        $isWish = false;
        $isShare = false;
        if (member_login_status()) {//찜하기 & 공유 상품여부
            $this->load->model('wish_model');
            $isWish = $this->wish_model->get_wish_row($this->aMemberInfo['m_num'],$aProductInfo['p_num']) > 0 ? true : false ;

            $this->load->model('share_model');
            $isShare = $this->share_model->get_share_row($this->aMemberInfo['m_num'],$aProductInfo['p_num']) > 0 ? true : false ;
        }

        {//ext_load

            $aRelationProduct = $this->product_model->getRelationProductLists($aProductInfo['p_num']);
            $rel_product  = $this->load->view('/product/mostview_1', array( 'tit' => '연관상품 리스트' , 'e_naming' => 'rel' , 'aProductLists' => $aRelationProduct ) , true );

            $with_product = $this->load->view('/product/mostview_1', array( 'tit' => '함께 구매하는 상품' , 'e_naming' => 'with' , 'aProductLists' => $aRelationProduct ) , true );

            $ext_comment = $this->ext_comment('product',$aProductInfo['p_num']);
        }

        {//댓글

            $aInput['where'] = array(
                    'tb'        => 'product'
                ,   'tb_num'    => $aProductInfo['p_num']
            );
            $nCommentLists = $this->comment_model->get_comment_list($aInput , '' , '' , true);
        }

        {//배송정보
            $this->load->model('common_model');
            $aDeliveryInfo = $this->common_model->get_common_code_row(1);
        }


        {
            $sql = "SELECT * FROM snsform_product_tb WHERE item_no = '{$aProductInfo['p_order_code']}'; ";
            $oOutsideProductInfo = $this->db->query($sql);
            $aOutsideProductInfo = $oOutsideProductInfo->row_array();

            if(empty($aOptionInfo) == true){

                $aOptionInfo = json_decode($aOutsideProductInfo['option_info'],true);

    //            zsView($aOptionInfo);
    //            zsView($aOutsideProductInfo['option_info']);
    //            zsView($aOptionInfo);

                $option_depth = '0';

                if($aOptionInfo[0]['option_depth3']){
                    $option_depth = 3;
                }else if($aOptionInfo[0]['option_depth2']){
                    $option_depth = 2;
                }else if($aOptionInfo[0]['option_depth1']){
                    $option_depth = 1;
                }

                $aOption = array();
                $aAddOption = array();

                if($option_depth == 2){

                    foreach ($aOptionInfo as $k => $r) {
                        if($r['option_plus'] == 'Y'){
                            $aAddOption[$r['option_depth1']] = $r;
                        }else{
                            $aOption[$r['option_depth1']][] = $r;
                        }
                    }

                }else if($option_depth == 3){
                    foreach ($aOptionInfo as $k => $r) {
                        if($r['option_plus'] == 'Y'){
                            $aAddOption[$r['option_depth1']] = $r;
                        }else{
                            $aOption[$r['option_depth1']][$r['option_depth2']][] = $r;
                        }
                    }

                }else{

                    foreach ($aOptionInfo as $k => $r) {

                        if($r['option_plus'] == 'Y'){
                            $aAddOption[$r['option_depth1']] = $r;
                        }else{
                            $aOption[] = $r;
                        }

                    }

                }

            }

        }

        /*test referer campaign*/
        $_SESSION['_set_referer'] = 'rtest';
        $_SESSION['_set_campaign'] = 'ctest';

        $options = array('title' => '상품상세' , 'top_type' => 'back' , 'aProductInfo' => $aProductInfo);
        $this->_header($options);

        $this->load->view('/product/detail', array(
                'aProductInfo' => $aProductInfo
            ,   'with_product' => $with_product
            ,   'rel_product'  => $rel_product
            ,   'ext_comment'  => $ext_comment
            ,   'nCommentLists' => $nCommentLists
            ,   'aDeliveryInfo' => $aDeliveryInfo
            ,   'aOption'       => $aOption
            ,   'isShare'      => $isShare
            ,   'isWish'       => $isWish
            ,   'option_depth' => $option_depth
            ,   'sSnsformOptionType'   => $aOutsideProductInfo['option_type']
        ) );

        $this->_footer();

    }//end of detail()

    public function detail_app(){

        if(is_app() == false) exit;

        $p_num = number_only($this->uri->segment(3));
        if(empty($p_num) == true ) $p_num = $this->input->post_get('p_num',true);

        if( empty($p_num) ) {
            alert(lang("site_product_sale_end").'.', "/");
        }
        $aInput         = array( 'p_num' => $p_num );
        $aProductInfo   = $this->product_model->get_product_row($aInput);

        if( empty($aProductInfo) ) {
            alert(lang("site_product_sale_end").'.', "/");
        }

        $options = array('title' => '상품상세' , 'top_type' => 'back');
        $this->_header(array('no_header' => true));

        $this->load->view('/product/detail_app', array(
            'aProductInfo' => $aProductInfo
        ) );

    }

}//end of class Product