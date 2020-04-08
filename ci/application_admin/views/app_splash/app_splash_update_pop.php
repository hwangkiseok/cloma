<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="aps_num" value="<?php echo $app_splash_row->aps_num; ?>" />
                        <input type="hidden" name="aps_termlimit1" value="<?php echo $app_splash_row->aps_termlimit1; ?>">
                        <input type="hidden" name="aps_termlimit2" value="<?php echo $app_splash_row->aps_termlimit2; ?>">

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">스플래시<br />이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_aps_image">
                                    <input type="file" name="aps_image" class="form-control" accept="image/*" />
                                </div>
                                <?php if( $this->config->item('splash_image_size') ) { ?>
                                    <p class="help-block">*
                                        <?php foreach ($this->config->item('splash_image_size') as $item) { ?>
                                            <?php echo $item[0] . " x " . $item[1]; ?>
                                        <?php } ?>
                                        로 리사이징 됩니다.
                                    </p>
                                <?php } ?>

                                <?php
                                if( !empty($app_splash_row->aps_image) ) {
                                    echo create_img_tag($app_splash_row->aps_image, "", 200, "");
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group form-group-sm form-inline">
                            <label class="col-sm-2 control-label">시작시간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_aps_termlimit1">
                                    <div class="input-group date pull-left" style="width:133px;">
                                        <input type="text" class="form-control" style="width:100px;" id="aps_termlimit1_d" name="aps_termlimit1_d" value="<?php echo get_date_format($app_splash_row->aps_termlimit1, '-'); ?>" />
                                        <span class="input-group-btn text-left">
                                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="aps_termlimit1_h" class="form-control">
                                            <?php echo get_select_option_hour('', substr($app_splash_row->aps_termlimit1, 8, 2)); ?>
                                        </select> 시
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="aps_termlimit1_m" class="form-control">
                                            <?php echo get_select_option_min('', substr($app_splash_row->aps_termlimit1, 10, 2)); ?>
                                        </select> 분
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm form-inline">
                            <label class="col-sm-2 control-label">종료시간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_aps_termlimit2">
                                    <div class="input-group date pull-left" style="width:133px;">
                                        <input type="text" class="form-control" style="width:100px;" id="aps_termlimit2_d" name="aps_termlimit2_d" value="<?php echo get_date_format($app_splash_row->aps_termlimit2, '-'); ?>" />
                                        <span class="input-group-btn text-left">
                                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="aps_termlimit2_h" class="form-control">
                                            <?php echo get_select_option_hour('', substr($app_splash_row->aps_termlimit2, 8, 2)); ?>
                                        </select> 시
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="aps_termlimit2_m" class="form-control">
                                            <?php echo get_select_option_min('', substr($app_splash_row->aps_termlimit2, 10, 2)); ?>
                                        </select> 분
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">배경색 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_aps_bg_color" class="form-inline">
                                    <input type="text" name="aps_bg_color" value="<?=$app_splash_row->aps_bg_color?>" alt="" class="form-control" style="vertical-align: top">
                                    <div class="color-sample" style="display: inline-block;background-color: #<?=$app_splash_row->aps_bg_color?>;width: 120px; height: 30px;border: 1px solid #ddd;"></div>
                                </div>
                                <p class="help-block">* #을 뺸 색상표값 기입</p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">사용여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_aps_usestate">
                                    <?php echo get_input_radio('aps_usestate', $this->config->item('app_splash_usestate'), $app_splash_row->aps_usestate, $this->config->item('app_splash_usestate_text_color')); ?>
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

        $('input[name="aps_bg_color"]').on('keyup',function(){
            if($(this).val().length == 6){
                $('.color-sample').css('background-color','#'+$(this).val());
            }else{
                if($('.color-sample').css('background-color') != 'rgb(255, 255, 255)'){
                    $('.color-sample').css('background-color','#ffffff');
                }
            }
        });

        $(form).on('submit', function(){

            info_message_all_clear();

            var datetime1 = '';
            var date1 = $(form + ' [name="aps_termlimit1_d"]').val();
            var hour1 = $(form + ' [name="aps_termlimit1_h"]').val();
            var min1 = $(form + ' [name="aps_termlimit1_m"]').val();
            if( date1 ) {
                datetime1 = date1 + ' ' + hour1 + ':' + min1 + ':00';
                //시간:분 검증
                if( parseInt(hour1) < 0 || parseInt(hour1) > 24 ) {
                    error_message($('#field_aps_termlimit1'), '시간을 제대로 입력하세요.');
                    return false;
                }
                if( parseInt(min1) < 0 || parseInt(min1) > 59 ) {
                    error_message($('#field_aps_termlimit1'), '분을 제대로 입력하세요.');
                    return false;
                }
            }else{
                error_message($('#field_aps_termlimit1'), '시작날짜를 선택해주세요.');
                return false;
            }

            var datetime2 = '';
            var date2 = $(form + ' [name="aps_termlimit2_d"]').val();
            var hour2 = $(form + ' [name="aps_termlimit2_h"]').val();
            var min2 = $(form + ' [name="aps_termlimit2_m"]').val();
            if( date2 ) {
                datetime2 = date2 + ' ' + hour2 + ':' + min2 + ':00';
                //시간:분 검증
                if( parseInt(hour2) < 0 || parseInt(hour2) > 24 ) {
                    error_message($('#field_aps_termlimit2'), '시간을 제대로 입력하세요.');
                    return false;
                }
                if( parseInt(min2) < 0 || parseInt(min2) > 59 ) {
                    error_message($('#field_aps_termlimit2'), '분을 제대로 입력하세요.');
                    return false;
                }
            }else{
                error_message($('#field_aps_termlimit2'), '종료날짜를 선택해주세요.');
                return false;
            }

            $('input[name="aps_termlimit1"]').val(datetime1);
            $('input[name="aps_termlimit2"]').val(datetime2);

            if($('input[name="aps_bg_color"]').val() == ''){
                error_message($('#field_aps_bg_color'), '배경색을 입력해주세요.');
                return false;
            }else{
                if($('input[name="aps_bg_color"]').val().length != 6){
                    error_message($('#field_aps_bg_color'), '배경색 색상표값을 확인해주세요 (6자)');
                    return false;
                }
            }

            Pace.restart();
        });

        $('.input-group.date').datepicker({format:"yyyy-mm-dd", language:"kr", autoclose:true, todayHighlight:true});


        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                info_message_all_clear();
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
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>