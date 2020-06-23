"use strict";

var empty_data = '<div class="push_area"><div class="cont" style="text-align: center;padding: 10px 0;">받은 알림 메시지가 없습니다.</div></div>';

function chk_view(seq , obj){

    $.ajax({
        url : '/push/chk_view',
        data : { seq : seq },
        type : 'post',
        dataType : 'json',
        success : function(result) {
            if( result.msg ) alert(result.message);
            if( result.success == true ) $(obj).find('.on').removeClass('on');
        },
        complete : function() {
        }
    });

}

function print_info_list(obj){

    var html = '';

    if(obj.length > 0) {

        $.each(obj,function(k,r){

            var view_class = '';

            if( r.view_flag == 'N') view_class = ' on ';

            html += '<div class="push_area info_list" data-seq="'+r.seq+'" data-loc_type="'+r.loc_type+'" role="button">';
            html += '   <div class="cont">';

            html += '       <div class="text '+view_class+'">'+r.noti_subject+'</div>';
            html += '           <div class="btm">';
            html += '               <span class="fl link '+view_class+'">'+r.noti_content+'</span>';
            html += '               <span class="fr date no_font">'+r.reg_date_str+'</span>';
            html += '           <div class="clear"></div>';
            html += '       </div>';

            html += '   </div>';
            html += '</div>';

        })

    }else {
        html += empty_data;
    }

    $('.box-in.push').html(html);

}

function print_product_list(obj){

    var html = "";


    if(obj.length > 0) {

        $.each(obj, function (k, r) {

            var list_image = JSON.parse(r.p_rep_image)[0];

            html += '<div class="push_area product_list" data-seq="' + r.ap_pnum + '" role="button">';
            html += '   <div class="img">';
            html += '       <img src="' + list_image + '" alt="img1" />';
            html += '   </div>';
            html += '   <div class="cont">';
            html += '       <div class="text">' + r.ap_list_comment_repl + '</div>';
            html += '       <div class="btm">';
            html += '           <span class="fl link">';
            if (empty(r.ap_list_btn_msg) == false) {
                html += r.ap_list_btn_msg;
            } else {
                html += '상품바로가기';
            }
            html += '           </span>';
            html += '           <span class="fr date no_font">' + r.reg_date_str + '</span>';
            html += '           <div class="clear"></div>';
            html += '       </div>';
            html += '   </div>';
            html += '</div>';

        });

    }else{

        html += empty_data;
    }

    $('.box-in.push').html(html);

}

$(function(){

    $('.date_set li').on('click',function(e){
        e.preventDefault();

        if($(this).hasClass('active') == true) return false;

        var type = $(this).data('type');

        $.ajax({
            url : '/push/index',
            data : { type : type },
            type : 'post',
            dataType : 'json',
            success : function(result) {

                if(result.success == true) {
                    $('.date_set li').removeClass('active');
                    $('.date_set li[data-type="' + type + '"]').addClass('active')

                    if(type == 'product') print_product_list(result.data);
                    else print_info_list(result.data);

                }

            },
            complete : function() {
            }
        });

    });

    if(isApp == 'Y'){
        var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
        $('.push').css({'min-height': min_height+'px'});
    }

    // window.history.replaceState({} , '', window.location.pathname);
    $('#container').css('background','#eff0f4');

});

$(document).on('click','.push_area.info_list',function(e){
    e.preventDefault();

    var loc_type = $(this).data('loc_type');
    var seq = $(this).data('seq');

    chk_view(seq , this);

    if(loc_type != 'none') go_link('/'+loc_type,'','',loc_type)

});

$(document).on('click','.push_area.product_list',function(e){
    e.preventDefault();

    var p_num = $(this).data('seq');
    go_product(p_num,'push');

});
