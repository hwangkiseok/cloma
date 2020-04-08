<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트종류 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_e_division">
                                    <?php echo get_input_radio('e_division', $this->config->item('event_division'), ''); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트코드</label>
                            <div class="col-sm-10 form-inline">
                                <input type="text" id="field_e_code" class="form-control" style="width:200px" name="e_code" maxlength="30" value="" />
                                <span style="margin-left:10px;">(* 내부처리용, 30자이하)</span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_usestate">
                                    <?php echo get_input_radio('e_display_state', $this->config->item('event_display_state'), 'Y', $this->config->item('event_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진행상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_usestate">
                                    <?php echo get_input_radio('e_proc_state', $this->config->item('event_proc_state'), 'Y', $this->config->item('event_proc_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_subject">
                                    <input type="text" class="form-control" name="e_subject" maxlength="100" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_termlimit_yn">
                                    <?php echo get_input_radio('e_termlimit_yn', $this->config->item('event_termlimit_yn'), 'N', $this->config->item('event_termlimit_yn_text_color')); ?>
                                </div>
                                <div id="field_e_termlimit_date" style="display:none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="e_termlimit_datetime1" name="e_termlimit_datetime1" value="" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="e_termlimit_datetime2" name="e_termlimit_datetime2" value="" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">대표이미지 타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <?php echo get_input_radio('e_rep_image_type', $this->config->item('event_rep_image_type'), '1'); ?>
                            </div>
                        </div>

                        <div class="form-group form-group-sm" id="wrap_e_rep_image_type_1">
                            <label class="col-sm-2 control-label">대표이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_rep_image">
                                    <input type="file" name="e_rep_image" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm" id="wrap_e_rep_image_type_2" style="display:none;">
                            <label class="col-sm-2 control-label">대표이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_rep_image_ym_1" class="input-group">
                                    <span class="input-group-addon"><?php echo $cur_m; ?>월</span>
                                    <input type="file" name="e_rep_image_ym_1" class="form-control" />
                                </div>
                                <div id="field_e_rep_image_ym_2" class="input-group mgt10">
                                    <span class="input-group-addon"><?php echo $next_m; ?>월</span>
                                    <input type="file" name="e_rep_image_ym_2" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!--<div class="form-group form-group-sm">-->
                        <!--    <label class="col-sm-2 control-label">연속출석일</label>-->
                        <!--    <div class="col-sm-10">-->
                        <!--        <div id="field_e_attend_day" class="input-group" style="width:100px;">-->
                        <!--            <input type="text" class="form-control" name="e_attend_day" maxlength="3" value="" />-->
                        <!--            <span class="input-group-addon">일</span>-->
                        <!--        </div>-->
                        <!--        <p class="mgt5">* 출석체크이벤트에만 적용됩니다.</p>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">Alert메시지</label>
                            <div class="col-sm-10">
                                <div id="field_e_alert_message">
                                    <input type="text" class="form-control" name="e_alert_message" maxlength="500" />
                                </div>
                                <p class="mgt5">* 노출안함, 이벤트종료일때만 출력됩니다.</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">참여후이동타입</label>
                            <div class="col-sm-10">
                                <div id="field_e_after_type">
                                    <?php echo get_input_radio("e_after_type", $this->config->item("event_after_type"), 0); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group form-group-sm">
                            <div id="field_e_content">
                                <textarea name="e_content" id="e_content" style="width:100%; height:200px; visibility:hidden;"></textarea>
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
<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>

<script>
    //====================================== smarteditor2
    var oEditors = [];
    var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    var editorElement = 'e_content';
    var form = '#pop_insert_form';

    loadingBar.show('#field_e_content');

    $(function(){
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: editorElement,
            sSkinURI: "/plugins/smarteditor2/SmartEditor2Skin.html",
            htParams : {
                bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                fOnBeforeUnload : function(){
                    //alert("완료!");
                }
            },
            fOnAppLoad : function(){
//                oEditors.getByIdeditorElement].exec("PASTE_HTML", ['<p style="font-family:Noto Sans;font-size:1em;"></p>']);
//                oEditors.getById[editorElement].setDefaultFont("Noto Sans", "1em");
                loadingBar.hide();
            },
            fCreator: "createSEditor2"
        });
    });
    //====================================== /smarteditor2
</script>

<script>
    //document.ready
    $(function(){
        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        //e_termlimit_yn click
        $(form + ' [name="e_termlimit_yn"]').on('click', function(){
            if( $(this).val() == 'Y' ) {
                $('#field_e_termlimit_date').show();
            }
            else {
                $('#field_e_termlimit_date').hide();
            }
        });

        //대표이미지 타입 click
        $('[name="e_rep_image_type"]').on('click', function () {
            $('[id*="wrap_e_rep_image_type_"]').hide();
            $('#wrap_e_rep_image_type_' + $(this).val()).show();
        });

        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();

            // 에디터의 내용이 textarea에 적용
            oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);

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