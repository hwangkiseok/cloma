<style>
    .tab_title { color:#fff;background:#000;display:block;font-size:13px;font-weight:bold;padding:5px;overflow:hidden; }
</style>


<div class="container-fluid <?php if( !empty($req['pop']) ) { ?>pop<?php } ?>">
    <div class="row">
        <h4 class="page-header">회원관리 > 회원수정</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->update_proc; ?>">
                        <input type="hidden" name="m_num" value="<?php echo $member_row['m_num']; ?>" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">회원번호</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo $member_row['m_num']; ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">SNS 사이트</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo get_config_item_text($member_row['m_sns_site'], 'member_sns_site'); ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">닉네임</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo $member_row['m_nickname']; ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이메일</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm">
                                    <?php echo $member_row['m_email']; ?>

                                    <?if($member_row['m_email_yn'] == 'Y'){?>
                                        <a class="btn btn-xs btn-success">이메일 광고 수신 허용</a>
                                    <?}else{?>
                                        <a class="btn btn-xs btn-danger">이메일 광고 수신 거부</a>
                                    <?}?>
                                </span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">가입경로</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo get_config_item_text($member_row['m_join_path'], 'member_join_path'); ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">가입일시</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo get_datetime_format($member_row['m_regdatetime']); ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">가입IP</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo $member_row['m_join_ip']; ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">최종로그인일시</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo get_datetime_format($member_row['m_logindatetime']); ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">최종로그인IP</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm"><?php echo $member_row['m_login_ip']; ?></span>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">총주문수</label>
                            <div class="col-sm-10">
                                <span class="help-inline-sm">
                                    <?php echo number_format($member_row['m_order_count']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">본인인증</label>
                            <div class="col-sm-10">
                                <p class="help-inline-sm">
                                    <?php if ( !empty($member_row['m_authno']) ) { ?>
                                    <b style="color:#0000ff;">인증됨 (<?php echo $member_row['m_authno']; ?>)</b>
                                    <a href="#none" class="btn btn-xs btn-danger" onclick="member_auth_delete();">인증초기화</a>
                                    <?php } else { ?>
                                    인증 안됨
                                    <?php }//endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">주문정보</label>
                            <div class="col-sm-10">
                                <p class="help-inline-sm" style="word-break:break-all;">
                                    <b style="color:#0000ff;">
                                        <?php if($member_row['m_order_phone']){
                                            echo '주문자명 : '.$member_row['m_order_name'].' / 주문자연락처 : '. ph_slice($member_row['m_order_phone']);
                                        }else{
                                            echo '등록안됨';
                                        }?></b>
                                </p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">성별/생년</label>
                            <div class="col-sm-10">
                                <p class="help-inline-sm" style="word-break:break-all;">

                                    <?if($member_row['m_gender']){?>
                                        <?if($member_row['m_gender']=='F'){?>
                                            여성
                                        <?}else{?>
                                            남성
                                        <?}?>
                                        / 연령대 : <?=$member_row['m_age_range']?>대
                                    <?}?>
                                </p>
                            </div>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">FCM ID</label>
                            <div class="col-sm-10">
                                <p class="help-inline-sm" style="word-break:break-all;">
                                    <?php echo $member_row['m_regid']; ?>
                                    <?if(empty($member_row['m_regid']) == false){?>
                                        <br><button class="member_send_push btn btn-primary btn-sm" data-fcm_id="<?=$member_row['m_regid']?>" data-m_num="<?=$member_row['m_num']?>" >푸시발송</button>
                                    <?}?>

                                </p>

                            </div>
                        </div>


                        <script>
                            $(function(){

                                $('.member_send_push').on('click',function(e){
                                    e.preventDefault();

                                    var id = $(this).data('fcm_id');
                                    var m_num = $(this).data('m_num');

                                    if(empty(id) == true){
                                        alert('FCM_ID가 없는 경우 푸시발송이 불가능합니다.');
                                        return false;
                                    }

                                    var container = $('<div>');
                                    $(container).load('/common/send_push_pop?m_num='+m_num);

                                    modalPop.createPop('푸시발송', container);
                                    modalPop.createButton('발송', 'btn btn-primary btn-sm', function(){
                                        $('#push_insert_form').submit();
                                    });
                                    modalPop.createCloseButton('취소', 'btn btn-default btn-sm');

                                    modalPop.show({backdrop:'static'});

                                });

                            });

                        </script>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">휴대폰정보</label>
                            <div class="col-sm-10">
                                <p>앱버전 : <?php echo $member_row['m_app_version']; ?> (<?php echo $member_row['m_app_version_code']; ?>)</p>
                                <p>휴대폰모델 : <?php echo $member_row['m_device_model']; ?></p>
                                <p>OS버전 : <?php echo $member_row['m_os_version']; ?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">관리자 회원</label>
                            <div class="col-sm-10">
                                <label><input type="radio" name="m_admin_yn" value="Y" <?=($member_row['m_admin_yn'] == "Y") ? "checked" : "";?> /> <span style="color:red">관리자</span></label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="radio" name="m_admin_yn" value="N" <?=($member_row['m_admin_yn'] == "N") ? "checked" : "";?> /> 일반</label>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">제재/탈퇴일시</label>
                            <div class="col-sm-10">
                                <?php if( $member_row['m_state'] == '2' ) { ?>
                                <span class="help-inline-sm"><?php echo get_datetime_format($member_row['m_procdatetime']); ?></span>
                                <?php } else if( $member_row['m_state'] == '3' ) { ?>
                                <span class="help-inline-sm"><?php echo get_datetime_format($member_row['m_deldatetime']); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">관리자 메모</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" style="width:90%;height:80px;" readonly="readonly"><?=nl2br($member_row['m_memo']);?></textarea>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">회원상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div style="padding-top:4px;">
                                    <?php echo get_input_radio('m_state', $this->config->item('member_state'), $member_row['m_state'], $this->config->item('member_state_text_color')); ?>
                                </div>
                            </div>
                        </div>

                        <hr />
                        <div class="clearfix"></div>

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                                <?php if( empty($req['pop']) ) { ?>
                                <a href="<?echo $list_url; ?>" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                <?php } ?>

                                <button type="submit" class="btn btn-primary btn-sm">수정완료</button>
                            </div>
                        </div>
                    </form>

                    <hr />
                    <div class="clearfix"></div>


                    <!-- 1:1문의 -->
                    <div class="col-xs-12">
                        <div class="tab_title">
                            <div class="pull-left" style="padding:7px">1:1문의 목록</div>
                        </div>
                        <div id="board_qna_list" style="color:#000;margin-bottom:30px;"></div>
                    </div>
                    <!-- // 1:1문의 -->

                    <div class="clear"></div>


                    <!-- 당첨상품내역 -->
                    <!--
                    <div class="col-xs-12">
                        <div class="tab_title">
                            <div class="pull-left" style="padding:7px;">당쳠 상품 내역</div>
                        </div>
                        <div id="gift_list" style="color:#000;margin-bottom:30px;">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr role="row" class="active">
                                    <th>No.</th>
                                    <th>이벤트년월</th>
                                    <th>핀번호</th>
                                    <th>발급일시</th>
                                </tr>
                                <tbody>
                            <?php
                            $query = "select * from event_gift_tb where eg_member_num = '" . $member_row['m_num'] . "'";
                            $gift_list = $this->db->query($query)->result_array();

                            foreach($gift_list as $key => $row) {
                            ?>

                                <tr role="row">
                                    <td><?php echo ($key + 1); ?></td>
                                    <td><?php echo $row['eg_event_ym']; ?></td>
                                    <td><?php echo $row['eg_gift']; ?></td>
                                    <td><?php echo get_datetime_format($row['eg_issuedatetime']); ?></td>
                                </tr>

                            <?php
                            }//endforeach;
                            ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    -->
                    <!-- // 당첨상품내역 -->

                    <div class="clear"></div>

                    <!-- 당첨/응모내역
                    <div class="col-xs-12">
                        <div class="tab_title">
                            <div class="pull-left" style="padding:7px;">당첨/응모 내역</div>
                        </div>
                        <div id="gift_list" style="color:#000;margin-bottom:30px;">
                            <table class="table table-hover table-bordered">
                                <thead>
                                <tr role="row" class="active">
                                    <th>No.</th>
                                    <th>이벤트</th>
                                    <th>연락처</th>
                                    <th>기프티콘</th>
                                    <th>등록일시</th>
                                    <th>상태</th>
                                </tr>
                                <tbody>
                                <?php
//                                $query = "
//                                    select *
//                                    from event_winner_tb
//                                        join event_tb on e_num = ew_event_num
//                                        left join event_gift_code_tb A on A.event_code = e_code AND ew_event_gift = A.gift_code
//                                    where
//                                        ew_member_num = '" . $member_row['m_num'] . "'
//                                        order by ew_num asc
//                                ";
//
//                                $winner_list = $this->db->query($query)->result_array();

                                foreach($winner_list as $key => $row) {
                                    ?>

                                    <tr role="row">
                                        <td><?php echo ($key + 1); ?></td>
                                        <td><?php echo $row['e_subject']; ?> / <?php echo substr($row['ew_regdatetime'], 0, 6); ?> / <?php echo $row['ew_type_detail']; ?>일</td>
                                        <td><?php echo $row['ew_contact']; ?></td>
                                        <td>
                                            <?php
                                            if($row['ew_gift']){
                                                echo $row['ew_gift'] ;
                                            }else{
                                                echo $row['gift_name'];
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo get_datetime_format($row['ew_regdatetime']); ?></td>
                                        <td><?php echo get_config_item_text($row['ew_state'], "event_winner_state"); ?></td>
                                    </tr>

                                    <?php
                                }//endforeach;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     // 당첨/응모내역 -->

                    <div class="clear"></div>

                    <!-- 이벤트 참여
                    <div class="col-xs-12" id="event_active_list_wrap">
                        <div class="tab_title">
                            <div class="pull-left" style="padding:7px;">이벤트참여 목록</div>
                            <div class="pull-right">
                                <select name="ea_ym" class="form-control btn-success" onclick="get_event_active_list();">
                                    <?php echo get_select_option_ym("", date("Ym"), date("Ym"), 201602, "desc"); ?>
                                </select>
                            </div>
                        </div>
                        <div id="event_active_list" style="color:#000;margin-bottom:30px;"></div>
                    </div>
                     // 이벤트 참여 -->



                    <!-- 이벤트 참여 -->
                    <div class="col-xs-12" id="comment_list_wrap">
                        <div class="tab_title">
                            <div class="pull-left" style="padding:7px;">지난댓글 목록</div>
                        </div>
                        <div id="comment_list" style="color:#000;margin-bottom:30px;"></div>
                    </div>
                    <!-- // 이벤트 참여 -->


                </div>
            </div>
        </div>
    </div>
</div>

<br />
<br />
<br />

<form name="my_cmt_frm" action="/comment/comment_list_ajax/">
    <input name="m_num" type="hidden" value="<?=$member_row['m_num']?>"/>
    <input name="member_only" type="hidden" value="Y"/>
    <input name="pop" type="hidden" value="Y"/>
</form>

<script>
    var list_url = '<?php echo $list_url; ?>';
    var m_num = '<?php echo $member_row['m_num']; ?>';

    //===================================================== 1:1문의
    /**
     * 1:1문의 목록
     */
    function get_board_qna_list(page) {
        if( empty(page) ) {
            page = 1;
        }
        //$('#board_qna_list').load('/board_qna/list_ajax/?m_num=<?php //echo $member_row->m_num; ?>//&view_type=simple&page=' + page);
        $('#board_qna_list').load('/board_qna/list_ajax/?m_num=<?php echo $member_row['m_num']; ?>&view_type=simple&page=' + page);
    }//end of get_comment_list()

    /**
     * 1:1문의 답글 팝업
     */
    function board_qna_answer_pop(bq_num) {
        var container = $('<div>');
        $(container).load('/board_qna/answer_pop/?bq_num=' + bq_num);

        modalPop.createPop('1:1문의 답글', container);
        modalPop.createButton('삭제', 'btn btn-danger btn-sm pull-left', function(){
            board_qna_delete(bq_num);

            //새로고침
            get_board_qna_list();
        });

        /*
        modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
            $('#pop_update_form').submit();

            //새로고침
            get_board_qna_list();
        });
        */

        if(bq_num > 28096) {
            modalPop.createButton('처리중', 'btn btn-warning btn-sm', function(){
                $("input[name='bq_flag']").val('P');
                $('#pop_update_form').submit();

                //새로고침
                get_board_qna_list();
            });
            modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
                $("input[name='bq_flag']").val('C');
                $('#pop_update_form').submit();

                //새로고침
                get_board_qna_list();
            });
            modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        } else {
            modalPop.createButton('답변완료', 'btn btn-primary btn-sm', function(){
                $('#pop_update_form').submit();

                //새로고침
                get_board_qna_list();
            });
            modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        }

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
                    // $('#search_form').submit();
                    //새로고침
                    get_board_qna_list();
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
    //===================================================== /1:1문의



    function addComma(num) {
        var regexp = /\B(?=(\d{3})+(?!\d))/g;
        return num.toString().replace(regexp, ',');
    }

    /**
     * 적립금목록 출력
     */
    function point_member_list_print() {
        $.ajax({
            url : '/point/member_list_ajax',
            data : {m_num:m_num},
            type : 'post',
            dataType : 'html',
            success : function (result) {
                $('#point_member_list').html(result);
            }
        });
    }//end of point_member_list_print()

    /**
     * 적립금 삭제
     */
    function point_delete(uid) {
        if( empty(uid) ) {
            alert('삭제하실 적립금을 선택하세요.');
            return false;
        }

        if( !confirm('해당 적립금을 삭제하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/point/delete_ajax',
            data : {uid:uid, m_num:m_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.success == true ) {
                    alert('해당 적립금이 삭제되었습니다.');
                    point_member_list_print();
                    point_member_select_print();
                }
                else {
                    alert(result.message);
                }
            }
        });
    }//end of point_delete()

    //=========================== 적립금

    /**
     * 이벤트 참여 목록
     */
    function get_event_active_list() {
        var ym = $('[name="ea_ym"]').val();

        $('#event_active_list').load('/event_active/list_ajax/?m_num=' + m_num + '&ym=' + ym + '&list_per_page=999999');
    }//end of get_event_active_list()

    /**
     * 회원 인증번호 삭제
     */
    function member_auth_delete() {
        if( !confirm('본인인증을 초기화하시겠습니까?') ) {
            return false;
        }

        $.ajax({
            url : '/member/auth_delete',
            data : {m_num:m_num},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    location.reload();
                }
            }
        });
    }//end of member_auth_delete()


    //document.ready
    $(function(){

        $('form[name="my_cmt_frm"]').ajaxForm({
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(result) {
                $('#comment_list').html(result);
            },
            complete: function() {
            }
        });//end of ajax_form()

        $('form[name="my_cmt_frm"]').submit();

        //1:1문의 목록
        get_board_qna_list();

        //이벤트 참여 목록
        get_event_active_list();

        //ajax form
        $('#main_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
                Pace.restart();
            },
            success: function(res) {
                if( res.message ) {
                    if( res.message_type == 'alert' ) {
                        alert(res.message);
                    }
                }

                if( res.status == status_code['success'] ) {
                    <?php if( empty($req['pop']) ) { ?>
                    location.replace(list_url);
                    <?php } else { ?>
                    location.reload();
                    <?php } ?>
                }
                else {
                    if( res.error_data ) {
                        $.each(res.error_data, function(key, msg){
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

        //ajax page
        $(document).on('click', '#board_qna_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            get_board_qna_list($(this).data('ci-pagination-page'));
        });
    });//en d of document.ready()

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

    //댓글끝
    
</script>