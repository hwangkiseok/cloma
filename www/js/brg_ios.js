/**
 * 앱 로그인 화면으로 이동
 */

var ios_call = window.webkit.messageHandlers.callbackHandler.postMessage;

function $F(caller) {
    var f = arguments.callee.caller;
    if(caller) f = f.caller;
    var pat = /^function\s+([a-zA-Z0-9_]+)\s*\(/i;
    pat.exec(f);
    var func = new Object();
    func.name = RegExp.$1;
    return func;
}


function appTest11(){

    var name = $F().name.toLowerCase();   // 함수 자신의 이름 가져오기
    var result = name.replace('app', '');
    alert(result);      // 또는 alert($F().name);

}

/**
 * 앱 로그인 화면으로 이동
 */
function appRedirectLogin(backUrl) {
    if( empty(backUrl) ) {
        backUrl = "";
    }

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기

    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:backUrl});


}//end of appRedirectLogin()

/**
 * 앱 로그인 처리로 이동
 */
function appRedirectSignup(backUrl) {
    if( empty(backUrl) ) {
        backUrl = "";
    }
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:backUrl});
}//end of appRedirectSignup()

/**
 * 기기 화면 크기(px) 추출 (width:height)
 * @returns {*}
 */
function appScreenSize() {//리턴있음 사용안함
    if( !empty(window.AndroidApp) && !empty(window.AndroidApp.screenSize) ) {
        return window.AndroidApp.screenSize();
    }

}//end of appScreenSize()

/**
 * 기기 화면 density 추출
 * @returns {*}
 */
function appScreenDensity() {//리턴있음 사용안함
    if( !empty(window.AndroidApp) && !empty(window.AndroidApp.screenDensity) ) {
        return window.AndroidApp.screenDensity();
    }
}//end of appScreenDensity()

/**
 * 새창 열기
 * @param url
 */
function appNewWin(url) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:url});

}//end of appNewWin()

/**
 * 웹브라우저에서 열기
 * @param url
 */
function appNewWebBrowser(url) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:url});
}//end of appNewWebBrowser()

/**
 * 새창 닫기
 */
function appWinClose(openerReload) {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:openerReload});
}//end of appWinClose()

/**
 * 뒤로가기
 */
function appGoBack() {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appGoBack()

/**
 * 홈으로 이동 (새창일때 닫음)
 */
function  appGoHome() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appGoHome()


function  appGo_na_home() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appGoHome()

/**
 * 웹뷰 새로고침
 */
function appReload() {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});


}//end of appReload()

/**
 * 메인 웹뷰 새로고침
 */
function appOpenerReload() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});

}//end of appOpenerReload()

/**
 * 웹 캐시 삭제
 */
function webClearCache() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of

/**
 * 앱 캐시 삭제
 */
function appClearCache() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appClearCache()

/**
 * 웹 캐시 삭제
 */
function appWebClearCache() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appWebClearCache()

/**
 * 로그아웃
 */
function appLogout() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appLogout()

/**
 * 앱 해제(탈퇴)
 */
function appUnlink() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appUnlink()

/**
 * 로딩 보이기
 */
function appShowPregress() {
    var name = $F().name.toLowerCase().replace('app', '');   //웹로직의 오타로 직접 넣음..
    window.webkit.messageHandlers.callbackHandler.postMessage({function: "showprogress"});
}//end of appShowPregress()

/**
 * 로딩 감추기
 */
function appHideProgress() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}//end of appShowPregress()

/**
 * 토스트 팝업
 * @param type : S=short, L=long
 * @param msg : 메시지
 */
function appToast(msg, type) {

        type = 'S';
        var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
        window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:type,string2:msg});

}//end of appToast()

/**
 * 파일 업로드 파일선택 창 열기
 * @param key
 * @param thumbnailId
 */
function appFileUploadOpen(key, thumbnailId) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:key,string2:thumbnailId});


}//end of appFileUploadOpen()


/**
 * 새창인지 (Y/N)
 * @returns {*}
 */
function appIsNewWin() { //리턴있음
    /*
    if( !empty(window.AndroidApp) ) {
        return window.AndroidApp.isNewWin();
        //return "N";
    }
    */
    return "Y";

}//end of appIsNewWin()

// /**
//  * SharedPreferences 저장
//  * @param key
//  * @param value
//  * @returns {*}
//  */
// function appSavePrefSetting(key, value) {//리턴있음 사용안함
//     if( empty(key) ) {
//         return false;
//     }
//
//     if( !empty(window.AndroidApp) && !empty(window.AndroidApp.savePrefSetting) ) {
//         return window.AndroidApp.savePrefSetting(key, value);
//     }
// }//end of appSavePrefSetting()

/**
 * 앱 저장소에 값 저장하기
 * @param key
 * @param value
 * @returns {boolean}
 */
function appSavePrefSetting(key, value) {
    if( empty(key) ) {
        return false;
    }

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    return window.webkit.messageHandlers.callbackHandler.postMessage({function:name, string1:key, value:value});
}//end of appSavePrefSetting()

// /**
//  * SharedPreferences 불러오기
//  * @param key
//  * @returns {*}
//  */
// function appLoadPrefSetting(key) {//리턴있음 사용안함
//     if( empty(key) ) {
//         return false;
//     }
//
//     if( !empty(window.AndroidApp) && !empty(window.AndroidApp.loadPrefSetting) ) {
//         return window.AndroidApp.loadPrefSetting(key);
//     }
// }//end of appLoadPrefSetting()

/**
 * 앱 저장소에 저장된 값 불러오기
 * @param key
 * @returns {boolean}
 */
function appLoadPrefSetting(key) {
    if( empty(key) ) {
        return false;
    }

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    return window.webkit.messageHandlers.callbackHandler.postMessage({function:name, string1:key});
}//end of appLoadPrefSetting()

/**
 * SwipeRefreshLayout 사용함/안함
 * @param yn
 * @returns {*}
 */
function appSwipeRefreshLayoutEnabled(yn) {
    if( empty(yn) ) {
        yn = "Y";
    }

    if( !empty(window.AndroidApp) && !empty(window.AndroidApp.swipeRefreshLayoutEnabled) ) {
        return window.AndroidApp.swipeRefreshLayoutEnabled(yn);
    }
}//end of appSwipeRefreshLayoutEnabled()

/**
 * 디아비스 OS 버전코드 (안드로이드:SDK_INT)
 * @returns {*}
 */
function appDeviceAPILevel() {//리턴있음 사용안함
    //16 => Android 4.1, 4.1.1 / JELLY_BEAN
    //17 => Android 4.2, 4.2.2 / JELLY_BEAN_MR1
    //18 => Android 4.3 / JELLY_BEAN_MR2
    //19 => Android 4.4 / KITKAT
    //21 => Android 5.0 / LOLLIPOP
    //22 => Android 5.1 / LOLLIPOP_MR1
    //23 => Android 6.0 / Marshmallow
    //24 => Android 7.0 / Nougat
    //25 => Android 7.1 / Nougat_MR1

    if( !empty(window.AndroidApp) && !empty(window.AndroidApp.deviceAPILevel) ) {
        return window.AndroidApp.deviceAPILevel();
    }
}//end of appDeviceAPILevel()

/**
 * 디바이스 상태바 높이 추출
 * @returns {*}
 */
function appDeviceStatusbarHeight() {//리턴있음 사용안함
    if( !empty(window.AndroidApp) && !empty(window.AndroidApp.deviceStatusbarHeight) ) {
        return window.AndroidApp.deviceStatusbarHeight();
    }
}//end of appDeviceStatusbarHeight()

/**
 * 카카오스토리 글 포스팅
 * @param url
 * @param content
 * @param msg
 * @returns {boolean}
 */
//function appKakaoStoryPostNote(content, msg) {
function appkakaoStoryRequestPostNote(content, msg) {
    if( !appCheck() ) {
        return false;
    }
    if( empty(content) ) {
        return false;
    }
    if( empty(msg) ) {
        msg = '';
    }

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:content,string2:msg});

}//end of appKakaoStoryPostNote()

/**
 * 카카오스토리 링크 포스팅
 * @param url
 * @param content
 * @param msg
 * @returns {boolean}
 */
//function appKakaoStoryPostLink(url, content, msg) {
function appkakaoStoryRequestPostLink(url, content, msg) {
    if( !appCheck() ) {
        return false;
    }

    if( empty(url) ) {
        url = site_domain_http;
    }
    if( empty(content) ) {
        content = site_name_kr + ' - 공동구매 1위 ,최저가 쇼핑몰, 백화점,마트, 홈쇼핑상품, 패션의류/잡화, 뷰티';
    }
    if( empty(msg) ) {
        msg = '';
    }

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:url,string2:msg});


}//end of appKakaoStoryPostLink()



function appGo_detail(p_num,arrival_source,val) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:p_num,string2:arrival_source,string3:val});
}

function appLogin_pop_opne() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}

function appLogin_pop_opne_1() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});
}



function appApp_header() {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});

}


function appN_app_link(val) {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:val});

}

function appPop_close(type,time_val) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:type,string2:time_val});

}

function appToast_msg(val) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:val});

}

function appBack_newWin(val) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:val});

}

function appreferer_info(val,type) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:type});

}

function result_referer_info(val,type) {
    alert(val+"|"+type);
}

function appn_referer(val,type) {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:type});

}

function result_referer(val,type) {
    alert(val+"|"+type);
}


function appGoCart() {

    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name});

}

function appViewSize(w, h) {

    //alert($(window).width() + " × " + $(window).height());
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    var w_width = (typeof(w) != 'undefined' && !empty(w)) ? w : $(window).width();
    var w_height =  (typeof(h) != 'undefined' && !empty(h)) ? h : $(window).height();

    w_width = w_width.toString();
    w_height = w_height.toString();

    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:w_width, string2:w_height});


}

function appFire_call(val) {

}

/**
 * 리뷰 좋아요 on/off 연동
 * @param re_num : 리뷰번호
 * @param state : 1=좋아요, 2=좋아요해제
 */
function appReviewLike(re_num, state) {
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:re_num.toString(), string2:state.toString()});
}//end appReviewLike;

/**
 * 클립보드로 문자열 복사 (IOS앱 70이상부터 사용가능)
 * @param txt
 * @param msg
 */
// function appCopyClipboard(txt, msg) {
//     var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
//     window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:txt.toString(), string2:msg.toString()});
// }//end appCopyClipboard;
function appCopyClipboard(txt, msg) {
    try {
        window.AndroidApp.copyClipboard(txt, msg);
    }
    catch (e) {
    }
}//end appCopyClipboard;

/**
 * 앱 최상단으로 스크롤 이동
 * @param txt
 * @param msg
 */
function appScrollTop() {
    window.webkit.messageHandlers.callbackHandler.postMessage({function:'scroll_top'});
}//end appScrollTop;

/**
 * 앱 권한 허용 요청
 * @param per_code : 권한코드 (CAMERA=카메라, STORAGE=저장소, CALL_PHONE=전화, CONTACTS=주소록) (복수개 가능, '|' 로 구분)
 */
function appAllowPermission(per_code) {
    try {
        window.AndroidApp.allowPermission(per_code);
    }
    catch (e) {
    }
}//end appAllowPermission;

/**
 * @desc Adbrix share event
 * @ex shareProductEvent(String 채널, String 상품ID, String  상품이름)
 **/
function appShareProductEvent(channel, p_num, p_name){
    window.AndroidApp.shareProductEvent(channel, p_num, p_name);
}
/**
 * @desc Adbrix search kwd
 * @ex searchProductEvent(String 키워드)
 **/
function appSearchProductEvent(str) {
    window.AndroidApp.searchProductEvent(str);
}
/**
 * @desc Adbrix Purchase complete
 * @ex (String 주문번호, String 상품이름, int 구매갯수, double 총가격)
 **/
function appPurchaseEvent(order_num,product_name,product_cnt,pay){
    window.AndroidApp.purchaseEvent(order_num, product_name, product_cnt, pay);
}

/**
 * @param string
 * @desc 파라메터와 같은 메소드가 앱에 있는지 확인
 * @add 강제 업데이트가 아닌 버전업시 구버전/신버전 앱을 사용하는 사용자가 있어
 * 웹에서 호출하는 브릿지가 앱에서 지원하는 메소드인지 확인하는 함수
 * @returns boolean
 **/
function method_exists(method_name){
    var name = $F().name.toLowerCase().replace('app', '');   // 함수 자신의 이름 가져오기
    return window.webkit.messageHandlers.callbackHandler.postMessage({function: name, string1:method_name});
}