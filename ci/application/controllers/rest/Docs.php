<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Docs
 * @description :
 * - ApiDoc 도큐먼트 파일
 * - rest api 생성시, dummy 메소드(식별할 수 있는 이름으로 명명)를 생성하여 해당 메소드에 맞는 포맷양식으로 표기
 * - 문서화는 이곳 주석을 토대로 생성됨
 * - https://github.com/calinrada/php-apidoc
 *
 * =========================================================================================================
 * 1. CI용 Rest API Core Class
 * - 아래 2개의 파일은 CI용 rest api를 구성하기 위한 필수 라이브러리
 * - library > RestServer > Rest_Controller.php => 핵심 라이브러리, rest Controller들에서 상속됨
 * - library > RestServer > Format.php => 관련 라이브러리
 *
 * 2. W_Controller
 * - library > RestServer > Rest_Core.php => rest Controller들은 REST_Controller를 직접상속하기 때문에,
 * W_Controller역할을 하는 별도의 클래스
 *
 * 3. CI용 Apidoc 관련
 * - controllers > Docs.php => Apidoc 핵심 문서 (rest api 생성시, 포맷에 맞는 주석으로 함께 생성(기입)되어야 함)
 * - controllers > DocsExec.php => Apidoc 문서화를 시작하는 Controller, 아래 Apidoc 라이브러를 호출
 * - 아래 2개의 파일은 CI용 Apidoc 구동을 위한 필수 라이브러리
 * - library > Apidoc.php => 문서화를 실행하는 시작(점) 라이브러리
 * - third_party > apidoc => 실제 문서를 찍어내는 써드파티
 *
 * 4. 문서생성 url
 * - /rest/docsExec
 * =========================================================================================================
 */

class Docs extends W_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->key_name = $this->config->item('api_key_name');
        $this->key_val = $this->config->item('api_key_value');
        $this->doc_version = $this->config->item('api_doc_version');
    }

    /**
     */
    public function index()
    {

        $this->load->view('apidoc/v' . $this->doc_version . '/api.html');
    }
    //* @ApiParams(name="p-num", type="integer", nullable=false, description="상품키값1")

    /**
     * 메인 > index
     *
     * @ApiDescription(section="Main", description="메인페이지")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/main/index/")
     * @ApiParams(name="top15_type", type="string", nullable=false, description="상품 재정렬", sample="1 | 2 | 3")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: [],
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function main_index_get() {}

    /**
     * 메인 > best
     *
     * @ApiDescription(section="Main", description="베스트")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/main/best/")
     * @ApiParams(name="best_code", type="string", nullable=false, description="하위카테고리 값", sample="today | week | month")
     * @ApiParams(name="list_per_page", type="integer", nullable=false, description="페이지당 노출상품 수", sample="default : 50")
     * @ApiParams(name="page", type="integer", nullable=false, description="페이지", sample="default : 1")
     *
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: [{
    'aProductList': [{}]
    }] ,
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function main_best_get() {}

    /**
     * 메인 > best
     *
     * @ApiDescription(section="Main", description="패션")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/main/fashion/")
     * @ApiParams(name="ctgr_code", type="integer", nullable=false, description="하위카테고리 값", sample="1:패션잡화 | 2:아우터 | 3:상의 | 4:하의 | 5:원피스 | 6:홈웨어 ")
     * @ApiParams(name="list_per_page", type="integer", nullable=false, description="페이지당 노출상품 수", sample="default : 50")
     * @ApiParams(name="page", type="integer", nullable=false, description="페이지")
     * @ApiParams(name="sort_type", type="string", nullable=true, description="정렬순서", sample="new_desc:신상품 | ingi_desc:인기순 | discount_desc:할인순 | lowprice_desc:낮은 가격순 ")
     *
     *
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: [{
    'aProductList': [{}]
    }] ,
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function main_fashion_get() {}

    /**
     * 메인 > 기획전
     *
     * @ApiDescription(section="Main", description="기획전")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/exhibition/index/")
     *
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: [],
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function exhibition_get() {}



    /**
     * 상품 > detail
     *
     * @ApiDescription(section="Product", description="상품 상세")
     * @ApiMethod(type="get")
     * @ApiLink(name="link", description="
    상품 상세 API : <a href='/rest/product/detail/p-num/1?<?=$this->key_name?>=<?=$this->key_val?>' target='_blank'>link</a>
     * ")
     * @ApiRoute(name="/rest/product/detail/")
     * @ApiParams(name="p_num", type="integer", nullable=false, description="p_num")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: {
     *      'aProductInfo': {} //상품상세
     *   },
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function product_detail_get() {}

    /**
     * 찜상품 > put
     *
     * @ApiDescription(section="Wish", description="상품 찜하기 :: toggle type")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/wish/upsert/")
     * @ApiParams(name="p_num", type="integer", nullable=false, description="p_num")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: { },
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function wish_upsert_put() {}

    /**
     * 찜하기 > get
     *
     * @ApiDescription(section="Wish", description="찜 리스트")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/wish/list/")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
        {
        'status': '000',
        'message': '',
        'message_type': '',
        'error_data': '',
        'data': {
            'aWishList': [
                {
                    'w_member_num': '',
                    'w_product_num': '',
                    'w_regdatetime': '',
                    'p_num': '',
                    'p_category': '',
                    'p_cate1': '',
                    'p_cate2': '',
                    'p_cate3': '',
                    'p_name': '',
                    'p_summary': '',
                    'p_detail': '',
                    'p_detail_add': '',
                    'p_rep_image': '',
                    'p_rep_image_add': '',
                    'p_today_image': '',
                    'p_banner_image': '',
                    'p_detail_image': '',
                    'p_order_link': '',
                    ....
                }
            ]
        },
        'goUrl': '',
        'exit': true
        }
     * ")
     */
    public function wish_list_get() {}

    /**
     * 댓글등록 > put
     *
     * @ApiDescription(section="Comment", description="댓글등록")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/comment/insert/")
     * @ApiParams(name="cmt_table", type="string", nullable=false, description="댓글타입", sample="product / event")
     * @ApiParams(name="cmt_table_num", type="integer", nullable=false, description="seq" )
     * @ApiParams(name="cmt_content", type="string", nullable=false, description="내용")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message : '문의가 등록되었습니다.',
     *   error_data: [],
     *   data: { },
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function comment_insert_put() {}

    /**
     * 댓글등록 > delete
     *
     * @ApiDescription(section="Comment", description="댓글삭제")
     * @ApiMethod(type="delete")
     * @ApiParams(name="cmt_num", type="integer", nullable=false, description="seq")
     *
     * @ApiReturn(type="object", sample="
     *   {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '삭제 완료',
     *   message_type: '',
     *   error_data: '',
     *   data: '',
     *   goUrl: '',
     *   exit: true
     *   }
     * ")
     */
    public function comment_delete_delete() {}

    /**
     * 댓글 > get
     *
     * @ApiDescription(section="Comment", description="댓글리스트")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/comment/list/")
     *
     * @ApiParams(name="tb_table", type="integer", nullable=false, description="댓글 타입", sample="product / event / my")
     * @ApiParams(name="tb_table_num", type="integer", nullable=false, description="seq")
     * @ApiParams(name="list_per_page", type="integer", nullable=false, description="페이지당 노출상품 수", sample="default : 50")
     * @ApiParams(name="page", type="integer", nullable=false, description="페이지", sample="default : 1")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
    'aCommentLists': [
    {
    cmt_num: '',
    cmt_table: '',
    cmt_table_num: '',
    cmt_admin: '',
    cmt_name: '',
    cmt_profile_img: '',
    cmt_member_num: '',
    cmt_content: '',
    cmt_answer: '',
    cmt_regdatetime: '',
    cmt_best_order: '',
    cmt_blind: '',
    cmt_blind_memo: '',
    cmt_blind_regdatetime: '',
    cmt_report_count: '',
    cmt_display_state: '',
    m_nickname: '',
    m_profile_img: '',
    m_sns_nickname: '',
    m_email: ''
    },
    ...
    ]
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function comment_list_get() {}

    /**
     * 1:1문의 삭제 > delete
     *
     * @ApiDescription(section="Qna", description="1:1문의 삭제")
     *
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/rest/qna/delete/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiParams(name="bq_num", type="integer", nullable=false, description="seq")
     *
     * @ApiReturn(type="object", sample="
        {
        'status': '000',
        'message': '',
        'message_type': '',
        'error_data': '',
        'data': { },
        'goUrl': '',
        'exit': true
        }
     * ")
     */
    public function qna_delete_delete() {}

    /**
     * 1:1문의 등록 > put
     *
     * @ApiDescription(section="Qna", description="1:1문의 등록/수정")
     *
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/qna/upsert/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiParams(name="bq_num", type="integer", nullable=true, description="seq")
     * @ApiParams(name="bq_name", type="String", nullable=false, description="이름")
     * @ApiParams(name="bq_product_name", type="String", nullable=true, description="상품명")
     * @ApiParams(name="bq_product_num", type="integer", nullable=true, description="상품번호")
     * @ApiParams(name="bq_contact", type="integer", nullable=false, description="연락처")
     * @ApiParams(name="bq_content", type="text", nullable=false, description="내용")
     * @ApiParams(name="bq_category", type="integer", nullable=false, description="문의카테고리")
     * @ApiParams(name="board_qna_file1", type="file", nullable=false, description="이미지1")
     * @ApiParams(name="board_qna_file2", type="file", nullable=false, description="이미지2")
     * @ApiParams(name="board_qna_file3", type="file", nullable=false, description="이미지3")
     * @ApiParams(name="refund_info_bank", type="String", nullable=true, description="환불 - 은행")
     * @ApiParams(name="refund_info_account", type="String", nullable=true, description="환불 - 계좌")
     * @ApiParams(name="refund_info_owner", type="String", nullable=true, description="환불 - 예금주")
     *
     * @ApiReturn(type="object", sample="
        {
        'status': '000',
        'message': '',
        'message_type': '',
        'error_data': '',
        'data': { },
        'goUrl': '',
        'exit': true
        }
     * ")
     */
    public function qna_upsert_put() {}

    /**
     * 1:1문의 > get
     *
     *
     * @ApiDescription(section="Qna", description="1:1문의 리스트")
     *
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/Qna/list/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
    'aQnaList': [
    {
    'bq_num': '',
    'bq_member_num': '',
    'bq_category': '',
    'bq_secret_yn': '',
    'bq_product_num': '',
    'bq_product_name': '',
    'bq_name': '',
    'bq_contact': '',
    'bq_content': '',
    'bq_refund_info': '',
    'bq_file': '',
    'bq_answer_yn': 'N',
    'bq_answer_content': null,
    'bq_adminuser_num': '0',
    'bq_regdatetime': '',
    'bq_answerdatetime': '',
    'bq_display_state_1': '',
    'bq_display_state_2': '',
    'bq_last_writer': null
    }
    ...
    ]
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function qna_list_get() {}


    /**
     * 게시판 > FAQ
     *
     * @ApiDescription(section="Board", description="FAQ")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/board/faq/")
     * @ApiParams(name="ctgr", type="String", nullable=true, description="카테고리")
     * @ApiParams(name="search_text", type="String", nullable=true, description="검색어")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
    'aFaqList': [
    {
    'bh_subject': '',
    'bh_category_str': '',
    'bh_content': '',
    'bh_usestate': '',
    }
    ],
    'req': {
    'page': 1,
    'list_per_page': 5
    }
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function board_faq_get() {}

    /**
     * 게시판 > 공지사항
     *
     * @ApiDescription(section="Board", description="공지사항")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/board/notice/")
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
    'aNoticeList': [
    {
    'bh_num': '',
    'bh_division': '',
    'bh_category': '',
    'bh_adminuser_num': '',
    'bh_top_yn': '',
    'bh_subject': '',
    'bh_content': '',
    'bh_regdatetime': '',
    'bh_usestate': '',
    'bh_last_writer': ''
    }
    ],
    'req': {
    'page': 1,
    'list_per_page': 5
    }
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function board_notice_get() {}


    /**
     *  검색 > index
     *
     * @ApiDescription(section="Search", description="검색")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/search/index/")
     * @ApiParams(name="kwd", type="String", nullable=false, description="kwd")
     * @ApiParams(name="list_per_page", type="integer", nullable=false, description="페이지당 노출상품 수", sample="default : 50")
     * @ApiParams(name="page", type="integer", nullable=false, description="페이지", sample="default : 1")
     *
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: {
     *      'aProductInfo': {} //상품상세
     *   },
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function search_index_get() {}


    /**
     * 장바구니 등록 > put
     *
     * @ApiDescription(section="Cart", description="장바구니 등록")
     *
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/cart/upsert/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiParams(name="p_num", type="integer", nullable=false, description="상품번호")
     * @ApiParams(name="option_info", type="String", nullable=false, description="옵션정보")
     * @ApiParams(name="set_referer", type="String", nullable=true, description="리퍼러")
     * @ApiParams(name="set_campaign", type="String", nullable=true, description="캠패인")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function cart_upsert_put() {}

    /**
     * 대량등록문의 > put
     *
     * @ApiDescription(section="Offer", description="대량구매")
     *
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/offer/insert/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=true, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=true, description="회원번호")
     *
     * @ApiParams(name="user_name", type="string", nullable=false, description="이름")
     * @ApiParams(name="user_hp", type="integer", nullable=false, description="휴대폰")
     * @ApiParams(name="user_email", type="String", nullable=true, description="이메일")
     * @ApiParams(name="content", type="String", nullable=true, description="내용")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function offer_upsert_put() {}


    /**
     * 회원가입/로그인 > post
     *
     * @ApiDescription(section="Member", description="로그인/가입")
     *
     * @ApiMethod(type="post")
     * @ApiRoute(name="/rest/member/login/")
     * @ApiParams(name="id", type="integer", nullable=false, description="id")
     * @ApiParams(name="nickname", type="String", nullable=false, description="nickname")
     * @ApiParams(name="profile_image", type="String", nullable=true, description="profile_image")
     * @ApiParams(name="sns_site", type="String", nullable=true, description="sns_site")
     * @ApiParams(name="fcm_id", type="String", nullable=true, description="fcm_id")
     * @ApiParams(name="device_info", type="String", nullable=true, description="device_info")
     * @ApiParams(name="os_version", type="String", nullable=true, description="os_version")
     * @ApiParams(name="app_version", type="String", nullable=true, description="app_version")
     * @ApiParams(name="app_version_code", type="String", nullable=true, description="app_version_code")
     * @ApiParams(name="sns_site", type="String", nullable=true, description="sns_site")
     * @ApiParams(name="adid", type="String", nullable=true, description="adid")
     * @ApiParams(name="fcm_id", type="String", nullable=true, description="fcm_id")
     * @ApiParams(name="age_range", type="String", nullable=true, description="age_range::카카오한정")
     * @ApiParams(name="birthyear", type="integer", nullable=true, description="birthyear::카카오한정")
     * @ApiParams(name="birthday", type="integer", nullable=true, description="birthday::카카오한정")
     * @ApiParams(name="gender", type="String", nullable=true, description="gender::카카오한정")
     * @ApiParams(name="phone_number", type="String", nullable=true, description="phone_number::카카오한정")
     * @ApiParams(name="address", type="String", nullable=true, description="address::카카오한정")
     * @ApiParams(name="ci", type="String", nullable=true, description="ci::카카오한정")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function member_login_post() {}

    /**
     * 회원정보 승인 > get
     *
     * @ApiDescription(section="Member", description="회원정보 승인")
     *
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/member/accept_proc/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=true, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=true, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function member_accept_proc_get() {}

    /**
     * 회원정보 > get
     *
     * @ApiDescription(section="Member", description="회원정보")
     *
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/member/info/")
     *
     * @ApiHeaders(name="m_key", type="String", nullable=true, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=true, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function member_info_get() {}

    /**
     * 각종 회원정보 저장 > put
     *
     * @ApiDescription(section="Member", description="각종정보 저장")
     *
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/member/save_info/")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiParams(name="app_version", type="String", nullable=true, description="앱 버전")
     * @ApiParams(name="app_version_code", type="Integer", nullable=true, description="앱 버전코드")
     * @ApiParams(name="device_model", type="String", nullable=true, description="디바이스모델")
     * @ApiParams(name="os_version", type="Integer", nullable=true, description="OS버전")
     * @ApiParams(name="adid", type="String", nullable=true, description="광고아이디")
     * @ApiParams(name="fcm_id", type="String", nullable=true, description="FCM아이디")
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': { },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function member_save_info_post() {}

    /**
     * 공유하기 > put
     *
     * @ApiDescription(section="Share", description="상품 공유하기 :: toggle type")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/share/upsert/")
     * @ApiParams(name="p_num", type="integer", nullable=false, description="p_num")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     * @ApiReturn(type="object", sample="
     * {
     *   status: '000', // 000 : 정상 , 100 : 권한없음 , 200 : 에러 , 300 : 중복
     *   message: '',
     *   message_type: '', // alert
     *   error_data: [],
     *   data: { },
     *   goUrl: '',
     *   exit: true
     * }
     * ")
     */
    public function share_upsert_put() {}

    /**
     * 찜하기 > get
     *
     * @ApiDescription(section="Share", description="상품 공유 리스트")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/share/list/")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
    'aWishList': [
    {
    's_member_num': '',
    's_product_num': '',
    's_regdatetime': '',
    'p_num': '',
    'p_category': '',
    'p_cate1': '',
    'p_cate2': '',
    'p_cate3': '',
    'p_name': '',
    'p_summary': '',
    'p_detail': '',
    'p_detail_add': '',
    'p_rep_image': '',
    'p_rep_image_add': '',
    'p_today_image': '',
    'p_banner_image': '',
    'p_detail_image': '',
    'p_order_link': '',
    ....
    }
    ]
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function share_list_get() {}


    /**
     * 팝업리스트 > get
     *
     * @ApiDescription(section="Popup", description="팝업리스트")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/popup/list/")
     * @ApiParams(name="exec", type="String", nullable=false, description="실행위치", sample="{'product' : '상품상세' , 'main' : '메인페이지'}")
     * @ApiParams(name="apo_uid", type="integer", nullable=false, description="seq" )
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data': {
        'aPopupList': [
            {
                'apo_num': '2',	=> 팝업 uid
                'apo_content_type': '2', => 1=상품상세로이동:apo_p_num 참조 / 2=기획전으로이동:apo_special_offer_seq 참조
                'apo_image': 'https://via.placeholder.com/300x400',	=> img
                'apo_p_num': '0', => 상품 uid
                'apo_special_offer_seq': '2', => 기획전 uid
            }
        ]
     ,  'popup_size_type': '2', => 1=전체팝업 / 2=다이얼로그팝업
     ,  'popup_btn_type': '1', => 1=닫기 / 2= 다시보지않기 + 닫기 / 3= 오늘보지않기 + 닫기
    },
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function popup_list_get() {}

    /**
     * 클릭체크 > put
     *
     * @ApiDescription(section="Popup", description="클릭체크")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/popup/click_view/")
     * @ApiParams(name="apo_num", type="integet", nullable=false, description="seq")
     * @ApiParams(name="type", type="String", nullable=false, description="클릭타입", sample="type: 이미지클릭:click / 보기:view ")
     * @ApiHeaders(name="m_key", type="String", nullable=false, description="회원키")
     * @ApiHeaders(name="m_num", type="integer", nullable=false, description="회원번호")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function popup_click_view_put() {}

    /**
     * 종료팝업 노출수 체크
     *
     * @ApiDescription(section="Popup", description="종료팝업 노출수")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/popup/close_pop_view/")
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function close_pop_view_get() {}

    /**
     * 스플래시 > get
     *
     * @ApiDescription(section="App", description="스플래시")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/rest/App/splash/")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data' : '',
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function app_splash_get() {}

    /**
     * 리퍼러카운팅 > put
     *
     * @ApiDescription(section="common", description="리퍼러카운팅")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/common/referer/")
     * @ApiParams(name="ref", type="string", nullable=false, description="referer")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data' : '',
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function referer_put() {}

    /**
     * 푸시클릭 카운팅 > put
     *
     * @ApiDescription(section="common", description="푸시클릭 카운팅")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/rest/common/push/")
     * @ApiParams(name="app_push_id", type="integer", nullable=false, description="푸시 식별값")
     *
     * @ApiReturn(type="object", sample="
    {
    'status': '000',
    'message': '',
    'message_type': '',
    'error_data': '',
    'data' : '',
    'goUrl': '',
    'exit': true
    }
     * ")
     */
    public function push_put() {}

}