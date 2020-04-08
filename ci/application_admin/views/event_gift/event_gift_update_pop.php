<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="eg_num" value="<?php echo $eg_row->eg_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_num">
                                    <select name="eg_event_num" class="form-control" style="width:auto;">
                                        <?php echo get_select_option("* 선택 *", $event_option_array, $eg_row->eg_event_num); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm field_eg_event_gift" style="display: none;">
                            <label class="col-sm-2 control-label">이벤트 상품<span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_gift">
                                    <select class="form-control" name="eg_event_gift" style="width:auto;">
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트 년월</label>
                            <div class="col-sm-10">
                                <div id="field_eg_event_ym">
                                    <select name="eg_event_ym" class="form-control" style="width:auto;">
                                        <?php echo get_select_option_ym("* 선택 *", $eg_row->eg_event_ym, date("Ym", strtotime("-2 months")), date("Ym", time())); ?>
                                    </select>
                                </div>
                                <p class="help-block">* 출석이벤트일때 사용</p>
                            </div>
                        </div>

                        <?php if( !empty($eg_row->eg_member_num) ) { ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">발급회원</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <a href="#none" onclick="member_update_pop('<?php echo $eg_row->eg_member_num; ?>')"><?php echo $eg_row->m_loginid; ?></a>
                                </p>
                            </div>
                        </div>

                        <?php }//endif; ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_state">
                                    <?php echo get_input_radio("eg_state", $this->config->item("event_gift_state"), $eg_row->eg_state, $this->config->item("event_gift_state_text_color")); ?>
                                </div>
                                <p class="help-block" style="color:#ff0000;">* 발급완료 상태에서 등록완료로 변경시 발급취소됩니다.</p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">파일 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_eg_gift">
                                    <input type="file" class="form-control" name="eg_gift_file" />
                                </div>
                                <p class="help-block">* <span style="color:#ff0000;">핀번호.jpg 형식</span>의 이미지 업로드(GIF, JPG, PNG)</p>
                                <p class="gift_img mgt10"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var form = '#pop_update_form';
    var event_gift = '<?=$eg_row->eg_event_gift?>';
    var event_num = '<?=$eg_row->eg_event_num?>';

    function getEventGift(num){

        $.ajax({
            url : '/event_gift/getEventInfo',
            data : {e_num:num},
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

                    $('.field_eg_event_gift select option[value="'+event_gift+'"]').attr('selected','selected');
                    $('.field_eg_event_gift').show();

                }
            }
        });

    }

    $('select[name="eg_event_num"]').on('change',function(){
        getEventGift($(this).val());
    });

    //document.ready
    $(function(){

        getEventGift(event_num);

        <?php if( !empty($eg_row->eg_gift_file) ) { ?>
        $('.gift_img').load('/download/img/?f=<?php echo urlencode($eg_row->eg_gift_file); ?>', function(){
            $('.gift_img img').css({'width':'100px'});
        });
        <?php }//endif; ?>

        //submit check
        $(form).on('submit', function(){
            <?php if( $eg_row->eg_state == "2" ) { ?>
            if( $('[name="eg_state"]:checked').val() == '1' ) {
                if( !confirm('발급된 기프티콘이 회수됩니다. 수정하시겠습니까?') ) {
                    return false;
                }
            }
            <?php }//endif; ?>

            //info_message_all_clear();
            //Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            //async: false,
            //cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                info_message_all_clear();
                Pace.restart();
                loadingScreen.show();
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
                Pace.stop();
                loadingScreen.hide();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>