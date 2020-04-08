<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">Home</h4>
    </div>

    <div class="row">
        <h5><a href="/app_push/list">오늘의 예약푸시</a></h5>
        <div id="push_list" style="position:relative;"></div>
    </div>

	<div class="row">
        <h5><a href="/total_stat">통계</a></h5>
        <div id="total_stat_list" style="position:relative;"></div>
    </div>
    <!-- <div class="row">
        <h5><a href="/product/list">상품관리</a></h5>
        <div id="product_count_list" style="position:relative;">
            <div class="table-responsive">
                <table class="table table-hover table-bordered dataTable">
                    <thead>
                    <tr role="row" class="active">
                        <th></th>
                        <th>등록상품</th>
                        <th>진열상품</th>
                        <th>미진열상품</th>
                        <th>판매상품</th>
                        <th>품절상품</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach( $product_count_array as $key => $item ) { ?>
                        <tr role="row">
                            <td><?php echo get_config_item_text($key, "product_category"); ?></td>
                            <td><?php echo number_format($item[0]); ?></td>
                            <td><?php echo number_format($item[1]['Y']); ?></td>
                            <td><?php echo number_format($item[1]['N']); ?></td>
                            <td><?php echo number_format($item[2]['Y']); ?></td>
                            <td><?php echo number_format($item[2]['N']); ?></td>
                        </tr>
                    <?php } ?>

                    <tr role="row" style="background:#f5f5f5;">
                        <td>합계</td>
                        <td><?php echo number_format($product_totalcount_array[0]); ?></td>
                        <td><?php echo number_format($product_totalcount_array[1]['Y']); ?></td>
                        <td><?php echo number_format($product_totalcount_array[1]['N']); ?></td>
                        <td><?php echo number_format($product_totalcount_array[2]['Y']); ?></td>
                        <td><?php echo number_format($product_totalcount_array[2]['N']); ?></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div> -->

    <div class="row">
        <h5> <a href="/board_qna/list">1:1문의</a>&nbsp;</h5>
        <div id="qna_list" style="position:relative;"></div>
    </div>

	 <div class="row">
        <h5><a href="/comment/list">Q&A</a></h5>
        <div id="comment_list" style="position:relative;"></div>
    </div>

    
</div>

<br />
<br />
<br />
<br />
<br />

<script>
    function form_submit() {
        return false;
    }

    //document.ready
    $(function(){

        //푸시
        $.ajax({
            url : '/app_push/list_ajax',
            data : {rctly:"Y", ans_yn:"N", usestate:"Y" },
            type : 'post',
            dataType : 'html',
            success : function(result){
                $('#push_list').html(result);
            }
        });

        //통계
        $.ajax({
            url : '/total_stat/list_ajax',
            data : {sort_yn:'N'},
            type : 'post',
            dataType : 'html',
            success : function(result){
                $('#total_stat_list').html(result);
            }
        });


        //1:1문의
        $.ajax({
            url : '/board_qna/list_ajax',
            data : {rctly:"Y", ans_yn:"N", usestate:"Y" },
            type : 'post',
            dataType : 'html',
            success : function(result){
                $('#qna_list').html(result);
            }
        });

		//댓글
        $.ajax({
            url : '/comment/list_ajax',
            data : {rctly:"Y", list_per_page:20, main : 'Y'},
            type : 'post',
            dataType : 'html',
            success : function(result){
                $('#comment_list').html(result);
            }
        });

    });//end of document.ready

    function get_main_cmt(){

        $.ajax({
            url : '/comment/list_ajax',
            data : {rctly:"Y", list_per_page:20, main : 'Y'},
            type : 'post',
            dataType : 'html',
            success : function(result){
                $('#comment_list').html(result);
            }
        });

    }

	/**
     * 신규추가 시작
     * 댓글 수정 팝업
     */
    function comment_update_pop(cmt_num) {
        if( empty(cmt_num) ) {
            alert('댓글을 선택하세요.');
            return false;
        }

        var container = $('<div>');
        $(container).load('/comment/update_pop/?cmt_num=' + cmt_num);

        modalPop.createPop('댓글 수정', container);
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
            url : '/comment/delete_proc',
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
                    get_main_cmt();
                    modalPop.hide();
                }
            },
            complete : function() {
                Pace.stop();
            }
        });
    }//end of comment_delete()

    function comment_reply_pop(cmt_num) {
        if( empty(cmt_num) ) {
            return false;
        }

        var container = $('<div>');
        $(container).load('/comment/reply_pop/?cmt_num=' + cmt_num);

        modalPop.createPop('답댓글 등록', container);
        modalPop.createButton('등록완료', 'btn btn-primary btn-sm', function(){
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
                    }
                }

                if( result.status == status_code['success'] ) {
                    get_main_cmt();
                }
            },
            complete : function() {
                //Pace.stop();
            }
        });
    }//end of team_select

    //댓글끝
    ////////1:1문의시작

    /**
     * 1:1문의 답글 팝업
     */
    function board_qna_answer_pop(bq_num) {
        var container = $('<div>');
        $(container).load('/board_qna/answer_pop/?bq_num=' + bq_num);

        modalPop.createPop('1:1문의 답글', container);
        modalPop.createButton('삭제', 'btn btn-danger btn-sm pull-left', function(){
            board_qna_delete(bq_num);
        });
        modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    }//end of board_qna_update_pop()

    /**
     * 1:1문의 답글 팝업
     */
    function board_qna_answer_pop(bq_num) {
        var container = $('<div>');
        $(container).load('/board_qna/answer_pop/?bq_num=' + bq_num);

        modalPop.createPop('1:1문의 답글', container);
        modalPop.createButton('삭제', 'btn btn-danger btn-sm pull-left', function(){
            board_qna_delete(bq_num);
        });
        modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();
        });
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
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
            url : '/board_qna/delete_proc',
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

	function member_order_list_pop (url) {
        new_win_open(url, '', 800, 600);
    }//end of member_order_list_pop()
</script>