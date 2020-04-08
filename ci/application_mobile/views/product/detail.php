<? link_src_html('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css' , 'css');  ?>

<script type="text/javascript">
    var p_num = '<?=$aProductInfo['p_num']?>';
    var p_order_code = '<?=$aProductInfo['p_order_code']?>';

    var cmt_num = '<?=$aProductInfo['p_num']?>'; //코멘트시 사용 num
    var option_depth = '<?=$option_depth?>'; //옵션에 사용

</script>

<div class="box product_detail no-before">
    <div class="box-in product_info">

        <img src="<?=$aProductInfo['p_today_image']?>" alt="<?=$aProductInfo['p_name']?>">

        <div class="top-info">

            <!--
            <?if($aProductInfo['p_discount_rate'] != '0.00'){?>
                <span class="btn btn-default badge"><?=number_format($aProductInfo['p_discount_rate'])?>% 할인</span>
            <?}else{?>
                <span class="btn btn-default badge">할인상품</span>
            <?}?>

            <?if($aProductInfo['p_deliveryprice_type'] == '2'){?>
                <span class="btn btn-border-purple">조건부 무료배송</span>
            <?}else if($aProductInfo['p_deliveryprice_type'] == '3'){?>
                <span class="btn btn-border-blue">무료배송</span>
            <?}else{ //유료배송?>

            <?}?>
            -->


            <div class="inner-top">
                <div class="fl">
                    <div class="p-name"><?=$aProductInfo['p_name']?></div>
                    <div class="p-price">
                        <em class="org-price no_font"><?=number_format($aProductInfo['p_sale_price'])?></em>원
                        <em class="dis-price sig_col no_font"><?=number_format($aProductInfo['p_sale_price'])?></em>원
                    </div>
                </div>
                <div class="fr icon-set">
                    <span class="setWish icon-wish">
                        <em class="icon normal-icon <?if($isWish == true){?>active<?}?>"><i class="fas fa-heart"></i></em>
                        <em class="icon abs-icon <?if($isWish == true){?>active<?}?>"></em>
                        <br>
                        <em class="no_font"><?=number_format($aProductInfo['p_wish_count'])?></em>
                    </span>
                    <span class="setShare icon-share">
                        <em class="icon <?if($isShare == true){?>active<?}?>"><i class="fas fa-share-alt"></i></em><br>
                        <em class="no_font"><?=number_format($aProductInfo['p_share_count'])?></em>
                    </span>
                </div>
                <div class="clear"></div>
            </div>

        </div>

        <div class="product-tab">
            <ul>
                <li class="active" data-set="product_cont" data-seq="0">상세정보</li>
                <li data-set="product_deli" data-seq="1">배송정보</li>
                <li data-set="product_comment" data-seq="2">댓글(<em class=""><?=$nCommentLists?></em>)</li>
                <li class="btm-line"></li>
            </ul>
            <div class="clear"></div>
        </div>

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

        <div class="cont_area product_deli" style="display: none">
            <?=$aDeliveryInfo['cm_content']?>
        </div>

        <?=$rel_product?>
        <?=$with_product?>

        <!-- 코멘트 사용시 -->
        <input type="hidden" name="comment_p" value="2" />
        <input type="hidden" name="nCommentLists" value="<?=$nCommentLists?>" />
        <input type="hidden" name="cmt_table" value="product" />
        <input type="hidden" name="obj_name" value="product_comment" />
        <div class="product_comment"><?=$ext_comment['comment_view']?></div>

    </div>

    <?
    /*구매시 주문서페이지에 들고갈 데이터*/ 
    ?>
    <form name="product_order" id="product_order" method="post" >
        <input type="hidden" name="item_no" value="">
        <input type="hidden" name="option_info" value="">
        <input type="hidden" name="set_referer" value="<?=$_SESSION['_set_referer']?>">
        <input type="hidden" name="set_campaign" value="<?=$_SESSION['_set_campaign']?>">
        <input type="hidden" name="buy_count" value=""> <!-- 옵션상품이 아닌경우 구매 수량 -->
        <input type="hidden" name="cart_yn" value="N">
    </form>

    <?
    /*장바구니시 들고갈 데이터*/
    ?>
    <form name="product_cart" id="product_cart" method="post" >
        <input type="hidden" name="item_no" value="">
        <input type="hidden" name="option_info" value="">
        <input type="hidden" name="set_referer" value="<?=$_SESSION['_set_referer']?>">
        <input type="hidden" name="set_campaign" value="<?=$_SESSION['_set_campaign']?>">
        <input type="hidden" name="buy_count" value=""> <!-- 옵션상품이 아닌경우 구매 수량 -->
        <input type="hidden" name="sSnsformOptionType" value="<?=$sSnsformOptionType?>">
    </form>

    <div class="buy_area">
        <div class="opt_area">

            <div class="opt_inner">

                <div class="tit_top">
                    <i class="fas fa-chevron-down"></i>
                    <i class="fas fa-chevron-up"></i>
                </div>

                <?if($option_depth < 1){?>
                    <div class="option_sel_result">

                        <div class="sel_option_row" data-price="<?=$aProductInfo['p_sale_price']?>" data-uid="" data-supply="<?=$aProductInfo['p_supply_price']?>">
                           <div class="sel_option_tit"><?=$aProductInfo['p_name']?></div>
                           <span class="fl cnt_ctrl">
                               <span class="opt_minus">-</span>
                               <span><input type="text" class="no_font" name="option_input" value="1" title="수량"></span>
                               <span class="opt_plus">+</span>
                           </span>
                           <span class="fr sel_option_price"><em class="no_font"><?=number_format($aProductInfo['p_sale_price'])?></em>원</span>
                           <div class="clear"></div>
                        </div>
                    </div>
                <?}else{?>

                    <div class="option_sel" data-depth="1">
                        <div class="opt_tit" role="button">옵션선택 1
                            <i class="fas fa-chevron-down fr" style="padding-top: 6px;"></i>
                            <i class="fas fa-chevron-up fr" style="padding-top: 6px;"></i>
                        </div>
                        <div class="opt_list">
                            <ul>
                                <? foreach ($aOption as $k => $r) {

                                    $data = array();
                                    if($option_depth == 1) {
                                        $data['val'] = $r['option_depth1'];
                                        $data['name'] = $r['option_depth1'];
                                        $data['price'] = $r['option_price'];
                                        $data['option_supply'] = $r['option_supply'];
                                        $data['option_count'] = $r['option_count']; //재고량

                                    } else {
                                        $data['val'] = $k;
                                        $data['name'] = $k;
                                        $data['price'] = '';
                                        $data['option_count'] = '';
                                    }

                                ?>
                                    <li class="<?if($data['option_count'] != '' && $data['option_count'] < 1){?>disable<?}?>" data-val="<?=$data['val']?>" data-price="<?=$data['price']?>" data-name="<?=$data['name']?>" data-option_count="<?=$data['option_count']?>" data-option_supply="<?=$data['option_supply']?>" role="button">
                                        <?=$data['name']?> <?if(empty($data['price']==false)){?><span class="fr"><em class="no_font"><?= number_format($data['price'])?></em>원</span><?}?>
                                    </li>
                                <? } ?>
                            </ul>
                        </div>
                    </div>

                    <?if($option_depth >= 2){?>
                    <div class="option_sel" data-depth="2">
                        <div class="opt_tit" role="button">옵션선택 2
                            <i class="fas fa-chevron-down fr" style="padding-top: 6px;"></i>
                            <i class="fas fa-chevron-up fr" style="padding-top: 6px;"></i>
                        </div>
                        <? foreach ($aOption as $k => $r) {?>
                            <div class="opt_list" data-seq="<?=$k?>">
                                <ul>
                                    <? foreach ($r as $kk => $rr) { //zsView($rr);

                                        $data = array();
                                        if($option_depth == 2) {
                                            $data['val'] = $rr['option_depth2'];
                                            $data['name'] = $rr['option_depth2'];
                                            $data['price'] = $rr['option_price'];
                                            $data['option_supply'] = $rr['option_supply'];
                                            $data['option_count'] = $rr['option_count']; //재고량
                                        } else {
                                            $data['val'] = $kk;
                                            $data['name'] = $kk;
                                            $data['price'] = '';
                                            $data['supply'] = '';
                                            $data['option_count'] = ''; //재고량
                                        }
                                    ?>
                                    <li class="<?if($data['option_count'] != '' && $data['option_count'] < 1){?>disable<?}?>" data-supply_price="<?=$data['supply']?>" data-val="<?=$data['val']?>" data-price="<?=$data['price']?>" data-name="<?=$data['name']?>" data-option_count="<?=$data['option_count']?>" data-option_supply="<?=$data['option_supply']?>" role="button">
                                        <?=$data['name']?> <?if(empty($data['price']==false)){?><span class="fr"><em class="no_font"><?= number_format($data['price'])?></em>원</span><?}?>
                                    </li>
                                    <? } ?>
                                </ul>
                                <div class="clear"></div>
                            </div>
                        <? } ?>
                    </div>
                    <?}?>

                    <?if($option_depth >= 3){?>

                    <div class="option_sel" data-depth="3">
                        <div class="opt_tit" role="button">옵션선택 3
                            <i class="fas fa-chevron-down fr" style="padding-top: 6px;"></i>
                            <i class="fas fa-chevron-up fr" style="padding-top: 6px;"></i>
                        </div>
                        <? foreach ($aOption as $k => $r) {?>
                            <? foreach ($r as $kk => $rr) { ?>
                                <div class="opt_list" data-seq="<?=$k?>|<?=$kk?>">
                                    <ul>
                                        <? foreach ($rr as $kkk => $rrr) {
                                            $data = array();
                                            $data['val'] = $rrr['option_depth3'];
                                            $data['name'] = $rrr['option_depth3'];
                                            $data['price'] = $rrr['option_price'];
                                            $data['option_supply'] = $rrr['option_supply'];
                                            $data['option_count'] = $rrr['option_count']; //재고량
                                        ?>

                                            <li class="<?if($data['option_count'] != '' && $data['option_count'] < 1){?>disable<?}?>" data-val="<?=$data['val']?>" data-price="<?=$data['price']?>" data-name="<?=$data['name']?>" data-option_count="<?=$data['option_count']?>" data-option_supply="<?=$data['option_supply']?>" role="button">
                                                <?=$data['name']?> <?if(empty($data['price']==false)){?><span class="fr"><em class="no_font"><?= number_format($data['price'])?></em>원</span><?}?>
                                            </li>
                                        <?}?>
                                    </ul>
                                    <div class="clear"></div>
                                </div>
                            <? } ?>
                        <? } ?>

                    </div>
                    <?}?>

                    <div style="height: 8px;"></div>

                    <div class="option_sel_result"></div>
                    <div class="tot_result_price"></div>

                <?}?>


            </div>
        </div>
        <div class="buy_btn_area">
            <button class="btn btn-border-red goCart">장바구니</button>
            <button class="btn btn-default goBuy">구매하기</button>
        </div>
    </div>

</div>

<div id="cart_c_pop">
    <div class="cart_pop_bg"></div>

    <div class="cart_wrap">
        <div class="cart_top sig_col">장바구니 담기</div>
        <div class="cart_cont">
            <span class="icon_cart fl"><i></i></span>
            <span class="cart_txt fl">
                장바구니에 선택하신 상품이<br>정상적으로 담겼습니다.
            </span>
            <div class="clear"></div>
        </div>

        <div class="cart_btn_warp">
            <a role="button" class="go_cart zs-cp" onclick="go_link('/cart');">장바구니 이동</a>
            <a role="button" onclick="$('#cart_c_pop').hide();">쇼핑 계속하기</a>
            <div class="clear"></div>
        </div>

    </div>

</div>

<? link_src_html('/js/product.js' , 'js');  ?>