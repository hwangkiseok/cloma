<style>

    .product_curation {margin: 0 -8px;}
    .product_curation:before {display: block;content: "";height: 8px;background: #f1f1f1;}
    .product_curation > div {padding: 10px;}
    .product_curation .tit {font-size: 16px;font-weight: bold;padding-bottom: 15px;padding-top: 5px;}
    .product_curation li:first-child {margin-left: 0!important;}
    .product_curation li:nth-child(3n+1) {margin-left: 0!important;}
    .product_curation li {width: 30%;display: inline-block;margin-left: 4%;vertical-align: top;}
    .product_curation{margin-top: 8px;}
    .product_curation img { }
    .product_curation .rolling-info {margin-top: 5px;text-align: left;padding-right: 10px;margin-bottom: 15px;}
    .product_curation .rolling-info .p-name {white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
    .product_curation .rolling-info .p-price{}
    .product_curation .rolling-info .p-price em.dis-price{font-size: 15px;}
    .product_curation .rolling-info .p-price em.rate{color: red;margin-right: 5px;font-size: 15px;}

    @media (max-width: 320px) {
        .product_curation li {margin-left: 10px;}
    }
    
</style>
<? if(count($aProductLists) > 0){ ?>
    <div class="product_curation">
        <div>
            <p class="tit"><?=$tit?></p>
            <ul>

                <? foreach ($aProductLists as $r) {?>

                    <li onclick="go_product('<?=$r['p_num']?>','recommand')">
                        <img src="<?=$r['p_today_image']?>" alt="" style="width: 100%;"/>
                        <div class="rolling-info">
                            <p class="p-name"><?=$r['p_name']?></p>
                            <!--
                            <p class="p-price">
                                <em class="rate no_font"><?=number_format($r['p_discount_rate'])?>%</em>
                                <em class="dis-price no_font"><?=number_format($r['p_sale_price'])?></em>원
                            </p>
                            -->
                        </div>
                    </li>

                <? } ?>

            </ul>
            <div class="clear"></div>
        </div>
    </div>
<? } ?>