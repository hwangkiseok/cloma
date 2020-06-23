<?php link_src_html("/plugins/icheck/skins/square/blue.css", "css"); ?>
<?php link_src_html("/plugins/icheck/icheck.min.js", "js"); ?>
<form id="cancel_frm" name="cancel_frm" action="/order/cancel_proc_v2" method="post">

    <input type="hidden" name="trade_no" value="<?=$trade_no?>" />
    <input type="hidden" name="t" value="<?=$cancel_type?>" />
    <input type="hidden" name="payway_cd" value="<?=$aSnsformOrderInfo['payway_cd']?>" />
    <input type="hidden" name="status_cd" value="<?=$aSnsformOrderInfo['status_cd']?>" />

    <input type="hidden" name="refund_receiver_name" value="<?=$aOrderInfo['receiver_name']?>" />
    <input type="hidden" name="refund_receiver_tel" value="<?=number_only($aOrderInfo['receiver_tel'])?>" />
    <input type="hidden" name="refund_receiver_zip" value="<?=$aOrderInfo['receiver_zip']?>" />
    <input type="hidden" name="refund_receiver_addr1" value="<?=$aOrderInfo['receiver_addr1']?>" />
    <input type="hidden" name="refund_receiver_addr2" value="<?=$aOrderInfo['receiver_addr2']?>" />

    <?

    //금액 정리
    if(empty($aOrderInfo['m_trade_no']) == true){
        $tot_buy_amt    = $aOrderInfo['buy_amt'];
        $prod_buy_amt   = (int)$aOrderInfo['buy_amt'] - (int)$aOrderInfo['delivery_amt'];
        $del_amt        = $aOrderInfo['delivery_amt'];
    }else{
        if( $aLastOrderInfo['isLast'] == true ) { $aLastOrderInfo['data'] = array_shift($aLastOrderInfo['data']); //장바구니 > 마지막 주문인 경우
            $tot_buy_amt    = (int)$aOrderInfo['buy_amt']+(int)$aLastOrderInfo['data']['delivery_amt'];
            $prod_buy_amt   = $aOrderInfo['buy_amt'];
            $del_amt        = $aLastOrderInfo['data']['delivery_amt'];
        }else{
            $tot_buy_amt    = $aOrderInfo['buy_amt'];
            $prod_buy_amt   = $aOrderInfo['buy_amt'];
            $del_amt        = $aOrderInfo['cart_del_amt']; //환불금액계산시 사용
        }
    }

    //취소사유
    if($cancel_type == '66') $reason = $this->config->item("order_cancel_gubun");
    else if($cancel_type == '67') $reason = $this->config->item("order_exchange_gubun");
    else $reason = $this->config->item("order_refund_gubun");

    $option_list_arr = json_decode($aOrderInfo['option_list'],true);

    /*환불정보관련*/
    $isRefundView = false;
    if(in_array($aOrderInfo['payway_cd'] ,$this->config->item('refund_view_cd')) == true) $isRefundView = true; //무통장입금 / 가상계좌
    if( substr(number_only($aOrderInfo['register_date']) , 0 ,6) < date('Ym') && $aOrderInfo['payway_cd'] == 5 ) $isRefundView = true; //익월 휴대폰 결제
    
    ?>
    <input type="hidden" name="del_amt" value="<?=$del_amt?>" />
    <input type="hidden" name="tot_buy_amt" value="<?=$tot_buy_amt?>" />

    <div class="box order_cancel_wrap">

        <div class="box-in">
            
            <div class="order_block">
                <div>
                    <span class="fl"><label>주문번호</label></span>
                    <span class="fr"><em class="no_font"><?=$aOrderInfo['trade_no']?></em></span>
                    <div class="clear"></div>
                </div>

                <div>
                    <span class="fl"><label>주문일시</label></span>
                    <span class="fr"><em class="no_font"><?=$aOrderInfo['register_date']?></em></span>
                    <div class="clear"></div>
                </div>

            </div>

            <div class="order_block">
                <div>
                    <span class="fl"><label>상품명</label></span>
                    <span class="fr"><?=$aOrderInfo['item_name']?></span>
                    <div class="clear"></div>
                </div>

                <div>
                    <span class="fl"><label>옵션/수량</label></span>
                    <span class="fr">
                        <? foreach ($option_list_arr as $k => $r) {?>
                            <?=$r['option_name']?> / <em class="no_font"><?=number_format($r['option_count'])?></em> 개
                        <?}?>
                    </span>
                    <div class="clear"></div>
                </div>

            </div>

            <div class="order_block">

                <div>
                    <span class="fl"><label>총 결제 금액</label></span>
                    <span class="fr"><em class="no_font"><?=number_format($tot_buy_amt)?> 원</em></span>
                    <div class="clear"></div>
                </div>

                <div>
                    <span class="fl"><label>상품금액</label></span>
                    <span class="fr"><em class="no_font"><?=number_format($prod_buy_amt)?> 원</em></span>
                    <div class="clear"></div>
                </div>

                <?if(empty($aOrderInfo['m_trade_no']) == true || $aLastOrderInfo['isLast'] == true ){ //단일 주문 || 장바구니 마지막 주문 ?>

                <div>
                    <span class="fl"><label>배송비</label></span>
                    <span class="fr"><em class="no_font"><?=number_format($del_amt)?> 원</em></span>
                    <div class="clear"></div>
                </div>

                <?}else{ //장바구니 마지막 주문 x ?>

                <div>
                    <span class="fl"><label>배송비</label></span>
                    <span class="fr"><em class="no_font">0 원</em></span>
                    <div class="clear"></div>
                </div>
                <?}?>

            </div>

        </div>
    </div>

    <div class="box order_cancel_wrap">

        <div class="box-in">

            <div class="order_block">
                <div style="margin-bottom: 16px">
                    <span class="fl"><label><?=$tit_str?> 사유항목</label></span>
                    <div class="clear"></div>
                    <select name="cancel_gubun" title="cancel_gubun">
                        <?=get_select_option("항목을 선택해주세요.", $reason , $cancel_type=='68'?'A':'');?>
                    </select>
                </div>

                <div>
                    <span class="fl"><label>상세사유<?if($cancel_type == 67){?>/교환 옵션 입력<?}?></label></span>
                    <div class="clear"></div>
                    <textarea name="cancel_reason" title="cancel_reason" placeholder="상세사유를 기재해주세요." rows="5" style="width: 100%;" ></textarea>
                </div>

            </div>

        </div>

    </div>

    <? if($cancel_type != '66'){ ?>

    <div class="box order_cancel_wrap">

        <div class="box-in">
            <div class="order_block" >
                <label><input type="radio" name="del_type" title="del_type" value="direct"> 반품 택배 직접 발송</label>
            </div>

            <div class="order_block">
                <label><input type="radio" name="del_type" title="del_type" value="request" checked> 반품 회수 택배 요청</label>
            </div>

            <div class="order_block del_type_addr">
                <p>
                    <span class="user_info"><?=$aOrderInfo['receiver_name']?> <em class="no_font">( <?=$aOrderInfo['receiver_tel']?> )</em></span>
                    <span class="fr"><button class="srh_addr">변경</button></span>
                </p>
                <div class="clear"></div>
                <p class="juso1">(<?=$aOrderInfo['receiver_zip']?>) <?=$aOrderInfo['receiver_addr1']?></p>
                <p class="juso2"><?=$aOrderInfo['receiver_addr2']?></p>
            </div>

        </div>
    </div>

    <?}?>

    <?if(($cancel_type == 66 || $cancel_type == 68) && $isRefundView == true && $aOrderInfo['status_cd'] > 61){?>

    <div class="box order_cancel_wrap">

        <div class="box-in">

            <div class="order_block">

                <label class="refund_tit"><i></i> 환불정보입력</label>

                <div style="margin-bottom: 16px">
                    <span class="fl"><label>예금주</label></span>
                    <div class="clear"></div>
                    <input type="text" name="account_holder" placeholder="예금주명을 기재해주세요" title="account_holder" value="">
                </div>

                <div style="margin-bottom: 16px">
                    <span class="fl"><label>은행명</label></span>
                    <div class="clear"></div>
                    <select name="account_bank" title="account_bank" style="width: 100%;padding: 8px">
                        <?=get_select_option("은행을 선택해주세요.", $this->config->item("cancel_bank"));?>
                    </select>
                </div>

                <div>
                    <span class="fl"><label>계좌번호</label></span>
                    <div class="clear"></div>
                    <input type="number" name="account_no" placeholder="계좌번호를 기재해주세요" title="account_holder" value="">
                </div>

            </div>

        </div>
    </div>

    <?}?>

    <div class="box order_cancel_wrap refund_wrap">

        <div class="box-in">

            <? if($cancel_type == 66 || $cancel_type == 68){?>
            <div class="order_block">
                <?if($cancel_type == 68){?>
                    <div>
                        <span class="fl"><label>총 결제 금액</label></span>
                        <span class="fr"><em class="no_font buy_amt"><?=number_format($tot_buy_amt)?></em> 원</span>
                        <div class="clear"></div>
                    </div>
                    <div class="refund_del_amt">
                        <span class="fl"><label>반품 배송비</label></span>
                        <span class="fr"><em class="no_font del_amt">- <?=number_format($del_amt)?></em> 원</span>
                        <div class="clear"></div>
                    </div>
                    <div>
                        <span class="fl"><label>환불 예상 금액</label></span>
                        <span class="fr"><em class="no_font esti_amt sig-col"><?=number_format((int)$tot_buy_amt - (int)$del_amt)?></em> 원</span>
                        <div class="clear"></div>
                    </div>
                <?}else{?>
                    <div>
                        <span class="fl"><label>환불 예상 금액</label></span>
                        <span class="fr"><em class="no_font esti_amt sig-col"><?=number_format((int)$tot_buy_amt)?></em> 원</span>
                        <div class="clear"></div>
                    </div>
                <?}?>
            </div>
            <? }?>

            <div class="order_block block_warning">
                <?if($cancel_type == '66'){?>
                    <p>* 상품이 이미 출고되었을 경우 취소가 불가할 수 있습니다.</p>
                    <p>* 환불 계좌정보와 결제자가 일치하지 않을 경우, 환불이 지연되거나 불가할 수 있습니다.</p>
                <?}else if($cancel_type == '67'){?>
                    <p>* 반품 택배 직접 발송을 선택하셨을 경우, 반드시 CJ택배를 통해 선불로 발송해 주세요. 타택배로 발송하시게 되면 추가 택배비 차감이 발생할 수 있습니다.</p>
                    <p>* 교환을 요청하신 옵션의 재고가 없을 경우 상품 교환이 불가할 수 있습니다.</p>
                <?}else if($cancel_type == '68'){?>
                    <p>* 사유에 따라 환불 금액에서 배송비가 차감될 수 있습니다.</p>
                    <p>* 반품 택배 직접 발송을 선택하셨을 경우, 반드시 CJ택배를 통해 선불로 발송해 주세요. 타택배로 발송하시게 되면 추가 택배비 차감이 발생할 수 있습니다.</p>
                    <p>* 환불 계좌정보와 결제자가 일치하지 않을 경우, 환불이 지연되거나 불가할 수 있습니다.</p>
                <?}?>
            </div>

        </div>
    </div>

    <div class="btm-bnt-area">
        <button type="submit" class="fl cancel_req_btn">신청</button>
        <button type="button" class="fr cancel_order_btn">취소</button>
        <div class="clear"></div>
    </div>

</form>

<script type="text/javascript">

    $(function(){

        $('.cancel_order_btn').on('click',function(e){
            e.preventDefault();
            history.back(-1);
        })

        $('#cancel_frm').on('submit',function(e){

            if( $('select[name="cancel_gubun"]').length > 0 ){
                if(empty($('select[name="cancel_gubun"]').val()) == true){
                    alert('취소 사유를 선택해 주세요.');
                    $('select[name="cancel_gubun"]').focus();
                    return false;
                }
            }

            if( $('textarea[name="cancel_reason"]').length > 0 ){
                if(empty($('textarea[name="cancel_reason"]').val()) == true){
                    alert('상세 사유를 입력해 주세요.');
                    $('select[name="cancel_reason"]').focus();
                    return false;
                }
            }

            if($('input[name="account_holder"]').length > 0){

                if (    empty($('input[name="account_holder"]').val()) == true
                    ||  empty($('input[name="account_bank"]').val()) == true
                    ||  empty($('input[name="account_no"]').val()) == true
                ){
                    alert('환불 정보를 정확하게 입력해 주세요.');
                    return false;
                }

            }

            if( $('input[name="del_type"]').length > 0 ){
                if(empty($('input[name="del_type"]:checked').val()) == true){
                    alert('반송 택배 발송 방법을 선택해 주세요.');
                    return false;
                }
            }

        });

        $('.srh_addr').on('click',function(e){
            e.preventDefault();
            var container = $('<div class="srh_addr_pop">');
            $(container).load('/common/srh_addr');
            $('body').append(container);
        });

        $('input[type="radio"]').iCheck({
            radioClass: 'iradio_square-blue'
        });

        if($('.direct_area').length > 0) $('.direct_area').remove();

        $('#cancel_frm').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(){
            },
            success: function(result){
                if( !empty(result.message) && result.message_type == 'alert' ) {
                    alert(result.message);
                }
                if( result.status == '<?php echo get_status_code('success');?>' ){
                    location_replace('/delivery')
                }
            },
            error: function(){
            }
        });

        $('select[name="cancel_gubun"]').on('change',function(){

            var esti_amt = '';

            if( $('#cancel_frm input[name="t"]').val() == 68 && $(this).val() == 'A' && $('.refund_wrap .del_amt').length > 0 && $('.refund_wrap .esti_amt').length > 0){

                esti_amt = parseInt($('#cancel_frm input[name="tot_buy_amt"]').val()) - parseInt($('#cancel_frm input[name="del_amt"]').val());
                esti_amt = esti_amt.toString().comma();

                $('.refund_wrap .del_amt').html('- '+$('#cancel_frm input[name="del_amt"]').val());
                $('.refund_wrap .esti_amt').html(esti_amt);

            }else{

                esti_amt = $('#cancel_frm input[name="tot_buy_amt"]').val().comma();

                $('.refund_wrap .del_amt').html('0');
                $('.refund_wrap .esti_amt').html(esti_amt);

            };

        });

        $('input[name="del_type"]').on('ifChanged',function(){
            if($(this).val() == 'direct') $('.del_type_addr').hide();
            else $('.del_type_addr').show();
        });

    });

</script>
