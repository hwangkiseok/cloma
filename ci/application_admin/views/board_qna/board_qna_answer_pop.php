<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->answer_proc; ?>">
                        <input type="hidden" name="bq_num" value="<?php echo $board_qna_row['bq_num']; ?>" />
                        <input type="hidden" name="bq_flag" value="" />
                        <input type="hidden" name="push_yn" value="" />

                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group">
                                <span class="input-group-addon" style="width:110px;">문의일시</span>
                                <input type="text" class="form-control" value="<?php echo get_datetime_format($board_qna_row['bq_regdatetime']); ?>" readonly="readonly" style="background:#fff;" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group">
                                <span class="input-group-addon" style="width:110px;">닉네임</span>
                                <input type="text" class="form-control" value="<?php echo $board_qna_row['m_nickname']; ?>" readonly="readonly" style="background:#fff;" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group">
                                <span class="input-group-addon" style="width:110px;">문의유형</span>
                                <input type="text" class="form-control" value="<?php echo $this->config->item($board_qna_row['bq_category'], 'board_qna_category'); ?>" readonly="readonly" style="background:#fff;" />
                            </div>
                        </div>
                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group col-md-12">
                                <span class="input-group-addon" style="width:110px;">문의내용</span>
                                <div class="br-r3" style="border:1px solid #ccc;padding:10px;">
                                    <?php if ($board_qna_row['bq_display_state_1'] == 'N') { ?>
                                        <p style="font-weight:bold;color:red;margin:0;">[고객이 삭제한 문의글]</p>
                                    <?php } ?>
                                    <?php if ( !empty($board_qna_row->bq_product_name) ) { echo "상품명 : " . $board_qna_row['bq_product_name'] . "<br />"; } ?>
                                    <?php if ( !empty($board_qna_row->bq_name) ) { echo "이름 : " . $board_qna_row['bq_name'] . "<br />"; } ?>
                                    <?php if ( !empty($board_qna_row->bq_contact) ) { echo "연락처 : " . $board_qna_row['bq_contact'] . "<br />"; } ?>
                                    <?php if ( !empty($board_qna_row->bq_refund_info) ) { echo "환불계좌 : " . $board_qna_row['bq_refund_info'] . "<br />"; } ?>
                                    <p style="margin-bottom: 0;" class="bq_cont <?php if ( !empty($board_qna_row['bq_display_state_1'] == 'N') ) { ?>line<?php } ?>"><?php echo nl2br(stripslashes($board_qna_row['bq_content'])); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($board_qna_row['bq_file'])) { ?>
                            <div class="form-group form-group-sm form-inline">
                                <div class="input-group col-md-12">
                                    <span class="input-group-addon" style="width:110px;">첨부파일</span>
                                    <div class="br-r3" style="border:1px solid #ccc;padding:10px;">
                                        <a href="#none" onclick="new_win_open('/download/?m=view&f=<?php echo urlencode($board_qna_row['bq_file']); ?>', 'img_win', 1000, 800); "><i class="fa fa-file-image-o" aria-hidden="true"></i>&nbsp;<?php echo pathinfo($board_qna_row['bq_file'], PATHINFO_BASENAME); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php }//endif; ?>

                        <hr />

                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group">
                                <span class="input-group-addon">푸시발송</span>
                                <div class="br-r3" style="border:1px solid #ccc;padding:5px 10px;">
                                    <label><input name="send_push" id="send_push" type="checkbox" value="Y" >&nbsp;활성화</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm form-inline">
                            <div class="input-group">
                                <span class="input-group-addon">문구선택</span>
                                <div class="br-r3" style="border:1px solid #ccc;padding:5px 10px;">

                                    <!-- 자주 사용하는 문구 -->

                                    <button type="button" class="btn"
                                        value="<p>-------------------------------------------------------------------------------------------------</p>"
                                        onclick="word_use_insert(this.value)">구분선</button>

                                    <?php
                                        foreach ($word_use_list as $key => $value) {
                                            if ($value->wd_view == 'Y') {
                                                ?>
                                                <button type="button" class="btn"
                                                        value="<?php echo htmlspecialchars(nl2br(str_replace("%%%", $_SESSION['GroupMemberNick'], $value->wd_content))); ?><br />"
                                                        onclick="word_use_insert(this.value)"><?php echo $value->wd_subject; ?></button>
                                                <?php
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div><font color='red'><b>※  구분선입력시에만 1,2,3차 답변이 구분됨을 유의하세요.</b></font></div>
                        </div>

                        <div class="form-group form-group-sm">
                            <div id="field_bq_answer_content">
                                <textarea name="bq_answer_content" id="bq_answer_content" style="width:100%; height:200px; visibility:hidden;"><?php echo $board_qna_row->bq_answer_content; ?></textarea>
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

    // mysdis\www\plugins\smarteditor2\sample\photo_uploader\photo_uploader_not_html5.html 파일참조 ==> 특정 게시판 아이디로만 싱글업로드 분기처리
    //====================================== smarteditor2
    var oEditors = [];
    var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    var editorElement = 'bq_answer_content';
    var form = '#pop_update_form';

    loadingBar.show('#field_bq_answer_content');

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
        //submit check
        $(form).on('submit', function(){
            if( empty(oEditors) ) {
                alert('HTML 에디터 로딩중입니다... \nHTML 에디터 로딩 완료후에 시도해 주세요.');
                return false;
            }

            if( !confirm('문의글에 답변을 등록하시겠습니까?') ) {
                return false;
            }

            info_message_all_clear();
            oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.

            if( !$(form + ' [name="bq_answer_content"]').val() ) {
                error_message($('#field_bq_content'), '내용을 입력하세요.');
                return false;
            }

            Pace.restart();
        });


        $("#send_push").on('click', function() {
            if($("#send_push").is(":checked") == true) {
                $("input[name='push_yn']").val("Y");
            } else {
                $("input[name='push_yn']").val("N");
            }
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

    var word_use_insert = function(v) {
        oEditors.getById['bq_answer_content'].exec('PASTE_HTML', [v]);
    }
</script>