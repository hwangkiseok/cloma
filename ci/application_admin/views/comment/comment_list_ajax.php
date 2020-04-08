<div class="table-responsive">
    <table class="table table-hover table-bordered <?php if( $req['rctly'] != "Y" ) { ?>dataTable<?php } ?>">
        <thead>
        <tr role="row" class="active">
            <th style="width:40px;">No.</th>

            <?php if( $req['view_type'] == "simple" ) { ?>
                <th style="width:70px;">등록일시</th>
                <th style="width:110px;padding: 0;text-align:" class="text-center">지연시간</th>
                <th style="width:75px;">구분</th>
                <th style="width:150px;">대상글제목</th>
                <th>댓글내용</th>
                <th style="width:65px;">작성자</th>
                <th style="width:65px;">상태</th><?/*블라인드*/?>
                <th style="width:65px;">참고</th>

            <?php } else { ?>

                <th style="width:80px;" class="<?php echo $sort_array['cmt_regdatetime'][1];?>" onclick="form_submit('sort_field=cmt_regdatetime&sort_type=<?php echo $sort_array['cmt_regdatetime'][0]; ?>');">등록일시</th>
                <th style="width:110px;padding: 0;text-align:" class="text-center">지연시간</th>
                <th style="width:75px;" class="<?php echo $sort_array['cmt_gubun'][1];?>" onclick="form_submit('sort_field=cmt_name&sort_type=<?php echo $sort_array['cmt_gubun'][0]; ?>');">문의유형</th>
                <th style="width:150px;" class="<?php echo $sort_array['target_subject'][1];?>" onclick="form_submit('sort_field=target_subject&sort_type=<?php echo $sort_array['target_subject'][0]; ?>');">대상글제목</th>
                <th class="<?php echo $sort_array['cmt_content'][1];?>" onclick="form_submit('sort_field=cmt_content&sort_type=<?php echo $sort_array['cmt_content'][0]; ?>');">댓글 내용</th>
                <th style="width:75px;" class="<?php echo $sort_array['cmt_name'][1];?>" onclick="form_submit('sort_field=cmt_name&sort_type=<?php echo $sort_array['cmt_name'][0]; ?>');">작성자</th>
                <th style="width:75px;" class="">상태</th>
                <th style="width:75px;" class="<?php echo $sort_array['cmt_blind'][1];?>" onclick="form_submit('sort_field=cmt_blind&sort_type=<?php echo $sort_array['cmt_blind'][0]; ?>');">참고</th><?/*블라인드*/?>
                <!--<th style="width:75px;" class="<?php echo $sort_array['cmt_display_state'][1];?>" onclick="form_submit('sort_field=cmt_display_state&sort_type=<?php echo $sort_array['cmt_display_state'][0]; ?>');">노출여부</th>-->
                <th style="width:60px;">관리</th>

            <?php } ?>
        </tr>
        </thead>
        <tbody>

        <?php $list_number = $list_count['cnt'] - ($list_per_page * ($page-1)); ?>
        <?php foreach($comment_list as $key => $row) { //zsView($row); ?>

            <tr role="row" class="cmt-item">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_datetime_format($row['cmt_regdatetime']); ?></td>
                <td style="width:110px;padding: 0;text-align:" class="text-center">
                    <?
                    if(empty($row['cmt_answer']) == false){
                        echo '<span class="text-primary">'.$row['delay_time'].'<br>답변완료</span>';
                    }else{
                        echo '<span class="text-danger">'.$row['delay_time'].'<br>답변대기</span>';
                    }
                    ?>
                </td>
                <td><?php echo get_config_item_text($row['cmt_table'], 'comment_table'); ?></td>
                <td>
                    <?php if( $row->cmt_table == "product" ) { ?>
                        <a href="#none" onclick="new_win_open('<?php echo $this->config->item($row->cmt_table, "comment_table_detail_url") . $row['cmt_table_num']; ?>', '', '1000', '800');"><?php echo $row['table_num_name']; ?></a>
                    <?php } else { ?>
                        <?php echo $row['table_num_name']; ?>
                    <?php }//endif; ?>
                </td>
                <td class="comm" style="text-align:left;<?php if($row->cmt_admin == 'Y') { ?>color:#0000ff;<?php }//endif; ?><?php if( !empty($row->cmt_parent_num) ) { ?>padding-left:15px;<?php }//endif; ?>">
                    <div class="comm_q">
                        <?php echo nl2br($row['cmt_content']); ?>
                    </div>
                    <?if(empty($row['cmt_answer']) == false){?>
                    <div class="comm_a" style="display: block;">
                        <br><br>(<?php echo get_datetime_format($row['cmt_answertime']); ?>)<br>
                        <font color='blue'><?php echo nl2br($row['cmt_answer']); ?></font>
                    </div>
                    <?php } ?>
                </td>
                <td>
                    <?php if($row['cmt_admin'] == 'N'): ?>
                        <a href="#none" onclick="member_update_pop('<?php echo $row['cmt_member_num']; ?>');"><?=(isset($row['name']) && !empty($row['name'])) ? $row['name'] : $row['cmt_name']; ?></a>
                    <?php else: ?>
                        <font color="red"><b>[<?php echo $row['cmt_name']; ?>]</b></font>
                    <?php endif; ?>
                </td>
                <?php
                if($row['cmt_admin'] == 'N') {
                    if( empty($row['cmt_answer']) == false )  echo '<td><font color="blue"><b>완료</b></font></td>';
                    else echo '<td><font color="#FFBB00"><b>대기</b></font></td>';
                } else {
                    echo '<td><font color="blue"><b>완료</b></font></td>';
                }
                ?>
                <td>
                    <?php if ( $row['cmt_blind'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="blind_select('<?php echo $row['cmt_num']; ?>', 'N');">참고</button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="blind_select('<?php echo $row['cmt_num']; ?>', 'Y');">정상</button>
                    <?php } ?>
                </td>

                <?php if( empty($req['view_type']) ) { ?>
                    <td>
                        <button type="button" class="btn btn-success btn-xs" style="width:58px;" onclick="comment_update_pop('<?php echo $row['cmt_num']; ?>')">상세</button><br />
                        <?php if( empty($row['cmt_answer']) == false ) { ?>
                            <button type="button" class="btn btn-info btn-xs mgt5" style="width: 60px;" onclick="comment_reply_pop('<?php echo $row['cmt_num']; ?>')">답변완료</button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-primary btn-xs mgt5" style="width: 60px;" onclick="comment_reply_pop('<?php echo $row['cmt_num']; ?>')">답변달기</button>
                        <?php } ?>
                    </td>
                <?php } ?>
            </tr>

            <?php $list_number--; ?>
        <?php } //end of foreach() ?>

        </tbody>
    </table>
</div>

<?php if( $req['rctly'] != "Y" ) { ?>

    <div class="row text-center">
        <?php echo $pagination; ?>
    </div>

<?php } ?>
<script>
    $(document).ready(function () {

        <?php if($req['admin'] == 'N'): ?>
        $(".comm_a").css('display', 'none');
        <?php endif; ?>

       $(".comm_q").click(function() {

            var submenu = $(this).next(".comm_a");

            if( submenu.is(":visible") ){
               submenu.slideUp();
            }else{
               submenu.slideDown();
            }

       });
    });
</script>
