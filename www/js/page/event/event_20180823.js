/**
 * 깜짝이벤트
 **/

/**
 * 휴대폰인증 (비회원) 팝업
 */
function sms_auth_nologin_pop() {
    var container = $('<div></div>');

    $(container).load('/event/load/?page=ph_certifi&cb=callback_ev_27_fnc&e_num='+e_num);

    modalPop.createPop('휴대폰인증', container);
    modalPop.show({'hide_header':true,'hide_footer':true,'body_class':'no_padding','backdrop':'static'});
}//end of sms_auth_nologin_pop()

/**
 * 응모하기
 */
function callback_ev_27_fnc() {
    go_link('/event_gift');
}//end of naver_search_event_insert()


$(function(){
    $('.evt-cont img').css({'cursor':'pointer'});
    //이벤트 상세 이미지 클릭시
    $('.evt-cont img').on('click', function(){

        if(isApp == 'Y'){
            sms_auth_nologin_pop();
        }else{

            //if( $.browser.android ) {
                if( confirm('미스할인 앱에서만 응모가 가능합니다.\n미스할인 앱을 설치하시겠습니까?') ) {
                    location.href = 'https://goo.gl/VQemCd';
                    return false;
                }
            // } else {
            //     alert('미스할인 앱에서만 응모가 가능합니다.');
            // }

        }

    });

});


