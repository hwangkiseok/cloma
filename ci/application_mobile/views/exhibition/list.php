
<div class="box">
    <div class="box-in">
        <img src="<?=$aExhibitionList['banner_img']?>" alt="" style="width:100%;" />
    </div>
</div>
<? ?>

<div class="product_list_half box">
    <div class="box-in">
        <?if(count($aExhibitionList) > 0) {?>

            <? foreach ($aExhibitionList['children'] as $r) {
                $aListImage = $r['p_today_image'];
                ?>

                <div class="product_part" onclick="go_product('<?=$r['p_num']?>','exhibition');" role="button" <?if($k > 0){?>style="padding-top: 8px;" <?}?>>

                    <div class="img_l">
                        <img src="<?=$aListImage?>" alt="<?=$rr['p_name']?>" style="" />
                    </div>

                    <div class="img_r">

                        <ul>
                            <li class="img_r_pname"><?=$r['p_name']?></li>
                            <?if(empty($r['p_summary']) == false){?><li class="img_r_psummary"><?=nl2br($r['p_summary'])?></li><?}?>

                            <li class="img_r_price_tit">
<!--                                <span class="tit"></span> <span class="delivery_f">무료배송</span>-->
                            </li>
                            <li class="img_r_price">
                                <em class="no_font"><?=number_format($r['p_sale_price'])?></em>원
                            </li>
                        </ul>

                    </div>

                    <div class="clear"></div>
                </div>

            <? } ?>

        <? } ?>
    </div>
</div>

