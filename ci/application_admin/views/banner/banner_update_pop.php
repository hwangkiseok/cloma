<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="bn_num" value="<?php echo $banner_row->bn_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배너종류 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bn_division">
                                    <select name="bn_division" class="form-control" style="width:auto;">
                                        <?php echo get_select_option('', $this->config->item('banner_division'), $banner_row->bn_division); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">활성상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bn_usestate">
                                    <?php echo get_input_radio('bn_usestate', $this->config->item('banner_usestate'), $banner_row->bn_usestate); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bn_subject">
                                    <input type="text" class="form-control" name="bn_subject" maxlength="100" value="<?php echo $banner_row->bn_subject; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bn_image">
                                    <input type="file" class="form-control" name="bn_image" />
                                </div>
                                <div class="mgt10">
                                    <?php echo create_img_tag_from_json($banner_row->bn_image, 1, 100); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공개기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bn_termlimit_yn">
                                    <?php echo get_input_radio('bn_termlimit_yn', $this->config->item('banner_termlimit_yn'), $banner_row->bn_termlimit_yn); ?>
                                </div>
                                <div id="field_bn_termlimit_date" style="display:none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="bn_termlimit_datetime1" value="<?php echo get_date_format($banner_row->bn_termlimit_datetime1); ?>" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="bn_termlimit_datetime2" value="<?php echo get_date_format($banner_row->bn_termlimit_datetime2); ?>" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이동URL</label>
                            <div class="col-sm-10">
                                <div class="input-group" id="field_bn_target_url">
                                    <span class="input-group-addon">http://</span>
                                    <input type="text" class="form-control" name="bn_target_url" maxlength="200" value="<?php echo str_replace("http://", "", $banner_row->bn_target_url); ?>" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>
    var form = '#pop_update_form';

    //document.ready
    $(function(){
        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        //bn_termlimit_yn click
        $(form + ' [name="bn_termlimit_yn"]').on('click', function(){
            if( $(this).val() == 'Y' ) {
                $('#field_bn_termlimit_date').show();
            }
            else {
                $('#field_bn_termlimit_date').hide();
            }
        });

        $('#bn_termlimit_yn_<?php echo $banner_row->bn_termlimit_yn; ?>').trigger('click');

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
            //beforeSubmit: function(formData, jqFor, options) {
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