<? if(count($aProductLists) > 0){?>

    <? foreach ($aProductLists as $k => $r) {
        $aListImage = $r['p_today_image'];//json_decode($r['today_image'],true)[0];
        ?>
        <div class="product_part" onclick="go_product('<?=$r['p_num']?>','search');" role="button" <?if($k > 0){?>style="padding-top: 8px;" <?}?>>

            <img src="<?=$aListImage?>" alt="<?=$r['p_name']?>" />

            <div class="img_r">

                <ul>
                    <li class="img_r_pname"><?=$r['p_name']?></li>
                    <?if(empty($r['p_summary']) == false){?><li class="img_r_psummary"><?=nl2br($r['p_summary'])?></li><?}?>
                    <li class="img_r_price_tit">
                        <span class="tit"></span> <span class="delivery_f">무료배송</span>
                    </li>
                    <li class="img_r_price">
                        <em class="no_font"><?=number_format($r['p_sale_price'])?></em>원
                    </li>
                </ul>

            </div>

            <div class="clear"></div>
        </div>

    <?}?>

<?} else{?>

    <p>검색결과가 없습니다.</p>

<?} ?>

<script type="text/javascript">
    $(function(){
        <? if($req['page'] == $total_page){?>
        $('input[name="more"]').val(0);
        <?} ?>
    })
</script>