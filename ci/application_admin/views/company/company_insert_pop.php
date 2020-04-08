<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">제휴사명 <span class="txt-danger">*</span></label>
                            <div class="col-sm-9">
                                <div id="field_co_name">
                                    <input type="text" class="form-control" name="co_name" maxlength="50" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">제휴사아이디 <span class="txt-danger">*</span></label>
                            <div class="col-sm-9">
                                <div id="field_co_loginid">
                                    <input type="text" class="form-control" name="co_loginid" maxlength="50" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">제휴사비밀번호</label>
                            <div class="col-sm-9">
                                <div id="field_co_passwd">
                                    <input type="text" class="form-control" name="co_passwd" maxlength="50" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">제휴사URL</label>
                            <div class="col-sm-9">
                                <div class="input-group" id="field_co_url">
                                    <span class="input-group-addon">http://</span>
                                    <input type="text" class="form-control" name="co_url" maxlength="200" />
                                </div>
                            </div>
                        </div>
                        <div class="hidden"><button type="submit">등록완료</button></div>
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