<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">관리자계정관리 > 등록</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">레벨 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div id="field_u_level">
                                    <?php echo get_input_radio('au_level', $this->config->item('adminuser_level'), 1); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이디 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_loginid" name="au_loginid" class="form-control" data-toggle="tooltip" data-placement="bottom" title="아이디">
                            </div>
                            <span class="help-inline col-xs-12 col-sm-2">
                                <span class="middle txt-default">* 영문, 숫자 4~20자</span>
                            </span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">비밀번호 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="password" id="field_au_password" name="au_password" class="form-control" data-toggle="tooltip" data-placement="bottom" title="비밀번호">
                            </div>
                            <span class="help-inline col-xs-12 col-sm-2">
                                <span class="middle txt-default">* 4~20자</span>
                            </span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">비밀번호 확인 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="password" id="field_au_password_confirm" name="au_password_confirm" class="form-control" data-toggle="tooltip" data-placement="bottom" title="비밀번호 확인">
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이름 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_name" name="au_name" class="form-control" data-toggle="tooltip" data-placement="bottom" title="이름">
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이메일</label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_email" name="au_email" class="form-control" data-toggle="tooltip" data-placement="bottom" title="이메일">
                            </div>
                        <span class="help-inline col-xs-12 col-sm-2">
							<span class="middle txt-default">* 입력예 : test@a.com</span>
						</span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">휴대폰</label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_mobile" name="au_mobile" class="form-control" data-toggle="tooltip" data-placement="bottom" title="휴대폰" maxlength="20">
                            </div>
                            <span class="help-inline col-xs-12 col-sm-4">
                                <span class="middle txt-default">* 입력예 : 010-0000-0000</span>
                            </span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">사용여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div id="field_au_usestate">
                                    <?php echo get_input_radio('au_usestate', $this->config->item('adminuser_usestate'), 'Y', $this->config->item('adminuser_usestate_text_color')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-6 col-xs-12">
                                <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                <button type="submit" class="btn btn-primary btn-sm">등록완료</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        //Ajax Form
        $('#main_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(res) {
                if( res.message ) {
                    if( res.message_type == 'alert' ) {
                        alert(res.message);
                    }
                }

                if( res.status == '<?php echo get_status_code('success'); ?>' ) {
                    location.replace('<?php echo $list_url; ?>');
                }
                else {
                    if( res.error_data ) {
                        $.each(res.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            }
        });//end of ajax_form()
    });
</script>