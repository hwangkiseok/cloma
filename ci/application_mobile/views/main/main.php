<!-- top rolling -->
<div class="top-rolling">
    <ul class="swiper-wrapper">
        <? foreach ($aRollingLists as $k => $r) { ?>
            <li class="swiper-slide"><a role="button" onclick="go_link('/exhibition/list?seq=<?=$r['seq']?>')"><img src="<?=IMG_HTTP.$r['banner_img']?>" alt="" style="width: 100%;"/></a></li>
        <? } ?>
    </ul>
    <div class="clear"></div>
</div>


<? foreach ($aTop10Lists as $k => $r) {
    $aListImage = json_decode($r['p_rep_image'],true)[0];
    //$aListImage = $r['p_today_image'];
?>

    <div class="main_product_list arrange_1 box" <?if($k < 1){?>style="border-top: none;" <?}?>>
        <div class="box-in">

            <div onclick="go_product('<?=$r['p_num']?>','top10');" style="display: block" role="button">

                <div class="tit">
                    <p class="p_name"><?=$r['p_name']?></p>
                    <?if(empty($r['p_summary']) == false){?><p class="p_summary"><?=nl2br($r['p_summary'])?></p><?}?>
                </div>

                <img src="<?=$aListImage?>" alt="<?=$r['p_name']?>" />
                <div class="p_info">
                    <ul>
                        <!--                   <li><b style="font-size: 16px;">--><?//=$r['p_name']?><!--</b></li>-->
                        <!--<li>[<em class="no_font"><?=$r['p_discount_rate']?></em>]<em class="no_font"><?=$r['p_sale_price']?></em></li>-->
                        <li class="price_info">
                            <a><span>옷쟁이들 쇼핑가 <em class="no_font"><?=number_format($r['p_sale_price'])?></em></span>원</a>
                            <div class="main_buy_btn_area"><button class="main_buy_btn">구매하기</button></div>
                        </li>
    <!--                    <li class="delivery_info"><span class="f">무료배송</span></li>-->
                    </ul>
                </div>
            </div>

        </div>

    </div>
    <div class="clear"></div>

<?}?>

<div class="thema_wrap"></div>
<script type="text/javascript">

    $(function(){
        var mainRolling = new Swiper ('.top-rolling', {
            loop: true
        });
        get_btm_thema();
    });

    function get_btm_thema(){

        $.ajax({
            url : '/main/get_btm_thema',
            type : 'post',
            dataType : 'html',
            success : function (result) {
                $('.thema_wrap').html(result);
            }
        });

    }

</script>



