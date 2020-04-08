<style>
    .list-group { max-height:280px; overflow-y:auto; }
    .list-group-item { padding:5px; }
    .list-group-item .row { margin:0; padding:5px; }
    .list-group-item .row p { margin:0; }
    #product_list_group .left { padding-right:5px; }
    #product_list_group .right { padding-left:5px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="ed_num" value="<?php echo $everyday_row->ed_num; ?>" />
                        <input type="hidden" name="ed_product_num" value="<?php echo $everyday_row->ed_product_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진행상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_usestate">
                                    <?php echo get_input_radio('ed_usestate', $this->config->item('everyday_usestate'), $everyday_row->ed_usestate); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_displaystate">
                                    <?php echo get_input_radio('ed_displaystate', $this->config->item('everyday_displaystate'), $everyday_row->ed_displaystate); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">당첨인원 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_winner_count">
                                    <input type="text" class="form-control" name="ed_winner_count" style="width:100px;" maxlength="5" numberOnly="true" value="<?php echo $everyday_row->ed_winner_count; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품정보</label>
                            <div class="col-sm-10">
                                <p><?php echo create_img_tag_from_json($everyday_row->p_rep_image, 1, 100); ?></p>
                                <p><b><?php echo $everyday_row->p_name; ?></b></p>
                                <p><?php echo get_date_format($everyday_row->p_termlimit_datetime1); ?> ~ <?php echo get_date_format($everyday_row->enddate); ?></p>
                                <p><?php echo get_config_item_text($everyday_row->p_sale_state, "product_sale_state"); ?> / <?php echo get_config_item_text($everyday_row->p_display_state, "product_display_state"); ?></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var form = '#pop_update_form';

    //document.ready
    $(function(){
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
            beforeSubmit: function(formData, jqForm, options) {
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
    });//end of document.ready()
</script>