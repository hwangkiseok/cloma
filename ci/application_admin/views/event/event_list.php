<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">이벤트관리 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="div">이벤트종류</label>
                <div class="col-sm-10">
                    <select id="div" name="div" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("event_division"), ''); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="pro_state">노출여부</label>
                <div class="col-sm-10">
                    <select id="pro_state" name="pro_state" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("event_active_state"), ''); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="dis_state">진행여부</label>
                <div class="col-sm-10">
                    <select id="dis_state" name="dis_state" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("event_display_state"), $req['dis_state']); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="e_subject">제목</option>
                                <option value="e_content">내용</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" onclick="event_insert_pop();" style="width:100px;">등록</button>
                <!--<a href="--><?php //echo $this->page_link->insert; ?><!--/?--><?php //echo $GV; ?><!--" class="btn btn-info btn-sm mgl10" style="width:100px;">등록</a>-->
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
     * 이벤트 등록 팝업
     */
    function event_insert_pop() {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?' + $('#search_form').serialize());

        modalPop.createPop('이벤트 등록', container);
        modalPop.createButton('등록', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of event_insert_pop()

    /**
     * 이벤트 수정 팝업
     */
    function event_update_pop(e_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?e_num=' + e_num);

        modalPop.createPop('이벤트 수정', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg'});
    }//end of event_update_pop()

    /**
     * 이벤트 삭제
     */
    function event_delete(e_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {e_num:e_num},
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
    }//end of event_delete()

    /**
     * 이벤트 정보 수정 토글
     * @param p_num
     * @param fd
     */
    function event_update_toggle(e_num, fd) {
        if( empty(e_num) || empty(fd) ) {
            return false;
        }

        if( !confirm('상태를 변경하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/event/update_toggle',
            data : {e_num:e_num, fd:fd},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of event_update_toggle()

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