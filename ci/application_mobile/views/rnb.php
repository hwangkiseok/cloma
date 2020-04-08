<aside id="rnb_area">

    <div class="rnb">

        <div class="rnb_top">
            <div class="fl prof_img">
                <i class="fas fa-user-alt"></i>
            </div>

            <?if(member_login_status() == true){   ?>

                <div class="fl nick_name">
                    <a style="color:#fff;"><?=$aMemberInfo['m_nickname']?>님 안녕하세요!</a>
                </div>

                <div class="fr cont_side">
                    <ul>
                        <li style="text-align: right"><i onclick="side_close('r');" class="fas fa-times-circle" style="color: #fff;font-size: 24px;"></i></li>
                        <li>
                            <a href="#none" onclick="go_link('/auth/logout')" class="rnb-btn">로그아웃</a>
                            <a href="#none" class="rnb-btn">회원정보</a>
                        </li>
                    </ul>
                </div>

            <?}else{?>

                <div class="fl nick_name">
                    <a href="#none" onclick="go_link('/member')" style="text-decoration: underline;color:#fff;">로그인이 필요합니다.</a>
                </div>

                <div class="fr cont_side">
                    <ul>
                        <li style="text-align: right"><i onclick="side_close('r');" class="fas fa-times-circle" style="color: #fff;font-size: 24px;"></i></li>
                        <li>
                            <a href="#none" onclick="go_link('/test/login/1')" class="rnb-btn">로그인</a>
                            <a href="#none" onclick="go_link('/member')" class="rnb-btn">회원가입</a>
                        </li>
                    </ul>
                </div>

            <?}?>
        </div>

        <section class="my_list">
            <ul>
                <li><a href="/delivery">주문/배송조회<i class="fas fa-chevron-right fr"></i></a></li>
                <li><a href="#none">환불내역<i class="fas fa-chevron-right fr"></i></a></li>
                <li><a href="/cart">장바구니<i class="fas fa-chevron-right fr"></i></a></li>
                <li><a href="/comment">나의 댓글<i class="fas fa-chevron-right fr"></i></a></li>
                <li><a href="/push">알림 메시지<i class="fas fa-chevron-right fr"></i></a></li>
                <li><a href="/setting">환경설정<i class="fas fa-chevron-right fr"></i></a></li>
            </ul>
        </section>

    </div>

</aside>

