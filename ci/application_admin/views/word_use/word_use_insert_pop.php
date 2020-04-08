<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                        <?php
                        if(isset($word_use_row->wd_num) == true && $word_use_row->wd_num != '') {
                            ?>
                            <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                            <input type="hidden" id="field_wd_num" name="wd_num" value="<?php echo $word_use_row->wd_num; ?>" />
                            <?php
                        } else {
                        ?>
                            <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <?php
                        }
                        ?>
                        <p style="color:red"> 내용 작성시 %%%은 자신의 닉네임과 치환 됩니다.</p>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">사용처 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <select id="field_wd_use" name="wd_use" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                    <?php
                                    foreach ($this->config->item('wd_use') as $key => $value) {
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php if($word_use_row->wd_use == $key) echo 'selected'; ?>><?php echo $value; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <select id="field_wd_view" name="wd_view" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                    <option value="Y">노출</option>
                                    <option value="N" <?php if($word_use_row->wd_view == 'N') echo 'selected'; ?>>비노출</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field_wd_subject" name="wd_subject" maxlength="50" value="<?php if(isset($word_use_row->wd_subject) == true) echo $word_use_row->wd_subject; ?>" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">내용 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="field_wd_content" name="wd_content" style="height: 200px"><?php if(isset($word_use_row->wd_content) == true) echo $word_use_row->wd_content; ?></textarea>
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
        $(form).on('submit', function(){
            info_message_all_clear();
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
                        console.log(result.error_data);
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