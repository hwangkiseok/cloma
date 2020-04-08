<style>
    table tr.on { background:#FFF0F0; }
    table tr.on:hover { background:#FFF0F0; }
    table tr.on:focus { background:#FFF0F0; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="cmt_table_num" value="<?php echo $req['tb_num']; ?>" />
                        <input type="hidden" name="cmt_profile_img" value="" />





                        <div class="form-group form-group-sm" id="cmt_table_wrap">
                            <label class="col-sm-2 control-label">구분 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10 form-inline">
                                <select name="cmt_table" id="field_cmt_table" class="form-control" style="width:auto;">
                                    <?php echo get_select_option("* 선택 *", $this->config->item('comment_table'), $req['tb']); ?>
                                </select>

                                <label style="margin-left:20px;">&gt;&gt; 검색 <input type="text" name="kwd" value="" class="form-control" /></label>

                                <span id="table_data_name" class="pull-right" style="font-weight:bold;margin-left:20px;line-height:30px;"></span>
                            </div>
                        </div>

                        <div id="table_item_list" style="display:none;height:372px;overflow:auto;border:1px solid #ccc;margin-bottom:20px;">
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">작성자 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="field_cmt_name" name="cmt_name" class="form-control" style="width:200px;" value="<?//php echo $this->config->item("admin_name"); ?>" />
                                <p class="alert alert-danger" style="position: absolute;left: 240px;top: 0; display: inline-block;vertical-align: top;padding:  5px 10px!important;">주의 : 댓글작업시 작성자 확인요망(미스할인으로 적으면 안됨!)</p>
                            </div>
                        </div>

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">프로필이미지</label>
                            <div class="col-sm-10">
                                <div id="select_profile_img">
                                    <img src="<?php echo $this->config->item('member_profile_img_default'); ?>" alt="" />
                                </div>
                                <ul class="profile_img_select">
                                    <?php foreach( $profile_array as $item ) { ?>
                                        <li><a href="#none" class="abs_link" data-url="<?php echo $item; ?>" onclick="select_profile_img(this);">선택</a><img src="<?php echo $item; ?>" alt="" /></li>
                                    <?php }//end of foreach() ?>
                                </ul>
                            </div>
                        </div>
                        -->

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_cmt_display_state">
                                    <?php echo get_input_radio("cmt_display_state", $this->config->item("comment_display_state"), "Y", $this->config->item("comment_display_state_text_color")); ?>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group form-group-sm">-->
                        <!--    <div id="field_cmt_content">-->
                        <!--        <textarea name="cmt_content" id="cmt_content" style="width:100%;height:200px;visibility:hidden;"></textarea>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">댓글내용 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="cmt_content" id="field_cmt_content" class="form-control" style="width:100%;height:100px;"></textarea>
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
    var form = '#pop_insert_form';

    //====================================== smarteditor2
    //var oEditors = [];
    //var aAdditionalFontSet = [["Noto Sans", "Noto Sans"]];      // 추가 글꼴 목록
    //var editorElement = 'cmt_content';
    //
    //loadingBar.show('#field_cmt_content');
    //
    //$(function(){
    //    nhn.husky.EZCreator.createInIFrame({
    //        oAppRef: oEditors,
    //        elPlaceHolder: editorElement,
    //        sSkinURI: "/plugins/smarteditor2/SmartEditor2Skin.html",
    //        htParams : {
    //            bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
    //            bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
    //            bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
    //            aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
    //            fOnBeforeUnload : function(){
    //                //alert("완료!");
    //            }
    //        }, //boolean
    //        fOnAppLoad : function(){
    //            //oEditors.getById[editorElement].exec("PASTE_HTML", ['<p style="font-family:Noto Sans;font-size:1em;">&nbsp;</p>']);
    //            //oEditors.getById[editorElement].setDefaultFont("Noto Sans", "1em");
    //            loadingBar.hide();
    //        },
    //        fCreator: "createSEditor2"
    //    });
    //});
    //====================================== /smarteditor2
</script>

<script>
    /**
     * 테이블 데이터 가져오기
     */
    function get_table_data(data) {
        if( empty(data) ) {
            return false;
        }

        loadingBar.show('#table_item_list');

        $.ajax({
            url : '/comment/table_data_list_ajax',
            data : data,
            type : 'post',
            dataType : 'html',
            success : function (result) {
                $('#table_item_list').html(result);
                $('#table_item_list').show();

                //선택된 데이터가 있으면 선택 처리
                if( !empty($('#pop_insert_form input[name="cmt_table_num"]').val()) ) {
                    $('#table_item_list table tr[data-num="' + $('#pop_insert_form input[name="cmt_table_num"]').val() + '"]').addClass('on');
                }
            },
            complete : function () {
                loadingBar.hide();
            }
        });
    }//end of get_table_data()

    /**
     * 테이블 데이터 선택
     */
    function table_data_select(tb, num, name) {
        $('#pop_insert_form input[name="cmt_table_num"]').val(num);
        $('#table_data_name').text(name);

        $('#table_item_list table tr').removeClass('on');
        $('#table_item_list table tr[data-num="' + num + '"]').addClass('on');
    }//end of table_data_select()

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
        <?php if( !empty($req['tb']) && !empty($req['tb_num']) ) { ?>
        $('#cmt_table_wrap').hide();
        <?php } ?>

        //구분 change
        $('[name="cmt_table"]').on("change", function () {
            $('#table_item_list').hide();
            $('#pop_insert_form input[name="cmt_table_num"]').val('');
            $('#pop_insert_form input[name="kwd"]').val('');
            $('#table_data_name').text('');

            if( empty($(this).val()) ) {
                return false;
            }

            get_table_data({tb:$(this).val()});

            //loadingBar.show('#table_item_list');
            //
            //$.ajax({
            //    url : '/comment/table_data_list_ajax',
            //    data : {tb:$(this).val()},
            //    type : 'post',
            //    dataType : 'html',
            //    success : function (result) {
            //        $('#table_item_list').html(result);
            //        $('#table_item_list').show();
            //    },
            //    complete : function () {
            //        loadingBar.hide();
            //    }
            //});
        });

        //검색
        $('[name="kwd"]').on('keyup', function () {
            var tb = $('[name="cmt_table"]').val();
            var kwd = $(this).val();

            if( empty(tb) ) {
                $(this).val('');
                alert('구분을 선택하세요.');
                return false;
            }

            get_table_data({tb:tb, kwd:kwd});

            //loadingBar.show('#table_item_list');
            //
            //$.ajax({
            //    url : '/comment/table_data_list_ajax',
            //    data : {tb:tb, kwd:kwd},
            //    type : 'post',
            //    dataType : 'html',
            //    success : function (result) {
            //        $('#table_item_list').html(result);
            //        $('#table_item_list').show();
            //    },
            //    complete : function () {
            //        loadingBar.hide();
            //    }
            //});
        });

        //submit check
        $(form).on('submit', function(){
            //if( empty(oEditors) ) {
            //    alert('HTML 에디터 로딩중입니다... \nHTML 에디터 로딩 완료후에 시도해 주세요.');
            //    return false;
            //}

            info_message_all_clear();

            //oEditors.getById[editorElement].exec("UPDATE_CONTENTS_FIELD", []);	// 에디터의 내용이 textarea에 적용됩니다.

            if( !$(form + ' [name="cmt_table"]').val() ) {
                alert('구분을 선택하세요.');
                return false;
            }
            if( !$(form + ' [name="cmt_table_num"]').val() ) {
                alert('댓글을 입력할 대상을 선택하세요.');
                return false;
            }

            if( $(form + ' [name="cmt_name"]').val() == '미스할인' ) {
                if(confirm('작업 댓글의 작성자는 "미스할인" 일수 없습니다.\n등록을 진행하시겠습니까? ') == false){
                    return false;
                }
            }


            if( !$(form + ' [name="cmt_content"]').val() ) {
                alert('댓글 내용을 입력하세요.');
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