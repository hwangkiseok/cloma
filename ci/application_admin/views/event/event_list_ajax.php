<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['e_division'][1];?>" onclick="form_submit('sort_field=e_division&sort_type=<?php echo $sort_array['e_division'][0]; ?>');">이벤트종류</th>
            <th class="<?php echo $sort_array['e_subject'][1];?>" onclick="form_submit('sort_field=e_subject&sort_type=<?php echo $sort_array['e_subject'][0]; ?>');">제목</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['e_termlimit_datetime1'][1];?>" onclick="form_submit('sort_field=e_termlimit_datetime1&sort_type=<?php echo $sort_array['e_termlimit_datetime1'][0]; ?>');">기간</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['e_regdatetime'][1];?>" onclick="form_submit('sort_field=e_regdatetime&sort_type=<?php echo $sort_array['e_regdatetime'][0]; ?>');">등록일</th>
            <th class="<?php echo $sort_array['e_proc_state'][1];?>" onclick="form_submit('sort_field=e_proc_state&sort_type=<?php echo $sort_array['e_proc_state'][0]; ?>');">진행상태</th>
            <th class="<?php echo $sort_array['e_display_state'][1];?>" onclick="form_submit('sort_field=e_display_state&sort_type=<?php echo $sort_array['e_display_state'][0]; ?>');">노출여부</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($event_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->e_division, 'event_division'); ?></td>
                <td><?php echo $row->e_subject; ?> <?if($row->e_num == 16){ echo "(참여신청인원 : {$row->join_cnt}명)";}?>  </td>
                <td><?php echo create_img_tag($row->e_rep_image, 0, 100);?></td>
                <td>
                    <?php
                    if($row->e_termlimit_yn == 'Y') {
                        echo get_date_format($row->e_termlimit_datetime1) . " ~ " . get_date_format($row->e_termlimit_datetime2);
                    }
                    else {
                        echo get_config_item_text($row->e_termlimit_yn, 'event_termlimit_yn');
                    }
                    ?>
                </td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_date_format($row->e_regdatetime); ?></td>
                <td>
                    <?php if( $row->e_proc_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="event_update_toggle('<?php echo $row->e_num; ?>', 'e_proc_state');"><?php echo get_config_item_text($row->e_proc_state, 'event_proc_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="event_update_toggle('<?php echo $row->e_num; ?>', 'e_proc_state');"><?php echo get_config_item_text($row->e_proc_state, 'event_proc_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php if( $row->e_display_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="event_update_toggle('<?php echo $row->e_num; ?>', 'e_display_state');"><?php echo get_config_item_text($row->e_display_state, 'event_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="event_update_toggle('<?php echo $row->e_num; ?>', 'e_display_state');"><?php echo get_config_item_text($row->e_display_state, 'event_display_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="event_update_pop('<?php echo $row->e_num; ?>')">수정</a>
                    <?php if($row->e_division != '1') { ?>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="event_delete('<?php echo $row->e_num; ?>')">삭제</a>
                    <?php } ?>
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