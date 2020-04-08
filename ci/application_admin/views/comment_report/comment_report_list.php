<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">댓글신고 > 목록</h4>
    </div>

    <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />
        <input type="hidden" name="cmt_num" value="<?php echo $req['cmt_num']; ?>" />

        <div class="row" id="search_wrap">
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="all">전체</option>
                                <option value="rp_reason">신고이유</option>
                                <option value="cmt_content">댓글내용</option>
                                <option value="CM.m_nickname">댓글작성자</option>
                                <option value="RM.m_nickname">신고자</option>
                            </select>
                            <script>selected_check($('#kfd'), '<?php echo $req['kdf']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>
        </div>

    </form>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="comment_list" style="position:relative;"></div>
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
     * 댓글 등록 팝업
     */
    function comment_insert_pop(tb, tb_num) {
        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/?tb=' + tb + '&tb_num=' + tb_num);

        modalPop.createPop('댓글 등록', container);
        modalPop.createButton('등록완료', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of comment_insert_pop()

    /**
     * 댓글 수정 팝업
     */
    function comment_update_pop(cmt_num) {
        if( empty(cmt_num) ) {
            alert('댓글을 선택하세요.');
            return false;
        }

        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?cmt_num=' + cmt_num);

        modalPop.createPop('댓글 수정', container);
        modalPop.createButton('삭제하기', 'btn btn-danger btn-sm pull-left', function(){
            comment_delete(cmt_num);
        });
        modalPop.createButton('수정완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of comment_update_pop()

    /**
     * 댓글 삭제
     */
    function comment_delete(cmt_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
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
                    $('#search_form').submit();
                    modalPop.hide();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of comment_delete()


    //document.ready
    $(function () {
        <?php if( !empty($req['pop']) ) { ?>
        $('#search_wrap').hide();
        $('body').css({'background':'#fff'});
        <?php } ?>


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
                loadingBar.show($('#comment_list'));
            },
            success: function(result) {
                $('#comment_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#comment_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//end of document.ready
</script>