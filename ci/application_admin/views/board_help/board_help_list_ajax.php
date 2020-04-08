<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['bh_division'][1];?>" onclick="form_submit('sort_field=bh_division&sort_type=<?php echo $sort_array['bh_division'][0]; ?>');">게시판이름</th>
            <?php if ( $req['div'] == 2 ) { ?>
            <th class="<?php echo $sort_array['bh_category'][1];?>" onclick="form_submit('sort_field=bh_category&sort_type=<?php echo $sort_array['bh_category'][0]; ?>');">분류</th>
            <?php } ?>
            <th class="<?php echo $sort_array['bh_top_yn'][1];?>" onclick="form_submit('sort_field=bh_top_yn&sort_type=<?php echo $sort_array['bh_top_yn'][0]; ?>');">상위노출</th>
            <th class="<?php echo $sort_array['bh_subject'][1];?>" onclick="form_submit('sort_field=bh_subject&sort_type=<?php echo $sort_array['bh_subject'][0]; ?>');">제목</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['bh_regdatetime'][1];?>" onclick="form_submit('sort_field=bh_regdatetime&sort_type=<?php echo $sort_array['bh_regdatetime'][0]; ?>');">등록일</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($board_help_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->bh_division, 'board_help_division'); ?></td>
                <?php if ( $req['div'] == 2 ) { ?>
                <td><?php echo get_config_item_text($row->bh_category, 'faq_category'); ?></td>
                <?php } ?>
                <td><?php echo get_config_item_text($row->bh_top_yn, 'board_help_top_yn'); ?></td>
                <td><?php echo $row->bh_subject; ?></td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_date_format($row->bh_regdatetime); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="board_help_update_pop('<?php echo $row->bh_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="board_help_delete('<?php echo $row->bh_num; ?>')">삭제</a>
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