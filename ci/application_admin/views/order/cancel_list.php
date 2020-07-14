<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">주문취소관리 > 목록</h4>
    </div>

    <form name="search_form" id="search_form" method="post" action="/order/cancel_list_ajax" class="form-horizontal">

        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />

        <div class="row" id="search_wrap">
            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="div">날짜검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select id="dateType" name="dateType" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                            <option value="A.reg_date">신청일</option>
                            <option value="A.proc_date">처리일</option>
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
                                <option value="C.m_nickname">닉네임</option>
                                <option value="A.account_holder">예금주</option>
                                <option value="B.trade_no">주문번호</option>
                                <option value="B.receiver_tel">수령자 연락처</option>
                                <option value="B.receiver_name">수령자명</option>
                                <option value="B.buyer_hhp">주문자 연락처</option>
                                <option value="B.buyer_name">주문자명</option>
                                <option value="B.m_trade_no">장바구니번호</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색필터</label>
                <div class="col-sm-2">
                    <select name="form_status_cd" class="form-control" >
                        <option value="">- 주문상태 전체보기 - </option>
                        <?=get_select_option("", $this->config->item('form_status_cd') );?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="after_form_status_cd" class="form-control" >
                        <option value="">- 취소상태 전체보기 -</option>
                        <?=get_select_option("", $this->config->item('after_form_status_cd') );?>
                    </select>
                </div>

            </div>


            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
            </div>

        </div>

    </form>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="order_list" style="position:relative;"></div>
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
     * 댓글 삭제
     */
    function order_delete(seq) {
        if( !confirm('삭제하시겠습니까?') ) {
            return false;
        }

        Pace.restart();

        $.ajax({
            url : '<?php echo $this->page_link->delete_proc; ?>',
            data : {seq:seq},
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
    }//end of order_delete()

    // 참고/정상 토글
    function proc_flag(seq, proc_flag) {
        //Pace.restart();

        var conf_txt = '';
        if(proc_flag == 'Y') {
            conf_txt = "처리완료";
        } else {
            conf_txt = "처리 전";
        }

        if( !confirm('처리상태를 ' + conf_txt + '(으)로 변경하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/order/proc_flag',
            data : { 'seq' : seq, 'proc_flag' : proc_flag },
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




    $(document).on('click','.popExchangInfo',function(e){
        e.preventDefault();

        var seq = $(this).data('seq');
        var container = $('<div>');
        $(container).load('/order/exchange_pop?seq=' + seq);

        modalPop.createPop('주문 교환 상세', container);
        // modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
        //     $('#pop_update_form').submit();
        // });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });

    $(document).on('click','.popDetail',function(e){
        e.preventDefault();

        var seq = $(this).data('seq');
        var container = $('<div>');
        $(container).load('/Order/cancel_pop?seq=' + seq);

        modalPop.createPop('주문취소 상세', container);
        modalPop.createButton('수정', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();

    });

    //document.ready
    $(function () {

        $('select[name="form_status_cd"],select[name="after_form_status_cd"]').on('change',function(){
            $('form[name="search_form"]').submit();
        });

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

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
                loadingBar.show($('#order_list'));
            },
            success: function(result) {
                $('#order_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#order_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//end of document.ready
</script>