"use strict";

function isOptionChk(b = true){

    var data;
    var ret             = true;
    var p_num           = empty($('input[name="p_num"]').val())==false?$('input[name="p_num"]').val():'';
    var option_token    = $('input[name="opt_token"]').val();
    var option_type     = $('input[name="p_option_type"]:checked').val();

    $.ajax({
        url : '/product/option',
        data : {p_num : p_num , option_token : option_token , type : option_type},
        type : 'post',
        dataType : 'json',
        async : false,
        success : function(result) {
            if(result.data.length > 0) ret = false
            data = result.data;
        }
    });

    if( ret == false ) {
        if(b == true) alert('해당상품의 옵션관련 정보를 변경하시려면 기존 옵션데이터 삭제 후 진행해주세요.');

        //초기화
        $('input[name="p_option_type"][value="'+data[0].option_type+'"]').prop('checked',true);
        $('input[name="p_option_depth"][value="'+data[0].option_depth+'"]').prop('checked',true);
    }

    return ret;

}

function openOptionSet(){

    var option_type     = $('input[name="p_option_type"]:checked').val();
    var option_depth    = $('input[name="p_option_depth"]:checked').val();
    var opt_token       = $('input[name="opt_token"]').val();
    var p_num           = empty($('input[name="p_num"]').val())==false?$('input[name="p_num"]').val():'';

    if(option_depth == undefined){
        alert('옵션 차수를 선택해주세요');
        return false;
    }

    var popup_path  = '/product/option_pop?depth='+option_depth;
    popup_path += '&p_num='+p_num;
    popup_path += '&view_type='+option_type;
    popup_path += '&opt_token='+opt_token;

    var container = $('<div class="option_wrap" style="max-height: 680px;overflow-y: auto;">');
    $(container).load(popup_path);

    modalPop.createPop('옵션설정', container);
    modalPop.createButton('설정', 'btn btn-primary btn-sm', function(){
        $('#pop_insert_form').submit();
    });
    modalPop.createCloseButton('닫기', 'btn btn-default btn-sm');
    modalPop.show({'dialog_class':'modal-xlg','backdrop' : 'static'});

}

$(function(){

    isOptionChk(false);

    $('input[name="p_option_use"]').on('change',function(){
        if($(this).val() == 'Y') $('.option_section').removeClass('hidden');
        else $('.option_section').addClass('hidden');
    });

    $('input[name="p_option_type"],input[name="p_option_depth"]').on('change',function(){
        if( isOptionChk() == true ){//변경가능

            if($(this).attr('name') == 'p_option_type'){
                var val = $(this).val();

                if(val == '11') $('input[name="p_option_depth"][value="2"]').prop('checked',true);
                else if(val == '111') $('input[name="p_option_depth"][value="3"]').prop('checked',true);
            }else{
                $('input[name="p_option_type"][value="basic"]').prop('checked',true);
            }
        }
    });

    // $('.openOptionSet').on('click',function(e){
    //     e.preventDefault()
    //
    //
    //
    // });

    $(document).on('change','#pop_insert_form input[type="file"]',function(){
        readURL(this);
    })

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if($(input).parent().parent().find('img').length > 0){
                    $(input).parent().parent().find('img').attr('src', e.target.result);
                }else{
                    $(input).parent().parent().find('.option_img .thumbnail').html('<img src="'+e.target.result+'" alt="" />');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }



    $(document).on('click','.option_group_del',function(e){
        e.preventDefault();

        var b = true;
        if($('#pop_insert_form table tbody tr').length < 2){
            if(confirm("현재 옵션이 1개입니다.\n삭제하시겠습니까?") == false) b = false;
        }

        if(b == true){
            if( $(this).parent().parent().hasClass('insert') == true ){
                $(this).parent().parent().remove();
            }else{//기 옵션데이터 삭제처리

                if(confirm("이미 저장된 옵션입니다.\n삭제 하시겠습니까?") == true){

                    $.ajax({
                        url: '/product/delete_option/',
                        data: {option_group_id : $(this).parent().parent().find('[name="option_group_id[]"]').val()},
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            if(result.msg) alert(result.msg);
                            if(result.success == true) get_option_page();
                        }

                    });

                }

            }
        }

    });

    $(document).on('click','.option_del',function(e){
        e.preventDefault();

        var b = true;
        if($('#pop_insert_form table tbody tr').length < 2){
            if(confirm("현재 옵션이 1개입니다.\n삭제하시겠습니까?") == false) b = false;
        }

        if(b == true){
            if( $(this).parent().parent().hasClass('insert') == true ){
                $(this).parent().parent().remove();
            }else{//기 옵션데이터 삭제처리

                if(confirm("이미 저장된 옵션입니다.\n삭제 하시겠습니까?") == true){

                    $.ajax({
                        url: '/product/delete_option/',
                        data: {option_id : $(this).parent().parent().find('[name="option_id[]"]').val()},
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            if(result.msg) alert(result.msg);
                            if(result.success == true) get_option_page();
                        }

                    });

                }

            }
        }
    });

});
