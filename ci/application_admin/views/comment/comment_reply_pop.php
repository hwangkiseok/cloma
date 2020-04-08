<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="/comment/reply_proc">
                        <input type="hidden" name="cmt_num" value="<?php echo $req['cmt_num']; ?>" />
                        <input type="hidden" name="cmt_flag" id="cmt_flag" value="C" />

                        <div class="form-group form-group-sm" id="cmt_table_wrap">
                            <label class="col-sm-2 control-label">문구선택 </label>
                            <div class="col-sm-10 form-inline">
                                <?php
                                foreach ($word_use_list as $key => $value) {
                                    if($value->wd_view == 'Y') {
                                        ?>
                                        <button type="button" class="btn btn-default btn-sm"
                                                value="<?php echo htmlspecialchars(str_replace("%%%", $_SESSION['GroupMemberNick'], $value->wd_content)); ?>"
                                                onclick="insertAtCaret('field_cmt_content', this.value, '')"><?php echo $value->wd_subject; ?></button>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">Push</label>
                            <div class="col-sm-10">
                                <div  class="form-control-static">
                                    <label for="send_push"> <input name="send_push" id="send_push" value="Y" type="checkbox">&nbsp;보내기 </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">내용 </label>
                            <div class="col-sm-10">
                                <div class="form-control-static"><?php echo $comment_row['cmt_content']; ?></div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">답댓글 <span class="txt-danger">*</span></label>
                            <div class="form_group">
                                <div class="col-sm-10">
                                <textarea title="답댓글" name="cmt_content" id="field_cmt_content" class="form-control-uniq" style="width:100%;height:120px;"><?=$comment_row['cmt_answer']?></textarea>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var form = '#pop_insert_form';

    //document.ready
    $(function(){
        ////submit check
        //$(form).on('submit', function(){
        //});

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                info_message_all_clear();

                var ta_cnt = $(".form-control-uniq").length;
                if( !$('#field_cmt_content').val() ) {
                    alert('댓글 내용을 입력하세요.');
                    return false;
                }

                Pace.restart();
            },
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( result.error_data ) {
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
                Pace.stop();
            }
        });//end of ajax_form()


        $(".delete_reply").click(function () {

            var cmt_num = $(this).data('cmtNum');

            if( !confirm('삭제하시겠습니까?') ) {
                return false;
            }

            Pace.restart();

            $.ajax({
                url : '<?php echo $this->page_link->delete_proc; ?>',
                data : { cmt_num : cmt_num },
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

        });

    });//end of document.ready()

    // textarea 커서 위치에 text 삽입
    function insertAtCaret(areaId, text) {
        if(text != '') {
            text = text + '\n';

            var txtarea = document.getElementById(areaId);
            if (!txtarea) {
                return;
            }

            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br == "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br == "ff") {
                strPos = txtarea.selectionStart;
            }

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;
            if (br == "ie") {
                txtarea.focus();
                var ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }
    }
</script>