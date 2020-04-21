<?php
/*
<!--로그인wrap 시작-->
<div class="box">
    <div class="box-in">
    <!--sns간편로그인-->
        <div class="login_sns">
            <h3><span>SNS 간편로그인</span></h3>

            <a href="#none" class="n" onclick="go_link('<?=$NAVER_REQUEST_URL?>');"><img src="<?=IMG_HTTP?>/images/naver_login.png" alt=""></a><br>
            <a href="#none" class="k" onclick="go_link('<?=$KAKAO_REQUEST_URL?>');"><img src="<?=IMG_HTTP?>/images/kakao_login.png" alt=""></a><br>
            <a href="#none" class="g" onclick="go_link('<?=$GOOGLE_REQUEST_URL?>');"><img src="<?=IMG_HTTP?>/images/google_login.png" alt=""></a>

<!--        <a href="#none" class="sns_log0GOOGLE_REQUEST_URL2" onclick="go_kakao_sync();">카카오로 시작하기</a>-->
<!--        <a href="#none" class="f" onclick="go_link('<?=$FACEBOOK_REQUEST_URL?>');">페이스북으로 시작하기</a>-->

        </div>
    <!--sns간편로그인-->
    </div>
</div>
*/
?>

<style>

    .loc-join-wrap {padding: 20px;}
    .loc-join-wrap label{ width: 100%;height: 100%; display: inline-block; margin-bottom: 5px; }
    .loc-join-wrap label span{display: inline-block;width: 20%;float: left;vertical-align: middle;line-height: 36px;text-align: center;}
    .loc-join-wrap label input{padding: 8px; width: 80%;float: left;}


</style>

<div class="loc-join-wrap" style="padding: 20px;">

    <label><span>아이디</span><input type="text" name="login_id" /></label>
    <label><span>비밀번호</span><input type="password" name="login_pw" /></label>
    <div class="clear"></div>

    <div style="text-align: center">
        <button style="width: 100%;margin-bottom: 5px;" class="btn btn-border-purple chkLogin">로그인</button>
    </div>
    <hr>

    <div style="text-align: center">
        <button style="width: 100%;" class="btn btn-border-blue" onclick="go_link('/member/loc_join_form')">회원가입</button>
    </div>

</div>

<script type="text/javascript">

    $(function(){

        $('.chkLogin').on('click',function(e){
            e.preventDefault();

            if($('input[name="login_id"]').val() == '' || $('input[name="login_pw"]').val() == ''){
                alert('아이디 또는 비밀번호를 입력해주세요');
                return false;
            }

            $.ajax({
                url : '/auth/chk_login',
                data : {login_id : $('input[name="login_id"]').val() , login_pw : $('input[name="login_pw"]').val()},
                type : 'post',
                dataType : 'json',
                success : function (result) {
                    if( result.status == '<?php echo get_status_code('success'); ?>' ) {
                        go_home();
                    }else{
                        alert('계정정보를 확인해주세요!');
                    }
                }
            });

        })

    });

</script>



