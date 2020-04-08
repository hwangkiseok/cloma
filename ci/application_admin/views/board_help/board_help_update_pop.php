<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="bh_num" value="<?php echo $board_help_row->bh_num; ?>" />

                        <div class="form-group form-group-sm form-inline">
                            <label class="col-sm-2 control-label">게시판 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="bh_division" id="field_bh_division" class="form-control" style="width:auto;">
                                    <?php echo get_select_option('', $this->config->item('board_help_division'), $board_help_row->bh_division); ?>
                                </select>
                                <label class="mgl10">
                                    <input type="checkbox" name="bh_top_yn" value="Y" <?php echo ($board_help_row->bh_top_yn == 'Y') ? 'checked' : ''; ?> /> 상위노출 (공지사항만)
                                </label>
                            </div>
                        </div>
                        <div id="category_select" class="form-group form-group-sm form-inline" style="display:none;">
                            <label class="col-sm-2 control-label">분류 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="bh_category" id="field_bh_category" class="form-control" style="width:auto;">
                                    <?php echo get_select_option('', $this->config->item('faq_category'), $board_help_row->bh_category); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field_bh_subject" name="bh_subject" maxlength="100" style="width:100%;" value="<?php echo $board_help_row->bh_subject; ?>" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_bh_usestate">
                                    <?php echo get_input_radio('bh_usestate', $this->config->item('board_help_usestate'), $board_help_row->bh_usestate, $this->config->item('board_help_usestate_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <div id="field_bh_content">
                                <textarea name="bh_content" id="bh_content" style="width:100%; height:200px; visibility:hidden;"><?php echo $board_help_row->bh_content; ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>

<script>
    //====================================== smarteditor2
    var oEditors = [];
    var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    var editorElement = 'bh_content';
    var form = '#pop_update_form';

    loadingBar.show('#field_bh_content');

    $(function(){
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: editorElement,
            sSkinURI: "/plugins/smarteditor2/SmartEditor2Skin.html",
            htParams : {
                bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                bSkipXssFilter : true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
                aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                fOnBeforeUnload : function(){
                    //alert("완료!");
                }
            }, //boolean
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
    $('#pop_update_form [name="bh_division"]').on('change', function(){
        if( $('#pop_update_form [name="bh_division"]').val() == 2 ) {
            $('#category_select').show();
        }
        else {
            $('#category_select').hide();
        }
    });

    <?php if( $board_help_row->bh_division == 2 ) { ?>
    $('#category_select').show();
    <?php } ?>

    //document.ready
    $(function(){
        //submit check
        $(form).on('submit', function(){
            if( empty(oEditors) ) {
                alert('HTML 에디터 로딩중입니다... \nHTML 에디터 로딩 완료후에 시도해 주세요.');
                return false;
            }

            info_message_all_clear();
            oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.

            if( !$(form + ' [name="bh_subject"]').val() ) {
                error_message($('#field_bh_subject'), '제목을 입력하세요.');
                return false;
            }
            if( !$(form + ' [name="bh_content"]').val() ) {
                error_message($('#field_bh_content'), '내용을 입력하세요.');
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