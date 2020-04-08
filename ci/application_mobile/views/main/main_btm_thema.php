
<? if(count($aTheme) > 0){ ?>

    <? foreach ($aTheme as $k => $r) {?>

        <?if($r['view_type'] == 'A'){ // 가로이미지 + 가로 리스트?>

            <? if(empty($r['title']) == false){?>
            <div class="box">
                <div class="box-in" style="padding-bottom: 0!important;">
                    <label class="thema_tit" style="margin-bottom: 2px!important;"><?=$r['title']?></label>
                </div>
            </div>
            <?php } ?>
            <? foreach ($r['aLists'] as $kk => $rr) {
                $aListImage = $rr['p_rep_image'];
                $addClass = '';
                if(empty($r['title']) == false && $kk < 1) $addClass .= 'no-before';
                ?>
                <div class="main_product_list arrange_1 box <?=$addClass?>" <?if($kk < 1){?>style="border-top: none;" <?}?>>
                    <div class="box-in">

                        <div onclick="go_product('<?=$rr['p_num']?>','thema');" style="display: block" role="button">

                            <div class="tit">
                                <p class="p_name"><?=$rr['p_name']?></p>
                                <?if(empty($rr['p_summary']) == false){?><p class="p_summary"><?=nl2br($rr['p_summary'])?></p><?}?>
                            </div>

                            <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" />
                            <div class="p_info">
                                <ul>
                                    <li class="price_info">
                                        <a><span>옷쟁이들 쇼핑가 <em class="no_font"><?=number_format($rr['p_sale_price'])?></em></span>원</a>
                                        <div class="main_buy_btn_area"><button class="main_buy_btn">구매하기</button></div>
                                    </li>
                                    <!--                    <li class="delivery_info"><span class="f">무료배송</span></li>-->
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>
            <?}?>
            <div class="clear"></div>


        <?}else if($r['view_type'] == 'B'){ // 정사각형이미지 좌측 + 가로 리스트?>


            <div class="product_list_half box">
                <div class="box-in">

                    <label class="thema_tit"><?=$r['title']?></label>

                    <? foreach ($r['aLists'] as $kk => $rr) {
                        $aListImage = $rr['p_today_image'];//json_decode($r['today_image'],true)[0];
                        ?>
                        <div class="product_part" onclick="go_product('<?=$rr['p_num']?>','thema');" role="button" <?if($kk > 0){?>style="padding-top: 8px;" <?}?>>

                            <div class="img_l">
                                <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" style="" />
                            </div>

                            <div class="img_r">

                                <ul>
                                    <li class="img_r_pname"><?=$rr['p_name']?></li>
                                    <?if(empty($rr['p_summary']) == false){?><li class="img_r_psummary"><?=nl2br($rr['p_summary'])?></li><?}?>
                                    <li class="img_r_price_tit">
                                        <span class="tit">옷쟁이들 쇼핑가</span> <!--<span class="delivery_f">무료배송</span>-->
                                    </li>
                                    <li class="img_r_price">
                                        <em class="no_font"><?=number_format($rr['p_sale_price'])?></em>원
                                    </li>
                                </ul>

                            </div>

                            <div class="clear"></div>
                        </div>
                    <?}?>
                </div>
            </div>

            <div class="clear"></div>

        <?}else if($r['view_type'] == 'C'){ // 정사각형이미지 2개 리스트?>

            <div class="box">
                <div class="box-in">
                    <label class="thema_tit" style="margin-bottom: 10px!important;"><?=$r['title']?></label>
                </div>
            </div>
            <? foreach ($r['aLists'] as $kk => $rr) {
                //$aListImage = json_decode($r['p_rep_image'],true)[0];
                $aListImage = $rr['p_today_image'];
                ?>

                <div class="main_product_list arrange_2 box no-before">
                    <div class="box-in">

                        <div onclick="go_product('<?=$rr['p_num']?>','thema');" style="display: block" role="button">
                            <div class="tit">
                                <p class="p_name"><?=$rr['p_name']?></p>
                                <?if(empty($rr['p_summary']) == false){?><p class="p_summary"><?=nl2br($rr['p_summary'])?></p><?}?>
                            </div>
                            <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" />
                            <div class="p_info">
                                <ul>
                                    <li class="tit_2">
                                        <p class="p_name"><?=$rr['p_name']?></p>
                                    </li>
                                    <li class="price_info">
                                        <a><span style="letter-spacing: -0.2px;"><em class="no_font"><?=number_format($rr['p_sale_price'])?></em></span>원</a>
                                        <div class="main_buy_btn_area"><button class="main_buy_btn">구매하기</button></div>
                                    </li>
                                    <li class="delivery_info"><span class="f">무료배송</span></li>
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>

            <?}?>

            <div class="clear"></div>

        <? } ?>

    <? }?>

<?}?>
