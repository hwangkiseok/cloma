"use strict";

//=========================================================== modal
/**
 * 팝업 생성 - modalPop.createPop(title, contents, closeFun)
 * @param title - 팝업 명
 * @param contents - 팝업에 가운데 들어갈 내용(xhtml 태그형식)
 * @param closeFun - 종료할때 실행할 함수 (함수명 또는 직접 선언) default null
 *
 * 팝업에 버튼추가 - modalPop.createButton(title, cl, fun)
 * @param title - 버튼명
 * @param cl - 클래스명
 * @param fun - 버튼 클릭시 실행함수
 *
 * 기본 종료버튼 - modalPop.createCloseButton(title, cl, fun)
 * @param title - 버튼명
 * @param cl - 클래스명
 * @param fun - 버튼 클릭시 실행함수
 *
 * 팝업 열기 - modalPop.show();
 * 팝업 닫기 - modalPop.hide();
 */
var is_modal_open = false;
var modalPop = {
    modalContainer:$("<div class='modal'>"),
    modalDialog:$("<div class='modal-dialog'>"),
    modalContent:$("<div class='modal-content'>"),
    modalHeader:$("<div class='modal-header'>"),
    modalBody:$("<div class='modal-body'>"),
    modalFooter:$("<div class='modal-footer'>"),
    modalIconClose:$('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="hidden">&times;</span></button>'),
    //modalIconClose:$('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="/images/icon_close_pop.png" alt="" /></button>'),
    modalTitle:$("<h4 class='modal-title'></h4>"),
    buttonTypeDefault:"btn btn-default",
    buttonTypePrimary:"btn btn-primary",
    initPop : function(){
        //초기화
        this.modalContainer = $("<div class='modal'>");
        this.modalDialog = $("<div class='modal-dialog'>");
        this.modalContent = $("<div class='modal-content'>");
        this.modalHeader = $("<div class='modal-header'>");
        this.modalBody = $("<div class='modal-body'>");
        this.modalFooter = $("<div class='modal-footer'>");
        this.modalIconClose = $('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="hidden">&times;</span></button>');
        this.modalTitle = $("<h4 class='modal-title'></h4>");
        this.buttonTypeDefault = "btn btn-default";
        this.buttonTypePrimary = "btn btn-primary";

        //초기 설정
        this.modalContainer.html(this.modalDialog);
        this.modalDialog.html(this.modalContent);
        this.modalContent.html(this.modalHeader);
        this.modalContent.append(this.modalBody);
        this.modalContent.append(this.modalFooter);
        this.modalHeader.html(this.modalIconClose);
        this.modalHeader.append(this.modalTitle);
    },
    createPop : function(title, contents, closeFun){
        if(typeof closeFun == "undefined"){
            closeFun = null;
        }

        this.hide();
        //if( this.modalContainer.length <= 0 ) {
        this.initPop();
        //}

        this.modalBody.empty();
        this.modalFooter.empty();

        this.modalTitle.html(title);
        this.modalBody.html(contents);
        this.modalIconClose.click(closeFun);
    },
    createButton : function(title, cl, fun){
        if(typeof title == "undefined"){
            title = "";
        }

        if(typeof cl == "undefined" || cl == ""){
            cl = this.buttonTypeDefault;
        }

        if(typeof fun == "undefined"){
            fun = function(){};
        }

        this.modalFooter.append($("<button type='button'>").addClass(cl).html(title).click(fun));
    },
    createCloseButton : function(title, cl, fun){
        if(typeof title == "undefined"){
            title = "";
        }

        if(typeof cl == "undefined" || cl == ""){
            cl = this.buttonTypeDefault;
        }

        if(typeof fun == "undefined"){
            fun = function(){};
        }

        this.modalFooter.append($("<button type='button'>").addClass(cl).html(title).click(fun).attr("data-dismiss", "modal"));

    },
    show : function(options){
        app_refresh_act(false);

        if( !empty(options) ) {
            if( !empty(options.skin) ) {
                if( options.skin == 'gray' ) {
                    this.modalContainer.addClass('gray');
                }
                else if( options.skin == 'gray2' ) {
                    this.modalContainer.addClass('gray2');
                }
                else if( options.skin == 'white' ) {
                    this.modalContainer.addClass('white');
                }
                else if( options.skin == 'full_image' ) {
                    this.modalContainer.addClass('full_image');
                    this.modalBody.append(this.modalIconClose);
                }
                else if( options.skin == 'trans' ) {
                    this.modalContainer.addClass('trans');
                    this.modalBody.append(this.modalIconClose);
                }
            }
            if( !empty(options.header_class) ) {
                this.modalHeader.addClass(options.header_class);
            }
            if( !empty(options.title_class) ) {
                this.modalTitle.addClass(options.title_class);
            }
            if( !empty(options.dialog_class) ) {
                this.modalDialog.addClass(options.dialog_class);
            }
            if( !empty(options.content_class) ) {
                this.modalContent.addClass(options.content_class);
            }
            if( !empty(options.body_class) ) {
                this.modalBody.addClass(options.body_class);
            }
            if( !empty(options.hide_header) ) {
                this.modalHeader.hide();
            }
            if( !empty(options.hide_footer) ) {
                this.modalFooter.hide();
            }
            if( !empty(options.no_background) ) {
                this.modalContent.addClass('no-background');
            }
            if( !empty(options.full_screen) ) {
                this.modalDialog.addClass('full-screen');
                this.modalHeader.addClass('full-screen');
                this.modalBody.addClass('full-screen');
                this.modalFooter.addClass('full-screen');
            }
            if( !empty(options.close_abs) ) {
                this.modalDialog.append(this.modalIconClose);
                this.modalIconClose.addClass('sqr_gray').addClass('abs');
                this.modalIconClose.show();
            }
            if( !empty(options.close_text) ) {
                this.modalIconClose.text(options.close_text);
            }
        }else{
            options = {};
        }

        //if( !empty(options) && !empty(options.close_btn_class) && options.close_btn_class == 'sqr' ) {
        if( !empty(options) && !empty(options.close_btn_class) ) {
            this.modalIconClose.addClass(options.close_btn_class);
        }
        else {
            this.modalIconClose.removeClass('sqr');
        }

        if( !empty(options) && !empty(options.backdrop) ) {
            this.modalContainer.modal({backdrop: options.backdrop});
        }
        if( this.modalContainer.length <= 0 ) {
            this.initPop();
        }

        $.when(this.modalContainer.modal('show')).then(function(e){

            // 모달시 상단 마진 관련이슈로 인한 top px fix - option ojb 에 'margin_top':'109px' 형태로 처리
            if( !empty(options.margin_top)){
                $(this).find('.modal-content').css("margin-top", options.margin_top);
            }
            else if( !empty(options) && !empty(options.center) ) {
                centerModals($(this));
            }

            if( !empty(options) && !empty(options.hide_backdrop) ) {
                $('.modal-backdrop').css({opacity:0});
            }
        });
        is_modal_open = true;

    },
    hide : function(){

        app_refresh_act(true);

        //this.modalContainer.modal('hide');
        this.modalContainer.remove();
        this.modalDialog.remove();
        this.modalContent.remove();
        this.modalHeader.remove();
        $('.modal-backdrop').remove();
        $('.modal').remove();
        $('body').removeClass('modal-open');

        is_modal_open = false;
    }
};//end of modalPop

// modal창이 닫힐때 액션
$(document).on('hide.bs.modal','.modal', function (e) {

    app_refresh_act(true);

    $('.modal-backdrop').remove();
    $('.modal').remove();
    $('body').removeClass('modal-open');

    is_modal_open = false;

});



//=========================================================== / modal

function appCheck(){

    if (navigator.userAgent.search("kr.co.cloma.app") > -1 ) {
        return true;
    }
    return false;

}

/**
 * 토스트 팝업 출력
 * @param msg
 */
function showToast(msg) {
    if( appCheck() ) {
        appToast(msg)
    }
    else {
        toast(msg);
    }
}//end of showToast()

/**
 * 토스트 팝업 (웹용)
 * @param message
 * @returns {boolean}
 */
function toast(message) {
    if( $('.toast').length > 0 ) {
        return false;
    }

    var $toast = $('<div class="toast ui-loader ui-overlay-shadow ui-body-e ui-corner-all">' + message + '</div>');

    $toast.stop();

    $toast.css({
        'display':'block',
        'background':'rgba(90,90,90,0.9)',
        'color':'#fff',
        'border-radius':'20px',
        'position':'fixed',
        'padding':'7px',
        'marginLeft':'0',
        'height':'auto',
        'text-align':'center',
        'width':'280px',
        'left':($(window).width() - 294) / 2,
        'bottom':'60px',
        'font-size':'17px',
        'z-index':'9999'
    });

    var removeToast = function(){
        $(this).remove();
    };

    $toast.click(removeToast);

    $toast.appendTo('body').delay(2000);
    $toast.fadeOut(400, removeToast);
}//end of toast()

function goSearch(){
    if($('input[name="srh_text"]').val() == '' ){
        alert('검색어를 넣어주세요');
        return false;
    }
}

function view_search(){

    var h_l = $('.header-L').width()/2;
    var h_r = $('.header-R').width();
    var m_w = $(window).outerWidth(true)>720?720:$(window).outerWidth(true);
    var input_w = m_w - h_l - h_r - 5;

    $('.srh_area').css('width' , input_w+'px');
    $('.srh_area button').css('left' , input_w-40+'px');
    $('.srh_area input').css('width' , input_w-10+'px');
    $('.srh_area input').css('left' , '0');
    $('.srh_area').addClass('active');

    $('form[name="srh_form"] input[name="kwd"]').focus();

}

$(document).on('keyup','input[name="srh_text"]',function(){
    $('input[name="srh_text"]').val($(this).val());
});

//document.ready
$(function () {

    //카피라이트 토글처리
    $('.copyright_toggle').on('click',function(){
        $('.copyright .copyright_cont').toggle();
    });

    /*header fix*/
    if($('#header').length > 0 ) {

        $(window).scroll(function(){
            setHeaderNav(); //상단 headerfix
            setBtmN4depthMenu(); //하단 및 상단 4depth메뉴 on/off
        });

    }
 
    // $(document).on("selectstart", function(e){ e.preventDefault(); return false; });
    // $(document).on("dragstart", function(e){ e.preventDefault(); return false; });
    // $(document).on("contextmenu", function(e) { e.preventDefault(); return false; });

    $('a[href="#none"]').on('click', function(e){
        e.preventDefault();
        return false;
    });

    //ajax start 시 loader 로드
    $(document).ajaxStart(function(){
        if( isShowLoader ) {
            // show_loader();
        }
    });
    //ajax complete 시 loader 숨김
    $(document).ajaxComplete(function() {
        hide_loader();
        isShowLoader = true;
    });

    //ajax 오류 처리
    $(document).ajaxError(
        function(e, request) {
            if( isAjaxErrorAlert ) {
                if (request.status == 401) {
                    alert('권한이 없습니다.');
                }
                else if (request.status == 403) {
                    alert('로그인 후 이용하세요.');
                    window.location.reload();
                    //window.location.href = "/auth/login";
                }
                else if (request.status == 404) {
                    alert('404 Page Not Found!!')
                }
                else {
                    //alert('Request Error!!')
                }
            }
            isAjaxErrorAlert = true;
        }
    );

    // direct menu - scroll positioning
    if($('.direct_area').length > 0 ){

        $(window).scroll(function() {
            var limit_h         = $('body').height();
            var y = $(window).scrollTop();

            if(y > 0 ){
                if($('.direct_area').hasClass('active') == false) {
                    $('.direct_area').addClass('active')
                }
            } else{
                $('.direct_area').removeClass('active');
            }
        });

    }

    //modal
    modalPop.initPop();

    //container min-height
    var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
    $('#container').css({'min-height':min_height + 'px'});
    $(window).on('resize', function(){

    });

});//end of document.ready

/* setBtmN4depthMenu(); //하단 및 상단 4depth메뉴 on/off */
var prev_t = 0;
function setBtmN4depthMenu(){

    if($('.btm_fix_menu').length > 0 || $('.depth4nav').length > 0){

        var curr_t = $(window).scrollTop();
        var type = ''; // up / down

        if(curr_t > prev_t) {
            type = 'down';
            prev_t = curr_t;
        } else {

            if(curr_t < parseInt(prev_t) - 100){
                type = 'up';
                prev_t = curr_t;
            }

        }

        if(type == 'up'){
            if( $('.btm_fix_menu').length > 0 && $('.btm_fix_menu').hasClass('off') == true ) $('.btm_fix_menu').removeClass('off').addClass('on'); //하단메뉴 보이기
            if( $('.depth4nav').length > 0 && $('.depth4nav').hasClass('off') == false )  $('.depth4nav').addClass('off') //nav4 메뉴(sorting)가 있는경우 사라지기
        }else if(type == 'down'){
            if( $('.btm_fix_menu').length > 0 && $('.btm_fix_menu').hasClass('off') == false) $('.btm_fix_menu').addClass('off').removeClass('on'); //하단메뉴 사라지기
            if( $('.depth4nav').length > 0 && $('.depth4nav').hasClass('off') == true ) $('.depth4nav').removeClass('off') //nav4 메뉴(sorting)가 있는경우 보이기
        }

    }

}//end of setBtmN4depthMenu

/* setHeaderNav(); //상단 headerfix */
var expended = false ;
function setHeaderNav(){

    if($(document).scrollTop() > 45 ){

        if(expended == false){

            var set_html = '';

            if( $('.depth1').length > 0 ) set_html  += '<nav class="depth1">'+$('#header .depth1').html()+'</nav>';
            if( $('.depth2nav').length > 0 ) set_html += '<nav class="depth2 depth2nav">'+$('#header .depth2nav').html()+'</nav>';
            if( $('.depth3nav').length > 0 ) set_html += '<nav class="depth3 depth3nav">'+$('#header .depth3nav').html()+'</nav>';
            if( $('.depth4nav').length > 0 )  set_html += '<nav class="depth4 depth4nav box">'+$('#header .depth4nav').html()+'</nav>';

            if(empty(set_html) == false){

                $('.header_fixed').html(set_html);
                var depth4nav_t = ( parseInt($('.header_fixed nav').length) - 1 ) * 45;

                $('.header_fixed .depth4nav').css('top',depth4nav_t+'px');
                $('.header_fixed').addClass('active');

                //검색어 이전
                if(empty($('#header input[name="srh_text"]').val()) == false && empty($('.header_fixed input[name="srh_text"]').val()) == true ){
                    $('.header_fixed input[name="srh_text"]').val($('#header input[name="srh_text"]').val());
                };

                if($('.depth1').length > 0){
                    //var depth1 = new Swiper ('.depth1', { slidesPerView: 4 });
                }

                if ($('.depth2').length > 0){
                    var depth2_cnt = $('.header_fixed.active nav.depth2 a').length > 4 ? 4 : $('.header_fixed.active nav.depth2 a').length;
                    var depth2nav = new Swiper ('.depth2nav', { slidesPerView: depth2_cnt });
                }

                if ($('.depth3').length > 0){
                    var depth3_cnt = $('.header_fixed.active nav.depth3 a').length > 6 ? 6 : $('.header_fixed.active nav.depth3 a').length;
                    var depth3nav2 = new Swiper ('.depth3nav', { slidesPerView: depth3_cnt });
                }

            }

            expended = true;

        }

    }else{

        if(typeof productDetailExpended == 'undefined'){ //상품상세에서 제어안되도록 변수 확인

            $('.header_fixed').html('');
            $('.header_fixed').removeClass('active');
            expended = false;

        }

    }

}//end of setHeaderNav

function setShare(p_num , obj){

    if(isLogin != 'Y'){ goLogin(); return false; }

    var proc_url    = '';

    if(obj.hasClass('active')) proc_url    = '/share/delete_proc'; // 찜해제
    else proc_url    = '/share/upsert_proc'; // 찜하기

    if(proc_url){

        isShowLoader = false;

        $.ajax({
            url : proc_url,
            data : {p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.status == status_code['success'] ) {

                }else if( result.status == status_code['overlap'] ) {
                    alert('이미 공유하셨습니다.');
                }else if( result.status == status_code['error'] ) {
                    alert('잠시 후 다시 시도해주세요.');
                }
            }
        });
    }
    ``
}


function setWish(p_num , obj){

    if(isLogin != 'Y'){ goLogin(); return false; }

    var proc_url    = '';

    if(obj.hasClass('active')) proc_url    = '/wish/delete_proc'; // 찜해제
    else proc_url    = '/wish/upsert_proc'; // 찜하기

    if(proc_url){

        isShowLoader = false;

        $.ajax({
            url : proc_url,
            data : {p_num:p_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.status == status_code['success'] ) {
                    // if(obj.hasClass('active')) obj.removeClass('active').html('찜하기');
                    // else obj.addClass('active').html('찜해제');
                }else if( result.status == status_code['overlap'] ) {
                    alert('이미 찜하셨습니다.');
                }else if( result.status == status_code['error'] ) {
                    alert('잠시 후 다시 시도해주세요.');
                }
            }
        });
    }

}

/*
 * @date 200306
 * @author 황기석
 * @params
 *  url = 이동url
 *  newWin = 액티비티 어픈여부 == 웹인경우 newBrowser와 같음
 *  newBrowser = 브라우저 오픈여부 == 웹인경우 newWin와 같음
 *  sub = 앱사용시 sub페이지 호출인경우 ( used classname )
 *  moveType = 페이지 이동방법 H = href / R = replace
 * */
function go_link(url , newWin='N' , newBrowser='N' , sub = '' , moveType = 'H'){

    if(isApp == 'Y'){

        if(empty(sub) == false){

            app_go_sub(sub);

        }else{

            if(newBrowser == 'Y') {
                appNewWebBrowser(url);
            }else if(newWin == 'Y') {
                appNewWin(url);
            }else{

                var tmp_url = url.split('?');

                if(tmp_url.length > 1) url = url + '&en_ak='+en_ak; //query string  있는 경우
                else url = url + '?en_ak='+en_ak;

                if(moveType == 'R'){
                    location_replace(url);
                }else{
                    location.href = url;
                }

            }

        }
    }else{

        if(newBrowser == 'Y' || newWin == 'Y'){
            window.open(url,'_blank')
        }else{
            if(moveType == 'R'){
                location.replace(url);
            }else{
                location.href = url;
            }

        }

    }

}


/*
 * 사이트메뉴 팝업
 * @param type
 *  l = left menu
 *  r = right menu
 * */
function side_close(type){
    if(type == 'l') $('#lnb_area').removeClass('active');
    else $('#rnb_area').removeClass('active');
    toggle_bg(false);
}
/*
 * 사이트메뉴 답기
 * @param type
 *  l = left menu
 *  r = right menu
 * */
function side_show(type){
    if(type == 'l') $('#lnb_area').addClass('active');
    else $('#rnb_area').addClass('active');
    toggle_bg(true,true);
}
/*
 * 팝업 시 html,body scrolling stop
 * @param stoped (boolean)
 * */
function toggle_bg(stoped,bg_color=false){
    if(stoped == true) {
        $('html,body').css('overflow','hidden');
        if(bg_color == true) $('html').append('<div class="bg_layer"></div>');
    }
    else {
        $('html,body').css('overflow','auto');
        if($('html').find('.bg_layer').length > 0) $('html').find('.bg_layer').remove();
    }
}

function go_product(p_num , c = '' ){

    if(isApp == 'Y'){
        app_go_product(p_num , c);
    }else{
        go_link('/product/detail/'+p_num);
    }

}

//스크롤 최상단
function set_scrolltop(){
    $(window).scrollTop(0);
}

function go_back(){
    history.back(-1);
}

function go_home(){
    if(isApp == 'Y'){
        appGoHome();
    }else{
        go_link('/');
    }
}
//페이지 로드 후 loader 숨김
$(window).load(function(e){
    hide_loader();
});


