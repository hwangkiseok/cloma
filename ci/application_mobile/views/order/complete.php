
<style>
    .order_complete_wrap .tit {margin: -8px;border-bottom: 1px solid #ddd; text-align: center;line-height: 60px;height: 60px;vertical-align: middle;}
    .order_complete_wrap .tit span {
        vertical-align: top;
        line-height: 60px;
        display: inline-block;
        font-weight: bold;
        font-size: 15px;
    }
    .order_complete_wrap .tit i {
        background-size: 125px!important;
        width: 35px;
        height: 35px;
        background: url('http://www.cloma.co.kr/images/mb_icon_set_img.png') no-repeat -89px -355px;
        display: inline-block;
        margin-top: 12px;
    }

</style>
<div class="box">

    <div class="box-in order_complete_wrap">

        <div class="tit">
            <i></i><span
                    style="letter-spacing: -.5px">고객님의 주문이 정상적으로 완료되었습니다.</span>
        </div>

        <div class="clear" style="height: 8px;"></div>
        <div class="order_product_info" style="margin-top: 20px">

            <h2>주문내역</h2>
            <?
            $tot_amt = 0;

            if($_GET['v'] == '1'){
//                zsView($aOrderInfo);
            }
            foreach ($aOrderInfo as $r) {

                if($r['item_name'] == '장바구니 배송비') continue;

                $r['option_info'] = json_decode($r['option_list'],true);
                $tot_amt         += (int)$r['buy_amt'];
                ?>

                <div class="sub_prod_list delivery_row" style="margin-bottom: 15px;">

                    <div class="img fl"><img src="<?=$r['p_today_image']?>" width="100%" alt="alt" /></div>
                    <div class="cont fl" >
                        <ul style="line-height: 20px;">
                            <li class="p_name"><?=$r['item_name']?></li>
                            <li style="font-size: 15px;"><em class="no_font" style="letter-spacing: 0;" ><?=number_format((int)$r['buy_amt']-(int)$r['delivery_amt'])?></em>원<!-- | <em class="no_font"><?=number_format($r['buy_count'])?></em>개--></li>
                            <li class="opt">주문일 : <em class="no_font" style="letter-spacing: -.1pt;"><?=rtrim($r['register_date'],'.0')?></em></li>

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

                            <!---
                        <li class="btn_area">

                            <? if($r['status_cd'] == '66'){?>
                                <a class="btn btn-border-red btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                            <?}else if($r['status_cd'] == '67' || $r['status_cd'] == '68'){?>
                                <a class="btn btn-border-purple btn-thin fl"><?=$this->config->item($r['status_cd'],'form_status_cd')?></a>
                            <?} else{?>
                                <a class="btn btn-border-blue btn-thin fl" ><?=$this->config->item($r['status_cd'],'form_status_cd')?> </a>
                            <?}?>

                            <?if(is_app()){?>
                                <a role="button" style="font-weight: normal;" href="#none" onclick="appNewWin('<?=$this->config->item('default_http')?>/delivery/detail/?tn=<?=$r['trade_no']?>');" class="btn btn-gray btn-thin fl">상세보기</a>
                            <?}else{?>
                                <a role="button" style="font-weight: normal;" href="#none" onclick="go_link('<?=$this->config->item('default_http')?>/delivery/detail/?tn=<?=$r['trade_no']?>');" class="btn btn-gray btn-thin fl">상세보기</a>
                            <?}?>

                            <div class="clear"></div>
                        </li>
                        -->
                        </ul>

                    </div>
                    <div class="clear"></div>
                </div>

            <? } ?>

        </div>

        <div class="tno_info">
            <h2>주문번호 <span class="fr no_font"><?=$tno?></span></h2>
        </div>
        <div class="list_info vacc_info">

            <?if($aOrderInfo[0]['payway_cd'] == '3'){?>

                <h2 style="border-bottom: 1px solid #ddd; margin-bottom: 15px;padding-bottom: 10px">무통장 입금정보</h2>

                <ul>
                    <li>
                        <span class="fl">&middot; 입금은행</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_bank_cd']?></span>
                    </li>

                    <li>
                        <span class="fl">&middot; 입금계좌</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_acct_no']?></span>
                    </li>
                    <li>
                        <span class="fl">&middot; 예금주</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_acct_name']?></span>
                    </li>
                    <li>
                        <span class="fl">&middot; 입금자명</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_check_name']?></span>
                    </li>

                </ul>

            <?}?>

            <div class="highlight">

                <span class="fl">
                    <button class="btn btn-default" style="padding: 10px 30px;border-radius: 20px;font-weight: normal">결제금액</button>
                </span>
                <span class="fr no_font" style="font-weight: bold;font-size: 18px">총 <?=number_format($aSnsformOrderInfo['total_buy_amt'])?> 원</span>
                <div class="clear"></div>
                <div class="complete_price_detail">
                    <p>상품금액 <span><?=number_format($aSnsformOrderInfo['total_item_amt'])?>원</span></p>
                    <p>배송비 <span><?=number_format($aSnsformOrderInfo['total_delivery_amt'])?>원</span></p>
                </div>

            </div>

        </div>

        <div class="list_info delivery_info">
            <h2 style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom: 10px ">배송정보</h2>

            <ul>
                <li>
                    <span class="fl">&middot; 수령자</span>
                    <span class="fr"><?=$aOrderInfo[0]['receiver_name']?></span>
                </li>

                <li>
                    <span class="fl">&middot; 배송지</span>
                    <span class="fr" style="width: 65%;display: inline-block;text-align: right;"><?=$aOrderInfo[0]['receiver_addr1']. ' ' . $aOrderInfo[0]['receiver_addr2']?></span>
                </li>
                <li>
                    <span class="fl">&middot; 연락처 </span>
                    <span class="fr"><?=$aOrderInfo[0]['receiver_tel']?></span>
                </li>
                <li>
                    <span class="fl">&middot; 배송메모</span>
                    <span class="fr"><?=$aOrderInfo[0]['order_memo']?></span>
                </li>
            </ul>

        </div>

    </div>

</div>


<div class="btm_fix_area _btm">
    <div class="fl main_btn">메인바로가기</div>
    <div class="fl delivery_btn">주문내역보기</div>
</div>

<script type="text/javascript">

    $(function(){

        $('.main_btn').on('click',function(){
            go_home();
        });

        $('.delivery_btn').on('click',function(){
            app_go_sub('delivery');
        });

    });

</script>

<? if( is_app() == true ) {?>

    <script type="text/javascript">

        $(function(){

            var a_receiver_name = '<?=$aOrderInfo[0]['receiver_name']?>';
            var a_receiver_hhp = '<?=$aOrderInfo[0]['receiver_tel']?>';
            var a_receiver_zip = '<?=$aOrderInfo[0]['receiver_zip']?>';
            var a_receiver_addr1 = '<?=$aOrderInfo[0]['receiver_addr1']?>';
            var a_receiver_addr2 = '<?=$aOrderInfo[0]['receiver_addr2']?>';
            var buyer_name      = '<?=$aOrderInfo[0]['buyer_name']?>';
            var buyer_hhp       = '<?=$aOrderInfo[0]['buyer_hhp']?>';

            appSavePrefSetting('a_receiver_name' , a_receiver_name);
            appSavePrefSetting('a_receiver_hhp' , a_receiver_hhp);
            appSavePrefSetting('a_receiver_zip' , a_receiver_zip);
            appSavePrefSetting('a_receiver_addr1' , a_receiver_addr1);
            appSavePrefSetting('a_receiver_addr2' , a_receiver_addr2);
            appSavePrefSetting('a_buyer_name' , buyer_name);
            appSavePrefSetting('a_buyer_hhp' , buyer_hhp);

        });

    </script>

<? } ?>