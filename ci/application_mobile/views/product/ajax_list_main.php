<? if($campaign == 'best'){?>

    <div class="product_list_half box no-before">
        <div class="box-in">

            <? foreach ($aProductLists as $kk => $rr) {
                $aListImage = $rr['p_today_image'];//json_decode($r['today_image'],true)[0];
                $no = (int)$kk+1;
                ?>
                <div class="product_part" onclick="go_product('<?=$rr['p_num']?>','thema');" role="button" <?if($kk > 0){?>style="padding-top: 8px;" <?}?>>

                    <div class="img_l">
                        <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" style="" />
                        <a class="best-no no-font"><?=$no?></a>
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

<? } else {?>

    <? foreach ($aProductLists as $k => $r) {
        $aListImage = $r['p_today_image'];
        ?>

        <div class="main_product_list arrange_2 box">
            <div class="box-in">

                <div onclick="go_product('<?=$r['p_num']?>','<?=$campaign?>');" style="display: block" role="button">
                    <div class="tit">
                        <p class="p_name"><?=$r['p_name']?></p>
                        <?if(empty($r['p_summary']) == false){?><p class="p_summary"><?=nl2br($r['p_summary'])?></p><?}?>
                    </div>
                    <img src="<?=$aListImage?>" alt="<?=$r['p_name']?>" />
                    <div class="p_info">
                        <ul>
                            <!--<li>[<em class="no_font"><?=$r['p_discount_rate']?></em>]<em class="no_font"><?=$r['p_sale_price']?></em></li>-->
                            <!--                        <li class="tit_2">-->
                            <!--                            <p><b>[--><?//=$r['p_cate1']?><!--]</b></p>-->
                            <!--                        </li>-->
                            <li class="tit_2">
                                <p class="p_name"><?=$r['p_name']?></p>
                            </li>
                            <li class="price_info">
                                <a><span style="letter-spacing: -0.2px;"><em class="no_font"><?=number_format($r['p_sale_price'])?></em></span>원</a>
                                <div class="main_buy_btn_area"><button class="main_buy_btn">구매하기</button></div>
                            </li>
                            <li class="delivery_info"><span class="f">무료배송</span></li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    <? } ?>

<?} ?>

<script type="text/javascript">
    $(function(){

        var a = '<?=$req['page']?>';
        var b = '<?=$total_page?>';

        <? if($req['page'] >= $total_page){?>
        $('input[name="more"]').val(0);
        <?} ?>
    })
</script>
