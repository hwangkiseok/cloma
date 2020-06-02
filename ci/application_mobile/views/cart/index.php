<?php link_src_html("/plugins/icheck/skins/square/blue.css", "css"); ?>
<?php link_src_html("/plugins/icheck/icheck.min.js", "js"); ?>

<script>

    $(function(){

        var ctrl_top = $('.ctrl-line').offset().top;

        $(window).scroll(function() {

            var x = parseInt($(this).scrollTop());

            <?if(is_app() == false){?>

            if(ctrl_top <= x && $('.depth3').length < 1){

                $('.all_check').iCheck('destroy');

                var html  = "<div class='depth3 depth3nav'>";
                    html += "   <div class='box' >";
                    html += "       <div class='box-in' style='padding-bottom: 0!important;'>";
                    html += "           <div class='ctrl-line' style='margin-bottom: 0!important;border-left: 1px solid #ddd;border-right: 1px solid #ddd;'>";
                    html += $('.ctrl-line').html();
                    html += "           </div>";
                    html += "       </div>";
                    html += "   </div>";
                    html += "</div>";

                    $('.header_fixed').append(html);

                $('.all_check').iCheck({
                    checkboxClass: 'icheckbox_square-blue'
                });

            }

            <?}else{?>

            if(ctrl_top <= x ){

                $('.all_check').iCheck('destroy');

                var html  = "<div class='ctrl-line-fixed'>";
                    html += "   <div class='box' >";
                    html += "       <div class='box-in'>";
                    html += "           <div class='ctrl-line'>";
                    html += $('.ctrl-line').html();
                    html += "           </div>";
                    html += "       </div>";
                    html += "   </div>";
                    html += "</div>";
                $('.cart_wrap').prepend(html);

                $('.all_check').iCheck({
                    checkboxClass: 'icheckbox_square-blue'
                });

            }else{

                $('.cart_wrap .ctrl-line-fixed').remove();
            }

            <?}?>

        });

    });

</script>

<style>
    .ctrl-line-fixed { position: fixed;top: 0;left: 0;width: 100%;z-index: 100;border: 0!important;  }
    .ctrl-line-fixed .box-in{padding-bottom: 0!important;}
    .ctrl-line-fixed .box-in .ctrl-line{margin-bottom: 0!important;}
</style>

<div class="cart_wrap">
<?
$tot_price = 0;
?>
<?if($list_count['cnt'] > 0){?>

    <?foreach ($cart_prod_list as $k => $r) { //zsView($r);
        //$aListImage = json_decode($r['p_rep_image'],true)[0];
        $aListImage = $r['p_today_image'];
        $p_price = $r['p_sale_price'];
        $option_info = json_decode($r['option_info'],true);
        $option_tot_price = (int)$option_info['option_price']*(int)$option_info['option_count'];

        $product_option_info = json_decode($r['product_option_info'],true);
        $option_name_arr = explode(' | ',$option_info['option_name']);

        $depth = 1;
        $depth1 = $option_name_arr[0];
        if(empty($option_name_arr[1]) == false) {
            $depth2 = $option_name_arr[1];
            $depth = 2;
        }
        if(empty($option_name_arr[2]) == false) {
            $depth3 = $option_name_arr[2];
            $depth = 3;
        }

        $is_stock = true;
        foreach ($product_option_info as $rr) {

            if($depth == 3){
                if($rr['option_depth1'] == $depth1 && $rr['option_depth2'] == $depth2 && $rr['option_depth3'] == $depth3) if($rr['option_count'] < 1) $is_stock = false;
            }else if($depth == 2){
                if($rr['option_depth1'] == $depth1 && $rr['option_depth2'] == $depth2) if($rr['option_count'] < 1) $is_stock = false;
            }else if($depth == 1){
                if($rr['option_depth1'] == $depth1) if($rr['option_count'] < 1) $is_stock = false;
            }

        }

        $is_sale = false;
        if($r['p_sale_state'] == 'Y' && $r['p_stock_state'] == 'Y' && $is_stock == true) $is_sale = true;

        ?>

        <div class="box">

            <div class="box-in">

                <?if($k == 0){?>

                    <div class="ctrl-line">
                        <span class="fl" style="margin-left: 7px;"><input type="checkbox" class="all_check" checked />&nbsp;&nbsp;전체선택</span>
                        <span class="fr" style="margin-right: 7px;">
                            <a class="del" href="#none" onclick="chkDel();"><i></i>선택삭제</a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
<!--                            <a class="mod" href="#none" onclick="chkMod();"><i></i>옵션저장</a>-->
                        </span>
                        <div class="clear"></div>
                    </div>

                <?}?>

                <div style="position: relative" class="sub_prod_list <?if($is_sale == true){?>on<?}?>" data-p_name="<?=$r['p_name']?>" data-cart_id="<?=$r['cart_id']?>" data-p_order_code="<?=$r['p_order_code']?>" <? foreach ($option_info as $kk => $vv) {?>data-<?=$kk?>="<?=$vv?>"<?}?> >

                    <div class="chk fl">
                        <?if($is_sale == true){?>
                            <input type="checkbox" name="num_check" class="num_check" value="<?=$r['cart_id']?>" checked  />
                        <?}else{?>
                            <input type="checkbox" name="num_check" class="num_check" value="<?=$r['cart_id']?>" disabled />
                        <?}?>
                    </div>
                    <div class="img fl">
                        <a class="zs-cp" onclick="go_product('<?=$r['p_num']?>','cart')"><img src="<?=$aListImage?>" width="100%" alt="<?=$r['p_name']?>" /></a></div>
                    <div class="cont fl">
                        <ul style="line-height: 22px">
                            <li class="p_name"><?=$r['p_name']?></li>
                            <li><em class="no_font"><?=number_format($option_info['option_price'])?></em>원 | <em class="no_font option_count"><?=number_format($option_info['option_count'])?></em>개</li>
                            <li class="opt">옵션 : <?=$option_info['option_name']?></li>
                            <li>
                                <div class="cart_cnt">
                                    <span class="cart_m" role="button" content="zs-cp">-</span>
                                    <input type="text" name="cart_count" value="<?=$option_info['option_count']?>" class="no_font" maxlength="3" numberOnly readonly  />
                                    <span class="cart_p" role="button" content="zs-cp">+</span>
                                </div>
                                <div class="cart_price" data-pnum="<?=$r['p_num']?>">
                                    <em class="no_font" style="letter-spacing: .5pt!important;"><?=number_format($option_tot_price)?></em>원
                                </div>
                                <div class="clear"></div>

                            </li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                    <?=chk_soldout_layer($is_sale)?>
                </div>

            </div>


        </div>

    <?if($is_sale == true) $tot_price += (int)$option_tot_price;}?>

<?}else{?>

    <div class="box">

        <div class="box-in">

            <p style="text-align: center;line-height: 40px;height: 40px;">장바구니에 담긴 상품이 없습니다.</p>

        </div>
    </div>

<?}?>

<div class="box">
    <div class="box-in" style="padding: 8px 16px;">
        <div class="tot_price price_detail_expended">
            <span class="fl price_tit">주문금액 <em>(배송비 포함)</em></span>
            <span class="fr price"><em class="no_font"><?=$tot_price < 1 ? 0 : number_format($tot_price+2500)?></em>원 <i class="arrow-bottom"></i></span>
        </div>
        <div class="clear"></div>
        <div class="price_detail">

            <ul>
                <li class="detail_tot_price_wrap">
                    <span class="fl detail_tot_price_tit">총 결제금액</span>
                    <span class="fr detail_tot_price"><em class="no_font"><?=$tot_price < 1 ? 0 : number_format($tot_price+2500)?></em>원</span>
                    <div class="clear"></div>
                </li>
                <li class="detail_product_price_wrap">
                    <span class="fl">상품금액</span>
                    <span class="fr"><em class="no_font"><?=number_format($tot_price)?></em>원</span>
                    <div class="clear"></div>
                </li>
                <li class="detail_delivery_price_wrap">
                    <span class="fl">배송비</span>
                    <span class="fr"><em class="no_font"><?=$tot_price < 1 ? 0 : '2,500'?></em>원</span>
                    <div class="clear"></div>
                </li>
            </ul>

        </div>
    </div>
</div>

<div class="box no-before no-shadow">
    <div class="box-in" style="padding: 0 16px;">
        <ul class="cart-notice">
            <li class="warning"><i></i> 유의사항<div class="clear"></div></li>
            <li>묶음배송이 아닌 경우 개별 배송비가 발생합니다.</li>
            <li>판매종료 / 품절 상품은 별도로 표기 됩니다.</li>
        </ul>
    </div>
</div>

<div class="btm_fix_area cart_btm">
    <div class="fl main_btn">메인바로가기</div>
    <div class="fl pay_btn">구매하기</div>
</div>

<form name="cart_form" action="" method="post">
    <input type="hidden" name="shop_id" value="<?=$aInput['shop_id']?>">
<!--    <input type="hidden" name="a_session_id" value="--><?//=$aInput['a_session_id']?><!--">-->
    <input type="hidden" name="a_code" value="<?=$aInput['a_code']?>">
    <input type="hidden" name="a_campaign" value="<?=$aInput['a_campaign']?>">
    <input type="hidden" name="a_referer" value="<?=$aInput['a_referer']?>">
    <input type="hidden" name="partner_buyer_id" value="<?=$aInput['partner_buyer_id']?>">
    <input type="hidden" name="toggle_header" value="<?=$aInput['toggle_header']?>">
    <input type="hidden" name="a_buyer_name" value="<?=$aLastOrder['buyer_name']?>">
    <input type="hidden" name="a_buyer_hhp" value="<?=$aLastOrder['buyer_hhp']?>">
    <input type="hidden" name="a_receiver_name" value="<?=$aLastOrder['receiver_name']?>">
    <input type="hidden" name="a_receiver_hhp" value="<?=$aLastOrder['receiver_tel']?>">
    <input type="hidden" name="a_receiver_zip" value="<?=$aLastOrder['receiver_zip']?>">
    <input type="hidden" name="a_receiver_addr1" value="<?=$aLastOrder['receiver_addr1']?>">
    <input type="hidden" name="a_receiver_addr2" value="<?=$aLastOrder['receiver_addr2']?>">
    <input type="hidden" name="a_basket_info" value="">
    <input type="hidden" name="a_cart_yn" value="Y">
    <input type="hidden" name="a_payway_cd" value="1">
</form>


<form name="order_form" method="post" action="" >
    <input type="hidden" name="a_buy_count" value="">
    <input type="hidden" name="a_option_info" value=''>
    <input type="hidden" name="item_no" value="">
    <input type="hidden" name="a_campaign" value="<?=$aInput['a_campaign']?>">
    <input type="hidden" name="a_referer" value="<?=$aInput['a_referer']?>">
    <input type="hidden" name="partner_buyer_id" value="<?=$aInput['partner_buyer_id']?>">
    <input type="hidden" name="a_code" value="<?=$aInput['a_code']?>">
    <input type="hidden" name="toggle_header" value="<?=$aInput['toggle_header']?>">
    <input type="hidden" name="partner_seller_id" value="<?=$this->config->item('form_api_id')?>">
    <input type="hidden" name="a_buyer_name" value="<?=$aLastOrder['buyer_name']?>">
    <input type="hidden" name="a_buyer_hhp" value="<?=$aLastOrder['buyer_hhp']?>">
    <input type="hidden" name="a_receiver_name" value="<?=$aLastOrder['receiver_name']?>">
    <input type="hidden" name="a_receiver_hhp" value="<?=$aLastOrder['receiver_tel']?>">
    <input type="hidden" name="a_receiver_zip" value="<?=$aLastOrder['receiver_zip']?>">
    <input type="hidden" name="a_receiver_addr1" value="<?=$aLastOrder['receiver_addr1']?>">
    <input type="hidden" name="a_receiver_addr2" value="<?=$aLastOrder['receiver_addr2']?>">
    <input type="hidden" name="a_cart_yn" value="Y">
    <input type="hidden" name="a_payway_cd" value="1">
</form>
</div>

<script type="text/javascript">

    var cart_url    = '/order/cart';
    var order_url   = '/order';

    var form_cart_url = '<?=$this->config->item('prefix_cart_url');?>';
    var form_order_url = '<?=$this->config->item('prefix_order_url');?>';

    function cart_calc(){

        var delivery_amt = 2500;
        var product_amt = 0;

        if( $('input[name="num_check"]:checked').not('[disabled]').length < 1 ){

            $('.tot_price .price em').html(0);
            $('.price_detail .detail_tot_price_wrap em').html(0);
            $('.price_detail .detail_product_price_wrap em').html(0);
            $('.price_detail .detail_delivery_price_wrap em').html(0);

        }else{

            $('input[name="num_check"]:checked').not('[disabled]').each(function(){
                var cart_id = $(this).val();
                var target_obj = $('.sub_prod_list[data-cart_id="'+cart_id+'"]');

                product_amt += parseInt($(target_obj).data('option_price')) * parseInt($(target_obj).find('input[name="cart_count"]').val());

            });

            $('.tot_price .price em').html(number_format(product_amt+delivery_amt));
            $('.price_detail .detail_tot_price_wrap em').html(number_format(product_amt+delivery_amt));
            $('.price_detail .detail_product_price_wrap em').html(number_format(product_amt));
            $('.price_detail .detail_delivery_price_wrap em').html(number_format(delivery_amt));

        }

    }

    // shop_id : 수빈샵의 snsform계정
    // a_session_id : 장바구니 키 (유니크) max(64byte)
    // a_code : (수빈샵 제휴코드 - 'D005' 고정)
    // a_campaign : 캠페인
    // a_referer : 레퍼러
    // partner_buyer_id : 수빈샵 구매자 ID
    // toggle_header : 헤더노출여부
    // a_basket_info : 장바구니 결제 데이터
        // - buy_count: 옵션상품이 아닌 구매 시 구매 수량
        // - item_name: 상품명
        // - item_no: 상품번호
        // - option_info : 상품옵션데이터
            // - option_supply : 옵션공급가
            // - option_count: 옵션 구매 수량
            // - option_plus: 추가옵션여부
            // - option_price: 옵션가격
            // - option_type: 옵션타입
            // - option_name: 옵션명

    $(function(){


        $('.cart_soldout_wrap .cart_soldout a').on('click',function(){

            proc_del($(this).parent().parent().parent().data('cart_id'));

        });

        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.cart_wrap').css({'min-height': min_height+'px'});
        }

        $('.cart_btm .main_btn').on('click',function(){
            go_home();
        });

        $('.cart_btm .pay_btn').on('click',function(){

            if( $('.sub_prod_list.on').length < 1 ){
                showToast('주문 할 상품을 선택해주세요 !');
                return false;
            }

            var basket_array = new Array();
            var option_array = new Array();
            var basket_inner = new Object();
            //var last_seq = 0;
            var last_seq = $('.sub_prod_list:eq(0)').data('p_order_code');

            $('.sub_prod_list.on').each(function(k,r){

                var next_num        = parseInt(k)+1;
                var curr_seq        = $('.sub_prod_list.on:eq('+next_num+')').data('p_order_code');
                var option_inner    = new Object();

                option_inner.option_supply  = $(this).data('option_supply');
                option_inner.option_count   = parseInt($(this).find('input[name="cart_count"]').val());//$(this).data('option_count');
                option_inner.option_plus    = $(this).data('option_plus');
                option_inner.option_price   = $(this).data('option_price');
                option_inner.option_type    = $(this).data('option_type');

                <?if(is_app()){ //앱에서 옵션명에 특수문자(+)가 있는경우 문제 발생여지가 있어 urlencoding처리 ?>
                option_inner.option_name    = encodeURIComponent($(this).data('option_name'));
                <?}else{?>
                option_inner.option_name    = $(this).data('option_name');
                <?}?>


                option_array.push(option_inner);

                if( last_seq != curr_seq || $('.sub_prod_list').find('input[name="num_check"]:checked').length == 1 ){


                    basket_inner.item_name      = $(this).data('p_name');
                    basket_inner.item_no        = $(this).data('p_order_code').toString();
                    basket_inner.buy_count      = 0;
                    basket_inner.option_info    = option_array;

                    basket_array.push(basket_inner);

                    option_array = new Array();
                    basket_inner = new Object();

                    last_seq     = curr_seq;

                }

            });

            if(basket_array.length == 1) $('form[name="cart_form"]').attr('action',order_url);
            else $('form[name="cart_form"]').attr('action',cart_url);

            var basket_info = JSON.stringify(basket_array);
            $('form[name="cart_form"] input[name="a_basket_info"]').val(basket_info);

            <?if(is_app() == true){ ?>

            var form_data = '';
            var form_url = '';

            if(basket_array.length == 1) {
                <?
                /**
                 * @date 200227
                 * @modify 황기석
                 * @TODO 옵션없는 상품 고려안됨
                 */
                ?>

                var option_arr  = [];


                $(basket_array[0].option_info).each(function(k,r){
                    delete r.option_type;
                    r.option_seller_supply = 0;
                    option_arr.push(r);
                });

                $('form[name="order_form"] input[name="a_buy_count"]').val(0);
                $('form[name="order_form"] input[name="a_option_info"]').val(JSON.stringify(option_arr));
                $('form[name="order_form"] input[name="item_no"]').val(basket_array[0].item_no);

                form_data = decodeURIComponent($('form[name="order_form"]').serialize());

                form_url = form_order_url;

            }else{

                form_data = decodeURIComponent($('form[name="cart_form"]').serialize());
                form_url = form_cart_url;

            }

            app_cart_order(form_url,form_data);

            <?}else{?>

            $('form[name="cart_form"]').submit();

            <?}?>

        });

        $('.cart_m').on('click',function(){

            var parent_obj = $(this).parent().parent().parent().parent().parent();
            var parent_obj2 = $(this).parent().parent();

            var option_count = parseInt($(parent_obj).find('input[name="cart_count"]').val())-1;

            if(option_count < 1){

                return false;

            }else{

                var ret = chkMod(parent_obj,option_count);

                if(ret != true){

                    $(parent_obj).find('.cont .option_count').html(option_count);
                    $(parent_obj).attr('data-option_count',option_count);
                    $(parent_obj).find('input[name="cart_count"]').val(option_count);

                    var option_price = parseInt($(parent_obj).data('option_price'));
                    $(parent_obj2).find('.cart_price em').html(number_format(option_count*option_price));

                    cart_calc();

                }
            }
        });

        $('.cart_p').on('click',function(){

            var parent_obj  = $(this).parent().parent().parent().parent().parent();
            var parent_obj2 = $(this).parent().parent();

            var option_count = parseInt($(parent_obj).find('input[name="cart_count"]').val())+1;

            if(option_count > 999) {
                return false
            }else {

                var ret = chkMod(parent_obj,option_count);

                if(ret != true){

                    $(parent_obj).find('.cont .option_count').html(option_count);
                    $(parent_obj).attr('data-option_count',option_count);
                    $(parent_obj).find('input[name="cart_count"]').val(option_count);

                    var option_price = parseInt($(parent_obj).data('option_price'));
                    $(parent_obj2).find('.cart_price em').html(number_format(option_count*option_price));

                    cart_calc();

                }

            }

        });

        $('.empty-space').css('height', parseInt($('.cart_btm').height())+ 'px'); //하단 fixed영역 추가

        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue'
        });

        $('input[type="checkbox"]').not('.all_check').on('ifChanged',function(){

            $(this).parent().parent().parent().toggleClass('on');

            cart_calc();

            var tot_cart = $('input[type="checkbox"]').not('.all_check').length;
            var check_cnt = 0;
            $('input[type="checkbox"]').not('.all_check').each(function(){
                if( $(this).is(':checked') == true ) check_cnt++;
            });

            if(tot_cart == check_cnt) $('.all_check').iCheck('check');
            else $('.all_check').iCheck('uncheck');

        });



        $('.price_detail_expended').on('click',function(){

            $(this).toggleClass('on');

            if($(this).hasClass('on') == true){
                $('.price_detail ul').show()
            }else{
                $('.price_detail ul').hide()
            }

        });

    });

    $(document).on('ifClicked','.all_check',function(){
        chkAll();
    });


    function chkAll(){

        if( !$('.num_check').length ) {
            return false;
        }

        var checked = $('.all_check').prop('checked');
        if( checked ) {
            $('.num_check').not('[disabled]').iCheck('uncheck');
        }
        else {
            $('.num_check').not('[disabled]').iCheck('check');
        }

    }

    function chkMod(target_obj , cnt){

        var arr = [];
        var obj = new Object();

        obj.cart_id = $(target_obj).data('cart_id');
        obj.cart_count = cnt ;

        arr.push(obj);

        var bRet = false;

        $.ajax({
            url : '/cart/save_proc',
            data : {data:arr},
            type : 'post',
            dataType : 'json',
            async : false,
            success : function (result) {

                if( result.message ) {
                    alert(result.message);
                }

                if( result.status == status_code['error'] ) {
                    //location.reload();

                    bRet = true
                }

            }

        });

        return bRet;
    }

    function chkDel(){

        if( !$('.num_check:checked').length ) {
            alert('삭제할 상품을 선택해주세요 !');
            return false;
        }

        var arr = [];

        $.each($('.num_check:checked') , function(){
            arr.push($(this).val());
        });

        if(confirm('선택하신 상품을 장바구니에서 삭제하시겠습니까 ?') == false) return false;

        // show_loader();

        proc_del(arr);

    }

    function proc_del(data){

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {cart_id:data},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }
                if( result.status == status_code['success'] ) {
                    location.reload();
                }
            },
            complete : function() {
                // hide_loader();
            }
        });

    }

</script>