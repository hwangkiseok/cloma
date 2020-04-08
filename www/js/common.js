"use strict";

var error_message_class = 'error_message';

/**
 * 객체가 있는지 없는지 체크
 * @param obj
 * @returns {boolean}
 */
function empty(obj) {
    if( typeof(obj) != 'undefined' && obj != null && obj != '' ) {
        return false;
    }
    else {
        return true;
    }
}//end of empty()

/**
 * 고유한 문자열 생성
 * @returns {string}
 */
function create_uniqid() {
    var d = new Date().getTime();
    if(window.performance && typeof window.performance.now === "function"){
        d += performance.now();; //use high-precision timer if available
    }
    var uid = 'xxxxyxxxxyxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uid;
}//end of create_uniqid()

/**
 * UUID 생성
 * @returns {string}
 */
function generateUUID(){
    var d = new Date().getTime();
    if(window.performance && typeof window.performance.now === "function"){
        d += performance.now();; //use high-precision timer if available
    }
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
}//end of generateUUID()


/**
 * 새창
 * url  => URL
 * name => 새창이름
 * w    => width
 * h    => height
 * t    => top
 * l    => left
 * s    => scrollbars
 * r    => resizable
 */
function new_win_open(url, name, w, h, t, l, s, r){
    //기본값으로 화면 가운데
    if( !t ) {
        t = (parseInt(screen.availHeight) - parseInt(h))/2;
    }
    if( !l ) {
        l = (parseInt(screen.availWidth) - parseInt(w))/2;
    }

    if( empty(name) ) {
        name = create_uniqid();
    }

    if( w && h ) {
        var win = window.open(url, name, 'width=' + w + ', height=' + h + ', top=' + t + ', left=' + l + ', scrollbars=' + s + ', resizable=' + r);
    }
    else {
        var win = window.open(url, name, 'top=' + t + ', left=' + l + ', scrollbars=' + s + ', resizable=' + r);
    }

    if (win == null) {
        alert("팝업 차단기능 혹은 팝업차단 프로그램이 동작중입니다. 팝업 차단 기능을 해제한 후 다시 시도하세요.");
        return false;
    }
    else {
        win.focus();
        return true;
    }
}//end of new_win_open()

/**
 * 폼 검증 오류 메시지 출력
 * obj  => 체크할 객체
 * msg  => 메시지
 */
function error_message(obj, msg) {
    if( !$(obj).length ) {
        return false;
    }

    var inline = $(obj).hasClass('info_inline');
    var obj_tag = $(obj).get(0).tagName;


    $(obj).removeClass('fl mgr10');

    var tag = 'p';

    //같은 라인에 출력일때
    if( inline  ) {
        tag = 'span';
        $(obj).addClass('fl mgr10');

        if( obj_tag == 'INPUT' || obj_tag == 'SELECT' || obj_tag == 'TEXTAREA' ) {
            $(obj).css({'display':'inline'});
        }
    }
    else {
        if( obj_tag == 'INPUT' || obj_tag == 'SELECT' || obj_tag == 'TEXTAREA' ) {
            $(obj).css({'display':'block'});
        }
    }

    $(obj).next('.' + error_message_class).remove();
    $(obj).after('<' + tag + ' class="' + error_message_class + '">' + msg + '</' + tag + '>');
}//end of error_message()

/**
 * 오류메시지 alert 출력
 * @param error_data
 * @returns {boolean}
 */
function error_message_alert(error_data) {
    if( empty(error_data) ) {
        return false;
    }

    var alert_msg = '';

    $.each(error_data, function(key, msg){
        alert_msg += '* ' + msg + '\n';
    });

    if( !empty(alert_msg) ) {
        alert(alert_msg);
    }
}//end of error_message_alert()

/**
 * 메시지 알림 지우기
 */
function info_message_clear(obj) {
    if( !$(obj).length ) {
        return false;
    }
    $(obj).next('.' + error_message_class).remove();
    $(obj).removeClass('fl mgr10');
    $(obj).parent('div').next('.'  + error_message_class).remove();
    $(obj).parent('div').removeClass('fl mgr10');
}//end of info_message_clear()

/**
 * 전체 메시지 알림 지우기
 */
function info_message_all_clear() {
    $('.' + error_message_class).remove();
    $('.' + error_message_class).prev().removeClass('fl mgr10');
    $('.' + error_message_class).parent().removeClass('fl mgr10');
}//end of info_message_all_clear()

//================== 날짜 관련
Number.prototype.padLeft = function(base,chr){
    var  len = (String(base || 10).length - String(this).length)+1;
    return len > 0? new Array(len).join(chr || '0')+this : this;
};

function get_ymd(date, div) {
    if( empty(div) ) {
        div = '-';
    }
    return [date.getFullYear(), (date.getMonth()+1).padLeft(), date.getDate().padLeft()].join(div);
}

function get_ymdhis(date, div1, div2) {
    if( empty(div1) ) {
        div1 = '-';
    }
    if( empty(div2) ) {
        div2 = ':';
    }
    return [date.getFullYear(), (date.getMonth()+1).padLeft(), date.getDate().padLeft()].join(div1) + ' ' + [date.getHours().padLeft(), date.getMinutes().padLeft(), date.getSeconds().padLeft()].join(div2);
}
//================== /날짜 관련


function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}//end of number_format()

/**
 * select 선택값 체크
 * obj      => 체크할 객체
 * ckVal    => 선택값
 */
function selected_check(obj, ckVal){
    if( typeof(obj) != 'undefined' && typeof(obj) != null ){
        $(obj).find('option').each(function(){
            if( $(this).val() == ckVal ){
                $(this).prop('selected', 'selected');
                return true;
            }
        });
    }//end of if()
} //end of selected_check()



var modalVerticalCenterClass = ".modal";

function centerModals($element) {

    var $modals;
    if ($element.length) {
        $modals = $element;
    } else {
        $modals = $(modalVerticalCenterClass + ':visible');
    }
    $modals.each( function(i) {
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    });
}//end of centerModals()


//=========================================== 스크롤 막기/풀기
var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e = e || window.event;
    if (e.preventDefault)
        e.preventDefault();
    e.returnValue = false;
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

function disableScroll() {
    if (window.addEventListener) // older FF
        window.addEventListener('DOMMouseScroll', preventDefault, false);
    window.onwheel = preventDefault; // modern standard
    window.onmousewheel = document.onmousewheel = preventDefault; // older browsers, IE
    window.ontouchmove  = preventDefault; // mobile
    document.onkeydown  = preventDefaultForScrollKeys;
}

function enableScroll() {
    if (window.removeEventListener)
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.onmousewheel = document.onmousewheel = null;
    window.onwheel = null;
    window.ontouchmove = null;
    document.onkeydown = null;
}
//=========================================== / 스크롤 막기/풀기

/**
 * 현재 타임스탬프
 */
function current_time() {
    var date = new Date();
    return date.setDate(date.getDate());
}//end of current_time()

/**
 * 현재 ymd
 */
function current_ymd() {
    var date = new Date();
    return [date.getFullYear(), (date.getMonth()+1).padLeft(), date.getDate().padLeft()].join('');
}//end of current_ymd()

/**
 * 쿠키 & App Pref 에 값 저장
 * @param name
 * @param value
 * @param expire_day
 * @returns {boolean}
 */
function save_cookie_pref(name, value, expire_day) {
    if( empty(name) || empty(value) ) {
        return false;
    }

    var date = new Date();

    if( !empty(expire_day) ) {
        $.cookie(name, value, {expires:expire_day});
        appSavePrefSetting(name, date.setDate(date.getDate() + expire_day));    //app pref에는 만료시간을 저장함
    }
    else {
        $.cookie(name, value);
        appSavePrefSetting(name, value);
    }
}//end of save_cookie_pref()

/**
 * 남은 시간 구하기
 * @param time : timestamp
 * @returns {string}
 */
function leftTime(time, digit) {
    var nt = new Date();
    var lt = time * 1000 - nt;

    lt = lt / 1000;

    // 남은시간 구하기
    var d = parseInt(lt / 86400);
    var h = parseInt((lt % 86400) / 3600);
    var m = parseInt((lt % 3600) / 60);
    var s = parseInt((lt % 60));

    var str = '';

    //남은시간이 없을때
    if( d <= 0 && h <= 0 && m <= 0 && s <= 0 ) {
        str = "00:00:00";
    }
    //남은시간이 있을때
    else {
        //1일 미만으로 남았을때 (시간만 나오게)
        if( d == 0 ) {
            str = zeroPad(h,10) + ":" + zeroPad(m,10) + ":" + zeroPad(s,10);
        }
        else {
            str = d + "일 " + zeroPad(h,10) + ":" + zeroPad(m,10) + ":" + zeroPad(s,10);
        }
    }

    if( !empty(digit) ) {
        if( digit == 'h' ) {
            str = zeroPad(h,10) + ":" + zeroPad(m,10) + ":" + zeroPad(s,10);
        }
        else if( digit == 'm' ) {
            str = zeroPad(m,10) + ":" + zeroPad(s,10);
        }
        else if( digit == 's' ) {
            str = zeroPad(s,10);
        }
    }

    return str;
}//end of leftTime()

/**
 * 자릿수 0으로 채우기 (예=> zeroPad(1,10) => 01, zeroPad(1,100) => 001)
 * @param nr
 * @param base
 * @returns {string}
 */
function zeroPad(nr,base){
    var  len = (String(base).length - String(nr).length)+1;
    return len > 0? new Array(len).join('0')+nr : nr;
}//end of zeroPad()

/**
 * 클립보드에 복사
 * @param trb
 */
function copy_clipboard(trb) {
    //IE용
    if ( !empty(window.clipboardData) ) {
        window.clipboardData.setData("Text", trb);
        alert('클립보드에 복사되었습니다.');
    }
    //그외
    else {
        var temp = prompt("Ctrl+C를 눌러 클립보드로 복사하세요", trb);
    }
}//end of copy_clipboard()

/**
 * 숫자만
 * @param str
 * @returns {*}
 */
function number_only(str) {
    if( empty(str) ) {
        return '';
    }
    return str.replace(/[^0-9]/gi, "");
}//end of number_only()

/**
 * location.replace
 */
function location_replace(url) {
    if( history.replaceState ){
        history.replaceState(null, document.title, url);
        history.go(0);
    }
    else{
        location.replace(url);
    }

    return false;
}//end of location_replace()


//document.ready
$(function(){
    $(document).on('click', '[href="#none"]', function(e){
        e.preventDefault();
    });

    //입력시 에러메시지 감추기
    $(document).on('keypress keydown change', 'input, select, textarea', function(){
        info_message_clear($(this));
    });

    // 숫자만 입력받음
    $(document).on('keypress keyup', 'input[type="text"][numberOnly],input[type="tel"][numberOnly]', function(e) {
        $(this).val(number_only($(this).val()));
    });

    //입력 최대글자수 체크
    $(document).on('keypress keyup', 'input[type="text"][maxlenthCheck],input[type="tel"][maxlenthCheck],input[type="number"][maxlenthCheck]', function(e) {

        var max = $(this).attr('maxlength');
        if( !max ) {
            return true;
        }

        var len = $(this).val().length;

        if( len >= max ) {
           /* *
            * @date180309 황기석
            * @desc 모바일에서 처리안됨에 따라 아래 구문 추가
            * */
            this.value = this.value.slice(0, max);
            e.preventDefault();
            return false;
        }
    });


    // - Comma 적용 ---
    String.prototype.comma=function() {
        var l_text=this.replace(/,/g,'');

        if(l_text == "0") return "0";

        var l_pattern=/^(-?\d+)(\d{3})($|\..*$)/;

        if(l_pattern.test(l_text)){
            l_text=l_text.replace(l_pattern,function(str,p1,p2,p3)
            {
                return p1.comma() + ("," + p2 + p3);
            });
        }
        return l_text;
    }
    // - Comma 적용 ---

    $(".number_style").keypress(function() {
        // 숫자만 입력
        if((event.keyCode<48 || event.keyCode>57 || event.keyCode==45) && event.keyCode!=13) event.returnValue=false;
    });
    $(".number_style").keyup(function() {
        // 천단위 콤마
        this.value = this.value.comma();
    });



});//end of document.ready


//input tag Add >> onkeyup="moveFocus(this,4,'cellphone3')"
function moveFocus(obj,no,nextObj){
    if(obj.value.length == no){
        $('#'+nextObj).focus();
    }
}

function onlyNumeric() {
    if (event.keyCode >= 48 && event.keyCode <= 57) { //숫자키만 입력
        return true;
    } else {
        event.returnValue = false;
    }
}
/*** 콤마 출력 ***/
function comma(x){
    var temp = "", co = 3;
    var x = String(uncomma(x));
    var num_len = x.length;
    while (num_len>0){
        num_len = num_len - co;
        if (num_len<0){
            co = num_len + co;
            num_len = 0;
        }
        temp = ","+x.substr(num_len,co)+temp;
    }
    return temp.substr(1);
}
/*** 콤마 미출력 ***/
function uncomma(x){
    var reg = /(,)*/g;
    x = parseInt(String(x).replace(reg,""),10);
    return (isNaN(x)) ? 0 : x;
}

/**
 * Shuffles array in place.
 * @param {Array} a items An array containing the items.
 */
function arr_shuffle(arr) {
    var j, x, i;
    for (i = arr.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = arr[i];
        arr[i] = arr[j];
        arr[j] = x;
    }
    return arr;
}

/**
 * 줄바꿈을 <br>로 변환
 * @param str
 * @returns {*}
 */
function nl2br(str){
    return str.replace(/\n/g, "<br />");
}


function getTextLength(str) {
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        if (escape(str.charAt(i)).length == 6) {
            len++;
        }
        len++;
    }
    return len;
}

function chkString(){

}



