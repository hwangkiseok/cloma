<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pf_name">
                                    <input type="text" class="form-control" name="pf_name" maxlength="100" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제안서 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pf_file_url">
                                    <input type="file" class="form-control" name="pf_file" />
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
        //datepicker
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