"use strict";

/**
 * 댓글 관련 공통 함수
 */


//폼 필요
//<form name="comment_list_form" id="comment_list_form" method="post">
//  <input type="hidden" name="tb" value="" />
//  <input type="hidden" name="tb_num" value="" />
//  <input type="hidden" name="page" value="1" />
//  <input type="hidden" name="sort_field" value="" />
//  <input type="hidden" name="sort_type" value="" />
//  <input type="hidden" name="output_type" value="html" />
//</form>

//    .comment_list_wrap .cmt-list

var cmt_form = 'comment_list_form';
var cmt_formId = '#' + cmt_form;
var expend_more = false;
// $.fn.메소드명 = function (인자명) {
// //내용
// }

// $.fn.extend({
//     get_best_comment_list : function () {
//         var data = $('#' + cmt_form).serialize() + '&list_type=S&list_per_page=5';
//
//         $.ajax({
//             url : '/comment/list_ajax',
//             data : data,
//             type : 'post',
//             dataType : 'html',
//             success : function (result) {
//                 $(this).html(result);
//             }
//         });
//     }
//
// });

// $.fn.get_best_comment_list = function () {
//     var data = $('#' + cmt_form).serialize() + '&list_type=S&list_per_page=5';
//
//     $.ajax({
//         url : '/comment/list_ajax',
//         data : data,
//         type : 'post',
//         dataType : 'html',
//         success : function (result) {
//             $(this).html(result);
//         }
//     });
// };


/**
 * 유용한 댓글 목록
 */
function get_best_comment_list(target) {
    if( empty(target) || !$(target).length ) {
        return false;
    }

    //tb=product&tb_num=<?php //echo $product_row->p_num; ?>//&list_type=S&list_per_page=5

    var data = $(cmt_formId).serialize() + '&best=Y&list_type=best&list_per_page=5';

    $.ajax({
        url : '/comment/list_ajax',
        data : data,
        type : 'post',
        dataType : 'html',
        success : function (result) {
            $(target).html(result);
        }
    });
}//end of get_best_comment_list()

/**
 * 댓글 목록
 */
function get_comment_list(target) {
    if( empty(target) || !$(target).length ) {
        return false;
    }

    var output_type = $(cmt_formId + ' [name="output_type"]').val();
    var page = $(cmt_formId + ' [name="page"]').val();
    var cur_pos = $(document).scrollTop();

    $.ajax({
        url : '/comment/list_ajax',
        data : $(cmt_formId).serialize(),
        type : 'post',
        dataType : 'html',
        async : false,
        // dataType : 'json',
        success : function (result) {
            if( output_type == 'append' ) {
                $(target).append(result);
            }
            else if( output_type == 'prepend' ) {
                $.when($(target).prepend(result)).done(function () {
                    hidden_remove('.funcCommentMore');

                    var h = 0;
                    $.each($('.n_cmt li[data-page="' + page + '"]'), function(index, item){
                    //$.each($('.cmt-item[data-page="' + page + '"]'), function(index, item){
                        h += parseInt($(this).outerHeight(true));
                    });

                    if( $('.funcCommentMore').length <= 0 ) {
                        h -= 55;
                    }

                    $(document).scrollTop(cur_pos + h);

                    $('.cmt-list').append($('.funcCommentMore'));

                });
            }
            else {
                $(target).html(result);
            }
            // comment_list_print(result, target);
        }
    });
}//end of get_comment_list()

/**
 * 댓글 더보기
 */
function get_comment_list_more() {

    if($(cmt_formId + ' [name="my"]').val() == 'Y' /*&& zsDebug == '1'*/){
        var output_type = 'append';
        $('.funcCommentMore').css({'display':'none'});
    }else{

        //if(zsDebug == '1'){

            var output_type = 'append';
            $('.funcCommentMore').hide();

        // }else{
        //     var output_type = 'prepend';
        //     $('.funcCommentMore').css({'visibility':'hidden'});
        // }


    }

    set_form_value(cmt_form, {'page':parseInt($(cmt_formId + ' [name="page"]').val()) + 1, 'output_type':output_type});
    get_comment_list($('.comment_list_wrap .cmt-list'));
}//end of get_comment_list_more()

/**
 * 댓글 신고 팝업
 */
function comment_report_pop(cmt_num) {
    var container = $('<div></div>');

    // $(container).load('/comment_report/insert_pop/?cmt_num=' + cmt_num);
    // modalPop.createPop("댓글 신고", container);
    // modalPop.show({'hide_footer':true});

    $.ajax({
        url : '/comment_report/insert_pop',
        data : {cmt_num:cmt_num},
        type : 'post',
        dataType : 'html',
        success : function (result) {
            $(container).append(result);
            modalPop.createPop("댓글 신고", container);
            modalPop.show({'hide_footer':true});
        },
        error : function () {
            $('.cmt_menu_pop[data-num="' + cmt_num + '"]').remove();
        }
    });
}//end of comment_report_pop()

/**
 * 댓글 삭제
 */
function comment_delete(cmt_num) {
    if( empty(cmt_num) ) {
        return false;
    }

    if( !confirm('해당 댓글을 삭제하시겠습니까?') ) {
        return false;
    }

    $.ajax({
        url : '/comment/delete_proc',
        data : {cmt_num:cmt_num},
        type : 'post',
        dataType : 'json',
        cache : false,
        async : false,
        success : function (result) {
            if( !empty(result.message_type) && result.message_type == 'alert' && !empty(result.message) ) {
                //alert(result.message);
            }

            if( result.status == status_code['success'] ) {

                $('.cmt_menu_pop').remove();
                //삭제후 폼 초기화
                if(expend_more == true) set_form_value(cmt_form, {'page':1, 'output_type':'html'});
                get_comment_list($('.comment_list_wrap .cmt-list'));

                //댓글수--
                if( $('#comment_count').length > 0 ) {
                    var cnt = $('#comment_count').text();
                    $('#comment_count').text(parseInt(cnt) - 1);
                }

                if( !$('.comment_list.my .cmt-item').length ) {
                    $('.content.comment.my .no_data').show();
                }

            }
            /*
            // if( result.status == status_code['success'] ) {
            // if( result.status == '000' ) {
                $('.cmt_menu_pop').remove();
                $('.comment_list .cmt-item[data-num="' + cmt_num + '"]').next('hr').remove();
                $('.comment_list .cmt-item[data-num="' + cmt_num + '"]').remove();

                //답댓글 삭제
                $('.comment_list .cmt-item.reply[data-parent_num="' + cmt_num + '"]').remove();
                // location.reload();

                //댓글수--
                if( $('#comment_count').length > 0 ) {
                    var cnt = $('#comment_count').text();
                    $('#comment_count').text(parseInt(cnt) - 1);
                }

                if( !$('.comment_list.my .cmt-item').length ) {
                    $('.content.comment.my .no_data').show();
                }
            //}
            */
        }
    });
}//end of comment_delete()

/**
 * 댓글 클릭시
 * @param obj
 */
function comment_item_click(obj) {

    //console.log(obj);


    if( isLogin != 'Y' ) {
        return false;
    }

    if( !appCheck() ) {
        return false;
    }

    var form = '#comment_insert_form';
    var input = $(form + ' #cmt_content');
    var num = $(obj).data('num');
    var name = $(obj).data('name');
    var num_obj = $(form + ' [name="cmt_reply_comment_num"]');
    var reply_obj = $(form + ' [name="cmt_reply_member_name"]');

    if( $(form).length > 0 ) {
        if( !empty(name) ) {
            if( $(input).val().indexOf('@' + name) == -1 ) {
                $(input).val($(input).val() + ' @' + name + ' ');
            }
            if( $(reply_obj).val().indexOf(':@' + name + ':') == -1 ) {
                $(reply_obj).val($(reply_obj).val() + ':@' + name + ':');
            }
            if( $(num_obj).val().indexOf(':' + num + ':') == -1 ) {
                $(num_obj).val($(num_obj).val() + ':' + num + ':');
            }

            $(input).focus();
            comment_input_focus();
        }
    }
}//end of comment_item_click()

/**
 * 댓글 입력폼에 포커스
 */
function comment_input_focus() {
    var t = $('.comment_list_wrap').offset().top - 90;
    $(window).scrollTop(t);
}//end of comment_input_focus()


/**
 * 댓글 서브 메뉴 클릭시
 * @param obj
 * @returns {boolean}
 */
function comment_submenu_click(obj, e) {
    if( $(obj).length < 1 ) {
        return false;
    }
    if( !appCheck() ) {
        return false;
    }

    var cmt_num = $(obj).attr('data-num');
    if( empty(cmt_num) ) {
        e.preventDefault();
        e.stopPropagation();
        // e.stopBubble();
        return false;
    }

    var is_mine = $(obj).attr('data-mine');

    var win_w = $(window).width();
    var win_h = $(window).height();
    var scrolltop = $(window).scrollTop();
    var y = $(obj).offset().top + 33;
    var x = parseInt(e.clientX) - 70;

    if( $('.cmt_menu_pop').length > 0 ) {
        var cur_num = $('.cmt_menu_pop').attr('data-num');

        $('.cmt_menu_pop').remove();

        if( cur_num == cmt_num ) {
            e.preventDefault();
            e.stopPropagation();
            // e.stopBubble();
            return false;
        }
    }

    var container = $('<div class="cmt_menu_pop" data-num="' + cmt_num + '"></div>');
    var item_wrap = $('<ul></ul>');
    $(container).append(item_wrap);

    var item_report = $('<li><a href="#none" class="abs_link report" title="신고하기" onclick="comment_report_pop(' + cmt_num + ');">신고</a></li>');
    var item_delete = $('<li><a href="#none" class="abs_link delete" title="삭제하기" onclick="comment_delete(' + cmt_num + ');">삭제</a></li>');

    //자기 댓글이면 : 삭제
    if( is_mine == 'Y' ) {
        $(item_wrap).append(item_delete);
    }
    //남의 댓글이면 : 신고
    else {
        $(item_wrap).append(item_report);
    }

    $('body').append(container);

    $('.cmt_menu_pop').css({'top':y + 'px', 'left':x + 'px'});
    $('.cmt_menu_pop').show();

    e.preventDefault();
    e.stopPropagation();
    // e.stopBubble();
    return false;
}//end of comment_submenu_click()


//document.ready
$(function () {
    //댓글 더보기
    $(document).on('click', '.funcCommentMore', function(){
        expend_more = true;
        get_comment_list_more();
    });

    //댓글 입력 포커스
    $(document).on('click', '#comment_insert_form [name="cmt_content"]', function () {
        comment_input_focus();
    });

    // //댓글 서브 메뉴 클릭시
    // $(document).on('click', '.funcCommentSubMenu', function (e) {
    //     var cmt_num = $(this).attr('data-num');
    //     if( empty(cmt_num) ) {
    //         return false;
    //     }
    //
    //     var is_mine = $(this).attr('data-mine');
    //
    //     var win_w = $(window).width();
    //     var win_h = $(window).height();
    //     var scrolltop = $(window).scrollTop();
    //     var y = $(this).offset().top + 33;
    //
    //     if( $('.cmt_menu_pop').length > 0 ) {
    //         var cur_num = $('.cmt_menu_pop').attr('data-num');
    //
    //         $('.cmt_menu_pop').remove();
    //
    //         if( cur_num == cmt_num ) {
    //             return false;
    //         }
    //     }
    //
    //     var container = $('<div class="cmt_menu_pop" data-num="' + cmt_num + '"></div>');
    //     var item_wrap = $('<ul></ul>');
    //     $(container).append(item_wrap);
    //
    //     var item_report = $('<li><a href="#none" class="abs_link report" title="신고하기" onclick="comment_report_pop(' + cmt_num + ');">신고</a></li>');
    //     var item_delete = $('<li><a href="#none" class="abs_link delete" title="삭제하기" onclick="comment_delete(' + cmt_num + ');">삭제</a></li>');
    //
    //     //자기 댓글이면 : 삭제
    //     if( is_mine == 'Y' ) {
    //         $(item_wrap).append(item_delete);
    //     }
    //     //남의 댓글이면 : 신고
    //     else {
    //         $(item_wrap).append(item_report);
    //     }
    //
    //     $('body').append(container);
    //
    //     $('.cmt_menu_pop').css({'top':y + 'px', 'left':win_w - $('.cmt_menu_pop').width() - 15 + 'px'});
    //     $('.cmt_menu_pop').show();
    // });

    //로그인 & 앱일때만
    if( isLogin == 'Y' && appCheck() ) {
        if( $('#comment_insert_form').length > 0 ) {
            //ajaxform
            $('#comment_insert_form').ajaxForm({
                type: 'post',
                dataType: 'json',
                async: false,
                cache: false,
                beforeSubmit: function(formData, jqForm, options) {
                    if( !appCheck() ) {
                        only_app_alert();
                        return false;
                    }

                    if( !$('[name="cmt_content"]').val() ) {
                        alert('댓글 내용을 입력하세요.');
                        return false;
                    }
                },
                success: function(result) {
                    if( !empty(result.message_type) && result.message_type == 'alert' && !empty(result.message) ) {
                        //alert(result.message);
                    }

                    if( result.status == status_code['success'] ) {

                        $('#comment_insert_form [name="cmt_content"]').val('').blur();

                        //더보기를 하고 글을 쓴경우 페이지순서를 초기화
                        if(expend_more == true) set_form_value(cmt_form, {'page':1, 'output_type':'html'});

                        get_comment_list($('.comment_list_wrap .cmt-list'));

                        //더보기를 하고 글을 쓴경우 리스트를 리로드 후 더보기 실행
                        if(expend_more == true) get_comment_list_more();

                        //글 등록 후 스크롤이동 처리
                        /*
                        //출석체크 댓글 포커스 하단이동 이슈로 주석 180905 sh
                        var li_last_offsetTop = $('.n_cmt2 li').last().offset().top;
                        $(window).scrollTop(li_last_offsetTop-48);
                        */

                    }
                    else {
                        if( result.error_data ) {
                            error_message_alert(result.error_data);
                        }
                    }//end of if()


                }
            });//end of ajax_form()
        }
    }
    else {
        $('#comment_insert_form #cmt_content').on('focus', function () {
            $(this).blur();
            only_app_alert();
            return false;
        });
        $('#comment_insert_form button').on('click', function () {
            only_app_alert();
            return false;
        });
    }//endif;
});//end of document.ready