/**
 * 네이버 검색 이벤트 (~20170922)
 * - 넘겨받는 값 : $sess_ref
 **/
var now = new Date();
var ymd = number_only(get_ymd(now));
var close_ymd = 20171122;

/**
 * 이벤트 기간 체크
 * @returns {boolean}
 */
function nse_date_check() {
    if( parseInt(ymd) > parseInt(close_ymd) ) {
        return false;
    }
    else {
        return true;
    }
}//end of nse_date_check()

/**
 * 네이버 유입인지
 * @returns {boolean}
 */
function nse_ref_check() {
    // if( typeof(sess_ref) != 'undefined' && sess_ref.indexOf('naver') != -1 ) {
    //     return true;
    // }
    if( document.referrer.indexOf('naver.com') != -1 ) {
        return true;
    }

    return false;
}//end of nse_ref_check()

/**
 * 네이버 검색 이벤트 안내 팝업
 * @returns {boolean}
 */
function naver_search_event_pop_1() {
    if( !empty($.cookie('cki_nse_pop_1_yn')) && $.cookie('cki_nse_pop_1_yn') == 'Y' ) {
        return false;
    }
    if( appCheck() || !nse_date_check() || nse_ref_check() ) {
        return false;
    }

    var container = $('<div></div>');

    // var tar_url = 'http://naver.com';
    // if( $.browser.mobile ) {
    //     tar_url = 'http://m.naver.com';
    // }
    // var tar_url = 'https://goo.gl/4x54dU';
    var tar_url = 'https://goo.gl/VMAUMU';

    var html = '';
    html += '<div>';
    html += '   <a href="#none" onclick="window.open(\'' + tar_url + '\');"><img src="' + img_http + '/images/event/naver_search_event_p1.png" style="width:100%;" alt="" /></a>';
    // html += '   <a href="#none" onclick="go_link(\'' + tar_url + '\');"><img src="/images/event/naver_search_event_p1.png" style="width:100%;" alt="" /></a>';
    html += '   <a href="#none" class="ico btn_nsep_close_1" style="position:absolute;bottom:0;left:0;width:50%;height:9%;">다시보지않기</a>';
    html += '   <a href="#none" class="ico btn_nsep_close_2" style="position:absolute;bottom:0;left:50%;width:50%;height:9%;">닫기</a>';
    html += '</div>';

    $(container).append(html);

    modalPop.createPop('', container);
    modalPop.show({
        'skin':'trans',
        'close_btn_class':'black',
        'close_abs':true,
        'content_class':'mg0'
    });

    //다시보지않기
    $('.btn_nsep_close_1').on('click', function(){
        $.cookie('cki_nse_pop_1_yn', 'Y', { expires:9999 });
        modalPop.hide();

    });
    //닫기
    $('.btn_nsep_close_2').on('click', function(){
        $.cookie('cki_nse_pop_1_yn', 'Y', { expires:1 });
        modalPop.hide();
    });
    $('.modal .close').on('click', function(){
        $.cookie('cki_nse_pop_1_yn', 'Y', { expires:1 });
        modalPop.hide();
    });
}//end of naver_search_event_pop_1()

/**
 * 이벤트 참여 버튼 팝업
 */
function naver_search_event_pop_2() {
    if( !empty($.cookie('cki_nse_pop_2_yn')) && $.cookie('cki_nse_pop_2_yn') == 'Y' ) {
        return false;
    }
    if( appCheck() || !nse_date_check() || !nse_ref_check() ) {
        return false;
    }

    // var img = '/images/event/naver_search_event_p2.png';

    var container = $('<div></div>');
    var img = $('<img src="' + img_http + '/images/event/naver_search_event_p2.png" style="width:100%;cursor:pointer;" alt="" />');

    // var html = '';
    // html += '<div>';
    // html += '   <a href="#none" onclick=""><img src="/images/event/naver_search_event_p2.png" style="width:100%;" alt="" /></a>';
    // html += '</div>';

    $(container).append(img);

    $(img).one('load', function(){
        modalPop.createPop('', container);
        modalPop.show({
            'skin':'trans',
            'close_btn_class':'black',
            'close_abs':true,
            'center':true
        });

        $(img).on('click', function(){
            // var expDate = new Date();
            // expDate.setTime(expDate.getTime() + (1 * 60 * 1000));   //1min
            // $.cookie('cki_nse_pop_2_yn', 'Y', {path: '/', expires: expDate});
            go_link('/event/detail/?e_code=naver_search_20170922');
        });
        $('.modal .close').on('click', function(){
            // var expDate = new Date();
            // expDate.setTime(expDate.getTime() + (1 * 60 * 1000));   //1min
            $.cookie('cki_nse_pop_2_yn', 'Y', { expires:1 });
            modalPop.hide();
        });
    });
}//end of naver_search_event_pop_2()

// /**
//  * 참여여부 확인
//  */
// function naver_search_event_insert_check(ph) {
//     if( empty(ph) ) {
//         alert('잘못된 접근입니다.(No Phone)');
//         return false;
//     }
//
//     $.ajax({
//         url : '/event/naver_search_insert',
//         data : {mode:1, ph:ph},
//         type : 'post',
//         dataType : 'json',
//         success : function(result) {
//             if( result.status == status_code['success'] ) {
//                 // sms_auth_nologin_pop();
//             }
//             else {
//                 alert(result.message);
//             }
//         }
//     });
// }//end of naver_search_event_insert_check()

/**
 * 휴대폰인증 (비회원) 팝업
 */
function sms_auth_nologin_pop() {
    var container = $('<div></div>');

    $(container).load('/auth/sms_auth_nologin_pop/?ret_call=naver_search_event_insert&silent=y');

    modalPop.createPop('휴대폰인증', container);
    modalPop.show({'skin':'gray2'});
}//end of sms_auth_nologin_pop()

/**
 * 응모하기
 */
function naver_search_event_insert(ph) {
    if( empty(ph) ) {
        alert('잘못된 접근입니다.(No Phone)');
        return false;
    }

    $.ajax({
        url : '/event/naver_search_insert',
        data : {mode:'2', ph:ph},
        type : 'post',
        dataType : 'json',
        success : function(result) {
            if( result.status == status_code['success'] ) {
                $.cookie('cki_nse_insert_yn', 'Y');

                if( appCheck() ) {
                    alert('응모가 완료 되었습니다.\n네이버에서 \"미스할인을 자주 검색해주세요.\"');
                    location.reload();
                }
                else if( $.browser.android ) {
                    alert('응모가 완료 되었습니다.\n앱을 설치해주세요.');
                    go_link('https://goo.gl/ZZmdUv');
                }
                else {
                    alert('응모가 완료 되었습니다.\n네이버에서 \"미스할인을 자주 검색해주세요.\"');
                    location.reload();
                }
            }
        }
    });
}//end of naver_search_event_insert()


$(function(){
    //이벤트 상세가 아닐때 팝업 출력함
    if( location.href.indexOf('\/event\/detail') == -1 ) {
        if( !appCheck() && nse_date_check() ) {
            if( nse_ref_check() ) {
                // naver_search_event_pop_2();
            }
            else {
                naver_search_event_pop_1();
            }
        }
    }
    //이벤트 상세 페이지일때
    else {
        $('.evt-cont img').css({'cursor':'pointer'});

        //이벤트 상세 이미지 클릭시
        $('.evt-cont img').on('click', function(){
            if( $.cookie('cki_nse_insert_yn') == 'Y' ) {
                alert('이미 참여하셨습니다.');
            }
            else {
                sms_auth_nologin_pop();
            }
        });
    }//endif;
});


