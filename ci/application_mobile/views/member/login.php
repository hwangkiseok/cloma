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

    #container { width:100%; height:100%; margin:0 auto;}
    #container .join-wrap { position:fixed; width:100%; height:100%; left:0; top:0; overflow:hidden;padding: 0;background-color: #fff; }
    #container .join-wrap .join_bg {background:url('<?=IMG_HTTP?>/images/login_bg.png') no-repeat top center; background-size:cover;max-width: 580px;height: 100%;margin: 0 auto;}
    #container .join-wrap .join_content { position:absolute; bottom:0; text-align:center;  margin-bottom:60px; background-color: #fff;width: 100%;max-width: 580px}
    #container .join-wrap .btn_area li {margin-bottom: 10px;}
    #container .join-wrap .btn_area button { font-size: 15px; color: #777; border: none;background: #fff; width: 80%;padding: 15px 0;position: relative;border-radius: 5px;}
    #container .join-wrap .btn_area button.n {background-color: #1EC800;color: #FFFFFF}
    #container .join-wrap .btn_area button.k {background-color: #fae000;color: #411b1b}
    #container .join-wrap .btn_area button.g {background-color: #ececec;color: #000000}

    #container .join-wrap .btn_area button i { position: absolute; left: 20px; top:5px; display: inline-block; width: 35px; height: 35px; background: url('http://www.cloma.co.kr/images/mb_icon_set_img.png') no-repeat; background-size: 130px!important; }
    #container .join-wrap .btn_area button.n i {background-position: -63px -1px}
    #container .join-wrap .btn_area button.k i {background-position: -93px -1px}
    #container .join-wrap .btn_area button.g i {background-position: -63px -31px}


</style>

<div class="join-wrap">

    <div class="join_bg">

        <div class="join_content">
            <div class="btn_area">
                <ul>
                    <li><button class="n" onclick="go_link('<?=$NAVER_REQUEST_URL?>');"><i></i>네이버로 시작하기</button></li>
                    <li><button class="k" onclick="go_link('<?=$KAKAO_REQUEST_URL?>');"><i></i>카카오로 시작하기</button></li>
                    <li><button class="g" onclick="go_link('<?=$GOOGLE_REQUEST_URL?>');"><i></i>구글로 시작하기</button></li>
                </ul>
            </div>
        </div>

    </div>
</div>

