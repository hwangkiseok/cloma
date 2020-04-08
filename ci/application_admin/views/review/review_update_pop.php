<style>
    .form-control-static { padding-left:0 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="/review/update_proc">
                        <input type="hidden" name="re_num" value="<?php echo $req['re_num']; ?>" />
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 대상글</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo $review_row->table_num_name; ?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 작성일시</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo get_datetime_format($review_row->re_regdatetime); ?></p>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <?php if( $review_row->re_admin == "Y" ) { ?>

                                <label class="col-sm-2 control-label">작성자 <span class="txt-danger">*</span></label>
                                <div class="col-sm-10 form-inline">
                                    <input type="text" name="re_name" id="field_re_name" class="form-control" style="width:200px;" value="<?php echo $review_row->re_name; ?>" /> <span class="mgl10">(관리자작성글)</span>
                                </div>

                            <?php } else { ?>

                                <label class="col-sm-2 control-label">작성자</label>
                                <div class="col-sm-10 form-inline">
                                    <p class="form-control-static"><?php echo $review_row->m_nickname; ?></p>
                                </div>

                            <?php } ?>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">참고 <span class="txt-danger">*</span></label><?/*블라인드*/?>
                            <div class="col-sm-10">
                                <?php echo get_input_radio("re_blind", $this->config->item("comment_blind"), $review_row->re_blind, $this->config->item("comment_blind_text_color")); ?>
                                <p class="form-control-static">* 유저불만 처리 시 사용 (댓글을 남긴 유저만 보이고 다른 유저는 보이지 않음)</p>
                                <?php if($review_row->re_blind == "Y") { ?>
                                    <p class="form-control-static">(참고 처리일시 : <?php echo get_datetime_format($review_row->re_blind_regdatetime); ?>)</p><?/*블라인드*/?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">참고 메모</label> <?/*블라인드*/?>
                            <div class="col-sm-10">
                                <textarea name="re_blind_memo" id="field_re_blind_memo" class="form-control" style="width:100%;height:60px;"><?php echo $review_row->re_blind_memo; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_display_state">
                                    <?php echo get_input_radio("re_display_state", $this->config->item("comment_display_state"), $review_row->re_display_state, $this->config->item("comment_display_state_text_color")); ?>
                                    <p class="form-control-static">* 광고, 욕설 처리 시 사용 (댓글을 남긴 유저와 다른 유저 모두에게 보이지 않음)</p>
                                </div>
                            </div>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품상세노출 여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_display_state">
                                    <label>
                                        <input type="radio" name="re_recommend" value="Y" <?=$review_row->re_recommend=='Y'?'checked':''?> >노출&nbsp;
                                    </label>
                                    <label style="color: red">
                                        <input type="radio" name="re_recommend" value="N" <?=$review_row->re_recommend=='N' || $review_row->re_recommend == ''?'checked':''?> >노출안함
                                    </label>
                                    <p class="form-control-static">* 상품상세 추천리뷰 노출 시 사용</p>
                                </div>
                            </div>
                        </div>

                        <?php if($req['re_admin'] == 'N'): ?>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">적립금처리 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_display_state">
                                    <div id="point_display_before">
                                        <?php echo get_input_radio("reward_type_detail", $this->config->item("point_display_state"), "", $this->config->item("point_display_state_text_color")); ?>

                                        <input type="button" onclick="reg_point('<?php echo $req['re_num']; ?>', 'Y');" value="적용">
                                    </div>
                                    <div id="point_display_after">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label"> 상품만족도 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_re_grade">
                                    <?php if( $review_row->re_admin == "Y" ) { ?>
                                        <label>
                                            <input type="radio" name="re_grade" value="A" <?=$review_row->re_grade=='A' || $review_row->re_grade == ''?'checked':''?> >완전 추천해요!&nbsp;
                                        </label>
                                        <label>
                                            <input type="radio" name="re_grade" value="B" <?=$review_row->re_grade=='B'?'checked':''?>>추천해요!&nbsp;
                                        </label>
                                        <label>
                                            <input type="radio" name="re_grade" value="C" <?=$review_row->re_grade=='C'?'checked':''?>>아쉬워요
                                        </label>
                                    <?}else{?>
                                        <p class="form-control-static">
                                        <?if($review_row->re_grade == 'A'){?>
                                            완전 추천해요!
                                        <?}else if($review_row->re_grade == 'B'){?>
                                            추천해요!
                                        <?}else if($review_row->re_grade == 'C'){?>
                                            아쉬워요
                                        <?}?>
                                        </p>
                                    <?}?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지첨부</label>
                            <div class="col-sm-10">
                                <?
                                $aReImg = json_decode($review_row->re_img);
                                if(count($aReImg) > 0){
                                    foreach ($aReImg as $k => $val) {?>
                                        <a href="#none" style="display: inline-block;" class="thumbnail review_thumbnail<?=$k+1?>" >
                                            <img src="<?=$val?>" width="100" alt="" onclick="new_win_open('<?=$val?>', 'img_pop', 800, 600);">
                                            <div class="caption text-center" style="padding-bottom: 0">
                                                <button class="btn btn-danger btn-xs" onclick="review_img_del('<?=$k+1?>');">삭제</button>
                                            </div>
                                        </a>
                                    <?}
                                    echo '<br>';
                                }
                                ?>

                                <div id="field_re_display_state">
                                    <input type="file" name="review_file[]" multiple value="찾아보기" accept="image/*" >
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글 내용</label>
                            <div class="col-sm-10">
                                <?php if( $review_row->re_admin == "Y" ) { ?>
                                    <textarea name="re_content" id="field_re_content" class="form-control" style="width:100%;height:100px;"><?php echo $review_row->re_content; ?></textarea>
                                <?php } else { ?>
                                    <div id="field_re_content" style="width:100%;position:relative;display:table;border:1px solid #eee;padding:10px;color:#000;"><?php echo nl2br($review_row->re_content); ?></div>
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
    //var oEditors = [];
    //var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    //var editorElement = 're_content';
    //
    //$(function(){
    //    if( $('#re_content').length > 0 ) {
    //        loadingBar.show('#field_re_content');
    //
    //        nhn.husky.EZCreator.createInIFrame({
    //            oAppRef: oEditors,
    //            elPlaceHolder: editorElement,
    //            sSkinURI: "/plugins/smarteditor2/SmartEditor2Skin.html",
    //            htParams : {
    //                bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
    //                bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
    //                bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
    //                aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
    //                fOnBeforeUnload : function(){
    //                    //alert("완료!");
    //                }
    //            }, //boolean
    //            fOnAppLoad : function(){
    //                //oEditors.getById[editorElement].exec("PASTE_HTML", ['<p style="font-family:Noto Sans;font-size:1em;">&nbsp;</p>']);
    //                //oEditors.getById[editorElement].setDefaultFont("Noto Sans", "1em");
    //                loadingBar.hide();
    //            },
    //            fCreator: "createSEditor2"
    //        });
    //    }
    //});
    //====================================== /smarteditor2

    function review_img_del(seq){

        var cf = confirm('해당 이미지를 삭제하시겠습니까?');
        if(!cf) return false;

        $.ajax({
            url: '/review/img_delete/',
            data: { img_seq : seq , re_num : $('input[name="re_num"]').val() },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                if(result.msg) alert(result.msg);
                if(result.success == true) $('.review_thumbnail'+seq).remove();
            }
        });

    }

    // 적립금 적용
    function reg_point(re_num) {

        if ($('input[name="reward_type_detail"]:checked').length < 1) {
            alert('적용할 적립금을 선택해주세요.');
            return false;
        }

        var reward_type = $("input[name='reward_type_detail']:checked").val();

        var cf = confirm('해당 적립금을 적용하시겠습니까?');
        if(!cf) return false;

        $.ajax({
            url: '/point/insertReviewPointMemberAjax/',
            data: { reward_type : $('input[name="reward_type_detail"]:checked').val() , re_num : $('input[name="re_num"]').val() },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                $('#search_form').submit();
                alert(result.msg);
                chk_pointmember('<?php echo $req["re_num"]; ?>');
            }
        });
    }

    // 적립금 삭제
    function delete_point(re_num) {

        var cf = confirm('지급하신 적립금을 회수하시겠습니까?');
        if(!cf) return false;

        $.ajax({
            url: '/point/deleteReviewPointMember/',
            data: { re_num : re_num },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                $('#search_form').submit();
                alert(result.msg);
                chk_pointmember('<?php echo $req["re_num"]; ?>');
                //$('input[name="reward_type"]').removeAttr('checked');
            }
        });
    }

    // 기지급 체크
    function chk_pointmember(re_num) {
        $.ajax({
            url: '/point/chkReviewReward/',
            data: { re_num : $('input[name="re_num"]').val() },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                if(result.reward_yn == 'Y') {
                    $("#point_display_before").hide();
                    $("#point_display_after").html(result.reward_type + ' ' +
                        "<input type='button' onclick=\"delete_point('<?php echo $req['re_num']; ?>');\" value='적립금회수'>"
                    );
                    $("#point_display_after").show();
                } else {
                    $("#point_display_before").show();
                    $("#point_display_after").hide();
                }
            }
        });
    }

    //document.ready
    $(function(){
        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();
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

        chk_pointmember('<?php echo $req["re_num"]; ?>');
    });//end of document.ready()
</script>