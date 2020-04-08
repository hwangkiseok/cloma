/**
 * 추석 이벤트 (~20171027)
 **/
var now = new Date();
var ymd = number_only(get_ymd(now));
var close_ymd = 20171027;

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

/**event_20171027
 * 추석 이벤트 안내 팝업
 * @returns {boolean}
 */
function chuseok_event_pop_1() {

    if( !empty($.cookie('cki_nse_pop_2_yn')) && $.cookie('cki_nse_pop_2_yn') == 'Y' ) {
        return false;
    }
    if( appCheck() || !nse_date_check() ) {
        return false;
    }
    var container = $('<div></div>');

    /* 이벤트 랜딩페이지 변경 -------------------------------------------*/
    //var tar_url = 'https://goo.gl/VMAUMU';
    var tar_url = 'https://goo.gl/VycHxn';
    /*-------------------------------------------*/
    var html = '';
    html += '<div>';
    html += '   <a href="#none" onclick="window.open(\'' + tar_url + '\');"><img src="' + img_http + '/images/event/chuseok_event_pop.jpg?asd" style="width:100%;" alt="" /></a>';
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
        $.cookie('cki_nse_pop_2_yn', 'Y', { expires:9999 });
        modalPop.hide();

    });
    //닫기
    $('.btn_nsep_close_2').on('click', function(){
        $.cookie('cki_nse_pop_2_yn', 'Y', { expires:1 });
        modalPop.hide();
    });
    $('.modal .close').on('click', function(){
        $.cookie('cki_nse_pop_2_yn', 'Y', { expires:1 });
        modalPop.hide();
    });
}//end of naver_search_event_pop_1()

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
        url : '/event/event_chuseok_insert',
        data : {mode:'2', ph:ph},
        type : 'post',
        dataType : 'json',
        success : function(result) {
            if( result.status == status_code['success'] ) {
                $.cookie('cki_nse_insert_yn', 'Y');

                if( appCheck() ) {
                    alert('응모가 완료 되었습니다.');
                    location.reload();
                }
                else if( $.browser.android ) {
                    alert('응모가 완료 되었습니다.\n앱을 설치해주세요.');
                    go_link('https://goo.gl/Lvf9gh');
                }
                else {
                    alert('응모가 완료 되었습니다.');
                    location.reload();
                }
            }
        }
    });
}//end of naver_search_event_insert()


$(function(){
    //이벤트 상세가 아닐때 팝업 출력함
    if( location.href.indexOf('\/event\/detail') == -1 ) {
        chuseok_event_pop_1();
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


