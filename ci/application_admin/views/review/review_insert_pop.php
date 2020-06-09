<style>
    table tr.on { background:#FFF0F0; }
    table tr.on:hover { background:#FFF0F0; }
    table tr.on:focus { background:#FFF0F0; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="re_table_num" value="<?php echo $req['tb_num']; ?>" />
                        <input type="hidden" name="re_profile_img" value="" />
                        <input type="hidden" name="re_table" value="<?=$req['tb']?>" />
                        <div id="table_item_list" style="display:none;height:372px;overflow:auto;border:1px solid #ccc;margin-bottom:20px;">
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">작성자 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_re_name" name="re_name" class="form-control" style="width:200px;" value="<?//php echo $this->config->item("admin_name"); ?>" />
                                <p class="alert alert-danger" style="position: absolute;left: 240px;top: 0; display: inline-block;vertical-align: top;padding:  5px 10px!important;">주의 : 댓글작업시 작성자 확인요망(옷쟁이들으로 적으면 안됨!)</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_display_state">
                                    <?php echo get_input_radio("re_display_state", $this->config->item("comment_display_state"), "Y", $this->config->item("comment_display_state_text_color")); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품상세노출 여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_recommend">
                                    <label>
                                        <input type="radio" name="re_recommend" value="Y" >노출&nbsp;
                                    </label>
                                    <label style="color: red">
                                        <input type="radio" name="re_recommend" value="N" checked >노출안함
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label"> 상품만족도 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_grade">
                                    <label>
                                        <input type="radio" name="re_grade" value="A" checked>완전 추천해요!
                                    </label>
                                    <label>
                                        <input type="radio" name="re_grade" value="B">추천해요!&nbsp;
                                    </label>
                                    <label>
                                        <input type="radio" name="re_grade" value="C">아쉬워요
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지첨부</label>
                            <div class="col-sm-10">
                                <div id="field_re_display_state">
                                    <input type="file" name="review_file[]" multiple value="찾아보기" accept="image/*" >
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글내용 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="re_content" id="field_re_content" class="form-control" style="width:100%;height:100px;"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>

<script>
    var form = '#pop_insert_form';

    //document.ready
    $(function(){

        //submit check
        $(form).on('submit', function(){

            info_message_all_clear();

            if( !$(form + ' [name="re_table"]').val() ) {
                alert('구분을 선택하세요.');
                return false;
            }
            if( !$(form + ' [name="re_table_num"]').val() ) {
                alert('댓글을 입력할 대상을 선택하세요.');
                return false;
            }

            if( $(form + ' [name="re_name"]').val() == '옷쟁이들' ) {
                if(confirm('작업 댓글의 작성자는 "옷쟁이들" 일수 없습니다.\n등록을 진행하시겠습니까? ') == false){
                    return false;
                }
            }


            if( !$(form + ' [name="re_content"]').val() ) {
                alert('댓글 내용을 입력하세요.');
                return false;
            }

            Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            //beforeSubmit: function(formData, jqForm, options) {
            //},
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
    });//end of document.ready()
</script>