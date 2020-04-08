<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">관리자계정관리 > 수정</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="au_num" value="<?php echo $adminuser_row->au_num; ?>" />

                        <?php if( is_adminuser_high_auth() ){ ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">레벨 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div id="field_u_level">
                                    <?php echo get_input_radio('au_level', $this->config->item('adminuser_level'), $adminuser_row->au_level); ?>
                                </div>
                            </div>
                        </div>

                        <?php } ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이디</label>
                            <div class="col-sm-4">
                                <div class="form-control-static"><?php echo $adminuser_row->au_loginid; ?></div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">비밀번호</label>
                            <div class="col-sm-4">
                                <input type="password" id="field_au_password" name="au_password" class="form-control">
                            </div>
                            <span class="help-inline col-xs-12 col-sm-4">
                                <span class="middle txt-default">* 변경시에만 입력, 4~20자</span>
                            </span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">비밀번호 확인</label>
                            <div class="col-sm-4">
                                <input type="password" id="field_au_password_confirm" name="au_password_confirm" class="form-control">
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이름 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_name" name="au_name" class="form-control" value="<?php echo $adminuser_row->au_name; ?>">
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이메일</label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_email" name="au_email" class="form-control" value="<?php echo $adminuser_row->au_email; ?>">
                            </div>
                        <span class="help-inline col-xs-12 col-sm-2">
							<span class="middle txt-default">* 입력예 : test@a.com</span>
						</span>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">휴대폰</label>
                            <div class="col-sm-4">
                                <input type="text" id="field_au_mobile" name="au_mobile" class="form-control" value="<?php echo $adminuser_row->au_mobile; ?>" maxlength="20">
                            </div>
                        <span class="help-inline col-xs-12 col-sm-4">
							<span class="middle txt-default">* 입력예 : 010-0000-0000</span>
						</span>
                        </div>

                        <?php if( is_adminuser_high_auth() ){ ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">사용여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-4">
                                <div id="field_au_usestate">
                                    <?php echo get_input_radio('au_usestate', $this->config->item('adminuser_usestate'), $adminuser_row->au_usestate, $this->config->item('adminuser_usestate_text_color')); ?>
                                </div>
                            </div>
                        </div>

                        <?php } ?>

                        <div class="clearfix"></div>

                        <hr />

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                                <?php if( is_adminuser_high_auth() ) { ?>
                                <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary btn-sm">수정완료</button>
                                <?php if( is_adminuser_high_auth() ) { ?>
                                <a href="#none" class="btn btn-danger btn-sm mgl5 pull-right" onclick="adminuser_delete();">삭제</a>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * 삭제
     */
    function adminuser_delete() {
        if( confirm('삭제하시겠습니까?') ) {
            location.href = '<?php echo $this->page_link->delete_proc . "/?" . $this->input->server('QUERY_STRING'); ?>';
        }
    }//end of adminuser_delete()

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