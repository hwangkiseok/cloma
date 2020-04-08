<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['m_regdatetime'][1];?>" onclick="form_submit('sort_field=m_regdatetime&sort_type=<?php echo $sort_array['m_regdatetime'][0]; ?>');">가입일</th>
            <th class="<?php echo $sort_array['m_join_path'][1];?>" onclick="form_submit('sort_field=m_join_path&sort_type=<?php echo $sort_array['m_join_path'][0]; ?>');">가입경로</th>
            <th class="<?php echo $sort_array['m_loginid'][1];?>" onclick="form_submit('sort_field=m_loginid&sort_type=<?php echo $sort_array['m_loginid'][0]; ?>');">아이디</th>
            <th class="<?php echo $sort_array['m_division'][1];?>" onclick="form_submit('sort_field=m_division&sort_type=<?php echo $sort_array['m_division'][0]; ?>');">회원구분</th>
            <th class="<?php echo $sort_array['m_sns_site'][1];?>" onclick="form_submit('sort_field=m_sns_site&sort_type=<?php echo $sort_array['m_sns_site'][0]; ?>');">SNS</th>
            <th class="<?php echo $sort_array['m_nickname'][1];?>" onclick="form_submit('sort_field=m_nickname&sort_type=<?php echo $sort_array['m_nickname'][0]; ?>');">닉네임</th>
            <th class="<?php echo $sort_array['m_email'][1];?>" onclick="form_submit('sort_field=m_email&sort_type=<?php echo $sort_array['m_email'][0]; ?>');">이메일</th>
            <th class="<?php echo $sort_array['m_state'][1];?>" onclick="form_submit('sort_field=m_state&sort_type=<?php echo $sort_array['m_state'][0]; ?>');">회원상태</th>
            <th class="<?php echo $sort_array['m_logindatetime'][1];?>" onclick="form_submit('sort_field=m_logindatetime&sort_type=<?php echo $sort_array['m_logindatetime'][0]; ?>');">최종접속</th>
            <th class="<?php echo $sort_array['m_order_count'][1];?>" onclick="form_submit('sort_field=m_order_count&sort_type=<?php echo $sort_array['m_order_count'][0]; ?>');">총주문수</th>
            <th>수정</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count['cnt'] - ($list_per_page * ($page-1));

        foreach($member_list as $row) {
        ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?php echo get_date_format($row['m_regdatetime']); ?>
                    <?php
                    if( $row['m_rejoin_yn'] == 'Y' || strpos($row['m_memo'], "#[재가입]") !== false ) {
                        echo "<br>[재가입]";
                    }
                    ?>
                </td>
                <td><?php echo get_config_item_text($row['m_join_path'], 'member_join_path'); ?></td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row['m_num']; ?>');"><?php echo $row['m_nickname']; ?>
                        <?php
                        if($row['m_tag']){
                            //echo "<br>[".$row['m_tag']."]";
                        }
                        ?>
                    </a></td>
                <td><?php echo get_config_item_text($row['m_division'], 'member_division'); ?></td>
                <td><?php echo get_config_item_text($row['m_sns_site'], 'member_sns_site'); ?></td>
                <td><?php echo $row['m_nickname']; ?></td>
                <td><?php echo $row['m_email']; ?></td>
                <td><?php echo get_config_item_text($row['m_state'], 'member_state'); ?></td>
                <td><?php echo get_datetime_format($row['m_logindatetime']); ?></td>
                <td><a href="#none" class="btn btn-success btn-xs" onclick="member_order_list_pop('<?php echo get_order_list_link($row['m_key']); ?>')"><?php echo number_format($row['m_order_count']); ?></a></td>
                <td>
                    <a href="<?php echo $this->page_link->update; ?>/?m_num=<?php echo $row['m_num']; ?>&<?php echo $GV; ?>" class="btn btn-primary btn-xs">수정</a>
                </td>
            </tr>

        <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>

<script>
    /**
     * 회원 주문내역 팝업
     */
    function member_order_list_pop (url) {
        new_win_open(url, '', 800, 600);
    }//end of member_order_list_pop()


    //document.ready
    $(function(){
        //갯수 업데이트
        $('#member_count_total').text(number_format(<?php echo $member_count_array['total']['cnt']; ?>));
        <?php
        $btn_calss_arr = array('1' => 'btn-primary', '2' => 'btn-warning', '3' => 'btn-success', '4' => 'btn-info', '99' => 'btn-danger');
        foreach( $this->config->item('member_state') as $key => $text ) {
            ?>
        $('#member_count_<?php echo $key; ?>').text(number_format(<?php echo $member_count_array['state'][$key]['cnt']; ?>));
        $('#member_per_<?php echo $key; ?>').text(<?php echo $member_count_array['state_per'][$key]; ?>);
        $('#member_count_<?=$key;?>').closest('.btn').removeClass('btn-info').addClass('<?=$btn_calss_arr[$key];?>');
        <?php }//endforach; ?>

        $('#member_count_99').text(number_format(<?=$member_count_array['state']['99'];?>));
        $('#member_per_99').text(<?=$member_count_array['state_per']['99'];?>);
        $('#member_count_99').closest('.btn').removeClass('btn-info').addClass('<?=$btn_calss_arr['99'];?>');

        var m_state = '<?=$req['state']?>';
        //if(!empty(m_state)){
            $('input[name="m_state"][value="'+m_state+'"]').prop('checked',true);
        //}

    });//end of document.ready
</script>

<?
//zsView($req);
?>

