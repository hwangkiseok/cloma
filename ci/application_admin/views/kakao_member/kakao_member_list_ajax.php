<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>

            <th>ID</th>
            <th>연결상태</th>
            <th>닉네임</th>
            <th>연령대</th>
            <th>생일</th>
            <th>이메일</th>
            <th>성별</th>
            <th>연락처</th>
            <th>등록일<br>(최종상태변경일)</th>

        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count['cnt'] - ($list_per_page * ($page-1));

        foreach($kakao_member_list as $row) {
        ?>

            <tr role="row">

                <td><?php echo number_format($list_number); ?></td>
                <td><?=$row['sns_id']?></td>
                <td><?=$row['friend_flag']=='added'?'채널추가':'차단'?></td>
                <td><?=$row['nickname']?></td>
                <td><?=$row['age_range']?></td>
                <td><?=substr($row['birthday'],0,2)?>월 <?=substr($row['birthday'],2,2)?>일</td>
                <td><?=$row['email']?></td>
                <td><?=$row['gender']=='male'?'남성':'여성'?></td>
                <td><?=ph_slice($row['phone_number'])?></td>
                <td><?=view_date_format($row['reg_date'],2)?><?if($row['update_date'] != ''){?><br>(<?=view_date_format($row['update_date'],2)?>)<?}?></td>

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

