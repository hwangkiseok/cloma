<div class="table-responsive">
    <p class="alert alert-warning">※ 당첨자가 있는 경우에만 이벤트가 노출</p>
    <table class="table table-hover table-bordered">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>이벤트명</th>
            <th>이벤트 년월</th>
            <th>당첨회원수</th>
            <th>기프티콘수</th>
            <th>등록일시</th>
            <th>상태</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        if( count($event_gift_list) > 0 ) {
            $list_number = count($event_gift_list);

            foreach($event_gift_list as $row) {

                if ($row->winner_cnt > 0) {

                    if($row->gift_name){
                        $gift_name = $row->gift_name;
                    }else{
                        $gift_name = $this->config->item($row->eg_event_gift, $row->e_code);
                    }

                    ?>

                    <tr role="row">
                        <td><?php echo number_format($list_number); ?></td>
                        <td>
                            <?php echo $row->e_subject; ?>
                            <?
                            if ($row->eg_event_gift) {
                                ?>
                                <br>
                                <?if($gift_name){?>(<?=$gift_name?>)<?}?>
                            <?
                            } ?>

                        </td>
                        <td><?php echo substr(get_date_format($row->eg_event_ym, "-"), 0, -1); ?></td>
                        <td><?php echo number_format($row->winner_cnt); ?></td>
                        <td><?php echo number_format($row->gift_cnt); ?></td>
                        <td><?php echo get_datetime_format($row->eg_regdatetime); ?></td>
                        <td><?php echo get_config_item_text($row->eg_state, "event_gift_state"); ?></td>
                        <td>
                            <?php if ($row->winner_cnt > 0) { ?>
                                <a href="#none" class="btn btn-warning btn-xs"
                                   onclick="event_gift_issue_proc('<?php echo $row->eg_event_num; ?>', '<?php echo $row->eg_event_ym; ?>', '<?php echo $row->eg_event_gift; ?>');">발급</a>
                            <?php } else { ?>
                                <a href="#none" class="btn btn-warning btn-xs" onclick="alert('당첨회원이 없습니다.');">발급</a>
                            <?php }//endif;
                            ?>
                        </td>
                    </tr>

                    <?php
                    $list_number--;
                }//end of foreach()
            }
        }
        else {
            ?>

            <tr role="row">
                <td colspan="10" style="padding:30px;">발급할 기프티콘이 없습니다.</td>
            </tr>

        <?php
        }//endif;
        ?>
        </tbody>
    </table>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>