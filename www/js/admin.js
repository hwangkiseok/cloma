"use strict";

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
var modalPop = {
    modalContainer:$("<div class='modal fade'>"),
    modalDialog:$("<div class='modal-dialog'>"),
    modalContent:$("<div class='modal-content'>"),
    modalHeader:$("<div class='modal-header'>"),
    modalBody:$("<div class='modal-body'>"),
    modalFooter:$("<div class='modal-footer'>"),
    modalIconClose:$('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'),
    modalTitle:$("<h4 class='modal-title'></h4>"),
    buttonTypeDefault:"btn btn-default",
    buttonTypePrimary:"btn btn-primary",
    initPop : function(){
        this.modalContainer = $("<div class='modal fade'>"),
            this.modalDialog = $("<div class='modal-dialog'>"),
            this.modalContent = $("<div class='modal-content'>"),
            this.modalHeader = $("<div class='modal-header'>"),
            this.modalBody = $("<div class='modal-body'>"),
            this.modalFooter = $("<div class='modal-footer'>"),
            this.modalIconClose = $('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'),
            this.modalTitle = $("<h4 class='modal-title'></h4>"),
            this.buttonTypeDefault = "btn btn-default",
            this.buttonTypePrimary = "btn btn-primary",

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
        this.initPop();

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
            fun = null;
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
            fun = null;
        }

        this.modalFooter.append($("<button type='button'>").addClass(cl).html(title).click(fun).attr("data-dismiss", "modal"));
    },
    show : function(options){
        if( !empty(options) ) {
            if( !empty(options.dialog_class) ) {
                this.modalDialog.addClass(options.dialog_class);
            }
            if( !empty(options.hide_footer) ) {
                this.modalFooter.hide();
            }
        }

        if( this.modalContainer.length <= 0 ) {
            this.initPop();
        }

        if( !empty(options) && !empty(options.backdrop) ) {
            this.modalContainer.modal({backdrop:options.backdrop});
        }
        else {
            this.modalContainer.modal('show');
        }
    },
    hide : function(){
        // this.modalContainer.modal('hide');
        // $('.modal-backdrop').remove();
        this.modalContainer.remove();
        this.modalDialog.remove();
        this.modalContent.remove();
        this.modalHeader.remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({'padding-right':'0'});
    }
};//end of modalPop

var prograssBar = {
    isShow:false,
    isCount:0,
    modalContainer:$("<div class='modal fade in' >"),
    modalDialog:$("<div class='modal-dialog'>"),
    modalContent:$("<div class='modal-content'>"),
    modalHeader:$("<div class='modal-header'>"),
    modalBody:$("<div class='modal-body' style='padding:0;'>"),
    modalFooter:$("<div class='modal-footer'>"),
    modalTitle:$("<h4 class='modal-title'></h4>"),
    container:$("<div class='custom_progress_bar' role='progressbar' aria-valuenow='45' aria-valuemin='0' aria-valuemax='100' style='width:100%;'></div>"),
    initPop : function(){
        this.modalContainer.html(this.modalDialog);
        this.modalDialog.html(this.modalContent);
        this.modalContent.html(this.modalBody);

        this.modalBody.empty();

        this.modalBody.html(this.container);
    },
    show : function(){
        this.isShow = true;
        this.modalContainer.modal({backdrop:false});

        //this.isCount++;
        //if(!this.isShow){
        //    this.isShow = true;
        //    this.modalContainer.modal({backdrop:false});
        //}
    },
    hide : function(){
        this.isShow = false;
        this.modalContainer.modal('hide');

        //this.isCount--;
        //if(this.isCount == 0){
        //    this.isShow = false;
        //    this.modalContainer.modal('hide');
        //}
    }
};//end of prograssBar

/**
 * 로딩바
 */
var loadingBar = {
    isShow:false,
    container:$('<div class="loadingbar"></div>'),
    show : function(target){
        this.isShow = true;

        var tar = $(document);
        if( !empty(target) ) {
           tar = $(target);
        }

        //var l = parseInt((parseInt(tar.width()) - parseInt(this.container.width())) / 2);
        //var t = parseInt((parseInt(tar.height()) - parseInt(this.container.height())) / 2);
        //if( !t ) {
        //    t = 50;
        //}
        //var t = 50;

        //console.log(l, t);
        //this.container.css({left:l + 'px', top:t + 'px'});
        tar.prepend(this.container);
    },
    hide : function(){
        this.isShow = false;
        this.container.remove();
    }
};

/**
 * 로딩화면
 */
var loadingScreen = {
    isShow:false,
    container:$('<div class="loading"><div class="spinner"></div></div>'),
    show : function(){
        this.isShow = true;
        $('body').prepend(this.container);
    },
    hide : function(){
        this.isShow = false;
        this.container.remove();
    }
};//end of loadingScreen

/**
 * 상품 상세보기 새창 열기
 * @param pnum
 */
function product_detail_win(pnum) {
    new_win_open('/product/detail/?pop=1&p_num=' + pnum, 'pdt_win', '800', '800');
    return true;
}//end of product_detail_win()


/**
 * 날짜 설정
 * @param term  : null=오늘, -3d=3일전, -1m=1달전
 */
function set_date_term(term) {
    var now = new Date();

    var d = now;
    var date1 = get_ymd(d);
    var date2 = get_ymd(d);

    if( !empty(term) ) {

        if(term == '-1'){

            d = new Date(now.getFullYear(), now.getMonth(), now.getDate()-parseInt(1));

            date1 = get_ymd(d);
            date2 = get_ymd(d);

        }else if(term == '-2'){

            d = new Date(now.getFullYear(), now.getMonth(), now.getDate() - parseInt(2));

            date1 = get_ymd(d);
            date2 = get_ymd(d);

        }else if(term == '-3') {

            d = new Date(now.getFullYear(), now.getMonth(), now.getDate() - parseInt(3));

            date1 = get_ymd(d);
            date2 = get_ymd(d);

        }else{

            var t = term.slice(0, -1);
            var unit = term.slice(-1);

            if( unit == 'd' ) {
                d = new Date(now.getFullYear(), now.getMonth(), now.getDate()+parseInt(t));
            }
            else if( unit == 'm' ) {
                d = new Date(now.getFullYear(), now.getMonth()+parseInt(t), now.getDate());
            }

            date1 = get_ymd(d);

        }
    }

    $('input[name="date1"]').val(date1);
    $('input[name="date2"]').val(date2);
}//end of set_date_term()

/**
 * 날짜 없애기
 */
function clear_date_term() {
    $('input[name="date1"]').val('');
    $('input[name="date2"]').val('');
}//end of clear_date_term()


///**
// * form submit
// * @param str   : 설정할 폼 값 (예: a=111&b=222&c=333)
// * @param form  : 폼이름 (예: #search_form)
// */
//function form_submit(str, form) {
//    if( empty(form) ) {
//        form = '#search_form';
//    }
//
//    var arr1 = str.split('&');
//
//    for(var i in arr1) {
//        if( !empty(arr1[i]) ) {
//            var arr2 = arr1[i].split('=');
//            var name = arr2[0];
//            var val = arr2[1];
//
//            $(form + ' [name="' + name + '"]').val(val);
//        }
//    }//end of for()
//
//    $(form).submit();
//}//end of form_submit()


//document.ready
$(function(){
    //pace
    $(document).ajaxStart(function(){
        //Pace.restart();
    });

    //modal
    modalPop.initPop();
    //prograssBar.initPop();

    //큰 화면일때 사이드바 토글 버튼
    $('.navbar-toggle2').on('click', function(){
        $('.sidebar').toggle();

        if( $('.sidebar:visible').length < 1 ) {
            $('#page-wrapper').css({'margin':'0'});
            sidebar_yn = false;
        }
        else {
            $('#page-wrapper').css({'margin-left':'250px'});
            sidebar_yn = true;
        }
    });


    //ajax 오류 처리
    $(document).ajaxError(
        function(e, request) {
            if( isAjaxErrorAlert ) {
                if (request.status == 401) {
                    alert('권한이 없습니다.');
                }
                else if (request.status == 403) {
                    //alert('로그인 후 이용하세요.');
                    //window.location.reload();
                    //window.location.href = "/auth/login";
                    location.href = "/auth/login";
                }
                else if (request.status == 404) {
                    alert('404 Page Not Found!!')
                }
                else {
                    alert('Request Error!!')
                }
            }
            isAjaxErrorAlert = true;
        }
    );
});//end of document.ready()