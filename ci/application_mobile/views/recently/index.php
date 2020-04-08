<style>
    #container .box:last-of-type:after{ display: block; content: ""; height: 8px;_box-shadow: 0 3px 2px 0 rgba(0,0,0,0.1) inset;background: #f1f1f1 }
</style>
<div class="recently">
<?if(count($aRecentlyProduct) > 0){?>

    <?foreach ($aRecentlyProduct as $k => $r) { //zsView($r);//$aListImage = json_decode($r['p_rep_image'],true)[0];
        $aListImage = $r['p_today_image'];
        $p_price = $r['p_sale_price'];

        ?>

        <div class="box">

            <div class="box-in">

                <div class="sub_prod_list">

                    <div class="img fl"><img src="<?=$aListImage?>" width="100%" /></div>
                    <div class="cont fl">
                        <ul>
                            <li class="p_name"><?=$r['p_name']?></li>
                            <li><em class="no_font"><?=number_format($p_price)?>원</em></li>
                            <li><a class="btn btn-default wide" href="#none" onclick="go_product('<?=$r['p_num']?>','recently');">상세보기</a></li>
                        </ul>
                    </div>

                    <div class="clear"></div>

                </div>


            </div>
        </div>

    <?}?>

<?}else{?>

    <div class="box">

        <div class="box-in">

            <p style="text-align: center;line-height: 40px;height: 40px;">최근 본 상품이 없습니다.</p>

        </div>
    </div>

<?}?>
</div>

<script>
    $(function(){
        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.recently').css({'min-height': min_height+'px'});
        }
    })
</script>