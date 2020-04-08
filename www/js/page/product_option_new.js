"use strict";

/**
 * 상품 옵션 관련 JS
 * : p_num, p_sale_state, p_stock_state
 */
var pdt_option_type = '';   //1depth, 2depth, 3depth, nooption
var pdt_option_data = '';   //옵션data
var pdt_other_data = '';    //추가옵션data
var empty_stock_cnt = 0;    //품절상품 갯수
var modal_yn = false;       //상품옵션을 모달로 출력할지
var stock_on = 'on';
var shipping_text = '';
// if( !$.browser.android ) {
//     modal_yn = true;
// }

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

                //console.log(info);

                if(!empty(info.sendcompany)){
                    shipping_text = info.sendcompany;
                }

                if(!empty(info.shipping_info)){
                    if(!empty(shipping_text)){
                        shipping_text += ' <br> ' +info.shipping_info
                    }else{
                        shipping_text += info.shipping_info
                    }
                }

                if(!empty(shipping_text)){
                    shipping_text += '<br>';
                }

                $('.add-item .shipping_info').html(shipping_text);
                $('.add-item .shipping_info').show();

                pdt_option_type = info.option_type;
                empty_stock_cnt = info.empty_stock_cnt;

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
                html += '   <div class="t_btn" style="visibility:hidden "><a href="#none" class="btn" onclick="toggle_pdt_option_wrap();"><img src="' + img_http + '/images/new/icon_arw_w_up_red.png" alt="" /></a></div>';
                html += '   <div class="item_wrap op_wrap">';

                //옵션이 없을때 (수량선택만)
                if( pdt_option_type == 'nooption' || empty(options) ) {
                    if( parseInt(info.stock) < 1  ) {
                        $('.order_fix').addClass('full');
                        $('.order_fix').html('<a href="#none" class="btn btn-xxlarge" onclick="alert(\'품절된 상품입니다.\');">품절</a>');
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

                    html += '       <div class="op_selected_wrap_pd">';
                    html += '       <div class="op_selected_wrap">';
                    html += '           <div class="op_selected_item" data-uid="0" data-cnt="1" data-stock="' + info.stock + '" data-price="' + price + '" data-del="N" data-other="N">';
                    html += '              <input type="hidden" name="op_uid[]" value="0" />';
                    html += '              <div class="op_name">' + info.name + '</div>';
                    if(info.stock < 11){ // 재고수량이 11개 미만인 경우 아래 태그 삽입
                        html += '<span class="deadline2">매진임박 (<em>8개</em>)</span>';
                    }
                    html += '              <div class="op_cnt" style="margin-right: 0px">';
                    html += '                  <a href="#none" class="btn btn_op_cnt_minus ico" onclick="update_op_cnt(0, \'-\', 1);">#</a>';
                    //html += '                  <input type="number" name="op_cnt[]" id="op_cnt_0" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" />';
                    html += '                  <input type="hidden" name="op_cnt[]" id="op_cnt_0" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" />';
                    html += '                  <div class="input_op_cnt_div uid_0">1</div>';
                    html += '              <a href="#none" class="btn btn_op_cnt_plus ico" onclick="update_op_cnt(0, \'+\', 1);">#</a>';
                    html += '              </div>';
                    html += '           </div>';
                    html += '           </div>';
                    html += '           </div>';
                    html += '       <div class="op_selected_total">총 주문금액 <em>' + number_format(price)+ '원</em></div>';
                }
                //옵션이 있을때
                else {

                    html += '<div class="select_opt_wrap">';
                    html += '    <h2 class="tit">옵션</h2>';
                    html += '<div class="select_opt" role="button">옵션을 선택해 주세요.</div>';
                    html += '<div class="select_opt_add" role="button" style="display: none;">추가옵션을 선택해 주세요.</div>';

                    html += '<div class="op_selected_wrap"></div>';

                    html += '</div>';
                    html += '       <div class="op_selected_total">총 주문금액 <em>' + number_format(price)+ '원</em></div>';

                    html += '   </div>';
                    html += '</div>';

                    var sub_html = "";

                        sub_html += '<div class="op_layer">';

                        sub_html += '   <div class="op_hd">';
                        sub_html += '       <h3 class="fl">옵션 선택</h3>';
                        sub_html += '       <button class="chk fl">품절제외</button>';
                        sub_html += '       <a href="#none" class="op_close fr" style="z-index:5;">닫기</a>';
                        sub_html += '   </div>';
                        sub_html += '   <div class="op_slt_wrap">';

                    if( !empty(options[1]) ) {
                        sub_html += get_product_option_html_new(1,options[1],options[2]);
                    }

                    if( !empty(options[2]) ) {
                        sub_html += get_product_option_html_new(2,options[2],options[3]);
                    }

                    if( !empty(options[3]) ) {
                        sub_html += get_product_option_html_new(3,options[3],"");
                    }

                        sub_html += '    </div>';
                        sub_html += '</div>';


                    if(!empty(pdt_other_data) ) {

                        var sub_html_add = "";

                        sub_html_add += '<div class="op_layer other">';

                        sub_html_add += '   <div class="op_hd">';
                        sub_html_add += '       <h3 class="fl">옵션 선택</h3>';
                        sub_html_add += '       <button class="chk fl">품절제외</button>';
                        sub_html_add += '       <a href="#none" class="op_close fr" style="z-index:5;">닫기</a>';
                        sub_html_add += '   </div>';
                        sub_html_add += '   <div class="op_slt_wrap">';

                        sub_html_add += '    <div class="op_slt op_new_other open" data-depth="0" >';
                        sub_html_add += '        <button type="button" class="op_name">';
                        sub_html_add += '        옵션명';
                        sub_html_add += '        <span id="optSelected" class="selected"></span>';
                        sub_html_add += '        </button>';

                        sub_html_add += '        <ul class="list_img" name="pdt_option_other">';
                        sub_html_add += '        <input type="hidden" name="pdt_option_other" value="" />';

                        $(pdt_other_data).each(function (index, row) {

                            var deadline_html_add   = '';
                            var deadline_class_add  = '';
                            //재고 체크
                            if (parseInt(row.stock) < 1) {
                                var add_style = '';
                                if(stock_on == 'off') add_style = '" style="display:none; ';
                                deadline_html_add = '<span class="deadline">품절</span>';
                                deadline_class_add = ' out' +add_style;
                            }else if(parseInt(row.stock) < 11){
                                deadline_html_add = '<span class="deadline2">매진임박 (<em>'+parseInt(row.stock)+'개</em>)</span>';
                            }

                            sub_html_add += '<li class="' + deadline_class_add + '" data-name="' + row.name + '" data-uid="' + row.uid + '" data-depth="0" data-stock="' + row.stock + '" data-price="' + row.price + '" onclick="other_option_add(this);">';
                            sub_html_add += '    <a>';
                            if (sub_html_add.img) html += ' <span class="thumb"><img src="' + row.img + '" alt=""></span>';
                            sub_html_add += '        <span class="opt">' + row.name + '</span>';
                            sub_html_add += deadline_html_add;
                            sub_html_add += '        <span class="prc"><b>'+number_format(row.price)+'</b>원</span>';
                            sub_html_add += '    </a>';
                            sub_html_add += '</li>';

                        });

                        sub_html_add += '        </ul>';
                        sub_html_add += '    </div>';
                        sub_html_add += '    </div>';
                        sub_html_add += '</div>';

                    }

                }//end of if()

                $('.order_fix').append(html);
                $('.order_fix').append(sub_html);
                $('.order_fix').append(sub_html_add);

                //품절상품이 없는 경우 품절제외 영역 hide
                if(empty_stock_cnt < 1) $('.op_hd .chk').hide();

            }
        }//end of success()
    });//end of ajax()
}//end of get_product_option()

function get_product_option_html_new(depth,opt,child_opt){

    var html = '';
        html += '    <div class="op_slt op_new'+depth+'" data-depth="'+depth+'" >';
        html += '        <button type="button" class="op_name">';
        html += '        옵션명'+depth;
        html += '        <span id="optSelected" class="selected"></span>';
        html += '        </button>';

        html += '        <ul class="list_img" name="pdt_option_'+depth+'">';
        html += '        <input type="hidden" name="pdt_option_' + depth + '" value="" />';

    if(depth == 1) { //1차 옵션

        $(opt).each(function (index, row) {

            var deadline_html   = '';
            var deadline_class  = '';

            //2차 옵션이 없을때 재고 체크
            if (empty(child_opt)) {
                if (parseInt(row.stock) < 1) {
                    var add_style = '';
                    if(stock_on == 'off') add_style = '" style="display:none; ';
                    deadline_html = '<span class="deadline">품절</span>';
                    deadline_class = ' out' +add_style;
                }else if(parseInt(row.stock) < 11){
                    deadline_html = '<span class="deadline2">매진임박 (<em>'+parseInt(row.stock)+'개</em>)</span>';
                }
            }else if(row.stock_flag == 'N'){
                var add_style = '';
                if(stock_on == 'off') add_style = '" style="display:none; ';
                deadline_html = '<span class="deadline">품절</span>';
                deadline_class = ' out' +add_style;
            }

            html += '<li class="' + deadline_class + '" data-name="' + row.name + '" data-uid="' + row.uid + '" data-depth="' + depth + '" data-stock="' + row.stock + '" data-price="' + row.price + '" onclick="op_select_click_new(this);">';
            html += '    <a>';
            //html += '<span class="thumb"><img src="/images/option/option_dumy.png" alt=""></span>';
            if (row.img) html += ' <span class="thumb"><img src="' + row.img + '" alt=""></span>';
            html += '        <span class="opt">' + row.name + '</span>';
            html += deadline_html;
            html += '        <span class="prc"><b>'+number_format(row.price)+'</b>원</span>';
            html += '    </a>';
            html += '</li>';

        });

    }

    html += '        </ul>';
    html += '    </div>';

    return html;

}

/**
 * 옵션영역 toggle
 */
function toggle_pdt_option_wrap() {
    //감추기
    if( $('.item_wrap').hasClass('on') ) {
        $('.pdt_option_wrap .t_btn').css('visibility','hidden');
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

    $('.order_fix .cartTooltip').removeClass('ani');


}//end of toggle_pdt_option_wrap()

/**
 * 2차옵션 값 설정
 */
function set_pdt_option_2_new() {
    var uid = $('[name="pdt_option_1"]').val();

    if( empty(uid) ) {
        $('[name="pdt_option_1"]').focus();
        return false;
    }

    reset_pdt_option(2);
    $('[name="pdt_option_2"]').val('');

    var cnt = pdt_option_data[2][uid].length;

    var html  = '';
        html += '       <input type="hidden" name="pdt_option_2" value="" />';

    $.each(pdt_option_data[2][uid], function(index, row){
        var deadline_html = '';
        var deadline_class = '';
        //3차 옵션이 없을때 재고 체크
        if (empty(pdt_option_data[3])) {
            if (parseInt(row.stock) < 1) {
                var add_style = '';
                if(stock_on == 'off') add_style = '" style="display:none; ';
                deadline_html = '<span class="deadline">품절</span>';
                deadline_class = ' out' +add_style;
            }else if(parseInt(row.stock) < 11){
                deadline_html = '<span class="deadline2">매진임박 (<em>'+parseInt(row.stock)+'개</em>)</span>';
            }
        }else if(row.stock_flag == 'N'){
            var add_style = '';
            if(stock_on == 'off') add_style = '" style="display:none; ';
            deadline_html = '<span class="deadline">품절</span>';
            deadline_class = ' out' +add_style;
        }

        html += '<li class="' + deadline_class + '" data-name="' + row.name + '" data-uid="' + row.uid + '" data-depth="2" data-stock="' + row.stock + '" data-price="' + row.price + '" onclick="op_select_click_new(this);">';
        html += '    <a>';

        if (row.img) {
            html += '        <span class="thumb"><img src="' + row.img + '" alt=""></span>';
        }

        html += '        <span class="opt">' + row.name + '</span>';
        html += deadline_html;
        html += '        <span class="prc"><b>'+number_format(row.price)+'</b>원</span>';
        html += '    </a>';
        html += '</li>';


    });

    $('[name="pdt_option_2"]').html(html);
    $('.op_new2').addClass('open');

    reset_pdt_option(3);
}//end of set_pdt_option_2()


/**
 * 2차옵션 값 설정
 */
function set_pdt_option_3_new() {
    var uid = $('[name="pdt_option_2"]').val();

    if( empty(uid) ) {
        $('[name="pdt_option_2"]').focus();
        return false;
    }

    reset_pdt_option(3);
    $('[name="pdt_option_3"]').val('');

    var cnt = pdt_option_data[3][uid].length;

    var html = '';
    html    += '       <input type="hidden" name="pdt_option_3" value="" />';

    $.each(pdt_option_data[3][uid], function(index, row){

        var deadline_html = '';
        var deadline_class = '';

        //옵션이 없을때 재고 체크
        if (parseInt(row.stock) < 1 || row.stock_flag == 'N') {
            var add_style = '';
            if(stock_on == 'off') add_style = '" style="display:none; ';
            deadline_html = '<span class="deadline">품절</span>';
            deadline_class = ' out' +add_style;
        }else if(parseInt(row.stock) < 11){
            deadline_html = '<span class="deadline2">매진임박 (<em>'+parseInt(row.stock)+'개</em>)</span>';
        }

        html += '<li class="' + deadline_class + '" data-name="' + row.name + '" data-uid="' + row.uid + '" data-depth="3" data-stock="' + row.stock + '" data-price="' + row.price + '" onclick="op_select_click_new(this);">';
        html += '    <a>';

        if (row.img) {
            html += '        <span class="thumb"><img src="' + row.img + '" alt=""></span>';
        }

        html += '        <span class="opt">' + row.name + '</span>';
        html += deadline_html;
        html += '        <span class="prc"><b>'+number_format(row.price)+'</b>원</span>';
        html += '    </a>';
        html += '</li>';

    });

    $('[name="pdt_option_3"]').html(html);
    $('.op_new3').addClass('open');

}//end of set_pdt_option_2()

/**
 * 최종옵션 선택시
 */
function selected_option_add() {
    var sel_obj = null;

    // console.log('FNC selected_option_add');

    if( pdt_option_type == "1depth" ) {
        sel_obj = $('[name="pdt_option_1"] li.on');
    }
    else if( pdt_option_type == "2depth" ) {
        sel_obj = $('[name="pdt_option_2"] li.on');
    }
    else if( pdt_option_type == "3depth" ) {
        sel_obj = $('[name="pdt_option_3"] li.on');
    }

    var name = '';
    if( $('[name="pdt_option_1"]').length > 0 ) {
        name += $('[name="pdt_option_1"] li.on').data('name');
    }
    if( $('[name="pdt_option_2"]').length > 0 ) {
        name += ' | ' + $('[name="pdt_option_2"] li.on').data('name');
    }
    if( $('[name="pdt_option_3"]').length > 0 ) {
        name += ' | ' + $('[name="pdt_option_3"] li.on').data('name');
    }

    var uid = $(sel_obj).data('uid');
    var stock = $(sel_obj).data('stock');

    //판매가 설정(앱혜택가 체크)
    var price = $(sel_obj).data('price');
    if( appCheck() && !empty($(sel_obj).data('price_app_yn')) && $(sel_obj).data('price_app_yn') == "Y" && !empty($(sel_obj).data('price_app')) ) {
        price = $(sel_obj).data('price_app');
    }

    //이미 선택된 옵션이면
    if( $('.op_selected_item[data-uid="' + uid + '"]').length > 0 ) {
        update_op_cnt(uid, '+', 1);
        $('.op_layer').hide();
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

    $('.op_layer').hide();
    reset_pdt_option(1);
    reset_pdt_option(2);
    reset_pdt_option(3);
    // $('body').css('overflow','');
    if(!empty(pdt_other_data) ) $('.select_opt_add').show();

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
    html += '   <div class="op_price">' + number_format(price) + '원</div>';
    html += '   <div class="op_cnt">';
    html += '       <a href="#none" class="btn btn_op_cnt_minus ico" onclick="update_op_cnt(\'' + uid + '\', \'-\', 1);">#</a>';
    // html += '       <input type="text" name="op_cnt[]" id="op_cnt_' + uid + '" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" readonly />';
    html += '       <input type="hidden" name="op_cnt[]" id="op_cnt_' + uid + '" numberOnly="true" maxlength="5" value="1" class="input_op_cnt" />';
    html += '       <div class="input_op_cnt_div uid_'+uid+'">1</div>';
    html += '       <a href="#none" class="btn btn_op_cnt_plus ico" onclick="update_op_cnt(\'' + uid + '\', \'+\', 1);">#</a>';
    html += '   </div>';
    html += '   <a hare="#none" class="btn_op_del btn ico" onclick="delete_op_selected(\'' + uid + '\')">#</a>';
    html += '</div>';
    $('.op_selected_wrap').append(html);
}//end of selected_option_add_html()

/**
 * 추가옵션 선택시
 */
function other_option_add(obj) {

    if( !$(obj).data('uid') ) {
        return false;
    }
    if( $(obj).hasClass('soldout') || parseInt($(obj).data('stock')) < 1 ) {
        alert('품절된 옵션입니다.');
        return false;
    }

    var uid = $(obj).data('uid');
    var name = $(obj).data('name');
    var price = $(obj).data('price');
    var stock = $(obj).data('stock');

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
    $('.op_layer').hide();
    // $('body').css('overflow','');

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

    $('.input_op_cnt_div.uid_'+ uid).html(cnt);

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

    // console.log('FNC reset_pdt_option',depth);

    if( empty(depth) ) {

        if( $('[name="pdt_option_1"]').length > 0 ) {
            $('[name="pdt_option_1"]').html();
            $('[name="pdt_option_1"]').val('');
            $('.op_new1 #optSelected').html('');

            $('[name="pdt_option_1"] li').css('display','table');
            $('[name="pdt_option_1"] li').removeClass('on');


        }else if( $('[name="pdt_option_2"]').length > 0 ) {
            $('[name="pdt_option_2"]').html();
            $('[name="pdt_option_2"]').val('');
            $('.op_new2 #optSelected').html('');

            $('[name="pdt_option_2"] li').hide();
            $('[name="pdt_option_2"] li').removeClass('on');
            $('.op_new2').removeClass('open');

        }else if( $('[name="pdt_option_3"]').length > 0 ) {
            $('[name="pdt_option_3"]').html();
            $('[name="pdt_option_3"]').val('');
            $('.op_new3 #optSelected').html('');

            $('[name="pdt_option_3"] li').hide();
            $('[name="pdt_option_3"] li').removeClass('on');
            $('.op_new3').removeClass('open');

        }

    } else {

        $('[name="pdt_option_' + depth + '"]').html();
        $('[name="pdt_option_' + depth + '"]').val('');
        $('.op_new' + depth + ' #optSelected').html('');

        if(depth == 1){
            $('[name="pdt_option_' + depth + '"] li').css('display','table');
            $('[name="pdt_option_' + depth + '"] li').removeClass('on');
        }else{
            $('.op_new' + depth).removeClass('open');
            if(depth != 1) $('[name="pdt_option_' + depth + '"]').html('');
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

        if(!empty(pdt_other_data)) $('.select_opt_add').hide();

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

/**
 * 옵션창 위치 설정
 */
function set_pdt_option_wrap_position() {
/*
    var top  = $('.pdt_option_wrap .op_item').outerHeight(true);
        top += $('.pdt_option_wrap .t_btn').outerHeight(true);
        // top += $('.pdt_option_wrap .op_selected_wrap').outerHeight(true);
        top += $('.pdt_option_wrap .op_selected_total').outerHeight(true);
        top += $('.pdt_option_wrap .select_opt_wrap').outerHeight(true) != null?$('.pdt_option_wrap .select_opt_wrap').outerHeight(true) :$('.pdt_option_wrap .op_selected_wrap').outerHeight(true)
*/

    // console.log('start');
    // console.log($('.pdt_option_wrap .op_item').outerHeight(true));
    // console.log($('.pdt_option_wrap .t_btn').outerHeight(true));
    // console.log($('.pdt_option_wrap .op_selected_wrap').outerHeight(true));
    // console.log($('.pdt_option_wrap .op_selected_total').outerHeight(true));
    // console.log($('.pdt_option_wrap .select_opt_wrap').outerHeight(true));
    // console.log('end');

    var top  = $('.pdt_option_wrap').outerHeight(true);

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
 * 옵션 클릭(선택)시
 * @returns {boolean}
 */
function op_select_click_new(obj) {

    var depth = $(obj).data('depth');
    var uid = $(obj).data('uid');
    var name = $(obj).data('name');

    if( $(obj).hasClass('out') ) {
        alert('품절된 옵션입니다.');
        return false;
    }

    $('.op_new'+depth).removeClass('open');
    $('.op_new'+depth+' #optSelected').text(name);

    if( depth == 1 ) {
        if( empty(uid) ) {
            reset_pdt_option(2);
            reset_pdt_option(3);
            return false;
        }

        $('[name="pdt_option_1"]').val(uid);
        $(obj).parent().find('li').removeClass('on');
        $(obj).addClass('on');


        if( !empty(pdt_option_data[2]) && !empty(pdt_option_data[2][uid]) ) {
            set_pdt_option_2_new();
        }
        else {
            selected_option_add();
        }

    }else if( depth == 2 ) {

        if( empty(uid) ) {
            reset_pdt_option(3);
            return false;
        }

        $('[name="pdt_option_2"]').val(uid);
        $(obj).parent().find('li').removeClass('on');
        $(obj).addClass('on');

        if( !empty(pdt_option_data[3]) && !empty(pdt_option_data[3][uid]) ) {
            set_pdt_option_3_new();
        }
        else {
            selected_option_add();
        }

    }else if( depth == 3 ) {

        if( empty(uid) ) {
            return false;
        }

        $('[name="pdt_option_3"]').val(uid);
        $(obj).parent().find('li').removeClass('on');
        $(obj).addClass('on');

        selected_option_add();

    }

    $('.op_new'+depth+' li:not(".on")').hide();

}

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

//품절체크
$(document).on('click','.op_hd .chk', function () {

    $(this).toggleClass('on');

    if(stock_on == 'on') {
        stock_on = 'off';
        if(empty_stock_cnt > 0) {
            if( appCheck() ) {
                showToast(empty_stock_cnt+"개의 품절옵션이\n제외되었습니다");
            }else{
                showToast(empty_stock_cnt+'개의 품절옵션이<br>제외되었습니다');
            }

        }
        $('.list_img li.out').hide();
    }else{
        stock_on = 'on';
        $('.list_img li.out').show();
    }

});
//옵션 리스트 열릴때 open추가
$(document).on('click','.op_name',function(){
    var depth =  $(this).parent().attr('data-depth');
    $('.op_new'+depth+' li').css('display','table');
    if( $('[name="pdt_option_'+depth+'"] li').length > 0 && $('.op_new' + depth + '.op_slt').hasClass('open') == false ) {
        $('.op_new' + depth + '.op_slt').addClass('open');
    }

});

$(document).on('click','.select_opt', function () {
    $('.op_new1').addClass('open');
    // $('body').css('overflow','hidden');
    $('.op_layer:not(.other)').css('display', 'block');
    reset_pdt_option(1);
    reset_pdt_option(2);
    reset_pdt_option(3);
});


$(document).on('click','.select_opt_add', function () {
    $('.op_new1').addClass('open');
    $('.op_hd .chk').removeClass('on');
    // $('body').css('overflow','hidden');
    $('.op_layer.other').css('display', 'block');
});

$(document).on('click','.op_close', function () {
    // $('body').css('overflow','');
    $('.op_layer').css('display', 'none');
});



