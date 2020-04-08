<div class="box product_detail no-before">
    <div class="box-in product_info">

        <div class="cont_area product_cont" style="display: block;text-align: center;">
            <?=$aProductInfo['p_detail']?>
            <? $product_img_arr = json_decode($aProductInfo['p_detail_image'],true);
            if(count($product_img_arr) > 0){
            foreach ($product_img_arr as $k => $r) {?>
                <img src="<?=$r[0]?>" alt="img_<?=$k?>" />
            <?
                }
            }
            ?>
        </div>

    </div>
</div>