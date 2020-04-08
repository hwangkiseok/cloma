
<? if(count($aProductLists) > 0){ ?>
    <div class="product_curation">
        <div>
            <p class="tit"><?=$tit?></p>
            <div class="<?=$e_naming?>-rolling ext-rolling">
                <ul class="swiper-wrapper">

                    <? foreach ($aProductLists as $r) {?>

                        <li class="swiper-slide">
                            <img src="<?=$r['p_today_image']?>" alt="" style="width: 100%;"/>
                            <div class="rolling-info">
                                <p class="p-name"><?=$r['p_name']?></p>
                                <p class="p-price"><em class="dis-price no_font"><?=number_format($r['p_sale_price'])?></em>원</p>
                            </div>
                        </li>

                    <? } ?>

                </ul>
                <div class="clear"></div>
            </div>
        </div>
    </div>
<script>
    $(function(){
        new Swiper ('.<?=$e_naming?>-rolling', {
             slidesPerView: 3
            ,spaceBetween: 10
        });
    });
</script>
<? } ?>