<aside id="lnb_area">

    <div class="lnb">

        <div class="lnb_top">

            <? if(empty($aMemberInfo['m_sns_profile_img']) == false){  ?>
                <style>
                    .lnb .prof_img span.user-img{background: url(<?=$aMemberInfo['m_sns_profile_img']?>) no-repeat center;border-radius: 100%;background-size: 45px;}
                </style>
                <div class="fl prof_img" style="background: inherit">
                    <span class="user-img"></span>
                </div>
            <?} else {?>
                <div class="fl prof_img">
                    <span class="empty-user"></span>
                </div>
            <?} ?>

            <?if(member_login_status() == true){   ?>

                <div class="fl nick_name">
                    <a style="color:#fff;"><?=$aMemberInfo['m_nickname']?>님<br>안녕하세요!</a>
                </div>

                <div class="fr cont_side">
                    <ul>
                        <li style="text-align: right;height: 30px;"> <i onclick="side_close('l');" role="button" class="lnb-close" ></i> </li>
                        <li style="margin-top: 6px;"><a href="#none" onclick="go_link('/auth/logout')" class="lnb-btn">로그아웃</a></li>
                    </ul>
                </div>

            <?}else{?>

                <div class="fl nick_name">
                    <a href="#none" onclick="go_link('/member')" style="color:#fff;border-bottom: 1px solid #ddd; padding-bottom: 2px;line-height: 40px;">로그인이 필요합니다.</a>
                </div>

                <div class="fr cont_side">
                    <ul>
                        <li style="text-align: right;height: 30px;"><i onclick="side_close('l');" role="button" class="lnb-close"></i></li>
                        <li style="margin-top: 6px;"> <a href="#none" onclick="go_link('/member')" class="lnb-btn">로그인</a> </li>
                    </ul>
                </div>

            <?}?>

        </div>

        <nav class="box lnb-sub-nav">
            <div class="box-in">
                <ul>
                    <li class="fl">
                        <a href="#none" onclick="go_link('/delivery')">
                            <span class="icon lnb-delivery"></span>
                            <span class="txt">주문조회</span>
                        </a>
                    </li>
                    <li class="fl">
                        <a href="#none" onclick="go_link('/cart')">
                            <span class="icon lnb-cart"></span>
                            <span class="txt">장바구니</span>
                        </a>
                    </li>
                    <li class="fl">
                        <a href="#none" onclick="go_link('/wish')">
                            <span class="icon lnb-wish"></span>
                            <span class="txt">찜한상품</span>
                        </a>
                    </li>
                    <li class="fl">
                        <a href="#none" onclick="go_link('/comment')">
                            <span class="icon lnb-comment"></span>
                            <span class="txt">댓글</span>
                        </a>
                    </li>
                    <li class="fl">
                        <a href="#none" onclick="go_link('/recently')">
                            <?if(empty($aRecentlyProduct) == false){?>
                                <!--<span class="icon"><img src="<?=$aRecentlyProduct['p_today_image']?>" alt="<?=$aRecentlyProduct['p_name']?>" style="width: 100%;margin-top: 1px;"  /> </span>-->
                                <span class="icon lnb-recently"></span>
                            <?}else{?>
                                <span class="icon lnb-recently"></span>
                            <?}?>
                            <span class="txt" style="letter-spacing: -2px">최근 본 상품</span>
                        </a>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
        </nav>

        <div class="box">

            <div class="box-in ctgr_list">

                <ul class="lnb_list_area">
                    <li>
                        <a href="#none" onclick="go_link('/notice')">
                            <em>공지사항</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="go_link('/push')">
                            <em>알람메세지</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#none"  onclick="go_link('/share')">
                            <em>공유상품</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#none"  onclick="go_link('/setting')">
                            <em>환경설정</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="go_link('/qna')">
                            <em>나의 1:1문의</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#none" class="setManyBuyPop">
                            <em>대량구매문의</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                    <li style="border: none;">
                        <a href="#none" onclick="go_link('/mypage')">
                            <em>개인정보변경</em>
                            <i class="arrow-right"></i>
                        </a>
                    </li>
                </ul>

                <div class="clear"> </div>

            </div>
        </div>


        <div class="box">
            <div class="box-in info">

                <div class="fl info-l">

                    <span class="tit">고객센터</span>

                    <ul>
                        <li> T. <em class="no_font" style="letter-spacing: .5pt!important;"><?=$this->config->item('site_help_tel')?></em></li>
                        <li>&middot; 평일 AM <em class="no_font" style="letter-spacing: 0!important;">10:00</em> ~ PM <em class="no_font" style="letter-spacing: 0!important;">18:00</em></li>
                        <li>&middot; 점심 AM <em class="no_font" style="letter-spacing: 0!important;">12:30</em> ~ PM <em class="no_font" style="letter-spacing: 0!important;">13:30</em></li>
                        <li>&middot; 토 / 일 / 공유일 휴무</li>
                    </ul>

                </div>

                <div class="fr info-r">
                    <ul>
                        <li><a href="#none" onclick="chatChannel()"><img src="<?=IMG_HTTP?>/images/call_kakao.png" /></a></li>
                        <li><a href="tel:<?=$this->config->item('site_help_tel')?>"><img src="<?=IMG_HTTP?>/images/call_cantact.png" alt="call_contact" /></a></li>
                        <div class="clear"></div>
                    </ul>

                </div>
                <div class="clear"></div>

            </div>
        </div>

    </div>

</aside>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>

<script type="text/javascript">

    //<![CDATA[
        // 사용할 앱의 JavaScript 키를 설정해 주세요.
    Kakao.init('<?=$this->config->item('kakao_app_key')['javascript']?>');
    function chatChannel() {
        Kakao.Channel.chat({
            channelPublicId: '_ISxgbxb' // 카카오톡 채널 홈 URL에 명시된 id로 설정합니다.
        });
    }
    //]]>

    $(function(){

        $('.setManyBuyPop').on('click',function(){

            var container = $('<div class="offer_area">');

            $(container).load('/offer');

            modalPop.createPop('대량구매문의', container);
            modalPop.show({'hide_footer':true , 'backdrop' : 'static'});

        });

    });

</script>

<div id="kakao-talk-channel-chat-button"></div>