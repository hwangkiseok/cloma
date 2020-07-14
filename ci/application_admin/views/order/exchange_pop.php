

<? $option_list = json_decode($aOrderInfo['option_list'],true); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="/Order/update_cancel_proc">

                        <input type="hidden" name="seq" value="<?=$aOrderInfo['seq']?>" />
                        <input type="hidden" name="after_status_cd" value="<?=$aOrderInfo['after_status_cd']?>">


                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">주문번호(장바구니번호)</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?=$aOrderInfo['trade_no']?><?if(empty($aOrderInfo['m_trade_no']) == false){?> ( <?=$aOrderInfo['m_trade_no']?> )<?}?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">상품명</label>
                            <div class="col-sm-8">
                                <p style="line-height: 22px;margin-bottom: 0;padding: 6px 10px">
                                    <b><?=$aOrderInfo['item_name']?></b><br>
                                    <? foreach ($option_list as $k => $r) {?>
                                        <b>&middot; 옵션<?=$k+1?> :</b> <?=$r['option_name']?> / <?=number_format($r['option_count'])?>개<br>
                                    <?}?>

                                </p>
                            </div>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">주문자명</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?=$aOrderInfo['buyer_name']?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">주문자 연락처</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?=ph_slice($aOrderInfo['buyer_hhp'])?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">주문상태</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['status_cd'],'form_status_cd')?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">취소상태</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?=$this->config->item($aOrderInfo['after_status_cd'],'form_status_cd')?></p>
                            </div>
                        </div>

                        <?if($aOrderInfo['receiver_name']){?>
                        <hr>


                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">회수 요청자 성함</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?=$aOrderInfo['receiver_name']?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">회수 요청자 연락처</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?=ph_slice($aOrderInfo['receiver_tel'])?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-4 control-label">회수지 주소</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    (<?=$aOrderInfo['receiver_zip']?>) <?=$aOrderInfo['receiver_addr1']?> <?=$aOrderInfo['receiver_addr2']?>
                                </p>
                            </div>
                        </div>
                        <?}?>




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

            if($('input[name="after_status_cd"]').val() == '67'){

                if($('input[name="exchange_delivery"]').val() == '' && $('select[name="proc_flag"]').val() == 'Y'){
                    alert("교환이 완료 된 경우 택배사를 선택해주세요.");
                    return false;
                }

                if($('input[name="exchange_delivery_no"]').val() == '' && $('select[name="proc_flag"]').val() == 'Y'){
                    alert("교환이 완료 된 경우 송장번호를 입력해주세요.");
                    return false;
                }
            }

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
