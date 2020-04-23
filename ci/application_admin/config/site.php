<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 사이트 공통 환경설정
 */

//사이트 정보
$config['site_name_kr']         = '옷쟁이들';                            //사이트명(한글)
$config['site_name_en']         = '';                   //사이트명(영문)
$config['site_domain']          = SITE_DOMAIN;                          //도메인(예: aaa.co.kr)
$config['site_domain_cookie']   = '.'.SITE_DOMAIN;                      //쿠키 도메인(예: .aaa.co.kr)
$config['site_http']            = "http://m." . $config['site_domain'];   //HTTP
$config['site_https']           = "https://m." . $config['site_domain'];  //HTTPS
$config['site_img_http']        = "https://img." . $config['site_domain'];  //이미지(https)

if( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) {
    $config['default_http'] = $config['site_https'];
}else{
    $config['default_http'] = $config['site_http'];
}

//결과 코드
$config['status_code']['success']   = "000";
$config['status_code']['noauth']    = "100";
$config['status_code']['error']     = "200";
$config['status_code']['overlap']   = "300";    //중복


//공구폼(09sns)
$config['order_cpid'] = "kakaomys";


//로그인유지 쿠키 만료기간
$config['autologin_expire_days'] = 30;

//09FORM 상점코드
$config['09form_code'] = "";

//SEED 암호키
$config['seed_key'] = "";
$config['seed_iv'] = "";

//구글 서버키
$config['google_server_key'] = "AAAANUo0amk:APA91bFF0qu2gymOWaj3wimBnE8ZzFaoTpfu_DygJSg191dM3Zi96lkMYmyCmWUK5WPWrLLuOogZOHWM1OsptCxvAI2yb9wIrpLxwkGkj6tg0VUQKGHY-m4PSKzY89Ll9dFQWHuoT8fb";
$config['google_web_key'] = "";

//bitly
$config['bitly_login'] = 'o_4o3qe27s8k';
$config['bitly_token'] = '62f72809b5d46dac325b07254d185bd35da5e6ce';

//외부 사이트 설정
$config['app_id'] = "kr.co.cloma.app";
$config['ios_link_key'] = "";
$config['app_market_url'] = "https://play.google.com/store/apps/details?id=" . $config['app_id'];                                 //구글마켓으로(앱용)
$config['app_market_url_web'] = "https://play.google.com/store/apps/details?id=" . $config['app_id'];   //구글마켓으로(웹용)

$config['app_main_string'] = "";
$config['app_version_string'] = $config['app_main_string'] . "Version";
$config['app_version_code_string'] = $config['app_main_string'] . "VersionCode";

$config['dynamic_link_http'] = "https://cloma.page.link";

//기본 관리자명
$config['admin_name'] = $config['site_name_kr'];

//경로 설정
$config['upload_path'] = DOCROOT . "/uploads";
$config['upload_path_web'] = "/uploads";

//리뷰 경로 관련
$config['review_img_path'] = DOCROOT . "/uploads/review";
$config['review_img_path_web'] = "/uploads/review";

//파일 경로
$config['qna_file_head'] = "/files/qna";
$config['qna_file_path'] = HOMEPATH . $config['qna_file_head'];

/*특가전 배너 이미지 경로*/
$config['special_offer_file_path'] = DOCROOT . $config['upload_path_web'] . "/special_offer";
$config['special_offer_file_path_web'] = $config['upload_path_web'] . "/special_offer";

/*메인테마 이미지 경로*/
$config['main_thema_file_path'] = DOCROOT . $config['upload_path_web'] . "/main_thema";
$config['main_thema_file_path_web'] = $config['upload_path_web'] . "/main_thema";

$config['smarteditor_file_path'] = DOCROOT . $config['upload_path_web'] . "/smarteditor";
$config['smarteditor_file_path_web'] = $config['upload_path_web'] . "/smarteditor";

$config['product_image_path'] = DOCROOT . $config['upload_path_web'] . "/product";
$config['product_image_path_web'] = $config['upload_path_web'] . "/product";

$config['banner_image_path'] = DOCROOT . $config['upload_path_web'] . "/banner";
$config['banner_image_path_web'] = $config['upload_path_web'] . "/banner";

$config['proposal_image_path'] = DOCROOT . $config['upload_path_web'] . "/proposal";
$config['proposal_image_path_web'] = $config['upload_path_web'] . "/proposal";

$config['app_push_image_path'] = DOCROOT . $config['upload_path_web'] . "/app_push";
$config['app_push_image_path_web'] = $config['upload_path_web'] . "/app_push";

$config['app_splash_image_path'] = DOCROOT . $config['upload_path_web'] . "/app_splash";
$config['app_splash_image_path_web'] = $config['upload_path_web'] . "/app_splash";

$config['event_image_path'] = DOCROOT . $config['upload_path_web'] . "/event";
$config['event_image_path_web'] = $config['upload_path_web'] . "/event";

$config['product_desc_image_path'] = DOCROOT . $config['upload_path_web'] . "/product_desc";
$config['product_desc_image_path_web'] = $config['upload_path_web'] . "/product_desc";

$config['event_gift_head'] = "/files/gift";
$config['event_gift_path'] = HOMEPATH . $config['event_gift_head'];

$config['category_md_image_path_web'] = $config['upload_path_web'] . "/category_md";
$config['category_md_image_path'] = DOCROOT . $config['category_md_image_path_web'];

$config['member_profile_dir'] = "/files/profile_img";
$config['member_profile_path'] = HOMEPATH . $config['member_profile_dir'];


//이미지 썸네일 사이즈 : [0]가로(px), [1]세로(px), [2]자르기 여부(Y|N)
$config['product_rep_image_size'][1] = array("720", "430", 'Y');
$config['product_detail_image_size'][1] = array("720", "5000", 'N');
$config['banner_image_size'][1] = array("720", "1280", 'N');
$config['event_rep_image_size'][1] = array("720", "1280", 'N');

$config['upload_max_size'] = 20000;         //20MB
$config['upload_total_max_size'] = 100000;  //100MB

//목록 출력수
$config['list_per_page'][2] = "2개씩 보기";     //테스트용
$config['list_per_page'][5] = "5개씩 보기";     //테스트용
$config['list_per_page'][10] = "10개씩 보기";   //테스트용
$config['list_per_page'][20] = "20개씩 보기";
$config['list_per_page'][30] = "30개씩 보기";
$config['list_per_page'][50] = "50개씩 보기";
$config['list_per_page'][100] = "100개씩 보기";
$config['list_per_page'][999999] = "999999개씩 보기";

//요일 (date("N"))
$config['week_name'][1] = "월";
$config['week_name'][2] = "화";
$config['week_name'][3] = "수";
$config['week_name'][4] = "목";
$config['week_name'][5] = "금";
$config['week_name'][6] = "토";
$config['week_name'][7] = "일";
$config['week_name_text_color'][1] = "#000000";
$config['week_name_text_color'][2] = "#000000";
$config['week_name_text_color'][3] = "#000000";
$config['week_name_text_color'][4] = "#000000";
$config['week_name_text_color'][5] = "#000000";
$config['week_name_text_color'][6] = "#0000ff";
$config['week_name_text_color'][7] = "#ff0000";


//관리자 계정
$config['adminuser_level'][1] = "운영";
$config['adminuser_level'][2] = "전체";
$config['adminuser_level'][3] = "제안서등록 제한";

$config['adminuser_usestate']['Y'] = "사용";
$config['adminuser_usestate']['N'] = "사용중지";
$config['adminuser_usestate_text_color']['Y'] = "#0000ff";
$config['adminuser_usestate_text_color']['N'] = "#ff0000";


//회원
$config['member_division'][1] = "자체";
$config['member_division'][2] = "SNS";

$config['member_sns_site'][1] = "카카오";
$config['member_sns_site'][2] = "네이버";
$config['member_sns_site'][3] = "페이스북";
$config['member_sns_site'][4] = "구글";

$config['member_join_path'][1] = "앱A";
$config['member_join_path'][2] = "모바일웹";
$config['member_join_path'][3] = "PC";
$config['member_join_path'][4] = "앱I";

$config['member_state'][1] = "정상";
$config['member_state'][2] = "제재";
$config['member_state'][3] = "탈퇴";
$config['member_state'][4] = "대기";
$config['member_state_text_color'][1] = "#0000ff";
$config['member_state_text_color'][2] = "#FF7F27";
$config['member_state_text_color'][3] = "#ff0000";
$config['member_state_text_color'][4] = "#00ff00";


//상품
$config['product_division'][1] = "일반상품";
$config['product_division'][2] = "이벤트상품";

$config['product_category'][1] = "오늘추천";
$config['product_category'][2] = "시즌패션";
$config['product_category'][3] = "해외상품";

$config['product_detail_add']['weight'] = "중량/용량";
$config['product_detail_add']['delivery_info'] = "배송정보";
$config['product_detail_add']['deliveryprice_text'] = "배송비";

$config['product_taxation'][1] = "과세";
$config['product_taxation'][2] = "영세";
$config['product_taxation'][3] = "면세";

$config['product_wish_raise_yn']['Y'] = "사용";
$config['product_wish_raise_yn']['N'] = "사용안함";

$config['product_share_raise_yn']['Y'] = "사용";
$config['product_share_raise_yn']['N'] = "사용안함";

$config['product_deliveryprice_type'][1] = "유료배송";
$config['product_deliveryprice_type'][2] = "조건부 무료배송";
$config['product_deliveryprice_type'][3] = "무료배송";

$config['product_termlimit_yn']['Y'] = "사용";
$config['product_termlimit_yn']['N'] = "사용안함";
$config['product_termlimit_yn_text_color']['Y'] = "#0000FF";
$config['product_termlimit_yn_text_color']['N'] = "#777777";

$config['product_display_info1']['today'] = "오늘오픈";
$config['product_display_info1']['deadline'] = "마감임박";
$config['product_display_info1']['limit_qty'] = "한정수량";
$config['product_display_info1']['encore1'] = "1차 앵콜";
$config['product_display_info1']['encore2'] = "2차 앵콜";
$config['product_display_info1']['encore3'] = "3차 앵콜";
$config['product_display_info2']['free_delivery'] = "무료배송";
$config['product_display_info2']['conditional_delivery'] = "조건부 무료배송";
$config['product_display_info3']['discount_name'] = "할인가";
$config['product_display_info4']['event_price'] = "이벤트특가";
$config['product_display_info4']['Eventgift'] = "사은품증정";

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

$config['product_stock_state']['Y'] = "있음";
$config['product_stock_state']['N'] = "품절";
$config['product_stock_state_text_color']['Y'] = "#0000ff";
$config['product_stock_state_text_color']['N'] = "#ff0000";

$config['product_detail_image_max_count'] = 30;     //한번에 올릴 수 있는 파일 갯수

//상품 MD
$config['product_md_division'][1] = "메인상품30";
//$config['product_md_division'][2] = "지금입기 좋은 아우터";
$config['product_md_division'][3] = "편하고 이쁜 밴딩팬츠 맛집~";
$config['product_md_division'][4] = "코디걱정 없는 상하의세트!";


$config['product_option_buy_cnt_view']['Y'] = "노출함";
$config['product_option_buy_cnt_view']['N'] = "노출안함";
$config['product_option_buy_cnt_view_text_color']['Y'] = "#0000ff";
$config['product_option_buy_cnt_view_text_color']['N'] = "#ff0000";

//상품정보제공공시
$config['product_info_tab_view']['Y'] = "노출함";
$config['product_info_tab_view']['N'] = "노출안함";
$config['product_info_tab_view_text_color']['Y'] = "#0000ff";
$config['product_info_tab_view_text_color']['N'] = "#ff0000";


//게시판 (고객센터)
$config['board_help_top_yn']['Y'] = "적용";
$config['board_help_top_yn']['N'] = "미적용";

$config['board_help_division'][1] = "공지사항";
$config['board_help_division'][2] = "자주묻는 질문";
$config['board_help_division'][3] = "이용약관";
$config['board_help_division'][4] = "개인정보보호정책";
$config['board_help_division'][6] = "개인정보 제3자 제공동의";
$config['board_help_division'][7] = "이벤트 쇼핑정보 수신동의";


$config['board_help_usestate']['Y'] = "노출";
$config['board_help_usestate']['N'] = "노출안함";
$config['board_help_usestate_text_color']['Y'] = "#0000ff";
$config['board_help_usestate_text_color']['N'] = "#ff0000";

//FAQ
$config['faq_category'][1] = "주문/결제";
$config['faq_category'][2] = "배송문의";
$config['faq_category'][3] = "반품";
$config['faq_category'][4] = "환불문의";
$config['faq_category'][5] = "회원서비스";
$config['faq_category'][6] = "적립금/쿠폰";
$config['faq_category'][7] = "구매확정";
$config['faq_category'][8] = "교환";

//게시판 (1:1문의)
$config['board_qna_category'][1] = "제품문의";
$config['board_qna_category'][2] = "배송문의";
$config['board_qna_category'][3] = "결제문의";

$config['board_qna_secret']['Y'] = "비밀글";
$config['board_qna_secret']['N'] = "공개글";

$config['board_qna_answer_yn']['Y'] = "답변완료";
$config['board_qna_answer_yn']['P'] = "처리중";
$config['board_qna_answer_yn']['N'] = "미답변";

$config['board_qna_usestate']['Y'] = "노출";
$config['board_qna_usestate']['N'] = "노출안함";
$config['board_qna_usestate_text_color']['Y'] = "#0000ff";
$config['board_qna_usestate_text_color']['N'] = "#ff0000";


//배너
$config['banner_division'][1] = "메인배너1(오늘추천)";
$config['banner_division'][2] = "메인배너2(시즌패션)";
$config['banner_division'][3] = "이벤트배너";
$config['banner_division'][4] = "사이드메뉴배너";

$config['banner_termlimit_yn']['Y'] = "노출기간사용";
$config['banner_termlimit_yn']['N'] = "상시노출";

$config['banner_usestate']['Y'] = "활성";
$config['banner_usestate']['N'] = "비활성";
$config['banner_usestate_text_color']['Y'] = "#0000ff";
$config['banner_usestate_text_color']['N'] = "#ff0000";


//팝업
$config['popup_division'][1] = "일반 팝업";
$config['popup_division'][2] = "이벤트 팝업";
$config['popup_division'][3] = "메인 플로팅";
$config['popup_division'][4] = "전체 팝업";
$config['popup_division'][5] = "컨펌 팝업";

$config['popup_termlimit_yn']['Y'] = "노출기간사용";
$config['popup_termlimit_yn']['N'] = "상시노출";

$config['popup_target_type'][1] = "일반";
$config['popup_target_type'][2] = "새창";

$config['popup_platform'][1] = "전체";
$config['popup_platform'][2] = "APP";
$config['popup_platform'][3] = "웹(전체)";
$config['popup_platform'][4] = "웹(Android)";
$config['popup_platform'][5] = "웹(IOS)";

$config['popup_usestate']['Y'] = "활성";
$config['popup_usestate']['N'] = "비활성";
$config['popup_usestate_text_color']['Y'] = "#0000ff";
$config['popup_usestate_text_color']['N'] = "#ff0000";


//이벤트
$config['event_division'][1] = "자동 출석체크 이벤트";
$config['event_division'][2] = "일반 이벤트";
$config['event_division'][3] = "매일 룰렛이벤트";

$config['event_termlimit_yn']['Y'] = "기간한정이벤트";
$config['event_termlimit_yn']['N'] = "상시이벤트";

$config['event_proc_state']['Y'] = "진행중";
$config['event_proc_state']['N'] = "종료";
$config['event_proc_state_text_color']['Y'] = "#0000ff";
$config['event_proc_state_text_color']['N'] = "#6F6F6F";

$config['event_display_state']['Y'] = "노출";
$config['event_display_state']['N'] = "노출안함";
$config['event_display_state_text_color']['Y'] = "#0000ff";
$config['event_display_state_text_color']['N'] = "#ff0000";

$config['event_alert_yn']['Y'] = "출력";
$config['event_alert_yn']['N'] = "출력안함";
$config['event_alert_yn_text_color']['Y'] = "#0000ff";
$config['event_alert_yn_text_color']['N'] = "#ff0000";

$config['event_after_type'][0] = "사용안함";
$config['event_after_type'][1] = "MD상품";
$config['event_after_type'][2] = "구글마켓";
$config['event_after_type'][3] = "카스공유";
$config['event_after_type'][4] = "인기상품팝업";

$config['event_rep_image_type'][1] = "일반";
$config['event_rep_image_type'][2] = "월별";

$config['event_content_type'][1] = "HTML";
$config['event_content_type'][2] = "이미지";


//APP 버전관리
$config['app_version_offer_type'][1] = "일반 업데이트";
$config['app_version_offer_type'][2] = "강제 업데이트";
$config['app_version_offer_type'][3] = "알림없음";

$config['app_version_os_type'][1] = "안드로이드";
$config['app_version_os_type'][2] = "IOS";

//APP 푸시관리
$config['app_push_type']['product'] = "상품푸시";
$config['app_push_type']['point'] = "적림금 푸시";

$config['app_push_os_type'][1] = "안드로이드";
$config['app_push_os_type'][2] = "IOS";

$config['app_push_noti_type'][1] = "소리";
$config['app_push_noti_type'][2] = "무음";

$config['app_push_badge']['Y'] = "사용";
$config['app_push_badge']['N'] = "미사용";
$config['app_push_badge_text_color']['Y'] = "#0000ff";
$config['app_push_badge_text_color']['N'] = "#333";

$config['app_push_idle']['Y'] = "켜질때";
$config['app_push_idle']['N'] = "즉시";

$config['app_stock_flag']['Y'] = "진행";
$config['app_stock_flag']['N'] = "정지";

$config['app_push_display_state']['Y'] = "노출";
$config['app_push_display_state']['N'] = "미노출";
$config['app_push_display_state_text_color']['Y'] = "#0000ff";
$config['app_push_display_state_text_color']['N'] = "#ff0000";

$config['app_push_state'][1] = "발송대기";
$config['app_push_state'][2] = "발송중";
$config['app_push_state'][3] = "발송완료";
$config['app_push_state_text_color'][1] = "#000000";
$config['app_push_state_text_color'][2] = "#ff0000";
$config['app_push_state_text_color'][3] = "#0000ff";

//APP 메인팝업 관리
$config['app_popup_position'][1] = "메인팝업";  //기본값

$config['app_popup_os_type'][1] = "안드로이드";  //기본값
$config['app_popup_os_type'][2] = "IOS";
$config['app_popup_os_type'][3] = "전체";

$config['app_popup_size_type'][1] = "전체팝업";
$config['app_popup_size_type'][2] = "작은팝업";  //기본값

$config['app_popup_content_type'][1] = "상품";  //기본값
$config['app_popup_content_type'][2] = "기획전";

$config['app_popup_view_page']['main'] = "메인페이지";  //기본값
$config['app_popup_view_page']['product'] = "상품상세페이지";

$config['app_popup_view_target']['A'] = "모든대상";  //기본값
$config['app_popup_view_target']['B'] = "sns로그인";
$config['app_popup_view_target']['C'] = "비sns로그인";

$config['app_popup_btn_type'][1] = "닫기만";               //기본값
$config['app_popup_btn_type'][2] = "다시보지않기 + 닫기";
$config['app_popup_btn_type'][3] = "오늘보지않기 + 닫기";

$config['app_popup_back_close_yn']['N'] = "불가";     //기본값
$config['app_popup_back_close_yn']['Y'] = "가능";

$config['app_popup_termlimit_yn']['Y'] = "사용";
$config['app_popup_termlimit_yn']['N'] = "사용안함";

$config['app_popup_display_yn']['Y'] = "노출";
$config['app_popup_display_yn']['N'] = "숨김";
$config['app_popup_display_yn_text_color']['Y'] = "#0000ff";
$config['app_popup_display_yn_text_color']['N'] = "#ff0000";

$config['app_popup_image_path'] = DOCROOT . $config['upload_path_web'] . "/main_pop";
$config['app_popup_image_path_web'] = $config['upload_path_web'] . "/main_pop";

//공통관리
$config['common_code'][1] = "배송정보";
$config['common_code'][2] = "고객센터정보";

$config['common_code'][3] = "상품정보제공공시";
$config['common_code'][4] = "상품옵션창(구매클릭시)정보";


$config['common_usestate']['Y'] = "활성";
$config['common_usestate']['N'] = "비활성";
$config['common_usestate_text_color']['Y'] = "#0000ff";
$config['common_usestate_text_color']['N'] = "#ff0000";


//공구폼(09sns)
$config['order_domain'] = "09sns.co.kr";
$config['order_site_http'] = "http://" . $config['order_domain'];
//주문조회 페이지 URL
$config['order_list_url'] = $config['order_site_http'] . "/?pn=service.guest.order.list&cpid=kakaomys&m=1";
$config['order_link_head'] = $config['order_site_http'] . "/?pn=product.view.v8&cpid=" . $config['order_cpid'] . "&m=1&pcode=";

//매일참여
//추첨실행일(등록일 기준 + ?일)
$config['everyday_winner_day'] = 2;

$config['everyday_usestate']['Y'] = "진행"; 
$config['everyday_usestate']['N'] = "종료";
$config['everyday_usestate_text_color']['Y'] = "#0000ff";
$config['everyday_usestate_text_color']['N'] = "#ff0000";

$config['everyday_displaystate']['Y'] = "노출";
$config['everyday_displaystate']['N'] = "노출안함";
$config['everyday_displaystate_text_color']['Y'] = "#0000ff";
$config['everyday_displaystate_text_color']['N'] = "#ff0000";

$config['everyday_active_winner_yn']['Y'] = "당첨";
$config['everyday_active_winner_yn']['N'] = "";


//출석이벤트
$config['attend_day_info_btn']['당첨'] = "당첨";
$config['attend_day_info_btn']['응모'] = "응모";

//APP 스플래시
$config['app_splash_os_type'][1] = "안드로이드";
$config['app_splash_os_type'][2] = "IOS";

$config['app_splash_state'][1] = "적용대기";
$config['app_splash_state'][2] = "적용중";
$config['app_splash_state'][3] = "적용완료";
$config['app_splash_state_text_color'][1] = "#000000";
$config['app_splash_state_text_color'][2] = "#FF0000";
$config['app_splash_state_text_color'][3] = "#0000FF";

$config['app_splash_usestate']['Y'] = "사용";
$config['app_splash_usestate']['N'] = "사용안함";
$config['app_splash_usestate_text_color']['Y'] = "#000000";
$config['app_splash_usestate_text_color']['N'] = "#FF0000";

$config['splash_image_size'][1] = array("1500", "2700", "Y");

//롤링배너 이미지 사이즈
$config['special_offer_banner_image_size'][1] = array("1000", "426", "Y");

//APP 상태바
$config['app_statusbar_os_type'][1] = "안드로이드";
$config['app_statusbar_os_type'][2] = "IOS";

$config['app_statusbar_state'][1] = "적용대기";
$config['app_statusbar_state'][2] = "적용중";
$config['app_statusbar_state'][3] = "적용완료";
$config['app_statusbar_state_text_color'][1] = "#000000";
$config['app_statusbar_state_text_color'][2] = "#FF0000";
$config['app_statusbar_state_text_color'][3] = "#0000FF";

$config['app_statusbar_usestate']['Y'] = "사용";
$config['app_statusbar_usestate']['N'] = "사용안함";
$config['app_statusbar_usestate_text_color']['Y'] = "#000000";
$config['app_statusbar_usestate_text_color']['N'] = "#FF0000";

//이벤트 기프티콘
$config['event_gift_state']['1'] = "등록완료";
$config['event_gift_state']['2'] = "발급완료";
$config['event_gift_state']['3'] = "만료";
$config['event_gift_state_text_color']['1'] = "#000000";
$config['event_gift_state_text_color']['2'] = "#0000FF";


//카테고리 MD
$config['category_md_division'][1] = "무료배송";
$config['category_md_division'][2] = "1만원대";
$config['category_md_division'][3] = "재구매많은";
$config['category_md_division'][4] = "상품카테고리";
$config['category_md_division'][5] = "신상품";

$config['category_md_state']['Y'] = "활성";
$config['category_md_state']['N'] = "비활성";
$config['category_md_state_text_color']['Y'] = "#000000";
$config['category_md_state_text_color']['N'] = "#FF0000";

//상품단축URL 사이트
//$config['product_ref_site']['kakao_story'] = "카카오스토리";
//$config['product_ref_site']['kakao_plus'] = "플러스친구";
//$config['product_ref_site']['kakao_banner'] = "카카오배너";
$config['product_ref_site']['app_push'] = "앱푸시";

//[0]웹, [1]앱(웹), [2]앱(마켓) , [3]축약x
//$config['product_ref_site_type']['kakao_story'] = array("N", "Y", "Y", "N");
//$config['product_ref_site_type']['kakao_plus'] = array("N", "Y", "Y", "N");
//$config['product_ref_site_type']['kakao_banner'] = array("N", "Y", "Y", "N");
$config['product_ref_site_type']['app_push'] = array("Y", "N", "N", "Y");

$config['comment_table']['product'] = "상품상세";
$config['comment_table']['event'] = "이벤트";

$config['comment_table_detail_url']['product'] = "/product/detail/?pop=1&p_num=";
$config['comment_table_detail_url']['event'] = "";

$config['comment_blind']['N'] = "정상";
$config['comment_blind']['Y'] = "참고";/*블라인드*/
$config['comment_blind_text_color']['N'] = "#000000";
$config['comment_blind_text_color']['Y'] = "#ff0000";

$config['comment_admin']['Y'] = "관리글";
$config['comment_admin']['N'] = "일반글";

$config['comment_display_state']['Y'] = "노출";
$config['comment_display_state']['N'] = "노출안함";
$config['comment_display_state_text_color']['Y'] = "#000000";
$config['comment_display_state_text_color']['N'] = "#ff0000";

$config['comment_table_initial']['product'] = "p_";
$config['comment_table_initial']['event'] = "e_";

$config['comment_table_num_name']['product'] = "p_name";
$config['comment_table_num_name']['event'] = "e_subject";

$config['comment_table_query']['product']['from'] = "from product_tb";
$config['comment_table_query']['product']['where'] = "where 1 = 1";
$config['comment_table_query']['product']['search_field'] = array("p_name", "p_detail", "p_manufacturer", "p_supplier");
$config['comment_table_query']['product']['orderby'] = "order by p_num desc";

$config['comment_table_query']['event']['from'] = "from event_tb";
$config['comment_table_query']['event']['where'] = "where 1= 1";
$config['comment_table_query']['event']['search_field'] = array("e_subject", "e_content");
$config['comment_table_query']['event']['orderby'] = "order by e_num desc";

// 적립금
$config['point_display_state']['A'] = "텍스트";
$config['point_display_state']['B'] = "포토";
$config['point_display_state']['C'] = "지급안함";
$config['point_display_state_text_color']['A'] = "#000000";
$config['point_display_state_text_color']['B'] = "#ff0000";
$config['point_display_state_text_color']['C'] = "#0054FF";

//신고
$config['report_table']['comment'] = "댓글";

//이벤트 당첨
$config['event_winner_state'][1] = "응모";
$config['event_winner_state'][2] = "당첨";
$config['event_winner_state'][3] = "상품지급완료";

//외부 접속 허용 아이피
$config['api_allow_ip'] = array(
    "112.146.73.238" //회사내부
,   "27.102.214.69" //snsform test
,   "27.102.213.35" //snsform service
,   "27.102.213.34" //snsform service
,   "27.102.213.33" //snsform service
);


$config['cmt_gubun']= array(
    'A' => '상품문의'
,   'B' => '배송문의'
,   'C' => '취소문의'
,   'D' => '반품문의'
,   'E' => '교환문의'
,   'F' => '기타문의'
);


//카카오광고 상품
$config['kakao_product_prod_type']['G'] = "일반";
$config['kakao_product_prod_type']['B'] = "브랜드";

$config['kakao_product_display_state']['Y'] = "노출";
$config['kakao_product_display_state']['N'] = "노출안함";
$config['kakao_product_display_state_text_color']['Y'] = "#000000";
$config['kakao_product_display_state_text_color']['N'] = "#ff0000";

//카카오스토리 광고 상품
$config['kstory_product_display_state']['Y'] = "노출";
$config['kstory_product_display_state']['N'] = "노출안함";
$config['kstory_product_display_state_text_color']['Y'] = "#000000";
$config['kstory_product_display_state_text_color']['N'] = "#ff0000";


// 자주 사용하는 문구
$config['wd_use']= array(
    'comment' => '댓글',
    'board_qna' => '1:1문의'
);

// 댓글 상태
$config['comment_flag']= array(
    'W' => '접수',
    'P' => '처리중',
    'C' => '완료'
);

$config['product_cate_array']= array(
      '48' => '패션잡화'
    , '49' => '아우터'
    , '50' => '상의'
    , '51' => '하의'
    , '52' => '원피스'
    , '53' => '홈웨어'
    , '54' => '땡처리'
);

$config['order_cancel_gubun']= array(
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

//snsform 결제수단코드
$config['form_status_cd'] = array(
    //status_cd
    60  => '주문대기'
,   61  => '입금확인 중'
,   62  => '신규주문'
,   63  => '배송준비 중'
,   64  => '배송중'
,   65  => '배송완료'
    //after_status_cd
,   66  => '취소관리'
,   67  => '교환관리'
,   68  => '반품관리'
,   166  => '취소완료'
,   167  => '교환완료'
,   168  => '반품완료'

);

$config['delivery_company'] = array(
      '01' => '우체국택배'
    , '04' => 'CJ 대한통운'
    , '05' => '한진택배'
    , '06' => '로젠택배'
    , '08' => '롯데택배'
    , '10' => 'KGB 택배'
    , '11' => '일양로지스'
    , '12' => 'EMS'
    , '13' => 'DHL'
    , '14' => 'UPS'
    , '15' => 'GTX 로지스'
    , '17' => '천일택배'
    , '18' => '건영택배'
    , '21' => 'Fedex'
    , '22' => '대신택배'
    , '23' => '경동택배'
    , '24' => 'CVSnet 편의점택배'
    , '25' => 'TNT Express'
    , '26' => 'USPS'
    , '28' => 'GSMNtoN(인로스)'
    , '29' => '에어보이익스프레스'
    , '32' => '합동택배'
    , '33' => 'DHL Global Mail'
    , '34' => 'i-Parcel'
    , '37' => '범한판토스'
    , '40' => '굿투럭'
    , '41' => '한서호남택배'
    , '42' => '롯데글러벌'
);


//snsform 결제수단코드
$config['form_payway_cd'] = array(
    1 => '신용카드'
,   2 => '계좌이체'
,   3 => '무통장입금'
,   5 => '휴대폰'
,   6 => '카카오페이'
,   7 => '가상계좌'
);

$config['isFormTest'] = 'N';

$config['member_withdraw'] = array(
      '1' => '주문하기 불편합니다.'
    , '2' => '배송지연이 심합니다.'
    , '3' => '사고 싶은 상품이 없습니다.'
    , '4' => '결제 시 오류가 있습니다.'
    , '5' => '어플 기능 들이 불편해요'
);