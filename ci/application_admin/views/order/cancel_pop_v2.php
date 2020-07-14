
<? //zsView($aOrderInfo); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="/Order/update_cancel_proc_v2">

                        <input type="hidden" name="seq" value="<?=$aOrderInfo['seq']?>" />
                        <input type="hidden" name="after_status_cd" value="<?=$aOrderInfo['after_status_cd']?>">

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품명</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=$aOrderInfo['item_name']?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">주문상태</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['status_cd'],'form_status_cd')?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">취소상태</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['after_status_cd'],'after_form_status_cd')?></p>
                            </div>
                        </div>

                        <? if($aOrderInfo['payway_cd'] <> 1 && $aOrderInfo['after_status_cd'] <> 67 && $aOrderInfo['after_status_cd'] <> 167){ //카드결제x / 교환x?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">환불정보</label>
                            <div class="col-sm-2">
                                <label class="control-label">&middot; 예금주</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="account_holder" title="account_holder" value="<?=$aOrderInfo['account_holder']?>">
                            </div>
                        </div>

                        <div class="form-group form-group-sm">

                            <div class="col-sm-2 col-sm-offset-2">
                                <label class="control-label">&middot; 은행</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="account_bank" title="account_bank" class="form-control">
                                    <option value="">- 환불할 은행 선택 -</option>
                                    <? foreach ($this->config->item('cancel_bank') as $k => $v) { ?>
                                        <option value="<?=$k?>" <?if($aOrderInfo['account_bank'] == $k){?> selected <?}?>  ><?=$v?></option>
                                    <?} ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-group form-group-sm">

                            <div class="col-sm-2 col-sm-offset-2">
                                <label class="control-label">&middot; 계좌</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="account_no" title="account_no" value="<?=$aOrderInfo['account_no']?>">
                            </div>

                        </div>

                        <?} ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">결제수단</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['payway_cd'],'form_payway_cd')?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">결제금액</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=number_format($aOrderInfo['buy_amt'])?>원</p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">택배비</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=number_format($aOrderInfo['delivery_amt'])?>원</p>
                            </div>
                        </div>


                        <?if($aOrderInfo['after_status_cd'] == '67' || $aOrderInfo['after_status_cd'] == '167'){ // 교환시?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label" style="padding-left: 5px!important;">교환택배사 <span class="txt-danger"></span></label>
                            <div class="col-sm-10">
                                <div id="field_exchange_delivery">
                                    <select class="form-control" name="exchange_delivery">
                                        <?php echo get_select_option('선택해주세요.', $this->config->item('delivery_company'), '01'); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">송장번호 <span class="txt-danger"></span></label>
                            <div class="col-sm-10">
                                <div id="field_exchange_delivery_no">
                                    <input type="text" class="form-control" name="exchange_delivery_no" value="<?=$aOrderInfo['exchange_delivery_no']?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label" style="padding-left: 5px!important;">교환정보</label>
                            <div class="col-sm-10">
                                <p style="padding: 6px 10px;margin-bottom: 0; "><?=nl2br($aOrderInfo['exchange_reason'])?></p>
                            </div>
                        </div>

                        <?}?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">취소사유</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['cancel_gubun'],'order_cancel_gubun')?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상세사유</label>
                            <div class="col-sm-10">
                                <p style="padding: 6px 10px;margin-bottom: 0; "><?=nl2br($aOrderInfo['cancel_reason'])?></p>
                            </div>
                        </div>


                        <? $exchange_del_able = array( '67' , '68' , '167' , '168' ); //교환/반품상태
                        if(in_array($aOrderInfo['after_status_cd'] , $exchange_del_able) == true){?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">반품/교환 배송비 여부</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <? $exchange_del_price = array('A'=> '입금확인' , 'B'=> '안내확인' , 'C'=> '옷쟁이들부담') ?>
                                    <?php echo get_input_radio('exchange_del_price', $this->config->item('exchange_del_price'), $aOrderInfo['exchange_del_price']); ?>
                                </p>
                            </div>
                        </div>

                        <?} ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">비고</label>
                            <div class="col-sm-10">
                                <div id="field_proc_flag">
                                    <textarea name="bigo" class="form-control" rows="6"><?=$aOrderInfo['bigo']?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">처리여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_proc_flag">
                                    <select class="form-control" name="proc_flag">
                                        <option value="Y" <?=$aOrderInfo['proc_flag']=='Y'?'selected':''?>>처리완료</option>
                                        <option value="N" <?=$aOrderInfo['proc_flag']=='N'?'selected':''?> >처리중</option>
                                    </select>
                                    <?if(empty($aOrderInfo['proc_date'] == false)){?><p class="text-danger form-control-static"><?=view_date_format($aOrderInfo['proc_date'],3)?></p><?}?>
                                </div>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>
</div>
</div>


<script src="/plugins/datepicker/bootstrap-datepicker.js" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js" charset="utf-8"></script>

<script>
    var form = '#pop_update_form';

    //document.ready
    $(function(){

        //datepicker
        $('.input-group.date').datepicker({format:"yyyy-mm-dd", language:"kr", autoclose:true, todayHighlight:true});


        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();

            // if($('input[name="after_status_cd"]').val() == '67'){
            //
            //     if($('input[name="exchange_delivery"]').val() == '' && $('select[name="proc_flag"]').val() == 'Y'){
            //         alert("교환이 완료 된 경우 택배사를 선택해주세요.");
            //         return false;
            //     }
            //
            //     if($('input[name="exchange_delivery_no"]').val() == '' && $('select[name="proc_flag"]').val() == 'Y'){
            //         alert("교환이 완료 된 경우 송장번호를 입력해주세요.");
            //         return false;
            //     }
            // }

            Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( result.error_data ) {
                        error_message_alert(result.error_data);
                    }
                }//end of if()
            },
            complete : function() {
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()

</script>
