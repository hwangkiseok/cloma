
        <div class="direct_area">
            <ul class="direct_menu">
                <li><a href="#none" onclick="set_scrolltop();"><i class="arrow-top"></i></a></li>
            </ul>
            <div class="clear"></div>
        </div>

        <?if($btm_fix_menu_yn == 'Y'){?>

            <div class="btm_fix_menu">
                <ul>
                    <li><a href="#none" onclick="side_show('l');"><i class="bars"></i></a></li>
                    <li><a href="/"><i class="home"></i></a></li>
                    <li><a href="/delivery"><i class="delivery"></i></a></li>
                    <li><a href="/push"><i class="push"></i></a></li>

                    <li>
                        <?if(empty($aRecentlyProduct) == true){?>
                            <a onclick="go_link('/recently');"><i class="empty-recently"></i></a>
                        <?}else{ $btm_fix_img = json_decode($aRecentlyProduct['p_rep_image'],true)[0];  ?>
                            <a onclick="go_link('/recently');"><i class="empty-recently"></i></a>

                            <!--
                            <a href="#none" onclick="go_link('/recently');">
                                <img src="<?=$btm_fix_img?>" alt="<?=$aRecentlyProduct['p_name']?>" width="100%">
                            </a>
                            -->
                        <?}?>
                    </li>
                    <div class="clear"></div>
                </ul>

            </div>
        <?}?>

    </section> <!-- section (container) of end -->

    <footer id="footer">

        <div class="copyright">

            <a class="copyright_toggle zs-cp"><?=$this->config->item('company_name_kr')?> 사업자정보 확인<i></i></a>

            <div class="copyright_cont">
                <p>상호 : <?=$this->config->item('company_name_kr')?> / 대표자 : <?=$this->config->item('site_name_ceo')?>  /  개인정보책임자 : <?=$this->config->item('site_name_cpo')?></p>
                <p><a href="tel:<?=$this->config->item('site_help_tel')?>">전화 : <?=$this->config->item('site_help_tel')?></a> <!-- / 이메일 : <?=$this->config->item('site_help_email')?> --> / 사업자등록번호 : <?=$this->config->item('biz_no')?> </p>
                <p>통신판매업 신고 : <?=$this->config->item('tongsin')?> <em class="zs_cp" onclick="go_link('http://www.ftc.go.kr/bizCommPop.do?wrkr_no=<?=$this->config->item('biz_no')?>','','Y')">[사업자정보확인]</em></p>
                <p>주소 : <?=$this->config->item('site_zip_code')?> <?=$this->config->item('site_addr')?></p>
                <p>Copyright © 2020 <?=$this->config->item('site_name_kr')?> All rights reserved.</p>
            </div>

        </div>




    </footer>

</div> <!-- wrapper of end -->
<div class="empty-space"></div>

</html>