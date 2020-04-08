<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">1:1문의 > 목록</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />
            <input type="hidden" name="init_team" value="" />
            <input type="hidden" name="none_answer" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">유형선택</label>
                <div class="col-sm-10">
                    <select id="cate" name="cate" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("board_qna_category"), $req['cate']); ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="ans_yn">답변여부</label>
                <div class="col-sm-10">
                    <select id="ans_yn" name="ans_yn" class="form-control" style="width:auto;">
                        <?php echo get_select_option("* 전체 *", $this->config->item("board_qna_answer_yn"), $req['ans_yn']); ?>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="all">전체</option>
                                <option value="bq_product_name">제목</option>
                                <option value="bq_content">문의내용</option>
                                <option value="m_loginid">작성자아이디</option>
                                <option value="bq_member_num" <?=$req['kfd'] == 'bq_member_num'?'selected':''?>>회원번호</option>
                                <option value="bq_last_writer" <?=$req['kfd'] == 'bq_last_writer'?'selected':''?>>답변자</option>
                            </select>
                            <script>//selected_check($('#kfd'), '<?php echo $req['kdf']; ?>')</script>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">일자검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" id="date_type" class="form-control" style="width:auto;">
                            <option value="bq_regdatetime">등록일</option>
                        </select>
                        <script>selected_check('#date_type', '<?php echo $req['date_type']; ?>');</script>
                    </div>
                    <div class="pull-left mgl10">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center middle" style="width:20px;">~</div>
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


            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="board_list" style="position:relative;"></div>
    </div>
</div>

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
     * 1:1문의 답글 팝업
     */
    function board_qna_answer_pop(bq_num) {
        var container = $('<div>');

        $(container).load('<?php echo $this->page_link->answer_pop; ?>/?bq_num=' + bq_num);
        modalPop.createPop('1:1문의 답글', container);

        modalPop.createButton('삭제', 'btn btn-danger btn-sm pull-left', function(){
            board_qna_delete(bq_num);
        });

        modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');

        modalPop.show({'dialog_class' : 'modal-lg'});

    }//end of board_qna_update_pop()

    /**
     * 1:1문의 삭제
     */
    function board_qna_delete(bq_num) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {bq_num:bq_num},
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
    }//end of board_qna_delete()

    /**
     * 회원 주문내역 팝업
     */
    function member_order_list_pop (url) {
        new_win_open(url, '', 800, 600);
    }//end of member_order_list_pop()

    //document.ready
    $(function () {

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        $('#div').on('change', function(){
            $('#search_form').submit();
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
                loadingBar.show($('#board_list'));
                $("input[name='init_team']").val(''); // 팀 버튼 클릭시 팀 이외의 조건 초기화
                $("input[name='none_answer']").val(''); // 미답변/처리중 버튼 클릭시 해당 조건 이외 초기화
            },
            success: function(result) {
                $('#board_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#board_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready


    // 팀변경
    var team_select = function (bq_num, bq_team) {
        //Pace.restart();

        if( !confirm('처리부서를 ' + bq_team + '팀으로 변경하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/board_qna/team_proc',
            data : {bq_num:bq_num, bq_team:bq_team},
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
</script>