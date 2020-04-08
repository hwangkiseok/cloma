<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">기프티콘관리 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="div">이벤트</label>
                <div class="col-sm-10">
                    <select id="div" name="div" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $event_option_array, ""); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="dis_state">상태</label>
                <div class="col-sm-10">
                    <select id="state" name="state" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("event_gift_state"), $req['state']); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="all">회원/핀번호</option>
                                <option value="eg_event_ph">휴대폰번호</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="event_gift_insert_pop();" style="width:100px;margin-right:10px;">등록</button>
                &nbsp;|&nbsp;
                <button type="button" class="btn btn-warning btn-sm" onclick="event_gift_issue_pop();" style="width:100px;margin-left:10px;">발급</button>
                &nbsp;|&nbsp;
                <button type="button" class="btn btn-success btn-sm" onclick="event_gift_code_insert_pop();" style="width:100px;margin-left:10px;">기프티콘 등록</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="event_list" style="position:relative;"></div>
    </div>
</div>

<script>
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


    /**
     * 이벤트 기프티콘 코드 등록 팝업
     */
    function event_gift_code_insert_pop() {
        var container = $('<div>');
        $(container).load('/event_gift/code_insert');

        modalPop.createPop('기프티콘 코드 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#gift_code_update_form').submit();
            modalPop.hide();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        //modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});
        modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});
    }//end of event_gift_code_insert_pop()


    /**
     * 이벤트 등록 팝업
     */
    function event_gift_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('기프티콘 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});
    }//end of event_gift_insert_pop()

    /**
     * 이벤트 수정 팝업
     */
    function event_gift_update_pop(eg_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?eg_num=' + eg_num);

        modalPop.createPop('기프티콘 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});
    }//end of event_gift_update_pop()

    /**
     * 이벤트 기프티콘 삭제
     */
    function event_gift_delete(eg_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {eg_num:eg_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of event_gift_delete()

    /**
     * 기프티콘 발급 팝업
     */
    function event_gift_issue_pop() {
        var container = $('<div>');
        $(container).load('/event_gift/issue_pop');

        modalPop.createPop('기프티콘 발급', container);
        //modalPop.createButton('발급', 'btn btn-success btn-sm', function(){
        //    $('#pop_form').submit();
        //});
        modalPop.createCloseButton('닫기', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});
    }//end of event_gift_issue_pop()

    /**
     * 기프티콘 발급 처리
     */
    function event_gift_issue_proc(eg_event_num, eg_event_ym,eg_event_gift) {
        if( !confirm('해당 기프티콘을 회원들에게 발급하시겠습니까?') ) {
            return false;
        }

        loadingScreen.show();
        Pace.restart();

        $.ajax({
            url : '/event_gift/issue_proc',
            data : {eg_event_num:eg_event_num, eg_event_ym:eg_event_ym, eg_event_gift : eg_event_gift},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    //location.reload();
                    $('#search_form').submit();
                    modalPop.hide();
                }
            },
            complete : function() {
                loadingScreen.hide();
                Pace.stop();
            }
        });
    }//end of event_gift_issue_proc()

    /**
     * 전체선택
     */
    function all_check_click() {
        var ck = $('[name="all_check"]').prop('checked');

        $.each($('[name="cknum[]"]'), function(){
            $(this).prop('checked', ck);
        });
    }//end of all_check_click()

    $(document).on('click','.addGifticon',function(e){

        e.preventDefault();

        $.ajax({
            url: '/event_gift/addGiftCode',
            data: {e_code : $('select[name="event_code"]').val()},
            type: 'post',
            dataType: 'json',
            success: function (result) {
                if(result.msg) alert(result.msg);
                setGifticonList();
            }
        })

    });


    $(document).on('click','.copyGifticon',function(e){

        e.preventDefault();

        $.ajax({
            url: '/event_gift/copyGiftCode',
            data: {e_code : $('select[name="event_code"]').val()},
            type: 'post',
            dataType: 'json',
            success: function (result) {
                if(result.msg) alert(result.msg);
                setGifticonList();
            }
        })

    });


    $(document).on('click','.delGifticon', function(e){
        e.preventDefault();


        var cf = confirm('선택한 기프티콘 코드를 삭제하시겠습니까?');
        if(cf == false) return false;

        var seq = $(this).parent().data('seq');

        $.ajax({
            url: '/event_gift/delGiftCode',
            data: {seq : seq},
            type: 'post',
            dataType: 'json',
            success: function (result) {
                if(result.msg) alert(result.msg);
                setGifticonList();
            }
        })

    });

    //document.ready
    $(function () {
        $('#div').on('change', function(){
            $('#search_form').submit();
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        $('#search_form').ajaxForm({
            //url : '<?php //echo $this->page_link->list_ajax; ?>//',
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#event_list'));
            },
            success: function(result) {
                $('#event_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#event_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready
</script>