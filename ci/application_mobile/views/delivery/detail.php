<style>
    .delivery_detail .expended .arrow-bottom {display: inline-block; width: 16px; height: 16px; background-size: 80px!important; background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -4px -152px; }
    .delivery_detail .expended .arrow-top {display: inline-block; width: 16px; height: 16px; background-size: 80px!important; background: url(https://www.cloma.co.kr/images/mb_icon_set_img.png) no-repeat -22px -152px; }
</style>
<div class="box no-before">
    <div class="box-in delivery_detail" style="padding-top: 0;padding-bottom: 0">

        <div class="expended" role="button" data-expended="1">
            <span class="fl tit">주문정보</span>
            <span class="fr"><i class="arrow-top"></i></span>
        </div>
        <ul class="expended_detail" data-seq="1" style="display: block">
            <li>
                <span class="fl">주문번호</span>
                <span class="fr"><em class="no_font"><?=$aSnsformOrderInfo['trade_no']?></em></span>

            </li>
            <li>
                <span class="fl">주문일자</span>
                <span class="fr"><em class="no_font"><?=rtrim($aSnsformOrderInfo['register_date'],'.0')?></em></span>
            </li>
            <li>
                <span class="fl">주문자</span>
                <span class="fr"><?=$aSnsformOrderInfo['buyer_name']?></span>
            </li>
            <li>
                <span class="fl">주문처리상태</span>
                <?/* snsform 주문상태 값과 옷쟁이들 주문상태 값의 문제로 snsform 상태값이 66이상 취소 인경우 1을 추가*/?>
                <?if($aSnsformOrderInfo['status_cd'] >= 66){?>
                    <span class="fr"><?=$this->config->item('1'.$aSnsformOrderInfo['status_cd'],'form_status_cd')?></span>
                <?}else{?>
                    <span class="fr"><?=$this->config->item($aSnsformOrderInfo['status_cd'],'form_status_cd')?></span>
                <?}?>
            </li>
        </ul>

        <div class="expended" role="button" data-expended="2">
            <span class="fl tit">주문상품</span>
            <span class="fr"><i class="arrow-top"></i></span>
        </div>

        <ul class="expended_detail" data-seq="2" style="display: block">

            <?$tot_cnt = 0;  foreach ($aSnsformOrderInfo['option_info'] as $kk => $rr) { $tot_cnt += $rr['option_count']; }?>

            <li class="product_info">
                <div>
                    <img src="<?=$aOrderInfo['p_today_image']?>" class="fl" />
                    <ul class="fl " style="">
                        <li>상품명 : <?=$aOrderInfo['item_name']?></li>
                        <li>결제금액 : <em class="no_font"><?=number_format((int)$rr['option_price'])?></em>원</li>
                        <li>수량 : <em class="no_font"><?=number_format($tot_cnt)?></em>개</li>

                        <?if(empty($aSnsformOrderInfo['option_info']) == false){?>
                            <li class="opt" style="margin-bottom: 10px;">
                                <span class="fl option_naming">옵션&nbsp;:&nbsp;</span>
                                <span class="fl option_info">
                                        <? foreach ($aSnsformOrderInfo['option_info'] as $kk => $rr) {?>
                                            <?=$kk > 0 ? '<br>' : ''?><?=$rr['option_name']?>
                                        <?}?>
                                    </span>
                                <div class="clear"></div>
                            </li>
                        <?}?>

                    </ul>
                </div>
            </li>

            <li class="product_info">

                <?if($aOrderInfo['after_status_cd'] > 0){ //취소신청 요청?>

                    <?if($aOrderInfo['after_status_cd'] == '66'){?>
                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 취소신청 </span>
                            <span class="fr"> <button class="btn btn-border-gray zs-cp">처리중</button> </span>
                        </p>
                    <?}else if($aOrderInfo['after_status_cd'] == '67'){?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 교환신청 </span>
                            <span class="fr"> <button class="btn btn-border-gray zs-cp">처리중</button> </span>
                        </p>

                    <?}else if($aOrderInfo['after_status_cd'] == '68'){?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 반품신청 </span>
                            <span class="fr"> <button class="btn btn-border-gray zs-cp">처리중</button> </span>
                        </p>

                    <?}else if($aOrderInfo['after_status_cd'] == '166'){?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 취소신청 </span>
                            <span class="fr"> <button class="btn btn-border-red">완료</button> </span>
                        </p>
                    <?}else if($aOrderInfo['after_status_cd'] == '167'){?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 교환신청 </span>
                            <span class="fr"> <button class="btn btn-border-red">완료</button> </span>
                        </p>

                    <?}else if($aOrderInfo['after_status_cd'] == '168'){?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 반품신청 </span>
                            <span class="fr"> <button class="btn btn-border-red" >완료</button> </span>
                        </p>

                    <?}?>

                <?}else{ ?>

                    <? $cancel_class = 'popCancel2';//if($_SESSION['session_m_num'] == 74 || $_SESSION['session_m_num'] == 55){ $cancel_class = 'popCancel2'; } else { $cancel_class = 'popCancel'; }?>

                    <? if($aSnsformOrderInfo['status_cd'] == '61' ){?>
                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 입금확인 중 </span>
                            <span class="fr"> <button class="btn btn-border-red zs-cp <?=$cancel_class?>" data-type="66">주문취소</button> </span>
                        </p>
                    <?}else if($aSnsformOrderInfo['status_cd'] == '62' || $aSnsformOrderInfo['status_cd'] == '63'){?>
                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 배송준비중 </span>
                            <span class="fr"> <button class="btn btn-border-red zs-cp <?=$cancel_class?>" data-type="66">주문취소</button> </span>
                        </p>
                    <?}else if($aSnsformOrderInfo['status_cd'] == '64'){ ?>

                        <p style="line-height: 34px;vertical-align: middle">
                            <?if($aSnsformOrderInfo['del_comp_cd'] == '04'){?>
                                <span class="fl"> 배송중 </span>
                                <span class="fr">
                                    <button class="btn btn-border-purple zs-cp oudtside" data-invoice_no="<?=$aSnsformOrderInfo['invoice_no']?>" data-company="<?=$this->config->item($aSnsformOrderInfo['del_comp_cd'],'del_comp_cd')?>">배송조회</button>
                                    <!--
                                    <button class="btn btn-border-blue zs-cp <?=$cancel_class?>" data-type="67">교환신청</button>
                                    <button class="btn btn-border-blue zs-cp <?=$cancel_class?>" data-type="68">반품신청</button>
                                    -->
                                </span>
                            <?}else{?>
                                <span class="fl"> <?=$this->config->item($aSnsformOrderInfo['del_comp_cd'],'del_comp_cd')?> </span>
                                <span class="fr"> <button class="btn btn-border-purple zs-cp oudtside no_font" id="copyClipboard" data-clipboard-text="<?=$aSnsformOrderInfo['invoice_no']?>" data-invoice_no="<?=$aSnsformOrderInfo['invoice_no']?>" data-company="<?=$this->config->item($aSnsformOrderInfo['del_comp_cd'],'del_comp_cd')?>"><?=$aSnsformOrderInfo['invoice_no']?></button> </span>
                            <?}?>
                        </p>

                    <?}else if($aSnsformOrderInfo['status_cd'] == '65'){?>
                        <p style="line-height: 34px;vertical-align: middle">
                            <span class="fl"> 배송완료 </span>
                            <span class="fr">
                                <button class="btn btn-border-blue zs-cp <?=$cancel_class?>" data-type="67">교환신청</button>
                                <button class="btn btn-border-blue zs-cp <?=$cancel_class?>" data-type="68">반품신청</button>
                            </span>
                        </p>
                    <?}?>
                <?}?>
            </li>
        </ul>

        <?// zsView($aSnsformOrderInfo); ?>

        <div class="expended" role="button" data-expended="4">
            <span class="fl tit">결제정보</span>
            <span class="fr"><i class="arrow-top"></i></span>
        </div>
        <ul class="expended_detail" data-seq="4" style="display: block">

            <?if($aOrderInfo['tot_buy_cnt'] > 0){?>
            <li>
                <span class="fl">구매상품 정보</span>
                <span class="fr">
                    <?=$aOrderInfo['item_name']?> 외 <?=(int)$aOrderInfo['tot_buy_cnt']-1?>건 묶음 구매
                </span>

            </li>
            <?}?>
            <li>
                <span class="fl">구매상품 금액</span>
                <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_item_amt'])?></em>원</span>
            </li>
            <?if($aSnsformOrderInfo['total_delivery_amt'] > 0){?>
                <li>
                    <span class="fl">배송비</span>
                    <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_delivery_amt'])?></em>원</span>
                </li>
            <?}?>
            <li>
                <span class="fl">합계</span>
                <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_buy_amt'])?></em>원</span>
            </li>
            <li>
                <span class="fl">결제수단</span>
                <span class="fr"><?=$this->config->item($aSnsformOrderInfo['payway_cd'],'form_payway_cd')?></span>
            </li>

            <? if($aSnsformOrderInfo['payway_cd'] == '3' || $aSnsformOrderInfo['payway_cd'] == '7'){?>

                <?if($aSnsformOrderInfo['payway_cd'] != '7'){?>

                    <li>
                        <span class="fl">입금자</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_check_name']?></span>
                    </li>

                <?}?>

                <li>
                    <span class="fl">계좌은행</span>
                    <span class="fr"><?=$aSnsformOrderInfo['vcnt_bank_cd']?></span>
                </li>
                <?if($aSnsformOrderInfo['payway_cd'] != '7'){?>
                    <li>
                        <span class="fl">예금주</span>
                        <span class="fr"><?=$aSnsformOrderInfo['vcnt_acct_name']?></span>
                    </li>
                <?}else{?>
                    <li>
                        <span class="fl">예금주</span>
                        <span class="fr">SNS Form</span>
                    </li>
                <?}?>
                <li>
                    <span class="fl">계좌번호</span>
                    <span class="fr"><em class="no_font"><?=$aSnsformOrderInfo['vcnt_acct_no']?></em></span>
                </li>

            <? } ?>

            <?if($aSnsformOrderInfo['total_discount_amt'] > 0){?>
                <li>
                    <span class="fl">총 할인금액</span>
                    <span class="fr sig_col"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_discount_amt'])?></em>원</span>
                </li>
            <?} ?>

            <li>
                <span class="fl">총 결제금액</span>
                <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_buy_amt'])?></em>원</span>
            </li>
        </ul>

        <?if(
                $aOrderInfo['after_status_cd'] == '66' //취소
            ||  $aOrderInfo['after_status_cd'] == '67' //교환
            ||  $aOrderInfo['after_status_cd'] == '68' //반품
            ||  $aOrderInfo['after_status_cd'] == '166' //취소완료
            ||  $aOrderInfo['after_status_cd'] == '167' //교환완료
            ||  $aOrderInfo['after_status_cd'] == '168' //반품완료
        ){?>

        <div class="expended" role="button" data-expended="3">
            <span class="fl tit">취소/환불 정보</span>
            <span class="fr"><i class="arrow-bottom"></i></span>
        </div>
        <ul class="expended_detail" data-seq="3">
            <li>
                    <span class="fl">결제수단</span>
                    <span class="fr"><em class="no_font"><?=$this->config->item($aSnsformOrderInfo['payway_cd'],'form_payway_cd')?></em></span>
            </li>
            <li>
                    <span class="fl">총 상품금액</span>
                    <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_item_amt'])?></em>원</span>
            </li>
            <?if($aSnsformOrderInfo['total_delivery_amt'] > 0){?>
            <li>
                <span class="fl">배송비</span>
                <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_delivery_amt'])?></em>원</span>
            </li>
            <?}?>

            <li>
                <span class="fl" style="width: 130px!important; ">취소/교환/반품 사유</span>
                <span class="fr"><?=$this->config->item($aOrderInfo['cancel_gubun'],'order_cancel_gubun_admin')?></span>
            </li>
        </ul>
        <?}?>

        <div class="expended" role="button" data-expended="6">
            <span class="fl tit">배송정보</span>
            <span class="fr"><i class="arrow-bottom"></i></span>
        </div>

        <ul class="expended_detail" data-seq="6">

            <li>
                <span class="fl">받으시는분</span>
                <span class="fr"><?=$aSnsformOrderInfo['receiver_name']?></span>
            </li>
            <li>
                <span class="fl">우편번호</span>
                <span class="fr"><em class="no_font"><?=$aSnsformOrderInfo['receiver_zip']?></em></span>
            </li>
            <li>
                <span class="fl">주소</span>
                <span class="fr"><?=$aSnsformOrderInfo['receiver_addr']?></span>
            </li>
            <?if(empty($aSnsformOrderInfo['buyer_tel']) == false){?>
            <li>
                <span class="fl">연락처</span>
                <span class="fr"><em class="no_font"><?=$aSnsformOrderInfo['buyer_tel']?></em> </span>
            </li>
            <?}?>
            <li>
                <span class="fl">휴대전화</span>
                <span class="fr"><em class="no_font"><?=$aSnsformOrderInfo['buyer_hhp']?></em> </span>
            </li>
            <?if(empty($aSnsformOrderInfo['receiver_memo']) ==false){?>
                <li>
                    <span class="fl">배송메시지</span>
                    <span class="fr"><?=$aSnsformOrderInfo['receiver_memo']?></span>
                </li>
            <?}?>
        </ul>

        <?if(
                $aOrderInfo['after_status_cd'] == '66' //취소중
            ||  $aOrderInfo['after_status_cd'] == '67' //교환중
            ||  $aOrderInfo['after_status_cd'] == '68' //반품중
            ||  $aOrderInfo['after_status_cd'] == '167' //교환완료
            ||  $aOrderInfo['after_status_cd'] == '168' //반품완료
            ||  $aOrderInfo['after_status_cd'] == '168' //반품완료
        ){?>

        <div class="expended" role="button" data-expended="7">
            <span class="fl tit">환불계좌정보</span>
            <span class="fr"><i class="arrow-bottom"></i></span>
        </div>


        <ul class="expended_detail" data-seq="7">
            <li>
                <span class="fl">환불일자</span>
                <span class="fr">
                    <?if($aOrderInfo['proc_flag'] == 'Y'
                        && (
                                $aOrderInfo['after_status_cd'] == '166' //취소
                            ||  $aOrderInfo['after_status_cd'] == '167' //교환
                            ||  $aOrderInfo['after_status_cd'] == '168' //반품

                        )){ // 완료된경우?>
                        <em class="no_font"><?=view_date_format($aOrderInfo['proc_date'],3)?></em>
                        (<?=$this->config->item($aSnsformOrderInfo['after_status_cd'],'form_status_cd')?>)
                    <?}else{?>
                        처리중
                    <?}?>
                </span>
            </li>
            <li>
                <span class="fl">환불금액</span>
                <span class="fr"><em class="no_font"><?=number_format($aSnsformOrderInfo['total_buy_amt'])?></em>원</span>
            </li>
            <li>
                <span class="fl">환불수단</span>
                <span class="fr"><?=$this->config->item($aSnsformOrderInfo['payway_cd'],'form_payway_cd')?></span>
            </li>
            <?if($aSnsformOrderInfo['payway_cd'] == 3){?>

                <li>
                    <span class="fl">환불계좌정보</span>
                    <span class="fr payway3">

                        <span>예금주 : <?=$aOrderInfo['account_holder']?></span>
                        <span>은행 : <?=$this->config->item($aOrderInfo['account_bank'],'cancel_bank')?></span>
                        <span>계좌번호 : <?=$aOrderInfo['account_no']?></span>

                    </span>
                </li>

            <?}?>
        </ul>
        <?}?>
    </div>
</div>


<script type="text/javascript">

    $(function(){


        $('.popCancel2').on('click',function(e){

            var type    = $(this).data('type');
            var tn      = '<?=$tn?>';

            go_link('/order/cancel?tn='+tn+'&t='+type)

        });

        $('.popCancel').on('click',function(e){
            e.preventDefault();

            var type        = $(this).data('type');
            var container   = $('<div class="offer_area">');

            $(container).load('/delivery/cancel?tn=<?=$tn?>&t='+type);

            var tit = '';
            if(type == '66')  tit = '주문취소';
            else if(type == '67') tit = '교환';
            else if(type == '68') tit = '반품';

            modalPop.createPop(tit, container);
            modalPop.createButton('신청하기', 'btn btn-default btn-sm', function(){
                cancel_order_chk();
            });
            modalPop.createCloseButton('취소', 'btn btn-primary btn-sm');
            modalPop.show({'backdrop' : 'static'});

        });

        $('.oudtside').on('click',function(){
            var invoice_no = $(this).data('invoice_no');
            var company = $(this).data('company');
            var url = "";

            if(company == 'CJ대한통운'){

                var url = 'http://nplus.doortodoor.co.kr/web/detail.jsp?slipno='+invoice_no;
                go_link(url,'','Y');
                //go_link('/delivery/outside_detail?invoice_no='+invoice_no+'&company='+company);
            }else{
                //clipboard Copy
                var clipboard = new Clipboard('#copyClipboard');
                clipboard.on('success', function(e) {
                    if(isApp != 'Y') showToast('클립보드에 송장번호가 저장되었습니다.');
                    e.clearSelection();
                });
                clipboard.on('error', function(e) {
                    showToast('클립보드에 운송장번호가 저장이 실패하였습니다.');
                    e.clearSelection();
                });

            }

        });

        $('.expended').on('click',function(){
            var seq = $(this).data('expended');
            if(empty(seq) == true) return false;

            if($(this).find('i').hasClass('arrow-top') == true){
                $(this).find('i').removeClass('arrow-top').addClass('arrow-bottom');

            }else{
                $(this).find('i').removeClass('arrow-bottom').addClass('arrow-top');
            }

            if( $('ul.expended_detail[data-seq="'+seq+'"]').css('display')== 'none'){
                $('ul.expended_detail[data-seq="'+seq+'"]').show();
            }else{
                $('ul.expended_detail[data-seq="'+seq+'"]').hide();
            };

        });

    });

</script>
<script type="text/javascript" src="/js/clipboard.min.js"></script>