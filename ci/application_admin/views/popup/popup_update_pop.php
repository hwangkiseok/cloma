<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="pu_num" value="<?php echo $popup_row->pu_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">팝업종류 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pu_division">
                                    <select name="pu_division" class="form-control" style="width:auto;">
                                        <?php echo get_select_option('', $this->config->item('popup_division'), $popup_row->pu_division); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">활성상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pu_usestate">
                                    <?php echo get_input_radio('pu_usestate', $this->config->item('popup_usestate'), $popup_row->pu_usestate); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pu_subject">
                                    <input type="text" class="form-control" name="pu_subject" maxlength="100" value="<?php echo $popup_row->pu_subject; ?>" />
                                </div>
                            </div>
                        </div>

                        <hr class="confirm_popup_input_line" style="display:none;" />

                        <div class="form-group form-group-sm" id="pu_target_url_wrap" style="display:none;">
                            <label class="col-sm-2 control-label">이동URL</label>
                            <div class="col-sm-10">
                                <div id="field_pu_target_url">
                                    <input type="text" class="form-control" name="pu_target_url" maxlength="200" value="<?php echo $popup_row->pu_target_url; ?>" />
                                </div>
                                <p class="help-block">* 전체팝업클릭시 / 컨펌팝업 [확인] 클릭시 이동하는 URL</p>
                                <p class="help-block">* http(s)://play.goole.com 은 market:// 로 변환 됩니다.</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm" id="pu_target_type_wrap" style="display:none;">
                            <label class="col-sm-2 control-label">이동타입</label>
                            <div class="col-sm-10">
                                <div id="field_pu_target_type">
                                    <?php echo get_input_radio("pu_target_type", $this->config->item("popup_target_type"), $popup_row->pu_target_type); ?>
                                </div>
                                <p class="help-block">* 전체팝업클릭시 / 컨펌팝업일때 [확인] 클릭시 이동하는 URL의 이동타입</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm" id="pu_platform_wrap" style="display:none;">
                            <label class="col-sm-2 control-label">플랫폼</label>
                            <div class="col-sm-10">
                                <div id="field_pu_platform">
                                    <?php echo get_input_radio("pu_platform", $this->config->item("popup_platform"), $popup_row->pu_platform); ?>
                                </div>
                                <p class="help-block">* 전체팝업 / 컨펌팝업일때만 동작함.</p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm" id="pu_button_text_wrap" style="display:none;">
                            <label class="col-sm-2 control-label">버튼텍스트</label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_pu_button_text">
                                    <label>확인 : <input type="text" class="form-control" id="pu_button_text_confirm" name="pu_button_text_confirm" value="<?php echo $popup_row->button_text_confirm; ?>" /> </label>
                                    <label class="mgl10">취소 : <input type="text" class="form-control" id="pu_button_text_cancel" name="pu_button_text_cancel" value="<?php echo $popup_row->button_text_cancel; ?>" /></label>
                                </div>
                            </div>
                        </div>

                        <hr class="confirm_popup_input_line" style="display:none;" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">공개기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pu_termlimit_yn">
                                    <?php echo get_input_radio('pu_termlimit_yn', $this->config->item('popup_termlimit_yn'), 'N'); ?>
                                </div>
                                <div id="field_pu_termlimit_date" style="display:none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="pu_termlimit_datetime1" value="<?php echo get_date_format($popup_row->pu_termlimit_datetime1, '-'); ?>" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="pu_termlimit_datetime2" value="<?php echo get_date_format($popup_row->pu_termlimit_datetime2, '-'); ?>" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <div id="field_pu_content">
                                <textarea name="pu_content" id="pu_content" style="width:100%; height:400px; visibility:hidden;"><?php echo $popup_row->pu_content; ?></textarea>
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
    var editorElement = 'pu_content';
    var form = '#pop_update_form';

    loadingBar.show('#field_pu_content');

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

        //pu_division change
        $(form + ' [name="pu_division"]').on('change', function(){
            if( $(this).val() == '4' || $(this).val() == '5' ) {
                $('#pu_target_url_wrap').show();
                $('#pu_target_type_wrap').show();
                $('#pu_platform_wrap').show();

                if( $(this).val() == '5' ) {
                    $('#pu_button_text_wrap').show();
                    $('.confirm_popup_input_line').show();
                }
            }
            else {
                $('#pu_target_url_wrap').hide();
                $('#pu_target_type_wrap').hide();
                $('#pu_platform_wrap').hide();
                $('#pu_button_text_wrap').hide();
                $('.confirm_popup_input_line').hide();
            }
        });

        <?php if( $popup_row->pu_division == '4' || $popup_row->pu_division == '5' ) { ?>
        $('#pu_target_url_wrap').show();
        $('#pu_target_type_wrap').show();
        $('#pu_platform_wrap').show();
            <?php if( $popup_row->pu_division == '5' ) { ?>
        $('#pu_button_text_wrap').show();
        $('.confirm_popup_input_line').show();
            <?php } ?>
        <?php } ?>

        //pu_termlimit_yn click
        $(form + ' [name="pu_termlimit_yn"]').on('click', function(){
            if( $(this).val() == 'Y' ) {
                $('#field_pu_termlimit_date').show();
            }
            else {
                $('#field_pu_termlimit_date').hide();
            }
        });
        $(form + ' #pu_termlimit_yn_<?php echo $popup_row->pu_termlimit_yn; ?>').trigger('click');

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