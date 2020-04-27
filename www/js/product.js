"use strict";

$(document).on('click','.product-tab li',function(){

    var set = $(this).data('set');
    var seq = $(this).data('seq');

    var set_v = '33.3333333333';
    //var set_v = '50';
    $('.product-tab li.btm-line').css('left',seq*set_v+'%');
    $('.product-tab li').removeClass('active');
    $(this).addClass('active');

    if(set == 'product_comment'){
        var move_To = parseInt($('.product_comment').offset().top);
        $(document).scrollTop(move_To);
    }else{
        $('.cont_area').hide();
        $('.'+set).show();
        var chk_t = parseInt($('.product_info .product-tab').offset().top)-20;
        var curr_t = $(document).scrollTop();

        if(chk_t < curr_t){
            $(document).scrollTop(chk_t);
        }

    }
});

function cart_complete(){
    cart_complete_pop();
}

function cart_complete_pop(){

    var chkW  = parseInt($(window).outerWidth(true)) ;

    if(chkW <= 720){
        var pop_w = (chkW/2) - (chkW*0.95)/2;
    }else{
        var pop_w = (chkW/2) - 342;
    }

    $('#cart_c_pop .cart_wrap').css('left' , pop_w+'px');
    $('#cart_c_pop').show();

}

$(function(){

    //상품설명 더보기
    $('.product_cont .more button').on('click' ,function(e){
        e.preventDefault();
        $('.product_cont').css('height' , '100%');
        $('.product_cont .more').hide();
    });


    /* header fix */
    $(window).scroll( setProductNav );
    setProductNav();

    $('.setShare').on('click',function(){
        if(isLogin == 'N' ) {
            goLogin();
        }else{

            setShare(p_num , $('.icon-share em.icon'));
            $('.icon-share em.icon').toggleClass('active');

            if( $('.icon-share em').hasClass('active') == true){
                var set_cnt  = parseInt($('.icon-share em.no_font').html()) + 1;
                $('.icon-share em.no_font').html(set_cnt);
            }

        }

    });

    $('.setWish').on('click',function(){

        if(isLogin == 'N' ) {
            goLogin();
        }else{

            setWish(p_num , $('.icon-wish em.normal-icon'));

            $('.icon-wish em.icon').toggleClass('active');
            $('.icon-wish .abs-icon').html($('.icon-wish .icon').html());

            if( $('.icon-wish em').hasClass('active') == true){
                var set_cnt  = parseInt($('.icon-wish em.no_font').html()) + 1;
                $('.icon-wish em.no_font').html(set_cnt);
            }

        }

    });

    $('.empty-space').css('height', parseInt($('.buy_area').height())+ 'px'); //하단 fixed영역 추가

    var set_w = parseInt($(window).outerWidth(true)) > 720 ? 720 : parseInt($(window).outerWidth(true));
    var tit_l = parseInt(set_w) / 2 - parseInt( $('.buy_area .tit_top').width() / 2 );
    $('.buy_area .tit_top').css('left' , tit_l+'px');

    $(window).on('resize', function() {
        var set_w = parseInt($(window).outerWidth(true)) > 720 ? 720 : parseInt($(window).outerWidth(true));
        var tit_l = parseInt(set_w) / 2 - parseInt( $('.buy_area .tit_top').width() / 2 );
        $('.buy_area .tit_top').css('left' , tit_l+'px');
    });

    //ajax form
    $('#product_cart').ajaxForm({
        type: 'post',
        dataType: 'json',
        beforeSubmit: function(){
        },
        success: function(result){
            if( !empty(result.message) && result.message_type == 'alert' ) {
                alert(result.message);
            }

            if( result.status == '000' ){
                cart_complete();
            }
        },
        error: function(){
            alert("장바구니 넣기에 실패하였습니다");
        }
    });

    $('.goCart').on('click',function(){

        if($('.option_sel_result > div.sel_option_row').length > 0) { //구매

            if ($('.opt_area').hasClass('on') == true) {

                var buy_count = 0;
                var arr = [];

                $('.sel_option_row').each(function () {

                    var obj = new Object();

                    obj.option_price = $(this).data('price');
                    obj.option_count = parseInt($(this).find('input[name="option_input"]').val());
                    obj.option_supply = $(this).data('supply');
                    obj.option_name = $(this).data('uid');
                    obj.option_plus = 'N';
                    obj.option_type = $('#product_cart input[name="sSnsformOptionType"]').val();

                    arr.push(obj);

                    buy_count += (obj.option_count);

                });

                var j = JSON.stringify(arr);

                $('#product_cart input[name="buy_count"]').val(buy_count);
                $('#product_cart input[name="item_no"]').val(p_order_code);
                $('#product_cart input[name="option_info"]').val(j);

                $('#product_cart').attr('action', '/cart/insert_proc');
                $('#product_cart').submit();

            } else {
                $('.opt_area').toggleClass('on');
                initOptionAreaH();
            }
        }else{
            $('.opt_area').toggleClass('on');
            initOptionAreaH();
        }

    });

    $('.tit_top').on('click',function(){
        $('.opt_area').toggleClass('on');
        initOptionAreaH();
    });
    $('.goBuy').on('click',function(){

        if($('.option_sel_result > div.sel_option_row').length > 0){ //구매

            //toast('주문서 페이지로 이동');
            if( $('.opt_area').hasClass('on') == true){

                var arr = [];

                $('.sel_option_row').each(function(){

                    var obj = new Object();

                    obj.option_price = $(this).data('price');
                    obj.option_count = parseInt($(this).find('input[name="option_input"]').val());
                    obj.option_supply = 0;
                    obj.option_name = $(this).data('uid');
                    obj.option_plus = 'N';
                    obj.option_seller_supply = 0;

                    arr.push(obj);

                });
                var j = JSON.stringify(arr);

                $('#product_order input[name="item_no"]').val(p_order_code);
                $('#product_order input[name="option_info"]').val(j);

                $('#product_order').attr('action','/order');
                $('#product_order').submit();

                //go_link('/order?item_no='+p_order_code+'&option_info='+j);

            }else{
                $('.opt_area').toggleClass('on');
                initOptionAreaH();
            }

        }else{ //옵션보이기
            $('.opt_area').toggleClass('on');
            initOptionAreaH();
        }

    });

    $('.opt_list li').on('click',function(){

        if( ( $(this).data('option_count') != '' || $(this).data('option_count') == 0 ) && parseInt($(this).data('option_count')) < 1 ){
            showToast('재고가 없습니다.');
            return false;
        }

        $(this).addClass('sel');

        var depth   = parseInt($(this).parent().parent().parent().data('depth'));

        $('.opt_tit.on').parent().find('.opt_list').hide();

        if(option_depth == depth){

            var v               = $(this).data('val');
            var p               = $(this).data('price');
            var supply_price    = $(this).data('option_supply');
            var name_1          = $('.option_sel[data-depth="1"] li.sel').data('name');
            var name_2          = $('.option_sel[data-depth="2"] li.sel').data('name');
            var name_3          = $('.option_sel[data-depth="3"] li.sel').data('name');

            var p_name;
            var p_v;

            if(option_depth == 3){
                p_name  = name_1 + ' | ' + name_2 + ' | ' + name_3 ;
                p_v = name_1 + '|' + name_2 + '|' + name_3 ;
            }else if(option_depth == 2){
                p_name  = name_1 + ' | ' + name_2 ;
                p_v = name_1 + '|' + name_2
            }else{
                p_name  = name_1 ;
                p_v = name_1;
            }

            var obj     = { p_name : p_name , uid : p_name , price : p , supply : supply_price };
            setRes(obj);
            calcTotPrice();

            $('.opt_list').find('.sel').removeClass('sel');
            $('.opt_list.list_on').removeClass('list_on');
            $('.opt_tit').removeClass('on');
            $('.option_sel_result').show();
            initOptionAreaH();

        }else{
            $('.option_sel[data-depth="'+(depth+1).toString()+'"] .opt_tit').click();
        }

    });

    $('.opt_tit').on('click',function(){

        var depth = $(this).parent().data('depth');
        $(this).parent().find('ul li').removeClass('sel');

        var iDepth = 0;

        if(depth > 1){

            iDepth = parseInt(depth)-1;
            if($('.option_sel[data-depth="'+iDepth+'"] li').hasClass('sel') == false){
                toast(parseInt(depth)-1+'차 옵션을 먼저 선택해주세요 !');
                return false;
            }
        }

        if($(this).hasClass('on') == true) return false;

        var b = false;

        //상위 옵션이 on 되어 있는경우 off 하고 보여지던 영역 hide
        if($('.opt_tit').hasClass('on') == true){
            $('.opt_tit.on').parent().find('.opt_list').hide();
            $('.opt_tit').removeClass('on');
            b = true;
        }

        //현재 클릭된 영역 on
        $(this).addClass('on');

        var Depth_1;
        var Depth_2;
        var prev_depth_val;

        //옵션 정보 노출

        $('.opt_list').removeClass('list_on');

        if(depth == 2) {
            Depth_1 = parseInt(depth)-1;
            prev_depth_val = $('.option_sel[data-depth="'+Depth_1+'"] li.sel').data('val');
            $(this).parent().find('.opt_list[data-seq="'+prev_depth_val+'"]').show().addClass('list_on');

        } else if(depth == 3) {
            Depth_1 = parseInt(depth)-2;
            Depth_2 = parseInt(depth)-1;
            prev_depth_val = $('.option_sel[data-depth="'+Depth_1+'"] li.sel').data('val')+'|'+$('.option_sel[data-depth="'+Depth_2+'"] li.sel').data('val');
            $(this).parent().find('.opt_list[data-seq="'+prev_depth_val+'"]').show().addClass('list_on');
        }
        else {
            $(this).parent().find('.opt_list').show().addClass('list_on');
        }


        /* 기존 영역이 사라진 경우  */
        var prev_obj_cnt = $('.option_sel[data-depth="'+iDepth+'"] .opt_list li').length;
        var target_obj  = $('.opt_area');
        var obj_cnt     = $(this).parent().find('.opt_list.list_on li').length;
        var default_h   = 43;
        var set_h       = parseInt(obj_cnt*default_h) - parseInt(prev_obj_cnt*default_h);
        // set_h = set_h+11;

        if(set_h > 320) set_h = 320 ;
        else if(set_h < -320) set_h = -320+(default_h*$(this).parent().find('.opt_list.list_on li').length+11) ;

        var target_t = parseInt(target_obj.css('top'));
        var target_h = parseInt(target_obj.css('height'));

        target_obj.css('top',(target_t-set_h)+'px');
        target_obj.css('height',target_h+set_h+'px');


        // console.log('------------------');
        // console.log(default_h,obj_cnt,prev_obj_cnt);
        // console.log(set_h,target_t,target_h);
        // console.log(target_t-set_h,target_h+set_h);

        // if(b == false){
        //     target_obj.css('top',(target_t-set_h)+'px');
        //     target_obj.css('height',target_h+set_h+'px');
        // }else{
        //     target_obj.css('top',(target_t-set_h)+'px');
        //     target_obj.css('height',target_h+set_h+'px');
        // }

    });

});

/*header fix*/
var productDetailExpended = false ; //header
var productDetailExpended2 = false ; //tab
function setProductNav() {

    var chk_t = parseInt($('.product_info .product-tab').offset().top)-10;
    var curr_t = $(document).scrollTop();

    if (curr_t > 0) {

        if (curr_t >= chk_t) {

            if(productDetailExpended2 == false){

                var set_html  = '<header>'+$('#header header').html()+'</header>';
                set_html += '<div class="product-tab" style="margin:0!important;">'+$('.product-tab').html()+'</div>';
                $('.header_fixed').html(set_html).addClass('active');

                if(empty($('#header input[name="srh_text"]').val()) == false && empty($('.header_fixed input[name="srh_text"]').val()) == true ){
                    $('.header_fixed input[name="srh_text"]').val($('#header input[name="srh_text"]').val());
                }

                productDetailExpended2 = true;
                productDetailExpended = false;

            }

        } else {

            if(productDetailExpended == false){

                var set_html = '<header>'+$('#header header').html()+'</header>';

                $('.header_fixed').html(set_html).addClass('active');

                if(empty($('#header input[name="srh_text"]').val()) == false && empty($('.header_fixed input[name="srh_text"]').val()) == true ){
                    $('.header_fixed input[name="srh_text"]').val($('#header input[name="srh_text"]').val());
                }

                productDetailExpended = true;
                productDetailExpended2 = false;
            }

        }

    }else{

        $('.header_fixed').html('').removeClass('active');
        productDetailExpended = false;
        productDetailExpended2 = false;

    }

}

/*옵션 컨트롤*/
var opt_list_h = 0;
function initOptionAreaH(){

    var tot_p_h = 0;
    var opt_list_h_in = 0;
    if(option_depth < 1){
        if($('.opt_area').hasClass('on') == false) {
            tot_p_h = 0 ;
        }else{
            tot_p_h = 20 ;
        }
    }
    else if($('.sel_option_row').length > 0 ) tot_p_h = 45 ;
    else $('.tot_result_price').html('');

    $('.opt_list').each(function(){ // 구매하기 클릭하여 옵션 영역 오픈 후 옵션을 클릭하여 옵션리스트가 나온상황에서 옵션영역을 줄이고 다시 키울 경우 세로사이즈 보정
        if($('.opt_area').hasClass('on') == false){
            if($(this).css('display') == 'block'){
                opt_list_h = parseInt($(this).height()) + 10;
            }
        }
    });

    var tit_top_h = 20;
    var default_h = 60;
    var m_h = 9*(parseInt(option_depth) -1);
    var set_h = (parseInt(option_depth)*default_h)-m_h+tot_p_h+tit_top_h+opt_list_h;



    if($('.opt_area').hasClass('on') == true) opt_list_h = 0;

    var res_h = 100;
    var res_cnt = $('.option_sel_result .sel_option_row').length > 2 ? 2 : $('.option_sel_result .sel_option_row').length ;
    var correction = (parseInt(res_cnt)-1) * 0 ;
    var f_res_h = res_h*res_cnt + correction >= 200 ? 200 : res_h*res_cnt + correction;
    if(res_cnt > 0) set_h = set_h+f_res_h-43;
    else set_h = set_h - 21;

    if($('.option_sel_result .sel_option_row').length > 2) $('.option_sel_result').scrollTop(999);

    //옵션창이 올라가 있는경우
    if($('.opt_area').hasClass('on') == false){

        $('.opt_area').css('top',0-parseInt(tot_p_h));
        $('.opt_area').css('height',parseInt(tot_p_h));

        $('.option_sel').hide();
        $('.option_sel_result').hide();

    }else{ //옵션창이 아래에 있는경우

        $('.option_sel').show();
        $('.option_sel_result').show();
        $('.opt_area').css('top','-'+(parseInt(set_h)+6)+'px').css('height',(parseInt(set_h)+6)+'px');

    }

}

$(document).on('click','.del_product',function(){

    $(this).parent().parent().addClass('del');

    var uid = $(this).data('uid');
    $('.sel_option_row[data-uid="'+uid+'"]').remove();
    calcTotPrice();
    initOptionAreaH();

});

$(document).on('click','.opt_minus',function(){

    if( $(this).parent().find('input').val() <= 1 ) return false;

    var setInputV = parseInt($(this).parent().find('input').val()) - 1;
    $(this).parent().find('input').val(setInputV);

    var price = $(this).parent().parent().data('price');
    var cnt = $(this).parent().find('input').val();
    var result_price = parseInt(price) * parseInt(cnt);

    //옵션영역 금액 변경
    $(this).parent().parent().find('.sel_option_price em').html(number_format(result_price));

    //총금액 변경
    var tot_price = number_only($('.tot_result_price em').html());
    var res_tot_price = parseInt(tot_price) - parseInt(price);

    $('.tot_result_price em').html(number_format(res_tot_price)+'원');

});

$(document).on('click','.opt_plus',function(){

    if( $(this).parent().find('input').val() >= 99 ) return false;

    var setInputV = parseInt($(this).parent().find('input').val()) + 1;
    $(this).parent().find('input').val(setInputV);

    var price = $(this).parent().parent().data('price');
    var cnt = $(this).parent().find('input').val();
    var result_price = parseInt(price) * parseInt(cnt);

    //옵션영역 금액 변경
    $(this).parent().parent().find('.sel_option_price em').html(number_format(result_price));

    //총금액 변경
    var tot_price = number_only($('.tot_result_price em').html());
    var res_tot_price = parseInt(tot_price) + parseInt(price);
    $('.tot_result_price em').html(number_format(res_tot_price)+'원');

});

function setRes(obj){

    var target = $('.sel_option_row[data-uid="'+obj.uid+'"]');

    if(target.length > 0){

        target.find('.opt_plus').click();
        return false;

    }else{


        var html  = '<div class="sel_option_row" data-price="'+obj.price+'" data-uid="'+obj.uid+'" data-supply="'+obj.supply+'">';
            //html += '   <div class="sel_option_tit">'+obj.p_name+'&nbsp;<span class="del_product" data-uid="'+obj.uid+'"><i class="fas fa-times-circle"></i></span></div>';
            html += '   <div class="sel_option_tit">'+obj.p_name+'&nbsp;<span class="del_product" data-uid="'+obj.uid+'"><i class="icon_del"></i></span></div>';
            html += '   <span class="fl cnt_ctrl">';
            html += '       <span class="opt_minus">-</span>';
            html += '       <span><input type="text" class="no_font" name="option_input" value="1" title="수량"></span>';
            html += '       <span class="opt_plus">+</span>';
            html += '   </span>';
            html += '   <span class="fr sel_option_price"><em class="no_font">'+number_format(obj.price)+'</em>원</span>';
            html += '   <div class="clear"></div>';
            html += '</div>';

        $('.option_sel_result').append(html);

    }
}

function calcTotPrice(){

    var tot_price = 0;

    $('.sel_option_row').each(function(){
        var cnt = $(this).find('input[name="option_input"]').val();
        var price = $(this).data('price');
        tot_price += parseInt(price)*parseInt(cnt);
    });

    var html = "총 금액<em class='no_font'>"+ number_format(tot_price) +"원</em>";

    $('.tot_result_price').html(html);

}