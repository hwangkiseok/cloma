<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<style>
    .datepicker { z-index:2000 !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="apo_position" value="1" /><!--1=메인팝업-->
                        <input type="hidden" name="apo_expire_day" value="1" /><!--1=숨김만료일-->
                        <input type="hidden" name="apo_product_num" value="" /><!-- 상품번호 -->
                        <input type="hidden" name="apo_startdate" value="" /><!-- 시작일 -->
                        <input type="hidden" name="apo_enddate" value="" /><!-- 종료일 -->
                        <input type="hidden" name="apo_special_offer_seq" value="" /><!-- 기획전 seq -->

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">OS타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_os_type">
                                    <?php echo get_input_radio('apo_os_type', $this->config->item('app_popup_os_type'), 1); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">팝업크기 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_noti_type">
                                    <?php echo get_input_radio('apo_size_type', $this->config->item('app_popup_size_type'), 2); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출대상 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_view_target">
                                    <?php echo get_input_radio('apo_view_target', $this->config->item('app_popup_view_target'), 'A'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <div id="field_p_termlimit_yn">
                                    <?php echo get_input_radio('apo_termlimit_yn', $this->config->item('app_popup_termlimit_yn'), 'Y'); ?>
                                </div>
                                <div id="termlimit_date">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="apo_termlimit_datetime1" value="" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="apo_termlimit_datetime1_hour" class="form-control">
                                                <?php echo get_select_option_hour("", "00"); ?>
                                            </select>
                                            :
                                            <select name="apo_termlimit_datetime1_min" class="form-control">
                                                <?php echo get_select_option_min("", "00"); ?>
                                            </select>
                                            :00
                                        </label>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="apo_termlimit_datetime2" value="" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left mgl5">
                                        <label class="inline">
                                            <select name="apo_termlimit_datetime2_hour" class="form-control">
                                                <?php echo get_select_option_hour("", "23"); ?>
                                            </select>
                                            :
                                            <select name="apo_termlimit_datetime2_min" class="form-control">
                                                <?php echo get_select_option_min("", "59"); ?>
                                            </select>
                                            :59
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ap_display_state">
                                    <?php echo get_input_radio('apo_display_yn', $this->config->item('app_popup_display_yn'), 'Y', $this->config->item('app_popup_display_yn_text_color')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제목 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_subject">
                                    <input type="text" name="apo_subject" class="form-control" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">콘텐츠 타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_content_type">
                                    <?php echo get_input_radio('apo_content_type', $this->config->item('app_popup_content_type'), '1'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm wrapContentType_1">
                            <label class="col-sm-2 control-label">상품선택 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <label class="inline">
                                    <input type="text" class="form-control" name="kwd" maxlength="100" style="width:200px;border-radius:0;" placeholder="검색어 입력" />
                                </label>
                                <label class="inline">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="product_search();">검색</button>
                                </label>
                            </div>
                        </div>

                        <div id="pop_product_list" class="wrapContentType_1" style="max-height:500px;overflow:auto;margin-bottom:10px; display: none;"></div>

                        <div class="form-group form-group-sm wrapContentType_1">
                            <label class="col-sm-2 control-label">선택한 상품</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="p_name" maxlength="100" style="width:100%;" readonly placeholder="상품검색 후 선택하세요." />
                            </div>
                        </div>

                        <div id="special_list" class="wrapContentType_2" style="max-height:500px;overflow:auto;margin-bottom:10px; display: none;">

                            <ul class="list-group">

                                <? foreach ($aSpecialOfferLists as $k => $r) { //zsView($r); ?>

                                <li class="list-group-item col-md-offset-2 col-md-10">
                                    <div class="row">
                                        <div class="pull-left">
                                            <img class="thumbnail" src="<?=$r['banner_img']?>" alt="" height="100" style="margin: 0 0 0 15px;padding: 0;">
                                        </div>
                                        <div class="pull-left mgl5">
                                            <p><b><?=$r['thema_name']?></b></p>
                                            <p>
                                                <?if($r['activate_flag'] == 'Y'){?>
                                                    <span class="badge badge-primary">활성화</span>
                                                <?}else{?>
                                                    <span class="badge badge-danger">진열함</span>
                                                <?}?>
                                            </p>

                                            <?if($r['view_type'] == 'A'){?>
                                                <p> <span class="badge badge-info">기간한정 기획전</span><br> </p>
                                                <p style="margin-bottom: 0;"> <span><?=view_date_format($r['start_date'])?> ~ <?=view_date_format($r['end_date'])?></span> </p>
                                            <?}else{?>
                                                <p style="margin-bottom: 0;"><span class="badge badge-warning">상시 기획전</span></p>
                                            <?}?>

                                        </div>
                                        <div class="pull-right" style="height: 100px;width: 100px;margin-right: 15px;">
                                            <button type="button" class="btn btn-warning btn-block" style="height: 100%;"  onclick="special_offer_select('<?=$r['seq']?>', '<?=$r['thema_name']?>');">선택</button>
                                        </div>
                                    </div>
                                </li>
                                <? }?>

                            </ul>

                        </div>

                        <div class="form-group form-group-sm wrapContentType_2" style="display: none;">
                            <label class="col-sm-2 control-label">선택한 기획전</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="special_offer_name" maxlength="100" style="width:100%;" readonly placeholder="기획전을 선택하세요." />
                            </div>
                        </div>


                        <!--
                        <div class="form-group form-group-sm wrapContentType_3">
                            <label class="col-sm-2 control-label">공지제목</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="apo_noti_subject" maxlength="15" style="width:100%;" placeholder="공지제목을 입력해주세요." />
                            </div>
                        </div>
                        -->
                        <div class="form-group form-group-sm wrapContentType_3">
                            <label class="col-sm-2 control-label">공지내용</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" id="apo_noti_content" name="apo_noti_content" maxlength="150" style="width:100%;"></textarea>
                            </div>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">버튼 타입 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_btn_type">
                                    <?php echo get_input_radio('apo_btn_type', $this->config->item('app_popup_btn_type'), '1'); ?>
                                </div>
                            </div>
                        </div>

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출수 제한 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_view_limit">
                                    <input type="text" name="apo_view_limit" class="form-control" value="" />
                                    <p class="help-block">* 무제한 = 0</p>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출 페이지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_view_page">
                                    <?php foreach( $this->config->item('app_popup_view_page') as $key => $text ) { ?>
                                        <label>
                                            <input type="checkbox" name="apo_view_page[]" value="<?php echo $key; ?>" <?php echo $checked; ?> /> <?php echo $text; ?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_apo_image">
                                    <input type="file" name="apo_image" class="form-control" accept="image/*" />
                                </div>
                                <p class="help-block">* 확장자는 jpg, png만</p>
                            </div>
                        </div>

                        <!-- 이동URL => 상품일때 자동설정됨 -->
                        <!--
                        <div class="form-group form-group-sm" style="display:none;">
                            <label class="col-sm-2 control-label">이동URL</label>
                            <div class="col-sm-10">
                                <div id="field_apo_url">
                                    <input type="text" name="apo_url" class="form-control" value="" />
                                </div>
                                <p class="help-block">* 입력예: <?php echo $this->config->item("site_https"); ?>/product/detail/?p_num=상품번호</p>
                            </div>
                        </div>
                        -->
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
    var editorElement = 'apo_noti_content';
    var form = '#pop_insert_form';

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

        $('[name="apo_termlimit_yn"]').on('click change', function() {
            if( $(this).val() == 'Y' ) {
                $('#termlimit_date').show();
            }
            else {
                $('#termlimit_date').hide();
            }
        });

        //datepicker
        $('.input-group.date').datepicker({format:"yyyy-mm-dd", language:"kr", autoclose:true, todayHighlight:true});


        //콘텐츠타입 변경시
        $('[name="apo_content_type"]').on('change', function() {
            changeContentType($(this).val());
        });


        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();

            oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.

            var date1 = $(form + ' [name="apo_termlimit_datetime1"]').val();
            var hour1 = $(form + ' [name="apo_termlimit_datetime1_hour"]').val();
            var min1 = $(form + ' [name="apo_termlimit_datetime1_min"]').val();

            var date2 = $(form + ' [name="apo_termlimit_datetime2"]').val();
            var hour2 = $(form + ' [name="apo_termlimit_datetime2_hour"]').val();
            var min2 = $(form + ' [name="apo_termlimit_datetime2_min"]').val();

            var date1Array = date1.split("-");
            var date2Array = date2.split("-");
            var startdate = date1Array[0] + date1Array[1] + date1Array[2] + hour1 + min1 + "00";
            var enddate = date2Array[0] + date2Array[1] + date2Array[2] + hour2 + min2 + "59";

            $("input[name='apo_startdate']").val(startdate);
            $("input[name='apo_enddate']").val(enddate);

            if($('[name="apo_termlimit_yn"]') == 'Y' && (date1 === '' || date2 === '')) {
                alert('노출기간을 입력해주세요.');
                return false;
            }

            if($("input[name='apo_subject']").val() == '') {
                alert('제목을 입력해주세요.');
                return false;
            }

            if( $("input[name='apo_content_type']:checked").val() == '1' && $("input[name='apo_product_num']").val() == '') {
                alert('상품을 선택해주세요.');
                return false;
            }
            else if( $("input[name='apo_content_type']:checked").val() == '2' && $("input[name='apo_special_offer_seq']").val() == '') {
                alert('기획전을 선택해주세요.');
                return false;
            }
            else if( $("input[name='apo_content_type']:checked").val() == '3' && $("input[name='apo_noti_content']").val() == '') {
                alert('공지사항 내용을 입력해주세요.');
                return false;
            }

            if($("input[name='apo_content_type']:checked").val() != '3' && $('input[name="apo_image"]').val() == ''){
                alert('이미지를 등록해주세요');
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

    /**
     * 콘텐츠타입 변경시 wrap 설정
     */
    function changeContentType(v) {
        if( empty(v) ) {
            var v = $('[name="apo_content_type"]:checked').val();
        }

        $('.wrapContentType_1').hide();
        $('.wrapContentType_2').hide();
        $('.wrapContentType_' + v).show();
    }//end changeContentType;

    /**
     * 상품 검색
     */
    function product_search(url) {
        var kwd = $(form + ' input[name="kwd"]').val();
        if( empty(kwd) ) {
            $(form + ' input[name="kwd"]').focus();
            return false;
        }
        if( empty(url) ) {
            url = '/product/search_ajax/?page=1';
        }

        loadingBar.show($('#pop_product_list'));

        $('#pop_product_list').show();

        $.ajax({
            url : url,
            data : {kfd:'p_name', kwd:kwd},
            type : 'post',
            //dataType : 'html',
            dataType : 'json',
            success : function (result) {

                var html = get_product_list_html(result.data);
                $('#pop_product_list').html(html);

                loadingBar.hide();
            },
            error : function() {
                loadingBar.hide();
            }
        });
    }//end of product_search()

    /**
     * 상품 출력
     */
    function get_product_list_html(data) {

        if( empty(data) ) {
            return false;
        }

        var html = '';
        html += '<ul class="list-group">';

        $.each(data, function(index, item){

            var disp_text = '<span class="badge badge-primary">진열함</span>';
            var sale_text = '<span class="badge badge-primary">판매중</span>';
            var stock_text = '<span class="badge badge-primary">재고있음</span>';

            if( item.p_display_state == 'N' ) {
                disp_text = '<span class="badge badge-danger">진열안함</span>';
            }
            if( item.p_sale_state == 'N' ) {
                sale_text = '<span class="badge badge-danger">판매종료</span>';
            }
            if( item.p_stock_state == 'N' ) {
                stock_text = '<span class="badge badge-danger">품절</span>';
            }

            html += '<li class="list-group-item col-md-offset-2 col-md-10">';
            html += '   <div class="row">';
            html += '       <div class="pull-left"><img class="thumbnail" src="' + item.p_rep_image_array[1] + '" alt="" _width="60" style="margin-bottom: 0;height: 100px" /></div>';
            html += '       <div class="pull-left mgl5">';
            html += '           <p><b>' + item.p_name + '</b></p>';
            html += '           <p>' + disp_text + ' ' + sale_text + ' ' + stock_text + '</p>';
            html += '       </div>';
            html += '       <div class="pull-right" style="width: 100px;height: 100px;margin-right: 15px;" ><button type="button" class="btn btn-warning btn-block" style="height: 100%;" onclick="product_select(\'' + item.p_num + '\', \'' + item.p_name + '\', \'' + item.p_rep_image_array[1] + '\');">선택</button></div>';
            html += '   </div>';
            html += '</li>';
        });

        html += '</ul>';

        return html;
    }//end of product_list_print()

    /**
     * 상품 선택
     */
    function product_select(p_num, p_name, p_img) {
        info_message_all_clear();

        if( empty(p_num) ) {
            alert('잘못된 접근입니다.');
            return false;
        }

        $(form + ' [name="p_name"]').val(p_name);
        $(form + ' [name="apo_product_num"]').val(p_num);

        $('#pop_product_list').hide();
    }//end of product_select()

    function special_offer_select(seq, thema_name) {

        info_message_all_clear();

        if( empty(seq) ) {
            alert('잘못된 접근입니다.');
            return false;
        }

        $(form + ' [name="special_offer_name"]').val(thema_name);
        $(form + ' [name="apo_special_offer_seq"]').val(seq);

        $('#special_list').hide();

    }

</script>