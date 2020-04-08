<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_num">
                                    <select name="eg_event_num" class="form-control" style="width:auto;">
                                        <?php echo get_select_option("* 선택 *", $event_option_array, ""); ?>
                                    </select>
                                </div>
                            </div>
                        </div>



                        <div class="form-group form-group-sm field_eg_event_gift" style="display: none;">
                            <label class="col-sm-2 control-label">이벤트 상품<span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_gift">

                                    <select class="form-control" name="eg_event_gift" style="width:auto;">
                                        <option value="">* 선택 *</option>
                                        <?$aEnsGift = $this->config->item("ens_gift");

                                        if(count($aEnsGift) > 0){
                                            foreach ($aEnsGift as $k => $v) {?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                        <?  }
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>




                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트 년월</label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_ym">
                                    <select name="eg_event_ym" class="form-control" style="width:auto;">
                                        <?php echo get_select_option_ym("* 선택 *", "", date("Ym", strtotime("-2 months")), date("Ym", time())); ?>
                                    </select>
                                </div>
                                <p class="help-block">* 출석이벤트일때 사용</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">파일 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_gift">
                                    <input type="file" class="form-control" name="eg_gift_file[]" multiple="multiple" maxlength="1000" />
                                </div>
                                <p class="help-block">* <span style="color:#ff0000;">핀번호.jpg 형식</span>의 이미지 업로드(GIF, JPG, PNG), 다중 선택 가능(최대 약1500개/총 100MB)</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var form = '#pop_insert_form';

    $('select[name="eg_event_num"]').on('change',function(){
        $.ajax({
            url : '/event_gift/getEventInfo',
            data : {e_num:$(this).val()},
            type : 'post',
            dataType : 'json',
            success : function (result) {

                $('.field_eg_event_gift option').remove();

                if(result.data == null || result.data == 'null' || result.data == ''){

                    $('.field_eg_event_gift').hide();

                }else{

                    var aData   = JSON.parse(result.data);
                    var html    = '<option value="">* 선택 *</option>';
                    for (var i in aData) {
                        html   += '<option value="'+i +'">'+aData[i]+'</option>';
                    }

                    $('.field_eg_event_gift select').html(html);
                    $('.field_eg_event_gift').show();

                }
            }
        });

    });

    //document.ready
    $(function(){
        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();
            //loadingScreen.show();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            //async: false,
            //cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                loadingScreen.show();
                Pace.restart();
            },
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
                        //$.each(result.error_data, function(key, msg){
                        //    if( $('#field_' + key).length ) {
                        //        error_message($('#field_' + key), msg);
                        //    }
                        //
                        //});
                        error_message_alert(result.error_data);
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
                loadingScreen.hide();
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>