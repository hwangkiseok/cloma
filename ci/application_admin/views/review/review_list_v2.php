<script type="text/javascript" src="/js/clipboard.min.js"></script>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<?php if( !empty($req['pop']) ) { ?>
    <style>
        body { background:#fff; margin:0 10px 10px 10px; }
    </style>
<?php } ?>

<?php if($req['dev'] == true) {
  //
}
?>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">리뷰 > 목록</h4>
    </div>

    <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />
        <input type="hidden" name="m_num" value="<?php echo $req['m_num']; ?>" />
        <input type="hidden" name="tb_num" value="<?php echo $req['tb_num']; ?>" />
        <input type="hidden" name="ym" value="<?php echo $req['ym']; ?>" />
        <input type="hidden" name="pop" value="<?php echo $req['pop']; ?>" />

        <div class="row" id="search_wrap">
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="blind">참고</label><?/*블라인드*/?>
                <div class="col-sm-2">
                    <label><input type="radio" id="blind_" name="blind" value="" <?php echo (empty($req['blind']) || $req['blind'] != "Y" || $req['blind'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="blind_Y" name="blind" value="Y" <?php echo ($req['blind'] == "Y") ? "checked" : ""; ?>> 참고</label><?/*블라인드*/?>
                    <label><input type="radio" id="blind_N" name="blind" value="N" <?php echo ($req['blind'] == "N") ? "checked" : ""; ?>> 정상</label>
                </div>

                <label class="col-sm-2 control-label" for="_grade">추천상태</label><?/*블라인드*/?>
                <div class="col-sm-2">
                    <label><input type="radio" id="grade" name="grade" value="" <?php echo (empty($req['grade']) || $req['grade'] != "A" || $req['grade'] != "B" || $req['grade'] != "C") ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="grade_A" name="grade" value="A" <?php echo ($req['blind'] == "A") ? "checked" : ""; ?>> 완전추천</label>
                    <label><input type="radio" id="grade_B" name="grade" value="B" <?php echo ($req['blind'] == "B") ? "checked" : ""; ?>> 추천</label>
                    <label><input type="radio" id="grade_C" name="grade" value="C" <?php echo ($req['blind'] == "C") ? "checked" : ""; ?>> 불만</label>
                </div>

            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="admin">관리글/일반글</label>
                <div class="col-sm-2">
                    <label><input type="radio" id="admin_" name="admin" value="" <?php echo (empty($req['admin']) || $req['admin'] != "Y" || $req['admin'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="admin_Y" name="admin" value="Y" <?php echo ($req['admin'] == "Y") ? "checked" : ""; ?>> 관리글</label>
                    <label><input type="radio" id="admin_N" name="admin" value="N" <?php echo ($req['admin'] == "N") ? "checked" : ""; ?>> 일반글</label>
                </div>

                <label class="col-sm-2 control-label" for="_grade">메인노출</label><?/*블라인드*/?>
                <div class="col-sm-2">
                    <label><input type="radio" id="main_view" name="main_view" value="" <?php echo (empty($req['main_view']) || $req['grade'] != "Y" || $req['grade'] != "N" ) ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="main_view_Y" name="main_view" value="Y" <?php echo ($req['main_view'] == "Y") ? "checked" : ""; ?>> 메인</label>
                    <label><input type="radio" id="main_view_N" name="main_view" value="N" <?php echo ($req['main_view'] == "N") ? "checked" : ""; ?>> 일반</label>
                </div>


            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="state">노출여부</label>
                <div class="col-sm-2">
                    <label><input type="radio" id="state_" name="state" value="" <?php echo (empty($req['state']) || $req['state'] != "Y" || $req['state'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <?php echo get_input_radio("state", $this->config->item("comment_display_state"), ""); ?>
                </div>

                <label class="col-sm-2 control-label" for="_grade">적립금 타입</label>
                <div class="col-sm-2">
                    <label><input type="radio" id="state_" name="reward_type" value="" checked=""> 전체</label>
                    <input type="radio" id="reward_typeA" name="reward_type" value="A" <?php echo ($req['reward_type'] == "A") ? "checked" : ""; ?>><label for="reward_typeA">텍스트</label>&nbsp;&nbsp;
                    <input type="radio" id="reward_typeB" name="reward_type" value="B" <?php echo ($req['reward_type'] == "B") ? "checked" : ""; ?>><label for="reward_typeB">포토</label>&nbsp;
                    <input type="radio" id="reward_typeC" name="reward_type" value="C" <?php echo ($req['reward_type'] == "C") ? "checked" : ""; ?>><label for="reward_typeC">지급안함</label>&nbsp;
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="img_yn">이미지 여부</label>
                <div class="col-sm-2">
                    <label><input type="radio" id="state_" name="img_yn" value="" checked=""> 전체</label>
                    <input type="radio" id="img_Y" name="img_yn" value="Y"><label for="img_Y">이미지 있음</label>&nbsp;&nbsp;
                    <input type="radio" id="img_N" name="img_yn" value="N"><label for="img_N">이미지 없음</label>&nbsp;&nbsp;
                </div>

                <label class="col-sm-2 control-label" for="_grade">적립금지급 여부</label>
                <div class="col-sm-2">
                    <label><input type="radio" id="state_" name="reward_yn" value="" checked=""> 전체</label>
                    <input type="radio" id="reward_Y" name="reward_yn" value="Y"><label for="reward_Y">지급</label>&nbsp;&nbsp;
                    <input type="radio" id="reward_N" name="reward_yn" value="N"><label for="reward_N">미지급</label>&nbsp;
                </div>
            </div>

            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="winner_mode">당첨자 모드</label>
                <div class="col-sm-2">
                    <label><input type="checkbox" name="winner_mode" value="Y" <?php echo ($req['winner_mode'] == "Y") ? "checked" : ""; ?>> 당첨자모드</label>
                </div>

                <label class="col-sm-2 control-label">메모유무</label>
                <div class="col-sm-2">
                    <label><input type="radio" name="memo_yn" value="" checked=""> 전체</label>
                    <input type="radio" id="memo_Y" name="memo_yn" value="Y"><label for="memo_Y">있음</label>&nbsp;&nbsp;
                    <input type="radio" id="memo_N" name="memo_yn" value="N"><label for="memo_N">없음</label>&nbsp;
                </div>
            </div>

            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label">CS처리</label>
                <div class="col-sm-2">
                    <label><input type="radio" name="cs_help_yn" value="" checked=""> 전체</label>
                    <input type="radio" id="cs_help_Y" name="cs_help_yn" value="Y"><label for="cs_help_Y">필요</label>&nbsp;&nbsp;
                    <input type="radio" id="cs_help_N" name="cs_help_yn" value="N"><label for="cs_help_N">불필요</label>&nbsp;
                </div>
            </div>

            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="div">날짜검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select id="dateType" name="dateType" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                            <option value="re_regdatetime">등록일</option>
                            <option value="re_winner_date">이벤트당첨일</option>
                        </select>
                    </div>
                    <div class="pull-left mgl10">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left">
                        <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="set_date_term();">오늘</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="clear_date_term();">전체</button>
                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="all">전체</option>
                                <option value="re_content">댓글내용</option>
                                <option value="re_name">작성자</option>
                                <option value="p_name">상품명</option>
                                <option value="re_member_num">회원번호</option>
                            </select>
                            <script>selected_check($('#kfd'), '<?php echo $req['kdf']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>

                <!--                <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="review_insert_pop();">등록</button>-->
            </div>
        </div>

    </form>

    <div class="row">
        <div class="row mgb10">
            <!--
            <div class="pull-left form-inline form-group-sm" style="margin:0 15px;">
                <select name="ym_select" id="ym_select" class="form-control btn-success">
                    <?php echo get_select_option_ym("* 선택 *", $req['ym'], date("Ym"), 201708, "desc"); ?>
                </select>
            </div>


            <div class="pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="comment_best_order_update();">베스트순위변경</button>
            </div>
-->
            <?php if( !empty($req['pop']) ) { ?>
                <div class="pull-left">
                    <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="review_insert_pop('review', '<?php echo $req['tb_num']; ?>');">등록</button>
                </div>
            <?php } ?>
            <div class="pull-left" style="padding-left: 20px;">
                <input type="radio" name="sel_reward_type" checked value="A">텍스트&nbsp;&nbsp;
                <input type="radio" name="sel_reward_type" value="B">이미지&nbsp;&nbsp;
                <input type="radio" name="sel_reward_type" value="C">지급안함
                <button type="button" class="btn btn-danger btn-xs" style="width:110px;" onclick="batch_apply_point();">적립금 선택처리</button>
            </div>

            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="review_list" style="position:relative;"></div>
    </div>
</div>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>
    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    /**
     * 댓글 등록 팝업
     */
    function review_insert_pop(tb, tb_num) {
        var param = '';
        if( !empty(tb) ) {
            param += '&tb=' + tb;
        }
        if( !empty(tb_num) ) {
            param += '&tb_num=' + tb_num;
        }
        if( !empty(param) ) {
            param = '?' + param;
        }

        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->insert_pop; ?>/' + param);

        modalPop.createPop('리뷰 등록', container);
        modalPop.createButton('등록완료', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of comment_insert_pop()

    /**
     * 댓글 수정 팝업
     */
    function review_update_pop (re_num, re_admin) {
        if( empty(re_num) ) {
            alert('리뷰을 선택하세요.');
            return false;
        }

        var container = $('<div>');
        $(container).load('<?php echo $this->page_link->update_pop; ?>/?re_num=' + re_num + '&re_admin=' + re_admin);

        modalPop.createPop('리뷰 수정', container);
        modalPop.createButton('삭제하기', 'btn btn-danger btn-sm pull-left', function(){
            review_delete(re_num);
        });
        modalPop.createButton('수정완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }//end of comment_update_pop()

    /**
     * 댓글 삭제
     */
    function review_delete(re_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        // 적립금 삭제
        $.ajax({
            url: '/point/deleteReviewPointMember/',
            data: { re_num : re_num },
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {
                if(result.success == true) { // 적립금 삭제가 되면

                    $.ajax({
                        url : '<?php echo $this->page_link->delete_proc; ?>',
                        data : {re_num:re_num},
                        type : 'post',
                        dataType : 'json',
                        success : function(result) {
                            if( result.msg ) alert(result.msg);

                            if( result.success == true ) {
                                $('#search_form').submit();
                                //delete_rel_point(re_num);
                                modalPop.hide();
                            }
                        },
                        complete : function() {
                            Pace.stop();
                        }
                    });

                } else {
                    alert(result.msg);
                }
            }
        });
    }//end of comment_delete()



    function setReviewFlag(fd , setFlag , re_num, obj){

        var setting_flag     = $(obj).parent().parent().data('flag_set');
        var setting_flag_arr = setting_flag.split('||');

        if(fd == 're_blind' && setFlag == 'Y' && setting_flag_arr[2] == 'Y'){
            alert('메인노출된 리뷰는 참고로 변경할수 없습니다.');
            return false;
        }


        if(fd == 're_recommend' && setFlag == 'Y' && setting_flag_arr[0] == 'Y'){
            alert('참고인 리뷰는 메인노출로 변경할수 없습니다.');
            return false;
        }


        if( !confirm('해당상품 상태를 변경하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '/review/setReviewFlag/',
            data : {fd:fd , setFlag : setFlag , re_num : re_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.msg ) alert(result.msg);
                if( result.success == true ) $('#search_form').submit();
            },
            complete : function() {
                Pace.stop();
            }
        });

    }

    //document.ready
    $(function () {

        //clipboard Copy
        var clipboard = new Clipboard('#dummyClipboard');
        clipboard.on('success', function(e) {
            //console.log(e);
            //console.log('!clipboard CALLBACK');
            alert('단축URL Copied');
            e.clearSelection();
        });
        clipboard.on('error', function(e) {
            alert('------- 클립보드 복사 실패 -------');
            $('.url_content_wrap>li>a.btn').removeClass('btn-danger');
            $('.url_content_wrap>li>a.btn').addClass('btn-default');
            e.clearSelection();
        });
        //clipboard Copy

        $('input[name="winner_mode"]').on('click',function(){
            $('#search_form').submit();
        });

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        <?php if( !empty($req['pop']) ) { ?>
        $('#search_wrap').hide();
        $('body').css({'background':'#fff'});
        <?php } ?>

        $('#tb').on('change', function(){
            form_submit('page=1');
        });

        $('#ym_select').on('change', function () {
            form_submit('ym=' + $(this).val());
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form

        $('#search_form').ajaxForm({
            //url : '<?php //echo $this->page_link->list_ajax; ?>//',
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#review_list'));
            },
            success: function(result) {
                $('#review_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#review_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });

    });//end of document.ready

    function copy_clipboard_n(str){

        if(str == null || str == '' || str == undefined){
            return false;
        }
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text','');
        /*Init*/
        $('#dummyClipboard').attr('data-clipboard-text',str);
        $('#dummyClipboard').off().click();

    }

</script>
<button id="dummyClipboard" data-clipboard-text="" style="display: none;"></button>
