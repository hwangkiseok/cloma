"use strict";

/**
 * 상품 옵션 관련 JS
 * : p_num, p_sale_state, p_stock_state
 */
var pdt_option_type = '';   //1depth, 2depth, 3depth, nooption
var pdt_option_data = '';   //옵션data
var pdt_other_data = '';    //추가옵션data
var op_selected_wrap_max = 220;
var modal_yn = false;       //상품옵션을 모달로 출력할지
if( !$.browser.android ) {
    modal_yn = true;
}

/**
 * 상품 옵션 정보 가져오기
 */
function get_product_option() {
    $.ajax({
        url : '/product/option_ajax',
        data : {p_num:p_num},
        type : 'post',
        dataType : 'json',
        success : function (result) {
            if( !empty(result) ) {
                var info = result.info;
                var options = result.option;
                var others = result.other;

                pdt_option_type = info.option_type;
                pdt_option_data = options;
                pdt_other_data = others;

                // if( pdt_option_type == "nooption" ) {
                //     return false;
                // }

                $('[name="op_type"]').val(pdt_option_type);
                $('[name="shopping_pay_use"]').val(info.shopping_pay_use);
                $('[name="shopping_pay"]').val(info.shopping_pay);
                $('[name="shopping_pay_free"]').val(info.shopping_pay_free);

                var html = '';
                html += '<div class="pdt_option_wrap">';
                html += '   <div class="t_btn"><a href="#none" class="btn" onclick="toggle_pdt_option_wrap();"><img src="' + img_http + '/images/new/icon_arw_w_up_red.png" alt="" /></a></div>';
                html += '   <div class="item_wrap">';

                //옵션이 없을때 (수량선택만)
                if( pdt_option_type == 'nooption' || empty(options) ) {
                    if( parseInt(info.stock) < 1 ) {
                        $('.order_fix').addClass('full');
                        $('.order_fix').html('<a href="#none" class="btnPur btn btn-xxlarge" onclick="alert(\'품절된 상품입니다.\');">품절</a>');
                        // alert('품절된 상품입니다.');
                        return false;
                    }

                    //판매가 설정(앱혜택가 체크)
                    var price = info.price;
                    if( appCheck() && !empty(info.price_app_yn) && info.price_app_yn == "Y" && !empty(info.price_app) ) {
                        price = info.price_app;
                    }
                    //판매가 설정(2차 판매가 체크)
                    if( appCheck() == false && !empty(info.price_second_yn) && info.price_second_yn == "Y" && !empty(info.price_second) ) {
                        price = info.price_second;
                    }



                    html += '       <div class="op_selected_wrap">';
                    html += '           <div class="op_selected_item" data-uid="0" data-cnt="1" data-stock="' + info.stock + '" data-price="' + price + '" data-del="N" data-other="N">';
                    html += '              <input type="hidden" name="op_uid[]" value="0" />';
                    html += '              <div class="op_name">' + info.name + '</div>';
                    html += '              <div class="op_cnt">';
                    html += '                  <a href="#none" class="btn btn_op_cnt_minus ico" onclick="update_op_cnt(0, \'-\', 1);">#</a>';
                    html += '                  <input type="number" name="op_cnt[]" id="op_cnt_0" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" />';
                    html += '                  <a href="#none" class="btn btn_op_cnt_plus ico" onclick="update_op_cnt(0, \'+\', 1);">#</a>';
                    html += '              </div>';
                    html += '           </div>';
                    html += '           </div>';
                    html += '       <div class="op_selected_total">총 주문금액 <em>' + number_format(price)+ '원</em></div>';
                }
                //옵션이 있을때
                else {
                    html += '       <ul class="op_item">';
                    //1차옵션
                    if( !empty(options[1]) ) {
                        html += get_product_option_html(1, options[1], options[2]);
                    }
                    //2차옵션
                    if( !empty(options[2]) ) {
                        html += get_product_option_html(2, options[2], '');
                    }
                    //3차옵션
                    if( !empty(options[3]) ) {
                        html += get_product_option_html(3, options[3], '');
                    }
                    html += '       </ul>';
                    html += '       <div class="op_selected_wrap" style="display:none;"></div>';
                    html += '       <div class="op_selected_total">총 주문금액 <em>0원</em></div>';
                    html += '   </div>';
                    html += '</div>';
                }//end of if()

                $('.order_fix').append(html);

                //하단 앱설치 유도 배너 위치 조정
                $('.app_setup_fix').css({'bottom':'100px'});

                op_selected_wrap_max = parseInt($('.op_selected_wrap').css('max-height'));
            }
        }//end of success()
    });//end of ajax()
}//end of get_product_option()

/**
 * 상품옵션 출력 (모달로 출력할지, select로 출력할지)
 * @param depth
 * @param options
 * @param child_options
 * @returns {string}
 */
function get_product_option_html(depth, options, child_options) {
    var html = '';
    html += '<li class="op_item-item">';

    //1단계일때
    if( depth == 1 ) {
        //모달 출력일때
        if( modal_yn ) {
            var cnt = options.length;
            html += '<button type="button" name="pdt_option_' + depth + '_btn" class="op_select" data-depth="' + depth + '" onclick="toggle_op_content(\'' + depth + '\');">옵션(' + depth + ')을 선택하세요.</button>';
            html += '<input type="hidden" name="pdt_option_' + depth + '" value="" />';
            html += '<div class="pdt_option_' + depth + '_content op_select_content" style="display:none;">';
            html += '   <div class="op_select_cont_head"><em class="title">옵션 선택 (<span class="op_' + depth + '_total_cnt">' + number_format(cnt) + '</span>)</em><a href="#none" class="close" onclick="toggle_op_content(\'' + depth + '\');">닫기</a></div>';
            html += '   <ul class="cont_wrap">';

            $.each(options, function(index, item){
                var item_name = item.name;
                var item_soldout = '';

                //2차 옵션이 없을때 재고 체크
                if( empty(child_options) ) {
                    if( parseInt(item.stock) < 1 ) {
                        item_name += '(품절)';
                        item_soldout = ' soldout';
                    }
                }
                html += '   <li class="op_select_item' + item_soldout + '" data-name="' + item_name + '" data-uid="' + item.uid + '" data-depth="' + depth + '" data-stock="' + item.stock + '" data-price="' + item.price + '" onclick="op_select_click(this);">' + item_name + '</li>';
            });
            html += '   </ul>';
            html += '</div>';
        }
        //select 출력일때
        else {
            html += '   <select name="pdt_option_' + depth + '" class="op_select" data-depth="' + depth + '">';
            html += '       <option value="">* 선택 *</option>';

            $.each(options, function(index, item){
                var item_name = item.name;
                var item_disabled = '';

                //2차 옵션이 없을때 재고 체크
                if( empty(child_options) ) {
                    if( parseInt(item.stock) < 1 ) {
                        item_name += '(품절)';
                        item_disabled = 'class="soldout" disabled';
                    }
                }
                html += '   <option value="' + item.uid + '" data-stock="' + item.stock + '" data-price="' + item.price + '" ' + item_disabled + '>' + item_name + '</option>';
            });

            html += '   </select>';
        }
    }
    //2, 3단계일때
    else {
        //모달 출력일때
        if( modal_yn ) {
            var cnt = options.length;
            html += '<button type="button" name="pdt_option_' + depth + '_btn" class="op_select" data-depth="' + depth + '" onclick="toggle_op_content(\'' + depth + '\');">옵션(' + depth + ')을 선택하세요.</button>';
            html += '<input type="hidden" name="pdt_option_' + depth + '" value="" />';
            html += '<div class="pdt_option_' + depth + '_content op_select_content" style="display:none;">';
            html += '   <div class="op_select_cont_head"><em class="title">옵션 선택 (<span class="op_' + depth + '_total_cnt">' + number_format(cnt) + '</span>)</em><a href="#none" class="close" onclick="toggle_op_content(\'' + depth + '\');">닫기</a></div>';
            html += '   <ul class="cont_wrap"></ul>';
            html += '</div>';
        }
        //select 출력일때
        else {
            html += '<select name="pdt_option_' + depth + '" class="op_select" data-depth="' + depth + '">';
            html += '   <option value="">* ' + depth + '차옵션을 선택하세요.</option>';
            html += '</select>';
        }
    }//endif;

    html += '</li>';

    return html;
}//end of get_product_option_html()

/**
 * 옵션선택 모달창 띄우기
 * @param title
 * @param cont
 */
function show_option_modal(title, cont) {
    var container = $(cont);

    modalPop.createPop(title, container);
    modalPop.show({
        center:true,
        hide_footer:true,
        close_btn_class:'txt',
        close_text:'닫기'
    });
}//end of show_option_modal()

/**
 * 옵션 내용 보이기 (모달일때)
 */
function toggle_op_content(depth) {
    var obj = $('.pdt_option_' + depth + '_content');
    var prev_depth = depth - 1;
    if( prev_depth > 0 ) {
        if( !$('[name="pdt_option_' + prev_depth + '"]').val() ) {
            showToast('옵션(' + prev_depth + ')을 선택하세요.');
            $('[name="pdt_option_' + prev_depth + '_btn"]').focus();
            return false;
        }
    }

    //modal버전
    if( is_modal_open ) {
        modalPop.hide();
    }
    else {
        var title = $(obj).find('.op_select_cont_head').text().replace("닫기", "");
        var cont = '<div class="cont_wrap">' + $(obj).find('.cont_wrap').html() + '</div>';
        show_option_modal(title, cont);

        if( $(window).scrollTop() == 0 ) {
            $(window).scrollTop(1);
        }
    }

    // //하단 fixed 버전
    // if( $(obj).length > 0 ) {
    //     //감추기
    //     if( $(obj).css('display') != 'none' ) {
    //         $(obj).hide();
    //
    //         body_scroll_release();
    //         appSwipeRefreshLayoutEnabled('Y');
    //     }
    //     //보이기
    //     else {
    //         $('.op_select_content').hide();
    //         $(obj).show();
    //
    //         body_scroll_lock(true);
    //         appSwipeRefreshLayoutEnabled('N');
    //
    //         if( $(window).scrollTop() == 0 ) {
    //             $(window).scrollTop(1);
    //         }
    //     }
    // }

    //화면 크기에 따른 height 조절
    var w_h = $(window).height() - $('.top_fix_banner').height() - $('.top_menu').height();
    var limit_h = parseInt(w_h * 0.7);
    var h = $(obj).find('.cont_wrap').height();

    if( h > limit_h ) {
        $(obj).find('.cont_wrap').css({'height':limit_h + 'px'});
        $(obj).find('.cont_wrap').addClass('scroll');
    }
}//end of toggle_op_content()

/**
 * 추가옵션 내용 보이기 (모달일때)
 */
function toggle_op_other_content() {
    var obj = $('.pdt_option_other_content');
    if( $(obj).length > 0 ) {
        // if( $(obj).css('display') != 'none' ) {
        //     $(obj).hide();
        // }
        // else {
        //     $('.op_select_content').hide();
        //     $(obj).show();
        // }

        if( is_modal_open ) {
            modalPop.hide();
        }
        else {
            var title = $(obj).find('.op_select_cont_head').text().replace("닫기", "");
            var cont = '<div class="cont_wrap">' + $(obj).find('.cont_wrap').html() + '</div>';
            show_option_modal(title, cont);

            if( $(window).scrollTop() == 0 ) {
                $(window).scrollTop(1);
            }
        }
    }

    //화면 크기에 따른 height 조절
    var w_h = $(window).height() - $('.top_fix_banner').height() - $('.top_menu').height();
    var limit_h = parseInt(w_h * 0.7);
    var h = $(obj).find('.cont_wrap').height();

    if( h > limit_h ) {
        $(obj).find('.cont_wrap').css({'height':limit_h + 'px'});
        $(obj).find('.cont_wrap').addClass('scroll');
    }
}//end of toggle_op_other_content()

/**
 * 옵션영역 toggle
 */
function toggle_pdt_option_wrap() {
    //감추기
    if( $('.item_wrap').hasClass('on') ) {
        $('.pdt_option_wrap').animate({'top':'-40px'}, 'fast');
        $('.item_wrap').removeClass('on');
        $('.op_select_content').hide();     //옵션선택 감추기

        //찜하기&구매하기 활성
        $('.order_fix .wrap.cart').hide();
        $('.order_fix .wrap.wish').show();

        body_scroll_release();
    }
    //보이기
    else {
        $('.item_wrap').addClass('on');
        $('.pdt_option_wrap').animate({'top':(parseInt($('.pdt_option_wrap').css('top')) - $('.item_wrap').height()) + 'px'}, 'fast');

        //장바구니&구매하기 활성
        $('.order_fix .wrap.cart').show();
        $('.order_fix .wrap.wish').hide();
    }
}//end of toggle_pdt_option_wrap()

/**
 * 2차옵션 값 설정
 */
function set_pdt_option_2() {
    var uid = $('[name="pdt_option_1"]').val();

    if( empty(uid) ) {
        $('[name="pdt_option_1"]').focus();
        return false;
    }

    reset_pdt_option(2);

    if( modal_yn ) {
        $('[name="pdt_option_2"]').val('');
    }
    else {
        $('[name="pdt_option_2"] option:first').text("* 선택 *");
    }

    var cnt = pdt_option_data[2][uid].length;
    $.each(pdt_option_data[2][uid], function(index, item){
        var item_name = item.name;
        var item_disabled = '';
        var item_soldout = '';

        //3차 옵션이 없을때 재고 체크
        if( empty(pdt_option_data[3]) ) {
            if( parseInt(item.stock) < 1 ) {
                item_name += '(품절)';
                item_disabled = 'class="soldout" disabled';
                item_soldout = ' soldout';
            }
        }
        if( modal_yn ) {
            $('.pdt_option_2_content .cont_wrap').append('<li class="op_select_item' + item_soldout + '" data-name="' + item_name + '" data-depth="2" data-uid="' + item.uid + '" data-stock="' + item.stock + '" data-price="' + item.price + '" onclick="op_select_click(this);">' + item_name + '</li>');
        }
        else {
            $('[name="pdt_option_2"]').append('<option value="' + item.uid  + '" data-stock="' + item.stock + '" data-price="' + item.price + '" ' + item_disabled + '>' + item_name + '</option>');
        }
    });

    if( modal_yn ) {
        $('.op_2_total_cnt').text(number_format(cnt));
    }
    else {
        $('[name="pdt_option_2"] option:first').prop('selected', true);
    }

    reset_pdt_option(3);
}//end of set_pdt_option_2()

/**
 * 3차옵션 값 설정
 */
function set_pdt_option_3() {
    var uid = $('[name="pdt_option_2"]').val();

    reset_pdt_option(3);

    if( modal_yn ) {
        $('[name="pdt_option_3"]').val('');
    }
    else {
        $('[name="pdt_option_3"] option:first').text("* 선택 *");
    }

    var cnt = pdt_option_data[3][uid].length;

    $.each(pdt_option_data[3][uid], function(index, item){
        var item_name = item.name;
        var item_disabled = '';
        var item_soldout = '';
        if( parseInt(item.stock) < 1 ) {
            item_name += '(품절)';
            item_disabled = 'class="soldout" disabled';
            item_soldout = ' soldout';
        }
        if( modal_yn ) {
            $('.pdt_option_3_content .cont_wrap').append('<li class="op_select_item' + item_soldout + '" data-name="' + item_name + '" data-depth="3" data-uid="' + item.uid + '" data-stock="' + item.stock + '" data-price="' + item.price + '" onclick="op_select_click(this);">' + item_name + '</li>');
        }
        else {
            $('[name="pdt_option_3"]').append('<option value="' + item.uid  + '" data-stock="' + item.stock + '" data-price="' + item.price + '" ' + item_disabled + '>' + item_name + '</option>');
        }
    });

    if( modal_yn ) {
        $('.op_3_total_cnt').text(number_format(cnt));
    }
    else {
        $('[name="pdt_option_3"] option:first').prop('selected', true);
    }
}//end of set_pdt_option_3()

/**
 * 최종옵션 선택시
 */
function selected_option_add() {
    var sel_obj = null;
    if( pdt_option_type == "1depth" ) {
        if( modal_yn ) {
            sel_obj = $('.op_select_item[data-uid="' + $('[name="pdt_option_1"]').val() + '"]');
        }
        else {
            sel_obj = $('[name="pdt_option_1"] option:selected');
        }
    }
    else if( pdt_option_type == "2depth" ) {
        if( modal_yn ) {
            sel_obj = $('.op_select_item[data-uid="' + $('[name="pdt_option_2"]').val() + '"]');
        }
        else {
            sel_obj = $('[name="pdt_option_2"] option:selected');
        }
    }
    else if( pdt_option_type == "3depth" ) {
        if( modal_yn ) {
            sel_obj = $('.op_select_item[data-uid="' + $('[name="pdt_option_3"]').val() + '"]');
        }
        else {
            sel_obj = $('[name="pdt_option_3"] option:selected');
        }
    }

    var name = '';
    if( $('[name="pdt_option_1"]').length > 0 ) {
        if( modal_yn ) {
            name += $('.op_select_item[data-uid="' + $('[name="pdt_option_1"]').val() + '"]').data('name');
        }
        else {
            name += $('[name="pdt_option_1"] option:selected').text();
        }
    }
    if( $('[name="pdt_option_2"]').length > 0 ) {
        if( modal_yn ) {
            name += ' | ' + $('.op_select_item[data-uid="' + $('[name="pdt_option_2"]').val() + '"]').data('name');
        }
        else {
            name += ' | ' + $('[name="pdt_option_2"] option:selected').text();
        }
    }
    if( $('[name="pdt_option_3"]').length > 0 ) {
        if( modal_yn ) {
            name += ' | ' + $('.op_select_item[data-uid="' + $('[name="pdt_option_3"]').val() + '"]').data('name');
        }
        else {
            name += ' | ' + $('[name="pdt_option_3"] option:selected').text();
        }
    }

    if( modal_yn ) {
        var uid = $(sel_obj).data('uid');
    }
    else {
        var uid = $(sel_obj).val();
    }
    var stock = $(sel_obj).data('stock');

    //판매가 설정(앱혜택가 체크)
    var price = $(sel_obj).data('price');
    if( appCheck() && !empty($(sel_obj).data('price_app_yn')) && $(sel_obj).data('price_app_yn') == "Y" && !empty($(sel_obj).data('price_app')) ) {
        price = $(sel_obj).data('price_app');
    }

    //이미 선택된 옵션이면
    if( $('.op_selected_item[data-uid="' + uid + '"]').length > 0 ) {
        update_op_cnt(uid, '+', 1);
        return false;
    }

    selected_option_add_html(uid, name, price, stock, 'N');
    update_total_price();

    $('.op_selected_wrap').show();
    // if( $('.op_selected_item').outerHeight(true) >= op_selected_wrap_max ) {
    //     return false;
    // }
    set_pdt_option_wrap_position();
    $('.op_selected_wrap').scrollTop($('.op_selected_wrap').prop('scrollHeight') - 80);
    set_op_data();

    //추가옵션
    if( pdt_option_type != 'nooption' && !empty(pdt_other_data) ) {
        if( $('.op_item-item.other').length >= 1 ) {
            $('.op_item-item.other').show();
            $('.op_item-item.other').eq(0).focus();
            return false;
        }

        var cnt = pdt_other_data.length;

        var html = '';
        html += '<li class="op_item-item other">';

        if( modal_yn ) {
            html += '   <button type="button" name="pdt_option_other_btn" class="op_select" data-depth="1" onclick="toggle_op_other_content()">추가 옵션을 선택하세요.</button>';
            html += '   <div class="pdt_option_other_content op_select_content" style="display:none;">';
            html += '       <div class="op_select_cont_head"><em class="title">추가 옵션 선택 (<span class="op_other_total_cnt">' + number_format(cnt) + '</span>)</em><a href="#none" class="close" onclick="toggle_op_content(\'other\');">닫기</a></div>';
            html += '       <ul class="cont_wrap">';
            $.each(pdt_other_data, function(index, item){
                var item_soldout = '';
                var item_name = item.name;
                if( parseInt(item.stock) < 1 ) {
                    item_soldout = ' soldout';
                    item_name += '(품절)';
                }
                html += '       <li class="op_select_item' + item_soldout + '" data-name="' + item_name + '" data-uid="' + item.uid + '" data-stock="' + item.stock + '" data-price="' + item.price + '" onclick="other_option_add(this);">' + item_name + '</option>';
            });
            html += '       </ul>';
        }
        else {
            html += '   <select name="pdt_option_other" class="op_other_select" data-depth="1">';
            html += '       <option value="" data-uid="">* 추가옵션 선택 *</option>';
            $.each(pdt_other_data, function(index, item){
                html += '   <option value="' + item.uid + '" data-uid="' + item.uid + '" data-stock="' + item.stock + '" data-price="' + item.price + '">' + item.name + '</option>';
            });
            html += '   </select>';
        }

        html += '</li>';
        $('.op_item').append(html);
    }

    set_pdt_option_wrap_position();
    set_op_data();
    $('.op_selected_wrap').scrollTop($('.op_selected_wrap').prop('scrollHeight') - 80);
}//end of selected_option_add()

/**
 * 선택된 옵션에 추가하기 HTML
 * @param uid
 * @param price
 */
function selected_option_add_html(uid, name, price, stock, other) {
    if( empty(other) ) {
        other = 'N';
    }

    var html = '';
    html += '<div class="op_selected_item" data-uid="' + uid + '" data-cnt="1" data-stock="' + stock + '" data-price="' + price + '" data-del="N" data-other="' + other + '">';
    html += '   <input type="hidden" name="op_uid[]" value="' + uid + '" />';
    html += '   <div class="op_name">' + name + '</div>';
    html += '   <div class="op_cnt">';
    html += '       <a href="#none" class="btn btn_op_cnt_minus ico" onclick="update_op_cnt(\'' + uid + '\', \'-\', 1);">#</a>';
    html += '       <input type="text" name="op_cnt[]" id="op_cnt_' + uid + '" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" />';
    html += '       <a href="#none" class="btn btn_op_cnt_plus ico" onclick="update_op_cnt(\'' + uid + '\', \'+\', 1);">#</a>';
    html += '   </div>';
    html += '   <div class="op_price">' + number_format(price) + '원</div>';
    html += '   <a hare="#none" class="btn_op_del btn ico" onclick="delete_op_selected(\'' + uid + '\')">#</a>';
    html += '</div>';
    $('.op_selected_wrap').append(html);
}//end of selected_option_add_html()

/**
 * 추가옵션 선택시
 */
function other_option_add(obj) {
    if( !modal_yn ) {
        obj = $('[name="pdt_option_other"] option:selected');

        if( !$(obj).val() ) {
            return false;'' +
            ''
        }

        var uid = $(obj).val();
        var name = $(obj).text();
    }
    else {
        if( !$(obj).data('uid') ) {
            return false;
        }
        if( $(obj).hasClass('soldout') || parseInt($(obj).data('stock')) < 1 ) {
            alert('품절된 옵션입니다.');
            return false;
        }

        var uid = $(obj).data('uid');
        var name = $(obj).data('name');
    }

    var price = $(obj).data('price');
    var stock = $(obj).data('stock');

    $('[name="pdt_option_other_btn"]').addClass('on');
    $('[name="pdt_option_other_btn"]').text(name);

    //이미 선택된 옵션이면
    if( $('.op_selected_item[data-uid="' + uid + '"]').length > 0 ) {
        update_op_cnt(uid, '+', 1);
        return false;
    }

    selected_option_add_html(uid, name, price, stock, 'Y');
    set_pdt_option_wrap_position();
    update_total_price();
    set_op_data();
    $('.op_selected_wrap').scrollTop($('.op_selected_wrap').prop('scrollHeight') - 40);
    toggle_op_other_content();
}//end of other_option_add()

/**
 * 선택옵션 수량 업데이트
 */
function update_op_cnt(uid, type, cnt) {
    //if( empty(uid) ) {
    //    return false;
    //}
    if( !$('.op_selected_item[data-uid="' + uid + '"]').length ) {
        return false;
    }

    if( empty(cnt) ) {
        cnt = 1;
    }

    var obj = $('.op_selected_item[data-uid="' + uid + '"]');
    var stock = $(obj).data('stock');

    //더하기
    if( type == '+' ) {
        var set_cnt = parseInt($('#op_cnt_' + uid).val()) + cnt;
        if( set_cnt > stock ) {
            alert('선택하신 옵션의 재고량이 부족합니다.');
            cnt = stock;
        }
        else {
            cnt = set_cnt;
        }
    }
    //빼기
    else if( type == '-' ) {
        cnt = parseInt($('#op_cnt_' + uid).val()) - cnt;
        if( cnt < 1 ) {
            cnt = 1;
        }
    }
    //다시 계산
    else {
        var set_cnt = parseInt($('#op_cnt_' + uid).val());
        if( set_cnt > stock ) {
            alert('선택하신 옵션의 재고량이 부족합니다.');
            cnt = stock;
        }
        else {
            cnt = set_cnt;
        }
    }

    var price = $(obj).data('price');
    $(obj).data('cnt', cnt);
    $('#op_cnt_' + uid).val(cnt);
    $('.op_cnt_' + uid).val(cnt);
    $(obj).find('.op_price').text(number_format(cnt * price) + '원');

    update_total_price();
    set_op_data();
}//end of update_selected_option()

/**
 * 총 주문금액 계산
 */
function update_total_price() {
    //총 주문금액 계산
    var total_price = 0;
    if( $('.op_selected_item').length > 0 ) {
        $.each($('.op_selected_item'), function(index, item){
            if( $(this).data('del') != 'Y' ) {
                total_price += parseInt($(this).data('cnt')) * parseInt($(this).data('price'));
            }
        });
    }

    $('.op_selected_total em').text(number_format(total_price) + '원');
}//end of update_total_price()


/**
 * 옵션 select reset
 */
function reset_pdt_option(depth) {
    if( empty(depth) ) {
        if( $('[name="pdt_option_1"]').length > 0 ) {
            if( modal_yn ) {
                $('[name="pdt_option_1"]').val('');
                $('[name="pdt_option_1_btn"]').removeClass('on');
                $('[name="pdt_option_1_btn"]').text('옵션(1)을 선택하세요.');
                $('.pdt_option_1_content .cont_wrap').html('');
            }
            else {
                $('[name="pdt_option_1"] option:first').prop('selected', true);
            }
        }
        if( $('[name="pdt_option_2"]').length > 0 ) {
            if( modal_yn ) {
                $('[name="pdt_option_2"]').val('');
                $('[name="pdt_option_2_btn"]').removeClass('on');
                $('[name="pdt_option_2_btn"]').text('옵션(2)을 선택하세요.');
                $('.pdt_option_2_content .cont_wrap').html('');
            }
            else {
                $('[name="pdt_option_2"] option:first').prop('selected', true);
            }
        }
        if( $('[name="pdt_option_3"]').length > 0 ) {
            if( modal_yn ) {
                $('[name="pdt_option_3"]').val('');
                $('[name="pdt_option_3_btn"]').removeClass('on');
                $('[name="pdt_option_3_btn"]').text('옵션(3)을 선택하세요.');
                $('.pdt_option_3_content .cont_wrap').html('');
            }
            else {
                $('[name="pdt_option_3"] option:first').prop('selected', true);
            }
        }
    }
    else {
        if( modal_yn ) {
            var text = '';
            if( depth != 1 ) {
                text = '옵션(' + depth + ')을 선택하세요.';
            }
            $('[name="pdt_option_' + depth + '"]').val('');
            $('[name="pdt_option_' + depth + '_btn"]').removeClass('on');
            $('[name="pdt_option_' + depth + '_btn"]').text(text);
            $('.pdt_option_' + depth + '_content .cont_wrap').html('');
        }
        else {
            $('[name="pdt_option_' + depth +'"] option').remove();
            $('[name="pdt_option_' + depth +'"]').append('<option value="">* ' + (parseInt(depth) - 1)+ '차옵션을 선택하세요.</option>');
        }
    }

    set_op_data();
}//end of reset_pdt_option()

/**
 * 선택한 옵션 삭제
 */
function delete_op_selected(uid) {
    if( empty(uid) ) {
        return false;
    }

    var obj = $('.op_selected_item[data-uid="' + uid + '"]');

    $(obj).data('del', 'Y');
    // $(obj).fadeOut('fast', function(){
    $(obj).remove();

    //모두 삭제되면 추가옵션도 삭제함
    if( !$('.op_selected_item[data-del="N"][data-other="N"]').length ) {
        $('.op_selected_item[data-other="Y"]').remove();
        $('.op_item-item.other').remove();
    }

    if( !$('.op_selected_item[data-del="N"]').length ) {
        $('.op_selected_wrap').hide();
    }

    set_pdt_option_wrap_position();
    update_op_cnt(uid);
    update_total_price();
    set_op_data();
    // });
}//end of delete_op_selected()

// /**
//  * 옵션창 위치 설정
//  */
// function set_pdt_option_wrap_position() {
//     var item_height = 0;
//     $.each($('.op_selected_item'), function(index, item){
//         if( $(this).data('del') != "Y" ) {
//             item_height += $(this).outerHeight(true);
//         }
//     });
//
//     if( item_height >= op_selected_wrap_max ) {
//         item_height = op_selected_wrap_max;
//     }
//
//     var top = $('.pdt_option_wrap .op_item').outerHeight(true);
//     top += $('.pdt_option_wrap .t_btn').outerHeight(true);
//     top += item_height;
//     top += $('.pdt_option_wrap .op_selected_total').outerHeight(true);
//
//     $('.pdt_option_wrap').animate({'top':'-' + top + 'px'}, 'fast');
//     // $('.pdt_option_wrap').css({'top':'-' + top + 'px'});
// }//end of set_pdt_option_wrap_position()
/**
 * 옵션창 위치 설정
 */
function set_pdt_option_wrap_position() {
    var top = $('.pdt_option_wrap .op_item').outerHeight(true);
    top += $('.pdt_option_wrap .t_btn').outerHeight(true);
    top += $('.pdt_option_wrap .op_selected_wrap').outerHeight(true);
    top += $('.pdt_option_wrap .op_selected_total').outerHeight(true);

    $('.pdt_option_wrap').animate({'top':'-' + top + 'px'}, 'fast');
}//end of set_pdt_option_wrap_position()

/**
 * 주문서로 넘길 옵션/수량 데이터 설정하기
 */
function set_op_data() {
    var op_data = '';   //형식 => uid:cnt|uid:cnt| ...
    var op_other = '';   //형식 => uid:cnt|uid:cnt| ...

    //초기화
    $('[name="op_data"]').val('');
    $('[name="op_other"]').val('');

    //op_data
    $.each($('.op_selected_item[data-del="N"][data-other="N"] input[name="op_uid[]"]'), function(index, item){
        var uid = $(this).val();
        op_data += uid + ':' + $('#op_cnt_' + uid).val() + '|';
    });
    if( !empty(op_data) ) {
        op_data = op_data.substr(0, op_data.length - 1);
    }

    //op_other
    $.each($('.op_selected_item[data-del="N"][data-other="Y"] input[name="op_uid[]"]'), function(index, item){
        var uid = $(this).val();
        op_other += uid + ':' + $('#op_cnt_' + uid).val() + '|';
    });
    if( !empty(op_data) ) {
        op_other = op_other.substr(0, op_other.length - 1);
    }

    $('[name="op_data"]').val(op_data);
    $('[name="op_other"]').val(op_other);
}//end of set_op_data()

/**
 * 옵션 클릭(선택)시 (modal버전일때 사용)
 * @returns {boolean}
 */
function op_select_click(obj) {
    var depth = $(obj).data('depth');
    var uid = $(obj).data('uid');
    var name = $(obj).data('name');

    if( $(obj).hasClass('soldout') ) {
        alert('품절된 옵션입니다.');
        return false;
    }

    if( depth == 1 ) {
        if( empty(uid) ) {
            reset_pdt_option(2);
            reset_pdt_option(3);
            return false;
        }

        $('[name="pdt_option_1"]').val(uid);
        $('[name="pdt_option_1_btn"]').addClass('on');
        $('[name="pdt_option_1_btn"]').text(name);

        if( !empty(pdt_option_data[2]) && !empty(pdt_option_data[2][uid]) ) {
            set_pdt_option_2();
        }
        else {
            selected_option_add();
        }
    }
    else if( depth == 2 ) {
        if( empty(uid) ) {
            reset_pdt_option(3);
            return false;
        }

        $('[name="pdt_option_2"]').val(uid);
        $('[name="pdt_option_2_btn"]').addClass('on');
        $('[name="pdt_option_2_btn"]').text(name);

        if( !empty(pdt_option_data[3]) && !empty(pdt_option_data[3][uid]) ) {
            set_pdt_option_3();
        }
        else {
            selected_option_add();
        }
    }
    else if( depth == 3 ) {
        if( empty(uid) ) {
            return false;
        }

        $('[name="pdt_option_3"]').val(uid);
        $('[name="pdt_option_3_btn"]').addClass('on');
        $('[name="pdt_option_3_btn"]').text(name);
        selected_option_add();
    }

    toggle_op_content(depth);
}//end of op_select_click()


$(function(){
    //선택한 옵션정보
    if( !$('[name="op_data"]').length ) {
        $('body').append('<input type="hidden" name="op_data" value="" />');
    }
    //선택한 추가옵션정보
    if( !$('[name="op_other"]').length ) {
        $('body').append('<input type="hidden" name="op_other" value="" />');
    }
    //상품옵션타입('nooption','1depth','2depth','3depth','img_option','manual_option')
    if( !$('[name="op_type"]').length ) {
        $('body').append('<input type="hidden" name="op_type" value="" />');
    }
    if( !$('[name="shopping_pay_use"]').length ) {
        $('body').append('<input type="hidden" name="shopping_pay_use" value="" />');
    }
    if( !$('[name="shopping_pay"]').length ) {
        $('body').append('<input type="hidden" name="shopping_pay" value="" />');
    }
    if( !$('[name="shopping_pay_free"]').length ) {
        $('body').append('<input type="hidden" name="shopping_pay_free" value="" />');
    }

    //select버전일때
    if( !modal_yn ) {
        //옵션 select 변경시
        $(document).on('change', '.op_select', function(){
            var depth = $(this).data('depth');
            var uid = $(this).val();

            if( depth == 1 ) {
                if( empty(uid) ) {
                    reset_pdt_option(2);
                    reset_pdt_option(3);
                    return false;
                }

                if( !empty(pdt_option_data[2]) && !empty(pdt_option_data[2][uid]) ) {
                    set_pdt_option_2();
                }
                else {
                    selected_option_add();
                }
            }
            else if( depth == 2 ) {
                if( empty(uid) ) {
                    reset_pdt_option(3);
                    return false;
                }

                if( !empty(pdt_option_data[3]) && !empty(pdt_option_data[3][uid]) ) {
                    set_pdt_option_3();
                }
                else {
                    selected_option_add();
                }
            }
            else if( depth == 3 ) {
                if( !empty(uid) ) {
                    selected_option_add();
                }
            }
        });

        //추가옵션 select 변경시
        $(document).on('change', '.op_other_select', function(){
            other_option_add();
        });
    }//endif;

    //수량변경시
    $(document).on('change', 'input.input_op_cnt', function(){
        if( $(this).val() == "" || $(this).val() <= 0 ) {
            $(this).val(1);
        }
        update_op_cnt($(this).closest('.op_selected_item').data('uid'));
    });

    if( typeof(p_sale_state) != 'undefined' && typeof(p_stock_state) != 'undefined' && p_sale_state == 'Y' && p_stock_state == 'Y' ) {
        //상품 옵션 가져오기
        get_product_option();
    }//end of if()
});//end of document.ready