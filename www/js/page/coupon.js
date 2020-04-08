"use strict";
/**
 * 쿠폰관련 JS
 */

/**
 * 쿠폰발급
 * @param code
 */
function coupon_issue(code) {
    $.ajax({
        url : '/coupon/coupon_issue_ajax',
        data : {code:code},
        type : 'post',
        dataType : 'json',
        success : function (result) {
            if( result.status == status_code['success'] ) {
                alert('쿠폰이 발급되었습니다. 기분 좋은 쇼핑되세요.');
                location.reload();
            }
            else {
                if( !empty(result.message) ) {
                    alert(result.message);
                }
                location.reload();
            }
        }
    });
}//end of coupon_issue()

/**
 * 쿠폰목록
 */
function coupon_list(selector) {
    if( empty(selector) ) {
        return false;
    }

    var tar_obj = $(selector);
    if( !$(tar_obj).length ) {
        return false;
    }

    $(tar_obj).html('');

    $.ajax({
        url : '/coupon/coupon_list_ajax',
        data : {},
        type : 'post',
        dataType : 'json',
        success : function(result){
            var html = '';
            $.each(result, function(index, item){
                html += '<a href="#none" class="btn btn-xxlarge btn-gray3 mgb10" onclick="coupon_issue(\'' + item.code + '\');">' + item.name + '</a>';
            });
            if( !empty(html) ) {
                $(tar_obj).append(html);
            }
        }
    });
}//end of coupon_list()

/**
 * 회원쿠폰목록
 * @param type
 */
function coupon_member_list(type, selector) {
    if( empty(selector) ) {
        return false;
    }

    var tar_obj = $(selector);
    if( !$(tar_obj).length ) {
        return false;
    }

    $(tar_obj).html('');
    $('.coupon_use_guide').hide();

    $.ajax({
        url : '/coupon/coupon_member_list_ajax',
        data : {type:type},
        type : 'post',
        dataType : 'json',
        success : function(result){
            var html = '';

            var now = new Date();
            var d = now;
            var date1 = get_ymd(d);



            if( empty(result) ) {
                html += '<tr class="cpnm_item">';
                html += '   <td colspan="2" class="col text-center" style="width:100%;padding:20px;position:relative;display:block;">쿠폰내역이 없습니다.</td>';
                html += '</tr>';
            }
            else {
                $.each(result, function(index, item){
                    html += '<tr class="cpnm_item">';
                    html += '   <td class="col text-center">';
                    html += '       <div class="coupon' +  ((item.kind == "2") ? ' one' : '') + '">' + '<span style="letter-spacing: -1px;">' + item.kind_txt + ((item.kind == '1') ? '</span> <br /> <span style="color:#ff3c63; font-weight: 600; font-size: 15px;">' + item.price : '')  + '</span> </div>';
                    html += '   </td>';
                    html += '   <td class="col" style="padding-left:10px;">';
                    html += '<span class="couName">';
                    html += item.name + '<br />';
                    html += '</span>';
                    html += '<span class="couPri">';
                    html += ((!empty(item.min_orderprice)) ? item.min_orderprice + '<br />' : '');
                    html += '</span>';
                    html += '<span class="couMpri">';
                    html += ((!empty(item.price_max)) ? item.price_max + '<br />' : '');
                    html += '</span>';

                    html += '<span class="couDate">';
                    if(date1 == item.expire_datetime) html += "오늘까지";
                    else html += item.reg_datetime + ' - ' +item.expire_datetime;
                    html += '</span>';

                    html += '   </td>';
                    html += '</tr>';
                });
            }
            $(tar_obj).append(html);

            if( type == '1' ) {
                $('.coupon_use_guide').show();
            }
        }
    });
}//end of coupon_member_list()