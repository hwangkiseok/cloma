/**
 * 안드로이드용 브릿지
 */

function $F(caller) {
    var f = arguments.callee.caller;
    if(caller) f = f.caller;
    var pat = /^function\s+([a-zA-Z0-9_]+)\s*\(/i;
    pat.exec(f);
    var func = new Object();
    func.name = RegExp.$1;
    return func;
}

/**
 * 앱 로그인 화면으로 이동
 */
function appRedirectLogin(backUrl) {
    if( empty(backUrl) ) {
        backUrl = "";
    }
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.redirectLogin(backUrl);
    }
}//end of appRedirectLogin()

/**
 * 기기 화면 크기(px) 추출 (width:height)
 * @returns {*}
 */
function appScreenSize() {
    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.screenSize) ) {
        return window.AndroidBrg.screenSize();
    }
}//end of appScreenSize()

/**
 * 새창 열기
 * @param url
 */
function appNewWin(url) {

    if( !empty(window.AndroidBrg) ) {

        window.AndroidBrg.newWin(url);
    }
}//end of appNewWin()

/**
 * 웹브라우저에서 열기
 * @param url
 */
function appNewWebBrowser(url) {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.newWebBrowser(url);
    }
}//end of appNewWebBrowser()

/**
 * 새창 닫기
 */
function appWinClose(openerReload) {

    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.winClose(openerReload);
    }
}//end of appWinClose()

/**
 * 뒤로가기
 */
// function appGoBack() {
//     if( !empty(window.AndroidBrg) ) {
//
//         window.AndroidBrg.goBack();
//     }
// }//end of appGoBack()

/**
 * 홈으로 이동 (새창일때 닫음)
 */
function  appGoHome() {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.goHome();
    }
}//end of appGoHome()

/**
 * 로그아웃
 */
function appLogout() {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.logout();
    }
}//end of appLogout()

/**
 * 로딩 보이기
 */
function appShowPregress() {
    try {
        window.AndroidBrg.showProgress();
    }
    catch (e) {
    }
}//end of appShowPregress()

/**
 * 로딩 감추기
 */
function appHideProgress() {
    // //if( !empty(window.AndroidBrg) ) {
    // window.AndroidBrg.hideProgress();
    // //}

    try {
        window.AndroidBrg.hideProgress();
    }
    catch (e) {
    }
}//end of appShowPregress()

/**
 * 토스트 팝업
 * @param msg : 메시지
 */
function appToast(msg) {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.toast(msg);
    }
}//end of appToast()

/**
 * 새창인지 (Y/N)
 * @returns {*}
 */
function appIsNewWin() {
    if( !empty(window.AndroidBrg) ) {
        return window.AndroidBrg.isNewWin();
    }
}//end of appIsNewWin()


/**
 * SharedPreferences 저장
 * @param key
 * @param value
 * @returns {*}
 */
function appSavePrefSetting(key, value) {
    if( empty(key) ) {
        return false;
    }

    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.savePrefSetting) ) {
        return window.AndroidBrg.savePrefSetting(key, value);
    }
}//end of appSavePrefSetting()

/**
 * SharedPreferences 불러오기
 * @param key
 * @returns {*}
 */
function appLoadPrefSetting(key) {
    if( empty(key) ) {
        return false;
    }

    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.loadPrefSetting) ) {
        return window.AndroidBrg.loadPrefSetting(key);
    }
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

    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.swipeRefreshLayoutEnabled) ) {
        return window.AndroidBrg.swipeRefreshLayoutEnabled(yn);
    }
}//end of appSwipeRefreshLayoutEnabled()

/**
 * 디아비스 OS 버전코드 (안드로이드:SDK_INT)
 * @returns {*}
 */
function appDeviceAPILevel() {
    //16 => Android 4.1, 4.1.1 / JELLY_BEAN
    //17 => Android 4.2, 4.2.2 / JELLY_BEAN_MR1
    //18 => Android 4.3 / JELLY_BEAN_MR2
    //19 => Android 4.4 / KITKAT
    //21 => Android 5.0 / LOLLIPOP
    //22 => Android 5.1 / LOLLIPOP_MR1
    //23 => Android 6.0 / Marshmallow
    //24 => Android 7.0 / Nougat
    //25 => Android 7.1 / Nougat_MR1

    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.deviceAPILevel) ) {
        return window.AndroidBrg.deviceAPILevel();
    }
}//end of appDeviceAPILevel()

/**
 * 디바이스 상태바 높이 추출
 * @returns {*}
 */
function appDeviceStatusbarHeight() {
    if( !empty(window.AndroidBrg) && !empty(window.AndroidBrg.deviceStatusbarHeight) ) {
        return window.AndroidBrg.deviceStatusbarHeight();
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
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.kakaoStoryRequestPostNote(content, msg);
    }
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
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.kakaoStoryRequestPostLink(url, content, msg);
    }
}//end of appKakaoStoryPostLink()


function appGoDetail(p_num,arrival_source,val) {
    try {
        window.AndroidBrg.go_detail(p_num,arrival_source,val);
    }
    catch(e) {
    }
}

/**
 * 클릭보드로 텍스트 복사
 * @param txt
 * @param msg
 */
function appCopyClipboard(txt, msg) {
    try {
        window.AndroidBrg.copyClipboard(txt, msg);
    }
    catch (e) {
    }
}//end appCopyClipboard;

/**
 * 앱 최상단으로 스크롤 이동
 */
function appScrollTop() {
    try {
        window.AndroidBrg.scroll_top();
    }
    catch (e) {
    }
}//end appScrollTop;

/**
 * 앱 권한 허용 요청
 * @param per_code : 권한코드 (CAMERA=카메라, STORAGE=저장소, CALL_PHONE=전화, CONTACTS=주소록) (복수개 가능, '|' 로 구분)
 */
function appAllowPermission(per_code) {
    try {
        window.AndroidBrg.allowPermission(per_code);
    }
    catch (e) {
    }
}//end appAllowPermission;

/**
 * @param method_name (string)
 * @desc 파라메터와 같은 메소드가 앱에 있는지 확인
 * @add 강제 업데이트가 아닌 버전업시 구버전/신버전 앱을 사용하는 사용자가 있어
 * 웹에서 호출하는 브릿지가 앱에서 지원하는 메소드인지 확인하는 함수
 * @returns boolean
 **/
function method_exists(method_name){

    var result = false;
    try {
        var result = window.AndroidBrg.method_exists(method_name);
    }catch (e) {

    }
    return result;
}



function appPop_close(type,time_val) {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.pop_close(type,time_val);
    }
}


/**
 * 파일 업로드 파일선택 창 열기
 * @param key
 * @param thumbnailId
 */
function appFileUploadOpen(key, thumbnailId) {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.fileUploadOpen(key, thumbnailId);
    }
}//end of appFileUploadOpen()

/**
 * 전화번호 추출
 */
function appGetPhoneNumber() {
    if( !empty(window.AndroidBrg) ) {
        return window.AndroidBrg.getPhoneNumber();
    }
}//end of appGetPhoneNumber()

function appLoadHeader() {
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.loadHeader();
    }
}

function app_load_url(url){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.load_url(url);
    }
}

//메인페이지 회원정보 reload
//개인정보 변경시 호출
function app_main_member_reload(){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.main_member_reload();
    }
}

//장바구니 구매시 주문서 호출 func
function app_cart_order(url,params){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.cart_order(url,params);
    }
}

//
function app_go_product(p_num, campaign = ""){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.go_product(p_num , campaign);
    }
}

function app_push_able(v){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.push_able(v);
    }
}
function app_shopping_push_able(v){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.shopping_push_able(v);
    }
}

function app_version_chk(){
    var v = '';
    if( !empty(window.AndroidBrg) ) {
         v = window.AndroidBrg.version_chk();
    }
    return v;
}


function app_go_sub(v){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.go_sub(v);
    }
}

function app_draw_member(){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.draw_member();
    }
}

function app_refresh_act(b){
    if( !empty(window.AndroidBrg) ) {
        window.AndroidBrg.refresh_act(b);
    }
}

function app_get_member_info(){
    var v = '';
    if( !empty(window.AndroidBrg) ) {
        v = window.AndroidBrg.get_member_info();
    }
    return v;
}
function app_get_loc_info(){
    var v = '';
    if( !empty(window.AndroidBrg) ) {
        v = window.AndroidBrg.get_loc_info();
    }
    return v;
}


