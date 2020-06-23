"use strict";

function pop_off(){
    app_refresh_act(true);
    $('html,body').css('overflow','');
    $('.srh_addr_pop').remove();
}

$(function(){

    $('.close_pop').on('click', pop_off );

    $('.go_srh').on('click', function(e){
        e.preventDefault();
        go_srh_post();
    } );

    $('.go_srh_view').on('click',function(){
        $('.srh_addr_pop .cont article').hide();
        $('.srh_addr_pop .cont article.srh_arti').show();
    });

    $('.srh_arti .cancel_btn').on('click',function(){

        srh_reset();

        $('.srh_addr_pop .cont article').hide();
        $('.srh_addr_pop .cont article.result_arti').show();

    });

    $('.result_arti .submit_btn').on('click',function(){

        if( $(this).hasClass('deactive') == false) {

            var pop_name        = $('.srh_addr_pop input[name="pop_name"]').val();
            var zipcd           = $('.srh_addr_pop input[name="zipcd"]').val();
            var pop_jibun       = $('.srh_addr_pop input[name="pop_jibun"]').val();
            var pop_road        = $('.srh_addr_pop input[name="pop_road"]').val();
            var pop_road_detail = $('.srh_addr_pop input[name="pop_road_detail"]').val();
            var pop_ph          = $('.srh_addr_pop input[name="pop_ph"]').val();

            var user_info_html = pop_name+" <em class=\"no_font\">( "+pop_ph+" )</em>";
            $('.order_block.del_type_addr .user_info').html(user_info_html);
            $('.order_block.del_type_addr .juso1').html("("+zipcd+")"+pop_road);
            $('.order_block.del_type_addr .juso2').html(pop_road_detail);

            $('input[name="refund_receiver_name"]').val(pop_name);
            $('input[name="refund_receiver_tel"]').val(pop_ph);
            $('input[name="refund_receiver_zip"]').val(zipcd);
            $('input[name="refund_receiver_addr1"]').val(pop_road);
            $('input[name="refund_receiver_addr2"]').val(pop_road_detail);

            pop_off();

        }

    });

    //엔터키로 상품검색
    $('.srh_addr_pop input[name="srh_addr"]').on('keypress', function(e){
        if( e.keyCode == 13 ) go_srh_post();
    });

    var l = ( parseInt(screen.availWidth) - parseInt($('.srh_addr_pop .cont').width()) ) / 2;
    $('html,body').css('overflow','hidden');
    $('.srh_addr_pop .cont').css('left',l+'px');

    $('.srh_addr_pop input[name="pop_name"],.srh_addr_pop input[name="pop_ph"],.srh_addr_pop input[name="pop_road_detail"]').on('keyup', chk_btn_active );

});

function chk_btn_active(){

    if(     empty($('.srh_addr_pop input[name="pop_name"]').val()) == false
        &&  empty($('.srh_addr_pop input[name="pop_ph"]').val()) == false
        &&  empty($('.srh_addr_pop input[name="pop_road"]').val()) == false
        &&  empty($('.srh_addr_pop input[name="pop_road_detail"]').val()) == false
    ){
        $('.result_arti .submit_btn').removeClass('deactive');
    }else{
        $('.result_arti .submit_btn').addClass('deactive');
    };

}

$(document).on('click','.addr_list li',function(){

    var jibun = $(this).data('jibunaddr');
    var road = $(this).data('roadaddr');
    var zipcd = $(this).data('zipno');

    $('.srh_addr_pop input[name="zipcd"]').val(zipcd);
    $('.srh_addr_pop input[name="pop_jibun"]').val(jibun);
    $('.srh_addr_pop input[name="pop_road"]').val(road);

    srh_reset();

    $('.srh_addr_pop input[name="pop_road_detail"]').show();

    $('.srh_addr_pop .cont article').hide();
    $('.srh_addr_pop .cont article.result_arti').show();

    chk_btn_active();

});

function srh_reset(){
    $('.srh_addr_pop input[name="srh_addr"]').val('');
    $('.result_warning').remove();
    $('.srh_result').find('ul,div').html('');
    $('.srh_noti').show();
    $('.srh_result').hide();
    $('.srh_addr_pop input[name="pop_road_detail"]').hide();
}

var default_page = 1;
var list_per_page = 20;

function go_srh_post(sel_page = ''){

    if(sel_page < 1) sel_page = default_page;

    var post_data = {
            'confmKey' : juso_key
        ,   'currentPage' : sel_page == '' ? default_page : sel_page
        ,   'countPerPage' : list_per_page
        ,   'keyword' : $('.srh_addr_pop input[name="srh_addr"]').val()
        ,   'resultType' : 'json'
    }

    if(empty(post_data.keyword) == true) {
        alert('검색하실 주소를 입력해주세요')
        $('.srh_addr_pop input[name="srh_addr"]').focus()
        return false;
    }
    if(checkSearchedWord(post_data.keyword) == false) return false;

    $.ajax({
        url: 'https://www.juso.go.kr/addrlink/addrLinkApi.do',
        data: post_data ,
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if(result.results.common.errorCode != '0'){
                alert(result.results.common.errorMessage);
            }else{
                print_addr(result.results , post_data.currentPage)
            }

        }
    });

}

function print_addr(obj , currentPage){

    app_refresh_act(false);
    var html = "";
    var page_html = "";

    if( obj.common.totalCount < 1 || obj.common.totalCount > 200){

        $('.srh_noti').find('.result_warning').remove();

        html = "<div class='result_warning'>";
        if(obj.common.totalCount < 1){
            html += "<span style='color: #333;display: block;'>검색하신 <b class='sig-col'>`"+$('.srh_addr_pop input[name="srh_addr"]').val()+"`</b>로 검색된 주소가 없습니다.</span>";
            html += "<span style='color: #333;display: block;margin-bottom: 10px;'>다시 검색해주세요.</span>";
        }else{
            html += "<span style='color: #333;display: block;'>검색결과가 너무 많습니다. (총 <b class='sig-col'>"+obj.common.totalCount.comma()+"</b> 건)</span>";
            html += "<span style='color: #333;display: block;margin-bottom: 10px;'>다시 검색해주세요.</span>";
        }

        html += "</div>";
        html += $('.srh_noti').html();

        $('.srh_noti').html(html);

        $('.srh_noti').show();
        $('.srh_result').hide();

    }else if( obj.common.totalCount <= 200){

        $.each(obj.juso,function(k,r){
            html += "<li role='button' data-roadaddr='"+r.roadAddr+"' data-jibunaddr='"+r.jibunAddr+"' data-zipno='"+r.zipNo+"'>";
            html += "   <p><label>도로명</label><span>"+r.roadAddrPart1+"</span></p>";
            html += "   <p><label>지번</label><span>"+r.jibunAddr+"</span></p>";
            html += "</li>";
        })

        var tot_page = Math.ceil( parseInt(obj.common.totalCount) / list_per_page ); //전체페이지

        if(tot_page == 1 ){

            page_html = '';

        }else if(tot_page <= 3 && tot_page > 0){ //3이하

            for(var i = 1 ; i <= tot_page ; i++){

                if(currentPage == i) page_html += "<a class='active'>"+i+"</a>";
                else page_html += "<a onclick='go_srh_post("+ i +")'>"+i+"</a>";
            }

        }else if(tot_page > 3){ //4이상

            var add_page = false;

            if(currentPage > 2){
                page_html += "<a onclick='go_srh_post("+ 0 +")'><<</a>";

                if(currentPage == tot_page) page_html += "<a onclick='go_srh_post("+ (parseInt(currentPage)-3) +")'><</a>";
                else page_html += "<a onclick='go_srh_post("+ (parseInt(currentPage)-2) +")'><</a>";
            }

            if(currentPage == tot_page){
                page_html += "<a onclick='go_srh_post("+ (parseInt(currentPage)-2) +")'>"+ (parseInt(currentPage)-2) +"</a>";
            }

            for(var i = parseInt(currentPage)-1 ; i <= parseInt(currentPage)+1 ; i++){

                if(parseInt(i) < 1) {
                    add_page = true;
                    continue;
                }

                if(parseInt(i) > tot_page) continue;

                if(currentPage == i) page_html += "<a class='active'>"+i+"</a>";
                else page_html += "<a onclick='go_srh_post("+ i +")'>"+i+"</a>";

            }

            if(add_page == true){
                page_html += "<a onclick='go_srh_post("+ (parseInt(currentPage)+2) +")'>"+ (parseInt(currentPage)+2) +"</a>";
            }

            if(tot_page >= parseInt(currentPage)+2  ){
                page_html += "<a onclick='go_srh_post("+ (parseInt(currentPage)+2) +")'>></a>";
                page_html += "<a onclick='go_srh_post("+ tot_page +")'>>></a>";
            }

        }

        $('.srch_addr_pagination').html(page_html);
        $('.srh_result .addr_list').html(html);

        if(obj.common.totalCount > 0) $('.srh_result').scrollTop(0);

        $('.srh_noti').hide();
        $('.srh_result').show();


    }

}

//특수문자, 특정문자열(sql예약어의 앞뒤공백포함) 제거
function checkSearchedWord(val){
    //특수문자 제거
    var expText = /[%=><]/ ;
    if(expText.test(val) == true){
        alert("특수문자를 입력 할수 없습니다.") ;
        val = val.split(expText).join("");
        return false;
    }

    //특정문자열(sql예약어의 앞뒤공백포함) 제거
    var sqlArray = new Array(
        //sql 예약어
        "OR", "SELECT", "INSERT", "DELETE", "UPDATE", "CREATE", "DROP", "EXEC",
        "UNION",  "FETCH", "DECLARE", "TRUNCATE"
    );

    var regex;
    for(var i=0; i<sqlArray.length; i++){
        regex = new RegExp( sqlArray[i] ,"gi") ;

        if (regex.test(val) ) {
            alert("\"" + sqlArray[i]+"\"와(과) 같은 특정문자로 검색할 수 없습니다.");
            val =val.replace(regex, "");
            return false;
        }
    }

    return true ;
}