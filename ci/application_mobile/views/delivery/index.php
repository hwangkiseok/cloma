<?php //link_src_html("/plugins/icheck/skins/square/blue.css", "css"); ?>
<?php //link_src_html("/plugins/icheck/icheck.min.js", "js"); ?>

<div class="box delivery">

    <div class="box-in">

        <div class="date_set">
            <ul>
                <li class="active" data-type="1m">1개월</li>
                <li data-type="6m">6개월</li>
                <li data-type="12m">12개월</li>
            </ul>
            <div class="clear"></div>
        </div>

        <div class="delivery_list">

            <div class="clear"></div>

            <?if(count($delivery_list) > 0){?>

                <? foreach ($delivery_list as $k => $r) { $aListImage = $r['img_url']; ?>

                    <div class="sub_prod_list delivery_row" style="margin-bottom: 15px;">

                        <div class="order-tit"> 주문번호 : <em class="no_font"><?=$r['trade_no']?></em> </div>
                        <!--                        <div class="chk fl"><input type="checkbox" class="num_check" value="--><?//=$r['p_num']?><!--" /></div>-->
                        <div class="img fl"><img src="<?=$aListImage?>" width="100%" alt="<?=$r['item_name']?>" /></div>
                        <div class="cont fl" >
                            <ul style="line-height: 20px;">
                                <li class="p_name"><?=$r['item_name']?></li>
                                <li><em class="no_font"><?=number_format($r['buy_amt'])?></em>원<!-- | <em class="no_font"><?=number_format($r['buy_count'])?></em>개--></li>
                                <li class="opt">주문일 : <em class="no_font" style="letter-spacing: -.1pt;"><?=view_date_format(rtrim($r['register_date'],'.0'),3)?></em></li>

                                <?if(empty($r['option_info']) == false){?>
                                    <li class="opt" style="margin-bottom: 10px;">
                                        <span class="fl">옵션&nbsp;:&nbsp;</span>
                                        <span class="fl">
                                            <? foreach ($r['option_info'] as $kk => $rr) {?>
                                                <?=$kk > 0 ? '<br>' : ''?><?=$rr['option_name']?>
                                            <?}?>
                                        </span>
                                        <div class="clear"></div>
                                    </li>
                                <?}?>

                                <li class="btn_area">

                                    <?if(empty($aOrderCancelLists[$r['trade_no']]) == false){?>

                                        <? if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '166'){?>
                                            <a class="btn btn-border-red btn-thin fl"><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?></a>
                                        <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '167'
                                                || $aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '168'
                                        ){?>
                                            <a class="btn btn-border-purple btn-thin fl"><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?></a>
                                        <?}else{?>

                                            <?if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '66'){?>
                                                <a class="btn btn-border-blue btn-thin fl" >취소처리</a>
                                            <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '67'){?>
                                                <a class="btn btn-border-blue btn-thin fl" >교환처리</a>
                                            <?}else if($aOrderCancelLists[$r['trade_no']]['after_status_cd'] == '68'){?>
                                                <a class="btn btn-border-blue btn-thin fl" >반품처리</a>
                                            <?}?>

                                            <!--<a class="btn btn-border-blue btn-thin fl" ><?=$this->config->item($aOrderCancelLists[$r['trade_no']]['after_status_cd'],'form_status_cd')?> </a>-->
                                        <?}?>


                                    <?}else{ //취소정보가 없는경우?>

                                        <? if($r['status_cd'] == '66'){?>
                                            <a class="btn btn-border-red btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                                        <?}else if($r['status_cd'] == '67' || $r['status_cd'] == '68'){?>
                                            <a class="btn btn-border-purple btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                                        <?} else{?>
                                            <a class="btn btn-border-blue btn-thin fl" ><?=$this->config->item($r['status_cd'],'form_status_cd')?> </a>
                                        <?}?>

                                    <?}?>
                                    <a role="button" style="font-weight: normal;" href="#none" onclick="go_link('/delivery/detail/?tn=<?=$r['trade_no']?>');" class="btn btn-gray btn-thin fl">상세보기</a>
                                    <div class="clear"></div>
                                </li>

                            </ul>

                        </div>
                        <div class="clear"></div>
                    </div>
                <? } ?>

            <?}else{?>

                <div class="delivery_empty">
                    <div class="empty-icon">
<!--                        <i></i>-->
                        <img src="<?=IMG_HTTP?>/images/empty_order_icon.png" alt style="margin-bottom: 10px;width: 20%; max-width: 60px;" />
                    </div>
                    <p>구매하신 상품이 없습니다.</p>
                    <br><br><br><br>
                    <p>주문/배송,취소/교환/반품내역 확인 불가 시</p>
                    <p><em style="text-decoration: underline;">고객센터</em>로 문의 주시기 바랍니다.</p>
                </div>

            <?}?>

        </div>

    </div>

</div>
<div class="btm_fix_area delivery_btm" style="height: 70px;">
<!--    <div class="fl order_cancel_btn">주문취소신청</div>-->
<!--    <div class="fl order_exchange_btn">교환신청</div>-->
<!--    <div class="clear"></div>-->
    <p class="btm_notice" style="">주문/배송, 취소/교환/반품 내역확인 불가시<br> <em>고객센터</em>로 문의주시기 바랍니다.</p>
</div>

<input type="hidden" name="date_type" value="1m" />
<input type="hidden" name="page" value="2" />
<input type="hidden" name="more"  value="1" />

<script type="text/javascript">

    var ajax_on  = false;
    var obj_name = 'delivery_list';

    function ajaxPaging(b = false){

        var p = $('input[name="page"]').val();
        isShowLoader = false;

        $.ajax({
            url : '<?=$this->page_link->list_ajax?>',
            data : {page : p , date_type : $('input[name="date_type"]').val() },
            type : 'post',
            async : false,
            dataType : 'html',
            success : function(result) {
                if(b == true) $('.'+obj_name).html(result);
                else {

                    if($(result).html().indexOf('empty-icon') == -1) $('.'+obj_name).append(result);
                    else $('input[name="more"]').val(0);

                }
                $('input[name="page"]').val(parseInt(p) + 1);
            }

        });

    }

    $(function(){


        if(isApp == 'Y'){
            var min_height = $(window).outerHeight(true) - $('#header').outerHeight(true) ;//- $('#footer').outerHeight(true);
            $('.delivery').css({'min-height': min_height+'px'});
        }

        $('.date_set li').on('click',function(){
            $('.date_set li').removeClass('active');
            $(this).addClass('active');
            var type = $(this).data('type');

            $('input[name="date_type"]').val(type);
            $('input[name="more"]').val(1);
            $('input[name="page"]').val(1);
            ajaxPaging(true);
        });

        $(window).scroll(function(){

            var more = $('input[name="more"]').val();

            if(more == 0) return false; //리스트 end
            if(ajax_on == true ) return false; //ajax 중인경우 return

            ajax_on = true;

            var x = parseInt($(this).scrollTop());
            var h = parseInt($('body').height()) - 200;
            var chkH =  parseInt($(window).outerHeight(true)) ;

            if( h < x +chkH ) ajaxPaging();

            ajax_on = false;

        });


        $('.empty-space').css('height', parseInt($('.delivery_btm').height())+ 'px'); //하단 fixed영역 추가


    });
</script>


