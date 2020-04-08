<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <form role="form" name="login_form" id="login_form" method="post" action="/auth/login_proc">
                        <fieldset>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                    <input class="form-control" placeholder="UserId" id="field_user_id" name="user_id"  maxlength="30" type="text" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                                    <input class="form-control" placeholder="Password" id="field_user_pw" name="user_pw" maxlength="30" type="password" value="">
                                </div>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" class="btn btn-lg btn-success btn-block" value="Login" />
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //document.ready
    $(function(){
        //ajax form
        $('#login_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(){
                if( empty($('#field_user_id').val()) ) {
                    error_message($('#field_user_id').parent(), '아이디를 입력하세요.');
                    return false;
                }
                if( empty($('#field_user_pw').val()) ) {
                    error_message($('#field_user_pw').parent(), '비밀번호를 입력하세요.');
                    return false;
                }
            },
            success: function(result){
                if( !empty(result.message) && result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == '<?php echo get_status_code('success');?>' ){
                    location.href = '/';
                }
            },
            error: function(){
                alert("Submit Error");
            }
        });
    });//end of document.ready
</script>