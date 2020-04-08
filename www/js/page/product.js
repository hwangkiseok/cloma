"use strict";

function submitContents(elClickedObj) {
    $('textarea[name="p_detail"]').val(CKEDITOR.instances.field_p_detail.getData());

    try {
        elClickedObj.form.submit();
    } catch(e) {}
}//end of submitContents()

//function pasteHTML(str) {
//    var sHTML = "<span style='color:#FF0000;'>이미지도 같은 방식으로 삽입합니다.<\/span>";
//    oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
//}

/**
 * 상품 상세이미지 input 추가
 */
function add_input_detail_image () {
    var no =  parseInt($('input[name*="p_detail_image["]').length) + 1;
    if( no > 10 ) {
        alert('최대 10까지 입력가능합니다.');
        return false;
    }

    var html = '';
    html += '<div class="input-group mgt10">';
    html += '    <span class="input-group-addon">상세이미지' + no + '</span>';
    html += '    <input type="file" id="field_p_detail_image_' + no + '" name="p_detail_image[' + no + ']" class="form-control" />';
    html += '</div>';

    $('#field_p_detail_image').append(html);
}//end of add_input_detail_image()

/**
 * 할인율 계산
 */
function calc_discount_rate() {

    var p_original_price = $('[name="p_original_price"]').val().replace(/,/gi, "");
    var p_sale_price = $('[name="p_sale_price"]').val().replace(/,/gi, "");


    var org_price = parseInt(p_original_price);   //기존가격
    var sale_price = parseInt(p_sale_price);      //판매가격
    var rate = '0.00';

    if( org_price && sale_price ) {
        rate = number_format(100 - ((sale_price / org_price) * 100), 2, '.');
    }

    $('[name="p_discount_rate"]').val(rate);
}//end of calc_discount_rate()

/**
 * 판매마진 계산
 */
function calc_margin_price() {

    var p_supply_price = $('[name="p_supply_price"]').val().replace(/,/gi, "");
    var p_sale_price = $('[name="p_sale_price"]').val().replace(/,/gi, "");

    var sup_price = parseInt(p_supply_price);     //공급가격
    var sale_price = parseInt(p_sale_price);      //판매가격
    var ma_price = 0;

    if( sup_price && sale_price ) {
        ma_price = number_format(sale_price - sup_price);
    }

    $('[name="p_margin_price"]').val(ma_price);
}//end of calc_margin_price()

/**
 * 마진율 계산
 */
function calc_margin_rate() {

    var p_supply_price = $('[name="p_supply_price"]').val().replace(/,/gi, "");
    var p_sale_price = $('[name="p_sale_price"]').val().replace(/,/gi, "");

    var sup_price = parseInt(p_supply_price);   //공급가격
    var sale_price = parseInt(p_sale_price);    //판매가격
    var ma_rate = 0;

    if( sup_price && sale_price ) {
        ma_rate = number_format(((sale_price - sup_price) / sale_price) * 100, 2, '.');
    }

    $('[name="p_margin_rate"]').val(ma_rate);
}//end of calc_margin_rate()

/**
 * 상품 삭제
 * @returns {boolean}
 */
function delete_product() {
    if( empty(delete_url) ) {
        alert('잘못된 접근입니다.');
        return false;
    }

    if( confirm('삭제하시겠습니까?') ) {
        $('#main_form').attr('action', delete_url);
        $('#main_form').submit();
    }
}//end of delete_product()

/**
 * 상품 상세 이미지 삭제
 * @param p_num     : 상품번호
 * @param img_div   : 이미지구분 (1=오늘추천이미지, 2=상세이미지(선택), 3=상세이미지(전체), 4=배너이미지, 5=추가대표이미지(선택))
 * @param img_no    : 이미지번호
 */
function delete_product_image(p_num, img_div, obj) {
    if( !confirm('이미지를 삭제하시겠습니까?\n삭제된 이미지는 복구가 되지 않으며,\n삭제 즉시 바로 반영됩니다.') ) {
        return false;
    }

    var img_no = $(obj).closest('.list-item').attr('data-key');

    if(img_div == 5){
        img_no = $(obj).closest('.list-item-2').attr('data-key');
    }
    Pace.restart();

    $.ajax({
        url : '/product/image_delete_proc',
        data : {p_num:p_num, img_div:img_div, img_no:img_no},
        type : 'post',
        dataType : 'json',
        success : function(result) {
            if( result.message_type == 'alert' ) {
                alert(result.message);
            }

            if( result.status == status_code['success'] ) {
                if( img_div == 1 ) {
                    $('.pTodayImage').html('');
                }
                else if ( img_div == 2 ) {
                    $('#detail_image_list [data-key="' + img_no + '"]').remove();

                    //재정렬
                    $.each($('#detail_image_list .list-item'), function(index, item){
                        $(this).attr('data-key', index);
                    });
                }
                else if ( img_div == 3 ) {
                    $('#detail_image_list .list-item').remove();
                }
                else if( img_div == 4 ) {
                    $('.pBannerImage').html('');
                }
                else if ( img_div == 5 ) {
                    $('#p_rep_image_add_list [data-key="' + img_no + '"]').remove();

                    //재정렬
                    $.each($('#p_rep_image_add_list .list-item'), function(index, item){
                        $(this).attr('data-key', index);
                    });
                }
            }
        },
        complete : function(){
            Pace.stop();
        }
    });
}//end of delete_product_image()

/**
 * 상품 상세 이미지 삭제 B
 * @param p_num     : 상품번호
 * @param img_div   : 이미지구분 (1=오늘추천이미지, 2=상세이미지(선택), 3=상세이미지(전체), 4=배너이미지)
 * @param img_no    : 이미지번호
 */
function delete_product_image_b(p_num, img_div, obj) {
    if( !confirm('이미지를 삭제하시겠습니까?\n삭제된 이미지는 복구가 되지 않으며,\n삭제 즉시 바로 반영됩니다.') ) {
        return false;
    }

    var img_no = $(obj).closest('.list-item').attr('data-key');

    Pace.restart();

    $.ajax({
        url : '/product/image_delete_proc',
        data : {p_num:p_num, img_div:img_div, img_no:img_no , type:'B'},
        type : 'post',
        dataType : 'json',
        success : function(result) {
            if( result.message_type == 'alert' ) {
                alert(result.message);
            }

            if( result.status == status_code['success'] ) {
                if( img_div == 1 ) {
                    alert('b test div 1');
                    //$('.pTodayImage').html('');
                }
                else if ( img_div == 2 ) {
                    $('#detail_image_b_list [data-key="' + img_no + '"]').remove();

                    //재정렬
                    $.each($('#detail_image_b_list .list-item'), function(index, item){
                        $(this).attr('data-key', index);
                    });
                }
                else if ( img_div == 3 ) {
                    $('#detail_image_b_list .list-item').remove();
                }
                else if( img_div == 4 ) {
                    alert('b test div 4');
                    //$('.pBannerImage').html('');
                }
            }
        },
        complete : function(){
            Pace.stop();
        }
    });
}//end of delete_product_image()


/**
 * 상세이미지 순서 변경
 * @param cur_key
 * @param prev_key
 */
function detail_img_order_update(p_num) {
    var data = [];
    $.each($('#detail_image_list .list-item'), function(index, item){
        data.push($(this).attr('data-key'));
        $(this).attr('data-key', index);
    });
    data = data.join('|');

    $.ajax({
        url : '/product/detail_img_order_update',
        data : {p_num:p_num, data:data},
        type : 'post',
        dataType : 'json',
        success : function (result) {
            if( result.status == status_code['success'] ) {
            }
        },
        error : function () {
            // alert('Error!!');
            console.log('Error!!');
        }
    });
}//end of detail_img_order_change()


function detail_img_order_update_rep(){


    var data = [];
    $.each($('#p_rep_image_add_list .list-item-2'), function(index, item){
        data.push($(this).attr('data-key'));
        $(this).attr('data-key', index);
    });
    data = data.join('|');

    $.ajax({
        url : '/product/rep_image_add_order_update',
        data : {p_num:p_num, data:data},
        type : 'post',
        dataType : 'json',
        success : function (result) {
            if( result.status == status_code['success'] ) {
            }
        },
        error : function () {
            // alert('Error!!');
            console.log('Error!!');
        }
    });


}

/**
 * 상세이미지 순서 변경
 * @param cur_key
 * @param prev_key
 */
function detail_img_b_order_update(p_num) {
    var data = [];
    $.each($('#detail_image_b_list .list-item'), function(index, item){
        data.push($(this).attr('data-key'));
        $(this).attr('data-key', index);
    });
    data = data.join('|');

    // console.log(data);

    $.ajax({
        url : '/product/detail_img_order_update',
        data : {p_num:p_num, data:data , type:'B'},
        type : 'post',
        dataType : 'json',
        success : function (result) {
            if( result.status == status_code['success'] ) {
            }
        },
        error : function () {
            // alert('Error!!');
            console.log('Error!!');
        }
    });

}//end of detail_img_order_change()
//document.ready
$(function(){

    $('.hashtag_ex').on('click',function(e){

        var val = $('#field_p_hash').val();
        if(empty(val) == false) $('#field_p_hash').val(val+','+$(this).data('tag'));
        else $('#field_p_hash').val($(this).data('tag'));

    });

    //datepicker
    $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

    //p_termlimit_yn click
    $('[name="p_termlimit_yn"]').on('click', function(){
        if( $(this).val() == 'Y' ) {
            $('#termlimit_date').show();
        }
        else {
            $('#termlimit_date').hide();
        }
    });

    //할인율, 판매마진, 마진율 계산
    $('[name="p_supply_price"],[name="p_original_price"],[name="p_sale_price"]').on('change', function(){
        calc_discount_rate();
        calc_margin_price();
        calc_margin_rate();
    });
    $('[name="p_discount_rate"]').on('focus blur', function(){
        calc_discount_rate();
    });
    $('[name="p_margin_price"]').on('focus blur', function(){
        calc_margin_price();
    });
    $('[name="p_margin_rate"]').on('focus blur', function(){
        calc_margin_rate();
    });

    //상세 이미지 파일 선택시
    $('[type="file"][name="p_detail_image[]"]').on('change', function(){
        if ( detail_image_max_count < $(this).get(0).files.length ) {
            alert('한번에 올릴 수 있는 파일 갯수는 ' + max_detail_image_count + '개 입니다.');
            return false;
        }
    });

    //submit
    $('#main_form').on('submit', function(){
        info_message_all_clear();

        submitContents();

        calc_discount_rate();
        calc_margin_price();
        calc_margin_rate();

        Pace.restart();
    });

    //ajax form
    if( $('#main_form').length > 0 ) {
        $('#main_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                //if( empty(oEditors) ) {
                //    alert('HTML 에디터 로딩중입니다... \nHTML 에디터 로딩 완료후에 시도해 주세요.');
                //    return false;
                //}
                //
                //info_message_all_clear();
                //
                //submitContents();
                //
                //calc_discount_rate();
                //calc_margin_price();
                //calc_margin_rate();
                //
                //Pace.restart();
                // console.log('formdata ==> ', formData);

            },
            success: function(res) {
                if( res.message ) {
                    if( res.message_type == 'alert' ) {
                        alert(res.message);
                    }
                }

                if( res.status == status_code['success'] ) {
                    location.replace(list_url);
                }
                else {
                    if( res.error_data ) {
                        $.each(res.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                $(this).attr('action', this_form_action);
                Pace.stop();
            }
        });//end of ajax_form()
    }//end of if()
});//en d of document.ready()