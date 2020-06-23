<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>
<script>

    /**
     * 적립금 목록 출력
     */
    function point_list_select_print() {
        $.ajax({
            url : '/point/list_ajax',
            data : {print_type:'select'},
            type : 'post',
            dataType : 'html',
            success : function (result) {
                $('#point_select_wrap').html(result);
            }
        });

    }//end of point_list_select_print()

    $(function(){

        point_list_select_print();

        $('input[name="ap_push_type"]').on('change',function(){

            if($(this).val() == 'point'){
                $('.point_select_area').show();
            }else{
                $('#point_select_wrap select').val('');
                $('.point_select_area').hide();
            };

        });

    })

</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="ap_reserve_datetime" value="" />
                        <input type="hidden" name="ap_push_type" value="product">
                        <input type="hidden" name="ap_os_type" value="1" />
                        <input type="hidden" name="ap_badge" value="Y" />
                        <input type="hidden" name="ap_stock_flag" value="Y" />
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">푸시타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_push_type">
                                    <?php echo get_input_radio('ap_push_type', $this->config->item('app_push_type'), 'product'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm point_select_area " style="display:none;">
                            <label class="col-sm-2 control-label">적립금 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_ptuid">
                                    <div class="pull-left" id="point_select_wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm" style="display:none;">
                            <label class="col-sm-2 control-label">OS타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_os_type">
                                    <?php echo get_input_radio('ap_os_type', $this->config->item('app_push_os_type'), 1); ?>
                                </div>
                            </div>
                        </div>
                        -->
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">알림타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_noti_type">
                                    <?php echo get_input_radio('ap_noti_type', $this->config->item('app_push_noti_type'), 2); ?>
                                </div>
                            </div>
                        </div>
                        -->
                        <!-- 푸시중간페이지 관련 -->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">푸시페이지 여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">

                                <div id="field_ap_new_push">
                                    <input type="radio" id="ap_new_push_Y" name="ap_new_push" value="Y" checked>
                                    <label for="ap_new_push_Y"><span style="color:#0000ff">사용</span></label>
                                    &nbsp;&nbsp;
                                    <input type="radio" id="ap_new_push_N" name="ap_new_push" value="N" >
                                    <label for="ap_new_push_N"><span style="color:#333">미사용</span></label>
                                    &nbsp;&nbsp;
                                </div>

                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">푸시 페이지 버튼</label>
                            <div class="col-sm-10">
                                <div id="field_ap_list_btn_msg">
                                    <input type="text" name="ap_list_btn_msg" class="form-control" value="<?php echo $app_push_row->ap_list_btn_msg; ?>"  />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">푸시페이지 코멘트</label>
                            <div class="col-sm-10">
                                <div id="ap_list_comment">
                                    <textarea name="ap_list_comment" class="form-control" style="height:80px;" ></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- 푸시중간페이지 관련 end -->


                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">뱃지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_badge">
                                    <?php echo get_input_radio('ap_badge', $this->config->item('app_push_badge'), 'Y', $this->config->item('app_push_badge_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_display_state">
                                    <?php echo get_input_radio('ap_display_state', $this->config->item('app_push_display_state'), 'Y', $this->config->item('app_push_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm form-inline">
                            <label class="col-sm-2 control-label">예약시간</label>
                            <div class="col-sm-10">
                                <div id="field_ap_reserve_datetime">
                                    <div class="input-group date pull-left" style="width:133px;">
                                        <input type="text" class="form-control" style="width:100px;" id="ap_reserve_date" name="ap_reserve_date" value="" />
                                        <span class="input-group-btn text-left">
                                            <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="ap_reserve_hour" class="form-control">
                                            <?php echo get_select_option_hour('', '00'); ?>
                                        </select> 시
                                    </div>
                                    <div class="pull-left mgl10">
                                        <select name="ap_reserve_min" class="form-control">
                                            <?php echo get_select_option_min('', '00'); ?>
                                        </select> 분
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_subject">
                                    <input type="text" name="ap_subject" class="form-control" value="" />
                                </div>
                                <!--1
                                <p class="help-block"><xmp>* 제목에 부분 컬러 설정하기 (예: <font color="#FF0000">빨간색</font>, <b>굵은글씨</b>)</xmp></p>
                                <p class="help-block" style="color:red;">* HTML 코드는 안드로이드앱 2.1.0 ~ 부터 적용됨.</p>
                                -->
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">내용 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_message">
                                    <textarea name="ap_message" class="form-control" style="height:80px;"></textarea>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">스타일</label>
                            <div class="col-sm-10">
                                <label class="col-sm-2 control-label">배경색상</label>
                                <div class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" name="background_color" class="form-control" style="width:150px;" maxlength="6" value="" placeholder="색상코드(FFFFFF)입력" />
                                </div>
                                <div class="clearfix mgt5"></div>
                                <label class="col-sm-2 control-label">제목색상</label>
                                <div class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" name="title_color" class="form-control" style="width:150px;" maxlength="6" value="" placeholder="색상코드(FFFFFF)입력" />
                                </div>
                                <div class="clearfix mgt5"></div>
                                <label class="col-sm-2 control-label">메시지색상</label>
                                <div class="input-group">
                                    <span class="input-group-addon">#</span>
                                    <input type="text" name="message_color" class="form-control" style="width:150px;" maxlength="6" value="" placeholder="색상코드(FFFFFF)입력" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">아이콘</label>
                            <div class="col-sm-10">
                                <div id="field_ap_icon">
                                    <input type="file" name="ap_icon" class="form-control" />
                                </div>
                                <p class="help-block">* 아이콘 이미지가 없으면 기본 앱 아이콘이 출력됩니다.</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지</label>
                            <div class="col-sm-10">
                                <div id="field_ap_image">
                                    <input type="file" name="ap_image" class="form-control" />
                                </div>
                                <p class="help-block">*  이미지 사이즈 512px x 256px / 확장자는 jpg, png만</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">요약내용</label>
                            <div class="col-sm-10">
                                <div id="field_ap_summary">
                                    <textarea name="ap_summary" class="form-control" style="height:80px;" disabled></textarea>
                                </div>
                                <p class="help-block">* 이미지 첨부형식일때 푸시영역이 확장시 나오는 내용</p>
                            </div>
                        </div>
                        -->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이동URL</label>
                            <div class="col-sm-10">
                                <div id="field_ap_target_url">
                                    <input type="text" name="ap_target_url" class="form-control" value="" />
                                </div>
                                <p class="help-block">* 상품관리 > 단축URL 불러오기 > 앱푸시 > WEB </p>
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
    var form = '#pop_insert_form';

    //document.ready
    $(function(){

        // $('input[name="ap_new_push"]').on('change',function(){
        //
        //     if($(this).val() == 'Y'){
        //         $('input[name="ap_list_btn_msg"]').prop('readonly',false);
        //         $('textarea[name="ap_list_comment"]').prop('readonly',false);
        //     }else{
        //         $('input[name="ap_list_btn_msg"]').val('').prop('readonly',true);
        //         $('textarea[name="ap_list_comment"]').val('').prop('readonly',true);
        //     }
        //
        // });

        //datepicker
        $('.input-group.date').datepicker({format:"yyyy-mm-dd", language:"kr", autoclose:true, todayHighlight:true});

        /*
        $('[name="ap_image"]').on('change', function(){
            if( $(this).val() ) {
                $('[name="ap_summary"]').prop('disabled', false);
            }
            else {
                $('[name="ap_summary"]').prop('disabled', true);
            }
        });
        */

        //submit check
        $(form).on('submit', function(){

            info_message_all_clear();

            if( $('input[name="ap_push_type"]').val() == 'point' && $('select[name="point_select"]').val() == ''){
                alert('포인트 푸시인 경우 지급하실 적립금을 선택해야합니다.');
                $('select[name="point_select"]').focus();
                return false;
            }

            var datetime = '';
            var date = $(form + ' [name="ap_reserve_date"]').val();
            var hour = $(form + ' [name="ap_reserve_hour"]').val();
            var min = $(form + ' [name="ap_reserve_min"]').val();
            if( date ) {
                datetime = date + ' ' + hour + ':' + min + ':00';
                //시간:분 검증
                if( parseInt(hour) < 0 || parseInt(hour) > 24 ) {
                    error_message($('#field_ap_reserve_datetime'), '시간을 제대로 입력하세요.');
                    return false;
                }
                if( parseInt(min) < 0 || parseInt(min) > 59 ) {
                    error_message($('#field_ap_reserve_datetime'), '분을 제대로 입력하세요.');
                    return false;
                }
            }

            if(empty($('input[name="background_color"]').val()) == false && $('input[name="background_color"]').val().length != 6){
                alert('스타일 배경색상을 확인해주세요');
                $('input[name="background_color"]').focus();
                return false;
            }
            if(empty($('input[name="title_color"]').val()) == false && $('input[name="title_color"]').val().length != 6){
                alert('스타일 제목색상을 확인해주세요');
                $('input[name="title_color"]').focus();
                return false;
            }
            if(empty($('input[name="message_color"]').val()) == false && $('input[name="message_color"]').val().length != 6){
                alert('스타일 메시지색상을 확인해주세요');
                $('input[name="message_color"]').focus();
                return false;
            }

            $(form + ' [name="ap_reserve_datetime"]').val(datetime);

            var msg;
            if( !empty(datetime) ) {
                msg = '푸시를 예약발송하시겠습니까?';
            }
            else {
                msg = '푸시를 즉시발송하시겠습니까?';
            }
            if( !confirm(msg) ) {
                return false;
            }

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
                        //$.each(result.error_data, function(key, msg){
                        //    if( $('#field_' + key).length ) {
                        //        error_message($('#field_' + key), msg);
                        //    }
                        //});
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