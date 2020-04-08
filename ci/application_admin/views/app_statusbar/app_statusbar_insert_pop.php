<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">색상코드 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_asb_color" class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" name="asb_color" class="form-control" maxlength="6" />
                                </div>
                                <p class="help-block">* 색상코드(6자리)를 "FF0000" 형식으로 입력하세요.</p>
                                <p class="text-danger"><i class="fa fa-warning"></i> FFFFFF 설정시 텍스트가 보이지 않습니다.</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">사용여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_asb_usestate">
                                    <?php echo get_input_radio('asb_usestate', $this->config->item('app_statusbar_usestate'), 'Y', $this->config->item('app_statusbar_usestate_text_color')); ?>
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
        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                info_message_all_clear();
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
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>