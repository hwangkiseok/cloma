<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<?php if( !empty($req['pop']) ) { ?>
    <style>
        body { background:#fff; margin:0 10px 10px 10px; }
    </style>
<?php } ?>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">댓글 > 목록</h4>
    </div>

    <form name="search_form" id="search_form" method="post" action="/comment/list_ajax" class="form-horizontal">

        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />
        <input type="hidden" name="m_num" value="<?php echo $req['m_num']; ?>" />
        <input type="hidden" name="tb" value="<?php echo $req['tb']; ?>" />
        <input type="hidden" name="tb_num" value="<?php echo $req['tb_num']; ?>" />
        <input type="hidden" name="ym" value="<?php echo $req['ym']; ?>" />
        <input type="hidden" name="cmt_flag" value="<?php echo $req['cmt_flag']; ?>" />
        <input type="hidden" name="view_type" value="<?php echo $req['view_type']; ?>" />
        <input type="hidden" name="pop" value="<?php echo $req['pop']; ?>" />

        <div class="row" id="search_wrap">
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">구분</label>
                <div class="col-sm-10">
                    <select id="tb" name="tb" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("comment_table"), $req['tb']?$req['tb']:'product' ); ?>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cmt_gubun">문의유형</label>
                <div class="col-sm-10">
                    <select id="cmt_gubun" name="cmt_gubun" class="form-control" style="width:auto;">
                        <!--                        <option value="">전체보기</option>-->
                        <?php echo get_select_option("* 전체 *", $this->config->item("cmt_gubun"), $req['tb']?$req['tb']:'' ); ?>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="reply_cnt">답댓글</label>
                <div class="col-sm-10">
                    <label><input type="radio" id="reply_cnt_" name="reply_cnt" value="" <?php echo (empty($req['reply_cnt']) || $req['reply_cnt'] != "Y" || $req['reply_cnt'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="reply_cnt_Y" name="reply_cnt" value="Y" <?php echo ($req['reply_cnt'] == "Y") ? "checked" : ""; ?>> 있음</label>
                    <label><input type="radio" id="reply_cnt_N" name="reply_cnt" value="N" <?php echo ($req['reply_cnt'] == "N") ? "checked" : ""; ?>> 없음</label>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="ans_yn">참고</label><?/*블라인드*/?>
                <div class="col-sm-10">
                    <label><input type="radio" id="blind_" name="blind" value="" <?php echo (empty($req['blind']) || $req['blind'] != "Y" || $req['blind'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <label><input type="radio" id="blind_Y" name="blind" value="Y" <?php echo ($req['blind'] == "Y") ? "checked" : ""; ?>> 참고</label><?/*블라인드*/?>
                    <label><input type="radio" id="blind_N" name="blind" value="N" <?php echo ($req['blind'] == "N") ? "checked" : ""; ?>> 정상</label>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="ans_yn">관리글/일반글</label>
                <div class="col-sm-10">
                    <label><input type="radio" id="admin_" name="admin" value="" <?php echo (empty($req['admin']) || $req['admin'] != "Y" || $req['admin'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <!--<label><input type="radio" id="admin_Y" name="admin" value="Y" <?php echo ($req['admin'] == "Y") ? "checked" : ""; ?>> 관리글</label>-->
                    <label><input type="radio" id="admin_N" name="admin" value="N" <?php echo ($req['admin'] == "N") ? "checked" : ""; ?>> 일반글</label>
                </div>
            </div>
            <!--
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="ans_yn">노출여부</label>
                <div class="col-sm-10">
                    <label><input type="radio" id="state_" name="state" value="" <?php echo (empty($req['state']) || $req['state'] != "Y" || $req['state'] != "N") ? "checked" : ""; ?>> 전체</label>
                    <?php echo get_input_radio("state", $this->config->item("comment_display_state"), ""); ?>
                </div>
            </div>
            -->
            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="div">날짜검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select id="dateType" name="dateType" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                            <option value="cmt_regdatetime">등록일</option>
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
                                <option value="cmt_content">댓글내용</option>
                                <option value="cmt_name">작성자</option>
                                <option value="cmt_reply_name">답변자</option>
                                <option value="p_name">상품명</option>
                                <option value="cmt_member_num" <?=$req['kfd'] == 'cmt_member_num'?'selected':''?>>회원번호</option>
                            </select>
                            <script>//selected_check($('#kfd'), '<?php echo $req['kdf']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="comment_insert_pop();">등록</button>
            </div>
        </div>

    </form>

    <div class="row">
        <div class="row mgb10">

            <!-- 지은님 요청 삭제 190321 -->
            <!--div class="pull-left form-inline form-group-sm" style="margin:0 15px;">
                <select name="ym_select" id="ym_select" class="form-control btn-success">
                    <?php echo get_select_option_ym("* 선택 *", $req['ym'], date("Ym"), 201708, "desc"); ?>
                </select>
            </div>

            <div class="pull-left">
                <button type="button" class="btn btn-primary btn-sm" onclick="comment_best_order_update();">베스트순위변경</button>
            </div-->
            <!-- // 지은님 요청 삭제 190321 -->

            <?php if( !empty($req['pop']) ) { ?>
                <div class="pull-left">
                    <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="comment_insert_pop('<?php echo $req['tb']; ?>', '<?php echo $req['tb_num']; ?>');">등록</button>
                </div>
            <?php } ?>

            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="comment_list" style="position:relative;"></div>
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
        $("input[name='cmt_flag']").val('');
        $('#search_form').submit();
    }//end of form_submit()

    /**
     * 댓글 등록 팝업
     */
    function comment_insert_pop(tb, tb_num) {
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
        $(container).load('/comment/insert_pop/' + param);

        modalPop.createPop('댓글 등록', container);
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
    function comment_update_pop(cmt_num) {
        if( empty(cmt_num) ) {
            alert('댓글을 선택하세요.');
            return false;
        }

        var container = $('<div>');
        $(container).load('/comment/update_pop/?cmt_num=' + cmt_num);

        modalPop.createPop('댓글 상세수정', container);
        modalPop.createButton('삭제하기', 'btn btn-danger btn-sm pull-left', function(){
            comment_delete(cmt_num);
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
    function comment_delete(cmt_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {cmt_num:cmt_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    modalPop.hide();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of comment_delete()

    /**
     * 댓글 베스트 순서 변경
     */
    function comment_best_order_update () {
        var cmt_num = {};
        $.each($('[name*="best_order[]"]'), function(){
            cmt_num[$(this).attr('data-num')] = $(this).val();
        });

        Pace.restart();

        $.ajax({
            url : '/comment/best_order_proc',
            data : {data:cmt_num},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' ) {
                    alert(result.message);
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of comment_best_order_update()


    /**
     * 답댓글 팝업
     * @param cmt_num
     */
    function comment_reply_pop(cmt_num) {
        if( empty(cmt_num) ) {
            return false;
        }

        var container = $('<div>');
        $(container).load('/comment/reply_pop/?cmt_num=' + cmt_num);

        modalPop.createPop('답댓글 등록', container);

        modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
            $('#pop_insert_form').submit();
        });

        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of comment_reply_pop()

    // 참고/정상 토글
    function blind_select(cmt_num, cmt_blind) {
        //Pace.restart();

        var conf_txt = '';
        if(cmt_blind == 'Y') {
            conf_txt = "참고";
        } else {
            conf_txt = "정상";
        }

        if( !confirm('참고여부를 ' + conf_txt + '(으)로 변경하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/comment/blind_proc',
            data : { 'cmt_num' : cmt_num, 'cmt_blind' : cmt_blind },
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                        console.log(result);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    //modalPop.hide();
                }
            },
            complete : function() {
                //Pace.stop();
            }
        });
    }//end of team_select

    //document.ready
    $(function () {
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
                loadingBar.show($('#comment_list'));
            },
            success: function(result) {
                $('#comment_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $(".btn-sm").on('click', function() {
            $("input[name='cmt_flag']").val('');
        });

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#comment_list .pagination.ajax a', function(e){

            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//end of document.ready
</script>