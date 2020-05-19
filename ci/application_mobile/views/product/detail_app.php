<div class="box product_detail no-before">
    <div class="box-in product_info">

        <div class="cont_area product_cont" style="display: block;text-align: center;">

            <p>&nbsp;</p>
            <p style="font-size: 15px;font-weight: bold">'<?=$aProductInfo['p_name']?>'</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <?=$aProductInfo['p_detail']?>
            <? $product_img_arr = json_decode($aProductInfo['p_detail_image'],true);
            if(count($product_img_arr) > 0){?>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            <? foreach ($product_img_arr as $k => $r) {?>
                <img src="<?=$r[0]?>" alt="img_<?=$k?>" />
            <?
                }
            }
            ?>
            <!--
            <div class="more">
                <button> + 상품설명 더보기</button>
            </div>
            -->
        </div>

    </div>
</div>

<script type="text/javascript">
    $(function(){
        //상품설명 더보기
        $('.product_cont .more button').on('click' ,function(e){
            e.preventDefault();
            $('.product_cont').css('height' , 'auto');
            $('.product_cont .more').hide();
        });
    })
</script>
