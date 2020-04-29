<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="av_num" value="<?php echo $app_version_row->av_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">OS타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_av_os_type">
                                    <?php echo get_input_radio('av_os_type', $this->config->item('app_version_os_type'), $app_version_row->av_os_type); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">버전명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_av_version">
                                    <input type="text" class="form-control" name="av_version" maxlength="50" value="<?php echo $app_version_row->av_version; ?>" />
                                    <p class="help-block">* 입력예: 1.0.0</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">버전코드 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_av_version">
                                    <input type="text" class="form-control" name="av_version_code" maxlength="10" value="<?php echo $app_version_row->av_version_code; ?>" />
                                    <p class="help-block">* 입력예: 60</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제공방식 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_av_offer_type">
                                    <?php echo get_input_radio('av_offer_type', $this->config->item('app_version_offer_type'), $app_version_row->av_offer_type); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">설치URL <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_av_download_url">
                                    <input type="text" class="form-control" name="av_download_url" maxlength="200" value="<?php echo $app_version_row->av_download_url; ?>" />
                                    <p class="help-block">* Android 입력예 : <?=$app_download_url[1];?></p>
                                    <p class="help-block">* IOS 입력예 : <?=$app_download_url[2];?></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">작업내역</label>
                            <div class="col-sm-10">
                                <div id="field_av_content">
                                    <textarea class="form-control" rows="8" name="av_content"><?php echo $app_version_row->av_content; ?></textarea>
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
    var app_download_url_arr = <?=json_encode_no_slashes($app_download_url);?>;
    var form = '#pop_update_form';

    //document.ready
    $(function(){
        //os타입 변경식
        $('[name="av_os_type"]').on('change', function() {
            $('[name="av_download_url"]').val(app_download_url_arr[$(this).val()]);
        });

        //submit check
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