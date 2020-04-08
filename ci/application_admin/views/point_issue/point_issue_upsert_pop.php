<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
    .attend_day_info_item { padding-bottom:10px; }
</style>

<? //zsView($aCouponLists); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="coupon_frm" id="coupon_frm" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="mode" value="<?=$aInput['mode']?>" />
                        <input type="hidden" name="seq" value="<?=$aInput['seq']?>" />

                        <div class="form-group form-group-sm">
                            <div id="field_seq"></div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">발급기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">

                                <div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="start_date" name="start_date" value="<?=$aCouponInfoLists['start_date']?>" autocomplete="off" />
                                            <span class="input-group-btn text-left">
                                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="end_date" name="end_date" value="<?=$aCouponInfoLists['end_date']?>" autocomplete="off" />
                                            <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                        </div>
                                    </div>
                                    <div style="clear: both;"></div>
                                    <div id="field_start_date"></div>
                                    <div id="field_end_date"></div>

                                </div>

                            </div>
                        </div>


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">적립금 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_coupon_info">
                                    <? //zsView($aCouponLists); ?>
                                    <select name="coupon_select" class="form-control" style="width: 100%">
                                        <option value="">선택해주세요!</option>
                                        <? foreach ($aCouponLists as $row) {$row = (array)$row;?>
                                            <option value="<?=$row['pt_code']?>::<?=$row['pt_uid']?>::<?=$row['pt_name']?>::<?=$row['pt_issue_type']?>"><?=$row['pt_name']?> [ <?=number_format($row['pt_issue_value'])?> 점 ] </option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div id="field_coupon_select_lists">
                                    <? foreach ($aSelectedCouponLists as $row) {?>
                                        <p class="alert alert-warning coupon-info-alert"><input type="hidden" name="coupon_info[]" value="<?=$row['pt_code']?>::<?=$row['pt_uid']?>::<?=$row['pt_name']?>::<?=$row['pt_issue_type']?>" /><span><?=$row['pt_name']?> [ <?=number_format($row['pt_issue_value'])?> 점 ]</span><button class="pull-right btn btn-danger btn-xs coupon-row-del">삭제</button></p>
                                    <?}?>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>

    var form  = '#coupon_frm';

    //document.ready
    $(function(){

        $('.input-group.date').datepicker({format: "yyyymmdd", language: "kr", autoclose: true});

        var select2_obj = {placeholder: "선택해주세요",minimumResultsForSearch : -1};

        $('select[name="coupon_select"]').select2(select2_obj);

        $('select[name="coupon_select"]').on('select2:select', function (e) {

            var data = e.params.data;
            var ret = 'Y';

            $('input[name="coupon_info[]"]').each(function(){
                if($(this).val() == data.id) {
                    alert('이미 같은 쿠폰이 선택되어 있습니다.');
                    ret = 'N';
                    return false;
                }
            });

            if(ret == 'Y'){
                var html = '';
                if( $('input[name="coupon_info[]"]').length < 1 ) html += '<hr>';
                    html += '<p class="alert alert-warning coupon-info-alert"><input type="hidden" name="coupon_info[]" value="'+data.id+'" /><span>'+data.text+'</span><button class="pull-right btn btn-danger btn-xs coupon-row-del">삭제</button></p>';
                $('#field_coupon_select_lists').append(html);
            }

        });

        //submit check
        $(form).on('submit', function(){
            Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            //beforeSubmit: function(formData, jqForm, options) {
            //},
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
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>