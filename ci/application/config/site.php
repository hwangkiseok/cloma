<?php
/**
 * 사이트 공통 환경설정
 */

//사이트 정보
$config['site_name_ceo'] = "조은순";
$config['site_name_cpo'] = "이규면";//개인정보책임관리자

$config['company_name_kr'] = "(주)인사이트 드림";
$config['company_name_en'] = "Insight Dream";
$config['site_name_kr'] = "옷쟁이들";
$config['site_name_en'] = "Cloma";
$config['site_help_tel'] = "070-4808-2390";
$config['site_help_email'] = "suvinshop@naver.com";
$config['site_zip_code'] = "02146";
$config['site_addr'] = "서울특별시 중랑구 면목로 419 (면목동) 6층";
$config['biz_no'] = "398-87-00626";
$config['tongsin'] = "제2017-서울중랑-0501호";

$config['site_description'] = "";
$config['site_domain'] = str_replace(array("http://", "https://"), array("", ""), $_SERVER['HTTP_HOST']);
$config['site_domain_http'] = "http://" . $config['site_domain'];
$config['site_domain_https'] = "https://" . $config['site_domain'];

if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) {
    $config['default_http'] = $config['site_domain_https'];
}else{
    $config['default_http'] = $config['site_domain_http'];
}

$config['site_domain_cookie'] = ".cloma.co.kr";
$config['site_img_http'] = "https://img." . SITE_DOMAIN;  //이미지(https)
//$config['site_img_http'] = IMG_HTTP;
$config['site_thumb'] = $config['site_domain_http'] . "/images/og_image.png?t=" . time();

//결과 코드
$config['status_code']['success']   = "000";
$config['status_code']['noauth']    = "100";
$config['status_code']['error']     = "200";
$config['status_code']['overlap']   = "300";    //중복

//로그인유지 쿠키 만료기간
$config['autologin_expire_days'] = 30;

//구글 서버키
$config['google_server_key'] = "AAAANUo0amk:APA91bFF0qu2gymOWaj3wimBnE8ZzFaoTpfu_DygJSg191dM3Zi96lkMYmyCmWUK5WPWrLLuOogZOHWM1OsptCxvAI2yb9wIrpLxwkGkj6tg0VUQKGHY-m4PSKzY89Ll9dFQWHuoT8fb";

//bitly
$config['bitly_key'] = '';
$config['bitly_login'] = 'o_4o3qe27s8k';
$config['bitly_token'] = '62f72809b5d46dac325b07254d185bd35da5e6ce';

//APP 관련
$config['app_main_string'] = "";
$config['app_version_string'] = "";
$config['app_version_code_string'] = "";

//카카오 관련
$config['kakao_app_key']['native'] = "d4090f3c5306d72ce3831f420945c48c";
$config['kakao_app_key']['rest'] = "b26beb2de048fe8731afc9b260d71da2";
$config['kakao_app_key']['javascript'] = "3140225942632fbeac8c0a552c8f8cd4";
$config['kakao_app_key']['admin'] = "b8c247692b4077ec6bd86887c46f4e82";
$config['kakao_ad_track_id'] = "";   //카카오픽셀 track_id

//$config['kakao_redirect_uri'] = $config['site_domain_http'] . "/kakao/oauth";
$config['kakao_redirect_uri'] = $config['site_domain_http'] . "/member/kakao_callback";
$config['kakao_login_uri'] = "https://kauth.kakao.com/oauth/authorize?client_id=" . $config['kakao_app_key']['rest'] . "&redirect_uri=" . urlencode($config['kakao_redirect_uri']) . "&response_type=code";

//인스타그램
$config['instagram_url'] = "";

//SEED 암호키
$config['seed_key'] = "!i@n#s:i%g^h&t*(d)r_e+a|m_key";
$config['seed_iv'] = "!i@n#s:i%g^h&t*(d)r_e+a|m_iv";

//AES 256 암호키 (32byte 여야함)
$config['aes256_key'] = "CKAtHbVY8sDWqiXoTPlhMzaUIBmJ534k";

//앱 관련
$config['app_id'] = "kr.co.cloma.app";
$config['ios_link_key'] = "";
//$config['app_market_url'] = "market://details?id=" . $config['app_id'];                                 //구글마켓으로(앱용)
$config['app_market_url'] = "https://play.google.com/store/apps/details?id=" . $config['app_id'];                                 //구글마켓으로(앱용)
$config['app_market_url_web'] = "https://play.google.com/store/apps/details?id=" . $config['app_id'];   //구글마켓으로(웹용)
$config['app_shceme'] = "";
$config['app_intent'] = ""; //intent (#PARAM#은 실제파라미터로 변경해야 함, 없으면 "")
$config['app_lnb_url'] = "";
$config['dynamic_link_http'] = "https://cloma.page.link";

//리뷰 경로 관련
$config['review_img_path'] = DOCROOT . "/uploads/review";
$config['review_img_path_web'] = "/uploads/review";

//프로필 경로 관련
$config['member_profile_img_path'] = DOCROOT . "/uploads/profile";
$config['member_profile_img_path_web'] = "/uploads/profile";
//이미지 썸네일 사이즈 : [쎔메일번호] => [0]가로(px), [1]세로(px), [2]자르기 여부(Y|N)
$config['member_profile_img_size'][1] = array("300", "300", 'N');

//공유 메시지
$config['share_text'] = "#NICKNAME#님이 " . SITE_NAME_KR . " 추천합니다!\\n카스1등 쇼핑몰 #" . SITE_NAME_KR;
$config['share_url'] = $config['site_domain_http'] . "/app";
$config['share_image'] = $config['site_thumb'];
$config['share_name'] = "앱으로 연결";

//기본 오류 페이지 (로그아웃/로그인, 초기화면으로)
$config['error_url'] = "/auth/blank";

//프로필
$config['member_profile_img_default'] = IMG_HTTP . "/images/profile_default.png";
//$config['member_profile_img_default'] = "/images/icon_person.png";
$config['member_profile_img_admin'] = IMG_HTTP . "/images/app_icon_192.png";
$config['member_profile_dir'] = "/files/profile_img";
$config['member_profile_path'] = HOMEPATH . $config['member_profile_dir'];

//파일 경로
$config['qna_file_head'] = "/files/qna";
$config['qna_file_path'] = HOMEPATH . $config['qna_file_head'];


$config['member_nickname_bannedwords'] = array(
    SITE_NAME_KR, "관리자", "운영자", "마스터", "테스트", "쇼핑몰관리자", "쇼핑몰운영자", "웹마스터", "test", "admin", "master"
);

//배너
$config['banner_division'][1] = "메인배너1(오늘추천)";
$config['banner_division'][2] = "메인배너2(인기상품)";
$config['banner_division'][3] = "이벤트배너";
$config['banner_division'][4] = "사이드메뉴배너";

$config['banner_termlimit_yn']['Y'] = "노출기간사용";
$config['banner_termlimit_yn']['N'] = "상시노출";

$config['banner_usestate']['Y'] = "활성";
$config['banner_usestate']['N'] = "비활성";
$config['banner_usestate_text_color']['Y'] = "#0000ff";
$config['banner_usestate_text_color']['N'] = "#ff0000";

//상품
$config['product_division'][1] = "일반상품";
$config['product_division'][2] = "이벤트상품";

$config['product_category'][1] = "오늘추천";
$config['product_category'][2] = "인기상품";
$config['product_category'][3] = "해외상품";

$config['product_taxation'][1] = "과세";
$config['product_taxation'][2] = "영세";
$config['product_taxation'][3] = "면세";

$config['product_wish_raise_yn']['Y'] = "사용";
$config['product_wish_raise_yn']['N'] = "사용안함";

$config['product_share_raise_yn']['Y'] = "사용";
$config['product_share_raise_yn']['N'] = "사용안함";

$config['product_deliveryprice_type'][1] = "유료배송";
$config['product_deliveryprice_type'][2] = "조건부무료배송";
$config['product_deliveryprice_type'][3] = "무료배송";

$config['product_termlimit_yn']['Y'] = "사용";
$config['product_termlimit_yn']['N'] = "사용안함";
$config['product_termlimit_yn_text_color']['Y'] = "#000000";
$config['product_termlimit_yn_text_color']['N'] = "#666666";

$config['product_option_use_yn']['Y'] = "옵션상품";
$config['product_option_use_yn']['N'] = "단일상품";
$config['product_option_use_yn_text_color']['Y'] = "#000000";
$config['product_option_use_yn_text_color']['N'] = "#666666";

$config['product_outside_display_able']['Y'] = "공유가능";
$config['product_outside_display_able']['N'] = "공유불가";

$config['product_display_state']['Y'] = "진열함";
$config['product_display_state']['N'] = "진열안함";
$config['product_display_state_text_color']['Y'] = "#0000ff";
$config['product_display_state_text_color']['N'] = "#ff0000";

$config['product_sale_state']['Y'] = "판매중";
$config['product_sale_state']['N'] = "판매종료";
$config['product_sale_state_text_color']['Y'] = "#0000ff";
$config['product_sale_state_text_color']['N'] = "#ff0000";

$config['product_detail_image_max_count'] = 30;     //한번에 올릴 수 있는 파일 갯수

$config['recently_view_product_max_count'] = 15;    //최근본상품 최대갯수


//FAQ
$config['faq_category'][1] = "주문/결제";
$config['faq_category'][2] = "배송문의";
$config['faq_category'][3] = "반품문의";
$config['faq_category'][8] = "교환문의";
$config['faq_category'][4] = "환불문의";
$config['faq_category'][5] = "회원서비스";
$config['faq_category'][6] = "적립금/쿠폰";
$config['faq_category'][7] = "구매확정";

//1:1 문의
//게시판 (1:1문의)
$config['board_qna_category'][1] = "제품 문의";
$config['board_qna_category'][2] = "배송 문의";
$config['board_qna_category'][3] = "결제 문의";
$config['board_qna_category'][4] = "취소/환불 문의";
$config['board_qna_category'][5] = "반품 문의";
$config['board_qna_category'][6] = "교환 문의";
$config['board_qna_category'][7] = "이벤트 문의";
$config['board_qna_category'][8] = "기타 문의";
//$config['board_qna_category_exclude'] = array(1,2,3,4,5,6,7);


$config['board_qna_secret']['Y'] = "비밀글";
$config['board_qna_secret']['N'] = "공개글";

$config['board_qna_answer_yn']['Y'] = "답변완료";
$config['board_qna_answer_yn']['N'] = "미답변";

$config['board_qna_usestate']['Y'] = "노출";
$config['board_qna_usestate']['N'] = "노출안함";
$config['board_qna_usestate_text_color']['Y'] = "#0000ff";
$config['board_qna_usestate_text_color']['N'] = "#ff0000";

//API 호출 허용 IP들
$config['api_allow_ip'] = array(
    "112.146.73.238" //회사내부
,   "106.243.140.135"   //마포
,   "27.102.214.69" //snsform
,   "27.102.213.35" //snsform service
,   "27.102.213.34" //snsform service
,   "27.102.213.33" //snsform service
);

//휴대폰 앞자리
$config['phonefirstnumber_list'] = array(
    "010"   => "010",
    "011"   => "011",
    "016"   => "016",
    "017"   => "017",
    "018"   => "018",
    "019"   => "019",
    "0130"  => "0130",
    "0502"  => "0502",
    "0504"  => "0504",
    "0505"  => "0505",
    "0506"  => "0506",
    "0507"  => "0507",
    "1541"  => "1541",
    "1595"  => "1595",
    "08217" => "08217"
);


//전화 지역번호
$config['tellocalnumber_list'] = array(
    "02"    => "02",
    "030"   => "030",
    "031"   => "031",
    "032"   => "032",
    "033"   => "033",
    "041"   => "041",
    "042"   => "042",
    "043"   => "043",
    "044"   => "044",
    "050"   => "050",
    "051"   => "051",
    "052"   => "052",
    "053"   => "053",
    "054"   => "054",
    "055"   => "055",
    "060"   => "060",
    "061"   => "061",
    "062"   => "062",
    "063"   => "063",
    "064"   => "064",
    "070"   => "070",
    "080"   => "080",
    "0130"  => "0130",
    "0502"  => "0502",
    "0504"  => "0504",
    "0505"  => "0505",
    "0506"  => "0506"
);

//회원탈퇴
$config['member_withdraw'] = array(
    '1' => '주문하기 불편합니다.'
, '2' => '배송지연이 심합니다.'
, '3' => '사고 싶은 상품이 없습니다.'
, '4' => '결제 시 오류가 있습니다.'
, '5' => '어플 기능 들이 불편해요'
);

//댓글
//관리자명
$config['comment_admin_name'] = $config['site_name_kr'];

$config['comment_table']['product'] = "상품상세";
$config['comment_table']['event'] = "이벤트";

$config['comment_table_name']['product'] = "product_tb";
$config['comment_table_name']['event'] = "event_tb";

$config['comment_table_initial']['product'] = "p_";
$config['comment_table_initial']['event'] = "e_";

$config['comment_table_rep_image']['product'] = "p_rep_image";
$config['comment_table_rep_image']['event'] = "e_rep_image";

$config['comment_table_count_update_query']['product'] = "update product_tb set p_comment_count = p_comment_count + 1 where p_num='#TB_NUM#'";
$config['comment_table_count_update_query']['event'] = "update event_tb set e_comment_count = e_comment_count + 1 where e_num='#TB_NUM#'";

$config['comment_table_count_delete_query']['product'] = "update product_tb set p_comment_count = p_comment_count - 1 where p_num='#TB_NUM#'";
$config['comment_table_count_delete_query']['event'] = "update event_tb set e_comment_count = e_comment_count - 1 where e_num='#TB_NUM#'";

$config['comment_table_select_query']['product'] = "select *, p_name title from product_tb where p_num='#TB_NUM#'";
$config['comment_table_select_query']['event'] = "select *, e_subject title from event_tb where e_num='#TB_NUM#'";

$config['comment_table_link']['product'] = "/product/detail/?p_num=#TB_NUM#";
$config['comment_table_link']['event'] = "/event/detail/?e_num=#TB_NUM#";

//댓글 신고
$config['comment_report_reason'][1] = "영리목적/홍보성";
$config['comment_report_reason'][2] = "개인정보노출";
$config['comment_report_reason'][3] = "욕설/인신공격";
$config['comment_report_reason'][4] = "음란/선정성/불법정보";
$config['comment_report_reason'][5] = "같은 내용 도배";

$config['cmt_gubun']= array(
    'A' => '상품문의'
,   'B' => '배송문의'
,   'C' => '취소문의'
,   'D' => '반품문의'
,   'E' => '교환문의'
,   'F' => '기타문의'
);

$config['order_cancel_gubun']= array(
    'A' => '배송지연'
,   'B' => '배송오류'
,   'C' => '배송지 수정'
,   'D' => '고객변심'
,   'E' => '상품하자'
,   'F' => '결제오류'
);

$config['order_cancel_gubun_admin']= array(
    'A' => '배송지연'
,   'B' => '배송오류'
,   'C' => '배송지 수정'
,   'D' => '고객변심'
,   'E' => '상품하자'
,   'F' => '결제오류'
,   'Z' => '관리자취소'
);


$config['cancel_bank'] = array(
    "04"	=> '국민은행',
    "88"	=> '신한은행',
    "03"	=> '기업은행',
    "20"	=> '우리은행',
    "11"	=> '농협',
    "23"	=> '스탠다드차타드은행',
    "81"	=> '하나은행',
    "48"	=> '신협은행',
    "05"	=> '외환은행',
    "27"	=> '한국씨티은행',
    "71"	=> '우체국',
    "07"	=> '수협중앙회',
    "45"	=> '새마을금고',
    "39"	=> '경남은행',
    "34"	=> '광주은행',
    "31"	=> '대구은행',
    "37"	=> '전북은행',
    "35"	=> '제주은행',
    "32"	=> '부산은행',
    "02"	=> 'KDB산업은행',
    "09"	=> '동양종금'
);

/* Api 키값 */
$config['api_key_name'] = 'key';
$config['api_key_value'] = '1i2n3s4i5g6h7t';
$config['api_doc_version'] = '1.0';

$config['isFormTest'] = 'N';

if($config['isFormTest'] == 'Y'){
    $config['snsform_prefix']   = 'https://pontos.snsform.co.kr:14047';
    $config['form_api_key']     = 'AE0B93D4266BD7E40949A6168D8A6DAA'; //test snsform api key
    $config['form_api_id']      = 'suvin01';
}else{
    $config['snsform_prefix']   = 'https://snsform.co.kr';
    $config['form_api_key']     = '68638F738535E08219EEB08E1CF498C7'; //new snsform api key
    $config['form_api_id']      = 'soobinshop';
}

//snsform 주문조회 api url
$config['form_order_api_url']        = $config['snsform_prefix'].'/sts/stsTradeInfoList.jsp';
$config['form_order_detail_api_url'] = $config['snsform_prefix'].'/sts/stsTradeDetail.jsp';
$config['form_order_cancel_api_url'] = $config['snsform_prefix'].'/sts/stsTradeUpdate.jsp';
$config['prefix_order_url']          = $config['snsform_prefix'].'/shop_v2/pay/form.jsp'; //snsform 단품 주문서 prefix
$config['prefix_cart_url']           = $config['snsform_prefix'].'/shop_v2/pay/form_basket.jsp'; //snsform 장바구니 주문서 prefix
$config['order_code']                = 'D005'; //snsform 주문서 사용 code

//snsform 결제수단코드
$config['form_payway_cd'] = array(
    1 => '신용카드'
,   2 => '계좌이체'
,   3 => '무통장입금'
,   5 => '휴대폰'
,   6 => '카카오페이'
,   7 => '가상계좌'
);

//snsform 결제수단코드
$config['form_status_cd'] = array(
    //status_cd
    60  => '주문대기'
,   61  => '입금확인 중'
,   62  => '입금완료'
,   63  => '배송준비 중'
,   64  => '배송중'
,   65  => '배송완료'
    //after_status_cd
,   66  => '취소신청완료' //취소관리
,   67  => '교환신청완료' //교환관리
,   68  => '반품신청완료' //반품관리
,   166  => '취소완료'
,   167  => '교환완료'
,   168  => '반품완료'

);

$config['del_comp_cd'] = array(
    '01' => '우체국택배'
,'04' => 'CJ대한통운'
,'05' => '한진택배'
,'06' => '로젠택배'
,'08' => '롯데택배'
,'10' => 'KGB택배'
,'11' => '일양로지스'
,'12' => 'EMS'
,'13' => 'DHL'
,'14' => 'UPS'
,'15' => 'GTX로지스'
,'17' => '천일택배'
,'18' => '건영택배'
,'21' => 'Fedex'
,'22' => '대신택배'
,'23' => '경동택배'
,'24' => 'CVSnet편의점택배'
,'25' => 'TNTExpress'
,'26' => 'USPS'
,'28' => 'GSMNtoN(인로스)'
,'29' => '에어보이익스프레스'
,'32' => '합동택배'
,'33' => 'DHL Global Mail'
,'34' => 'i-Parcel'
,'37' => '범한판토스'
,'40' => '굿투럭'
,'41' => '한서호남택배'
,'42' => '롯데글로벌'
,'43' => '농협택배'
);

$config['comment_limit'] = 10; //코멘트 페이지당 노출 수


/*프로필 이미지 경로*/
$config['profile_file_path'] = DOCROOT . $config['upload_path_web'] . "/uploads/prof_img";
$config['profile_file_path_web'] = $config['upload_path_web'] . "/uploads/prof_img";