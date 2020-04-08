$(function(){

    if($('.more-wrap').length > 0 && $('.comment_area').length < 1 ) $('.more-wrap').remove(); //댓글이 없는경우 버튼삭제

    $('form[name="comment_frm"]').ajaxForm({
        type: 'post',
        dataType: 'json',
        success: function (result) {

            if( result.message ) {
                if( result.message_type == 'alert' ) {
                    showToast(result.message);
                }
            }

            if($('input[name="cmt_table"]').val() == 'product'){
                if($('.product-tab li[data-set="product_comment"] em.no_font').length > 0){
                    $('.product-tab li[data-set="product_comment"] em.no_font').html(result.data.tot_cnt);
                }
            }

            if( result.status == status_code['success'] ) {
                comment_paging_ajax(true);
            }
        }
    });

});

function comment_paging_ajax(b = false){

    var obj_name    = $('input[name="obj_name"]').val();
    var p           = 1;
    var append      = true;

    if(b == false){
        p = $('input[name="comment_p"]').val();
        $('.more-wrap').remove();
    }

    if(b == true) append = false;

    $.ajax({
        url : '/comment/list_ajax',
        data : {page : p , cmt_num : cmt_num , append : append  ,  type : $('input[name="cmt_table"]').val() },
        type : 'post',
        async : false,
        dataType : 'html',
        success : function(result) {

            if(b == true) {

                $('.'+obj_name).html(result);

            } else {

                $('.'+obj_name).append(result);
                $('input[name="comment_p"]').val(parseInt(p) + 1);


                if($('.comment_area').length == parseInt( $('input[name="nCommentLists"]').val() )){
                    if($('.more-wrap').length > 0 )$('.more-wrap').remove();
                    if($('input[name="more"]').length > 0 )$('input[name="more"]').val(0);
                }

            }

        }

    });

}

function del_cmt(cmt_num){

    if(confirm('삭제하시겠습니까 ?') == true){

        $.ajax({
            url : '/comment/delete_proc',
            data : {cmt_num:cmt_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    comment_paging_ajax(true);
                }
            },
            complete : function() {
            }
        });

    }

}