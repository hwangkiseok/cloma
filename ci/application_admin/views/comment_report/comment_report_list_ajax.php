<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['cmt_table'][1];?>" onclick="form_submit('sort_field=cmt_table&sort_type=<?php echo $sort_array['cmt_table'][0]; ?>');">구분</th>
            <th>대상글</th>
            <th class="<?php echo $sort_array['cmt_content'][1];?>" onclick="form_submit('sort_field=cmt_content&sort_type=<?php echo $sort_array['cmt_content'][0]; ?>');">내용</th>
            <th class="<?php echo $sort_array['cmt_name'][1];?>" onclick="form_submit('sort_field=cmt_name&sort_type=<?php echo $sort_array['cmt_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['cmt_blind'][1];?>" onclick="form_submit('sort_field=cmt_blind&sort_type=<?php echo $sort_array['cmt_blind'][0]; ?>');">참고</th><?/*블라인드*/?>
            <th class="<?php echo $sort_array['cmt_display_state'][1];?>" onclick="form_submit('sort_field=cmt_display_state&sort_type=<?php echo $sort_array['cmt_display_state'][0]; ?>');">노출여부</th>
            <th class="<?php echo $sort_array['cmt_regdatetime'][1];?>" onclick="form_submit('sort_field=cmt_regdatetime&sort_type=<?php echo $sort_array['cmt_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['RM.m_nickname'][1];?>" onclick="form_submit('sort_field=RM.m_nickname&sort_type=<?php echo $sort_array['RM.m_nickname'][0]; ?>');">신고자</th>
            <th class="<?php echo $sort_array['rp_reason'][1];?>" onclick="form_submit('sort_field=rp_reason&sort_type=<?php echo $sort_array['rp_reason'][0]; ?>');">신고이유</th>
            <th class="<?php echo $sort_array['rp_regdatetime'][1];?>" onclick="form_submit('sort_field=rp_regdatetime&sort_type=<?php echo $sort_array['rp_regdatetime'][0]; ?>');">신고일시</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($comment_report_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->cmt_table, 'comment_table'); ?></td>
                <td><?php echo $row->table_num_name; ?></td>
                <td style="text-align:left;"><?php echo nl2br($row->cmt_content); ?></td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row->cmt_member_num; ?>');"><?php echo $row->name; ?></a></td>
                <td><?php echo get_config_item_text($row->cmt_blind, "comment_blind"); ?></td>
                <td><?php echo get_config_item_text($row->cmt_display_state, "comment_display_state"); ?></td>
                <td><?php echo get_datetime_format($row->rp_regdatetime); ?></td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row->report_m_num; ?>');"><?php echo $row->report_m_nickname; ?></a></td>
                <td><?php echo $row->rp_reason; ?></td>
                <td><?php echo get_datetime_format($row->rp_regdatetime); ?></td>
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