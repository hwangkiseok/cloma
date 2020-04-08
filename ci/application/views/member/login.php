<!--로그인wrap 시작-->
<div id="login_wrap">

    <!--sns간편로그인-->
    <div class="login_sns">
        <h3><span>SNS 간편로그인</span></h3>
        <a href="#none" class="sns_log01" onclick="go_link('<?=$NAVER_REQUEST_URL?>');">네이버로 시작하기</a>
        <a href="#none" class="sns_log02" onclick="go_link('<?=$KAKAO_REQUEST_URL?>');">카카오로 시작하기</a>
<!--        <a href="#none" class="sns_log02" onclick="go_kakao_sync();">카카오로 시작하기</a>-->
        <a href="#none" class="sns_log03" onclick="go_link('<?=$FACEBOOK_REQUEST_URL?>');">페이스북으로 시작하기</a>

    </div>
    <!--sns간편로그인-->

</div>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>


<!--로그인wrap-->
<script type="text/javascript">

</script>