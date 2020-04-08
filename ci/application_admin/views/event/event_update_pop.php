<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
    .attend_day_info_item { padding-bottom:10px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_update_form" id="pop_update_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="e_num" value="<?php echo $event_row->e_num; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트종류 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_e_division">
                                    <?php echo get_input_radio('e_division', $this->config->item('event_division'), $event_row->e_division); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이벤트코드</label>
                            <div class="col-sm-10 form-inline">
                                <input type="text" id="field_e_code" class="form-control" style="width:200px" name="e_code" maxlength="30" value="<?php echo $event_row->e_code; ?>" />
                                <span style="margin-left:10px;">(* 내부처리용, 30자이하)</span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_usestate">
                                    <?php echo get_input_radio('e_display_state', $this->config->item('event_display_state'), $event_row->e_display_state, $this->config->item('event_display_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진행상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_usestate">
                                    <?php echo get_input_radio('e_proc_state', $this->config->item('event_proc_state'), $event_row->e_proc_state, $this->config->item('event_proc_state_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_subject">
                                    <input type="text" class="form-control" name="e_subject" maxlength="100" value="<?php echo $event_row->e_subject; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_termlimit_yn">
                                    <?php echo get_input_radio('e_termlimit_yn', $this->config->item('event_termlimit_yn'), $event_row->e_termlimit_yn, $this->config->item('event_termlimit_yn_text_color')); ?>
                                </div>
                                <div id="field_e_termlimit_date" style="display:none;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="e_termlimit_datetime1" name="e_termlimit_datetime1" value="<?php echo get_date_format($event_row->e_termlimit_datetime1, '-'); ?>" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" id="e_termlimit_datetime2" name="e_termlimit_datetime2" value="<?php echo get_date_format($event_row->e_termlimit_datetime2, '-'); ?>" />
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
                                <?php echo get_input_radio('e_rep_image_type', $this->config->item('event_rep_image_type'), $event_row->e_rep_image_type); ?>
                            </div>
                        </div>

                        <div class="form-group form-group-sm" id="wrap_e_rep_image_type_1">
                            <label class="col-sm-2 control-label">대표이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_e_rep_image">
                                    <input type="file" name="e_rep_image" class="form-control" />
                                </div>
                                <p class="help-block">* 이미지 변경시에만 입력하세요.</p>
                                <div class="mgt5 mgb10">
                                    <?php echo create_img_tag_from_json($event_row->e_rep_image, 1, '100', '', 'data-type="rep_img"'); ?>
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
                                <div class="mgt5">
                                    <?php if( isset($event_row->e_rep_image_ym_array[$cur_ym]) && !empty($event_row->e_rep_image_ym_array[$cur_ym]) ) { ?>
                                        <?php echo create_img_tag($event_row->e_rep_image_ym_array[$cur_ym], 0, 50); ?>
                                    <?php } ?>
                                </div>
                                <div id="field_e_rep_image_ym_2" class="input-group mgt10">
                                    <span class="input-group-addon"><?php echo $next_m; ?>월</span>
                                    <input type="file" name="e_rep_image_ym_2" class="form-control" />
                                </div>
                                <div class="mgt5">
                                    <?php if( isset($event_row->e_rep_image_ym_array[$next_ym]) && !empty($event_row->e_rep_image_ym_array[$next_ym]) ) { ?>
                                        <?php echo create_img_tag($event_row->e_rep_image_ym_array[$next_ym], 0, 50); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>


                        <!--<div class="form-group form-group-sm">-->
                        <!--    <label class="col-sm-2 control-label">연속출석일</label>-->
                        <!--    <div class="col-sm-10">-->
                        <!--        <div id="field_e_attend_day" class="input-group" style="width:100px;">-->
                        <!--            <input type="text" class="form-control" name="e_attend_day" maxlength="3" value="--><?php //echo $event_row->e_attend_day; ?><!--" />-->
                        <!--            <span class="input-group-addon">일</span>-->
                        <!--        </div>-->
                        <!--        <p class="mgt5">* 출석체크이벤트에만 적용됩니다.</p>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <div class="form-group form-group-sm attend" id="attend_day_info_wrap" style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;background:#fafafa;">

                            <?php if( !empty($event_row->e_attend_day_info_array) ) { ?>

                                <?php
                                $idx = 0;
                                foreach ($event_row->e_attend_day_info_array as $key => $item ) {
                                ?>

                            <label class="col-sm-2 control-label"><?php if( $idx == 0 ) { ?>연속출석일설정<br>(출석이벤트전용)<?php } ?></label>
                            <div class="col-sm-10 form-inline attend_day_info_item" style="<?php if( $idx == 0 ) { ?>padding-top:10px;<?php } ?>">
                                <div class="input-group" style="width:130px;height:50px;">
                                    <span class="input-group-addon">달성일</span>
                                    <select name="attend_day[]" class="form-control" style="height:50px;">
                                        <?php echo get_select_option_day("선택", $item['day'], false); ?>
                                        <option value="99" <?php if($item['day'] == "99") { echo "selected"; } ?>>만근</option>
                                    </select>
                                </div>
                                <div class="input-group" style="width:130px;">
                                    <span class="input-group-addon">버튼명</span>
                                    <select name="attend_day_btn[]" class="form-control" style="height:50px;">
                                        <?php echo get_select_option("", $this->config->item("attend_day_info_btn"), $item['btn']); ?>
                                    </select>
                                </div>
                                <div class="input-group" style="width:200px;">
                                    <span class="input-group-addon">메시지</span>
                                    <textarea class="form-control" name="attend_day_msg[]" style="width:300px;height:50px;"><?php echo $item['msg']; ?></textarea>
                                </div>

                                    <?php if( $idx == 0 ) { ?>
                                <button type="button" class="btn btn-primary btn-sm" onclick="add_attend_day_input();"><span class="glyphicon glyphicon-plus"></span></button>
                                    <?php } else { ?>
                                <button type="button" class="btn btn-danger btn-sm" onclick="del_attend_day_input(this);"><span class="glyphicon glyphicon-minus"></span></button>
                                    <?php } ?>
                            </div>

                                <?php
                                    $idx++;
                                }//end of foreach()
                                ?>

                            <?php } else { ?>

                            <label class="col-sm-2 control-label">연속출석일설정<br>(출석이벤트전용)</label>
                            <div class="col-sm-10 form-inline attend_day_info_item" style="padding-top:10px;">
                                <div class="input-group" style="width:130px;height:50px;">
                                    <span class="input-group-addon">달성일</span>
                                    <select name="attend_day[]" class="form-control" style="height:50px;">
                                        <?php echo get_select_option_day("선택", "", false); ?>
                                        <option value="99">만근</option>
                                    </select>
                                </div>
                                <div class="input-group" style="width:130px;">
                                    <span class="input-group-addon">버튼명</span>
                                    <select name="attend_day_btn[]" class="form-control" style="height:50px;">
                                        <?php echo get_select_option("", $this->config->item("attend_day_info_btn"), ""); ?>
                                    </select>
                                </div>
                                <div class="input-group" style="width:200px;">
                                    <span class="input-group-addon">메시지</span>
                                    <textarea class="form-control" name="attend_day_msg[]" style="width:300px;height:50px;"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="add_attend_day_input();"><span class="glyphicon glyphicon-plus"></span></button>
                            </div>

                            <?php } ?>

                            <p class="help-block" style="padding-left:18.5%;">* 달성일을 입력하지 않으면, 적용되지 않습니다. 달성일이 만근일때는 날짜가 아닌 &quot;만근&quot;을 선택하세요.</p>
                            <p class="help-block" style="padding-left:18.5%;">* 고정출석이벤트에만 적용됩니다.</p>
                        </div>

                        <div class="form-group form-group-sm attend">
                            <label class="col-sm-2 control-label">Alert메시지</label>
                            <div class="col-sm-10">
                                <div id="field_e_alert_message">
                                    <input type="text" class="form-control" name="e_alert_message" maxlength="500" value="<?php echo $event_row->e_alert_message; ?>" />
                                </div>
                                <p class="mgt5">* 노출안함, 이벤트종료일때만 출력됩니다.</p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm attend">
                            <label class="col-sm-2 control-label">참여후이동타입</label>
                            <div class="col-sm-10">
                                <div id="field_e_after_type">
                                    <?php echo get_input_radio("e_after_type", $this->config->item("event_after_type"), $event_row->e_after_type); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상세타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <?php echo get_input_radio("e_content_type", $this->config->item("event_content_type"), $event_row->e_content_type); ?>
                                <p>* HTML은 현재 내용만 입력 가능, 이미지일때 금월, 내월의 상세이미지 입력 가능, 해당월에 이미지가 없을때 HTML 내용 사용됨.</p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm daliy_roulette">
                            <label class="col-sm-2 control-label">경품리스트</label>



                            <?
                            $init_ym = '';
                            foreach ($aGiftCode as $key => $row) { //zsView($row);

                            if($row['gift_ym'] != $init_ym){ $init_ym = $row['gift_ym'];?>

                            <div class="col-sm-10 form-inline <?=$key>0?'col-sm-offset-2':''?>">
                                <p class="form-control-static alert alert-warning "  style="margin-bottom: 5px;"><?=$row['gift_ym']?> 기프티콘</p>
                            </div>
                            <? } ?>

                            <div class="col-sm-10 form-inline col-sm-offset-2">
                                <span style="width:200px;display: inline-block ">[<?=$row['gift_name']?>] 일당 발급 가능수 : </span>
                                <input type="number" class="form-control" style="width:100px" name="daliy_issue_cnt[<?=$row['seq']?>]" maxlength="30" value="<?=$row['daliy_issue_cnt']?>">
                                <span style="margin-left:10px;">( 0 = 무제한 )</span>

                                <span style="width:80px;display: inline-block;text-align: right ">가중치 : </span>
                                <input type="number" class="form-control" style="width:100px" name="gift_weighted_arr[<?=$row['seq']?>]" maxlength="30" value="<?=$row['gift_weighted']?>">
                                <span style="margin-left:10px;">%</span>

                            </div>

                            <?} ?>
                        </div>

                        <div class="form-group form-group-sm" id="e_content_wrap">
                            <div id="field_e_content">
                                <textarea name="e_content" id="e_content" style="width:100%; height:200px; visibility:hidden;"><?php echo $event_row->e_content; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group form-group-sm" id="e_content_image_wrap" style="display:none;">
                            <label class="col-sm-2 control-label">상세이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group" id="field_e_content_image_1">
                                    <span class="input-group-addon"><?php echo $cur_m; ?>월</span>
                                    <input type="file" class="form-control" name="e_content_image_1" value="" />
                                </div>
                                <?php if( isset($event_row->e_content_image_array[$cur_ym]) && !empty($event_row->e_content_image_array[$cur_ym]) ) { ?>
                                    <?php echo create_img_tag($event_row->e_content_image_array[$cur_ym], 0, 50); ?>
                                <?php } ?>
                                <div class="input-group mgt5" id="field_e_content_image_2">
                                    <span class="input-group-addon"><?php echo $next_m; ?>월</span>
                                    <input type="file" class="form-control" name="e_content_image_2" value="" />
                                </div>
                                <?php if( isset($event_row->e_content_image_array[$next_ym]) && !empty($event_row->e_content_image_array[$next_ym]) ) { ?>
                                    <?php echo create_img_tag($event_row->e_content_image_array[$next_ym], 0, 50); ?>
                                <?php } ?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?//zsView($aGiftCode); ?>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>
<script src="/plugins/smarteditor2/js/HuskyEZCreator.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/smarteditor2/js/HuskyEZCreator.js"); ?>" charset="utf-8"></script>

<script>
    //====================================== smarteditor2
    var oEditors = [];
    var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    var editorElement = 'e_content';
    var form = '#pop_update_form';

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
    var input_limit = 10;

    /**
     * 연속출석일 설정 입력란 추가
     */
    function add_attend_day_input() {
        //갯수 제한
        if( $('.attend_day_info_item').length >= input_limit ) {
            alert('연속출석일 설정은 최대 ' + input_limit + '개까지만 가능합니다.');
            return false;
        }

        var html = '';
        html += '<label class="col-sm-2"></label>';
        html += '<div class="col-sm-10 form-inline attend_day_info_item">';
        html += '   <div class="input-group" style="width:130px;height:50px;">';
        html += '        <span class="input-group-addon">달성일</span>';
        html += '           <select name="attend_day[]" class="form-control" style="height:50px;">';
        html += '               <?php echo get_select_option_day("선택", "", false); ?>';
        html += '               <option value="99">만근</option>';
        html += '           </select>';
        html += '   </div>';
        html += '   <div class="input-group" style="width:130px;">';
        html += '       <span class="input-group-addon">버튼명</span>';
        html += '       <select name="attend_day_btn[]" class="form-control" style="height:50px;">';
        html += '           <?php echo get_select_option("", $this->config->item("attend_day_info_btn"), ""); ?>';
        html += '       </select>';
        html += '   </div>';
        html += '   <div class="input-group" style="width:200px;">';
        html += '       <span class="input-group-addon">메시지</span>';
        html += '       <textarea class="form-control" name="attend_day_msg[]" style="width:300px;height:50px;"></textarea>';
        html += '   </div>';
        html += '   <button type="button" class="btn btn-danger btn-sm" onclick="del_attend_day_input(this);"><span class="glyphicon glyphicon-minus"></span></button>';
        html += '</div>';

        $('#attend_day_info_wrap .attend_day_info_item:last').after(html);
    }//end of add_attend_day_info()

    /**
     * 연속출석일 설정 입력란 삭제
     */
    function del_attend_day_input(obj) {
        $(obj).parent('.attend_day_info_item').prev('label').remove();
        $(obj).parent('.attend_day_info_item').remove();
    }//end of del_attend_day_input()

    /**
     * 상세내용 타입별 영역 show/hide
     */
    function set_content_type() {
        var typeVal = $('[name="e_content_type"]:checked').val();

        $('#e_content_wrap').hide();
        $('#e_content_image_wrap').hide();

        if( typeVal == 1 ) {
            $('#e_content_wrap').show();
        }
        else if( typeVal == 2 ) {
            $('#e_content_image_wrap').show();
        }
    }//end of set_content_type()

    /**
     * 대표이미지 타입에 따른 입력폼 보임/안보임
     */
    function set_wrap_rep_image() {
        var type_val = $('[name="e_rep_image_type"]:checked').val();
        $('[id*="wrap_e_rep_image_type_"]').hide();
        $('#wrap_e_rep_image_type_' + type_val).show();
    }//end of set_wrap_rep_image()




    //document.ready
    $(function(){


        var e_division = '<?=$event_row->e_division?>';

        if(e_division == 3){
            $('form[name="pop_update_form"] .daliy_roulette').show();
            $('form[name="pop_update_form"] .attend').hide();
        }else{
            $('form[name="pop_update_form"] .daliy_roulette').hide();
            $('form[name="pop_update_form"] .attend').show();
        }


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
        $(form + ' #e_termlimit_yn_<?php echo $event_row->e_termlimit_yn; ?>').trigger('click');

        //대표이미지 타입 click
        $('[name="e_rep_image_type"]').on('click', function () {
            //$('[id*="wrap_e_rep_image_type_"]').hide();
            //$('#wrap_e_rep_image_type_' + $(this).val()).show();
            set_wrap_rep_image();
        });

        set_wrap_rep_image();

        //상세내용 타입 click
        $('[name="e_content_type"]').on('click', function () {
            set_content_type();
        });
        set_content_type();



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