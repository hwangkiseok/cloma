<form id="cancel_frm" name="cancel_frm" action="/order/cancel_proc" method="post">

    <input type="hidden" name="trade_no" value="<?=$trade_no?>" />
    <input type="hidden" name="t" value="<?=$cancel_type?>" />
    <input type="hidden" name="payway_cd" value="<?=$aSnsformOrderInfo['payway_cd']?>" />
    <input type="hidden" name="status_cd" value="<?=$aSnsformOrderInfo['status_cd']?>" />

    <div class="box">

        <div class="box-in">
            <p style="border-bottom: 1px solid #aaa;padding-bottom: 8px">주문취소사유</p>

            <div>
                <table class="cancel_table">
                    <colgroup>
                        <col style="width:25%;"/>
                        <col style="width:;" />
                    </colgroup>

                    <tr>
                        <th>사유항목</th>
                        <td>
                            <select name="cancel_gubun" title="cancel_gubun">
                                <?=get_select_option("항목을 선택해주세요.", $this->config->item("order_cancel_gubun"));?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>사유항목</th>
                        <td>
                            <textarea name="cancel_reason" placeholder="상세사유가 노출됩니다." rows="6" style="width: 100%;" ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <td class="cancel_noti">
                            &middot; 상품이 출고된 경우 요청사항이 거부될 수 있는 점 참고바랍니다.<br>
                            &middot; 교환하시고자 하는 상품명/색상/사이즈를 기재해 주시기 바랍니다.<br>
                            &middot; 취소/교환/반품을 하시는 상품 및 신청사유에 따라 배송비 환불 또는 추가 배송비가 발생 할 수 있습니다.<br>
                        </td>
                    </tr>

                </table>
            </div>

        </div>

    </div>


    <div class="box" <?if(!($aSnsformOrderInfo['payway_cd'] == '3' && $aSnsformOrderInfo['status_cd'] > 61)){?>style="display: none;"<?}?>>

        <div class="box-in">
            <p style="border-bottom: 1px solid #aaa;padding-bottom: 8px">환불계좌정보를 입력해주세요.</p>

            <div>
                <table class="cancel_table">
                    <colgroup>
                        <col style="width:25%;"/>
                        <col />
                    </colgroup>
                    <tr>
                        <th>예금주</th>
                        <td><input type="text" name="account_holder" style="width: 100%;padding: 8px"> </td>
                    </tr>
                    <tr>
                        <th>은행명</th>
                        <td>
                            <select name="account_bank" style="width: 100%;padding: 8px">
                                <?=get_select_option("항목을 선택해주세요.", $this->config->item("cancel_bank"));?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>환불계좌</th>
                        <td><input type="text" name="account_no" style="width: 100%;padding: 8px" numberOnly> </td>
                    </tr>

                </table>

            </div>

        </div>

    </div>


</form>

<script type="text/javascript">

    $(function(){

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
                    location.reload();
                }


            },
            error: function(){
            }
        });

    });

    //submit check
    function cancel_order_chk(){

        if( $('select[name="cancel_gubun"]:selected').val() == '' ){
            alert('취소사유를 선택해주세요 !');
            return false;
        }
        if( $('textarea[name="cancel_reason"]').val() == ''){
            alert('상세사유를 입력해주세요 !');
            return false;
        }

        if($('input[name="payway_cd"]').val() == '3' && $('input[name="status_cd"]').val() > 61){

            if( $('input[name="account_holder"]').val() == '' ){
                alert('환불할 계좌의 예금주를 입력해주세요 !');
                return false;
            };
            if( $('input[name="account_no"]').val()  == '' ){
                alert('환불할 계좌의 계좌번호를 입력해주세요 !');
                return false;
            };
            if( $('select[name="account_bank"]:selected').val() == '' ){
                alert('환불활 계좌의 은행을 선택해주세요 !');
                return false;
            }

        }

        $('#cancel_frm').submit();

    }

</script>
