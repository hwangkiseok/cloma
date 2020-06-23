<style>
    img { max-width: 100%; }
</style>
<div class="table-responsive">

    <table class="table table-hover table-bordered <?php if( $req['rctly'] != "Y" && $req['view_type'] != "simple" ) { ?>dataTable<?php } ?>">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['bq_regdatetime'][1];?>" onclick="form_submit('sort_field=bq_regdatetime&sort_type=<?php echo $sort_array['bq_regdatetime'][0]; ?>');">등록일시</th>
            <th>지연시간</th>
            <th class="<?php echo $sort_array['bq_category'][1];?>" onclick="form_submit('sort_field=bq_category&sort_type=<?php echo $sort_array['bq_category'][0]; ?>');">문의유형</th>
            <th class="<?php echo $sort_array['bq_content'][1];?>" onclick="form_submit('sort_field=bq_content&sort_type=<?php echo $sort_array['bq_content'][0]; ?>');">문의내용</th>
            <th class="<?php echo $sort_array['m_loginid'][1];?>" onclick="form_submit('sort_field=m_loginid&sort_type=<?php echo $sort_array['m_loginid'][0]; ?>');">작성자(ID)</th>

            <th class="<?php echo $sort_array['bq_answerdatetime'][1];?>" onclick="form_submit('sort_field=bq_answerdatetime&sort_type=<?php echo $sort_array['bq_answerdatetime'][0]; ?>');">답변일시</th>
            <th class="<?php echo $sort_array['bq_answer_content'][1];?>" onclick="form_submit('sort_field=bq_answer_content&sort_type=<?php echo $sort_array['bq_answer_content'][0]; ?>');">답변내용</th>
            <th class="<?php echo $sort_array['bq_last_writer'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['bq_last_writer'][0]; ?>');">답변자</th>
            <?php //if($req['view_type'] != "simple" ) { ?>
            <th>관리</th>
            <?php //} ?>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count['cnt'] - ($list_per_page * ($page-1));

        foreach($board_qna_list as $row) {
            ?>

            <tr role="row">
                <!-- No. -->
                <td><?php echo number_format($list_number); ?></td>
                <!-- 등록일시 -->
                <td style="width:80px;"><?php echo get_datetime_format($row['bq_regdatetime']); ?></td>
                <!-- 지연시간 -->
                <td style="padding: 0;" class="text-center">
                    <?
                    if($row['bq_display_state_1'] != 'N'){
                        if(empty($row['bq_answer_content']) && empty($row['bq_answerdatetime']) ){
                            echo '<span class="text-danger">'.dayDiff_second($row['bq_regdatetime'],date('YmdHis')).'<br>답변대기</span>';
                        }else{
                            echo '<span class="text-primary">'.dayDiff_second($row['bq_regdatetime'],$row['bq_answerdatetime']).'<br>답변완료</span>';
                        }
                    }
                    ?>
                </td>
                <!-- 문의유형 -->
                <td style="width:80px;"><?php echo get_config_item_text($row['bq_category'], 'board_qna_category'); ?></td>
                <!-- 문의내용 -->
                <td style="width:30%;">
                    <div class="text-left">
                        <?php if ($row['bq_display_state_1'] == 'N') { ?>
                            <p style="font-weight:bold;color:red;margin:0;">[고객이 삭제한 문의글]</p>
                        <?php } ?>
                        <?php if ( !empty($row['bq_product_name']) ) { echo "상품명 : " . $row['bq_product_name'] . "<br />"; } ?>
                        <?php if ( !empty($row['bq_name']) ) { echo "이름 : " . $row['bq_name'] . "<br />"; } ?>
                        <?php if ( !empty($row['bq_contact']) ) { echo "연락처 : " . $row['bq_contact'] . "<br />"; } ?>
                        <?php if ( !empty($row['bq_refund_info']) ) { echo "환불계좌 : " . $row['bq_refund_info'] . "<br />"; } ?>
                        <?php if ( !empty($row['bq_file']) ) { $bq_file_arr = json_decode($row['bq_file']);
                            if(count($bq_file_arr) > 0){
                                foreach ($bq_file_arr as $v) {?>

                                    <a href="#none" onclick="new_win_open('<?=$v?>', 'img_pop', 800, 600);" style="display: inline-block;width: 70px;height: 70px;overflow: hidden;margin-right: 5px; " >
                                        <img src="<?=$v?>">
                                    </a>

                                    <!--첨부파일 : <a href="#none" onclick="new_win_open('/download/?m=view&f=<?=$v?>', 'file_win', '1000', '700');"><?= pathinfo($v, PATHINFO_BASENAME)?></a><br />-->
                                <?}?>
                            <?}else{?>
                                첨부파일 : <a href="#none" onclick="new_win_open('/download/?m=view&f=<?=$row['bq_file']?>', 'file_win', '1000', '700');"><?= pathinfo($row['bq_file'], PATHINFO_BASENAME)?></a><br />
                            <?}?>
                        <?} ?>
                        <p class="bq_cont <?php if ( !empty($row['bq_name']) ) { ?>line<?php } ?>"><?php echo nl2br(stripslashes($row['bq_content'])); ?></p>
                    </div>
                </td>
                <!-- 작성자 -->
                <td>
                    <a href="#none" onclick="member_update_pop('<?php echo $row['m_num']; ?>')">
                        <?if($row['m_nickname']){?>
                            <?php echo $row['m_nickname']; ?>
                        <?}else{?>
                            [닉네임없음]
                        <?}?>
                    </a>
                </td>
                <!-- 답변일시 -->
                <td style="width:80px;"><?php echo get_datetime_format($row['bq_answerdatetime']); ?></td>
                <!-- 답변내용 -->
                <td style="width:30%;">
                    <div class="text-left">
                        <div style='text-align: left;'><?=stripslashes($row['bq_answer_content'])?></div>
                    </div>
                </td>
                <!-- 답변자 -->
                <td>
                    <?php echo $row['bq_last_writer']; ?>
                </td>

                <?php //if($req['view_type'] != "simple" ) { ?>
                <!-- 관리 -->
                <td>
                    <?php if( $row['bq_answer_yn'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-info btn-xs" onclick="board_qna_answer_pop('<?php echo $row['bq_num']; ?>')">답변완료</button><br />
                    <?php } else { ?>
                        <button type="button" class="btn btn-primary btn-xs" onclick="board_qna_answer_pop('<?php echo $row['bq_num']; ?>')">답변달기</button><br />
                    <?php } ?>
                </td>
                <?php //} ?>
            </tr>

            <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>

<?php if( $req['rctly'] != "Y" ) { ?>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>

<?php } ?>

<script>
    $(function(){
        //문의, 답변내용 click
        //$(document).on('click', '.long_content', function(){
        $('.long_content').on('click', function(){
            $(this).toggleClass('on');

            if( $(this).hasClass('on') ) {
                $(this).children('.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
            }
            else {
                $(this).children('.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
            }
        });
    });
</script>
