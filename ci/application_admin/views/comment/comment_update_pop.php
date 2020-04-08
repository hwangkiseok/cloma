<style>
    .form-control-static { padding-left:0 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="/comment/update_proc">
                        <input type="hidden" name="cmt_num" value="<?php echo $req['cmt_num']; ?>" />
                        <input type="hidden" name="cmt_profile_img" value="<?php echo $comment_row['cmt_profile_img']; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">구분</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo get_config_item_text($comment_row['cmt_table'], "comment_table"); ?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 대상글</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo $comment_row['table_num_name']; ?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 작성일시</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo get_datetime_format($comment_row['cmt_regdatetime']); ?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <?php if( $comment_row['cmt_admin'] == "Y" ) { ?>
                                <label class="col-sm-2 control-label">작성자 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10 form-inline">
                                    <input type="text" name="cmt_name" id="field_cmt_name" class="form-control" style="width:200px;" value="<?php echo $comment_row['cmt_name']; ?>" /> <span class="mgl10">(관리자작성글)</span>
                                </div>
                            <?php } else { ?>
                                <label class="col-sm-2 control-label">작성자</label>
                                <div class="col-sm-10 form-inline">
                                    <p class="form-control-static"><?php echo $comment_row['m_nickname']; ?></p>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">참고 <span class="txt-danger">*</span></label><?/*블라인드*/?>
                            <div class="col-sm-10">
                                <?php echo get_input_radio("cmt_blind", $this->config->item("comment_blind"), $comment_row['cmt_blind'], $this->config->item("comment_blind_text_color")); ?>
                                <p class="form-control-static">* 유저불만 처리 시 사용 (댓글을 남긴 유저만 보이고 다른 유저는 보이지 않음)</p>
                                <?php if($comment_row['cmt_blind'] == "Y") { ?>
                                    <p class="form-control-static">(참고 처리일시 : <?php echo get_datetime_format($comment_row['cmt_blind_regdatetime']); ?>)</p><?/*블라인드*/?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmt_display_state">
                                    <?php echo get_input_radio("cmt_display_state", $this->config->item("comment_display_state"), $comment_row['cmt_display_state'], $this->config->item("comment_display_state_text_color")); ?>
                                    <p class="form-control-static">* 광고, 욕설 처리 시 사용 (댓글을 남긴 유저와 다른 유저 모두에게 보이지 않음)</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 내용</label>
                            <div class="col-sm-10">
                                <?php if( $comment_row['cmt_admin'] == "Y" ) { ?>
                                    <textarea name="cmt_content" id="cmt_content" class="form-control" style="width:100%;height:100px;"><?php echo $comment_row['cmt_content']; ?></textarea>
                                <?php } else { ?>
                                    <div id="__field_cmt_content" style="width:100%;position:relative;display:table;border:1px solid #eee;padding:10px;color:#000;"><?php echo nl2br($comment_row['cmt_content']); ?></div>
                                <?php } ?>
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
    var form = '#pop_update_form';

    //====================================== smarteditor2
    var oEditors = [];
    var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    var editorElement = 'cmt_content';

    $(function(){
       if( $('textarea[name="cmt_content"]').length > 0 ) {
           loadingBar.show('#cmt_content');

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
               }, //boolean
               fOnAppLoad : function(){
                   //oEditors.getById[editorElement].exec("PASTE_HTML", ['<p style="font-family:Noto Sans;font-size:1em;">&nbsp;</p>']);
                   //oEditors.getById[editorElement].setDefaultFont("Noto Sans", "1em");
                   loadingBar.hide();
               },
               fCreator: "createSEditor2"
           });
       }
    });
    //====================================== /smarteditor2

    /**
     * 프로필 이미지 선택
     * @param obj
     */
    function select_profile_img(obj) {
        var url = $(obj).data('url');
        $('[name="cmt_profile_img"]').val(url);
        $('#select_profile_img').html('<img src="' + url + '" alt="" />');
    }//end of select_profile_img()

    //document.ready
    $(function(){
        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();

            <?php if( $comment_row['cmt_admin'] == "Y" ) { ?>

            oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.

            if( !$(form + ' [name="cmt_name"]').val() ) {
                error_message($('#field_cmt_name'), '제목을 입력하세요.');
                return false;
            }
            if( !$(form + ' [name="cmt_content"]').val() ) {
                error_message($('#field_cmt_content'), '내용을 입력하세요.');
                return false;
            }
            <?}?>
            Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            //beforeSubmit: function(formData, jqForm, options) {
            //    Pace.restart();
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
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()

    // textarea 커서 위치에 text 삽입
    function insertAtCaret(areaId, text) {
        if(text != '') {
            text = text+'\n';
            var txtarea = document.getElementById(areaId);
            if (!txtarea) {
                return;
            }

            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br == "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br == "ff") {
                strPos = txtarea.selectionStart;
            }

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;
            if (br == "ie") {
                txtarea.focus();
                var ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }
    }
</script>