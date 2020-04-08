
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" ></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css"/>

<style>
    .coupon-info-alert:first-child{margin-top: 5px;}
    .coupon-info-alert {padding: 10px 15px;margin-bottom: 5px}
    .coupon-info-alert span {display: inline-block;min-width: 250px;}
</style>


<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">쿠폰관리 > 목록</h4>
    </div>

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="/Coupon/get_coupon_lists_ajax/" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <?/*
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">일자검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" class="form-control" style="width:auto;">
                            <option value="reg_date">등록일</option>
                            <option value="start_date">노출시작일</option>
                            <option value="end_date">노출종료일</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="set_date_term();">오늘</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="clear_date_term();">전체</button>
                </div>
            </div>


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label hidden-xs"></label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
*/?>
            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-success btn-sm mgl10" style="width:100px;" onclick="location.href=''">전체보기</button>
                <button type="button" class="btn btn-info btn-sm mgl10 coupon_regist" style="width:100px;">쿠폰정보 등록</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-4 pull-left">
                <?if($condition == 'Y'){?>
                    알림톡 상태 : <button class="btn btn-danger stop-send-proc btn-xs">발송가능</button>
                <?}else{?>
                    알림톡 상태 : <span class="text-danger"><b>발송중단</b></span>
                <?}?>

            </div>
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="coupon_list" style="position:relative;"></div>
    </div>
</div>

<script type="text/javascript">


    $(document).on('click','.coupon-row-del',function(e){
        e.preventDefault();
        $(this).parent().remove();
        if( $('input[name="coupon_info[]"]').length < 1 ) html += $('#field_coupon_select_lists').html('');
    });


    $(document).on('click','.coupon_update',function(e){
        e.preventDefault();

        var seq = $(this).data('seq');
        var container = $('<div>');
        $(container).load('/coupon/upsert_pop/?m=update&seq='+seq);

        modalPop.createPop('쿠폰 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#coupon_frm').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});

    });

    $(document).on('click','.coupon_delete',function(e){
        e.preventDefault();

        var cf_msg = '해당 쿠폰을 삭제하시겠습니까 ?';
        var seq = $(this).data('seq');
        var cf = confirm(cf_msg);

        if(cf == true) {

            $.ajax({
                url: '/coupon/coupon_delete_proc',
                data: {seq : seq},
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if( result.message ) {
                        if( result.message_type == 'alert' ) {
                            alert(result.message);
                        }
                    }
                    if( result.status == status_code['success'] ) {
                        $('#search_form').submit();
                    }
                }
            });

        }

    });
    
    $(document).on('click','.coupon_activate',function(e){
        e.preventDefault();

        var flag    = $(this).data('flag');
        var seq     = $(this).data('seq');
        var cf_msg  = '';

        if(flag == 'N') cf_msg = '해당 쿠폰을 활성화 시키시겠습니까 ?';
        else cf_msg = '해당 쿠폰을 비활성화 시키시겠습니까 ?';

        var cf = confirm(cf_msg);

        if(cf == true) {

            $.ajax({
                url: '/coupon/coupon_set_activate',
                data: {flag : flag, seq : seq},
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if( result.message ) {
                        if( result.message_type == 'alert' ) {
                            alert(result.message);
                        }
                    }
                    if( result.status == status_code['success'] ) {
                        $('#search_form').submit();
                    }
                }
            });
        }
    });

    $(document).on('click','.send_alimtalk',function(e){
        e.preventDefault();

        var seq = $(this).data('seq');
        var code = $(this).data('code');

        var container = $('<div></div>');
        var html  = '<label><input type="radio" name="uniq_pop_no" value="m_authno"> 인증연락처</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            html += '<label><input type="radio" name="uniq_pop_no" value="m_order_phone"> 주문연락처</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            html += '<label><input type="radio" name="uniq_pop_no" value="non_members"> 비회원 주문자</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            html += '<label><input type="radio" name="uniq_pop_no" value="kakao_sync"> 카카오싱크</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            html += '<label><input type="radio" name="uniq_pop_no" value="test"> 테스트유저</label>';

        $(container).append(html);

        modalPop.createPop("발송 연락처 선택", container);

        modalPop.createButton('발송', 'btn btn-primary btn-sm', function(){

            <? if(1){ ?>
            SendAlimtalk_exec_v2(seq,code);
            <? } else { ?>
            SendAlimtalk_exec(seq,code);
            <? } ?>


        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });


    function SendAlimtalk_exec_v2(seq,code){

        var send_type = $(document).find('input[name="uniq_pop_no"]:checked').val();

        if(send_type == undefined || send_type == ''){
            alert('발송 연락처를 선택해주세요 !');
            $(document).find('input[name="uniq_pop_no"]').focus();
            return false;
        }

        var cf_msg = '알림톡을 발송하시겠습니까 ?';
        var cf = confirm(cf_msg);

        if(cf == true) {

            $.ajax({
                url: '/coupon/alimtalk_send_proc_v2',
                data: {seq : seq,code : code,send_type:send_type},
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if( result.message ) {
                        if( result.message_type == 'alert' ) {
                            alert(result.message);
                        }
                    }
                    if( result.status == status_code['success'] ) {
                        modalPop.hide();
                        $('#search_form').submit();
                    }
                }
            });

        }

    }


    function SendAlimtalk_exec(seq,code){

        var send_type = $(document).find('input[name="uniq_pop_no"]:checked').val();

        if(send_type == undefined || send_type == ''){
            alert('발송 연락처를 선택해주세요 !');
            $(document).find('input[name="uniq_pop_no"]').focus();
            return false;
        }

        var cf_msg = '알림톡을 발송하시겠습니까 ?';
        var cf = confirm(cf_msg);

        if(cf == true) {

            $.ajax({
                url: '/coupon/alimtalk_send_proc',
                data: {seq : seq,code : code,send_type:send_type},
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if( result.message ) {
                        if( result.message_type == 'alert' ) {
                            alert(result.message);
                        }
                    }
                    if( result.status == status_code['success'] ) {
                        modalPop.hide();
                        $('#search_form').submit();
                    }
                }
            });

        }

    }
    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    //document.ready
    $(function () {

        $('.stop-send-proc').on('click',function(e){
            e.preventDefault();
           alert('정책준비 중입니다.');
        });
        $('.coupon_regist').on('click',function(){

            var container = $('<div>');
            $(container).load('/coupon/upsert_pop/?m=insert');

            modalPop.createPop('쿠폰 등록', container);
            modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
                $('#coupon_frm').submit();
            });
            modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
            modalPop.show({'dialog_class':'modal-lg'});

        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#coupon_list'));
                },
                success: function(result) {
                    $('#coupon_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajax_form()

            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#coupon_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()

    });//end of document.ready

</script>
